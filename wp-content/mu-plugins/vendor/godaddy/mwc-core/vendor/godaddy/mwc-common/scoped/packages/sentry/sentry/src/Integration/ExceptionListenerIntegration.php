<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Integration;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\ErrorHandler;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\SentrySdk;
/**
 * This integration hooks into the global error handlers and emits events to
 * Sentry.
 */
final class ExceptionListenerIntegration extends AbstractErrorListenerIntegration
{
    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        $errorHandler = ErrorHandler::registerOnceExceptionHandler();
        $errorHandler->addExceptionHandlerListener(static function (\Throwable $exception): void {
            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if ($integration === null) {
                return;
            }
            $integration->captureException($currentHub, $exception);
        });
    }
}
