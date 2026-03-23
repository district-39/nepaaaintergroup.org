<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\DataStores\VariableProductDataStore;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Operations\PatchProductOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsMappingServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\BatchJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\HasJobSettingsContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobOutcome;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobSettings;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\BatchJobTrait;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Adapters\ProductAdapter;
use WC_Product;

/**
 * Job to update variation names using find-replace and send to Commerce API.
 * Processes variations in batches to handle large numbers of variations efficiently.
 *
 * The payload data is expected to contain a new name and an old name to find-replace in the variation names
 * {@see VariableProductDataStore::sync_variation_names()}
 *
 * @method BatchJobSettings getJobSettings()
 */
class UpdateVariationNamesJob implements QueueableJobContract, BatchJobContract, HasJobSettingsContract
{
    use BatchJobTrait {
        handle as traitHandle;
    }

    /** @var string represents the key of this job */
    public const JOB_KEY = 'updateVariationNamesJob';

    protected ProductsServiceContract $productsService;
    protected ProductsMappingServiceContract $productsMappingService;

    public function __construct(ProductsServiceContract $productsService, ProductsMappingServiceContract $productsMappingService)
    {
        $this->productsService = $productsService;
        $this->productsMappingService = $productsMappingService;

        $this->setJobSettings($this->configureJobSettings());
    }

    /**
     * Determines if the job should be handled.
     *
     * @return bool
     */
    protected function shouldHandle() : bool
    {
        return CatalogIntegration::isEnabled();
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function handle() : void
    {
        if (! $this->shouldHandle()) {
            $this->jobDone();

            return;
        }

        $this->traitHandle();
    }

    /**
     * {@inheritDoc}
     */
    protected function processBatch() : BatchJobOutcome
    {
        $variationIds = $this->getVariationIdsForCurrentBatch();
        $previousName = TypeHelper::string(ArrayHelper::get($this->args, '1'), '');
        $newName = TypeHelper::string(ArrayHelper::get($this->args, '2'), '');

        $this->setAttemptedResourcesCount(count($variationIds));

        if (! empty($variationIds) && $previousName && $newName && $previousName !== $newName) {
            $this->updateVariationNamesBatch($variationIds, $previousName, $newName);
        }

        $this->incrementOffsetForNextBatch();

        return $this->makeOutcome();
    }

    /**
     * Gets variation IDs for the current batch.
     *
     * @return int[]
     */
    protected function getVariationIdsForCurrentBatch() : array
    {
        $allVariationIds = TypeHelper::arrayOfIntegers(ArrayHelper::get($this->args, '0', []), false);
        $offset = $this->getOffsetForBatch();
        $maxPerBatch = $this->getJobSettings()->maxPerBatch;

        return array_slice($allVariationIds, $offset, $maxPerBatch);
    }

    /**
     * Gets the offset to use for the current batch.
     *
     * @return int
     */
    protected function getOffsetForBatch() : int
    {
        return max(TypeHelper::int(ArrayHelper::get($this->args, 'offset'), 0), 0);
    }

    /**
     * Increments the offset for the next batch.
     *
     * @return void
     */
    protected function incrementOffsetForNextBatch() : void
    {
        $this->args['offset'] = $this->getOffsetForBatch() + $this->getJobSettings()->maxPerBatch;
    }

    /**
     * Updates variation names for a batch of variations.
     *
     * @param int[] $variationIds
     * @param string $previousName
     * @param string $newName
     * @return void
     */
    protected function updateVariationNamesBatch(array $variationIds, string $previousName, string $newName) : void
    {
        $hasReportedException = false;

        // Process each variation in the batch
        foreach ($variationIds as $variationId) {
            try {
                $this->updateSingleVariationName($variationId, $previousName, $newName);
            } catch (Exception|CommerceExceptionContract $e) {
                // We only want to report max 1 exception per batch to prevent excessive spam
                if (! $hasReportedException) {
                    SentryException::getNewInstance('Failed to update variation name via Commerce API: '.$e->getMessage(), $e);
                    $hasReportedException = true;
                }
            }
        }
    }

    /**
     * Updates a single variation's name via the Commerce API.
     *
     * @param int $variationId
     * @param string $previousName
     * @param string $newName
     * @return void
     * @throws Exception|CommerceExceptionContract
     */
    protected function updateSingleVariationName(int $variationId, string $previousName, string $newName) : void
    {
        /*
         * The state of the variant product in the API is potentially inconsistent.
         *
         * Previous bugs {@see https://godaddy-corp.atlassian.net/browse/MWC-18387} may leave the variant in a state
         * where its name is entirely out of sync with WooCommerce. Additionally, other platforms could have modified the
         * variant name, and we need to ensure that the Commerce API reflects the correct local state.
         *
         * - `CatalogIntegration::withoutReads()` will retrieve the variation without an API read.
         * - `clean_post_cache()` is used to ensure that `ProductsRepository::get()` is not returning a cached API data.
         */
        $variation = CatalogIntegration::withoutReads(function () use ($variationId) {
            clean_post_cache($variationId);
            $variation = ProductsRepository::get($variationId);
            clean_post_cache($variationId);

            return $variation;
        });

        if (! $variation instanceof WC_Product) {
            return;
        }

        $nativeProduct = ProductAdapter::getNewInstance($variation)->convertFromSource();
        $remoteId = $this->productsMappingService->getRemoteId($nativeProduct);

        if (! $remoteId) {
            return;
        }

        $currentName = $variation->get_name();

        // Check if the current name already starts with the new name AND
        // the new name is longer than the previous name (append/prepend scenario)
        // This prevents double-replacement in cases where newName contains previousName
        if (strpos($currentName, $newName) === 0 && strlen($newName) >= strlen($previousName)) {
            $updatedName = $currentName;
        } else {
            // Use preg_replace to replace only at the beginning of the string
            $pattern = '/^'.preg_quote($previousName, '/').'/';
            $updatedName = preg_replace($pattern, $newName, $currentName);

            // If no change was made, use the current name
            if ($updatedName === null) {
                $updatedName = $currentName;
            }
        }

        // Create patch operation and send to Commerce API
        $operation = PatchProductOperation::getNewInstance()
            ->setLocalProductId($variationId)
            ->setName($updatedName);
        $this->productsService->patchProduct($operation, $remoteId);
    }
}
