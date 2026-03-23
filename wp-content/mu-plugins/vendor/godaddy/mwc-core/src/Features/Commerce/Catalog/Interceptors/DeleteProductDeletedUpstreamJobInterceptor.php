<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers\DeleteProductDeletedUpstreamJobHandler;

/**
 * Callback for the Action Scheduler job to delete products that are marked as deleted upstream.
 */
class DeleteProductDeletedUpstreamJobInterceptor extends AbstractInterceptor
{
    /** @var string */
    public const JOB_NAME = 'mwc_gd_commerce_delete_product_deleted_upstream';

    /**
     * @throws Exception
     */
    public function addHooks() : void
    {
        Register::action()
            ->setGroup(static::JOB_NAME)
            ->setArgumentsCount(1)
            ->setHandler([DeleteProductDeletedUpstreamJobHandler::class, 'handle'])
            ->execute();
    }
}
