<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Jobs;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Models\Image;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\DatabaseRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceResourceTypes;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceTables;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\ResourceMapCollection;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\ProductMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\MediaObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\DataObjects\Reference;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ProductReferences;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ResourceMap;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\MediaMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\SkuGroupMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\SkuMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\SkuReferencesServiceContract;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Adapters\ProductAdapter;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Models\Products\Product;

/**
 * Product mapping job class.
 *
 * This job maps local WooCommerce products to V2 Commerce API UUIDs.
 */
class ProductMappingJob extends AbstractMappingJob
{
    /** @var string unique identifier for the queue.jobs config */
    public const JOB_KEY = 'v2ProductMapping';

    /** @var SkuGroupMapRepository for inserting new v2 records */
    protected SkuGroupMapRepository $skuGroupMapRepository;

    /** @var SkuMapRepository for inserting new v2 records */
    protected SkuMapRepository $skuMapRepository;

    /** @var ProductMapRepository for querying old v1 records */
    protected ProductMapRepository $v1ResourceRepository;

    /** @var MediaMapRepository for inserting new v2 records */
    protected MediaMapRepository $mediaMapRepository;

    public function __construct(
        SkuGroupMapRepository $skuGroupMapRepository,
        SkuMapRepository $skuMapRepository,
        ProductMapRepository $productMapRepository,
        SkuReferencesServiceContract $skuReferencesService,
        MediaMapRepository $mediaMapRepository
    ) {
        $this->skuGroupMapRepository = $skuGroupMapRepository;
        $this->skuMapRepository = $skuMapRepository;
        $this->v1ResourceRepository = $productMapRepository;
        $this->referencesService = $skuReferencesService;
        $this->mediaMapRepository = $mediaMapRepository;

        $this->setJobSettings($this->configureJobSettings());
    }

    /** {@inheritDoc} */
    protected function getLocalResourceMapsByLocalIds(array $localIds) : ResourceMapCollection
    {
        return $this->v1ResourceRepository->getMappingsByLocalIds($localIds);
    }

    /**
     * Gets the SQL string for the unmapped local IDs query.
     *
     * We specifically base our query on the mapping table rather than the wp_posts table to ensure we only pick up
     * products that have been mapped to the v1 resource type. We don't want to return products that have never
     * been mapped to the v1 resource type, as those products will not require a v2 mapping.
     *
     * See example below for a resulting string.
     *
     * @return string
     */
    protected function getUnmappedLocalIdsSqlString() : string
    {
        $db = DatabaseRepository::instance();

        $skuGroupResourceTypeId = $this->skuGroupMapRepository->getResourceTypeId();
        $mappedSkuGroupIds = TypeHelper::string($db->prepare(
            /* @phpstan-ignore-next-line the only reason it's not a literal string is because we use constants to reference table/column names */
            $this->skuGroupMapRepository->getMappedLocalIdsForResourceTypeQuery(),
            $skuGroupResourceTypeId
        ), '');

        $skuResourceTypeId = $this->skuMapRepository->getResourceTypeId();
        $mappedSkuIds = TypeHelper::string($db->prepare(
            /* @phpstan-ignore-next-line the only reason it's not a literal string is because we use constants to reference table/column names */
            $this->skuMapRepository->getMappedLocalIdsForResourceTypeQuery(),
            $skuResourceTypeId
        ), '');

        $productsResourceTypeId = $this->v1ResourceRepository->getResourceTypeId();
        $resourceMapsTable = CommerceTables::ResourceMap;

        // Example:
        // SELECT godaddy_mwc_commerce_map_ids.local_id
        // FROM godaddy_mwc_commerce_map_ids
        // WHERE godaddy_mwc_commerce_map_ids.resource_type_id = 1
        //     AND godaddy_mwc_commerce_map_ids.local_id NOT IN (SELECT local_id FROM godaddy_mwc_commerce_map_ids WHERE resource_type_id = 11)
        //     AND godaddy_mwc_commerce_map_ids.local_id NOT IN (SELECT local_id FROM godaddy_mwc_commerce_map_ids WHERE resource_type_id = 12)
        // LIMIT 50
        return "
        SELECT {$resourceMapsTable}.local_id
        FROM {$resourceMapsTable}
        WHERE {$resourceMapsTable}.resource_type_id = {$productsResourceTypeId}
            AND {$resourceMapsTable}.local_id NOT IN ({$mappedSkuGroupIds})
            AND {$resourceMapsTable}.local_id NOT IN ({$mappedSkuIds})
        LIMIT %d
        ";
    }

    /**
     * {@inheritDoc}
     */
    protected function addLocalMappingRecord(ResourceMap $referenceMap) : void
    {
        if ($referenceMap->resourceType === CommerceResourceTypes::SkuGroup) {
            $this->skuGroupMapRepository->add($referenceMap->localId, $referenceMap->commerceId);
        } elseif ($referenceMap->resourceType === CommerceResourceTypes::Sku) {
            $this->skuMapRepository->add($referenceMap->localId, $referenceMap->commerceId);
        } elseif ($referenceMap->resourceType === CommerceResourceTypes::Media) {
            $this->mediaMapRepository->add($referenceMap->localId, $referenceMap->commerceId);
        }
    }

