<?php
/**
 * List Posts Tool Class
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
 * List Posts Tool
 *
 * Handles the registration and execution of the list posts ability
 * for the MCP adapter. Provides functionality similar to the WordPress
 * REST API /wp/v2/posts endpoint with support for custom post types.
 */
class List_Posts_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/list-posts';

	/**
	 * Tool instance
	 *
	 * @var List_Posts_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Posts_Tool
	 */
	public static function get_instance(): List_Posts_Tool {
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
	 * Register the list posts ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Posts', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of WordPress posts, pages, or custom post types with filtering and pagination options', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'content-management',
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
				'post_type'      => array(
					'type'        => 'string',
					'description' => __( 'The post type (post, page, or custom post type). Defaults to page.', 'mcp-adapter-initializer' ),
					'default'     => 'page',
				),
				'page'           => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'mcp-adapter-initializer' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page'       => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to be returned in result set', 'mcp-adapter-initializer' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'search'         => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'mcp-adapter-initializer' ),
				),
				'author'         => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to posts assigned to specific author ID', 'mcp-adapter-initializer' ),
				),
				'exclude'        => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes specific IDs', 'mcp-adapter-initializer' ),
				),
				'include'        => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to specific IDs', 'mcp-adapter-initializer' ),
				),
				'order'          => array(
					'type'        => 'string',
					'description' => __( 'Order sort attribute ascending or descending', 'mcp-adapter-initializer' ),
					'enum'        => array( 'asc', 'desc' ),
					'default'     => 'desc',
				),
				'orderby'        => array(
					'type'        => 'string',
					'description' => __( 'Sort collection by post attribute', 'mcp-adapter-initializer' ),
					'enum'        => array( 'author', 'date', 'id', 'include', 'modified', 'parent', 'title', 'menu_order' ),
					'default'     => 'date',
				),
				'parent'         => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to items with particular parent ID', 'mcp-adapter-initializer' ),
				),
				'parent_exclude' => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to all items except those of a particular parent ID', 'mcp-adapter-initializer' ),
				),
				'slug'           => array(
					'type'        => array( 'string', 'array' ),
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Limit result set to posts with one or more specific slugs. Can be a single slug string or an array of slugs', 'mcp-adapter-initializer' ),
				),
				'status'         => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to posts assigned one or more statuses', 'mcp-adapter-initializer' ),
					'enum'        => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'any' ),
					'default'     => 'publish',
				),
				'include_meta'   => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include post meta data', 'mcp-adapter-initializer' ),
					'default'     => false,
				),
				'_fields'        => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Limit response to specific fields. Available fields: id, title, content, excerpt, status, author_id, date_created, date_modified, slug, parent_id, menu_order, meta', 'mcp-adapter-initializer' ),
				),
				'context'        => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
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
				'posts'       => array(
					'type'        => 'array',
					'description' => __( 'Array of post objects', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'            => array(
								'type'        => 'integer',
								'description' => __( 'The post ID', 'mcp-adapter-initializer' ),
							),
							'title'         => array(
								'type'        => 'string',
								'description' => __( 'The post title', 'mcp-adapter-initializer' ),
							),
							'content'       => array(
								'type'        => 'string',
								'description' => __( 'The post content', 'mcp-adapter-initializer' ),
							),
							'excerpt'       => array(
								'type'        => 'string',
								'description' => __( 'The post excerpt', 'mcp-adapter-initializer' ),
							),
							'status'        => array(
								'type'        => 'string',
								'description' => __( 'The post status', 'mcp-adapter-initializer' ),
							),
							'author_id'     => array(
								'type'        => 'integer',
								'description' => __( 'The post author ID', 'mcp-adapter-initializer' ),
							),
							'date_created'  => array(
								'type'        => 'string',
								'description' => __( 'The post creation date', 'mcp-adapter-initializer' ),
							),
							'date_modified' => array(
								'type'        => 'string',
								'description' => __( 'The post modification date', 'mcp-adapter-initializer' ),
							),
							'slug'          => array(
								'type'        => 'string',
								'description' => __( 'The post slug', 'mcp-adapter-initializer' ),
							),
							'parent_id'     => array(
								'type'        => 'integer',
								'description' => __( 'The parent post ID', 'mcp-adapter-initializer' ),
							),
							'menu_order'    => array(
								'type'        => 'integer',
								'description' => __( 'The post menu order', 'mcp-adapter-initializer' ),
							),
							'meta'          => array(
								'type'        => 'object',
								'description' => __( 'Post meta data (if requested)', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of posts matching the query', 'mcp-adapter-initializer' ),
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
	 * Execute the list posts tool
	 *
	 * @param array $input Input parameters
	 * @return array List of posts with pagination info
	 */
	public function execute( array $input ): array {
		// Build query arguments
		$query_args = $this->build_query_args( $input );

		// Execute the query
		$query = new \WP_Query( $query_args );

		// Process results
		$posts         = array();
		$include_meta  = ! empty( $input['include_meta'] );
		$fields_filter = ! empty( $input['_fields'] ) && is_array( $input['_fields'] ) ? $input['_fields'] : array();
		$context       = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				// Build post data based on context
				$complete_post_data = $this->build_post_data( $post, $context, $include_meta );

				// Apply fields filter if specified
				if ( ! empty( $fields_filter ) ) {
					$post_data = $this->filter_fields( $complete_post_data, $fields_filter );
				} else {
					$post_data = $complete_post_data;
				}

				$posts[] = $post_data;
			}
			wp_reset_postdata();
		}

		// Calculate pagination
		$page        = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$per_page    = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$total       = $query->found_posts;
		$total_pages = max( 1, ceil( $total / $per_page ) );

		return array(
			'posts'       => $posts,
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
		// Get post type, defaulting to 'page'
		$post_type = isset( $input['post_type'] ) && '' !== $input['post_type'] ? sanitize_key( $input['post_type'] ) : 'page';

		// Base query args
		$args = array(
			'post_type'      => $post_type,
			'post_status'    => isset( $input['status'] ) ? $input['status'] : 'publish',
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

		return $args;
	}

	/**
	 * Build post data based on context
	 *
	 * @param \WP_Post $post         Post object
	 * @param string   $context      Context (view, embed, edit)
	 * @param bool     $include_meta Whether to include meta data
	 * @return array Post data
	 */
	private function build_post_data( \WP_Post $post, string $context, bool $include_meta ): array {
		// Base data available in all contexts
		$post_data = array(
			'id'         => $post->ID,
			'title'      => $post->post_title,
			'slug'       => $post->post_name,
			'status'     => $post->post_status,
			'parent_id'  => (int) $post->post_parent,
			'menu_order' => (int) $post->menu_order,
		);

		// Add fields based on context
		switch ( $context ) {
			case 'embed':
				// Embed context: minimal data for embedding
				$post_data['excerpt']       = wp_trim_words( $post->post_excerpt ? $post->post_excerpt : $post->post_content, 55 );
				$post_data['date_created']  = $post->post_date;
				$post_data['date_modified'] = $post->post_modified;
				break;

			case 'edit':
				// Edit context: all fields including sensitive data
				$post_data['content']       = $post->post_content; // Raw content for editing
				$post_data['excerpt']       = $post->post_excerpt;
				$post_data['author_id']     = (int) $post->post_author;
				$post_data['date_created']  = $post->post_date;
				$post_data['date_modified'] = $post->post_modified;

				// Include meta data for editing
				if ( $include_meta ) {
					$post_data['meta'] = get_post_meta( $post->ID );
				}
				break;

			case 'view':
			default:
				// View context: standard public view
				$post_data['content']       = apply_filters( 'the_content', $post->post_content );
				$post_data['excerpt']       = $post->post_excerpt;
				$post_data['author_id']     = (int) $post->post_author;
				$post_data['date_created']  = $post->post_date;
				$post_data['date_modified'] = $post->post_modified;

				// Include meta data if requested
				if ( $include_meta ) {
					$post_data['meta'] = get_post_meta( $post->ID );
				}
				break;
		}

		return $post_data;
	}

	/**
	 * Filter post data to only include specified fields
	 *
	 * @param array $post_data Complete post data
	 * @param array $fields    Array of field names to include
	 * @return array Filtered post data
	 */
	private function filter_fields( array $post_data, array $fields ): array {
		$filtered = array();

		// Always include 'id' field for reference
		if ( isset( $post_data['id'] ) ) {
			$filtered['id'] = $post_data['id'];
		}

		// Include requested fields
		foreach ( $fields as $field ) {
			$field = sanitize_key( $field );
			if ( isset( $post_data[ $field ] ) && 'id' !== $field ) {
				$filtered[ $field ] = $post_data[ $field ];
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
