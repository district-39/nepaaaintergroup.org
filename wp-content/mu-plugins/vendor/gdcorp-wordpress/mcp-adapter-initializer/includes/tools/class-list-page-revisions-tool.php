<?php
/**
 * List Page Revisions Tool Class
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
 * List Page Revisions Tool
 *
 * Handles the registration and execution of the list page revisions ability
 * for the MCP adapter. Provides functionality similar to the WordPress
 * REST API /wp/v2/pages/<parent>/revisions endpoint.
 */
class List_Page_Revisions_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/list-page-revisions';

	/**
	 * Tool instance
	 *
	 * @var List_Page_Revisions_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Page_Revisions_Tool
	 */
	public static function get_instance(): List_Page_Revisions_Tool {
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
	 * Register the list page revisions ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Page Revisions', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of revisions for a specific page with filtering and pagination options', 'mcp-adapter-initializer' ),
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
					'description' => __( 'The ID of the parent page for the revisions', 'mcp-adapter-initializer' ),
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
							'id'            => array(
								'type'        => 'integer',
								'description' => __( 'The revision ID', 'mcp-adapter-initializer' ),
							),
							'author_id'     => array(
								'type'        => 'integer',
								'description' => __( 'The revision author ID', 'mcp-adapter-initializer' ),
							),
							'date_created'  => array(
								'type'        => 'string',
								'description' => __( 'The revision creation date', 'mcp-adapter-initializer' ),
							),
							'date_modified' => array(
								'type'        => 'string',
								'description' => __( 'The revision modification date', 'mcp-adapter-initializer' ),
							),
							'parent_id'     => array(
								'type'        => 'integer',
								'description' => __( 'The parent page ID', 'mcp-adapter-initializer' ),
							),
							'slug'          => array(
								'type'        => 'string',
								'description' => __( 'The revision slug', 'mcp-adapter-initializer' ),
							),
							'title'         => array(
								'type'        => 'string',
								'description' => __( 'The revision title', 'mcp-adapter-initializer' ),
							),
							'content'       => array(
								'type'        => 'string',
								'description' => __( 'The revision content', 'mcp-adapter-initializer' ),
							),
							'excerpt'       => array(
								'type'        => 'string',
								'description' => __( 'The revision excerpt', 'mcp-adapter-initializer' ),
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
	 * Execute the list page revisions tool
	 *
	 * @param array $input Input parameters
	 * @return array List of revisions or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent page ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$parent_id = (int) $input['parent'];

		// Check if parent page exists
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post || 'page' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Page with ID %d not found', 'mcp-adapter-initializer' ), $parent_id ),
			);
		}

		// Get revisions
		$args = array(
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'offset'         => isset( $input['offset'] ) ? (int) $input['offset'] : 0,
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
		);

		// Add orderby if not default
		if ( isset( $input['orderby'] ) && 'date' !== $input['orderby'] ) {
			$args['orderby'] = $input['orderby'];
		}

		$revisions = wp_get_post_revisions( $parent_id, $args );

		// Process revisions
		$revision_data = array();
		$context       = isset( $input['context'] ) ? $input['context'] : 'view';

		foreach ( $revisions as $revision ) {
			// Apply include/exclude filters
			if ( ! empty( $input['exclude'] ) && in_array( $revision->ID, $input['exclude'], true ) ) {
				continue;
			}

			if ( ! empty( $input['include'] ) && ! in_array( $revision->ID, $input['include'], true ) ) {
				continue;
			}

			// Apply search filter
			if ( ! empty( $input['search'] ) ) {
				$search = strtolower( $input['search'] );
				if ( false === stripos( $revision->post_title, $search ) &&
					false === stripos( $revision->post_content, $search ) ) {
					continue;
				}
			}

			$revision_data[] = $this->format_revision( $revision, $context );
		}

		// Calculate pagination
		$total       = count( $revisions );
		$per_page    = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$total_pages = max( 1, ceil( $total / $per_page ) );

		return array(
			'revisions'   => $revision_data,
			'total'       => $total,
			'total_pages' => $total_pages,
		);
	}

	/**
	 * Format revision data based on context
	 *
	 * @param \WP_Post $revision Revision post object
	 * @param string   $context  Context (view, embed, edit)
	 * @return array Formatted revision data
	 */
	private function format_revision( \WP_Post $revision, string $context ): array {
		$data = array(
			'id'            => $revision->ID,
			'author_id'     => (int) $revision->post_author,
			'date_created'  => $revision->post_date,
			'date_modified' => $revision->post_modified,
			'parent_id'     => (int) $revision->post_parent,
			'slug'          => $revision->post_name,
		);

		// Add fields based on context
		switch ( $context ) {
			case 'embed':
				$data['title']   = $revision->post_title;
				$data['excerpt'] = wp_trim_words( $revision->post_excerpt ? $revision->post_excerpt : $revision->post_content, 55 );
				break;

			case 'edit':
				$data['title']   = $revision->post_title;
				$data['content'] = $revision->post_content;
				$data['excerpt'] = $revision->post_excerpt;
				break;

			case 'view':
			default:
				$data['title']   = $revision->post_title;
				$data['content'] = apply_filters( 'the_content', $revision->post_content );
				$data['excerpt'] = $revision->post_excerpt;
				break;
		}

		return $data;
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
