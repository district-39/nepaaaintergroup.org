<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;

/**
 * Contains references related to a local category.
 */
class CategoryReferences extends AbstractDataObject
{
    /** @var string the v2 UUID for the category */
    public string $listId;

    /** @var string the category name */
    public string $listName;

    /** @var Reference[] */
    public array $listReferences;

    /**
     * Creates a new data object.
     *
     * @param array{
     *     listId: string,
     *     listName: string,
     *     listReferences: Reference[]
     * } $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
