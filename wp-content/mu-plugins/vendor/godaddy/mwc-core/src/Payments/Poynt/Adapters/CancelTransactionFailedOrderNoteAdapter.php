<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Adapters;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Payments\Models\Transactions\PaymentTransaction;

/**
 * Adapter for cancel transaction failed order notes.
 */
class CancelTransactionFailedOrderNoteAdapter implements DataSourceAdapterContract
{
    /** @var PaymentTransaction */
    protected PaymentTransaction $source;

    /**
     * Constructor.
     *
     * @param PaymentTransaction $transaction
     */
    public function __construct(PaymentTransaction $transaction)
    {
        $this->source = $transaction;
    }

    /**
     * Converts from source to order notes.
     *
     * @return string[]
     * @throws Exception
     */
    public function convertFromSource() : array
    {
        $idempotencyKey = $this->source->getIdempotencyKey();

        if ($idempotencyKey) {
            /* translators: Placeholder: %s - idempotency key */
            $message = sprintf(
                __('Unable to automatically cancel this payment. Please verify the status of the transaction associated with this order in the GoDaddy Payments dashboard. (Idempotency Key: %s)', 'mwc-core'),
                $idempotencyKey
            );
        } else {
            $message = __('Unable to automatically cancel this payment. Please verify the status of the transaction associated with this order in the GoDaddy Payments dashboard.', 'mwc-core');
        }

        return [$message];
    }

    /**
     * Converts to source.
     *
     * @return PaymentTransaction
     */
    public function convertToSource() : PaymentTransaction
    {
        return $this->source;
    }
}
