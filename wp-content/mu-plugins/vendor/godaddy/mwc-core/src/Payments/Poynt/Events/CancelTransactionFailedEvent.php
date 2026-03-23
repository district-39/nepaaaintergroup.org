<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Events;

use GoDaddy\WordPress\MWC\Payments\Events\AbstractTransactionEvent;
use GoDaddy\WordPress\MWC\Payments\Models\Transactions\PaymentTransaction;

/**
 * Event fired when canceling a transaction fails.
 */
class CancelTransactionFailedEvent extends AbstractTransactionEvent
{
    /**
     * Constructor.
     *
     * @param PaymentTransaction $transaction
     */
    public function __construct(PaymentTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
