<?php
/**
 * Compatibility Notices
 *
 * Renders persistent admin notices for incompatible plugins and themes.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Compat;

/**
 * Manages persistent admin notices for compatibility warnings
 */
class Compatibility_Notices {

	/**
	 * Initialize compatibility notices
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();
		$instance->setupHooks();
	}

	/**
	 * Setup WordPress hooks
	 *
	 * @return void
	 */
	private function setupHooks(): void {
		add_action( 'admin_notices', array( $this, 'display_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'display_theme_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_notice_scripts' ) );

		// Block editor notices (post.php, site-editor.php).
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_notices' ) );

		// AJAX handler for dismissing notices.
		add_action( 'wp_ajax_gdmu_dismiss_plugin_notice', array( $this, 'ajax_dismiss_plugin_notice' ) );
		add_action( 'wp_ajax_gdmu_dismiss_theme_notice', array( $this, 'ajax_dismiss_theme_notice' ) );
	}

	/**
	 * Display plugin compatibility notices
	 *
	 * @return void
	 */
	public function display_plugin_notices(): void {
		// Get active blocked plugins.
		$blocked_plugins = Compatibility_Registry::get_active_blocked_plugins();
		$review_plugins  = Compatibility_Registry::get_active_review_plugins();

		// Filter out dismissed warnings.
		$blocked_plugins = array_filter(
			$blocked_plugins,
			function ( $info, $plugin ) {
				return ! Plugin_Compatibility::is_warning_dismissed( $plugin );
			},
			ARRAY_FILTER_USE_BOTH
		);

		$review_plugins = array_filter(
			$review_plugins,
			function ( $info, $plugin ) {
				return ! Plugin_Compatibility::is_warning_dismissed( $plugin );
			},
			ARRAY_FILTER_USE_BOTH
		);

		if ( empty( $blocked_plugins ) && empty( $review_plugins ) ) {
			return;
		}

		$all_plugins = array_merge( $blocked_plugins, $review_plugins );
		$this->render_plugin_notice( $all_plugins );
	}

	/**
	 * Display theme compatibility notice
	 *
	 * @return void
	 */
	public function display_theme_notice(): void {
		if ( ! Theme_Compatibility::has_incompatible_theme() ) {
			return;
		}

		if ( Theme_Compatibility::is_warning_dismissed() ) {
			return;
		}

		$theme_name = Theme_Compatibility::get_incompatible_theme_name();

		if ( empty( $theme_name ) ) {
			return;
		}

		$this->render_theme_notice( $theme_name );
	}

	/**
	 * Render plugin compatibility notice
	 *
	 * @param array<string, array{name: string, reason?: string}> $plugins Incompatible plugins.
	 *
	 * @return void
	 */
	private function render_plugin_notice( array $plugins ): void {
		$plugin_names = array_map(
			function ( $info ) {
				return '<strong>' . esc_html( $info['name'] ) . '</strong>';
			},
			$plugins
		);

		$nonce = wp_create_nonce( 'gdmu_compatibility' );
		?>
		<div id="gdmu-plugin-compatibility-notice" class="notice notice-warning is-dismissible gdmu-compatibility-notice" data-type="plugin" data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<p>
				<span class="dashicons dashicons-warning" style="color: #dba617; margin-right: 4px;"></span>
				<?php
				printf(
					wp_kses(
						/* translators: %s: plugin name */
						__( 'Some Airo for WordPress features are now limited, deactivate %s to restore full functionality.', 'wp-site-designer-mu-plugins' ),
						array( 'strong' => array() )
					),
					wp_kses( implode( ', ', $plugin_names ), array( 'strong' => array() ) )
				);
				?>
			</p>
			<p class="gdmu-notice-actions">
				<?php foreach ( $plugins as $plugin_file => $info ) : ?>
					<button type="button"
						class="button gdmu-deactivate-plugin"
						data-plugin="<?php echo esc_attr( $plugin_file ); ?>"
						data-nonce="<?php echo esc_attr( $nonce ); ?>">
						<?php
						printf(
							/* translators: %s: plugin name */
							esc_html__( 'Deactivate %s', 'wp-site-designer-mu-plugins' ),
							esc_html( $info['name'] )
						);
						?>
					</button>
				<?php endforeach; ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render theme compatibility notice
	 *
	 * @param string $theme_name Current theme name.
	 *
	 * @return void
	 */
	private function render_theme_notice( string $theme_name ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$recommended_name = Compatibility_Registry::get_recommended_theme_name();
		$nonce            = wp_create_nonce( 'gdmu_compatibility' );
		?>
		<div id="gdmu-theme-compatibility-notice" class="notice notice-warning is-dismissible gdmu-compatibility-notice" data-type="theme" data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<p>
				<span class="dashicons dashicons-warning" style="color: #dba617; margin-right: 4px;"></span>
				<?php
				printf(
					wp_kses(
						/* translators: %s: recommended theme name */
						__( 'Some Airo for WordPress features are now limited, switch to %s to restore full functionality.', 'wp-site-designer-mu-plugins' ),
						array( 'strong' => array() )
					),
					'<strong>' . esc_html( $recommended_name ) . '</strong>'
				);
				?>
			</p>
			<p class="gdmu-notice-actions">
				<button type="button"
					class="button button-primary gdmu-switch-theme"
					data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<?php
					printf(
						/* translators: %s: recommended theme name */
						esc_html__( 'Switch to %s', 'wp-site-designer-mu-plugins' ),
						esc_html( $recommended_name )
					);
					?>
				</button>
			</p>
		</div>
		<?php
	}

	/**
	 * Enqueue scripts for notice interactions
	 *
	 * @return void
	 */
	public function enqueue_notice_scripts(): void {
		// Only load if we might have notices.
		$has_plugin_issues = ! empty( Compatibility_Registry::get_active_blocked_plugins() )
			|| ! empty( Compatibility_Registry::get_active_review_plugins() );
		$has_theme_issues  = Theme_Compatibility::has_incompatible_theme()
							&& ! Theme_Compatibility::is_warning_dismissed();

		if ( ! $has_plugin_issues && ! $has_theme_issues ) {
			return;
		}

		wp_add_inline_script( 'jquery', $this->get_notice_script() );
	}

	/**
	 * Enqueue notices for the block editor (post.php, site-editor.php)
	 *
	 * Uses WordPress's native @wordpress/notices store to display notices
	 * within the editor interface.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_notices(): void {
		$notices = array();

		// Check for plugin issues.
		$blocked_plugins = Compatibility_Registry::get_active_blocked_plugins();
		$review_plugins  = Compatibility_Registry::get_active_review_plugins();

		// Filter out dismissed warnings.
		$blocked_plugins = array_filter(
			$blocked_plugins,
			function ( $info, $plugin ) {
				return ! Plugin_Compatibility::is_warning_dismissed( $plugin );
			},
			ARRAY_FILTER_USE_BOTH
		);

		$review_plugins = array_filter(
			$review_plugins,
			function ( $info, $plugin ) {
				return ! Plugin_Compatibility::is_warning_dismissed( $plugin );
			},
			ARRAY_FILTER_USE_BOTH
		);

		if ( ! empty( $blocked_plugins ) || ! empty( $review_plugins ) ) {
			$all_plugins  = array_merge( $blocked_plugins, $review_plugins );
			$plugin_names = array_map(
				function ( $info ) {
					return $info['name'];
				},
				$all_plugins
			);

			$notices[] = array(
				'id'      => 'gdmu-plugin-compatibility',
				'status'  => 'warning',
				'content' => sprintf(
					/* translators: %s: plugin name */
					__( 'Some Airo for WordPress features are now limited, deactivate %s to restore full functionality.', 'wp-site-designer-mu-plugins' ),
					implode( ', ', $plugin_names )
				),
			);
		}

