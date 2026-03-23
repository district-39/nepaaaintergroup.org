<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters;

use GoDaddy\WordPress\MWC\Common\Contracts\GraphQLOperationContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\RequestContract;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\ResponseContract;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Adapters\AbstractGatewayRequestAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\GoDaddy\Http\GraphQL\Queries\ListsReferencesOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ListReferencesInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\ListReferencesOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Providers\GoDaddy\Http\GraphQL\Requests\Request;

/**
 * Request adapter for {@see ListsReferencesOperation}.
 */
class ListReferencesRequestAdapter extends AbstractGatewayRequestAdapter
{
    use CanGetNewInstanceTrait;

    protected ListReferencesInput $input;

    /**
     * CategoriesReferencesRequestAdapter constructor.
     */
    public function __construct(ListReferencesInput $input)
    {
        $this->input = $input;
    }

    /**
     * Converts from source input to GraphQL request.
     */
    public function convertFromSource() : RequestContract
    {
        return Request::withAuth($this->getGraphQLOperation())
            ->setStoreId($this->input->storeId)
            ->setMethod('post');
    }

    /**
     * Gets the GraphQL operation for this request.
     */
    protected function getGraphQLOperation() : GraphQLOperationContract
    {
        return (new ListsReferencesOperation())->setVariables($this->getQueryVariables());
    }

    /**
     * Gets query variables for the GraphQL operation.
     *
     * @return array<string, mixed>
     */
    protected function getQueryVariables() : array
    {
        return [
            'first'           => $this->input->perPage,
            'after'           => $this->input->cursor,
            'referenceValues' => $this->input->referenceValues,
        ];
    }

    /**
     * Converts GraphQL response to output object.
     */
    protected function convertResponse(ResponseContract $response) : ListReferencesOutput
    {
        $responseBody = $response->getBody();
        $listsData = ArrayHelper::get($responseBody, 'data.lists', []);

        // Extract list nodes from the GraphQL edges structure
        $listNodes = [];
        $edges = TypeHelper::array(ArrayHelper::get($listsData, 'edges'), []);

        foreach ($edges as $edge) {
            $node = TypeHelper::array(ArrayHelper::get($edge, 'node'), []);
            if (! empty($node)) {
                $listNodes[] = $node;
            }
        }

        // Convert each list node to CategoryReferences
        $categoryReferences = array_map(function ($listNode) {
            /** @var array<string, mixed> $listNode */
            $adapter = new CategoryReferencesAdapter($listNode);

            return $adapter->convertFromSource();
        }, $listNodes);

        // Extract pagination info
        $pageInfo = ArrayHelper::get($listsData, 'pageInfo', []);

        return new ListReferencesOutput([
            'categoryReferences' => $categoryReferences,
            'hasNextPage'        => TypeHelper::bool(ArrayHelper::get($pageInfo, 'hasNextPage'), false),
            'endCursor'          => TypeHelper::string(ArrayHelper::get($pageInfo, 'endCursor'), ''),
        ]);
    }
}
