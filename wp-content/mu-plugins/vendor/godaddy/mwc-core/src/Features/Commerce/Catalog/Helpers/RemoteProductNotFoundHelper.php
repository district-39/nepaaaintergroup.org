<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Helpers;

use DateTime;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Schedule\Exceptions\InvalidScheduleException;
use GoDaddy\WordPress\MWC\Common\Schedule\Schedule;
use GoDaddy\WordPress\MWC\Common\Schedule\Types\RecurringAction;
use GoDaddy\WordPress\MWC\Common\Schedule\Types\SingleAction;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\DeleteProductDeletedUpstreamJobInterceptor;

class RemoteProductNotFoundHelper
{
    /**
     * Handles the scenario where we have a local product ID, a mapping entry linking it to a remote ID, but the request returned a 404 response.
     *
     * When this happens, that's an indicator that the remote product existed at one point, but has now been deleted.
     * We now want to delete the local WooCommerce instance to match.
     */
    public function handle(int $localId) : void
    {
        $this->tryToSchedule($this->getActionToDeleteProductDeletedUpstream($localId));
    }

    /**
     * @param SingleAction|RecurringAction $job
     */
    protected function tryToSchedule(Schedule $job) : void
    {
        if ($job->isScheduled()) {
            return;
        }

        try {
            $job->schedule();
        } catch (InvalidScheduleException $e) {
            SentryException::getNewInstance('Failed to schedule job for deleting local product that was deleted upstream: '.$e->getMessage(), $e);
        }
    }

    protected function getActionToDeleteProductDeletedUpstream(int $localId) : SingleAction
    {
        return Schedule::singleAction()
            ->setName(DeleteProductDeletedUpstreamJobInterceptor::JOB_NAME)
            ->setArguments($localId)
            ->setScheduleAt(new DateTime());
    }
}
