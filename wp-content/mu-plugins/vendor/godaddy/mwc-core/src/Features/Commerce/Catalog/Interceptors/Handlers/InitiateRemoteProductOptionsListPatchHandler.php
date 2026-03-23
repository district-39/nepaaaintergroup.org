<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\InitiateRemoteProductOptionsListPatch;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\JobQueue;

/**
 * Handler for the {@see InitiateRemoteProductOptionsListPatch} interceptor.
 */
class InitiateRemoteProductOptionsListPatchHandler extends AbstractInterceptorHandler
{
    /**
     * {@inheritDoc}
     */
    public function run(...$args)
    {
        if (! $jobs = $this->getRegisteredJobs()) {
            return;
        }

        JobQueue::getNewInstance()->chain($jobs)->dispatch();

        // mark the job as run
        InitiateRemoteProductOptionsListPatch::setHasRun();
    }

    /**
     * @return class-string<QueueableJobContract>[]
     */
    protected function getRegisteredJobs() : array
    {
        $jobs = TypeHelper::array(Configuration::get('features.commerce_remote_product_list_options.jobs'), []);

        return TypeHelper::arrayOfClassStrings($jobs, QueueableJobContract::class);
    }
}
