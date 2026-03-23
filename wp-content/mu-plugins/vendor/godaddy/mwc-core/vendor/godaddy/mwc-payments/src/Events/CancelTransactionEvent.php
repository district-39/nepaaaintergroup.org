<?php

namespace GoDaddy\WordPress\MWC\Payments\Events;

use GoDaddy\WordPress\MWC\Payments\Models\Transactions\AbstractTransaction;

/**
 * Cancel transaction event.
 */
class CancelTransactionEvent extends AbstractTransactionEvent
{
    /**
     * Sets the transaction the event is for.
     *
     * @param AbstractTransaction $transaction
     */
    public function __construct(AbstractTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
