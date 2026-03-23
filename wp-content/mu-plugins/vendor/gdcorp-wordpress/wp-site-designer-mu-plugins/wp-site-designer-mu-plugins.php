<?php
/**
 * Plugin Name: WordPress Site Designer MU-Plugins
 * Description: MU-Plugins for WordPress Site Designer integration
 * Version: 1.1.1
 * Author: GoDaddy
 * Requires PHP: 7.4
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner;

use GoDaddy\WordPress\Plugins\SiteDesigner\Api\Site_Designer_Api;
use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\JWT_Auth;
use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Signature_Auth;
use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\WP_Public_Api_Client;
use GoDaddy\WordPress\Plugins\SiteDesigner\Compat\Compatibility_Modal;
use GoDaddy\WordPress\Plugins\SiteDesigner\Compat\Compatibility_Notices;
use GoDaddy\WordPress\Plugins\SiteDesigner\Compat\Plugin_Compatibility;
use GoDaddy\WordPress\Plugins\SiteDesigner\Compat\Theme_Compatibility;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Compatibility_Bridge;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\FullStory_Iframe_Tracker;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Iframe_Support;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Hide_Admin_Bar;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Gutenberg_Support;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Cookie_Status_Bridge;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Navigation_Bridge;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Change_Highlighter;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Media_Upload;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Safari_Storage_Access;
use GoDaddy\WordPress\Plugins\SiteDesigner\Extensions\Viewport_Bridge;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Config;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Request_Validator;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;
use GoDaddy\WordPress\Plugins\SiteDesigner\Woo;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load gdmu_site_designer_should_run() scoping functions.
require_once __DIR__ . '/should_run.php';

// Early exit if plugin should not run in current context.
if ( ! gdmu_site_designer_should_run() ) {
	return;
}

// Load production autoloader (Mozart-prefixed dependencies + plugin code).
// This is a Composer-generated autoloader located in dependencies/.
$gdmu_site_designer_dependencies_autoloader = __DIR__ . '/dependencies/autoload.php';
if ( file_exists( $gdmu_site_designer_dependencies_autoloader ) ) {
	require $gdmu_site_designer_dependencies_autoloader;
}

// Load dev dependencies autoloader (only present in development environment).
// This includes PHPUnit, Mockery, code standards, etc.
$gdmu_site_designer_vendor_autoloader = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $gdmu_site_designer_vendor_autoloader ) ) {
	require_once $gdmu_site_designer_vendor_autoloader;
}

define( 'GDMU_SITE_DESIGNER_VERSION', '1.1.1' );
define( 'GDMU_SITE_DESIGNER_PATH', __DIR__ );
define( 'GDMU_SITE_DESIGNER_URL', plugins_url( '', __FILE__ ) );
define( 'GDMU_SITE_DESIGNER_PRESENT_OPTION', 'gdmu_site_designer' );

$gdmu_site_designer_config = new Config();
$gdmu_site_designer_config->load_from_json( GDMU_SITE_DESIGNER_PATH . '/config/site-designer.json' );

$gdmu_site_designer_request_validator = new Request_Validator( $gdmu_site_designer_config );
$gdmu_site_designer_request_validator->parse();

// Authentication providers.
$gdmu_site_designer_jwt_auth          = new JWT_Auth( $gdmu_site_designer_config );
$gdmu_site_designer_wp_public_api     = new WP_Public_Api_Client( $gdmu_site_designer_config );
$gdmu_site_designer_signature_auth    = new Signature_Auth( $gdmu_site_designer_wp_public_api );

$gdmu_site_designer_api = new Site_Designer_Api(
	$gdmu_site_designer_request_validator,
	$gdmu_site_designer_jwt_auth,
	$gdmu_site_designer_signature_auth
);
$gdmu_site_designer_api->register_endpoints();

add_action(
	'plugins_loaded',
	function () use ( $gdmu_site_designer_request_validator ) {
		// Check if this is a valid iframe request.
		$is_iframe_context = Iframe_Context_Detector::is_valid_request( $gdmu_site_designer_request_validator );
		$is_plugin_active  = Iframe_Context_Detector::is_plugin_activated();
		$is_woo_active     = function_exists( 'WC' );

		// Core functionalities that should run for valid iframe requests.
		if ( $is_iframe_context ) {
			// Enables WordPress to work properly within an iframe (security headers, cookie handling).
			Iframe_Support::init();
			// Hides the WordPress admin bar in iframe context for cleaner UI.
			Hide_Admin_Bar::init();
			// Bridges navigation state changes between iframe and parent window.
			Navigation_Bridge::init();
			// Bridges cookie status and authentication between iframe and parent window.
			Cookie_Status_Bridge::init();
			// Adds Gutenberg customizations for iframe context (content saver, welcome message, status bar).
			Gutenberg_Support::init();
			// Bridges viewport/window size data between iframe and parent window.
			Viewport_Bridge::init();
			// Bridges compatibility status (incompatible plugins/themes) to Site Designer parent window.
			Compatibility_Bridge::init();
			// Enables FullStory session tracker within iframe context.
			FullStory_Iframe_Tracker::init();
			// Handles media upload functionality within iframe context.
			Media_Upload::init();
			// Change highlighter.
			Change_Highlighter::init();
		}

		if ( $is_plugin_active ) {
			Safari_Storage_Access::init();

			// Compatibility system runs in wp-admin context only when Site Designer is activated.
			if ( is_admin() ) {
				Plugin_Compatibility::init();
				Theme_Compatibility::init();
				Compatibility_Notices::init();
				Compatibility_Modal::init();
			}
		}

		if ( $is_woo_active ) {
			// Automatically configures WooCommerce and skips setup wizard.
			Woo\Setup::init();
		}
	},
	0
);

Woo\Setup::setup();
