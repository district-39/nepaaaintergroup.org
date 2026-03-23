<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Factories;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Factories\Traits\BuildsProductsMappingStrategyTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\Contracts\CommerceContextContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractMappingStrategyFactory;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\SkuMapRepository;

class SkuMappingStrategyFactory extends AbstractMappingStrategyFactory
{
    use BuildsProductsMappingStrategyTrait;

    protected SkuMapRepository $productMapRepository;

    public function __construct(CommerceContextContract $commerceContext, SkuMapRepository $productMapRepository)
    {
        parent::__construct($commerceContext);
        $this->productMapRepository = $productMapRepository;
    }
}
