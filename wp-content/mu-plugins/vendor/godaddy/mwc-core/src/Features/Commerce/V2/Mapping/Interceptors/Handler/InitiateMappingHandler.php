<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Interceptors\Handler;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Core\JobQueue\Contracts\QueueableJobContract;
use GoDaddy\WordPress\MWC\Core\JobQueue\JobQueue;

/**
 * Handles the callback for the recurring category mapping job.
 */
class InitiateMappingHandler extends AbstractInterceptorHandler
{
    /**
     * Dispatches the chain of category mapping jobs.
     *
     * @param ...$args
     * @return void
     */
    public function run(...$args)
    {
        try {
            if (! $jobs = $this->getRegisteredJobs()) {
                return;
            }

            JobQueue::getNewInstance()->chain($jobs)->dispatch();
        } catch(Exception $exception) {
            SentryException::getNewInstance('Failed to dispatch V2 category mapping job.', $exception);
        }
    }

    /**
     * Gets the chain of category mapping jobs.
     *
     * @return class-string<QueueableJobContract>[]
     */
    protected function getRegisteredJobs() : array
    {
        $jobs = TypeHelper::array(Configuration::get('features.commerce_catalog_v2_mapping.jobs'), []);

        return TypeHelper::arrayOfClassStrings($jobs, QueueableJobContract::class);
    }
}
