<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\CategoriesMappingStrategyContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits\BuildsCategoriesMappingStrategyTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractMappingStrategyFactory;

/**
 * Factory to return a {@see CategoriesMappingStrategyContract} for the provided model.
 */
class CategoriesMappingStrategyFactory extends AbstractMappingStrategyFactory
{
    use BuildsCategoriesMappingStrategyTrait;

    protected CategoryMapRepository $categoryMapRepository;

    public function __construct(CommerceContextContract $commerceContext, CategoryMapRepository $categoryMapRepository)
    {
        parent::__construct($commerceContext);
        $this->categoryMapRepository = $categoryMapRepository;
    }
}
