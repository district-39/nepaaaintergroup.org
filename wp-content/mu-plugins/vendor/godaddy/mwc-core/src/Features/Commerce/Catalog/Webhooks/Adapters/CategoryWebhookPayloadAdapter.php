<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Webhooks\Adapters;

use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects\Categories\Category;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\GoDaddy\Adapters\Traits\CanConvertCategoryResponseTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\MissingCategoryRemoteIdException;

/**
 * Adapter to convert a Category webhook payload to a {@see Category} object.
 */
class CategoryWebhookPayloadAdapter
{
    use CanGetNewInstanceTrait;
    use CanConvertCategoryResponseTrait;

    /**
     * Converts the Category webhook payload response.
     *
     * @param array<string, mixed> $payload
     *
     * @throws MissingCategoryRemoteIdException
     */
    public function convertResponse(array $payload) : Category
    {
        return $this->convertCategoryResponse($payload);
    }
}
