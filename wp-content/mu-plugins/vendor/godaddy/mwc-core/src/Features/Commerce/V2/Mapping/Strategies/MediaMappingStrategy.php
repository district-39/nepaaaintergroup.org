<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Strategies;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Inventory\Services\AbstractMappingStrategy;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Traits\CanMapNumericIdentifierToRemoteIdsTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Strategies\Contracts\MediaMappingStrategyContract;

/**
 * Mapping strategy for media. This associates a numeric local ID with a string remote UUID.
 */
class MediaMappingStrategy extends AbstractMappingStrategy implements MediaMappingStrategyContract
{
    use CanMapNumericIdentifierToRemoteIdsTrait;
}
