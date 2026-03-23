<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceResourceTypes;

/**
 * Maintains a mapping between a local resource ID and its corresponding remote commerce ID.
 */
class ResourceMap extends AbstractDataObject
{
    /** @var string the (commerce) remote ID */
    public string $commerceId;

    /** @var int the local ID as stored in the WooCommerce database */
    public int $localId;

    /** @var string the v2 {@see CommerceResourceTypes} this mapping corresponds to */
    public string $resourceType;

    /**
     * @param array{commerceId: string, localId: int, resourceType: string} $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
