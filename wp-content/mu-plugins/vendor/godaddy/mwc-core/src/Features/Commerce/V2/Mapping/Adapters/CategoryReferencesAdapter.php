<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters;

use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\Adapters\ReferencesAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\CategoryReferences;

/**
 * Adapter to convert individual list references data from GraphQL response.
 *
 * Important: The source should be an individual list node, not the full GraphQL response.
 */
class CategoryReferencesAdapter implements DataSourceAdapterContract
{
    use CanGetNewInstanceTrait;

    /** @var array<string, mixed> */
    protected array $source;

    /**
     * CategoryReferencesAdapter constructor.
     *
     * @param array<string, mixed> $source Individual list node from GraphQL response
     */
    public function __construct(array $source)
    {
        $this->source = $source;
    }

    /**
     * Converts from individual list node to CategoryReferences.
     */
    public function convertFromSource() : CategoryReferences
    {
        return new CategoryReferences([
            'listId'         => TypeHelper::string(ArrayHelper::get($this->source, 'id'), ''),
            'listName'       => TypeHelper::string(ArrayHelper::get($this->source, 'name'), ''),
            'listReferences' => $this->extractListReferences(),
        ]);
    }

    /**
     * Converts to GraphQL format (not implemented).
     *
     * @return array<string, mixed>
     */
    public function convertToSource() : array
    {
        return [];
    }

    /**
     * Extracts list references from the current list node.
     *
     * @return Reference[]
     */
    protected function extractListReferences() : array
    {
        return (new ReferencesAdapter($this->source))->convertFromSource();
    }
}
