<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Strategies\Contracts;

use GoDaddy\WordPress\MWC\Common\Models\AbstractAttachment;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Services\Contracts\MappingStrategyContract;

interface MediaMappingStrategyContract extends MappingStrategyContract
{
    /**
     * Saves the given remote UUID as the remote ID for the given model.
     *
     * @param AbstractAttachment $model
     * @param string $remoteId
     * @throws CommerceExceptionContract
     */
    public function saveRemoteId(object $model, string $remoteId) : void;

    /**
     * Gets the remote UUID associated with the given model.
     *
     * @param AbstractAttachment $model
     * @return string|null
     */
    public function getRemoteId(object $model) : ?string;
}
