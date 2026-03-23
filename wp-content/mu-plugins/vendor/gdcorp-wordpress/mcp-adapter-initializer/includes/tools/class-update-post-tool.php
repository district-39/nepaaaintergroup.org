<?php
/**
 * Update Post Tool Class
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
 * Update Post Tool
 *
 * Handles the registration and execution of the update post ability
 * for the MCP adapter.
 */
class Update_Post_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/update-post';

	/**
	 * Tool instance
	 *
	 * @var Update_Post_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Update_Post_Tool
	 */
	public static function get_instance(): Update_Post_Tool {
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
	 * Register the update post ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Post', 'mcp-adapter-initializer' ),
				'description'         => __( 'Updates a WordPress post by its ID with new title, content, excerpt, status, and/or post meta fields', 'mcp-adapter-initializer' ),
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
				'post_id'        => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post to update', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'title'          => array(
					'type'        => 'string',
					'description' => __( 'The new title for the post', 'mcp-adapter-initializer' ),
				),
				'content'        => array(
					'type'        => 'string',
					'description' => __( 'The new content for the post', 'mcp-adapter-initializer' ),
				),
				'excerpt'        => array(
					'type'        => 'string',
					'description' => __( 'The new excerpt for the post', 'mcp-adapter-initializer' ),
				),
				'status'         => array(
					'type'        => 'string',
					'description' => __( 'The post status (publish, draft, private, etc.). Use "future" with date to schedule a post.', 'mcp-adapter-initializer' ),
					'enum'        => array( 'publish', 'draft', 'private', 'pending', 'future' ),
				),
				'date'           => array(
					'type'        => 'string',
					'description' => __( 'The date the post should be published, in the site\'s timezone. Format: YYYY-MM-DD HH:MM:SS. Required for scheduling (status=future).', 'mcp-adapter-initializer' ),
				),
				'meta'           => array(
					'type'        => 'array',
					'description' => __( 'Array of meta fields to update', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'key'   => array(
								'type'        => 'string',
								'description' => __( 'Meta key', 'mcp-adapter-initializer' ),
							),
							'value' => array(
								'type'        => array( 'string', 'array' ),
								'description' => __( 'Meta value (string or array)', 'mcp-adapter-initializer' ),
							),
						),
						'required'   => array( 'key' ),
					),
				),
				'featured_media' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to use as featured image', 'mcp-adapter-initializer' ),
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
			__( 'Post update result', 'mcp-adapter-initializer' ),
			array(
				'post_id'        => array(
					'type'        => 'integer',
					'description' => __( 'The updated post ID', 'mcp-adapter-initializer' ),
				),
				'updated_fields' => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'List of fields that were updated', 'mcp-adapter-initializer' ),
				),
				'updated_meta'   => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'List of meta keys that were updated', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the update post tool
	 *
	 * @param array $input Input parameters
	 * @return array Update result or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['post_id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Post ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$post_id = (int) $input['post_id'];

		// Check if post exists
		$existing_post = get_post( $post_id );
		if ( ! $existing_post ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Post with ID %d not found', 'mcp-adapter-initializer' ), $post_id ),
			);
		}

		// Prepare update data
		$update_data = array(
			'ID' => $post_id,
		);

		$updated_fields = array();

		// Add fields to update if provided
		if ( isset( $input['title'] ) && ! empty( $input['title'] ) ) {
			$update_data['post_title'] = sanitize_text_field( $input['title'] );
			$updated_fields[]          = 'title';
		}

		if ( isset( $input['content'] ) ) {
			$update_data['post_content'] = wp_kses_post( $input['content'] );
			$updated_fields[]            = 'content';
		}

		if ( isset( $input['excerpt'] ) ) {
			$update_data['post_excerpt'] = sanitize_textarea_field( $input['excerpt'] );
			$updated_fields[]            = 'excerpt';
		}

		if ( isset( $input['status'] ) && ! empty( $input['status'] ) ) {
			$allowed_statuses = array( 'publish', 'draft', 'private', 'pending', 'future' );
			if ( in_array( $input['status'], $allowed_statuses, true ) ) {
				$update_data['post_status'] = $input['status'];
				$updated_fields[]           = 'status';
			}
		}

		// Validate that date is provided when scheduling.
		if ( isset( $input['status'] ) && 'future' === $input['status'] && empty( $input['date'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Date is required when scheduling a post (status=future)', 'mcp-adapter-initializer' ),
			);
		}

		// Handle scheduling date field.
		if ( ! empty( $input['date'] ) ) {
			$scheduled_date_raw  = sanitize_text_field( $input['date'] );
			$scheduled_timestamp = strtotime( $scheduled_date_raw );

			if ( false === $scheduled_timestamp ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid date format provided for scheduling', 'mcp-adapter-initializer' ),
				);
			}

			// Validate date is in the future when scheduling.
			if ( isset( $input['status'] ) && 'future' === $input['status'] && $scheduled_timestamp <= time() ) {
				return array(
					'success' => false,
					'message' => __( 'Scheduled date must be in the future', 'mcp-adapter-initializer' ),
				);
			}

			// Use the sanitized input directly - WordPress expects post_date in site timezone.
			$update_data['post_date'] = $scheduled_date_raw;
			$updated_fields[]         = 'date';
		}

		// Check if we have at least some data to potentially update (fields, meta, or featured image)
		$has_update_data = ! empty( $updated_fields )
			|| ( isset( $input['meta'] ) && is_array( $input['meta'] ) && ! empty( $input['meta'] ) )
			|| ! empty( $input['featured_media'] );

		// If no fields to update, do not perform any updates and return success with no changes
		if ( ! $has_update_data ) {
			// No updates requested, but that's okay - just return success with no changes
			return array(
				'success'        => true,
				'post_id'        => $post_id,
				'message'        => __( 'No updates requested', 'mcp-adapter-initializer' ),
				'updated_fields' => array(),
				'updated_meta'   => array(),
			);
		}

		// Perform the update
		$result = wp_update_post( $update_data, true );

		// Check for errors
		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to update post: %s', 'mcp-adapter-initializer' ), $result->get_error_message() ),
			);
		}

		// Update meta fields if provided
		$updated_meta = array();

		if ( ! empty( $input['meta'] ) && is_array( $input['meta'] ) ) {

			foreach ( $input['meta'] as $meta_item ) {

				if ( ! is_array( $meta_item ) || empty( $meta_item['key'] ) ) {
					continue;
				}

				$meta_key = sanitize_text_field( $meta_item['key'] );

				// Skip empty keys after sanitization
				if ( empty( $meta_key ) ) {
					continue;
				}

				// Handle meta value - can be string or array
				$meta_value = isset( $meta_item['value'] ) ? $meta_item['value'] : '';

				// Sanitize based on value type
				if ( is_array( $meta_value ) ) {
					// Only flat rays are currently supported.
					$meta_value = array_map( 'sanitize_text_field', $meta_value );
				} else {
					$meta_value = sanitize_text_field( $meta_value );
				}

				// Update the meta
				$meta_result = update_post_meta( $post_id, $meta_key, $meta_value );

				// Only add to updated_meta if the meta was actually added or updated (not unchanged)
				if ( false !== $meta_result ) {
					$updated_meta[] = $meta_key;
				}
			}
		}

		// Set featured image if provided.
		$media_id = (int) ( $input['featured_media'] ?? 0 );
		if ( $media_id > 0 ) {
			set_post_thumbnail( $post_id, $media_id );
			$updated_fields[] = 'featured_media';
		}

		return array(
			'success'        => true,
			'post_id'        => $post_id,
			'message'        => __( 'Post updated successfully', 'mcp-adapter-initializer' ),
			'updated_fields' => $updated_fields,
			'updated_meta'   => $updated_meta,
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