		// Check for theme issues.
		if ( Theme_Compatibility::has_incompatible_theme() && ! Theme_Compatibility::is_warning_dismissed() ) {
			$theme_name       = Theme_Compatibility::get_incompatible_theme_name();
			$recommended_name = Compatibility_Registry::get_recommended_theme_name();

			$notices[] = array(
				'id'      => 'gdmu-theme-compatibility',
				'status'  => 'warning',
				'content' => sprintf(
					/* translators: %s: recommended theme name */
					__( 'Some Airo for WordPress features are now limited, switch to %s to restore full functionality.', 'wp-site-designer-mu-plugins' ),
					$recommended_name
				),
			);
		}

		if ( empty( $notices ) ) {
			return;
		}

		// Enqueue inline script to dispatch notices.
		$script = $this->get_block_editor_notice_script( $notices );
		wp_add_inline_script( 'wp-notices', $script );
	}

	/**
	 * Get JavaScript to dispatch notices in the block editor
	 *
	 * @param array $notices Array of notices to dispatch.
	 *
	 * @return string
	 */
	private function get_block_editor_notice_script( array $notices ): string {
		$notices_json = wp_json_encode( $notices );

		return "
		( function() {
			var notices = {$notices_json};
			
			function dispatchNotices() {
				if ( ! wp.data || ! wp.data.dispatch ) {
					return;
				}
				
				var noticesStore = wp.data.dispatch( 'core/notices' );
				if ( ! noticesStore ) {
					return;
				}
				
				notices.forEach( function( notice ) {
					noticesStore.createWarningNotice( notice.content, {
						id: notice.id,
						isDismissible: true,
						type: 'default'
					} );
				} );
			}
			
			// Wait for editor to be ready.
			if ( document.readyState === 'complete' ) {
				setTimeout( dispatchNotices, 100 );
			} else {
				window.addEventListener( 'load', function() {
					setTimeout( dispatchNotices, 100 );
				} );
			}
		} )();
		";
	}

	/**
	 * Get inline JavaScript for notice interactions
	 *
	 * @return string
	 */
	private function get_notice_script(): string {
		return "
		jQuery(function($) {
			// Deactivate plugin from notice
			$('.gdmu-deactivate-plugin').on('click', function() {
				var btn = $(this);
				var plugin = btn.data('plugin');
				var nonce = btn.data('nonce');
				
				btn.prop('disabled', true).text('Deactivating...');
				
				$.post(ajaxurl, {
					action: 'gdmu_deactivate_plugin',
					plugin: plugin,
					nonce: nonce
				}, function(response) {
					if (response.success && response.data && response.data.redirect) {
						window.location.href = response.data.redirect;
					} else if (response.success) {
						window.location.href = ajaxurl.replace('admin-ajax.php', 'plugins.php');
					} else {
						btn.prop('disabled', false).text('Error - Try Again');
					}
				});
			});
			
			// Switch theme from notice
			$('.gdmu-switch-theme').on('click', function() {
				var btn = $(this);
				var nonce = btn.data('nonce');
				
				btn.prop('disabled', true).text('Switching...');
				
				$.post(ajaxurl, {
					action: 'gdmu_switch_theme',
					nonce: nonce
				}, function(response) {
					if (response.success) {
						window.location.reload();
					} else {
						btn.prop('disabled', false).text('Error - Try Again');
					}
				});
			});
			
			// Dismiss notice via WordPress X button
			$('.gdmu-compatibility-notice').on('click', '.notice-dismiss', function() {
				var notice = $(this).closest('.gdmu-compatibility-notice');
				var type = notice.data('type');
				var nonce = notice.data('nonce');
				var action = type === 'theme' ? 'gdmu_dismiss_theme_notice' : 'gdmu_dismiss_plugin_notice';
				
				// Get all plugin files for plugin notices
				var plugins = [];
				if (type === 'plugin') {
					notice.find('.gdmu-deactivate-plugin').each(function() {
						plugins.push($(this).data('plugin'));
					});
				}
				
				$.post(ajaxurl, {
					action: action,
					nonce: nonce,
					plugins: plugins
				});
			});
		});
		";
	}

	/**
	 * AJAX handler: Dismiss plugin notice
	 *
	 * @return void
	 */
	public function ajax_dismiss_plugin_notice(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		$plugins = isset( $_POST['plugins'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['plugins'] ) ) : array();

		foreach ( $plugins as $plugin ) {
			Plugin_Compatibility::dismiss_warning( $plugin );
		}

		wp_send_json_success();
	}

	/**
	 * AJAX handler: Dismiss theme notice
	 *
	 * @return void
	 */
	public function ajax_dismiss_theme_notice(): void {
		check_ajax_referer( 'gdmu_compatibility', 'nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied' ) );
		}

		Theme_Compatibility::dismissWarning();

		wp_send_json_success();
	}
}

