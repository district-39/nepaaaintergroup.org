<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ProductReferences;

/**
 * Contract for SKU references services.
 */
interface SkuReferencesServiceContract extends ReferencesServiceContract
{
    /**
     * {@inheritDoc}
     * @return ProductReferences[]
     */
    public function getReferencesByReferenceValues(array $referenceValues) : array;
}
