<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits\BuildsProductsMappingStrategyTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractMappingStrategyFactory;

class ProductsMappingStrategyFactory extends AbstractMappingStrategyFactory
{
    use BuildsProductsMappingStrategyTrait;

    protected ProductMapRepository $productMapRepository;

    public function __construct(CommerceContextContract $commerceContext, ProductMapRepository $productMapRepository)
    {
        parent::__construct($commerceContext);

        $this->productMapRepository = $productMapRepository;
    }
}
