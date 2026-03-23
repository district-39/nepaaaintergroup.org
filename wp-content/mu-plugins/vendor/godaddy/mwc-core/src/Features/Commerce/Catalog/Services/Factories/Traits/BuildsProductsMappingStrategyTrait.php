<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsMappingStrategyContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\ProductMappingStrategy;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Models\Products\Product;

trait BuildsProductsMappingStrategyTrait
{
    /**
     * Gets the main mapping strategy for Products.
     *
     * @param Product|object $model
     * @return ProductsMappingStrategyContract|null
     */
    public function getPrimaryMappingStrategyFor(object $model) : ?ProductsMappingStrategyContract
    {
        if ($model instanceof Product && $model->getId()) {
            return $this->getProductMappingStrategy();
        }

        return null;
    }

    /**
     * Get the product mapping strategy.
     *
     * @return ProductsMappingStrategyContract
     */
    protected function getProductMappingStrategy() : ProductsMappingStrategyContract
    {
        return new ProductMappingStrategy($this->productMapRepository);
    }

    /**
     * Get the fallback mapping strategy.
     *
     * @return ProductsMappingStrategyContract
     */
    public function getSecondaryMappingStrategy() : ProductsMappingStrategyContract
    {
        return new class implements ProductsMappingStrategyContract {
            /**
             * {@inheritDoc}
             */
            public function saveRemoteId(object $model, string $remoteId) : void
            {
                // no-op
            }

            /**
             * {@inheritDoc}
             */
            public function getRemoteId(object $model) : ?string
            {
                return null;
            }
        };
    }
}
