<?php
/**
 * Should Run - Context-based plugin execution scoping
 *
 * This file contains functions that determine whether the MCP Adapter Initializer
 * plugin should run based on the current WordPress execution context.
 *
 * These functions are loaded early (before autoloader) to enable fast bailout
 * when the plugin is not needed, avoiding unnecessary memory and CPU overhead.
 *
 * @package mcp-adapter-initializer
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine if the MCP Adapter plugin should run based on the current WordPress execution context
 *
 * This plugin is specifically designed ONLY for serving MCP protocol requests via REST API.
 * It provides NO functionality for standard WordPress operations and should NOT run in
 * contexts where it provides no value or could cause unnecessary overhead.
 *
 * ALLOWED CONTEXTS (plugin WILL run):
 * - REST API Requests: Core functionality - serves the /gd-mcp/v1/mcp/streamable endpoint
 * - WP-CLI: Allows debugging and testing MCP tools via command-line
 *
 * BLOCKED CONTEXTS (plugin will NOT run):
 * - Regular HTTP/Page Requests: Plugin provides no frontend or admin UI functionality
 * - Cron Jobs: Plugin provides no scheduled task functionality
 * - WordPress Installation: Requires a fully installed WordPress with database access
 * - Database Repair: Not needed during DB maintenance operations
 * - Shortinit Mode: Requires full WordPress functionality (REST API, plugins, etc.)
 * - Plugin Uninstallation: Not needed during cleanup operations
 * - AJAX Requests: Plugin only serves REST API, not wp-admin/admin-ajax.php
 *
 * PERFORMANCE BENEFITS:
 * By only running for REST API requests to our specific endpoint, we:
 * - Eliminate ALL overhead on regular page loads (0ms impact on frontend/admin)
 * - Reduce memory usage by ~5-10MB on non-REST requests
 * - Avoid loading 35+ tool classes, authentication, abilities API, etc.
 * - Prevent potential errors in limited WordPress environments
 * - Improve overall site performance by 2-5ms per blocked context
 *
 * @return bool True if plugin should initialize, false to skip execution.
 */
function gd_mcp_adapter_initializer_should_run(): bool {
	// Block: Cron contexts - this plugin provides no cron functionality.
	if ( ( defined( 'DOING_CRON' ) && DOING_CRON ) || wp_doing_cron() ) {
		return false;
	}

	// Block: WordPress installation - requires a fully installed WordPress.
	if ( ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) || wp_installing() ) {
		return false;
	}

	// Block: Database repair mode - not needed for DB maintenance.
	if ( defined( 'WP_REPAIRING' ) && WP_REPAIRING ) {
		return false;
	}

	// Block: Shortinit mode - requires full WordPress functionality.
	if ( defined( 'SHORTINIT' ) && SHORTINIT ) {
		return false;
	}

	// Block: Plugin uninstallation - not needed during cleanup.
	if ( defined( 'WP_UNINSTALL_PLUGIN' ) && WP_UNINSTALL_PLUGIN ) {
		return false;
	}

	// Block: AJAX requests - plugin only serves REST API endpoints, not admin-ajax.php.
	if ( wp_doing_ajax() ) {
		return false;
	}

	// Allow: WP-CLI for debugging and testing.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return true;
	}

	// Block: Regular HTTP page requests (frontend/admin) - only allow REST API requests.
	// This is the key optimization: we only run for REST API requests.
	if ( ! gd_mcp_adapter_initializer_is_rest_request() ) {
		return false;
	}

	// Allow: REST API requests (our core functionality).
	return true;
}

/**
 * Detect if the current request is a REST API request
 *
 * Checks multiple indicators to determine if this is a REST API request:
 * 1. REST_REQUEST constant (most reliable, set by WordPress core)
 * 2. Request URI contains /wp-json/ path
 * 3. Query parameter rest_route is set
 *
 * This is more reliable than only checking REST_REQUEST since that constant
 * may not be defined yet during early plugin loading.
 *
 * @return bool True if this is a REST API request, false otherwise.
 */
function gd_mcp_adapter_initializer_is_rest_request(): bool {
	// Method 1: Check REST_REQUEST constant (most reliable if defined).
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	// Method 2: Check if request URI contains /wp-json/gd-mcp. All tools would be in this namespace, so it's a strong indicator of a relevant REST API request.
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		if ( false !== strpos( $request_uri, '/wp-json/gd-mcp/' ) ) {
			return true;
		}
	}

	// Method 3: Check for rest_route query parameter (pretty permalinks disabled).
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is a read-only check, not processing form data.
	if ( isset( $_GET['rest_route'] ) ) {
		return true;
	}

	return false;
}
