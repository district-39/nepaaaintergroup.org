<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories;

use GoDaddy\WordPress\MWC\Common\Repositories\WordPress\DatabaseRepository;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\CatalogIntegration;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceResourceTypes;
use GoDaddy\WordPress\MWC\Core\Repositories\AbstractResourceMapRepository;

class ListMapRepository extends AbstractResourceMapRepository
{
    /** @var string type of resources managed by this repository */
    protected string $resourceType = CommerceResourceTypes::List;

    /**
     * Gets the local IDs of product categories that do not yet exist in the mapping table.
     *
     * @param int $limit
     * @return int[]
     */
    public function getUnmappedLocalIds(int $limit = 50) : array
    {
        $resourceTypeId = $this->getResourceTypeId();

        $results = DatabaseRepository::getResults(
            $this->getUnmappedLocalIdsSqlString(),
            [
                CatalogIntegration::PRODUCT_CATEGORY_TAXONOMY,
                $resourceTypeId,
                $limit,
            ]
        );

        /** @var string[] $termIds */
        $termIds = array_column($results, 'term_id');

        return array_map('intval', $termIds);
    }

    /**
     * Gets the SQL string for the unmapped local IDs query.
     *
     * See example below for a resulting string.
     *
     * @return string
     */
    protected function getUnmappedLocalIdsSqlString() : string
    {
        $db = DatabaseRepository::instance();

        // Example:
        //
        // SELECT wp_terms.term_id
        // FROM wp_terms
        // INNER JOIN wp_term_taxonomy ON(wp_terms.term_id = wp_term_taxonomy.term_id)
        // WHERE wp_term_taxonomy.taxonomy = 'product_cat'
        //      AND wp_terms.term_id NOT IN(SELECT local_id FROM godaddy_mwc_commerce_map_ids WHERE resource_type_id = 11)
        // ORDER BY wp_term_taxonomy.parent ASC
        // LIMIT 0, 50
        return "
            SELECT {$db->terms}.term_id
            FROM {$db->terms}
            INNER JOIN {$db->term_taxonomy} ON({$db->terms}.term_id = {$db->term_taxonomy}.term_id)
            WHERE {$db->term_taxonomy}.taxonomy = %s
                AND {$db->terms}.term_id NOT IN ({$this->getMappedLocalIdsForResourceTypeQuery()})
            ORDER BY {$db->term_taxonomy}.parent ASC
            LIMIT %d
        ";
    }

    /**
     * This is just overridden to make the method public.
     *
     * {@inheritDoc}
     */
    public function getMappedLocalIdsForResourceTypeQuery() : string
    {
        return parent::getMappedLocalIdsForResourceTypeQuery();
    }
}
