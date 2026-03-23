<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\Adapters;

use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\GraphQLHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;

/**
 * Adapter to convert UUID references data from GraphQL response.
 *
 * @method static static getNewInstance(array<string, mixed> $source)
 */
class ReferencesAdapter implements DataSourceAdapterContract
{
    use CanGetNewInstanceTrait;

    /** @var array<string, mixed> A graphQL node containing references structure contains an origin & UUID (ex. sku or skuGroup) */
    protected array $source;

    /**
     * UuidReferencesAdapter constructor.
     *
     * @param array<string, mixed> $source SKU or SKU group node containing references structure
     */
    public function __construct(array $source)
    {
        $this->source = $source;
    }

    /**
     * Converts from GraphQL response format to array of UuidReferences.
     *
     * @return Reference[]
     */
    public function convertFromSource() : array
    {
        $uuidReferences = [];
        $references = GraphQLHelper::extractGraphQLEdges($this->source, 'references');

        foreach ($references as $reference) {
            if (! is_array($reference) || empty($reference)) {
                continue;
            }

            $uuidReferences[] = new Reference([
                'origin' => TypeHelper::string(ArrayHelper::get($reference, 'origin'), ''),
                'value'  => TypeHelper::string(ArrayHelper::get($reference, 'value'), ''),
            ]);
        }

        return $uuidReferences;
    }

    /**
     * @return array<string, mixed>
     */
    public function convertToSource() : array
    {
        // no-op.
        return [];
    }
}
