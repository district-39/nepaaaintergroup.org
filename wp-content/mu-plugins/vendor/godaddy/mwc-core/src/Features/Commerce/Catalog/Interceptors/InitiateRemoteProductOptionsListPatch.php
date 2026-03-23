<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors;

use DateTime;
use Exception;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Schedule\Schedule;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers\InitiateRemoteProductOptionsListPatchHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits\HasJobRunTrait;

/**
 * Interceptor that initiates the remote product list options patch.
 */
class InitiateRemoteProductOptionsListPatch extends AbstractInterceptor
{
    use HasJobRunTrait;

    /** @var string */
    public const JOB_NAME = 'mwc_remote_product_list_options_patch';

    /** @var string */
    protected const JOB_HAS_RUN_OPTION_NAME = 'mwc_remote_product_list_options_patch_job_has_run';

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addHooks() : void
    {
        Register::action()
            ->setGroup('admin_init')
            ->setHandler([$this, 'maybeScheduleJob'])
            ->execute();

        Register::action()
            ->setGroup(static::JOB_NAME)
            ->setHandler([InitiateRemoteProductOptionsListPatchHandler::class, 'handle'])
            ->execute();
    }

    /**
     * Schedules the remote product list options patch job if it has not already run.
     *
     * @return void
     */
    public function maybeScheduleJob() : void
    {
        if (static::hasRun()) {
            return;
        }

        $job = Schedule::singleAction()
            ->setName(static::JOB_NAME)
            ->setUniqueByName();

        if (! $job->isScheduled()) {
            try {
                $job
                    ->setScheduleAt(new DateTime('now'))
                    ->schedule();
            } catch (Exception $exception) {
                SentryException::getNewInstance('Failed to schedule remote product list options patch job.', $exception);
            }
        }
    }
}
