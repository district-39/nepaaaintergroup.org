<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\CategoryReferences;

/**
 * Contract for references services.
 */
interface ListReferencesServiceContract extends ReferencesServiceContract
{
    /**
     * Retrieves references.
     *
     * @param string|null $cursor Cursor for pagination, null for first page
     * @return CategoryReferences[]
     * @throws GatewayRequestException
     */
    public function getPaginatedReferences(?string $cursor = null) : array;

    /**
     * {@inheritDoc}
     * @return CategoryReferences[]
     */
    public function getReferencesByReferenceValues(array $referenceValues) : array;
}
