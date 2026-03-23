<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Providers\Contracts;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesInputContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\Contracts\ReferencesOutputContract;

interface ReferencesGatewayContract
{
    /**
     * Retrieves references for the given input.
     *
     * @param ReferencesInputContract $input
     * @return ReferencesOutputContract
     * @throws CommerceExceptionContract
     */
    public function getReferences(ReferencesInputContract $input) : ReferencesOutputContract;
}
