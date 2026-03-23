<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;

/**
 * A DTO representing Commerce API metadata.
 */
class Metadata extends AbstractDataObject
{
    public string $type = 'JSON';

    public string $key;

    public string $value;

    /**
     * Creates a new data object.
     *
     * @param array{
     *     type: string,
     *     key: string,
     *     value: string,
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
