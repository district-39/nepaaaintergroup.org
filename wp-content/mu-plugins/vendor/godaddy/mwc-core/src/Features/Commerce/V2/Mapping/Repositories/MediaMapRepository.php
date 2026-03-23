<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Repositories;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Enums\CommerceResourceTypes;
use GoDaddy\WordPress\MWC\Core\Repositories\AbstractResourceMapRepository;

class MediaMapRepository extends AbstractResourceMapRepository
{
    /** @var string type of resources managed by this repository */
    protected string $resourceType = CommerceResourceTypes::Media;
}
