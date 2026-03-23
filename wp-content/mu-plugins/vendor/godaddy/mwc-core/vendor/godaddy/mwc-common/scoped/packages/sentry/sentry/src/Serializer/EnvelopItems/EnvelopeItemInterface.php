<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Serializer\EnvelopItems;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Event;
/**
 * @internal
 */
interface EnvelopeItemInterface
{
    public static function toEnvelopeItem(Event $event): string;
}
