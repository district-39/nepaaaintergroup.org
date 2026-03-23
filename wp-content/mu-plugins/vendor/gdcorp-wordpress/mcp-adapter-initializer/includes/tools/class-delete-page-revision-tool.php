<?php
/**
 * Delete Page Revision Tool Class
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
 * Delete Page Revision Tool
 *
 * Handles the registration and execution of the delete page revision ability
 * for the MCP adapter. Provides functionality similar to the WordPress
 * REST API DELETE /wp/v2/pages/<parent>/revisions/<id> endpoint.
 */
class Delete_Page_Revision_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/delete-page-revision';

	/**
	 * Tool instance
	 *
	 * @var Delete_Page_Revision_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Delete_Page_Revision_Tool
	 */
	public static function get_instance(): Delete_Page_Revision_Tool {
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
	 * Register the delete page revision ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Page Revision', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deletes a specific page revision by ID. Revisions are permanently deleted and do not support trash.', 'mcp-adapter-initializer' ),
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
				'parent' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the parent page for the revision', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'id'     => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the revision to delete', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'force'  => array(
					'type'        => 'boolean',
					'description' => __( 'Required to be true, as revisions do not support trashing', 'mcp-adapter-initializer' ),
					'default'     => true,
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
		return $this->build_output_schema(
			__( 'Page revision deletion result', 'mcp-adapter-initializer' ),
			array(
				'revision_id' => array(
					'type'        => 'integer',
					'description' => __( 'The deleted revision ID', 'mcp-adapter-initializer' ),
				),
				'deleted'     => array(
					'type'        => 'object',
					'description' => __( 'The deleted revision data', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'           => array(
							'type'        => 'integer',
							'description' => __( 'The revision ID', 'mcp-adapter-initializer' ),
						),
						'parent_id'    => array(
							'type'        => 'integer',
							'description' => __( 'The parent page ID', 'mcp-adapter-initializer' ),
						),
						'author_id'    => array(
							'type'        => 'integer',
							'description' => __( 'The revision author ID', 'mcp-adapter-initializer' ),
						),
						'date_created' => array(
							'type'        => 'string',
							'description' => __( 'The revision creation date', 'mcp-adapter-initializer' ),
						),
						'title'        => array(
							'type'        => 'string',
							'description' => __( 'The revision title', 'mcp-adapter-initializer' ),
						),
						'content'      => array(
							'type'        => 'string',
							'description' => __( 'The revision content', 'mcp-adapter-initializer' ),
						),
						'slug'         => array(
							'type'        => 'string',
							'description' => __( 'The revision slug', 'mcp-adapter-initializer' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the delete page revision tool
	 *
	 * @param array $input Input parameters
	 * @return array Deletion result or error
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

		// Get the revision before deleting
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

		// Check if user has permission to delete revisions
		if ( ! current_user_can( 'delete_post', $parent_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to delete revisions for this page', 'mcp-adapter-initializer' ),
			);
		}

		// Store revision data before deletion
		$deleted_data = array(
			'id'            => $revision->ID,
			'author_id'     => (int) $revision->post_author,
			'date_created'  => $revision->post_date,
			'date_modified' => $revision->post_modified,
			'parent_id'     => (int) $revision->post_parent,
			'title'         => $revision->post_title,
		);

		// Delete the revision (revisions are always permanently deleted)
		$result = wp_delete_post_revision( $revision_id );

		if ( false === $result || is_wp_error( $result ) ) {
			$error_message = is_wp_error( $result ) ? $result->get_error_message() : __( 'Failed to delete revision', 'mcp-adapter-initializer' );
			return array(
				'success' => false,
				'message' => $error_message,
			);
		}

		return array(
			'success'     => true,
			'revision_id' => $revision_id,
			'message'     => __( 'Revision deleted successfully', 'mcp-adapter-initializer' ),
			'deleted'     => $deleted_data,
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
