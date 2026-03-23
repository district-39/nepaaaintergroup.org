<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\MediaObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;

/**
 * Contains remote references need to map local product.
 */
class ProductReferences extends AbstractDataObject
{
    /** @var string the v2 UUID for the product's SKU */
    public string $skuId;

    /** @var string the v2 UUID for the product's SKU group */
    public string $skuGroupId = '';

    /** @var string the product SKU */
    public string $skuCode;

    /** @var Reference[] */
    public array $skuReferences;

    /** @var Reference[] */
    public array $skuGroupReferences;

    /** @var MediaObject[] */
    public array $mediaObjects;

    /**
     * Creates a new data object.
     *
     * @param array{
     *     skuId: string,
     *     skuCode: string,
     *     skuReferences: Reference[],
     *     skuGroupReferences: Reference[],
     *     mediaObjects: MediaObject[]
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
