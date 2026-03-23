<?php
/**
 * Conditional loader for the bundled Abilities API.
 *
 * This file conditionally loads the bundled wordpress/abilities-api package
 * only if it's not already available in WordPress core or from another plugin.
 *
 * Note: WordPress 6.9+ renamed the Abilities API hook from 'abilities_api_init'
 * to 'wp_abilities_api_init'. The bundled API fires both hooks for compatibility.
 *
 * @package GoDaddy\WordPress\Plugins\MCPAdapterInitializer
 * @since 0.2.1
 */

declare( strict_types = 1 );

// Don't load abilities-api during PHPUnit tests - tests mock these functions.
// Check for PHPUnit test environment or if Brain Monkey is loaded.
if (
	defined( 'PHPUNIT_RUNNING' )
) {
	return;
}

$has_wp_register_ability = function_exists( 'wp_register_ability' );

// Only load our bundled abilities-api if wp_register_ability doesn't already exist.
if ( ! $has_wp_register_ability ) {
	// Load the bundled abilities-api classes.
	// These are Mozart-processed with the GD_MCP_ADAPTER_INITIALIZER_ prefix.

	// Check if the Mozart-processed files exist (they won't during initial composer install).
	$gd_mcp_adapter_initializer_ability_class_file = __DIR__ . '/classes/wordpress/abilities-api/includes/abilities-api/class-wp-ability.php';

	if ( file_exists( $gd_mcp_adapter_initializer_ability_class_file ) ) {
		// Load core classes in dependency order.

		// 1. Load WP_Ability class (base class for abilities).
		if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability' ) ) {
			require_once $gd_mcp_adapter_initializer_ability_class_file;
		}

		// 2. Load WP_Ability_Category class (base class for categories).
		$gd_mcp_adapter_initializer_ability_category_class_file = __DIR__ . '/classes/wordpress/abilities-api/includes/abilities-api/class-wp-ability-category.php';
		if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category' ) && file_exists( $gd_mcp_adapter_initializer_ability_category_class_file ) ) {
			require_once $gd_mcp_adapter_initializer_ability_category_class_file;
		}

		// 3. Load WP_Ability_Categories_Registry (depends on WP_Ability_Category).
		$gd_mcp_adapter_initializer_categories_registry_class_file = __DIR__ . '/classes/wordpress/abilities-api/includes/abilities-api/class-wp-ability-categories-registry.php';
		if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry' ) && file_exists( $gd_mcp_adapter_initializer_categories_registry_class_file ) ) {
			require_once $gd_mcp_adapter_initializer_categories_registry_class_file;
		}

		// 4. Load WP_Abilities_Registry (depends on WP_Ability_Categories_Registry).
		$gd_mcp_adapter_initializer_registry_class_file = __DIR__ . '/classes/wordpress/abilities-api/includes/abilities-api/class-wp-abilities-registry.php';
		if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry' ) && file_exists( $gd_mcp_adapter_initializer_registry_class_file ) ) {
			require_once $gd_mcp_adapter_initializer_registry_class_file;
		}

		// Load procedural functions (including wp_register_ability).
		$gd_mcp_adapter_initializer_api_functions_file = __DIR__ . '/classes/wordpress/abilities-api/includes/abilities-api.php';
		if ( file_exists( $gd_mcp_adapter_initializer_api_functions_file ) ) {
			require_once $gd_mcp_adapter_initializer_api_functions_file;
		}

		// Load REST API classes.
		$gd_mcp_adapter_initializer_rest_init_file = __DIR__ . '/classes/wordpress/abilities-api/includes/rest-api/class-wp-rest-abilities-init.php';
		if ( ! class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_REST_Abilities_Init' ) && file_exists( $gd_mcp_adapter_initializer_rest_init_file ) ) {
			require_once $gd_mcp_adapter_initializer_rest_init_file;

			// Initialize REST API routes when WordPress is available.
			if ( function_exists( 'add_action' ) ) {
				add_action( 'rest_api_init', array( 'GD_MCP_ADAPTER_INITIALIZER_WP_REST_Abilities_Init', 'register_routes' ) );
			}
		}

		// Define the version constant for the bundled API.
		if ( ! defined( 'WP_ABILITIES_API_VERSION' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
			define( 'WP_ABILITIES_API_VERSION', '0.4.0' );
		}

		// Create class aliases so that both prefixed and non-prefixed names work.
		// This is necessary because Mozart prefixes the abilities-api classes but doesn't.
		if ( ! class_exists( 'WP_Ability' ) && class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability' ) ) {
			class_alias( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability', 'WP_Ability' );
		}
		if ( ! class_exists( 'WP_Ability_Category' ) && class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category' ) ) {
			class_alias( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category', 'WP_Ability_Category' );
		}
		if ( ! class_exists( 'WP_Ability_Categories_Registry' ) && class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry' ) ) {
			class_alias( 'GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry', 'WP_Ability_Categories_Registry' );
		}
		if ( ! class_exists( 'WP_Abilities_Registry' ) && class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry' ) ) {
			class_alias( 'GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry', 'WP_Abilities_Registry' );
		}
		if ( ! class_exists( 'WP_REST_Abilities_Init' ) && class_exists( 'GD_MCP_ADAPTER_INITIALIZER_WP_REST_Abilities_Init' ) ) {
			class_alias( 'GD_MCP_ADAPTER_INITIALIZER_WP_REST_Abilities_Init', 'WP_REST_Abilities_Init' );
		}
	}
}
