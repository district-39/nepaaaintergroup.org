<?php
/**
 * Restore Post Revision Tool Class
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
 * Restore Post Revision Tool
 *
 * Handles the registration and execution of the restore post revision ability
 * for the MCP adapter. Restores a post to a previous revision using the
 * WordPress core wp_restore_post_revision() function. Restoring creates a
 * new revision — history is forward-only.
 */
class Restore_Post_Revision_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/restore-post-revision';

	/**
	 * Tool instance
	 *
	 * @var Restore_Post_Revision_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Restore_Post_Revision_Tool
	 */
	public static function get_instance(): Restore_Post_Revision_Tool {
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
	 * Register the restore post revision ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Restore Post Revision', 'mcp-adapter-initializer' ),
				'description'         => __( 'Restores a post to a previous revision. Creates a new revision with the restored content — history is forward-only.', 'mcp-adapter-initializer' ),
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
					'description' => __( 'The ID of the parent post', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'id'     => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the revision to restore', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
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
			__( 'Post revision restore result', 'mcp-adapter-initializer' ),
			array(
				'post_id'              => array(
					'type'        => 'integer',
					'description' => __( 'The post ID that was restored', 'mcp-adapter-initializer' ),
				),
				'restored_revision_id' => array(
					'type'        => 'integer',
					'description' => __( 'The revision ID that was restored from', 'mcp-adapter-initializer' ),
				),
				'current_revision_id'  => array(
					'type'        => 'integer',
					'description' => __( 'The new revision ID created by the restore', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the restore post revision tool
	 *
	 * @param array $input Input parameters
	 * @return array Restore result or error
	 */
	public function execute( array $input ): array {
		// Load revision functions.
		$this->load_admin_file( 'post.php' );

		// Validate required parameters.
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent post ID is required', 'mcp-adapter-initializer' ),
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

		// Check if parent post exists.
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Post with ID %d not found', 'mcp-adapter-initializer' ), $parent_id ),
			);
		}

		// Check if the post type supports revisions.
		if ( ! post_type_supports( $parent_post->post_type, 'revisions' ) ) {
			return array(
				'success' => false,
				'message' => __( 'This post type does not support revisions', 'mcp-adapter-initializer' ),
			);
		}

		// Get the revision.
		$revision = wp_get_post_revision( $revision_id );

		if ( ! $revision ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Revision with ID %d not found', 'mcp-adapter-initializer' ), $revision_id ),
			);
		}

		// Verify the revision belongs to the parent post.
		if ( (int) $revision->post_parent !== $parent_id ) {
			return array(
				'success' => false,
				'message' => __( 'Revision does not belong to the specified parent post', 'mcp-adapter-initializer' ),
			);
		}

		// Check if user has permission to edit the post.
		if ( ! current_user_can( 'edit_post', $parent_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to edit this post', 'mcp-adapter-initializer' ),
			);
		}

		// Restore the revision — this creates a new revision with the restored content.
		$restored_post_id = wp_restore_post_revision( $revision_id );

		if ( ! $restored_post_id || is_wp_error( $restored_post_id ) ) {
			$error_message = is_wp_error( $restored_post_id )
				? $restored_post_id->get_error_message()
				: __( 'Failed to restore revision', 'mcp-adapter-initializer' );
			return array(
				'success' => false,
				'message' => $error_message,
			);
		}

		// Get the latest revision after restore to return the new revision ID.
		$latest_revisions    = wp_get_post_revisions(
			$parent_id,
			array(
				'numberposts' => 1,
				'orderby'     => 'ID',
				'order'       => 'DESC',
			)
		);
		$current_revision_id = (int) array_key_first( $latest_revisions );

		return array(
			'success'              => true,
			'message'              => sprintf(
				__( 'Post %1$d restored to revision %2$d', 'mcp-adapter-initializer' ),
				$parent_id,
				$revision_id
			),
			'post_id'              => $parent_id,
			'restored_revision_id' => $revision_id,
			'current_revision_id'  => $current_revision_id,
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
