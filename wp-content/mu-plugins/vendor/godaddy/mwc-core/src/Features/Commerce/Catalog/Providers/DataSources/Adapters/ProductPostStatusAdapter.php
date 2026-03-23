<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataSources\Adapters;

class ProductPostStatusAdapter
{
    /**
     * Since Woo supports more product statuses than the Commerce Platform we are selective about how to map the statuses:
     * - Published: Are active in the platform.
     * - Draft: inactive in the platform.
     * - Trash: since the platform does not support soft deletes (in a way that's manageable by the merchant) a locally trashed post is _also_ inactive in the platform.
     * - Other statuses are maintained as is and not mapped from the platform.
     *
     * @param bool $isRemoteProductActive
     * @param string $localPostStatus
     * @return string
     */
    public function convertToSource(bool $isRemoteProductActive, string $localPostStatus) : string
    {
        // Since both draft and trashed are mapped to inactive, when products in either status is active in the
        // platform they should become published locally.
        if ($isRemoteProductActive && ('draft' === $localPostStatus || 'trash' === $localPostStatus)) {
            return 'publish';
        }

        // When the product is inactive in the Platform and published locally the product has been
        // deactivated for web and should be draft locally.
        if (! $isRemoteProductActive && 'publish' === $localPostStatus) {
            return 'draft';
        }

        // The `$localPostStatus` is empty when the product is new and has not been saved yet.
        // This condition defines the default status for new products.
        if ('' == $localPostStatus) {
            return $isRemoteProductActive ? 'publish' : 'draft';
        }

        // All other posts statuses (i.e. pending, private) are maintained regardless of the product's status in the platform.
        return $localPostStatus;
    }
}