    /**
     * {@inheritDoc}
     * @param ProductReferences[] $references
     */
    protected function buildReferenceMap(array $references, ResourceMapCollection $resourceMapCollection) : array
    {
        $resourceMaps = [];

        foreach ($references as $productReference) {
            $skuGroupMapping = $this->findAndBuildSkuGroupResourceMap($productReference, $resourceMapCollection);
            if ($skuGroupMapping) {
                $resourceMaps[] = $skuGroupMapping;
            }

            $skuMapping = $this->findAndBuildSkuResourceMap($productReference, $resourceMapCollection);
            if ($skuMapping) {
                $resourceMaps[] = $skuMapping;

                $resourceMaps = array_merge($resourceMaps, $this->buildAssetReferenceMaps($productReference->mediaObjects, $skuMapping->localId));
            }
        }

        /* @var ResourceMap[] $resourceMaps */
        return $resourceMaps;
    }

    /**
     * Attempts to find and build the SKU group resource map for the given product references.
     */
    protected function findAndBuildSkuGroupResourceMap(ProductReferences $productReferences, ResourceMapCollection $resourceMapCollection) : ?ResourceMap
    {
        return $this->findAndBuildResourceMap(
            $productReferences->skuGroupReferences,
            $resourceMapCollection,
            $productReferences->skuGroupId,
            CommerceResourceTypes::SkuGroup
        );
    }

    /**
     * Attempts to find and build the SKU resource map for the given product references.
     */
    protected function findAndBuildSkuResourceMap(ProductReferences $productReferences, ResourceMapCollection $resourceMapCollection) : ?ResourceMap
    {
        return $this->findAndBuildResourceMap(
            $productReferences->skuReferences,
            $resourceMapCollection,
            $productReferences->skuId,
            CommerceResourceTypes::Sku
        );
    }

    /**
     * Attempts to find and build the asset reference maps for the given media objects and local product ID.
     *
     * @param MediaObject[] $commerceMediaObjects
     * @param int $localProductId
     * @return ResourceMap[]
     */
    protected function buildAssetReferenceMaps(array $commerceMediaObjects, int $localProductId) : array
    {
        $resourceMaps = [];
        if (empty($commerceMediaObjects)) {
            return [];
        }

        $localAssets = $this->getLocalCoreAssets($localProductId);
        if (empty($localAssets)) {
            return $resourceMaps;
        }

        foreach ($localAssets as $localAsset) {
            try {
                $localUrl = $localAsset->getSize('full')->getUrl();
            } catch(Exception $e) {
                $localUrl = null;
            }
            if (! $localUrl) {
                continue;
            }

            foreach ($commerceMediaObjects as $mediaObject) {
                if ($mediaObject->url === $localUrl) {
                    $resourceMaps[] = new ResourceMap([
                        'commerceId'   => $mediaObject->id,
                        'localId'      => $localAsset->getId() ?? 0,
                        'resourceType' => CommerceResourceTypes::Media,
                    ]);
                    break; // No need to check other media objects for this local asset
                }
            }
        }

        return $resourceMaps;
    }

    /**
     * Gets the local core assets for the given local product ID.
     *
     * @param int $localProductId
     * @return Image[]
     */
    protected function getLocalCoreAssets(int $localProductId) : array
    {
        /** @var Product|null $coreProduct */
        $coreProduct = CatalogIntegration::withoutReads(function () use ($localProductId) {
            $wooProduct = wc_get_product($localProductId);
            if (! $wooProduct) {
                return null;
            }

            return ProductAdapter::getNewInstance($wooProduct)->convertFromSource();
        });

        if (! $coreProduct) {
            return [];
        }

        return array_values(array_filter(array_merge([$coreProduct->getMainImage()], $coreProduct->getImages())));
    }

    /**
     * Builds a {@see ResourceMap}, given the provided references and resource type.
     *
     * @param Reference[] $references
     * @param ResourceMapCollection $resourceMapCollection
     * @param string $commerceId
     * @param string $resourceType
     * @return ResourceMap|null
     */
    protected function findAndBuildResourceMap(
        array $references,
        ResourceMapCollection $resourceMapCollection,
        string $commerceId,
        string $resourceType
    ) : ?ResourceMap {
        $v1Id = $this->getV1Reference($references);
        if (! $v1Id) {
            return null;
        }

        $localId = $resourceMapCollection->getLocalId($v1Id);
        if (! $localId) {
            return null;
        }

        return new ResourceMap([
            'commerceId'   => $commerceId,
            'localId'      => $localId,
            'resourceType' => $resourceType,
        ]);
    }

    /** {@inheritDoc} */
    protected function getV1OriginString() : string
    {
        return 'catalog-api-v1-product';
    }

    /** {@inheritDoc} */
    protected function onAllBatchesCompleted() : void
    {
        update_option('mwc_v2_product_mapping_completed_at', date('Y-m-d H:i:s'));
    }
}
