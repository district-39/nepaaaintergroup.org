<?php
/**
 * Delete Media Tool Class
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
 * Delete Media Tool
 *
 * Handles the registration and execution of the delete media ability
 * for the MCP adapter.
 */
class Delete_Media_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/delete-media';

	/**
	 * Tool instance
	 *
	 * @var Delete_Media_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Delete_Media_Tool
	 */
	public static function get_instance(): Delete_Media_Tool {
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
	 * Register the delete media ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Media', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deletes a WordPress media attachment', 'mcp-adapter-initializer' ),
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
				'media_id' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to delete', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
			),
			'required'   => array( 'media_id' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Media deletion result', 'mcp-adapter-initializer' ),
			array(
				'deleted_media' => array(
					'type'        => 'object',
					'properties'  => array(
						'id'    => array(
							'type'        => 'integer',
							'description' => __( 'The deleted media attachment ID', 'mcp-adapter-initializer' ),
						),
						'title' => array(
							'type'        => 'string',
							'description' => __( 'The title of the deleted media', 'mcp-adapter-initializer' ),
						),
						'url'   => array(
							'type'        => 'string',
							'description' => __( 'The URL of the deleted media', 'mcp-adapter-initializer' ),
						),
					),
					'description' => __( 'Information about the deleted media (only present on success)', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the delete media tool
	 *
	 * @param array $input Input parameters
	 * @return array Delete result or error
	 */
	public function execute( array $input ): array {
		$media_id = ! empty( $input['media_id'] ) ? (int) $input['media_id'] : 0;

		if ( empty( $media_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'Media ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$post = get_post( $media_id );

		// Check if post exists and is an attachment
		if ( ! $post ) {
			return array(
				'success' => false,
				/* translators: %d: Media ID */
				'message' => sprintf( __( 'Media with ID %d not found', 'mcp-adapter-initializer' ), $media_id ),
			);
		}

		if ( 'attachment' !== $post->post_type ) {
			return array(
				'success' => false,
				/* translators: %d: Post ID */
				'message' => sprintf( __( 'Post with ID %d is not a media attachment', 'mcp-adapter-initializer' ), $media_id ),
			);
		}

		// Store media info before deletion
		$media_info = array(
			'id'    => $post->ID,
			'title' => $post->post_title,
			'url'   => wp_get_attachment_url( $media_id ),
		);

		// Delete the attachment (this will also delete the physical file)
		$deleted = wp_delete_attachment( $media_id, true );

		if ( false === $deleted || null === $deleted ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete media attachment', 'mcp-adapter-initializer' ),
			);
		}

		return array(
			'success'       => true,
			/* translators: %d: Media ID */
			'message'       => sprintf( __( 'Successfully deleted media with ID %d', 'mcp-adapter-initializer' ), $media_id ),
			'deleted_media' => $media_info,
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
