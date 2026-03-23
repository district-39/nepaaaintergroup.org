<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Contracts\CanConvertToArrayContract;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\GoDaddy\Adapters\Traits\CanConvertProductResponseTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Services\Contracts\ProductsCachingServiceContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\MissingProductRemoteIdException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\AbstractCachingService;

/**
 * Caching service for products.
 *
 * @method ProductBase|null get(string $resourceIdentifier)
 * @method ProductBase set(CanConvertToArrayContract $resource)
 */
class ProductsCachingService extends AbstractCachingService implements ProductsCachingServiceContract
{
    use CanConvertProductResponseTrait;

    /** {@inheritDoc} */
    protected string $resourceType = 'products';

    protected function getDefaultCacheTtlInSeconds() : int
    {
        return TypeHelper::int(Configuration::get('commerce.catalog.api.products.cache.ttl'), DAY_IN_SECONDS);
    }

    /**
     * Converts the product response to a {@see ProductBase} DTO.
     *
     * @param array<string, mixed> $resourceArray
     * @return ProductBase
     * @throws MissingProductRemoteIdException
     */
    protected function makeResourceFromArray(array $resourceArray) : object
    {
        return $this->convertProductResponse($resourceArray);
    }

    /**
     * Gets the remote ID of the given resource.
     *
     * @param ProductBase&object $resource
     * @return string
     * @throws MissingProductRemoteIdException
     */
    protected function getResourceIdentifier(object $resource) : string
    {
        if (! empty($resource->productId)) {
            return $resource->productId;
        }

        throw MissingProductRemoteIdException::withDefaultMessage();
    }
}
