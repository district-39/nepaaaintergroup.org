<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceResourceTypes;
use GoDaddy\WordPress\MWC\Core\Repositories\AbstractResourceMapRepository;

class SkuGroupMapRepository extends AbstractResourceMapRepository
{
    /** @var string type of resources managed by this repository */
    protected string $resourceType = CommerceResourceTypes::SkuGroup;

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
