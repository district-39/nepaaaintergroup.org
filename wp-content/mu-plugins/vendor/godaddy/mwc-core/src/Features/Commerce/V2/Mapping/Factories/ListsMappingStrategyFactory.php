<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Factories;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits\BuildsCategoriesMappingStrategyTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractMappingStrategyFactory;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\ListMapRepository;

/**
 * Factory to return a {@see CategoriesMappingStrategyContract} for the provided model.
 */
class ListsMappingStrategyFactory extends AbstractMappingStrategyFactory
{
    use BuildsCategoriesMappingStrategyTrait;

    protected ListMapRepository $categoryMapRepository;

    public function __construct(CommerceContextContract $commerceContext, ListMapRepository $categoryMapRepository)
    {
        parent::__construct($commerceContext);

        $this->categoryMapRepository = $categoryMapRepository;
    }
}
