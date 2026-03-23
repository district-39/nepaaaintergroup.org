<?php
/**
 * List Media Tool Class
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
 * List Media Tool
 *
 * Handles the registration and execution of the list media ability
 * for the MCP adapter. Provides functionality similar to the WordPress
 * REST API /wp/v2/media endpoint with support for filtering and pagination.
 */
class List_Media_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/list-media';

	/**
	 * Tool instance
	 *
	 * @var List_Media_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Media_Tool
	 */
	public static function get_instance(): List_Media_Tool {
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
	 * Register the list media ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Media', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of WordPress media attachments with filtering and pagination options', 'mcp-adapter-initializer' ),
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
				'context'         => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'page'            => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'mcp-adapter-initializer' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page'        => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to be returned in result set', 'mcp-adapter-initializer' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'search'          => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'mcp-adapter-initializer' ),
				),
				'after'           => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media uploaded after a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'modified_after'  => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media modified after a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'author'          => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to media assigned to specific author ID', 'mcp-adapter-initializer' ),
				),
				'author_exclude'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes media assigned to specific author IDs', 'mcp-adapter-initializer' ),
				),
				'before'          => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media uploaded before a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'modified_before' => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media modified before a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'exclude'         => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes specific IDs', 'mcp-adapter-initializer' ),
				),
				'include'         => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to specific IDs', 'mcp-adapter-initializer' ),
				),
				'offset'          => array(
					'type'        => 'integer',
					'description' => __( 'Offset the result set by a specific number of items', 'mcp-adapter-initializer' ),
					'minimum'     => 0,
				),
				'order'           => array(
					'type'        => 'string',
					'description' => __( 'Order sort attribute ascending or descending', 'mcp-adapter-initializer' ),
					'enum'        => array( 'asc', 'desc' ),
					'default'     => 'desc',
				),
				'orderby'         => array(
					'type'        => 'string',
					'description' => __( 'Sort collection by media attribute', 'mcp-adapter-initializer' ),
					'enum'        => array( 'author', 'date', 'id', 'include', 'modified', 'parent', 'relevance', 'slug', 'title' ),
					'default'     => 'date',
				),
				'parent'          => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to media attached to a particular parent ID', 'mcp-adapter-initializer' ),
				),
				'parent_exclude'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to all media except those of a particular parent ID', 'mcp-adapter-initializer' ),
				),
				'slug'            => array(
					'description' => __( 'Limit result set to media with one or more specific slugs. Can be a single slug string or an array of slugs', 'mcp-adapter-initializer' ),
				),
				'status'          => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to media assigned one or more statuses', 'mcp-adapter-initializer' ),
					'enum'        => array( 'inherit', 'private', 'trash' ),
					'default'     => 'inherit',
				),
				'media_type'      => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to media of a particular media type', 'mcp-adapter-initializer' ),
					'enum'        => array( 'image', 'video', 'audio', 'application' ),
				),
				'mime_type'       => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to media of a particular MIME type', 'mcp-adapter-initializer' ),
				),
				'include_meta'    => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include attachment meta data', 'mcp-adapter-initializer' ),
					'default'     => false,
				),
				'_fields'         => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Limit response to specific fields. Available fields: id, title, description, caption, alt_text, status, author_id, date_created, date_modified, slug, url, mime_type, file_size, dimensions, meta', 'mcp-adapter-initializer' ),
				),
			),
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
				'media'       => array(
					'type'        => 'array',
					'description' => __( 'Array of media objects', 'mcp-adapter-initializer' ),
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
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of media items matching the query', 'mcp-adapter-initializer' ),
				),
				'total_pages' => array(
					'type'        => 'integer',
					'description' => __( 'Total number of pages available', 'mcp-adapter-initializer' ),
				),
				'page'        => array(
					'type'        => 'integer',
					'description' => __( 'Current page number', 'mcp-adapter-initializer' ),
				),
				'per_page'    => array(
					'type'        => 'integer',
					'description' => __( 'Number of items per page', 'mcp-adapter-initializer' ),
				),
			),
		);
	}

	/**
	 * Execute the list media tool
	 *
	 * @param array $input Input parameters
	 * @return array List of media with pagination info
	 */
	public function execute( array $input ): array {
		// Build query arguments
		$query_args = $this->build_query_args( $input );

		// Execute the query
		$query = new \WP_Query( $query_args );

		// Process results
		$media         = array();
		$include_meta  = ! empty( $input['include_meta'] );
		$fields_filter = ! empty( $input['_fields'] ) && is_array( $input['_fields'] ) ? $input['_fields'] : array();
		$context       = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				// Build media data based on context
				$complete_media_data = $this->build_media_data( $post, $context, $include_meta );

				// Apply fields filter if specified
				if ( ! empty( $fields_filter ) ) {
					$media_data = $this->filter_fields( $complete_media_data, $fields_filter );
				} else {
					$media_data = $complete_media_data;
				}

				$media[] = $media_data;
			}
			wp_reset_postdata();
		}

		// Calculate pagination
		$page        = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$per_page    = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$total       = $query->found_posts;
		$total_pages = max( 1, ceil( $total / $per_page ) );

		return array(
			'media'       => $media,
			'total'       => $total,
			'total_pages' => $total_pages,
			'page'        => $page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Build WP_Query arguments from input parameters
	 *
	 * @param array $input Input parameters
	 * @return array WP_Query arguments
	 */
	private function build_query_args( array $input ): array {
		// Base query args
		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => isset( $input['status'] ) ? $input['status'] : 'inherit',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
			'orderby'        => isset( $input['orderby'] ) ? $input['orderby'] : 'date',
		);

		// Search parameter
		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}

		// Author parameter
		if ( isset( $input['author'] ) ) {
			$args['author'] = (int) $input['author'];
		}

		// Author exclude parameter
		if ( ! empty( $input['author_exclude'] ) && is_array( $input['author_exclude'] ) ) {
			$args['author__not_in'] = array_map( 'intval', $input['author_exclude'] );
		}

		// Exclude parameter
		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			$args['post__not_in'] = array_map( 'intval', $input['exclude'] );
		}

		// Include parameter
		if ( ! empty( $input['include'] ) && is_array( $input['include'] ) ) {
			$args['post__in'] = array_map( 'intval', $input['include'] );
			// When using post__in, orderby should be set to post__in to maintain order
			if ( 'include' === $args['orderby'] ) {
				$args['orderby'] = 'post__in';
			}
		}

		// Offset parameter
		if ( isset( $input['offset'] ) ) {
			$args['offset'] = (int) $input['offset'];
		}

		// Parent parameter
		if ( isset( $input['parent'] ) ) {
			$args['post_parent'] = (int) $input['parent'];
		}

		// Parent exclude parameter
		if ( ! empty( $input['parent_exclude'] ) && is_array( $input['parent_exclude'] ) ) {
			$args['post_parent__not_in'] = array_map( 'intval', $input['parent_exclude'] );
		}

		// Slug parameter - supports single slug or array of slugs
		if ( ! empty( $input['slug'] ) ) {
			if ( is_array( $input['slug'] ) ) {
				// Multiple slugs
				$args['post_name__in'] = array_map( 'sanitize_title', $input['slug'] );
			} else {
				// Single slug - use post_name__in for consistency
				$args['post_name__in'] = array( sanitize_title( $input['slug'] ) );
			}
		}

		// Media type parameter (filters by MIME type prefix)
		if ( ! empty( $input['media_type'] ) ) {
			$media_type             = sanitize_text_field( $input['media_type'] );
			$args['post_mime_type'] = $media_type;
		}

		// MIME type parameter (exact MIME type match)
		if ( ! empty( $input['mime_type'] ) ) {
			$args['post_mime_type'] = sanitize_mime_type( $input['mime_type'] );
		}

		// Date query parameters
		$date_query = array();

		if ( ! empty( $input['after'] ) ) {
			$date_query[] = array(
				'after'     => $input['after'],
				'inclusive' => true,
				'column'    => 'post_date',
			);
		}

		if ( ! empty( $input['before'] ) ) {
			$date_query[] = array(
				'before'    => $input['before'],
				'inclusive' => true,
				'column'    => 'post_date',
			);
		}

		if ( ! empty( $input['modified_after'] ) ) {
			$date_query[] = array(
				'after'     => $input['modified_after'],
				'inclusive' => true,
				'column'    => 'post_modified',
			);
		}

		if ( ! empty( $input['modified_before'] ) ) {
			$date_query[] = array(
				'before'    => $input['modified_before'],
				'inclusive' => true,
				'column'    => 'post_modified',
			);
		}

		if ( ! empty( $date_query ) ) {
			$args['date_query'] = $date_query;
		}

		return $args;
	}

	/**
	 * Build media data based on context
	 *
	 * @param \WP_Post $post         Post object
	 * @param string   $context      Context (view, embed, edit)
	 * @param bool     $include_meta Whether to include meta data
	 * @return array Media data
	 */
	private function build_media_data( \WP_Post $post, string $context, bool $include_meta ): array {
		// Get attachment metadata
		$attachment_metadata = wp_get_attachment_metadata( $post->ID );
		$file_path           = get_attached_file( $post->ID );
		$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

		// Base data - consistent with get-all-media, get-media-by-id, and update-media-meta tools
		// TODO We have inconsistencies wrt other tools for string vs object handling for context-based fields
		$media_data = array(
			'id'            => $post->ID,
			'title'         => $post->post_title,
			'description'   => $post->post_content,
			'caption'       => $post->post_excerpt,
			'alt_text'      => get_post_meta( $post->ID, '_wp_attachment_image_alt', true ),
			'slug'          => $post->post_name,
			'status'        => $post->post_status,
			'author_id'     => (int) $post->post_author,
			'date_created'  => $post->post_date,
			'date_modified' => $post->post_modified,
			'url'           => wp_get_attachment_url( $post->ID ),
			'mime_type'     => $post->post_mime_type,
			'file_size'     => $file_size,
			'dimensions'    => array(),
		);

		// Add dimensions for images
		if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) ) {
			$media_data['dimensions'] = array(
				'width'  => (int) $attachment_metadata['width'],
				'height' => (int) $attachment_metadata['height'],
			);
		}

		// Include meta data if requested
		if ( $include_meta ) {
			$media_data['meta'] = $this->get_processed_meta( $post->ID, $attachment_metadata );
		}

		return $media_data;
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
	 * Filter media data to only include specified fields
	 *
	 * @param array $media_data Complete media data
	 * @param array $fields     Array of field names to include
	 * @return array Filtered media data
	 */
	private function filter_fields( array $media_data, array $fields ): array {
		$filtered = array();

		// Always include 'id' field for reference
		if ( isset( $media_data['id'] ) ) {
			$filtered['id'] = $media_data['id'];
		}

		// Include requested fields
		foreach ( $fields as $field ) {
			$field = sanitize_key( $field );
			if ( isset( $media_data[ $field ] ) && 'id' !== $field ) {
				$filtered[ $field ] = $media_data[ $field ];
			}
		}

		return $filtered;
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
