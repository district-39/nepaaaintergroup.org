<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Factories;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits\BuildsProductsMappingStrategyTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractMappingStrategyFactory;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\SkuGroupMapRepository;

class SkuGroupsMappingStrategyFactory extends AbstractMappingStrategyFactory
{
    use BuildsProductsMappingStrategyTrait;

    protected SkuGroupMapRepository $productMapRepository;

    public function __construct(CommerceContextContract $commerceContext, SkuGroupMapRepository $productMapRepository)
    {
        parent::__construct($commerceContext);
        $this->productMapRepository = $productMapRepository;
    }
}
