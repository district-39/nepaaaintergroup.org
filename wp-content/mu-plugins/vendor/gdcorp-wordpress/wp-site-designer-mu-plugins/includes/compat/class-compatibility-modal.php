<?php
/**
 * Compatibility Modal
 *
 * Renders modal overlays for compatibility warnings on plugin/theme activation.
 * Uses pre-activation interception - modal appears BEFORE activation happens.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Compat;

/**
 * Manages modal rendering for compatibility warnings
 */
class Compatibility_Modal {

	/**
	 * Plugin data for JavaScript (installed incompatible plugins)
	 *
	 * @var array|null
	 */
	private static $plugin_data = null;

	/**
	 * Plugin blocklist for JavaScript (for Add Plugins page)
	 *
	 * @var array|null
	 */
	private static $plugin_blocklist = null;

	/**
	 * Theme data for JavaScript
	 *
	 * @var array|null
	 */
	private static $theme_data = null;

	/**
	 * Recommended theme slug
	 *
	 * @var string|null
	 */
	private static $recommended_theme = null;

	/**
	 * Recommended theme name
	 *
	 * @var string|null
	 */
	private static $recommended_theme_name = null;

	/**
	 * Plugin URL base
	 *
	 * @var string
	 */
	private static $plugin_url;

	/**
	 * Plugin directory path
	 *
	 * @var string
	 */
	private static $plugin_dir;

	/**
	 * Initialize modal handling
	 *
	 * @return void
	 */
	public static function init(): void {
		self::setup_paths();

		$instance = new self();
		$instance->setup_hooks();
	}

	/**
	 * Setup plugin paths
	 *
	 * @return void
	 */
	private static function setup_paths(): void {
		self::$plugin_dir = GDMU_SITE_DESIGNER_PATH;
		self::$plugin_url = GDMU_SITE_DESIGNER_URL;
	}

	/**
	 * Setup WordPress hooks
	 *
	 * @return void
	 */
	private function setup_hooks(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 20 );
		add_action( 'admin_footer', array( $this, 'render_modal' ) );

