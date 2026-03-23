<?php

namespace GoDaddy\WordPress\MWC\Payments\Traits;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Payments\Events\CancelTransactionEvent;
use GoDaddy\WordPress\MWC\Payments\Models\Transactions\AbstractTransaction;

/**
 * Trait for gateways that can cancel transactions.
 */
trait CanCancelTransactionsTrait
{
    use AdaptsRequestsTrait;

    /** @var string Cancel Transaction Adapter class */
    protected $cancelTransactionAdapter;

    /**
     * Cancels a transaction.
     *
     * @param AbstractTransaction $transaction
     *
     * @return AbstractTransaction
     * @throws Exception
     */
    public function cancel(AbstractTransaction $transaction) : AbstractTransaction
    {
        /** @var AbstractTransaction $cancelledTransaction */
        $cancelledTransaction = $this->doAdaptedRequest($transaction, new $this->cancelTransactionAdapter($transaction));

        Events::broadcast(new CancelTransactionEvent($cancelledTransaction));

        return $cancelledTransaction;
    }
}
