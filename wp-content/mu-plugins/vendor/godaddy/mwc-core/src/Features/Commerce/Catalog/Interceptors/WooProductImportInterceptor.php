<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers\WooProductImportDoneHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers\WooProductImportStartedHandler;

/**
 * Interceptor to handle WooCommerce product import.
 */
class WooProductImportInterceptor extends AbstractInterceptor
{
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function addHooks() : void
    {
        // Note: this fires for each row of the csv being imported.
        Register::action()
            ->setGroup('woocommerce_product_importer_before_set_parsed_data')
            ->setHandler([WooProductImportStartedHandler::class, 'handle'])
            ->setPriority(PHP_INT_MAX)
            ->execute();

        Register::action()
            ->setGroup('admin_init')
            ->setHandler([WooProductImportDoneHandler::class, 'handle'])
            ->setPriority(PHP_INT_MAX)
            ->execute();
    }
}
