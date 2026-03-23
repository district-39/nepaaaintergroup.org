<?php

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// Use only Composer's classmap instead of the full autoloader to avoid
// registering a global autoloader that could conflict with other plugins
// bundling their own Composer dependencies.
// This helps with performance by avoiding glob function call that was there prior.

$wpaas_classmap = require __DIR__ . '/../../vendor/composer/autoload_classmap.php';

spl_autoload_register(
	function ( $class ) use ( $wpaas_classmap ) {

		if ( strpos( $class, __NAMESPACE__ . '\\' ) === 0 && isset( $wpaas_classmap[ $class ] ) ) {

			require_once $wpaas_classmap[ $class ];

		}

	}
);

/**
 * Returns the plugin instance.
 *
 * @return Plugin
 */
function plugin() {

	return Plugin::load();

}
