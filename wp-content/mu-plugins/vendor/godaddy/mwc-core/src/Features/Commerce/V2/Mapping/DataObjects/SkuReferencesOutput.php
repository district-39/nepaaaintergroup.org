<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\AbstractPaginatedOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesOutputContract;

/**
 * Output data object for SKU references query response.
 */
class SkuReferencesOutput extends AbstractPaginatedOutput implements ReferencesOutputContract
{
    /** @var ProductReferences[] Array of product references */
    public array $productReferences;

    /**
     * SkuReferencesOutput constructor.
     *
     * @param array{
     *     hasNextPage: bool,
     *     productReferences: ProductReferences[],
     *     endCursor?: string|null
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
