<?php
/**
 * Theme Compatibility Handler
 *
 * Handles detection of classic theme activations with pre-activation modal.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Compat;

use WP_Theme;

/**
 * Manages theme compatibility checks and warnings
 */
class Theme_Compatibility {

	/**
	 * Option key for dismissed theme warning
	 */
	public const OPTION_DISMISSED_WARNING = 'gdmu_dismissed_theme_warning';

	/**
	 * Option key for tracking incompatible theme
	 */
	public const OPTION_INCOMPATIBLE_THEME = 'gdmu_incompatible_theme';

	/**
	 * Flag to prevent hook execution during programmatic theme switch
	 *
	 * @var bool
	 */
	private static $switching_programmatically = false;

	/**
	 * Initialize theme compatibility handling
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
		// Enqueue scripts on themes page for pre-activation interception.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add warning to theme tile on Appearance → Themes.
		add_action( 'admin_footer-themes.php', array( $this, 'add_theme_tile_warning' ) );

		// AJAX handlers.
		add_action( 'wp_ajax_gdmu_switch_theme', array( $this, 'ajax_switch_theme' ) );
		add_action( 'wp_ajax_gdmu_keep_theme', array( $this, 'ajax_keep_theme' ) );
		add_action( 'wp_ajax_gdmu_get_theme_info', array( $this, 'ajax_get_theme_info' ) );
	}

	/**
	 * Enqueue scripts on themes pages
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( string $hook_suffix ): void {
		// Load on both installed themes and add themes pages.
		$theme_pages = array( 'themes.php', 'theme-install.php' );

		if ( ! in_array( $hook_suffix, $theme_pages, true ) ) {
			return;
		}

		// Get inactive themes data for JavaScript (modal handles block/classic filtering).
		$inactive_themes = $this->get_inactive_themes_data();

		// Pass data to CompatibilityModal which handles the actual enqueue.
		Compatibility_Modal::set_theme_data(
			$inactive_themes,
			Compatibility_Registry::get_recommended_theme(),
			Compatibility_Registry::get_recommended_theme_name()
		);
	}

	/**
	 * Get inactive themes data for JavaScript
	 *
	 * Returns all themes except the currently active one, with block theme status.
	 * The modal JavaScript handles filtering based on isBlockTheme.
	 *
	 * @return array<string, array{name: string, isBlockTheme: bool}>
	 */
	private function get_inactive_themes_data(): array {
		$all_themes    = wp_get_themes();
		$themes_data   = array();
		$current_theme = wp_get_theme();

		foreach ( $all_themes as $stylesheet => $theme ) {
			// Skip the current active theme (we only intercept activation).
			if ( $stylesheet === $current_theme->get_stylesheet() ) {
				continue;
			}

			$themes_data[ $stylesheet ] = array(
				'name'         => $theme->get( 'Name' ),
				'isBlockTheme' => $theme->is_block_theme(),
			);
		}

		return $themes_data;
	}

	/**
	 * AJAX handler: Get theme info for modal
	 *
	 * @return void
	 */
	public function ajax_get_theme_info(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		$stylesheet = isset( $_POST['theme'] ) ? sanitize_text_field( wp_unslash( $_POST['theme'] ) ) : '';

		if ( empty( $stylesheet ) ) {
			wp_send_json_error( array( 'message' => 'No theme specified' ) );
		}

		$theme = wp_get_theme( $stylesheet );

		if ( ! $theme->exists() ) {
			wp_send_json_error( array( 'message' => 'Theme not found' ) );
		}

		wp_send_json_success(
			array(
				'name'         => $theme->get( 'Name' ),
				'isBlockTheme' => $theme->is_block_theme(),
			)
		);
	}

