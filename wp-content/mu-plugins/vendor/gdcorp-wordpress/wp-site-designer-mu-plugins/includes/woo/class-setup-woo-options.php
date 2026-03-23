<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Woo;

/**
 * WooCommerce Options Setup Handler
 *
 * Handles WooCommerce options configuration (location, currency, etc.)
 *
 * @package wp-site-designer-mu-plugins
 */
class Setup_Woo_Options {

	/**
	 * Setup completion option names
	 *
	 * @var array<string>
	 */
	private const SETUP_OPTIONS = array(
		'woocommerce_setup_wizard_completed',
		'woocommerce_onboarding_completed',
	);

	/**
	 * Check if setup options indicate incomplete setup
	 *
	 * @return bool True if setup is complete, false otherwise.
	 */
	public function is_setup_complete(): bool {
		foreach ( self::SETUP_OPTIONS as $option ) {
			if ( false === boolval( get_option( $option, false ) ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Mark setup as completed
	 *
	 * @return void
	 */
	public function mark_setup_completed(): void {
		foreach ( self::SETUP_OPTIONS as $option ) {
			update_option( $option, 'yes' );
		}

		// Set WooCommerce version to prevent setup wizard from showing.
		if ( defined( 'WC_VERSION' ) ) {
			update_option( 'woocommerce_version', WC_VERSION );
		}
	}

	/**
	 * Skip setup wizard redirect
	 *
	 * @return void
	 */
	public function skip_setup_wizard_redirect(): void {
		// Mark setup wizard as completed to prevent redirects.
		update_option( 'woocommerce_setup_wizard_completed', 'yes' );
		update_option( 'woocommerce_onboarding_completed', 'yes' );

		// Remove any pending setup wizard redirects.
		if ( function_exists( 'wc_admin_connect_page' ) ) {
			delete_transient( '_wc_activation_redirect' );
		}
	}

	/**
	 * Configure WooCommerce options (location, currency, weight unit, etc.)
	 *
	 * @return void
	 */
	public function configure_options(): void {
		// Permalink structure - set basic structure if not already set.
		$permalink_structure = get_option( 'permalink_structure', '' );
		if ( empty( $permalink_structure ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
			// Flush rewrite rules after updating permalink structure.
			flush_rewrite_rules( false );
		}

		// Currency settings.
		if ( ! get_option( 'woocommerce_currency' ) ) {
			update_option( 'woocommerce_currency', 'USD' );
		}

		if ( ! get_option( 'woocommerce_currency_pos' ) ) {
			update_option( 'woocommerce_currency_pos', 'left' );
		}

		// Store location settings.
		if ( ! get_option( 'woocommerce_default_country' ) ) {
			update_option( 'woocommerce_default_country', 'US:AZ' );
		}

		// Store address details.
		if ( ! get_option( 'woocommerce_store_address' ) ) {
			update_option( 'woocommerce_store_address', '' );
		}

		if ( ! get_option( 'woocommerce_store_address_2' ) ) {
			update_option( 'woocommerce_store_address_2', '' );
		}

		if ( ! get_option( 'woocommerce_store_city' ) ) {
			update_option( 'woocommerce_store_city', '' );
		}

		if ( ! get_option( 'woocommerce_store_postcode' ) ) {
			update_option( 'woocommerce_store_postcode', '' );
		}

		// Weight unit (lbs, kg, oz, g).
		if ( ! get_option( 'woocommerce_weight_unit' ) ) {
			update_option( 'woocommerce_weight_unit', 'lbs' );
		}

		// Dimension unit (in, cm, m, yd).
		if ( ! get_option( 'woocommerce_dimension_unit' ) ) {
			update_option( 'woocommerce_dimension_unit', 'in' );
		}

		// Store email.
		if ( ! get_option( 'woocommerce_store_email' ) ) {
			$admin_email = get_option( 'admin_email' );
			if ( $admin_email ) {
				update_option( 'woocommerce_store_email', $admin_email );
			}
		}
	}
}
