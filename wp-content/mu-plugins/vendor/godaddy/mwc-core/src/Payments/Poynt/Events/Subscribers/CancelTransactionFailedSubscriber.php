<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Events\Subscribers;

use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Adapters\CancelTransactionFailedOrderNoteAdapter;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\Events\Subscribers\TransactionOrderNotesSubscriber;

/**
 * Subscriber for cancel transaction failed events.
 */
class CancelTransactionFailedSubscriber extends TransactionOrderNotesSubscriber
{
    /** @var string the order note adapter class name */
    protected $adapter = CancelTransactionFailedOrderNoteAdapter::class;
}
