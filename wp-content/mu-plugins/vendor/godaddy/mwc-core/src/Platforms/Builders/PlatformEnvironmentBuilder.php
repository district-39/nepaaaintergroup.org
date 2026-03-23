<?php

namespace GoDaddy\WordPress\MWC\Core\Platforms\Builders;

use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Platforms\Contracts\PlatformEnvironmentBuilderContract;
use GoDaddy\WordPress\MWC\Common\Platforms\Contracts\PlatformEnvironmentContract;
use GoDaddy\WordPress\MWC\Common\Platforms\PlatformEnvironment;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;
use WPaaS\Plugin;

/**
 * Managed WordPress platform environment builder class.
 */
class PlatformEnvironmentBuilder implements PlatformEnvironmentBuilderContract
{
    use CanGetNewInstanceTrait;

    /**
     * {@inheritDoc}
     */
    public function build() : PlatformEnvironmentContract
    {
        return (new PlatformEnvironment())->setEnvironment($this->getEnvironmentName());
    }

    /**
     * Gets the environment name.
     *
     * Logic adapted from https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/ca776bcbd6480c14726193d84adb92775f2015d3/gd-system-plugin/includes/trait-helpers.php#L413-L435
     *
     * @return string
     */
    public function getEnvironmentName() : string
    {
        if ($env = getenv('SERVER_ENV')) {
            return $this->parseManagedWordPressEnvironment($env);
        }

        if (defined('GD_TEMP_DOMAIN') && StringHelper::endsWith(GD_TEMP_DOMAIN, '.ide')) {
            return $this->parseManagedWordPressEnvironment('test');
        }

        return PlatformEnvironment::PRODUCTION;
    }

    /**
     * Possible known values for {@param $environment} were extracted from https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/ca776bcbd6480c14726193d84adb92775f2015d3/gd-system-plugin/includes/class-rum.php#L87-L91.
     *
     * @return PlatformEnvironment::*
     */
    protected function parseManagedWordPressEnvironment(string $environment) : string
    {
        switch ($environment) {
            case 'prod':
                return PlatformEnvironment::PRODUCTION;
            case 'test':
                return PlatformEnvironment::TEST;
            case 'dev':
            default:
                return PlatformEnvironment::LOCAL;
        }
    }
}
