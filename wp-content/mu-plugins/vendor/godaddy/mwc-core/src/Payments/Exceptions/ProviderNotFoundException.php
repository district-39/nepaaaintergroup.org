<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Exceptions;

use GoDaddy\WordPress\MWC\Common\Exceptions\AbstractNotFoundException;

/**
 * An exception to be thrown when a payment provider is missing.
 */
class ProviderNotFoundException extends AbstractNotFoundException
{
}
