<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Traits;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use WC_Product;

/**
 * Trait for getting a batch of local products.
 */
trait CanGetLocalProductsBatchTrait
{
    /**
     * Gets the offset to use in the query. This is incremented each batch run.
     *
     * @return int
     */
    protected function getOffsetForBatch() : int
    {
        return max(TypeHelper::int(ArrayHelper::get($this->args, 'offset'), 0), 0);
    }

    /**
     * Queries for a batch of local products.
     *
     * @return WC_Product[]
     */
    protected function getLocalProductsBatch() : array
    {
        /** @var WC_Product[] $products */
        $products = CatalogIntegration::withoutReads(function () {
            return ProductsRepository::query([
                'offset'         => $this->getOffsetForBatch(),
                'posts_per_page' => $this->getJobSettings()->maxPerBatch,
            ]);
        });

        return $products;
    }

    /**
     * Increments the query offset in preparation for the next batch.
     *
     * @return void
     */
    protected function incrementOffsetForNextBatch() : void
    {
        $currentOffset = $this->getOffsetForBatch();

        if (! is_array($this->args)) {
            $this->args = [];
        }

        $this->args['offset'] = $currentOffset + $this->getJobSettings()->maxPerBatch;
    }
}
