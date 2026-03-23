<?php
/**
 * Upload Image Tool Class
 *
 * @package     mcp-adapter-initializer
 * @author      GoDaddy
 * @copyright   2025 GoDaddy
 * @license     GPL-2.0-or-later
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Upload Image Tool
 *
 * Handles the registration and execution of the upload image ability
 * for the MCP adapter.
 */
class Upload_Image_Tool extends Base_Tool {
	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/upload-image';

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
		* @return self
		*/
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {}

	/**
	 * Register the upload image ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Upload Image', 'mcp-adapter-initializer' ),
				'description'         => __( 'Downloads an image from a URL and uploads it to the WordPress media library, optionally attaching it to a post', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'media-management',
			)
		);
	}

	/**
	 * Get the tool identifier
	 *
	 * @return string
	 */
	public function get_tool_id(): string {
		return self::TOOL_ID;
	}

	/**
	 * Get input schema for the tool
	 *
	 * @return array
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'url'         => array(
					'type'        => 'string',
					'description' => __( 'The URL of the image to download and upload', 'mcp-adapter-initializer' ),
				),
				'post_id'     => array(
					'type'        => 'integer',
					'description' => __( 'Optional. The post ID to attach the image to', 'mcp-adapter-initializer' ),
				),
				'title'       => array(
					'type'        => 'string',
					'description' => __( 'Optional. The title for the image', 'mcp-adapter-initializer' ),
				),
				'description' => array(
					'type'        => 'string',
					'description' => __( 'Optional. The description for the image', 'mcp-adapter-initializer' ),
				),
				'alt_text'    => array(
					'type'        => 'string',
					'description' => __( 'Optional. The alt text for the image', 'mcp-adapter-initializer' ),
				),
				'caption'     => array(
					'type'        => 'string',
					'description' => __( 'Optional. The caption for the image', 'mcp-adapter-initializer' ),
				),
			),
			'required'   => array( 'url' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Image upload result', 'mcp-adapter-initializer' ),
			array(
				'attachment_id' => array(
					'type'        => 'integer',
					'description' => __( 'The attachment ID of the uploaded image', 'mcp-adapter-initializer' ),
				),
				'url'           => array(
					'type'        => 'string',
					'description' => __( 'The URL of the uploaded image', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the upload image tool
	 *
	 * @param array $input Input parameters
	 * @return array Upload result or error
	 */
	public function execute( array $input ): array {
		if ( empty( $input['url'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Image URL is required', 'mcp-adapter-initializer' ),
			);
		}

		// Sanitize and validate URL
		$url = esc_url_raw( $input['url'] );

		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid URL provided', 'mcp-adapter-initializer' ),
			);
		}

		// Check user permissions
		if ( ! current_user_can( 'upload_files' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to upload files', 'mcp-adapter-initializer' ),
			);
		}

		// Get post ID if provided, otherwise null
		$post_id = isset( $input['post_id'] ) ? absint( $input['post_id'] ) : null;

		// Validate post ID only if one was provided
		if ( $post_id && ! get_post( $post_id ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Post with ID %d does not exist', 'mcp-adapter-initializer' ), $post_id ),
			);
		}

		// Get title/description
		$title = isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : null;

		// Stage metadata to apply after upload (avoid multiple DB writes)
		$metadata_updates = array();

		if ( isset( $input['alt_text'] ) ) {
			$metadata_updates['alt_text'] = sanitize_text_field( $input['alt_text'] );
		}

		if ( isset( $input['description'] ) ) {
			$metadata_updates['post_content'] = wp_kses_post( $input['description'] );
		}

		if ( isset( $input['caption'] ) ) {
			$metadata_updates['caption'] = wp_kses_post( $input['caption'] );
		}

		// Include media handling functions
		if ( ! function_exists( 'media_sideload_image' ) ) {
			$this->load_admin_file( 'media.php' );
			$this->load_admin_file( 'file.php' );
			$this->load_admin_file( 'image.php' );
		}

		// Upload the image and get the attachment ID
		$attachment_id = media_sideload_image( $url, $post_id, $title, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to upload image: %s', 'mcp-adapter-initializer' ), $attachment_id->get_error_message() ),
			);
		}

		// Apply staged metadata updates in a single pass
		if ( ! empty( $metadata_updates ) ) {
			$post_update = array( 'ID' => $attachment_id );

			// Update caption (post_excerpt) if provided
			if ( isset( $metadata_updates['caption'] ) ) {
				$post_update['post_excerpt'] = $metadata_updates['caption'];
			}

			// Update description (post_content) if provided
			if ( isset( $metadata_updates['post_content'] ) ) {
				$post_update['post_content'] = $metadata_updates['post_content'];
			}

			// Update post data if we have changes
			if ( count( $post_update ) > 1 ) {
				$update_result = wp_update_post( $post_update, true );
				if ( is_wp_error( $update_result ) ) {
					// Image was uploaded but metadata update failed - log but don't fail
					error_log( sprintf( 'Upload Image Tool: Failed to update post metadata for attachment %d: %s', $attachment_id, $update_result->get_error_message() ) );
				}
			}

			// Update alt text (post meta) if provided
			if ( isset( $metadata_updates['alt_text'] ) ) {
				$alt_text_result = update_post_meta( $attachment_id, '_wp_attachment_image_alt', $metadata_updates['alt_text'] );
				if ( false === $alt_text_result ) {
					// Image was uploaded but alt text update failed - log but don't fail
					error_log( sprintf( 'Upload Image Tool: Failed to update alt text for attachment %d', $attachment_id ) );
				}
			}
		}

		// Get the attachment URL
		$attachment_url = wp_get_attachment_url( $attachment_id );

		return array(
			'success'       => true,
			'attachment_id' => $attachment_id,
			'url'           => $attachment_url,
			'message'       => __( 'Image uploaded successfully', 'mcp-adapter-initializer' ),
		);
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}
}
