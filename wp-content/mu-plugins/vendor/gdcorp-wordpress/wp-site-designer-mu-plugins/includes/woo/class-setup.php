<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Woo;

/**
 * WooCommerce Setup Handler
 *
 * Orchestrates automatic WooCommerce configuration and setup wizard skipping
 *
 * ## OVERVIEW
 *
 * This class provides a comprehensive solution for automatically configuring WooCommerce
 * when it's activated, eliminating the need for the setup wizard and ensuring consistent
 * configuration across environments. It manages three distinct phases:
 *
 * 1. **Activation Detection**: Tracks if WooCommerce is active and ready
 * 2. **Configuration Management**: Applies required options and creates pages
 * 3. **State Persistence**: Maintains configuration state across versions
 *
 * ## ACTIVATION TRACKING: check_is_woocommerce_active()
 *
 * ### Purpose
 * Determines if WooCommerce is currently active and available for configuration.
 * This is a prerequisite check before any configuration operations can proceed.
 *
 * ### How It Works
 * - Checks if the `WC()` function exists (WooCommerce's main instance function)
 * - Sets the `$is_woocommerce_active` property to true/false
 * - Runs on `woocommerce_init` hook (priority 0) for early detection
 * - Also runs on WooCommerce activation hook for immediate response
 *
 * ### Why Function Check
 * We use `function_exists('WC')` rather than checking if the plugin file exists because:
 * - It confirms WooCommerce is not only installed but fully loaded
 * - It works regardless of WooCommerce's installation method (regular plugin vs mu-plugin)
 * - It's the most reliable indicator that WooCommerce API is available
 *
 * ## CONFIGURATION TRACKING: check_is_woocommerce_configured()
 *
 * ### Purpose
 * Determines if WooCommerce has been fully configured by our setup process.
 * This prevents duplicate configuration and unnecessary database operations.
 *
 * ### How It Works - Multi-Stage Verification
 *
 * **Stage 1: Version-Based Completion Check**
 * ```
 * if (installation_flag_set && installed_version === current_version && version_not_empty)
 *     → Configuration is COMPLETE
 * ```
 * - Checks `gdmu_site_designer_woo_configured` option (our completion flag)
 * - Compares `gdmu_site_designer_woo_version` with current WC_VERSION
 * - If both match, we know configuration completed successfully for this version
 * - This is the "happy path" - most checks will succeed here
 *
 * **Stage 2: Version Change Detection**
 * ```
 * if (installation_flag_set && installed_version !== current_version && version_not_empty)
 *     → Configuration is INCOMPLETE (needs re-run for new version)
 * ```
 * - Detects when WooCommerce version has changed
 * - Forces re-configuration to apply new version's default options
 * - Ensures compatibility with WooCommerce updates
 * - Critical for catching new required settings in WooCommerce updates
 *
 * **Stage 3: Component-Based Verification**
 * ```
 * $configured = pages_created && setup_complete
 * ```
 * If Stages 1 & 2 don't apply (first run or flag missing):
 * - Checks if all required WooCommerce pages exist (via Setup_Woo_Pages)
 * - Checks if setup wizard completion flags are set (via Setup_Woo_Options)
 * - Both must be true for configuration to be considered complete
 * - This catches partial configurations from interrupted setups
 *
 * ### Why This Multi-Stage Approach?
 *
 * **Reliability**: Multiple verification methods prevent false positives
 * **Performance**: Version check (Stage 1) is fastest and handles 99% of requests
 * **Resilience**: Component checks (Stage 3) catch edge cases and manual changes
 * **Upgradeability**: Version detection (Stage 2) ensures compatibility across updates
 *
 * ## CONFIGURATION PROCESS FLOW
 *
 * ### 1. Initial Activation (WooCommerce just activated)
 * ```
 * activate_woocommerce/woocommerce.php hook fires
 *     ↓
 * check_is_woocommerce_active() → sets $is_woocommerce_active = true
 *     ↓
 * check_is_woocommerce_configured() → returns false (first time)
 *     ↓
 * maybe_configure_woocommerce() → configures options
 *     ↓
 * maybe_create_woocommerce_pages() → creates required pages
 *     ↓
 * maybe_mark_installation_complete() → sets completion flags
 * ```
 *
 * ### 2. Subsequent Page Loads (After Configuration)
 * ```
 * woocommerce_init hook fires (priority 0)
 *     ↓
 * check_is_woocommerce_active() → true
 *     ↓
 * check_is_woocommerce_configured() → true (Stage 1 check passes)
 *     ↓
 * All maybe_* methods → skip (early return)
 * ```
 * Result: Near-zero overhead on subsequent requests
 *
 * ### 3. Version Update Scenario
 * ```
 * WooCommerce updated from 8.0 to 8.5
 *     ↓
 * check_is_woocommerce_configured() → false (Stage 2: version mismatch)
 *     ↓
 * maybe_configure_woocommerce() → re-applies options for v8.5
 *     ↓
 * maybe_create_woocommerce_pages() → creates any new required pages
 *     ↓
 * mark_installation_complete() → updates version to 8.5
 * ```
 *
 * ## STATE PERSISTENCE
 *
 * ### Options Used for State Tracking
 *
 * **gdmu_site_designer_woo_configured** (boolean)
 * - Primary completion flag
 * - Set to true when all configuration is complete
 * - Never set to false (only deleted on full reset)
 *
 * **gdmu_site_designer_woo_version** (string)
 * - Stores WooCommerce version when configuration completed
 * - Used to detect version changes requiring re-configuration
 * - Example: "8.5.1"
 *
 * ### Why Track Version Separately?
 * - WooCommerce introduces new options in updates
 * - Default values may change between versions
 * - New required pages may be added
 * - Ensures our configuration stays current with WooCommerce evolution
 *
 * ## HOOK PRIORITIES
 *
 * All hooks use priority 0 (earliest possible) because:
 * - Configuration must complete before WooCommerce tries to create pages
 * - Options need to be set before WooCommerce reads them
 * - Prevents race conditions with other WooCommerce-dependent code
 * - Blocks default WooCommerce setup wizard from triggering
 *
 * ## EDGE CASES HANDLED
 *
 * **Interrupted Setup**: Component checks catch partially complete setups
 * **Manual Changes**: If admin deletes pages, component check will fail
 * **Version Rollback**: Version mismatch triggers re-configuration
 * **Multiple Activations**: Singleton pattern prevents duplicate processing
 * **Database Issues**: Each component check handles missing options gracefully
 *
 * @package wp-site-designer-mu-plugins
 */
