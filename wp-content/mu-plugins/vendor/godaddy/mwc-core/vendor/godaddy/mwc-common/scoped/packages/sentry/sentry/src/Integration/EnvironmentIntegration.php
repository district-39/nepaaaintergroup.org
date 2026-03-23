<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Integration;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Context\OsContext;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Context\RuntimeContext;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Event;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\SentrySdk;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\State\Scope;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Util\PHPVersion;
/**
 * This integration fills the event data with runtime and server OS information.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class EnvironmentIntegration implements IntegrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(static function (Event $event): Event {
            $integration = SentrySdk::getCurrentHub()->getIntegration(self::class);
            if ($integration !== null) {
                $event->setRuntimeContext($integration->updateRuntimeContext($event->getRuntimeContext()));
                $event->setOsContext($integration->updateServerOsContext($event->getOsContext()));
            }
            return $event;
        });
    }
    private function updateRuntimeContext(?RuntimeContext $runtimeContext): RuntimeContext
    {
        if ($runtimeContext === null) {
            $runtimeContext = new RuntimeContext('php');
        }
        if ($runtimeContext->getVersion() === null) {
            $runtimeContext->setVersion(PHPVersion::parseVersion());
        }
        return $runtimeContext;
    }
    private function updateServerOsContext(?OsContext $osContext): ?OsContext
    {
        if (!\function_exists('php_uname') && !\function_exists('GoDaddy\WordPress\MWC\Common\Vendor\php_uname')) {
            return $osContext;
        }
        if ($osContext === null) {
            $osContext = new OsContext(php_uname('s'));
        }
        if ($osContext->getVersion() === null) {
            $osContext->setVersion(php_uname('r'));
        }
        if ($osContext->getBuild() === null) {
            $osContext->setBuild(php_uname('v'));
        }
        if ($osContext->getKernelVersion() === null) {
            $osContext->setKernelVersion(php_uname('a'));
        }
        if ($osContext->getMachineType() === null) {
            $osContext->setMachineType(php_uname('m'));
        }
        return $osContext;
    }
}
