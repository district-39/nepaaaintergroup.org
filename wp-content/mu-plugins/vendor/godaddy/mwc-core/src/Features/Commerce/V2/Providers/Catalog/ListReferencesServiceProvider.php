<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Providers\Catalog;

use GoDaddy\WordPress\MWC\Common\Container\Providers\AbstractServiceProvider;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\ListReferencesServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\ListReferencesService;

class ListReferencesServiceProvider extends AbstractServiceProvider
{
    protected array $provides = [
        ListReferencesServiceContract::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function register() : void
    {
        $this->getContainer()->singleton(ListReferencesServiceContract::class, ListReferencesService::class);
    }
}