class Setup {

	/**
	 * Singleton instance
	 *
	 * @var Setup|null
	 */
	protected static ?Setup $instance = null;

	/**
	 * Installation flag option name
	 */
	private const INSTALLATION_FLAG_OPTION = 'gdmu_site_designer_woo_configured';

	/**
	 * Installation version option name
	 */
	private const INSTALLATION_VERSION_OPTION = 'gdmu_site_designer_woo_version';

	/**
	 * WooCommerce pages setup handler
	 *
	 * @var Setup_Woo_Pages
	 */
	private Setup_Woo_Pages $woo_pages;

	/**
	 * WooCommerce options setup handler
	 *
	 * @var Setup_Woo_Options
	 */
	private Setup_Woo_Options $woo_options;

	/**
	 * GoDaddy options setup handler
	 *
	 * @var Setup_Gd_Options
	 */
	private Setup_Gd_Options $gd_options;

	/**
	 * Tracks if WooCommerce is active
	 *
	 * @var bool
	 */
	private bool $is_woocommerce_active = false;

	/**
	 * Tracks if WooCommerce is configured
	 *
	 * @var bool
	 */
	private bool $is_woocommerce_configured = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->woo_pages   = new Setup_Woo_Pages();
		$this->woo_options = new Setup_Woo_Options();
		$this->gd_options  = new Setup_Gd_Options();
	}

	/**
	 * Initialize singleton instance
	 *
	 * @return self Singleton instance.
	 */
	public static function init(): self {
		$instance = self::$instance;
		if ( is_null( $instance ) ) {
			$instance       = new self();
			self::$instance = $instance;
		}

		// Always run these checks early.
		add_action( 'woocommerce_init', array( $instance, 'check_is_woocommerce_active' ), 0 );
		add_action( 'woocommerce_init', array( $instance, 'check_is_woocommerce_configured' ), 0 );
		// Configure GoDaddy-specific WooCommerce options.
		add_action( 'woocommerce_init', array( $instance, 'configure_gd_options' ) );
		// Run page creation on init hook to ensure rewrite rules are initialized.
		add_action( 'woocommerce_init', array( $instance, 'maybe_create_woocommerce_pages' ) );
		// Disable cart and account links in header.
		add_action( 'woocommerce_init', array( $instance, 'disable_header_cart_account' ) );
		// Skip setup wizard redirect.
		add_filter( 'woocommerce_prevent_admin_access', array( $instance, 'prevent_admin_access' ), 10, 2 );
		add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );

		return self::$instance;
	}

	/**
	 * Setup WordPress hooks
	 *
	 * @return void
	 */
	public static function setup(): void {
		$instance = self::$instance;
		if ( is_null( $instance ) ) {
			$instance       = new self();
			self::$instance = $instance;
		}

		// Always run these checks early.
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'check_is_woocommerce_active' ), 0 );
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'check_is_woocommerce_configured' ), 0 );
		// Prevent WooCommerce from creating pages on activation.
		add_filter( 'woocommerce_create_pages', array( $instance, 'prevent_woocommerce_page_creation' ), 1 );
		// Run after plugins are loaded to check if WooCommerce needs configuration.
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'maybe_configure_woocommerce' ), 1 );
		// Configure GoDaddy-specific WooCommerce options.
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'configure_gd_options' ), 2 );
		// Run page creation on init hook to ensure rewrite rules are initialized.
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'maybe_create_woocommerce_pages' ), 1 );
		// Disable cart and account links in header.
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'disable_header_cart_account' ), 2 );
		// Skip setup wizard redirect.
		add_action( 'activate_woocommerce/woocommerce.php', array( $instance, 'skip_setup_wizard_redirect' ), 2 );
	}

	/**
	 * Check if WooCommerce is installed and active
	 *
	 * @return void
	 */
	public function check_is_woocommerce_active(): void {
		$this->is_woocommerce_active = function_exists( 'WC' );
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @return bool True if WooCommerce is active, false otherwise.
	 */
	public function is_woocommerce_active(): bool {
		return $this->is_woocommerce_active;
	}

	/**
	 * Check if WooCommerce is configured
	 *
	 * @return void
	 */
	public function check_is_woocommerce_configured(): void {
		$is_installed      = get_option( self::INSTALLATION_FLAG_OPTION, false );
		$installed_version = get_option( self::INSTALLATION_VERSION_OPTION, '' );
		$current_version   = defined( 'WC_VERSION' ) ? WC_VERSION : '';

		// If installation flag is set and version matches, setup is complete.
		if ( $is_installed && $installed_version === $current_version && ! empty( $current_version ) ) {
			$this->is_woocommerce_configured = true;

			return;
		}

		// If version has changed, we need to re-run setup for new options.
		if ( $is_installed && $installed_version !== $current_version && ! empty( $current_version ) ) {
			$this->is_woocommerce_configured = false;

			return;
		}

		$woo_setup_complete = $this->woo_options->is_setup_complete();
		$woo_pages_created  = $this->woo_pages->check_required_pages_exist();

		$this->is_woocommerce_configured = $woo_pages_created && $woo_setup_complete;
	}

	/**
	 * Check if WooCommerce is configured
	 *
	 * @return bool True if WooCommerce is configured, false otherwise.
	 */
	public function is_woocommerce_configured(): bool {
		return $this->is_woocommerce_configured;
	}

	/**
	 * Maybe configure GoDaddy-specific WooCommerce options
	 *
	 * @return void
	 */
	public function configure_gd_options(): void {
		$this->gd_options->configure_options();
	}

	/**
	 * Maybe configure WooCommerce if it's installed but not configured
	 *
	 * @return void
	 */
	public function maybe_configure_woocommerce(): void {
		// Skip if already configured or WooCommerce is not active.
		if ( $this->is_woocommerce_configured() ) {
			return;
		}

		// Configure options immediately (these don't require rewrite rules).
		$this->woo_options->configure_options();
		$this->woo_options->mark_setup_completed();

		// Mark installation as complete after configuration is done.
		// This will also check if pages exist and mark complete if everything is ready.
		$this->maybe_mark_installation_complete();
	}

	/**
	 * Maybe create WooCommerce pages if they don't exist
	 *
	 * Runs on init hook to ensure rewrite rules are initialized before wp_insert_post.
	 *
	 * @return void
	 */
	public function maybe_create_woocommerce_pages(): void {
		// Skip if already configured or WooCommerce is not active.
		if ( $this->is_woocommerce_configured() ) {
			return;
		}

		// Create required pages if they don't exist.
		// This runs on init hook to ensure rewrite rules are initialized.
		$this->woo_pages->create_required_pages();

		// Mark installation as complete after page creation is done.
		$this->maybe_mark_installation_complete();
	}

	/**
	 * Maybe mark installation as complete if all configuration is done
	 *
	 * Checks if both options and pages are configured before marking complete.
	 *
	 * @return void
	 */
	public function maybe_mark_installation_complete(): void {
		// Rerun check here to ensure latest status.
		$this->check_is_woocommerce_configured();

		if ( $this->is_woocommerce_configured() ) {
			// Only mark complete if WooCommerce is configured.
			$this->mark_installation_complete();
		}
	}

	/**
	 * Mark installation as complete and store WooCommerce version
	 *
	 * @return void
	 */
	public function mark_installation_complete(): void {
		update_option( self::INSTALLATION_FLAG_OPTION, true );

		// Store current WooCommerce version.
		if ( defined( 'WC_VERSION' ) ) {
			update_option( self::INSTALLATION_VERSION_OPTION, WC_VERSION );
		}
	}

	/**
	 * Skip setup wizard redirect
	 *
	 * @return void
	 */
	public function skip_setup_wizard_redirect(): void {
		$this->woo_options->skip_setup_wizard_redirect();
	}

	/**
	 * Prevent admin access restrictions during setup
	 *
	 * @param bool        $prevent Whether to prevent admin access.
	 * @param string|null $capability The capability being checked (optional).
	 *
	 * @return bool Whether to prevent admin access.
	 */
	public function prevent_admin_access( bool $prevent, ?string $capability = null ): bool { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Parameter required by WordPress filter callback signature.
		// Allow admin access even if WooCommerce setup is incomplete.
		if ( ! $this->is_woocommerce_configured() ) {
			return false;
		}

		return $prevent;
	}

	/**
	 * Prevent WooCommerce from creating pages on activation
	 *
	 * Returns an empty array to prevent WooCommerce's default page creation behavior.
	 *
	 * @param array<string, array{title: string, content: string, option: string}> $pages Array of pages to create.
	 *
	 * @return array<string, array{title: string, content: string, option: string}> Empty array to prevent page creation.
	 */
	public function prevent_woocommerce_page_creation( array $pages ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Parameter required by WordPress filter callback signature.
		// Return empty array to prevent WooCommerce from creating pages on activation.
		return array();
	}

	/**
	 * Disable cart and account links in header template
	 *
	 * Prevents WooCommerce from adding cart and account links to header during installation.
	 * Works for FSE (Full Site Editing) enabled themes.
	 *
	 * This disables WooCommerce hooked blocks (Mini Cart and Customer Account blocks)
	 * that are automatically inserted into header templates.
	 *
	 * @return void
	 */
	public function disable_header_cart_account(): void {
		// Disable WooCommerce hooked blocks by setting the option to 'no'.
		// This prevents Mini Cart and Customer Account blocks from being auto-inserted into headers.
		update_option( 'woocommerce_hooked_blocks_version', 'no' );
	}
}
