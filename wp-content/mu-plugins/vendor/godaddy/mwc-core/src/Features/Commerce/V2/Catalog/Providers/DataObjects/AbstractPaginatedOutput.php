<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;

abstract class AbstractPaginatedOutput extends AbstractDataObject
{
    /** @var bool Whether there are more results */
    public bool $hasNextPage;

    /** @var string|null Cursor for the next page */
    public ?string $endCursor;

    /**
     * AbstractPaginatedOutput constructor.
     *
     * @param array{
     *     hasNextPage?: bool,
     *     endCursor?: string|null
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct(array_merge([
            'hasNextPage' => false,
            'endCursor'   => null,
        ], $data));
    }
}
