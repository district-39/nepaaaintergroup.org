<?php
/**
 * List Navigation Revisions Tool
 *
 * Retrieves a list of revisions for a specific navigation post
 *
 * @package MCP_Adapter_Initializer
 * @subpackage Tools
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * List Navigation Revisions Tool Class
 *
 * Provides functionality to list navigation revisions with filtering,
 * sorting, and pagination options similar to WordPress REST API.
 */
class List_Navigation_Revisions_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/list-navigation-revisions';

	/**
	 * Singleton instance
	 *
	 * @var List_Navigation_Revisions_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Navigation_Revisions_Tool
	 */
	public static function get_instance(): List_Navigation_Revisions_Tool {
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
	 * Register the list navigation revisions ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Navigation Revisions', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of revisions for a specific navigation post with filtering and pagination options', 'mcp-adapter-initializer' ),
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
				'parent'   => array(
					'type'        => 'integer',
					'description' => __( 'The ID for the parent navigation post', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'context'  => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'page'     => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'mcp-adapter-initializer' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page' => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to be returned in result set', 'mcp-adapter-initializer' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'search'   => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'mcp-adapter-initializer' ),
				),
				'exclude'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes specific revision IDs', 'mcp-adapter-initializer' ),
				),
				'include'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to specific revision IDs', 'mcp-adapter-initializer' ),
				),
				'offset'   => array(
					'type'        => 'integer',
					'description' => __( 'Offset the result set by a specific number of items', 'mcp-adapter-initializer' ),
					'minimum'     => 0,
				),
				'order'    => array(
					'type'        => 'string',
					'description' => __( 'Order sort attribute ascending or descending', 'mcp-adapter-initializer' ),
					'enum'        => array( 'asc', 'desc' ),
					'default'     => 'desc',
				),
				'orderby'  => array(
					'type'        => 'string',
					'description' => __( 'Sort collection by object attribute', 'mcp-adapter-initializer' ),
					'enum'        => array( 'date', 'id', 'include', 'relevance', 'slug', 'include_slugs', 'title' ),
					'default'     => 'date',
				),
			),
			'required'   => array( 'parent' ),
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
				'revisions'   => array(
					'type'        => 'array',
					'description' => __( 'Array of revision objects', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'           => array(
								'type'        => 'integer',
								'description' => __( 'Unique identifier for the revision', 'mcp-adapter-initializer' ),
							),
							'author'       => array(
								'type'        => 'integer',
								'description' => __( 'The ID for the author of the revision', 'mcp-adapter-initializer' ),
							),
							'date'         => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was published, in the site\'s timezone', 'mcp-adapter-initializer' ),
							),
							'date_gmt'     => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was published, as GMT', 'mcp-adapter-initializer' ),
							),
							'guid'         => array(
								'type'        => 'object',
								'description' => __( 'The globally unique identifier for the post', 'mcp-adapter-initializer' ),
							),
							'modified'     => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was last modified, in the site\'s timezone', 'mcp-adapter-initializer' ),
							),
							'modified_gmt' => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was last modified, as GMT', 'mcp-adapter-initializer' ),
							),
							'parent'       => array(
								'type'        => 'integer',
								'description' => __( 'The ID for the parent of the revision', 'mcp-adapter-initializer' ),
							),
							'slug'         => array(
								'type'        => 'string',
								'description' => __( 'An alphanumeric identifier for the revision unique to its type', 'mcp-adapter-initializer' ),
							),
							'title'        => array(
								'type'        => 'object',
								'description' => __( 'The title for the post', 'mcp-adapter-initializer' ),
							),
							'content'      => array(
								'type'        => 'object',
								'description' => __( 'The content for the post', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of revisions', 'mcp-adapter-initializer' ),
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
	 * Execute the list navigation revisions tool
	 *
	 * @param array $input Input parameters
	 * @return array List of revisions or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent navigation ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$parent_id = (int) $input['parent'];

		// Check if parent navigation exists
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post || 'wp_navigation' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Navigation with ID %d not found', 'mcp-adapter-initializer' ), $parent_id ),
			);
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $parent_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to view revisions for this navigation', 'mcp-adapter-initializer' ),
			);
		}

		// Build query arguments
		$args = $this->build_query_args( $input, $parent_id );

		// Get revisions
		$revisions_query = new \WP_Query( $args );

		// Build response
		$revisions = array();
		$context   = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $revisions_query->have_posts() ) {
			while ( $revisions_query->have_posts() ) {
				$revisions_query->the_post();
				$revision = get_post();

				$revisions[] = $this->build_revision_data( $revision, $context );
			}
			wp_reset_postdata();
		}

		return array(
			'revisions'   => $revisions,
			'total'       => (int) $revisions_query->found_posts,
			'total_pages' => (int) $revisions_query->max_num_pages,
			'page'        => (int) ( isset( $input['page'] ) ? $input['page'] : 1 ),
			'per_page'    => (int) ( isset( $input['per_page'] ) ? $input['per_page'] : 10 ),
		);
	}

	/**
	 * Build query arguments from input
	 *
	 * @param array $input Input parameters
	 * @param int   $parent_id Parent navigation ID
	 * @return array WP_Query arguments
	 */
	private function build_query_args( array $input, int $parent_id ): array {
		$args = array(
			'post_type'      => 'revision',
			'post_parent'    => $parent_id,
			'post_status'    => 'inherit',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
			'orderby'        => isset( $input['orderby'] ) ? $input['orderby'] : 'date',
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
		);

		// Search parameter
		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
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

		return $args;
	}

	/**
	 * Build revision data based on context
	 *
	 * @param \WP_Post $revision Revision post object
	 * @param string   $context Response context
	 * @return array Revision data
	 */
	private function build_revision_data( \WP_Post $revision, string $context ): array {
		// Base data available in all contexts
		$data = array(
			'id'           => $revision->ID,
			'author'       => (int) $revision->post_author,
			'date'         => $revision->post_date,
			'date_gmt'     => $revision->post_date_gmt,
			'guid'         => array(
				'rendered' => $revision->guid,
			),
			'modified'     => $revision->post_modified,
			'modified_gmt' => $revision->post_modified_gmt,
			'parent'       => (int) $revision->post_parent,
			'slug'         => $revision->post_name,
			'title'        => array(
				'rendered' => get_the_title( $revision->ID ),
			),
		);

		// Add content based on context
		if ( 'edit' === $context ) {
			$data['title']['raw'] = $revision->post_title;
			$data['content']      = array(
				'rendered' => apply_filters( 'the_content', $revision->post_content ),
				'raw'      => $revision->post_content,
			);
		} elseif ( 'view' === $context ) {
			$data['content'] = array(
				'rendered' => apply_filters( 'the_content', $revision->post_content ),
			);
		} elseif ( 'embed' === $context ) {
			$data['content'] = array(
				'rendered' => apply_filters( 'the_content', $revision->post_content ),
			);
		}

		return $data;
	}
}
