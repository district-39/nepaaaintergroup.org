<?php
/**
 * Navigation Bridge Plugin
 *
 * Bridges navigation events between WordPress (in iframe) and Site Designer parent.
 * Reports URL changes, page info, and navigation state to enable the parent app
 * to stay synchronized with WordPress navigation.
 *
 * Why this exists:
 * - Site Designer needs to know which page/post the user is viewing
 * - WordPress navigation happens in the iframe but parent needs to react
 * - Enables features like page switching, breadcrumbs, and context awareness
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;

/**
 * Bridges navigation events between WordPress iframe and Site Designer parent window
 *
 * Sends postMessage events to parent window when:
 * - Page loads (initial navigation state)
 * - URL changes (pushState, replaceState, popstate)
 * - Hash changes
 * - Site Editor page selection changes
 * - Post Editor page selection changes
 */
class Navigation_Bridge {

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
	 * Enqueue navigation bridge script
	 *
	 * @return void
	 */
	public function enqueue_script(): void {
		$parent_origin   = Iframe_Context_Detector::get_parent_origin();
		$allowed_origins = Iframe_Context_Detector::get_allowed_parent_origins();

		if ( empty( $parent_origin ) ) {
			return;
		}

		$plugin_url = plugins_url( '', dirname( __DIR__ ) );

		wp_enqueue_script(
			'site-designer-navigation-bridge',
			$plugin_url . '/assets/js/navigation-bridge.js',
			array(),
			GDMU_SITE_DESIGNER_VERSION,
			true
		);

		wp_localize_script(
			'site-designer-navigation-bridge',
			'siteDesignerNavigation',
			array(
				'parentOrigin'   => $parent_origin,
				'allowedOrigins' => $allowed_origins,
			)
		);
	}
}
