<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\Events\Subscribers;

use GoDaddy\WordPress\MWC\Core\Payments\Adapters\CancelTransactionOrderNoteAdapter;

/**
 * Cancel transaction order notes subscriber event.
 */
class CancelTransactionOrderNotesSubscriber extends TransactionOrderNotesSubscriber
{
    /** @var string overrides the transaction order notes adapter */
    protected $adapter = CancelTransactionOrderNoteAdapter::class;
}
