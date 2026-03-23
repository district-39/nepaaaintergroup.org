<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;

class Reference extends AbstractDataObject
{
    /** @var ?string the v2 `origin` value (ex. `"catalog-api-v1-level"`) */
    public ?string $origin = null;

    /** @var ?string the v1 UUID for reference */
    public ?string $value = null;

    /**
     * @param array{
     *      origin?: string,
     *      value?: string,
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
