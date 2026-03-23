<?php
/**
 * Get Media By ID Tool Class
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
 * Get Media By ID Tool
 *
 * Handles the registration and execution of the get media by ID ability
 * for the MCP adapter.
 */
class Get_Media_By_Id_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/get-media-by-id';

	/**
	 * Tool instance
	 *
	 * @var Get_Media_By_Id_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Get_Media_By_Id_Tool
	 */
	public static function get_instance(): Get_Media_By_Id_Tool {
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
	 * Register the get media by ID ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Media By ID', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a WordPress media attachment by its ID', 'mcp-adapter-initializer' ),
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
				'media_id'     => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to retrieve', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'include_meta' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include attachment meta data', 'mcp-adapter-initializer' ),
					'default'     => true,
				),
			),
			'required'   => array( 'media_id' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'type'        => 'integer',
					'description' => __( 'The media attachment ID', 'mcp-adapter-initializer' ),
				),
				'title'         => array(
					'type'        => 'string',
					'description' => __( 'The media title', 'mcp-adapter-initializer' ),
				),
				'description'   => array(
					'type'        => 'string',
					'description' => __( 'The media description', 'mcp-adapter-initializer' ),
				),
				'caption'       => array(
					'type'        => 'string',
					'description' => __( 'The media caption', 'mcp-adapter-initializer' ),
				),
				'alt_text'      => array(
					'type'        => 'string',
					'description' => __( 'The media alt text', 'mcp-adapter-initializer' ),
				),
				'status'        => array(
					'type'        => 'string',
					'description' => __( 'The attachment status', 'mcp-adapter-initializer' ),
				),
				'author_id'     => array(
					'type'        => 'integer',
					'description' => __( 'The media author ID', 'mcp-adapter-initializer' ),
				),
				'date_created'  => array(
					'type'        => 'string',
					'description' => __( 'The media creation date', 'mcp-adapter-initializer' ),
				),
				'date_modified' => array(
					'type'        => 'string',
					'description' => __( 'The media modification date', 'mcp-adapter-initializer' ),
				),
				'slug'          => array(
					'type'        => 'string',
					'description' => __( 'The media slug', 'mcp-adapter-initializer' ),
				),
				'url'           => array(
					'type'        => 'string',
					'description' => __( 'The media file URL', 'mcp-adapter-initializer' ),
				),
				'mime_type'     => array(
					'type'        => 'string',
					'description' => __( 'The media MIME type', 'mcp-adapter-initializer' ),
				),
				'file_size'     => array(
					'type'        => 'integer',
					'description' => __( 'The file size in bytes', 'mcp-adapter-initializer' ),
				),
				'dimensions'    => array(
					'type'        => 'object',
					'properties'  => array(
						'width'  => array(
							'type'        => 'integer',
							'description' => __( 'Image width in pixels', 'mcp-adapter-initializer' ),
						),
						'height' => array(
							'type'        => 'integer',
							'description' => __( 'Image height in pixels', 'mcp-adapter-initializer' ),
						),
					),
					'description' => __( 'Image dimensions (for images)', 'mcp-adapter-initializer' ),
				),
				'meta'          => array(
					'type'        => 'object',
					'description' => __( 'Attachment meta data (if requested)', 'mcp-adapter-initializer' ),
				),
			),
		);
	}

	/**
	 * Execute the get media by ID tool
	 *
	 * @param array $input Input parameters
	 * @return array Media information or error
	 */
	public function execute( array $input ): array {
		$media_id = ! empty( $input['media_id'] ) ? (int) $input['media_id'] : 0;

		if ( empty( $media_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'Media ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$post = get_post( $media_id );

		// Check if post exists and is an attachment
		if ( ! $post ) {
			return array(
				'success' => false,
				/* translators: %d: Media ID */
				'message' => sprintf( __( 'Media with ID %d not found', 'mcp-adapter-initializer' ), $media_id ),
			);
		}

		if ( 'attachment' !== $post->post_type ) {
			return array(
				'success' => false,
				/* translators: %d: Post ID */
				'message' => sprintf( __( 'Post with ID %d is not a media attachment', 'mcp-adapter-initializer' ), $media_id ),
			);
		}

		// Get attachment metadata
		$attachment_metadata = wp_get_attachment_metadata( $media_id );
		$file_path           = get_attached_file( $media_id );
		$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

		// Prepare result
		$result = array(
			'id'            => $post->ID,
			'title'         => $post->post_title,
			'description'   => $post->post_content,
			'caption'       => $post->post_excerpt,
			'alt_text'      => get_post_meta( $media_id, '_wp_attachment_image_alt', true ),
			'status'        => $post->post_status,
			'author_id'     => (int) $post->post_author,
			'date_created'  => $post->post_date,
			'date_modified' => $post->post_modified,
			'slug'          => $post->post_name,
			'url'           => wp_get_attachment_url( $media_id ),
			'mime_type'     => $post->post_mime_type,
			'file_size'     => $file_size,
			'dimensions'    => array(),
			'meta'          => array(), // Always include meta field, even if empty
		);

		// Add dimensions for images
		if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) ) {
			$result['dimensions'] = array(
				'width'  => (int) $attachment_metadata['width'],
				'height' => (int) $attachment_metadata['height'],
			);
		}

		// Add meta data if requested
		if ( ! empty( $input['include_meta'] ) ) {
			$result['meta'] = get_post_meta( $media_id );
		}

		return $result;
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
