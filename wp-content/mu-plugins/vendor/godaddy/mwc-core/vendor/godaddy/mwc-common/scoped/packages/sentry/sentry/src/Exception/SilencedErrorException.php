<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Exception;

/**
 * Error that has been captured by the {@see ErrorHandler}, but that was silenced
 * with `@` in the source code.
 */
final class SilencedErrorException extends \ErrorException
{
}
