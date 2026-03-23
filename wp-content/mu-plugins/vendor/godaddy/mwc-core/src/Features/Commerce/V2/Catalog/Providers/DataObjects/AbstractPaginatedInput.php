<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;

abstract class AbstractPaginatedInput extends AbstractDataObject
{
    /** @var string Store ID */
    public string $storeId;

    /** @var string|null Cursor for pagination */
    public ?string $cursor;

    /** @var int Number of items per page */
    public int $perPage;

    /**
     * Creates a new paginated input data object.
     *
     * @param array{
     *     storeId: string,
     *     cursor?: string|null,
     *     perPage: int
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct(array_merge([
            'cursor' => null,
        ], $data));
    }
}
