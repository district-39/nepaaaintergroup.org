<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Adapters;

use Exception;

/**
 * Cancel transaction order note adapter.
 */
class CancelTransactionOrderNoteAdapter extends TransactionOrderNoteAdapter
{
    /**
     * Gets the status message.
     *
     * @return string
     * @throws Exception
     */
    protected function getStatusMessage() : string
    {
        return sprintf(
            /* translators: Placeholders: %s - the total amount of the payment that was cancelled */
            __('A payment of %s was successfully cancelled.', 'mwc-core'),
            $this->getTotalAmount()
        );
    }
}
