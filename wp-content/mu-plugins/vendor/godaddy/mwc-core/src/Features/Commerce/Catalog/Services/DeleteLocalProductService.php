<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;

class DeleteLocalProductService
{
    /**
     * The mapping entry for the given local ID is automatically removed via `delete_post` hook in {@see LocalProductDeletedInterceptor}.
     */
    public function delete(int $localId) : void
    {
        /*
         * Disable reads, because `wp_delete_post()` issues a `get_post()` call that we do not need to be routed to the platform.
         * Disable writes, because \GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Interceptors\Handlers::handleUpdatingDeletedProductInPlatform()
         * triggers when a product is deleted in Woo to then update the remote product. But in this case the deletion
         * _originated_ in the platform and we're reacting to it, so we do not need to update the remote platform
         * yet again.
         */
        CatalogIntegration::withoutWrites(fn () => CatalogIntegration::withoutReads(fn () => wp_delete_post($localId, true)));
    }
}
