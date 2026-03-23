<?php
/**
 * Delete Post Tool Class
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
 * Delete Post Tool
 *
 * Handles the registration and execution of the delete post ability
 * for the MCP adapter.
 */
class Delete_Post_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/delete-post';

	/**
	 * Tool instance
	 *
	 * @var Delete_Post_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Delete_Post_Tool
	 */
	public static function get_instance(): Delete_Post_Tool {
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
	 * Register the delete post ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Post', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deletes a WordPress post or page by its ID. Can move to trash or permanently delete.', 'mcp-adapter-initializer' ),
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
				'post_id'      => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post to delete', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'force_delete' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to permanently delete the post (true) or move it to trash (false). Defaults to true.', 'mcp-adapter-initializer' ),
					'default'     => true,
				),
			),
			'required'   => array( 'post_id' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Post deletion result', 'mcp-adapter-initializer' ),
			array(
				'post_id'             => array(
					'type'        => 'integer',
					'description' => __( 'The deleted post ID', 'mcp-adapter-initializer' ),
				),
				'deleted_permanently' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the post was permanently deleted (true) or moved to trash (false)', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the delete post tool
	 *
	 * @param array $input Input parameters
	 * @return array Deletion result or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['post_id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Post ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$post_id      = (int) $input['post_id'];
		$force_delete = isset( $input['force_delete'] ) ? (bool) $input['force_delete'] : true;

		// Check if post exists
		$existing_post = get_post( $post_id );
		if ( ! $existing_post ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Post with ID %d not found', 'mcp-adapter-initializer' ), $post_id ),
			);
		}

		// Check if post is already in trash when trying to trash it
		if ( ! $force_delete && 'trash' === $existing_post->post_status ) {
			return array(
				'success' => false,
				'message' => __( 'Post is already in trash. Use force_delete to permanently delete it.', 'mcp-adapter-initializer' ),
			);
		}

		// Check if user has permission to delete this post
		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to delete this post', 'mcp-adapter-initializer' ),
			);
		}

		// Perform the deletion
		$result = wp_delete_post( $post_id, $force_delete );

		// Check for errors - wp_delete_post returns false on failure, WP_Post object or null on success
		if ( false === $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete post. The post may not exist or there was an error.', 'mcp-adapter-initializer' ),
			);
		}

		// Success - determine if it was trashed or permanently deleted
		$message = $force_delete
			? __( 'Post permanently deleted successfully', 'mcp-adapter-initializer' )
			: __( 'Post moved to trash successfully', 'mcp-adapter-initializer' );

		return array(
			'success'             => true,
			'post_id'             => $post_id,
			'message'             => $message,
			'deleted_permanently' => $force_delete,
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
