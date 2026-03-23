<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Catalog\Providers\GoDaddy\Http\GraphQL\Queries\SkuReferencesOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ProductReferences;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\SkuReferencesInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\SkuReferencesOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\SkuReferencesServiceContract;

/**
 * Service for retrieving Sku references from the v2 Commerce API.
 */
class SkuReferencesService extends AbstractReferencesService implements SkuReferencesServiceContract
{
    /**
     * Retrieves SKU references for the given reference values.
     * {@see SkuReferencesOperation} for the GraphQL operation used.
     *
     * @param array<int, string> $referenceValues
     * @return ProductReferences[]
     * @throws CommerceExceptionContract|GatewayRequestException
     */
    public function getReferencesByReferenceValues(array $referenceValues) : array
    {
        if (empty($referenceValues)) {
            return [];
        }

        /** @var SkuReferencesOutput $referencesOutput */
        $referencesOutput = $this->getReferences(
            $this->getSkuReferencesInput($referenceValues),
            SkuReferencesOutput::class
        );

        return $referencesOutput->productReferences;
    }

    /**
     * Creates input for SKU references GraphQL operation.
     *
     * @param array<int, string> $referenceValues
     * @return SkuReferencesInput
     */
    protected function getSkuReferencesInput(array $referenceValues) : SkuReferencesInput
    {
        // Create input for the GraphQL operation
        return new SkuReferencesInput([
            'storeId'         => $this->getStoreId(),
            'referenceValues' => $referenceValues,
            // @todo add support for pagination if needed.
            'cursor'  => null,
            'perPage' => count($referenceValues),
        ]);
    }
}
