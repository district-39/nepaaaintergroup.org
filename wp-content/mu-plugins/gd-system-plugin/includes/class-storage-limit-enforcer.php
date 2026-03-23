<?php
/**
 * class-storage-limit-enforcer.php
 *
 * Disables relevant capabilities when the storage limit is exceeded.
 */

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Storage_Limit_Enforcer {
	use Helpers;

	/**
	 * Instance of the Storage_Limit_Checker.
	 *
	 * @var Storage_Limit_Checker
	 */
	private Storage_Limit_Checker $storage_limit_checker;

	public function __construct( Storage_Limit_Checker $storage_limit_checker ) {

		$this->storage_limit_checker = $storage_limit_checker;

		if ( ! self::is_wpaas_v2() ) {
			return;
		}

		if ( self::is_mwcs() ) {
			return;
		}

        $should_check_storage = $GLOBALS['wpaas_feature_flag']->get_feature_flag_value( 'admin_storage_notice_gd', false );

		// Check if the feature flag is enabled

		if ( ! $should_check_storage ) {
			return;
		}

		// Block media uploads at a low level.
		add_filter( 'wp_handle_upload_prefilter', [ $this, 'block_new_uploads' ] );

		// Block media upload side-loads at a low level.
		add_filter( 'wp_handle_sideload_prefilter', [ $this, 'block_sideloaded_files' ] );

		// Block plugin installs at a low level.
		add_filter( 'upgrader_pre_install', [ $this, 'block_plugin_install' ], 10, 2 );

		// Block theme installs at a low level.
		add_filter( 'upgrader_pre_install', [ $this, 'block_theme_install' ], 10, 2 );

		// Show a user‐friendly modal on the “Upload Media” or “Install Plugin” pages.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_storage_limit_modal_script' ] );

	}

	/**
	 * Stop new file uploads by returning a custom error string (instead of a WP_Error).
	 * This typically produces a user-friendly error in the Media Library rather than a fatal 500.
	 */
	public function block_new_uploads( $file ) {
		if ( $this->is_storage_limit_exceeded() ) {
			// Use the key "error" to present a non-fatal error message.
			$file['error'] = __( 'Storage limit exceeded. You cannot upload new files.', 'gd-system-plugin' );
		}

		return $file;
	}

	public function block_sideloaded_files( $file ) {
		if ( $this->is_storage_limit_exceeded() ) {
			// Provide a user-friendly error (instead of a fatal or 500 error).
			$file['error'] = __( 'Storage limit exceeded. You cannot side-load files from a URL. ', 'gd-system-plugin' );
		}

		return $file;
	}

	/**
	 * Hook into upgrader_pre_install to block new plugin installs if the limit is exceeded.
	 * Does NOT block plugin updates.
	 */
	public function block_plugin_install( $response, $hook_extra ) {
		// Only block if the storage limit is exceeded and the current action is an install (not an update).
		if ( $this->is_storage_limit_exceeded() &&
		     isset( $hook_extra['action'] ) &&
		     'install' === $hook_extra['action']
		) {
			$myh_url = self::get_myh_add_storage_url();

			return new \WP_Error(
				'exceed_limit',
				__( 'Storage limit exceeded. You cannot install new plugins. '
				    . '<a href="' . $myh_url . '" target="_blank">' . __( 'Add More Storage', 'gd-system-plugin' ) . '</a>'
					, 'gd-system-plugin' )
			);
		}

		return $response;
	}

	/**
	 * Block new theme installs (but allow theme updates).
	 */
	public function block_theme_install( $response, $hook_extra ) {
		// Only block if the storage limit is exceeded, $hook_extra['type'] is 'theme',
		// and the current action is 'install'.
		if ( $this->is_storage_limit_exceeded() &&
		     isset( $hook_extra['type'] ) && 'theme' === $hook_extra['type'] &&
		     isset( $hook_extra['action'] ) && 'install' === $hook_extra['action']
		) {
			$myh_url = self::get_myh_add_storage_url();

			return new \WP_Error(
				'exceed_limit',
				__( 'Storage limit exceeded. You cannot install new themes. '
				    . '<a href="' . $myh_url . '" target="_blank">' . __( 'Add More Storage', 'gd-system-plugin' ) . '</a>'
					, 'gd-system-plugin' )
			);
		}

		return $response;
	}

	/**
	 * Show a modal/alert on specific admin pages and redirect back if limit is exceeded.
	 */
	public function enqueue_storage_limit_modal_script( $hook_suffix ): void {
		if ( ! $this->storage_limit_checker->is_storage_limit_exceeded() && ! $this->storage_limit_checker->is_storage_limit_warnable() ) {
			return;
		}
		// Show the modal on: plugin-install.php or media-new.php
		if ( is_admin() && in_array( $hook_suffix, [
				'plugin-install.php',
				'media-new.php',
				'theme-install.php'
			], true ) ) {
			add_action( 'admin_print_footer_scripts', function () use ( $hook_suffix ) {
				$myh_url     = self::get_myh_add_storage_url();
				$is_exceeded = self::is_storage_limit_exceeded();
				?>
                <script>
                    (function ($) {
                        let action;
                        switch ('<?php echo esc_js( $hook_suffix ); ?>') {
                            case 'plugin-install.php':
                                action = '<?php echo esc_js( __( 'install plugins', 'gd-system-plugin' ) ); ?>';
                                break;
                            case 'media-new.php':
                                action = '<?php echo esc_js( __( 'add media', 'gd-system-plugin' ) ); ?>';
                                break;
                            case 'theme-install.php':
                                action = '<?php echo esc_js( __( 'install themes', 'gd-system-plugin' ) ); ?>';
                                break;
                            default:
                                action = '<?php echo esc_js( __( 'add media or install plugins and themes', 'gd-system-plugin' ) ); ?>';
                        }

                        const isExceeded = <?php echo $is_exceeded ? 'true' : 'false'; ?>;
                        const messageError = '<?php echo esc_js( __( 'Your storage limit is exceeded. You cannot proceed to ', 'gd-system-plugin' ) ); ?>' + action + '<?php echo esc_js( __( ' until you clear up space or add storage. ', 'gd-system-plugin' ) ); ?>';
                        const messageWarning = '<?php echo esc_js( __( 'Your storage is almost full. Once it is full, you will not be able to proceed to ', 'gd-system-plugin' ) ); ?>' + action + '<?php echo esc_js( __( ' unless you clear up space or add storage. ', 'gd-system-plugin' ) ); ?>';
                        const message = isExceeded ? messageError : messageWarning;

                        const addStorageButton = '<a class="storage-modal-close" href="<?php echo esc_url( $myh_url ); ?>" target="_blank" style="background-color: #0073aa; color: #fff; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px;"><?php echo esc_js( __( 'Add More Storage', 'gd-system-plugin' ) ); ?></a>';

                        const modalHtml = `
                            <div id="storage-modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                                <div style="background-color: #fff; padding: 20px; border: 1px solid #888; width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); border-radius: 5px; text-align: center;">
                                    <p style="margin: 0 0 20px;">${message}</p>
                                    <div style="text-align: center;">
                                        ${addStorageButton}
                                        <button class="storage-modal-close" style="background-color: #ccc; color: #333; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer;">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('body').append(modalHtml);

                        $('#storage-modal').show();

                        $('.storage-modal-close').on('click', function () {
                            $('#storage-modal').hide();
                        });
                    })(jQuery);
                </script>
				<?php
			} );
		}
	}

	/**
	 * Check for whether the storage limit is exceeded.
	 */
	private function is_storage_limit_exceeded(): bool {
		return $this->storage_limit_checker->is_storage_limit_exceeded();
	}

}
