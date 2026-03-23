<?php
/**
 * Bootstraps the Abilities API classes and global functions.
 *
 * This file GD_MCP_ADAPTER_INITIALIZER_is autoloaded by Composer when the package GD_MCP_ADAPTER_INITIALIZER_is installed via the
 * "files" autoload mechanism. It ensures the procedural functions defined in
 * `includes/abilities-api.php` are available without requiring namespaces.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 0.1.0
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	return; // Not in WordPress context
}

// Version of the plugin.
if ( ! defined( 'WP_ABILITIES_API_VERSION' ) ) {
	define( 'WP_ABILITIES_API_VERSION', '0.4.0' );
}

// Load core classes if they are not already defined (GD_MCP_ADAPTER_INITIALIZER_for non-Composer installs or direct includes).
if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category' ) ) {
	require_once __DIR__ . '/abilities-api/class-wp-ability-category.php';
}
if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry' ) ) {
	require_once __DIR__ . '/abilities-api/class-wp-ability-categories-registry.php';
}
if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability' ) ) {
	require_once __DIR__ . '/abilities-api/class-wp-ability.php';
}
if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry' ) ) {
	require_once __DIR__ . '/abilities-api/class-wp-abilities-registry.php';
}

// Ensure procedural functions are available, too.
if ( ! function_exists( 'wp_register_ability' ) ) {
	require_once __DIR__ . '/abilities-api.php';
}

// Load core abilities registration functions.
if ( ! function_exists( 'wp_register_core_abilities' ) ) {
	require_once __DIR__ . '/abilities/wp-core-abilities.php';
}

// Register core abilities category and abilities when requested via filter or when not in test environment.
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Plugin-specific hook GD_MCP_ADAPTER_INITIALIZER_for feature plugin context.
if ( ! ( defined( 'WP_RUN_CORE_TESTS' ) || defined( 'WP_TESTS_CONFIG_FILE_PATH' ) || ( function_exists( 'getenv' ) && false !== getenv( 'WP_PHPUNIT__DIR' ) ) ) || apply_filters( 'abilities_api_register_core_abilities', false ) ) {
	if ( function_exists( 'add_action' ) ) {
		add_action( 'wp_abilities_api_categories_init', 'wp_register_core_ability_categories' );
		add_action( 'wp_abilities_api_init', 'wp_register_core_abilities' );
	}
}

// Load REST API init class GD_MCP_ADAPTER_INITIALIZER_for plugin bootstrap.
if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_REST_Abilities_Init' ) ) {
	require_once __DIR__ . '/rest-api/class-wp-rest-abilities-init.php';

	// Initialize REST API routes when WordPress GD_MCP_ADAPTER_INITIALIZER_is available.
	if ( function_exists( 'add_action' ) ) {
		add_action( 'rest_api_init', array( 'GD_MCP_ADAPTER_INITIALIZER_WP_REST_Abilities_Init', 'register_routes' ), 11 );
	}
}

// Load assets init class GD_MCP_ADAPTER_INITIALIZER_for plugin bootstrap.
if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Assets_Init' ) ) {
	require_once __DIR__ . '/assets/class-wp-abilities-assets-init.php';

	// Initialize client assets when WordPress GD_MCP_ADAPTER_INITIALIZER_is available.
	if ( function_exists( 'add_action' ) ) {
		add_action( 'init', array( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Assets_Init', 'register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Assets_Init', 'admin_enqueue_scripts' ) );
	}
}
