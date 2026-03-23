<?php
/**
 * Get Page Revision Tool Class
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
 * Get Page Revision Tool
 *
 * Handles the registration and execution of the get page revision ability
 * for the MCP adapter. Provides functionality similar to the WordPress
 * REST API /wp/v2/pages/<parent>/revisions/<id> endpoint.
 */
class Get_Page_Revision_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/get-page-revision';

	/**
	 * Tool instance
	 *
	 * @var Get_Page_Revision_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Get_Page_Revision_Tool
	 */
	public static function get_instance(): Get_Page_Revision_Tool {
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
	 * Register the get page revision ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Page Revision', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a specific page revision by ID', 'mcp-adapter-initializer' ),
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
				'parent'  => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the parent page for the revision', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'id'      => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the revision', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'context' => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
			),
			'required'   => array( 'parent', 'id' ),
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
		);
	}

	/**
	 * Execute the get page revision tool
	 *
	 * @param array $input Input parameters
	 * @return array Revision data or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent page ID is required', 'mcp-adapter-initializer' ),
			);
		}

		if ( empty( $input['id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Revision ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$parent_id   = (int) $input['parent'];
		$revision_id = (int) $input['id'];

		// Check if parent page exists
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post || 'page' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Page with ID %d not found', 'mcp-adapter-initializer' ), $parent_id ),
			);
		}

		// Get the revision
		$revision = wp_get_post_revision( $revision_id );

		if ( ! $revision ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Revision with ID %d not found', 'mcp-adapter-initializer' ), $revision_id ),
			);
		}

		// Verify the revision belongs to the parent page
		if ( (int) $revision->post_parent !== $parent_id ) {
			return array(
				'success' => false,
				'message' => __( 'Revision does not belong to the specified parent page', 'mcp-adapter-initializer' ),
			);
		}

		// Format and return revision data
		$context = isset( $input['context'] ) ? $input['context'] : 'view';
		return $this->format_revision( $revision, $context );
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
