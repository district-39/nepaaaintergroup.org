<?php
/**
 * Media Upload Plugin
 *
 * Provides WordPress media library integration for Site Designer iframe contexts.
 * Enables uploading and selecting media from within the embedded WordPress instance,
 * with secure cross-origin communication back to the parent Site Designer application.
 *
 * Why this exists:
 * - Site Designer embeds WordPress in an iframe
 * - Users need to upload/select images for their site (logos, content images)
 * - This bridges the WordPress media library modal to the parent app via postMessage
 *
 * Security model:
 * - Requires user to be logged in with 'upload_files' capability
 * - Origin validation is handled server-side by Iframe_Context_Detector
 * - PostMessage uses '*' since we've already validated the parent origin
 *
 * @package wp-site-designer-mu-plugins
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;

/**
 * Manages WordPress media library integration for Site Designer
 *
 * Enqueues media library scripts and provides configuration for
 * cross-origin communication with the parent Site Designer window.
 */
class Media_Upload {

	/**
	 * Script handle for the media upload JavaScript
	 *
	 * @var string
	 */
	private const SCRIPT_HANDLE = 'gdmu-site-designer-media-upload';

	/**
	 * Initialize the class and register all hooks
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();

		// Enqueue media library and scripts.
		add_action( 'admin_enqueue_scripts', array( $instance, 'enqueue_media_admin' ) );
		add_action( 'wp_enqueue_scripts', array( $instance, 'enqueue_media_frontend' ) );

		// Output configuration for JavaScript.
		add_action( 'wp_footer', array( $instance, 'output_media_config' ), 999 );
		add_action( 'admin_footer', array( $instance, 'output_media_config' ), 999 );
	}

	/**
	 * Enqueue media library in admin context
	 *
	 * Only loads for users who have permission to upload files.
	 *
	 * @return void
	 */
	public function enqueue_media_admin(): void {
		if ( ! $this->user_can_upload() ) {
			return;
		}

		wp_enqueue_media();
		$this->enqueue_media_script();
	}

	/**
	 * Enqueue media library on frontend
	 *
	 * Required for iframe contexts where WordPress frontend is displayed
	 * but user needs access to media upload functionality.
	 *
	 * @return void
	 */
	public function enqueue_media_frontend(): void {
		if ( ! $this->user_can_upload() ) {
			return;
		}

		wp_enqueue_media();
		$this->enqueue_media_script();
	}

	/**
	 * Enqueue the media upload JavaScript
	 *
	 * Registers and enqueues the custom media upload script that handles
	 * postMessage communication with the parent Site Designer window.
	 *
	 * @return void
	 */
	private function enqueue_media_script(): void {
		// Prevent double-loading if called from multiple hooks.
		if ( wp_script_is( self::SCRIPT_HANDLE, 'enqueued' ) ) {
			return;
		}

		$script_path = GDMU_SITE_DESIGNER_PATH . '/assets/js/media-upload.js';
		$script_url  = GDMU_SITE_DESIGNER_URL . '/assets/js/media-upload.js';

		// Verify script file exists before enqueueing.
		if ( ! file_exists( $script_path ) ) {
			$this->log_error( 'JavaScript file not found', $script_path );
			return;
		}

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			$script_url,
			array( 'media-editor', 'media-views', 'media-models' ),
			GDMU_SITE_DESIGNER_VERSION,
			true
		);
	}

	/**
	 * Output configuration object for frontend JavaScript
	 *
	 * Provides the validated parent origin for secure postMessage communication.
	 * Only the single validated origin is exposed, not the full allowed list.
	 *
	 * @return void
	 */
	public function output_media_config(): void {
		if ( ! $this->user_can_upload() ) {
			return;
		}

		// Get the validated parent origin (only available if request passed validation).
		$parent_origin = Iframe_Context_Detector::get_parent_origin();

		?>
		<script>
		window.siteDesignerMedia = window.siteDesignerMedia || {};
		Object.assign( window.siteDesignerMedia, {
			ready: !!( window.wp && window.wp.media ),
			config: {
				parentOrigin: <?php echo wp_json_encode( $parent_origin ); ?>
			}
		} );
		</script>
		<?php
	}

	/**
	 * Check if current user has permission to upload files
	 *
	 * @return bool True if user is logged in and can upload files.
	 */
	private function user_can_upload(): bool {
		return is_user_logged_in() && current_user_can( 'upload_files' );
	}

	/**
	 * Log error message in debug mode
	 *
	 * Only logs when WP_DEBUG is enabled to avoid cluttering production logs.
	 *
	 * @param string $message Error message.
	 * @param string $path    Related file path for context.
	 * @return void
	 */
	private function log_error( string $message, string $path ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'Site Designer Media Upload: %s - Path: %s', $message, $path ) );
		}
	}
}

