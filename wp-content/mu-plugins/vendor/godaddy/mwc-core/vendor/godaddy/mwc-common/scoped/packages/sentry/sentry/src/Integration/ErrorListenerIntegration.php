<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Integration;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\ErrorHandler;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Exception\SilencedErrorException;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\SentrySdk;
/**
 * This integration hooks into the global error handlers and emits events to
 * Sentry.
 */
final class ErrorListenerIntegration extends AbstractErrorListenerIntegration
{
    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        $errorHandler = ErrorHandler::registerOnceErrorHandler();
        $errorHandler->addErrorHandlerListener(static function (\ErrorException $exception): void {
            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();
            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if ($integration === null || $client === null) {
                return;
            }
            if ($exception instanceof SilencedErrorException && !$client->getOptions()->shouldCaptureSilencedErrors()) {
                return;
            }
            if (!$exception instanceof SilencedErrorException && !($client->getOptions()->getErrorTypes() & $exception->getSeverity())) {
                return;
            }
            $integration->captureException($currentHub, $exception);
        });
    }
}
