<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\Adapters;

use GoDaddy\WordPress\MWC\Common\Contracts\GraphQLOperationContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\RequestContract;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\ResponseContract;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Adapters\AbstractGatewayRequestAdapter;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Catalog\Providers\GoDaddy\Http\GraphQL\Queries\SkuReferencesOperation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\SkuReferencesInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Mapping\DataObjects\SkuReferencesOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\V2\Providers\GoDaddy\Http\GraphQL\Requests\Request;

/**
 * Request adapter for {@see SkuReferencesOperation}.
 *
 * @method static static getNewInstance(SkuReferencesInput $input)
 */
class SkuReferencesRequestAdapter extends AbstractGatewayRequestAdapter
{
    use CanGetNewInstanceTrait;

    protected SkuReferencesInput $input;

    /**
     * SkuReferencesRequestAdapter constructor.
     *
     * @param SkuReferencesInput $input
     */
    public function __construct(SkuReferencesInput $input)
    {
        $this->input = $input;
    }

    /**
     * Converts from source input to GraphQL request.
     *
     * @return RequestContract
     */
    public function convertFromSource() : RequestContract
    {
        return Request::withAuth($this->getGraphQLOperation())
            ->setStoreId($this->input->storeId)
            ->setMethod('post');
    }

    /**
     * Gets the GraphQL operation for this request.
     *
     * @return GraphQLOperationContract
     */
    protected function getGraphQLOperation() : GraphQLOperationContract
    {
        return (new SkuReferencesOperation())->setVariables($this->getQueryVariables());
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
     *
     * @param ResponseContract $response
     * @return SkuReferencesOutput
     */
    protected function convertResponse(ResponseContract $response) : SkuReferencesOutput
    {
        $responseBody = $response->getBody();
        $skusData = ArrayHelper::get($responseBody, 'data.skus', []);

        // Extract SKU nodes from the GraphQL edges structure
        $skuNodes = [];
        $edges = TypeHelper::array(ArrayHelper::get($skusData, 'edges'), []);

        foreach ($edges as $edge) {
            $node = TypeHelper::array(ArrayHelper::get($edge, 'node'), []);
            if (! empty($node)) {
                $skuNodes[] = $node;
            }
        }

        // Convert each SKU node to ProductReferences
        $productReferences = array_map(function ($skuNode) {
            $adapter = ProductReferencesAdapter::getNewInstance($skuNode);

            return $adapter->convertFromSource();
        }, $skuNodes);

        // Extract pagination info
        $pageInfo = ArrayHelper::get($skusData, 'pageInfo', []);

        return new SkuReferencesOutput([
            'productReferences' => $productReferences,
            'hasNextPage'       => TypeHelper::bool(ArrayHelper::get($pageInfo, 'hasNextPage'), false),
            'endCursor'         => TypeHelper::string(ArrayHelper::get($pageInfo, 'endCursor'), ''),
        ]);
    }
}
