<?php
/**
 * List Navigations Tool
 *
 * Retrieves a list of navigation posts with filtering and pagination options
 *
 * @package MCP_Adapter_Initializer
 * @subpackage Tools
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * List Navigations Tool Class
 *
 * Provides functionality to list navigation posts with various filtering,
 * sorting, and pagination options similar to WordPress REST API.
 */
class List_Navigations_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/list-navigations';

	/**
	 * Singleton instance
	 *
	 * @var List_Navigations_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Navigations_Tool
	 */
	public static function get_instance(): List_Navigations_Tool {
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
	 * Register the list navigations ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Navigations', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of navigation posts with filtering and pagination options', 'mcp-adapter-initializer' ),
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
					'description' => __( 'Limit response to posts published after a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'modified_after'  => array(
					'type'        => 'string',
					'description' => __( 'Limit response to posts modified after a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'before'          => array(
					'type'        => 'string',
					'description' => __( 'Limit response to posts published before a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
				),
				'modified_before' => array(
					'type'        => 'string',
					'description' => __( 'Limit response to posts modified before a given ISO8601 compliant date', 'mcp-adapter-initializer' ),
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
					'description' => __( 'Sort collection by post attribute', 'mcp-adapter-initializer' ),
					'enum'        => array( 'author', 'date', 'id', 'include', 'modified', 'parent', 'relevance', 'slug', 'include_slugs', 'title' ),
					'default'     => 'date',
				),
				'search_columns'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Array of column names to be searched', 'mcp-adapter-initializer' ),
				),
				'slug'            => array(
					'description' => __( 'Limit result set to posts with one or more specific slugs', 'mcp-adapter-initializer' ),
				),
				'status'          => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to posts assigned one or more statuses', 'mcp-adapter-initializer' ),
					'default'     => 'publish',
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
				'navigations' => array(
					'type'        => 'array',
					'description' => __( 'Array of navigation objects', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'           => array(
								'type'        => 'integer',
								'description' => __( 'Unique identifier for the post', 'mcp-adapter-initializer' ),
							),
							'date'         => array(
								'type'        => 'string',
								'description' => __( 'The date the post was published, in the site\'s timezone', 'mcp-adapter-initializer' ),
							),
							'date_gmt'     => array(
								'type'        => 'string',
								'description' => __( 'The date the post was published, as GMT', 'mcp-adapter-initializer' ),
							),
							'guid'         => array(
								'type'        => 'object',
								'description' => __( 'The globally unique identifier for the post', 'mcp-adapter-initializer' ),
							),
							'modified'     => array(
								'type'        => 'string',
								'description' => __( 'The date the post was last modified, in the site\'s timezone', 'mcp-adapter-initializer' ),
							),
							'modified_gmt' => array(
								'type'        => 'string',
								'description' => __( 'The date the post was last modified, as GMT', 'mcp-adapter-initializer' ),
							),
							'slug'         => array(
								'type'        => 'string',
								'description' => __( 'An alphanumeric identifier for the post unique to its type', 'mcp-adapter-initializer' ),
							),
							'status'       => array(
								'type'        => 'string',
								'description' => __( 'A named status for the post', 'mcp-adapter-initializer' ),
							),
							'type'         => array(
								'type'        => 'string',
								'description' => __( 'Type of post', 'mcp-adapter-initializer' ),
							),
							'link'         => array(
								'type'        => 'string',
								'description' => __( 'URL to the post', 'mcp-adapter-initializer' ),
							),
							'title'        => array(
								'type'        => 'object',
								'description' => __( 'The title for the post', 'mcp-adapter-initializer' ),
							),
							'content'      => array(
								'type'        => 'object',
								'description' => __( 'The content for the post', 'mcp-adapter-initializer' ),
							),
							'template'     => array(
								'type'        => 'string',
								'description' => __( 'The theme file to use to display the post', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of navigations', 'mcp-adapter-initializer' ),
				),
				'total_pages' => array(
					'type'        => 'integer',
					'description' => __( 'Total number of pages', 'mcp-adapter-initializer' ),
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
	 * Execute the list navigations tool
	 *
	 * @param array $input Input parameters
	 * @return array List of navigations or error
	 */
	public function execute( array $input ): array {
		// Build query arguments
		$args = $this->build_query_args( $input );

		// Execute query
		$query = new \WP_Query( $args );

		// Build response
		$navigations = array();
		$context     = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				$navigations[] = $this->build_navigation_data( $post, $context );
			}
			wp_reset_postdata();
		}

		return array(
			'navigations' => $navigations,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => (int) ( isset( $input['page'] ) ? $input['page'] : 1 ),
			'per_page'    => (int) ( isset( $input['per_page'] ) ? $input['per_page'] : 10 ),
		);
	}

	/**
	 * Build query arguments from input
	 *
	 * @param array $input Input parameters
	 * @return array WP_Query arguments
	 */
	private function build_query_args( array $input ): array {
		$args = array(
			'post_type'      => 'wp_navigation',
			'post_status'    => isset( $input['status'] ) ? $input['status'] : 'publish',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
			'orderby'        => isset( $input['orderby'] ) ? $input['orderby'] : 'date',
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
		);

		// Search parameter
		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}

		// Search columns parameter
		if ( ! empty( $input['search_columns'] ) && is_array( $input['search_columns'] ) ) {
			$args['search_columns'] = array_map( 'sanitize_text_field', $input['search_columns'] );
		}

		// Include parameter
		if ( ! empty( $input['include'] ) && is_array( $input['include'] ) ) {
			$args['post__in'] = array_map( 'intval', $input['include'] );
		}

		// Exclude parameter
		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			$args['post__not_in'] = array_map( 'intval', $input['exclude'] );
		}

		// Offset parameter
		if ( isset( $input['offset'] ) ) {
			$args['offset'] = (int) $input['offset'];
		}

		// Slug parameter - supports single slug or array of slugs
		if ( ! empty( $input['slug'] ) ) {
			if ( is_array( $input['slug'] ) ) {
				$args['post_name__in'] = array_map( 'sanitize_title', $input['slug'] );
			} else {
				$args['post_name__in'] = array( sanitize_title( $input['slug'] ) );
			}
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
	 * Build navigation data based on context
	 *
	 * @param \WP_Post $post Post object
	 * @param string   $context Response context
	 * @return array Navigation data
	 */
	private function build_navigation_data( \WP_Post $post, string $context ): array {
		// Base data available in all contexts
		$data = array(
			'id'           => $post->ID,
			'date'         => $post->post_date,
			'date_gmt'     => $post->post_date_gmt,
			'guid'         => array(
				'rendered' => $post->guid,
				'raw'      => $post->guid,
			),
			'modified'     => $post->post_modified,
			'modified_gmt' => $post->post_modified_gmt,
			'slug'         => $post->post_name,
			'status'       => $post->post_status,
			'type'         => $post->post_type,
			'link'         => get_permalink( $post->ID ),
			'title'        => array(
				'rendered' => get_the_title( $post->ID ),
				'raw'      => $post->post_title,
			),
		);

		// Add content based on context
		if ( 'edit' === $context ) {
			$data['content']  = array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			);
			$data['template'] = get_page_template_slug( $post->ID );
		} elseif ( 'view' === $context ) {
			$data['content']  = array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
			$data['template'] = get_page_template_slug( $post->ID );
		} elseif ( 'embed' === $context ) {
			$data['content'] = array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		return $data;
	}
}
