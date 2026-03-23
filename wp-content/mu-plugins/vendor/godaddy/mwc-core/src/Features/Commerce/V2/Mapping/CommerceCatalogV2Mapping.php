<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping;

use GoDaddy\WordPress\MWC\Common\Components\Exceptions\ComponentClassesNotDefinedException;
use GoDaddy\WordPress\MWC\Common\Components\Exceptions\ComponentLoadFailedException;
use GoDaddy\WordPress\MWC\Common\Components\Traits\HasComponentsFromContainerTrait;
use GoDaddy\WordPress\MWC\Common\Features\AbstractFeature;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Interceptors\InitiateMappingInterceptor;

/**
 * Feature that initiates a backfill of catalog v2 UUIDs within our local mapping table.
 *
 * We originally integrated with v1 of the catalog API and have those mappings saved. But v2 introduces new resource
 * types, which have different UUIDs. This class represents a one-time operation to determine what the v2 UUIDs are
 * for the corresponding v1 resources, and persist those mappings to our local database.
 */
class CommerceCatalogV2Mapping extends AbstractFeature
{
    use HasComponentsFromContainerTrait;

    /** @var class-string[] alphabetically ordered list of components to load */
    protected array $componentClasses = [
        InitiateMappingInterceptor::class,
    ];

    /**
     * {@inheritDoc}
     */
    public static function getName() : string
    {
        return 'commerce_catalog_v2_mapping';
    }

    /**
     * {@inheritDoc}
     *
     * @throws ComponentClassesNotDefinedException|ComponentLoadFailedException
     */
    public function load() : void
    {
        $this->loadComponents();
    }

    /**
     * Checks if the mapping jobs from v1 to v2 have been completed.
     */
    public static function hasCompletedMappingJobs() : bool
    {
        return ! empty(get_option('mwc_v2_category_mapping_completed_at')) &&
            ! empty(get_option('mwc_v2_product_mapping_completed_at'));
    }
}
