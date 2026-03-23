<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Providers\Catalog;

use GoDaddy\WordPress\MWC\Common\Container\Providers\AbstractServiceProvider;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\SkuReferencesServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\SkuReferencesService;

class SkuReferencesServiceProvider extends AbstractServiceProvider
{
    protected array $provides = [
        SkuReferencesServiceContract::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function register() : void
    {
        $this->getContainer()->singleton(SkuReferencesServiceContract::class, SkuReferencesService::class);
    }
}
