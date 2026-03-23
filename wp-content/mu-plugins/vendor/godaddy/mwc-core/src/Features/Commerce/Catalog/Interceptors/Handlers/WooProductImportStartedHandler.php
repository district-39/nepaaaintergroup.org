<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers;

use DateTime;
use DateTimeZone;
use GoDaddy\WordPress\MWC\Common\Interceptors\Handlers\AbstractInterceptorHandler;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Commerce;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Inventory\InventoryIntegration;

/**
 * Handles the WooCommerce product import started.
 *
 * This handler disables the integrations when the product import is started and records
 * the import start timestamp. If the integration is enabled while the import is running,
 * the platform can receive partial data, which can lead to inconsistencies.
 */
class WooProductImportStartedHandler extends AbstractInterceptorHandler
{
    /**
     * {@inheritDoc}
     */
    public function run(...$args)
    {
        /*
         * For the purposes of the import, we want to consider WooCommerce as the source of truth. In cases, where the
         * platform has become out of sync with WooCommerce (ex. https://github.com/gdcorp-partners/mwc-core/pull/7718),
         * the Import should overwrite the platform data to ensure consistency.
         *
         * By disabling the read capability, we ensure that the Woo importer uses the state of the products in WooCommerce,
         * to decide match updates and determine creation of new products.
         */
        if (CatalogIntegration::hasCommerceCapability(Commerce::CAPABILITY_READ)) {
            CatalogIntegration::disableCapability(Commerce::CAPABILITY_READ);
        }

        /*
         * We also disable the write capability to prevent any syncs from happening while the import is running.
         *
         * Particularly with variable products, WooCommerce creates temporary product states that should not be
         * synced to the platform {see https://github.com/gdcorp-partners/mwc-core/pull/7549}. Products will be synced
         * once the import is complete{@see WooProductImportDoneHandler}.
         */
        if (CatalogIntegration::hasCommerceCapability(Commerce::CAPABILITY_WRITE)) {
            CatalogIntegration::disableCapability(Commerce::CAPABILITY_WRITE);
        }

        if (InventoryIntegration::hasCommerceCapability(Commerce::CAPABILITY_WRITE)) {
            InventoryIntegration::disableCapability(Commerce::CAPABILITY_WRITE);
        }

        // Record the import start timestamp only if not already running
        $isAlreadyRunning = get_option('mwc_product_import_running');
        $timestamp = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        if (! $isAlreadyRunning) {
            update_option('mwc_product_import_started_at', $timestamp);

            // Set the running flag to prevent timestamp reset on subsequent batches
            update_option('mwc_product_import_running', true);
        }
    }
}
