<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Transport;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Event;
interface TransportInterface
{
    public function send(Event $event): Result;
    public function close(?int $timeout = null): Result;
}
