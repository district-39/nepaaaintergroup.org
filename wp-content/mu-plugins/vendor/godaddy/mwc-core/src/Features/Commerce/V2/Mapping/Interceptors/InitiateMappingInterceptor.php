<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Interceptors;

use DateInterval;
use DateTime;
use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Schedule\Schedule;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Interceptors\Handler\InitiateMappingHandler;

/**
 * Interceptor to handle scheduling the recurring category mapping job.
 *
 * This class is responsible for kicking off individual category mapping processes.
 */
class InitiateMappingInterceptor extends AbstractInterceptor
{
    public const MAPPING_JOB_NAME = 'mwc_gd_commerce_v2_mapping_manager';

    /**
     * Adds hooks.
     *
     * @return void
     * @throws Exception
     */
    public function addHooks() : void
    {
        Register::action()
            ->setGroup('admin_init')
            ->setHandler([$this, 'maybeScheduleJob'])
            ->execute();

        Register::action()
            ->setGroup(TypeHelper::string(static::MAPPING_JOB_NAME, ''))
            ->setHandler([InitiateMappingHandler::class, 'handle'])
            ->execute();
    }

    /**
     * Schedules the recurring job if it's not already scheduled.
     *
     * @return void
     */
    public function maybeScheduleJob() : void
    {
        $job = Schedule::recurringAction()->setName(TypeHelper::string(static::MAPPING_JOB_NAME, ''));

        if (! $job->isScheduled()) {
            try {
                $job
                    ->setScheduleAt(new DateTime('now'))
                    ->setInterval($this->getJobInterval())
                    ->schedule();
            } catch(Exception $exception) {
                SentryException::getNewInstance('Failed to schedule Commerce V2 category mapping job.', $exception);
            }
        }
    }

    /**
     * Gets the job interval.
     *
     * @return DateInterval
     */
    protected function getJobInterval() : DateInterval
    {
        $intervalString = TypeHelper::string(Configuration::get('features.commerce_catalog_v2_mapping.recurringJobDateInterval'), 'PT24H');

        try {
            return new DateInterval($intervalString);
        } catch(Exception $e) {
            return new DateInterval('PT24H');
        }
    }
}
