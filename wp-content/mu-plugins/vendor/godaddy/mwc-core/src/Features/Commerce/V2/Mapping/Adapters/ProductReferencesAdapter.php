<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters;

use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\GraphQLHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\Adapters\MediaObjectAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\Adapters\ReferencesAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\MediaObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ProductReferences;

/**
 * Adapter to convert individual SKU references data from GraphQL response.
 * Returns ProductReferences data object containing data required to map local product.
 *
 * Important: The source should be an individual SKU node, not the full GraphQL response.
 *
 * Note: In the case of `mediaObjects`, the "reference" data is actually contain in its `metafield` property as
 *       metaObjects schema does not contain a `references` property.
 */
class ProductReferencesAdapter implements DataSourceAdapterContract
{
    use CanGetNewInstanceTrait;

    /** @var array<string, mixed> */
    protected array $source;

    /**
     * ProductReferencesAdapter constructor.
     *
     * @param array<string, mixed> $source Individual SKU node from GraphQL response
     */
    public function __construct(array $source)
    {
        $this->source = $source;
    }

    /**
     * Converts from individual SKU node to ProductReferences.
     *
     * @return ProductReferences
     */
    public function convertFromSource() : ProductReferences
    {
        return ProductReferences::getNewInstance([
            'skuId'              => ArrayHelper::getStringValueForKey($this->source, 'id', ''),
            'skuGroupId'         => ArrayHelper::getStringValueForKey($this->source, 'skuGroup.id', ''),
            'skuCode'            => ArrayHelper::getStringValueForKey($this->source, 'code', ''),
            'skuReferences'      => $this->extractSkuReferences(),
            'skuGroupReferences' => $this->extractSkuGroupReferences(),
            'mediaObjects'       => $this->extractMediaObjects(),
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
     * Extracts SKU references from the current SKU node.
     *
     * @return Reference[]
     */
    protected function extractSkuReferences() : array
    {
        $adapter = ReferencesAdapter::getNewInstance($this->source);

        return $adapter->convertFromSource();
    }

    /**
     * Extracts SKU group references from the current SKU node.
     *
     * @return Reference[]
     */
    protected function extractSkuGroupReferences() : array
    {
        $skuGroupData = TypeHelper::arrayOfStringsAsKeys(ArrayHelper::get($this->source, 'skuGroup'));
        if (! empty($skuGroupData)) {
            $adapter = ReferencesAdapter::getNewInstance($skuGroupData);

            return $adapter->convertFromSource();
        }

        return [];
    }

    /**
     * Extracts media object references from the current SKU node.
     *
     * @return MediaObject[]
     */
    protected function extractMediaObjects() : array
    {
        $mediaObjects = [];
        $mediaObjectNodes = GraphQLHelper::extractGraphQLEdges($this->source, 'mediaObjects');

        foreach ($mediaObjectNodes as $mediaObjectNode) {
            if (! is_array($mediaObjectNode)) {
                continue;
            }

            /** @var array<string, mixed> $mediaObjectNode */
            $adapter = new MediaObjectAdapter($mediaObjectNode);
            $mediaObject = $adapter->convertFromSource();

            if ($mediaObject) {
                $mediaObjects[] = $mediaObject;
            }
        }

        return $mediaObjects;
    }
}
