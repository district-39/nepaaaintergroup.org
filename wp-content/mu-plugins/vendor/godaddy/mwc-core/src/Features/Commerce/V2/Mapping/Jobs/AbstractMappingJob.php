<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Jobs;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Exceptions\WordPressDatabaseException;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\Exceptions\WordPressRepositoryException;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\DatabaseRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\CommerceException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\ResourceMapCollection;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ResourceMap;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\ReferencesServiceContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\BatchJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\HasJobSettingsContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobOutcome;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobSettings;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\BatchJobTrait;

/**
 * @method BatchJobSettings getJobSettings()
 */
abstract class AbstractMappingJob implements QueueableJobContract, HasJobSettingsContract, BatchJobContract
{
    use BatchJobTrait;

    /** @var ReferencesServiceContract service class to fetch references from v2 API */
    protected ReferencesServiceContract $referencesService;

    /** @var int Number of successful mappings created in current batch */
    protected int $successfulMappingsCount = 0;

    /**
     * Processes a single batch of records.
     *
     * @return BatchJobOutcome
     * @throws CommerceException
     */
    protected function processBatch() : BatchJobOutcome
    {
        try {
            // Reset successful mappings count for this batch
            $this->successfulMappingsCount = 0;

            $localResourceMapCollection = $this->getV1Mappings();

            if (empty($localResourceMapCollection)) {
                return $this->makeOutcome();
            }

            $this->mapV1ResourcesToV2($localResourceMapCollection);

            return $this->makeOutcome();
        } catch (Exception|CommerceExceptionContract $e) {
            // Handle specific commerce exceptions if needed
            throw new CommerceException($e->getMessage(), $e);
        }
    }

    /**
     * Gets the v1 mappings that still require a corresponding v2 mapping.
     *
     * @return ResourceMapCollection|null
     * @throws WordPressRepositoryException
     */
    protected function getV1Mappings() : ?ResourceMapCollection
    {
        $localIds = $this->getUnmappedLocalResourceIds();

        if (empty($localIds)) {
            return null;
        }

        $resourceMapCollection = $this->getLocalResourceMapsByLocalIds($localIds);

        $this->setAttemptedResourcesCount(count($resourceMapCollection->getResourceMaps()));

        return $resourceMapCollection;
    }

    /**
     * Maps local resources to V2 references and persists the mappings.
     *
     * @param ResourceMapCollection $resourceMapCollection
     * @return void
     * @throws CommerceExceptionContract|Exception
     */
    protected function mapV1ResourcesToV2(ResourceMapCollection $resourceMapCollection) : void
    {
        // Get the V2 references from the Commerce API for the supplied V1 UUIDs
        $references = $this->referencesService->getReferencesByReferenceValues(
            $resourceMapCollection->getRemoteIds()
        );

        // Build mappings between the V2 data and our local resources.
        $referenceMappings = $this->buildReferenceMap($references, $resourceMapCollection);

        // Map local resources to V2 UUIDs
        foreach ($referenceMappings as $referenceMap) {
            $this->addLocalMappingRecordWithExceptionHandling($referenceMap);
        }
    }

    /**
     * Builds a mapping between the V2 references and the local resources.
     *
     * @param AbstractDataObject[] $references
     * @param ResourceMapCollection $resourceMapCollection
     * @return ResourceMap[]
     */
    abstract protected function buildReferenceMap(array $references, ResourceMapCollection $resourceMapCollection) : array;

    /**
     * Adds a local mapping record with exception handling.
     *
     * This method is used to handle exceptions when adding a local mapping record.
     * If the record already exists, it will skip the addition.
     *
     * @param ResourceMap $referenceMap
     * @return void
     */
    protected function addLocalMappingRecordWithExceptionHandling(ResourceMap $referenceMap) : void
    {
        try {
            $this->addLocalMappingRecord($referenceMap);
            $this->successfulMappingsCount++;
        } catch (WordPressDatabaseException $e) {
            if (StringHelper::startsWith($e->getMessage(), 'Duplicate entry')) {
                // If the record already exists, we can skip it.
                // This is considered a successful mapping since it already exists
                $this->successfulMappingsCount++;

                return;
            }

            // we don't want to throw exceptions here because we want to continue processing other records
            SentryException::getNewInstance($e->getMessage(), $e);
        }
    }

