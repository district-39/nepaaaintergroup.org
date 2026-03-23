<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\GoDaddy\Adapters;

use GoDaddy\WordPress\MWC\Common\Contracts\GraphQLOperationContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\RequestContract;
use GoDaddy\WordPress\MWC\Common\Http\Contracts\ResponseContract;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\CommerceException;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Exceptions\Contracts\CommerceExceptionContract;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\DataObjects\Enums\Status;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\DataObjects\OrderOutput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\DataObjects\UpdateOrderInput;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\GoDaddy\Builders\OrderBuilder;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\GoDaddy\Http\GraphQL\Mutations\CancelOrderMutation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\GoDaddy\Http\GraphQL\Mutations\FulfillAndCompleteOrderMutation;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\Providers\GoDaddy\Http\Requests\Request;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Providers\Adapters\AbstractGatewayRequestAdapter;

/**
 * @method static static getNewInstance(UpdateOrderInput $input)
 */
class UpdateOrderStatusRequestAdapter extends AbstractGatewayRequestAdapter
{
    use CanGetNewInstanceTrait;

    protected UpdateOrderInput $input;

    /**
     * Constructor.
     *
     * @param UpdateOrderInput $input
     */
    public function __construct(UpdateOrderInput $input)
    {
        $this->input = $input;
    }

    /**
     * {@inheritDoc}
     */
    public function convertFromSource() : RequestContract
    {
        return Request::withAuth($this->getStatusUpdateOperation())
            ->setStoreId($this->input->storeId)
            ->setMethod('post');
    }

    /**
     * Gets a GraphQL operation to update order status.
     *
     * @return GraphQLOperationContract
     * @throws CommerceExceptionContract
     */
    protected function getStatusUpdateOperation() : GraphQLOperationContract
    {
        $orderStatus = $this->input->order->statuses->status;

        // When an order is completed in WooCommerce, we mark orders as fulfilled in the Orders Service regardless
        // of the local fulfillment status. See https://godaddy-corp.atlassian.net/browse/MWC-17645
        if (Status::Completed === $orderStatus) {
            return $this->getFulfillAndCompleteOrderOperation();
        }

        if (Status::Canceled === $orderStatus) {
            return $this->getCanceledOrderOperation();
        }

        throw new CommerceException("There is no operation defined to change the order status to {$orderStatus}.");
    }

    /**
     * Gets a new instance of a GraphQL operation to fulfill and complete the order.
     *
     * Using this operation is a temporary fix to fulfill orders when they are marked as complete. The long term plan is to update
     * the fulfillment status of the order and each line item individually as those statuses change in WooCommerce.
     *
     * See https://godaddy-corp.atlassian.net/browse/MWC-12699
     *
     * @return GraphQLOperationContract
     */
    protected function getFulfillAndCompleteOrderOperation() : GraphQLOperationContract
    {
        return (new FulfillAndCompleteOrderMutation())->setVariables([
            'completeOrderId' => $this->input->order->id,
        ]);
    }

    /**
     * Gets a new instance of cancel order GraphQL mutation.
     *
     * @return GraphQLOperationContract
     */
    protected function getCanceledOrderOperation() : GraphQLOperationContract
    {
        return (new CancelOrderMutation())->setVariables([
            'cancelOrderId' => $this->input->order->id,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function convertResponse(ResponseContract $response)
    {
        return new OrderOutput([
            'order' => OrderBuilder::getNewInstance()
                ->setData(TypeHelper::array(ArrayHelper::get($response->getBody(), 'data.updateOrderStatus'), []))
                ->build(),
        ]);
    }
}
