<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Adapters;

use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\ProductBase;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\GoDaddy\Adapters\Traits\CanConvertProductResponseTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\MissingProductRemoteIdException;

class ProductWebhookPayloadAdapter
{
    use CanGetNewInstanceTrait;
    use CanConvertProductResponseTrait;

    /**
     * Converts the Product webhook payload to an {@see ProductBase} object.
     *
     * @param array<mixed> $payload
     *
     * @throws MissingProductRemoteIdException
     */
    public function convertResponse(array $payload) : ProductBase
    {
        return $this->convertProductResponse($payload);
    }
}
