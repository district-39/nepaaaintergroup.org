<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\HasJobSettingsContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobSettings;
use GoDaddy\WordPress\MWC\Core\JobQueue\JobQueue;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\HasJobSettingsTrait;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\QueueableJobTrait;

/**
 * Job to create or update products in the remote platform in batches. This job will repeat until all batches have been processed.
 *
 * @method BatchJobSettings getJobSettings()
 */
class BatchCreateOrUpdateProductsJob implements QueueableJobContract, HasJobSettingsContract
{
    use QueueableJobTrait;
    use HasJobSettingsTrait;

    public const JOB_KEY = 'batchCreateOrUpdateProductsJob';

    public function __construct()
    {
        $this->setJobSettings($this->configureJobSettings());
    }

    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        if ($this->shouldHandle()) {
            // get the first chunk
            $this->args = TypeHelper::arrayOfIntegers($this->args ?? []);
            $chunkOfLocalProductIds = TypeHelper::arrayOfIntegers(array_splice($this->args, 0, $this->getJobSettings()->maxPerBatch));

            if (! empty($chunkOfLocalProductIds)) {
                $this->writeChunk($chunkOfLocalProductIds);
            }

            // add the job back to the chain if we have more chunks to process.
            if (! empty($this->args)) {
                $this->reQueueJob();
            }
        }

        $this->jobDone();
    }

    /**
     * Writes a chunk of products.
     *
     * @param int[] $localIds
     * @return void
     */
    protected function writeChunk(array $localIds) : void
    {
        $jobQueue = JobQueue::getNewInstance();
        foreach ($localIds as $localId) {
            try {
                $jobQueue->chain([
                    CreateOrUpdateProductJob::class,
                ])->dispatch([$localId]);
            } catch(Exception $e) {
                SentryException::getNewInstance("Failed to write product {$localId} to the platform: {$e->getMessage()}", $e);
            }
        }
    }

    /**
     * Should the job be handled?
     *
     * @return bool
     */
    protected function shouldHandle() : bool
    {
        return ! empty($this->args);
    }
}
