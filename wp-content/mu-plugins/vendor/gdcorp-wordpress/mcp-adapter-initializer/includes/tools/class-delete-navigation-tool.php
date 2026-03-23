<?php
/**
 * Delete Navigation Tool
 *
 * Deletes a navigation post (trash or permanently)
 *
 * @package MCP_Adapter_Initializer
 * @subpackage Tools
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Delete Navigation Tool Class
 *
 * Provides functionality to delete navigation posts, either by moving them
 * to trash or permanently deleting them.
 */
class Delete_Navigation_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/delete-navigation';

	/**
	 * Singleton instance
	 *
	 * @var Delete_Navigation_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Delete_Navigation_Tool
	 */
	public static function get_instance(): Delete_Navigation_Tool {
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
	 * Register the delete navigation ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Navigation', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deletes a navigation post (trash or permanently)', 'mcp-adapter-initializer' ),
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
				'id'    => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the post', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'force' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to bypass Trash and force deletion', 'mcp-adapter-initializer' ),
					'default'     => false,
				),
			),
			'required'   => array( 'id' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Navigation deletion result', 'mcp-adapter-initializer' ),
			array(
				'deleted'  => array(
					'type'        => 'object',
					'description' => __( 'Information about the deleted navigation', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'     => array(
							'type'        => 'integer',
							'description' => __( 'The deleted navigation ID', 'mcp-adapter-initializer' ),
						),
						'status' => array(
							'type'        => 'string',
							'description' => __( 'The previous status of the navigation', 'mcp-adapter-initializer' ),
						),
					),
				),
				'previous' => array(
					'type'        => 'object',
					'description' => __( 'The navigation data before deletion', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'      => array(
							'type'        => 'integer',
							'description' => __( 'Unique identifier for the post', 'mcp-adapter-initializer' ),
						),
						'date'    => array(
							'type'        => 'string',
							'description' => __( 'The date the post was published', 'mcp-adapter-initializer' ),
						),
						'slug'    => array(
							'type'        => 'string',
							'description' => __( 'The post slug', 'mcp-adapter-initializer' ),
						),
						'status'  => array(
							'type'        => 'string',
							'description' => __( 'The post status', 'mcp-adapter-initializer' ),
						),
						'type'    => array(
							'type'        => 'string',
							'description' => __( 'Type of post', 'mcp-adapter-initializer' ),
						),
						'title'   => array(
							'type'        => 'object',
							'description' => __( 'The title for the post', 'mcp-adapter-initializer' ),
						),
						'content' => array(
							'type'        => 'object',
							'description' => __( 'The content for the post', 'mcp-adapter-initializer' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the delete navigation tool
	 *
	 * @param array $input Input parameters
	 * @return array Deletion result or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Navigation ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$navigation_id = (int) $input['id'];
		$force_delete  = ! empty( $input['force'] );

		// Check if navigation exists
		$post = get_post( $navigation_id );
		if ( ! $post || 'wp_navigation' !== $post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Navigation with ID %d not found', 'mcp-adapter-initializer' ), $navigation_id ),
			);
		}

		// Check permissions
		if ( ! current_user_can( 'delete_post', $navigation_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to delete this navigation', 'mcp-adapter-initializer' ),
			);
		}

		// Store previous data before deletion
		$previous = array(
			'id'      => $post->ID,
			'date'    => $post->post_date,
			'slug'    => $post->post_name,
			'status'  => $post->post_status,
			'type'    => $post->post_type,
			'title'   => array(
				'rendered' => get_the_title( $post->ID ),
				'raw'      => $post->post_title,
			),
			'content' => array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			),
		);

		// Perform deletion
		$result = wp_delete_post( $navigation_id, $force_delete );

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete navigation', 'mcp-adapter-initializer' ),
			);
		}

		$message = $force_delete
			? __( 'Navigation permanently deleted', 'mcp-adapter-initializer' )
			: __( 'Navigation moved to trash', 'mcp-adapter-initializer' );

		return array(
			'success'  => true,
			'message'  => $message,
			'deleted'  => array(
				'id'     => $navigation_id,
				'status' => $post->post_status,
			),
			'previous' => $previous,
		);
	}
}
