<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Operations\CreateOrUpdateProductOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsMappingServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits\CanGetLocalProductsBatchTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequest404Exception;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\BatchJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\HasJobSettingsContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobOutcome;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\BatchJobTrait;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Adapters\ProductAdapter;
use WC_Product;
use WC_Product_Attribute;

/**
 * A batch job that queries all products in the local database, if the local product contains non-variant product attributes
 * the job will patch the remote product to remove those attributes from the `options` array.
 */
class PatchProductListOptionsJob implements QueueableJobContract, HasJobSettingsContract, BatchJobContract
{
    use BatchJobTrait {
        handle as traitHandle;
    }
    use CanGetLocalProductsBatchTrait;

    /** @var string */
    public const JOB_KEY = 'patchProductListOptions';

    protected ProductsServiceContract $productsService;
    protected ProductsMappingServiceContract $productsMappingService;

    public function __construct(ProductsServiceContract $productsService, ProductsMappingServiceContract $productsMappingService)
    {
        $this->setJobSettings($this->configureJobSettings());
        $this->productsService = $productsService;
        $this->productsMappingService = $productsMappingService;
    }

    /**
     * {@inheritDoc}
     */
    public function getJobKey() : string
    {
        return static::JOB_KEY;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function handle() : void
    {
        // override the trait method so we can bail early if the catalog integration is not enabled
        // this check is necessary in case the job is being dispatched from a CLI command
        if (! CatalogIntegration::isEnabled()) {
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
        $wooProducts = $this->getLocalProductsBatch();
        $this->setAttemptedResourcesCount(count($wooProducts));
        $this->maybePatchProducts($wooProducts);

        $this->incrementOffsetForNextBatch();

        return $this->makeOutcome();
    }

    /**
     * @param WC_Product[] $wooProducts
     * @return void
     */
    protected function maybePatchProducts(array $wooProducts) : void
    {
        // filter out products that have non-variant product attributes
        $productsToPatch = array_filter($wooProducts, function ($product) {
            return $this->shouldPatchProduct($product);
        });

        foreach ($productsToPatch as $product) {
            try {
                $this->maybeUpdateProduct($product);
            } catch (Exception $e) {
                SentryException::getNewInstance('unable to patch product list options', $e);
            }
        }
    }

    /**
     * @param WC_Product $product
     *
     * @return void
     * @throws GatewayRequest404Exception|GatewayRequestException|Exception
     */
    protected function maybeUpdateProduct(WC_Product $product) : void
    {
        $nativeProduct = ProductAdapter::getNewInstance($product)->convertFromSource();
        if ($remoteId = $this->productsMappingService->getRemoteId($nativeProduct)) {
            $operation = CreateOrUpdateProductOperation::fromProduct($nativeProduct);

            $this->productsService->updateProduct($operation, $remoteId);
        }
    }

    /**
     * Returns `true` if the product has non-variant product attributes.
     *
     * @param WC_Product $product
     *
     * @return bool
     */
    protected function shouldPatchProduct(WC_Product $product) : bool
    {
        $attributes = $product->get_attributes();

        // Check if the product has any non-variant attributes (variation = 0)
        foreach ($attributes as $attribute) {
            /** @var WC_Product_Attribute $attribute */
            if (! $attribute->get_variation()) {
                return true;
            }
        }

        return false;
    }
}