    /**
     * Adds the provided reference map to the local mapping table.
     *
     * @param ResourceMap $referenceMap
     * @return void
     * @throws WordPressDatabaseException
     */
    abstract protected function addLocalMappingRecord(ResourceMap $referenceMap) : void;

    /**
     * Gets the records from the local mapping table, giving us a link between the local bigint IDs and the v1
     * remote UUIDs.
     *
     * @param int[] $localIds
     * @return ResourceMapCollection
     */
    abstract protected function getLocalResourceMapsByLocalIds(array $localIds) : ResourceMapCollection;

    /**
     * Gets the local IDs of resources that exist in the v1 mapping table but do not yet exist in the v2 mapping table.
     *
     * @return int[]
     */
    protected function getUnmappedLocalResourceIds() : array
    {
        $limit = $this->getJobSettings()->maxPerBatch;

        $results = DatabaseRepository::getResults(
            $this->getUnmappedLocalIdsSqlString(),
            [
                $limit,
            ],
        );

        /** @var string[] $localIds */
        $localIds = array_column($results, 'local_id');

        return array_map('intval', $localIds);
    }

    /**
     * Gets the SQL string for the unmapped local IDs query.
     *
     * @return string
     */
    abstract protected function getUnmappedLocalIdsSqlString() : string;

    /**
     * Gets the string value of the v2 `reference.origin` property for the v1 containing the v1 `reference.value` needed for mapping.
     *
     * @return string
     */
    abstract protected function getV1OriginString() : string;

    /**
     * Gets the V1 reference value from the provided references.
     *
     * @param Reference[] $references
     * @return string|null
     */
    protected function getV1Reference(array $references) : ?string
    {
        foreach ($references as $reference) {
            if ($reference->origin === $this->getV1OriginString()) {
                return $reference->value;
            }
        }

        return null;
    }

    /**
     * Gets the number of successful mappings created in the current batch.
     *
     * @return int
     */
    protected function getSuccessfulMappingsCount() : int
    {
        return $this->successfulMappingsCount;
    }

    /**
     * Makes a BatchJobOutcome with improved completion logic.
     *
     * The job is complete when:
     * 1. No resources were found to process (empty batch), OR
     * 2. A full batch was attempted but no new mappings were successfully created
     *
     * This prevents infinite loops when resources exist but can't be mapped to V2.
     *
     * @return BatchJobOutcome
     * @throws CommerceException
     */
    protected function makeOutcome() : BatchJobOutcome
    {
        $attemptedCount = $this->getAttemptedResourcesCount();
        $maxPerBatch = $this->getJobSettings()->maxPerBatch;
        $successfulCount = $this->getSuccessfulMappingsCount();

        // Standard completion logic: fewer resources found than requested
        $standardComplete = $attemptedCount < $maxPerBatch;

        // Enhanced completion logic: full batch attempted but zero successful mappings
        $noProgressComplete = $attemptedCount >= $maxPerBatch && $successfulCount === 0;

        // Log when enhanced completion logic prevents infinite loop
        if ($noProgressComplete) {
            throw new CommerceException(
                sprintf(
                    'V2 mapping job "%s" completed with 0/%d successful mappings to prevent infinite loop',
                    $this->getJobKey(),
                    $attemptedCount
                )
            );
        }

        return BatchJobOutcome::getNewInstance([
            'isComplete' => $standardComplete,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getJobKey() : string
    {
        // @phpstan-ignore-next-line
        return static::JOB_KEY;
    }
}
