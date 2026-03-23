<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\Adapters;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Core\Payments\Models\Transactions\PaymentTransaction;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\CancelTransactionRequest;

/**
 * Adapter for cancelling transactions.
 *
 * NOTE: Currently supports PaymentTransaction only. Expand to support other transaction types when needed.
 */
class CancelTransactionAdapter implements DataSourceAdapterContract
{
    /** @var PaymentTransaction */
    protected $source;

    /** @var string */
    protected $cancelReason;

    /**
     * Constructor.
     *
     * @param PaymentTransaction $transaction
     * @param string $cancelReason
     */
    public function __construct(PaymentTransaction $transaction, string $cancelReason = CancelTransactionRequest::CANCEL_REASON_TIMEOUT)
    {
        $this->source = $transaction;
        $this->cancelReason = $cancelReason;
    }

    /**
     * Converts transaction to a cancel request.
     *
     * @return CancelTransactionRequest
     * @throws Exception
     */
    public function convertFromSource() : CancelTransactionRequest
    {
        $idempotencyKey = $this->source->getIdempotencyKey();

        if (! $idempotencyKey) {
            throw new Exception('Transaction must have an idempotency key to be cancelled.');
        }

        $request = new CancelTransactionRequest($idempotencyKey);

        $request->setBody([
            'cancelReason' => $this->cancelReason,
        ]);

        return $request;
    }

    /**
     * Converts response back to transaction.
     *
     * @param Response|null $response
     * @return PaymentTransaction
     */
    public function convertToSource(?Response $response = null) : PaymentTransaction
    {
        // Cancel doesn't update the transaction, just return it
        return $this->source;
    }
}
