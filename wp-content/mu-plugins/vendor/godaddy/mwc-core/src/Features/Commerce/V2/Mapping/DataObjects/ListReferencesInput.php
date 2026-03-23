<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\AbstractPaginatedInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\GoDaddy\Http\GraphQL\Queries\ListsReferencesOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters\ListReferencesRequestAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesInputContract;

/**
 * Input data object for {@see ListsReferencesOperation, ListReferencesRequestAdapter}.
 */
class ListReferencesInput extends AbstractPaginatedInput implements ReferencesInputContract
{
    /** @var string[] Array of reference values to filter by */
    public array $referenceValues;

    /**
     * Creates a new list references input data object.
     *
     * @param array{
     *     storeId: string,
     *     referenceValues: string[],
     *     cursor?: string|null,
     *     perPage: int
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
