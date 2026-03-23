<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits;

use GoDaddy\WordPress\MWC\Common\Models\Term;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\CategoriesMappingStrategy;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\CategoriesMappingStrategyContract;

trait BuildsCategoriesMappingStrategyTrait
{
    /**
     * Gets the main mapping strategy for product categories.
     *
     * @param object|Term $model
     * @return CategoriesMappingStrategyContract|null
     */
    public function getPrimaryMappingStrategyFor(object $model) : ?CategoriesMappingStrategyContract
    {
        return $model instanceof Term && $model->getId()
            ? $this->getCategoryMappingStrategy()
            : null;
    }

    /**
     * Gets the product category mapping strategy.
     *
     * @return CategoriesMappingStrategyContract
     */
    protected function getCategoryMappingStrategy() : CategoriesMappingStrategyContract
    {
        return new CategoriesMappingStrategy($this->categoryMapRepository);
    }

    /**
     * Gets the fallback mapping strategy.
     *
     * @return CategoriesMappingStrategyContract
     */
    public function getSecondaryMappingStrategy() : CategoriesMappingStrategyContract
    {
        // we do not have a secondary mapping strategy at this time
        return new class implements CategoriesMappingStrategyContract {
            public function saveRemoteId(object $model, string $remoteId) : void
            {
                // no-op
            }

            public function getRemoteId(object $model) : ?string
            {
                // no-op
                return null;
            }
        };
    }
}
