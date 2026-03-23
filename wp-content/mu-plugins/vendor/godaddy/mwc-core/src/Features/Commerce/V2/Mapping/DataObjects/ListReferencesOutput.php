<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\AbstractPaginatedOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesOutputContract;

/**
 * Output data object for categories references query response.
 */
class ListReferencesOutput extends AbstractPaginatedOutput implements ReferencesOutputContract
{
    /** @var CategoryReferences[] Array of category references */
    public array $categoryReferences;

    /**
     * ListReferencesOutput constructor.
     *
     * @param array{
     *     hasNextPage: bool,
     *     categoryReferences: CategoryReferences[],
     *     endCursor?: string|null
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
