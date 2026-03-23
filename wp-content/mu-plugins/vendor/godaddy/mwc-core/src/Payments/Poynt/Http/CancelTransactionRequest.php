<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http;

use Exception;

/**
 * Cancel transaction API request.
 *
 * Used to cancel a transaction when a request failure occurs.
 */
class CancelTransactionRequest extends AbstractBusinessRequest
{
    /** @var string timeout cancel reason */
    public const CANCEL_REASON_TIMEOUT = 'TIMEOUT';

    /**
     * CancelTransactionRequest constructor.
     *
     * @param string $originalRequestId The original Poynt-Request-Id from the charge request
     *
     * @throws Exception
     */
    public function __construct(string $originalRequestId)
    {
        $this->setMethod('POST');
        $this->route = 'transactions/cancel';
        $this->query = ['original-request-id' => $originalRequestId];

        parent::__construct();
    }
}
