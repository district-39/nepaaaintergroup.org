<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services;

use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\GatewayRequestException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\GoDaddy\Http\GraphQL\Queries\ListsReferencesOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\CategoryReferences;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ListReferencesInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ListReferencesOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Services\Contracts\ListReferencesServiceContract;

/**
 * Service for retrieving references from the v2 Commerce API.
 */
class ListReferencesService extends AbstractReferencesService implements ListReferencesServiceContract
{
    /** @var int Maximum number of items to fetch per request */
    public const DEFAULT_PAGE_SIZE = 50;

    /**
     * Retrieves references with pagination support.
     * {@see ListsReferencesOperation} for the GraphQL operation used.
     *
     * @param string|null $cursor Cursor for pagination, null for first page
     * @return CategoryReferences[]
     * @throws CommerceExceptionContract|GatewayRequestException
     */
    public function getPaginatedReferences(?string $cursor = null) : array
    {
        /** @var ListReferencesOutput $referencesOutput */
        $referencesOutput = $this->getReferences(
            $this->getListReferencesInput([], $cursor),
            ListReferencesOutput::class
        );

        return $referencesOutput->categoryReferences;
    }

    /**
     * Retrieves references for the given reference values.
     *
     * @param string[] $referenceValues $referenceValues
     * @return CategoryReferences[]
     * @throws CommerceExceptionContract|GatewayRequestException
     */
    public function getReferencesByReferenceValues(array $referenceValues) : array
    {
        /** @var ListReferencesOutput $referencesOutput */
        $referencesOutput = $this->getReferences(
            $this->getListReferencesInput($referenceValues),
            ListReferencesOutput::class
        );

        return $referencesOutput->categoryReferences;
    }

    /**
     * Creates input for the List references GraphQL operation.
     *
     * @param string[] $referenceValues
     * @param ?string $cursor
     * @return ListReferencesInput
     */
    public function getListReferencesInput(array $referenceValues, ?string $cursor = null) : ListReferencesInput
    {
        return new ListReferencesInput([
            'storeId'         => $this->getStoreId(),
            'referenceValues' => $referenceValues,
            'cursor'          => $cursor,
            'perPage'         => static::DEFAULT_PAGE_SIZE,
        ]);
    }
}
