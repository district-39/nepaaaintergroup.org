<?php
/**
 * Compatibility Bridge Plugin
 *
 * Sends compatibility status (incompatible plugins/themes) to Site Designer parent window.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Compat;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;

use function add_action;

/**
 * Bridges compatibility status between WordPress and Site Designer iframe
 */
class Compatibility_Bridge {

	/**
	 * Initialize the compatibility bridge.
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();

		add_action( 'admin_enqueue_scripts', array( $instance, 'enqueue_script' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $instance, 'enqueue_script' ), PHP_INT_MAX );
	}

	/**
	 * Enqueue compatibility bridge script
	 *
	 * @return void
	 */
	public function enqueue_script(): void {
		$parent_origin = Iframe_Context_Detector::get_parent_origin();

		if ( empty( $parent_origin ) ) {
			return;
		}

		$plugin_url = plugins_url( '', dirname( __DIR__ ) );

		wp_enqueue_script(
			'site-designer-compatibility-bridge',
			$plugin_url . '/assets/js/compatibility-bridge.js',
			array(),
			'1.0.0',
			true
		);

		wp_localize_script(
			'site-designer-compatibility-bridge',
			'siteDesignerCompatibility',
			$this->get_compatibility_data( $parent_origin )
		);
	}

	/**
	 * Get compatibility data for localization
	 *
	 * @param string $parent_origin Parent origin for postMessage.
	 *
	 * @return array
	 */
	private function get_compatibility_data( string $parent_origin ): array {
		$current_theme   = wp_get_theme();
		$is_block_theme  = $current_theme->is_block_theme();
		$blocked_plugins = $this->get_active_blocked_plugins();
		$review_plugins  = $this->get_active_review_plugins();
		$allowed_origins = Iframe_Context_Detector::get_allowed_parent_origins();

		// Site is compatible if using block theme and no blocked plugins.
		$is_compatible = $is_block_theme && empty( $blocked_plugins );

		return array(
			'parentOrigin' => $parent_origin,
			'allowedOrigins' => $allowed_origins,
			'isCompatible' => $is_compatible,
			'theme'        => array(
				'isBlockTheme'         => $is_block_theme,
				'name'                 => $current_theme->get( 'Name' ),
				'slug'                 => $current_theme->get_stylesheet(),
				'recommendedTheme'     => Compat\Compatibility_Registry::get_recommended_theme(),
				'recommendedThemeName' => Compat\Compatibility_Registry::get_recommended_theme_name(),
			),
			'plugins'      => array(
				'blocked' => $blocked_plugins,
				'review'  => $review_plugins,
			),
		);
	}

	/**
	 * Get active blocked plugins with details
	 *
	 * @return array
	 */
	private function get_active_blocked_plugins(): array {
		$active_blocked = Compat\Compatibility_Registry::get_active_blocked_plugins();
		$plugins        = array();

		foreach ( $active_blocked as $plugin_file => $info ) {
			$plugins[] = array(
				'file'   => $plugin_file,
				'name'   => $info['name'],
				'reason' => $info['reason'],
			);
		}

		return $plugins;
	}

	/**
	 * Get active review-bucket plugins with details
	 *
	 * @return array
	 */
	private function get_active_review_plugins(): array {
		$active_review = Compat\Compatibility_Registry::get_active_review_plugins();
		$plugins       = array();

		foreach ( $active_review as $plugin_file => $info ) {
			$plugins[] = array(
				'file' => $plugin_file,
				'name' => $info['name'],
			);
		}

		return $plugins;
	}
}
