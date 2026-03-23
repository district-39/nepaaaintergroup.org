<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts;

use GoDaddy\WordPress\MWC\Common\DataObjects\AbstractDataObject;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;

interface ReferencesServiceContract
{
    /**
     * Retrieves references for the given reference values.
     *
     * @param string[] $referenceValues List of reference values to filter by
     * @return AbstractDataObject[]
     * @throws GatewayRequestException
     */
    public function getReferencesByReferenceValues(array $referenceValues) : array;
}
