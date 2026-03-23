<?php
/**
 * Plugin Compatibility Handler
 *
 * Handles detection of incompatible plugin activations with pre-activation modal.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Compat;

/**
 * Manages plugin compatibility checks and warnings
 */
class Plugin_Compatibility {

	/**
	 * Option key for dismissed plugin warnings
	 */
	public const OPTION_DISMISSED_WARNINGS = 'gdmu_dismissed_plugin_warnings';

	/**
	 * Initialize plugin compatibility handling
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();
		$instance->setup_hooks();
	}

	/**
	 * Setup WordPress hooks
	 *
	 * @return void
	 */
	private function setup_hooks(): void {
		// Enqueue scripts on plugins page for pre-activation interception.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// AJAX handlers.
		add_action( 'wp_ajax_gdmu_keep_plugin_active', array( $this, 'ajax_keep_plugin_active' ) );
		add_action( 'wp_ajax_gdmu_deactivate_plugin', array( $this, 'ajax_deactivate_plugin' ) );
	}

	/**
	 * Enqueue scripts on plugins pages
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( string $hook_suffix ): void {
		// Load on both installed plugins and add plugins pages.
		$plugin_pages = array( 'plugins.php', 'plugin-install.php' );

		if ( ! in_array( $hook_suffix, $plugin_pages, true ) ) {
			return;
		}

		// Get incompatible plugins data for JavaScript (installed plugins).
		$incompatible_plugins = $this->get_incompatible_plugins_data();

		// Get blocklist for Add Plugins page (plugins not yet installed).
		$blocklist = $this->get_blocklist_data();

		// Pass data to CompatibilityModal which handles the actual enqueue.
		Compatibility_Modal::set_plugin_data( $incompatible_plugins, $blocklist );
	}

	/**
	 * Get blocklist data for JavaScript
	 *
	 * Returns all blocked plugins with their slug patterns for matching on Add Plugins page.
	 *
	 * @return array<string, array{name: string, reason: string, slug: string}>
	 */
	private function get_blocklist_data(): array {
		$blocklist = array();

		foreach ( Compatibility_Registry::BLOCKED_PLUGINS as $plugin_file => $info ) {
			// Extract slug from plugin file (e.g., 'elementor/elementor.php' -> 'elementor').
			$slug = dirname( $plugin_file );
			if ( '.' === $slug ) {
				// Single file plugin, use filename without .php.
				$slug = str_replace( '.php', '', $plugin_file );
			}

			$blocklist[ $slug ] = array(
				'name'       => $info['name'],
				'reason'     => $info['reason'],
				'pluginFile' => $plugin_file,
			);
		}

		return $blocklist;
	}

	/**
	 * Get incompatible plugins data for JavaScript
	 *
	 * Returns all blocked and review-status plugins that are installed but not active.
	 *
	 * @return array<string, array{name: string, reason: string, status: string}>
	 */
	private function get_incompatible_plugins_data(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins  = get_plugins();
		$incompatible = array();

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$status = Compatibility_Registry::get_plugin_status( $plugin_file );

			// Only include blocked or review plugins.
			if ( Compatibility_Registry::STATUS_COMPATIBLE === $status ) {
				continue;
			}

			$info = Compatibility_Registry::get_plugin_display_info( $plugin_file );

			$incompatible[ $plugin_file ] = array(
				'name'   => $info['name'],
				'reason' => $info['reason'],
				'status' => $status,
			);
		}

		return $incompatible;
	}

	/**
	 * AJAX handler: Keep plugin active (dismiss warning)
	 *
	 * @return void
	 */
	public function ajax_keep_plugin_active(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';

		if ( empty( $plugin ) ) {
			wp_send_json_error( array( 'message' => 'No plugin specified' ) );
		}

		// Plugin activation proceeds normally after modal is dismissed.
		wp_send_json_success(
			array(
				'message' => 'Proceeding with activation',
			)
		);
	}

	/**
	 * AJAX handler: Deactivate plugin from notice
	 *
	 * @return void
	 */
	public function ajax_deactivate_plugin(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';

		if ( empty( $plugin ) ) {
			wp_send_json_error( array( 'message' => 'No plugin specified' ) );
		}

		// Deactivate the plugin.
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		deactivate_plugins( $plugin );

		wp_send_json_success(
			array(
				'message'  => 'Plugin deactivated successfully',
				'redirect' => admin_url( 'plugins.php' ),
			)
		);
	}

	/**
	 * Dismiss warning for a specific plugin
	 *
	 * @param string $plugin Plugin file path.
	 *
	 * @return void
	 */
	public static function dismiss_warning( string $plugin ): void {
		$dismissed = get_option( self::OPTION_DISMISSED_WARNINGS, array() );

		if ( ! in_array( $plugin, $dismissed, true ) ) {
			$dismissed[] = $plugin;
			update_option( self::OPTION_DISMISSED_WARNINGS, $dismissed );
		}
	}

	/**
	 * Check if warning is dismissed for a plugin
	 *
	 * @param string $plugin Plugin file path.
	 *
	 * @return bool
	 */
	public static function is_warning_dismissed( string $plugin ): bool {
		$dismissed = get_option( self::OPTION_DISMISSED_WARNINGS, array() );
		return in_array( $plugin, $dismissed, true );
	}
}
