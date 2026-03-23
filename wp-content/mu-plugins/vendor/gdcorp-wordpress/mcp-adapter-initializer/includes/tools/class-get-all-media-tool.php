<?php
/**
 * Get All Media Tool Class
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
 * Get All Media Tool
 *
 * Handles the registration and execution of the get all media ability
 * for the MCP adapter.
 */
class Get_All_Media_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/get-all-media';

	/**
	 * Tool instance
	 *
	 * @var Get_All_Media_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Get_All_Media_Tool
	 */
	public static function get_instance(): Get_All_Media_Tool {
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
	 * Register the get all media ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get All Media', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves all WordPress media attachments', 'mcp-adapter-initializer' ),
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
				'include_meta' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include attachment meta data', 'mcp-adapter-initializer' ),
					'default'     => false,
				),
			),
			'required'   => array(),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return array(
			'type'        => 'array',
			'description' => __( 'Array of all media attachments', 'mcp-adapter-initializer' ),
			'items'       => array(
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
			),
		);
	}

	/**
	 * Execute the get all media tool
	 *
	 * @param array $input Input parameters
	 * @return array Media list
	 */
	public function execute( array $input ): array {
		$include_meta = isset( $input['include_meta'] ) ? (bool) $input['include_meta'] : false;

		// Get attachments
		$query = new \WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => -1, // Get ALL media
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( ! $query->have_posts() ) {
			return array();
		}

		$media_items = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post = get_post();

			// Get attachment metadata
			$attachment_metadata = wp_get_attachment_metadata( $post->ID );
			$file_path           = get_attached_file( $post->ID );
			$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

			// Prepare media item
			$media_item = array(
				'id'            => $post->ID,
				'title'         => $post->post_title,
				'description'   => $post->post_content,
				'caption'       => $post->post_excerpt,
				'alt_text'      => get_post_meta( $post->ID, '_wp_attachment_image_alt', true ),
				'status'        => $post->post_status,
				'author_id'     => (int) $post->post_author,
				'date_created'  => $post->post_date,
				'date_modified' => $post->post_modified,
				'slug'          => $post->post_name,
				'url'           => wp_get_attachment_url( $post->ID ),
				'mime_type'     => $post->post_mime_type,
				'file_size'     => $file_size,
				'dimensions'    => array(),
			);

			// Add dimensions for images
			if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) ) {
				$media_item['dimensions'] = array(
					'width'  => (int) $attachment_metadata['width'],
					'height' => (int) $attachment_metadata['height'],
				);
			}

			// Add meta data if requested
			if ( $include_meta ) {
				$media_item['meta'] = $this->get_processed_meta( $post->ID, $attachment_metadata );
			}

			$media_items[] = $media_item;
		}

		// Reset post data
		wp_reset_postdata();

		return $media_items;
	}

	/**
	 * Get processed meta data for the attachment
	 *
	 * @param int   $media_id The media attachment ID
	 * @param array $attachment_metadata The attachment metadata
	 * @return array Processed meta data
	 */
	private function get_processed_meta( int $media_id, array $attachment_metadata ): array {
		$meta = array();

		// Get the attached file path
		$attached_file = get_post_meta( $media_id, '_wp_attached_file', true );
		if ( $attached_file ) {
			$meta['attached_file'] = $attached_file;
		}

		// Add image sizes information (if available)
		if ( isset( $attachment_metadata['sizes'] ) && is_array( $attachment_metadata['sizes'] ) ) {
			$meta['image_sizes'] = array();
			foreach ( $attachment_metadata['sizes'] as $size_name => $size_data ) {
				$meta['image_sizes'][ $size_name ] = array(
					'file'      => $size_data['file'],
					'width'     => $size_data['width'],
					'height'    => $size_data['height'],
					'mime_type' => $size_data['mime-type'],
					'filesize'  => isset( $size_data['filesize'] ) ? $size_data['filesize'] : 0,
				);
			}
		}

		// Add EXIF/image meta data (if available)
		if ( isset( $attachment_metadata['image_meta'] ) && is_array( $attachment_metadata['image_meta'] ) ) {
			$image_meta = $attachment_metadata['image_meta'];

			$meta['image_meta'] = array(
				'aperture'          => $image_meta['aperture'] ?? '',
				'credit'            => $image_meta['credit'] ?? '',
				'camera'            => $image_meta['camera'] ?? '',
				'caption'           => $image_meta['caption'] ?? '',
				'created_timestamp' => $image_meta['created_timestamp'] ?? '',
				'copyright'         => $image_meta['copyright'] ?? '',
				'focal_length'      => $image_meta['focal_length'] ?? '',
				'iso'               => $image_meta['iso'] ?? '',
				'shutter_speed'     => $image_meta['shutter_speed'] ?? '',
				'title'             => $image_meta['title'] ?? '',
				'orientation'       => $image_meta['orientation'] ?? '',
				'keywords'          => $image_meta['keywords'] ?? array(),
			);
		}

		return $meta;
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
