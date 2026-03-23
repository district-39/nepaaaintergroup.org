<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Jobs;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\HasJobSettingsContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\DataObjects\BatchJobSettings;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\HasJobSettingsTrait;
use GoDaddy\WordPress\MWC\Core\JobQueue\Traits\QueueableJobTrait;

abstract class AbstractProductCategoryRelationshipsJob implements QueueableJobContract, HasJobSettingsContract
{
    use HasJobSettingsTrait;
    use QueueableJobTrait;

    /** @var string Represents this jobs key. */
    public const JOB_KEY = '';

    public function __construct()
    {
        $this->setJobSettings($this->configureJobSettings());
    }

    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        if (isset($this->args['productIds']) && is_array($this->args['productIds'])) {
            // get the first chunk
            $chunkOfLocalProductIds = TypeHelper::arrayOfIntegers(array_splice($this->args['productIds'], 0, $this->getMaxPerBatch()));
            $categoryId = TypeHelper::int(ArrayHelper::get($this->args, 'categoryId'), 0);

            if (! empty($chunkOfLocalProductIds) && $categoryId) {
                $this->handleChunk($chunkOfLocalProductIds, $categoryId);
            }

            // add the job back to the chain if we have more chunks to process.
            if (! empty($this->args['productIds'])) {
                $this->reQueueJob();
            }
        }

        $this->jobDone();
    }

    /**
     * Handles a chunk of product IDs for the given category ID.
     *
     * @param int[] $productIds
     * @param int $categoryId
     */
    abstract protected function handleChunk(array $productIds, int $categoryId) : void;

    /**
     * Configures the job settings for this job.
     *
     * @return int
     */
    protected function getMaxPerBatch() : int
    {
        $settings = $this->getJobSettings();

        if ($settings instanceof BatchJobSettings) {
            return $settings->maxPerBatch;
        }

        return 1;
    }
}
