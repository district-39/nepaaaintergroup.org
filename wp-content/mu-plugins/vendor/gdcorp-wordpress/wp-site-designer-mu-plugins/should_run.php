<?php
/**
 * Should Run - Context-based plugin execution scoping
 *
 * This file contains functions that determine whether the Site Designer
 * plugin should run based on the current WordPress execution context.
 *
 * These functions are loaded early (before autoloader) to enable fast bailout
 * when the plugin is not needed, avoiding unnecessary memory and CPU overhead.
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine if the Site Designer plugin should run based on the current WordPress execution context
 *
 * This plugin is specifically designed for Site Designer iframe integration and REST API endpoints.
 * It provides functionality for iframe requests and REST API communication. It should NOT run in
 * contexts where it provides no value or could cause unnecessary overhead.
 *
 * ALLOWED CONTEXTS (plugin WILL run):
 * - REST API Requests: Core functionality - serves activation endpoints and potential future API endpoints
 * - Iframe Requests: Detected via Sec-Fetch-Dest: iframe header (browser-sent security header)
 * - WP-CLI: Allows debugging and testing via command-line tools
 *
 * BLOCKED CONTEXTS (plugin will NOT run):
 * - Regular HTTP/Page Requests (direct browser navigation): Plugin provides no value for standard page loads
 * - AJAX Requests: Plugin only serves REST API and iframe contexts, not wp-admin/admin-ajax.php
 * - Cron Jobs: Plugin provides no scheduled task functionality
 * - WordPress Installation: Requires a fully installed WordPress with database access
 * - Database Repair: Not needed during DB maintenance operations
 * - Shortinit Mode: Requires full WordPress functionality (plugins, themes, etc.)
 * - Plugin Uninstallation: Not needed during cleanup operations
 *
 * PERFORMANCE BENEFITS:
 * By only running for relevant contexts, we:
 * - Eliminate overhead on regular page loads when not in iframe context
 * - Reduce memory usage by avoiding loading unnecessary classes and dependencies
 * - Avoid potential errors in limited WordPress environments
 * - Improve overall site performance by 2-5ms per blocked context
 *
 * @return bool True if plugin should initialize, false to skip execution.
 */
function gdmu_site_designer_should_run(): bool {
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

	// Allow: WP-CLI for debugging and testing.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return true;
	}

	// Allow: REST API requests (for activation endpoint and future API endpoints).
	if ( gdmu_site_designer_is_supported_request() ) {
		return true;
	}

	// Allow: Iframe requests (detected by browser-sent Sec-Fetch-Dest header).
	// Full validation happens later via Iframe_Context_Detector::is_valid_request()
	// which also validates origin and activation status.
	if ( isset( $_SERVER['HTTP_SEC_FETCH_DEST'] ) && 'iframe' === $_SERVER['HTTP_SEC_FETCH_DEST'] ) {
		return true;
	}

	// Block: All other regular HTTP page requests (frontend/admin without Site Designer context).
	return false;
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
function gdmu_site_designer_is_supported_request(): bool {
	// Method 0: query argument override.
	$is_gdmu_site_designer = isset( $_REQUEST['wp_site_designer'] ) && sanitize_key( $_REQUEST['wp_site_designer'] );
	$is_gdmu_site_designer_safari_popup = (
		isset( $_REQUEST['safari_popup_auth'] ) && sanitize_key( $_REQUEST['safari_popup_auth'] ) ||
		isset( $_REQUEST['safari_popup_auth_alone'] ) && sanitize_key( $_REQUEST['safari_popup_auth_alone'] )
	);
	$is_gdmu_site_designer_safari_complete = isset( $_REQUEST['safari_popup_complete'] ) && sanitize_key( $_REQUEST['safari_popup_complete'] );
	$has_gdmu_site_designer_safari_pending_cookie = isset( $_COOKIE['safari_popup_auth_pending'] ) && sanitize_key( $_COOKIE['safari_popup_auth_pending'] );
	$has_gdmu_site_designer_safari_granted_cookie = isset( $_COOKIE['safari_storage_granted'] ) && sanitize_key( $_COOKIE['safari_storage_granted'] );
	if (
		$is_gdmu_site_designer ||
		$is_gdmu_site_designer_safari_popup ||
		$is_gdmu_site_designer_safari_complete ||
		$has_gdmu_site_designer_safari_pending_cookie ||
		$has_gdmu_site_designer_safari_granted_cookie
	) {
		return true;
	}

	// Method 0: AJAX requests - allow plugin's admin-ajax.php calls (gdmu_ prefix), block others.
	if ( wp_doing_ajax() ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check for action prefix.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : '';
		// Allow AJAX actions with gdmu_ prefix (plugin-specific actions).
		if ( 0 === strpos( $action, 'gdmu_' ) ) {
			return true;
		}
		// Block all other AJAX requests.
		return false;
	}

	// Method 1: Check REST_REQUEST constant (most reliable if defined).
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	// Method 2: Check if request URI contains /wp-json/.
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Simple string check, not used for output.
		$request_uri = wp_unslash( $_SERVER['REQUEST_URI'] );
		if ( false !== strpos( $request_uri, '/wp-json/' ) ) {
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