	/**
	 * AJAX handler: Switch to recommended theme
	 *
	 * @return void
	 */
	public function ajax_switch_theme(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		// Clear tracking BEFORE switching.
		self::clear_incompatible_tracking();

		$recommended = Compatibility_Registry::get_recommended_theme();

		// Set flag to prevent our hook from running during this switch.
		self::$switching_programmatically = true;

		// Switch to recommended theme.
		switch_theme( $recommended );

		self::$switching_programmatically = false;

		wp_send_json_success(
			array(
				'message'  => 'Theme switched successfully',
				'redirect' => admin_url( 'themes.php' ),
			)
		);
	}

	/**
	 * AJAX handler: Keep current theme (dismiss warning)
	 *
	 * @return void
	 */
	public function ajax_keep_theme(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		// Theme activation proceeds normally after modal is dismissed.
		wp_send_json_success(
			array(
				'message' => 'Proceeding with activation',
			)
		);
	}

	/**
	 * Check if current theme is incompatible (classic)
	 *
	 * @return bool
	 */
	public static function has_incompatible_theme(): bool {
		// Always check current theme directly.
		$current_theme = wp_get_theme();
		return ! $current_theme->is_block_theme();
	}

	/**
	 * Get current incompatible theme name
	 *
	 * @return string|null
	 */
	public static function get_incompatible_theme_name(): ?string {
		if ( ! self::has_incompatible_theme() ) {
			return null;
		}

		$current_theme = wp_get_theme();
		return $current_theme->get( 'Name' );
	}

	/**
	 * Dismiss theme warning
	 *
	 * @return void
	 */
	public static function dismissWarning(): void {
		update_option( self::OPTION_DISMISSED_WARNING, true );
	}

	/**
	 * Check if theme warning is dismissed
	 *
	 * @return bool
	 */
	public static function is_warning_dismissed(): bool {
		return (bool) get_option( self::OPTION_DISMISSED_WARNING, false );
	}

	/**
	 * Add warning badge to active theme tile on Appearance → Themes
	 *
	 * @return void
	 */
	public function add_theme_tile_warning(): void {
		// Only show if current theme is classic (not block theme).
		if ( ! self::has_incompatible_theme() ) {
			return;
		}

		$recommended_name = Compatibility_Registry::get_recommended_theme_name();
		?>
		<style>
			.gdmu-theme-warning {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				background: linear-gradient(135deg, #f0b849 0%, #d69e2e 100%);
				color: #1e1e1e;
				padding: 8px 12px;
				font-size: 12px;
				font-weight: 500;
				display: flex;
				align-items: center;
				gap: 6px;
				z-index: 10;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			}
			.gdmu-theme-warning .dashicons {
				font-size: 16px;
				width: 16px;
				height: 16px;
			}
			.theme.active .theme-screenshot {
				margin-top: 0;
			}
			.theme.active {
				position: relative;
			}
		</style>
		<script>
		(function() {
			function addWarningBadge() {
				var activeTheme = document.querySelector('.theme.active');
				if (!activeTheme) {
					return;
				}

				// Don't add if already exists.
				if (activeTheme.querySelector('.gdmu-theme-warning')) {
					return;
				}

				var warning = document.createElement('div');
				warning.className = 'gdmu-theme-warning';
				warning.innerHTML = '<span class="dashicons dashicons-warning"></span>' +
					'<span><?php echo esc_js( __( 'Incompatible Theme - Airo for WordPress features are limited', 'wp-site-designer-mu-plugins' ) ); ?></span>';

				// Insert at the top of the theme card.
				activeTheme.insertBefore(warning, activeTheme.firstChild);
			}

			// Run on DOM ready.
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', addWarningBadge);
			} else {
				addWarningBadge();
			}

			// Also run after a short delay to catch React/Backbone rendered content.
			setTimeout(addWarningBadge, 500);
			setTimeout(addWarningBadge, 1500);
		})();
		</script>
		<?php
	}

	/**
	 * Clear incompatible tracking
	 *
	 * @return void
	 */
	private static function clear_incompatible_tracking(): void {
		delete_option( self::OPTION_INCOMPATIBLE_THEME );
		delete_option( self::OPTION_DISMISSED_WARNING );
	}
}
