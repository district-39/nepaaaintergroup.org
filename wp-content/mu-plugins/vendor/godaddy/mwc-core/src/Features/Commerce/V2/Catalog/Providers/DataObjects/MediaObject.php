<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;

/**
 * Media object reference data object.
 */
class MediaObject extends AbstractDataObject
{
    public string $id;

    public string $url;

    /**
     * Creates a new data object.
     *
     * @param array{
     *     id: string,
     *     url: string,
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
