<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\Traits;

use GoDaddy\WordPress\MWC\Common\Models\Orders\Statuses\CheckoutDraftOrderStatus;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Commerce;
use GoDaddy\WordPress\MWC\Core\Features\Commerce\Orders\OrdersIntegration;
use WC_Order;

trait CanCheckWooCommerceOrderTrait
{
    /**
     * Check if the given WooCommerce order is incomplete.
     *
     * An order is considered incomplete if one of the following are true:
     *
     * - The order is in the "checkout-draft" status
     * - The order has no line items
     * - The order doesn't have a totals calculated yet (the subtotal is greater than the total but there is no discount total)
     *
     * @param WC_Order $wooOrder
     * @return bool
     */
    protected function isWooCommerceOrderIncomplete(WC_Order $wooOrder) : bool
    {
        return $wooOrder->get_status() === (new CheckoutDraftOrderStatus)->getName()
            || empty($wooOrder->get_items())
            || $wooOrder->get_subtotal() > $wooOrder->get_total() && $wooOrder->get_total_discount() === 0.0;
    }

    /**
     * Determines whether we can use the given input to create a WooCommerce order in the Commerce platform.
     *
     * @param mixed $wooOrder
     * @phpstan-assert-if-true WC_Order $wooOrder
     */
    protected function canWriteWooCommerceOrderInPlatform($wooOrder) : bool
    {
        return $this->isWooCommerceOrder($wooOrder) &&
            OrdersIntegration::hasCommerceCapability(Commerce::CAPABILITY_WRITE);
    }

    /**
     * Determines whether we can use the given input to create a WooCommerce order in the Commerce platform.
     *
     * @param mixed $wooOrder
     * @phpstan-assert-if-true WC_Order $wooOrder
     */
    protected function canReadWooCommerceOrderFromPlatform($wooOrder) : bool
    {
        return $this->isWooCommerceOrder($wooOrder) &&
            OrdersIntegration::hasCommerceCapability(Commerce::CAPABILITY_READ);
    }

    /**
     * Determines whether the given input is a WooCommerce order.
     *
     * @param mixed $wooOrder
     * @phpstan-assert-if-true WC_Order $wooOrder
     */
    protected function isWooCommerceOrder($wooOrder) : bool
    {
        return $wooOrder instanceof WC_Order;
    }
}
