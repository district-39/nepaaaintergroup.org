<?php

namespace GoDaddy\WordPress\MWC\Core\Exceptions\Payments;

use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use Throwable;

/**
 * Exception thrown when unable to cancel a failed GoDaddy Payments transaction.
 *
 * @method static static getNewInstance(string $message, ?Throwable $previous = null)
 */
class CancelPaymentTransactionException extends SentryException
{
    use CanGetNewInstanceTrait;
}
