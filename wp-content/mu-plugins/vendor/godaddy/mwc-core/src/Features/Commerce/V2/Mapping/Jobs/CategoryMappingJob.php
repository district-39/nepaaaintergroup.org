<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Jobs;

use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\DatabaseRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceResourceTypes;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceTables;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Models\ResourceMapCollection;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Repositories\CategoryMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\CategoryReferences;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ResourceMap;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories\ListMapRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\ListReferencesServiceContract;

/**
 * Category mapping job class.
 *
 * This job maps local WooCommerce categories to V2 Commerce API UUIDs.
 */
class CategoryMappingJob extends AbstractMappingJob
{
    /** @var string unique identifier for the queue.jobs config */
    public const JOB_KEY = 'v2CategoryMapping';

    /** @var ListMapRepository list map repository -- for inserting new v2 records */
    protected ListMapRepository $listMapRepository;

    /** @var CategoryMapRepository category map repository -- for querying old v1 records */
    protected CategoryMapRepository $v1ResourceRepository;

    /**
     * Constructor.
     */
    public function __construct(
        ListMapRepository $listMapRepository,
        CategoryMapRepository $categoryMapRepository,
        ListReferencesServiceContract $listReferencesService
    ) {
        $this->listMapRepository = $listMapRepository;
        $this->v1ResourceRepository = $categoryMapRepository;
        $this->referencesService = $listReferencesService;

        $this->setJobSettings($this->configureJobSettings());
    }

    /** {@inheritDoc} */
    protected function getLocalResourceMapsByLocalIds(array $localIds) : ResourceMapCollection
    {
        return $this->v1ResourceRepository->getMappingsByLocalIds($localIds);
    }

    /** {@inheritDoc} */
    protected function getUnmappedLocalIdsSqlString() : string
    {
        $db = DatabaseRepository::instance();

        $listsResourceTypeId = $this->listMapRepository->getResourceTypeId();
        $mappedListIds = TypeHelper::string($db->prepare(
            /* @phpstan-ignore-next-line the only reason it's not a literal string is because we use constants to reference table/column names */
            $this->listMapRepository->getMappedLocalIdsForResourceTypeQuery(),
            $listsResourceTypeId
        ), '');

        $productCategoriesResourceTypeId = $this->v1ResourceRepository->getResourceTypeId();
        $resourceMapsTable = CommerceTables::ResourceMap;

        // Example:
        // SELECT godaddy_mwc_commerce_map_ids.local_id
        // FROM godaddy_mwc_commerce_map_ids
        // WHERE godaddy_mwc_commerce_map_ids.resource_type_id = 1
        //     AND godaddy_mwc_commerce_map_ids.local_id NOT IN (SELECT local_id FROM godaddy_mwc_commerce_map_ids WHERE resource_type_id = 11)
        // LIMIT 50
        return "
        SELECT {$resourceMapsTable}.local_id
        FROM {$resourceMapsTable}
        WHERE {$resourceMapsTable}.resource_type_id = {$productCategoriesResourceTypeId}
            AND {$resourceMapsTable}.local_id NOT IN ({$mappedListIds})
        LIMIT %d
        ";
    }

    /**
     * {@inheritDoc}
     */
    protected function addLocalMappingRecord(ResourceMap $referenceMap) : void
    {
        $this->listMapRepository->add($referenceMap->localId, $referenceMap->commerceId);
    }

    /**
     * {@inheritDoc}
     * @param CategoryReferences[] $references
     */
    protected function buildReferenceMap(array $references, ResourceMapCollection $resourceMapCollection) : array
    {
        $resourceMaps = [];

        foreach ($references as $categoryReference) {
            $v1Id = $this->getV1Reference($categoryReference->listReferences);
            if (! $v1Id) {
                continue;
            }

            $localId = $resourceMapCollection->getLocalId($v1Id);
            if (! $localId) {
                continue;
            }

            $resourceMaps[] = new ResourceMap([
                'commerceId'   => $categoryReference->listId,
                'localId'      => $localId,
                'resourceType' => CommerceResourceTypes::List,
            ]);
        }

        return $resourceMaps;
    }

    /** {@inheritDoc} */
    protected function getV1OriginString() : string
    {
        return 'catalog-api-v1-category';
    }

    /** {@inheritDoc} */
    protected function onAllBatchesCompleted() : void
    {
        update_option('mwc_v2_category_mapping_completed_at', date('Y-m-d H:i:s'));
    }
}
