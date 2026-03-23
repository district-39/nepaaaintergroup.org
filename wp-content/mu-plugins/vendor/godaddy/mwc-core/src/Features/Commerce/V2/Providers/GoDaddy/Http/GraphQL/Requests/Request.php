<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Providers\GoDaddy\Http\GraphQL\Requests;

use GoDaddy\WordPress\MWC\Common\Contracts\GraphQLOperationContract;
use GoDaddy\WordPress\MWC\Common\Events\Http\GraphQL\Contracts\RequestContract;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Http\GraphQL\Request as GraphQLRequest;
use GoDaddy\WordPress\MWC\Common\Http\Traits\CanSetAuthMethodTrait;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetEnvironmentBasedConfigValueTrait;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Http\Traits\HasManagedWooCommerceAuthProviderTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Http\Traits\IsAuthenticatedGraphQLRequestTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Traits\HasStoreIdentifierTrait;

/**
 * GraphQL request class for communicating with the Commerce Catalog API v2.
 */
class Request extends GraphQLRequest implements RequestContract
{
    use IsAuthenticatedGraphQLRequestTrait;
    use CanGetNewInstanceTrait;
    use CanGetEnvironmentBasedConfigValueTrait;
    use CanSetAuthMethodTrait;
    use HasStoreIdentifierTrait;
    use HasManagedWooCommerceAuthProviderTrait;

    protected GraphQLOperationContract $operation;

    public function setOperation(GraphQLOperationContract $operation) : Request
    {
        $this->operation = $operation;

        return $this;
    }

    public function getTimeout() : int
    {
        return TypeHelper::int($this->getEnvironmentConfigValue('commerce.gateway.api.timeout'), $this->timeout);
    }

    public function getBaseUrl() : string
    {
        return TypeHelper::string($this->getEnvironmentConfigValue('commerce.gateway.api.url'), 'https://api.godaddy.com');
    }

    public function getPath() : string
    {
        return '/v1/commerce/proxy/v2/stores/'.$this->getStoreId().'/catalog-subgraph';
    }

    public function send()
    {
        $this->setUrl($this->getBaseUrl().$this->getPath());

        $this->addHeaders([
            'X-Store-Id' => $this->getStoreId(),
        ]);

        return parent::send();
    }
}
