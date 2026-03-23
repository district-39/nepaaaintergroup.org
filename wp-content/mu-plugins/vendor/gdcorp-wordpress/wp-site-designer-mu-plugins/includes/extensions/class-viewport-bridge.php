<?php
/**
 * Viewport Bridge Extension
 *
 * Bridges viewport/window size data between iframe and parent window.
 * Enables Site Designer to track and respond to viewport dimension changes.
 *
 * Why this exists:
 * - Site Designer needs to know the WordPress iframe viewport dimensions
 * - Enables responsive UI adjustments based on available space
 * - Facilitates proper layout coordination between iframe and parent window
 *
 * @package wp-site-designer-mu-plugins
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;

/**
 * Bridges viewport context between WordPress iframe and Site Designer parent window
 *
 * Sends postMessage events to parent window when:
 * - Page loads (initial viewport content)
 * - User scrolls (visible content changes)
 * - User clicks or focuses elements
 * - Block selection changes in Gutenberg
 */
class Viewport_Bridge {

	/**
	 * Initialize the class and register hooks
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();
		add_action( 'admin_enqueue_scripts', array( $instance, 'enqueue_script' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $instance, 'enqueue_script' ), PHP_INT_MAX );
	}


	/**
	 * Enqueue viewport bridge script
	 *
	 * @return void
	 */
	public function enqueue_script(): void {
		$parent_origin = Iframe_Context_Detector::get_parent_origin();
		$allowed_origins = Iframe_Context_Detector::get_allowed_parent_origins();

		if ( empty( $parent_origin ) ) {
			return;
		}

		$plugin_url = plugins_url( '', dirname( __DIR__ ) );

		wp_enqueue_script(
			'site-designer-viewport-bridge',
			$plugin_url . '/assets/js/viewport-bridge.js',
			array(),
			GDMU_SITE_DESIGNER_VERSION,
			true
		);

		wp_localize_script(
			'site-designer-viewport-bridge',
			'siteDesignerViewport',
			array(
				'parentOrigin' => $parent_origin,
				'allowedOrigins' => $allowed_origins,
			)
		);
	}
}
