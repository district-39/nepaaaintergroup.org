<?php
/**
 * Create Post Tool Class
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
 * Create Post Tool
 *
 * Handles the registration and execution of the create post ability
 * for the MCP adapter.
 */
class Create_Post_Tool extends Base_Tool {
	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/create-post';

	/**
	 * Tool instance
	 *
	 * @var Create_Post_Tool|null
	 */
	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Create_Post_Tool
	 */

	/**
		* @return self
		*/
	public static function get_instance() {
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
	 * Register the create post ability
	 *
	 * @return void
	 */
	public function register(): void {
		if ( function_exists( 'wp_register_ability' ) ) {
			wp_register_ability(
				self::TOOL_ID,
				array(
					'label'               => __( 'Create Post', 'mcp-adapter-initializer' ),
					'description'         => __( 'Creates a new WordPress post, page, or custom post type', 'mcp-adapter-initializer' ),
					'input_schema'        => $this->get_input_schema(),
					'output_schema'       => $this->get_output_schema(),
					'execute_callback'    => array( $this, 'execute_with_admin' ),
					'permission_callback' => '__return_true',
					'category'            => 'content-management',
				)
			);
		}
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
				'post_type'      => array(
					'type'        => 'string',
					'description' => __( 'The post type (post, page, or custom post type). Defaults to post.', 'mcp-adapter-initializer' ),
				),
				'title'          => array(
					'type'        => 'string',
					'description' => __( 'The title for the new post', 'mcp-adapter-initializer' ),
				),
				'content'        => array(
					'type'        => 'string',
					'description' => __( 'The content for the new post', 'mcp-adapter-initializer' ),
				),
				'excerpt'        => array(
					'type'        => 'string',
					'description' => __( 'The excerpt for the new post', 'mcp-adapter-initializer' ),
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
				'featured_media' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to use as featured image', 'mcp-adapter-initializer' ),
				),
				'meta'           => array(
					'type'        => 'array',
					'description' => __( 'Array of meta fields to set', 'mcp-adapter-initializer' ),
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
			),
			'required'   => array( 'title' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Post creation result', 'mcp-adapter-initializer' ),
			array(
				'post_id' => array(
					'type'        => 'integer',
					'description' => __( 'The new post ID', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the create post tool
	 *
	 * @param array $input Input parameters
	 * @return array Creation result or error
	 */
	public function execute( array $input ): array {
		if ( empty( $input['title'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Title is required', 'mcp-adapter-initializer' ),
			);
		}

		$post_type = isset( $input['post_type'] ) && '' !== $input['post_type'] ? sanitize_key( $input['post_type'] ) : 'post';
		if ( ! post_type_exists( $post_type ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Post type %s does not exist', 'mcp-adapter-initializer' ), $post_type ),
			);
		}

		if ( ! current_user_can( 'publish_posts' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to create posts', 'mcp-adapter-initializer' ),
			);
		}

		$post_data = array(
			'post_type'    => $post_type,
			'post_title'   => sanitize_text_field( $input['title'] ),
			'post_content' => isset( $input['content'] ) ? wp_kses_post( $input['content'] ) : '',
			'post_excerpt' => isset( $input['excerpt'] ) ? sanitize_textarea_field( $input['excerpt'] ) : '',
			'post_status'  => isset( $input['status'] ) ? $input['status'] : 'draft',
		);

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
			$post_data['post_date'] = $scheduled_date_raw;
		}

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to create post: %s', 'mcp-adapter-initializer' ), $post_id->get_error_message() ),
			);
		}

		// Create initial revision to match Block Editor behavior. Allows restore to the original content after edits.
		// wp_save_post_revision() handles all necessary checks internally
		wp_save_post_revision( $post_id );

		// Set featured image if provided.
		$media_id = (int) ( $input['featured_media'] ?? 0 );
		if ( $media_id > 0 ) {
			set_post_thumbnail( $post_id, $media_id );
		}

		// Set meta fields if provided.
		if ( ! empty( $input['meta'] ) && is_array( $input['meta'] ) ) {

			foreach ( $input['meta'] as $meta_item ) {

				if ( ! is_array( $meta_item ) || empty( $meta_item['key'] ) ) {
					continue;
				}

				$meta_key = sanitize_text_field( $meta_item['key'] );

				// Skip empty keys after sanitization.
				if ( empty( $meta_key ) ) {
					continue;
				}

				// Handle meta value - can be string or array.
				$meta_value = isset( $meta_item['value'] ) ? $meta_item['value'] : '';

				// Sanitize based on value type.
				if ( is_array( $meta_value ) ) {
					// Only flat arrays are currently supported.
					$meta_value = array_map( 'sanitize_text_field', $meta_value );
				} else {
					$meta_value = sanitize_text_field( $meta_value );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		return array(
			'success' => true,
			'post_id' => $post_id,
			'message' => __( 'Post created successfully', 'mcp-adapter-initializer' ),
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
