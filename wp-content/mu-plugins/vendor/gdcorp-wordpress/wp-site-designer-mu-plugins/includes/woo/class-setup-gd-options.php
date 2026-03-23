<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Woo;

/**
 * GoDaddy Options Setup Handler
 *
 * Handles GoDaddy-specific WooCommerce options configuration
 *
 * @package wp-site-designer-mu-plugins
 */
class Setup_Gd_Options {

	/**
	 * Check if GoDaddy MWC Core plugin exists in mu-plugins
	 *
	 * @return bool True if MWC Core plugin is present, false otherwise.
	 */
	private function is_mwc_core_plugin_present(): bool {
		$mu_plugin_dir = defined( 'WPMU_PLUGIN_DIR' ) ? WPMU_PLUGIN_DIR : WP_CONTENT_DIR . '/mu-plugins';
		$mwc_core_path = $mu_plugin_dir . '/godaddy/mwc-core/mwc-core.php';

		return file_exists( $mwc_core_path );
	}

	/**
	 * Configure GoDaddy-specific options
	 *
	 * @return void
	 */
	public function configure_options(): void {
		// Only configure GoDaddy options if MWC Core plugin is present.
		if ( ! $this->is_mwc_core_plugin_present() ) {
			return;
		}

		// Set MWC onboarding first time to skip first screen, but allow banner.
		update_option( 'mwc_onboarding_first_time', 'no' );
		// Mark MWC onboarding as dismissed to skip popup.
		update_option( 'mwc_onboarding_dismissed', 'yes' );
	}
}
