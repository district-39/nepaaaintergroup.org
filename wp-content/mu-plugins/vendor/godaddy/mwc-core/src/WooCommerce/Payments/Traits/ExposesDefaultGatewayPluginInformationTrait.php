<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\Traits;

trait ExposesDefaultGatewayPluginInformationTrait
{
    /**
     * Corresponding gateway plugin slug.
     *
     * @var string
     */
    public string $plugin_slug = 'mwc-core';

    /**
     * Corresponding gateway plugin file.
     *
     * Skip the .php extension to match the format used by the WP API.
     *
     * @var string
     */
    public string $plugin_file = 'mwc-core/mwc-core';
}