		// AJAX handlers for dismissing modal warnings.
		add_action( 'wp_ajax_gdmu_dismiss_modal_warnings', array( $this, 'ajax_dismiss_modal_warnings' ) );
	}

	/**
	 * AJAX handler: Dismiss modal warnings permanently
	 *
	 * @return void
	 */
	public function ajax_dismiss_modal_warnings(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

		if ( 'plugin' === $type && current_user_can( 'activate_plugins' ) ) {
			update_user_meta( get_current_user_id(), 'gdmu_dismiss_plugin_modal', '1' );
			wp_send_json_success();
		} elseif ( 'theme' === $type && current_user_can( 'switch_themes' ) ) {
			update_user_meta( get_current_user_id(), 'gdmu_dismiss_theme_modal', '1' );
			wp_send_json_success();
		}

		wp_send_json_error( array( 'message' => 'Invalid type or permission denied' ) );
	}

	/**
	 * Set plugin data for JavaScript interception
	 *
	 * @param array $plugins   Incompatible installed plugins data.
	 * @param array $blocklist Blocklist data for Add Plugins page.
	 *
	 * @return void
	 */
	public static function set_plugin_data( array $plugins, array $blocklist = array() ): void {
		self::$plugin_data      = $plugins;
		self::$plugin_blocklist = $blocklist;
	}

	/**
	 * Set theme data for JavaScript interception
	 *
	 * @param array  $themes                All themes with block theme status.
	 * @param string $recommended_theme     Recommended theme slug.
	 * @param string $recommended_theme_name Recommended theme display name.
	 *
	 * @return void
	 */
	public static function set_theme_data( array $themes, string $recommended_theme, string $recommended_theme_name ): void {
		self::$theme_data             = $themes;
		self::$recommended_theme      = $recommended_theme;
		self::$recommended_theme_name = $recommended_theme_name;
	}

	/**
	 * Check if we should enqueue assets
	 *
	 * @return bool
	 */
	private function should_enqueue(): bool {
		return null !== self::$plugin_data || null !== self::$plugin_blocklist || null !== self::$theme_data;
	}

	/**
	 * Enqueue modal assets
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! $this->should_enqueue() ) {
			return;
		}

		// Enqueue CSS.
		$css_path = self::$plugin_dir . '/assets/css/compatibility-modal.css';
		if ( file_exists( $css_path ) ) {
			wp_enqueue_style(
				'gdmu-compatibility-modal',
				self::$plugin_url . '/assets/css/compatibility-modal.css',
				array(),
				filemtime( $css_path )
			);
		}

		// Enqueue JS.
		$js_path = self::$plugin_dir . '/assets/js/compatibility-modal.js';
		if ( file_exists( $js_path ) ) {
			wp_enqueue_script(
				'gdmu-compatibility-modal',
				self::$plugin_url . '/assets/js/compatibility-modal.js',
				array( 'jquery' ),
				filemtime( $js_path ),
				true
			);

			// Build localization data.
			$localize_data = array(
				'ajaxUrl'                 => admin_url( 'admin-ajax.php' ),
				'nonce'                   => wp_create_nonce( 'gdmu_compatibility' ),
				'i18n'                    => array(
					/* translators: %s: plugin or theme name */
					'pluginWarningTitle'     => __( 'Activating %s will limit Airo features', 'wp-site-designer-mu-plugins' ),
					/* translators: %s: theme name */
					'themeWarningTitle'      => __( 'Activating %s will limit Airo features', 'wp-site-designer-mu-plugins' ),
					'pluginMessage'          => __( 'Certain plugins limit what Airo for WordPress can do for your site. To keep all of Airo\'s design capabilities, cancel this activation.', 'wp-site-designer-mu-plugins' ),
					'themeMessage'           => __( 'Certain themes limit what Airo for WordPress can do for your site. To keep all of Airo\'s design capabilities, cancel this activation.', 'wp-site-designer-mu-plugins' ),
					'reviewNote'             => __( 'This plugin is under review. It may or may not cause issues with Airo.', 'wp-site-designer-mu-plugins' ),
					'continueAnyway'         => __( 'Continue Anyway', 'wp-site-designer-mu-plugins' ),
					'cancelActivation'       => __( 'Cancel Activation', 'wp-site-designer-mu-plugins' ),
					'dontShowPluginWarnings' => __( 'Don\'t show plugin warnings again', 'wp-site-designer-mu-plugins' ),
					'dontShowThemeWarnings'  => __( 'Don\'t show theme warnings again', 'wp-site-designer-mu-plugins' ),
				),
				'dismissedPluginWarnings' => get_user_meta( get_current_user_id(), 'gdmu_dismiss_plugin_modal', true ) === '1',
				'dismissedThemeWarnings'  => get_user_meta( get_current_user_id(), 'gdmu_dismiss_theme_modal', true ) === '1',
			);

			// Add plugin data if set.
			if ( null !== self::$plugin_data ) {
				$localize_data['incompatiblePlugins'] = self::$plugin_data;
			}

			// Add blocklist for Add Plugins page.
			if ( null !== self::$plugin_blocklist && ! empty( self::$plugin_blocklist ) ) {
				$localize_data['pluginBlocklist'] = self::$plugin_blocklist;
			}

			// Add theme data if set.
			if ( null !== self::$theme_data ) {
				$localize_data['themes']               = self::$theme_data;
				$localize_data['recommendedTheme']     = self::$recommended_theme;
				$localize_data['recommendedThemeName'] = self::$recommended_theme_name;
			}

			wp_localize_script(
				'gdmu-compatibility-modal',
				'gdmuCompatibility',
				$localize_data
			);
		}
	}

	/**
	 * Render modal template in admin footer
	 *
	 * The modal is hidden by default and populated by JavaScript.
	 *
	 * @return void
	 */
	public function render_modal(): void {
		if ( ! $this->should_enqueue() ) {
			return;
		}

		?>
		<div id="gdmu-compatibility-modal" class="gdmu-modal" data-type="" data-plugin="" data-theme="">
			<div class="gdmu-modal-backdrop"></div>
			<div class="gdmu-modal-container">
				<div class="gdmu-modal-header">
					<span class="gdmu-modal-icon gdmu-modal-icon-warning">
						<svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9.74709 10.7705C9.94599 10.7705 10.1367 10.6914 10.2774 10.5508C10.418 10.4101 10.497 10.2194 10.4971 10.0205V6.02052C10.4971 5.82161 10.4181 5.63084 10.2774 5.49019C10.1368 5.34954 9.946 5.27052 9.74709 5.27052C9.54818 5.27052 9.35741 5.34954 9.21676 5.49019C9.07611 5.63084 8.99709 5.82161 8.99709 6.02052V10.0205C8.99714 10.2194 9.07618 10.4101 9.21682 10.5508C9.35746 10.6914 9.54819 10.7705 9.74709 10.7705Z" fill="#1E1E1E"/>
							<path d="M19.1182 13.501L12.2246 1.43799C11.975 1.00116 11.6144 0.638073 11.1793 0.38554C10.7441 0.133007 10.25 0 9.74685 0C9.24374 0 8.74957 0.133007 8.31443 0.38554C7.87929 0.638073 7.51865 1.00116 7.26906 1.43799L0.376001 13.501C0.127989 13.935 -0.00166566 14.4266 1.61557e-05 14.9264C0.00169797 15.4263 0.134657 15.917 0.385584 16.3493C0.636511 16.7816 0.996601 17.1405 1.42981 17.3899C1.86302 17.6393 2.35414 17.7706 2.85402 17.7705H16.6406C17.1404 17.7703 17.6314 17.6389 18.0645 17.3894C18.4975 17.14 18.8575 16.7811 19.1083 16.3489C19.3591 15.9166 19.4921 15.426 19.4938 14.9262C19.4955 14.4265 19.366 13.935 19.1182 13.501ZM17.8115 15.5957C17.6937 15.8019 17.5232 15.973 17.3174 16.0916C17.1117 16.2102 16.8781 16.2719 16.6406 16.2705H2.85402C2.61687 16.2706 2.38386 16.2084 2.17834 16.09C1.97281 15.9717 1.80198 15.8015 1.68295 15.5964C1.56393 15.3913 1.50089 15.1585 1.50014 14.9213C1.4994 14.6842 1.56099 14.451 1.67873 14.2451L8.5718 2.18213C8.69013 1.97492 8.86114 1.80269 9.0675 1.68289C9.27386 1.5631 9.50823 1.5 9.74685 1.5C9.98546 1.5 10.2198 1.5631 10.4262 1.68289C10.6326 1.80269 10.8036 1.97492 10.9219 2.18213L17.8155 14.2451C17.9345 14.4504 17.9969 14.6836 17.9962 14.921C17.9956 15.1583 17.9318 15.3911 17.8115 15.5957Z" fill="#1E1E1E"/>
							<path d="M9.74709 12.2705H9.74221C9.44341 12.2712 9.15708 12.3904 8.94614 12.602C8.73519 12.8137 8.61689 13.1004 8.61721 13.3992C8.61753 13.698 8.73646 13.9844 8.94786 14.1956C9.15926 14.4068 9.44585 14.5254 9.74465 14.5254C10.0435 14.5254 10.33 14.4068 10.5414 14.1956C10.7528 13.9844 10.8718 13.698 10.8721 13.3992C10.8724 13.1004 10.7541 12.8137 10.5432 12.602C10.3322 12.3904 10.0459 12.2712 9.74709 12.2705Z" fill="#1E1E1E"/>
						</svg>
					</span>
					<h2 class="gdmu-modal-title"></h2>
				</div>
				<div class="gdmu-modal-body">
					<p class="gdmu-modal-message"></p>
					<p class="gdmu-modal-note" style="display: none;"></p>
					<label class="gdmu-modal-dismiss-option">
						<input type="checkbox" class="gdmu-modal-dismiss-checkbox" />
						<span class="gdmu-modal-dismiss-label"></span>
					</label>
				</div>
				<div class="gdmu-modal-footer">
					<button type="button" class="gdmu-modal-link" data-action="proceed">
						<span class="gdmu-btn-text"></span>
					</button>
					<button type="button" class="gdmu-modal-cancel-btn" data-action="cancel">
						<span class="gdmu-btn-text"></span>
					</button>
				</div>
			</div>
		</div>
		<?php
	}
}
