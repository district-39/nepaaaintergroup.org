<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Gateways;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Core\Exceptions\Payments\CancelPaymentTransactionException;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Events\CancelTransactionFailedEvent;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\Adapters\CancelTransactionAdapter;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\Adapters\CaptureTransactionAdapter;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\Adapters\PaymentTransactionAdapter;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\Adapters\RefundTransactionAdapter;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http\Adapters\VoidTransactionAdapter;
use GoDaddy\WordPress\MWC\Payments\Gateways\AbstractGateway;
use GoDaddy\WordPress\MWC\Payments\Models\Transactions\PaymentTransaction;
use GoDaddy\WordPress\MWC\Payments\Traits\CanCancelTransactionsTrait;
use GoDaddy\WordPress\MWC\Payments\Traits\CanIssueCapturesTrait;
use GoDaddy\WordPress\MWC\Payments\Traits\CanIssuePaymentsTrait;
use GoDaddy\WordPress\MWC\Payments\Traits\CanIssueRefundsTrait;
use GoDaddy\WordPress\MWC\Payments\Traits\CanIssueVoidsTrait;

/**
 * The transactions gateway.
 *
 * @since 2.10.0
 */
class TransactionsGateway extends AbstractGateway
{
    use CanCancelTransactionsTrait;
    use CanIssueCapturesTrait;
    use CanIssuePaymentsTrait {
        pay as traitPay;
    }
    use CanIssueRefundsTrait;
    use CanIssueVoidsTrait;

    /**
     * The transactions gateway constructor.
     */
    public function __construct()
    {
        $this->cancelTransactionAdapter = CancelTransactionAdapter::class;
        $this->captureTransactionAdapter = CaptureTransactionAdapter::class;
        $this->paymentTransactionAdapter = PaymentTransactionAdapter::class;
        $this->refundTransactionAdapter = RefundTransactionAdapter::class;
        $this->voidTransactionAdapter = VoidTransactionAdapter::class;
    }

    /**
     * Creates payment with request failure handling.
     *
     * @param PaymentTransaction $transaction
     * @return PaymentTransaction
     * @throws Exception
     */
    public function pay(PaymentTransaction $transaction) : PaymentTransaction
    {
        try {
            return $this->traitPay($transaction);
        } catch (Exception $exception) {
            if ($this->isTimeoutException($exception)) {
                $this->cancelFailedPayment($transaction);
            }

            throw $exception;
        }
    }

    /**
     * Determines if an exception was caused by a timeout.
     *
     * Checks for common timeout error patterns from WordPress/cURL:
     * - "cURL error 28" (operation timeout or connection timeout)
     * - "Operation timed out"
     * - "Connection timed out"
     * - "Resolving timed out"
     *
     * @param Exception $exception
     * @return bool
     */
    protected function isTimeoutException(Exception $exception) : bool
    {
        $message = $exception->getMessage();

        // Check for cURL error 28 (timeout error code)
        if (strpos($message, 'cURL error 28') !== false) {
            return true;
        }

        // Check for timeout-related messages
        $timeoutPatterns = [
            'Operation timed out',
            'Connection timed out',
            'Resolving timed out',
        ];

        foreach ($timeoutPatterns as $pattern) {
            if (stripos($message, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cancels a failed payment.
     *
     * @param PaymentTransaction $transaction
     */
    protected function cancelFailedPayment(PaymentTransaction $transaction) : void
    {
        if (! $transaction->getIdempotencyKey()) {
            Events::broadcast(new CancelTransactionFailedEvent($transaction));
            CancelPaymentTransactionException::getNewInstance('Unable to cancel failed payment transaction: missing idempotency key');

            return;
        }

        try {
            $this->cancel($transaction);
        } catch (Exception $cancelException) {
            Events::broadcast(new CancelTransactionFailedEvent($transaction));
            CancelPaymentTransactionException::getNewInstance('Failed to cancel payment transaction: '.$cancelException->getMessage(), $cancelException);
        }
    }
}
