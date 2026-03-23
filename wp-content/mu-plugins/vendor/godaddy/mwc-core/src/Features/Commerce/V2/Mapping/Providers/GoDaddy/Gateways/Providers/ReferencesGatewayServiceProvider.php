<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\GoDaddy\Gateways\Providers;

use GoDaddy\WordPress\MWC\Common\Container\Providers\AbstractServiceProvider;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\Contracts\ReferencesGatewayContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\GoDaddy\Gateways\ReferencesGateway;

/**
 * Service provider for catalog gateway services.
 */
class ReferencesGatewayServiceProvider extends AbstractServiceProvider
{
    protected array $provides = [
        ReferencesGatewayContract::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function register() : void
    {
        $this->getContainer()->singleton(ReferencesGatewayContract::class, ReferencesGateway::class);
    }
}
