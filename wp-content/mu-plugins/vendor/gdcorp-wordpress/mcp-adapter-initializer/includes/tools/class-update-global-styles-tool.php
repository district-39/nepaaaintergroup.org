<?php
/**
 * Update Global Styles Tool Class
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
 * Update Global Styles Tool
 *
 * Handles the registration and execution of the update global styles ability
 * for the MCP adapter.
 */
class Update_Global_Styles_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/update-global-styles';

	/**
	 * Tool instance
	 *
	 * @var Update_Global_Styles_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Update_Global_Styles_Tool
	 */
	public static function get_instance(): Update_Global_Styles_Tool {
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
	 * Register the update global styles ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Global Styles', 'mcp-adapter-initializer' ),
				'description'         => __( 'Updates global styles configuration including styles, settings, and title', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'theme-management',
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
				'id'        => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the global styles template to update', 'mcp-adapter-initializer' ),
				),
				'styles'    => array(
					'type'        => 'object',
					'description' => __( 'Global styles configuration object', 'mcp-adapter-initializer' ),
				),
				'settings'  => array(
					'type'        => 'object',
					'description' => __( 'Global settings configuration object', 'mcp-adapter-initializer' ),
				),
				'title'     => array(
					'type'        => 'string',
					'description' => __( 'Title of the global styles variation', 'mcp-adapter-initializer' ),
				),
				'overwrite' => array(
					'type'        => 'boolean',
					'description' => __( 'If true, replace entire styles/settings instead of merging. Defaults to false.', 'mcp-adapter-initializer' ),
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
			__( 'Updated global styles data', 'mcp-adapter-initializer' ),
			array(
				'data' => array(
					'type'        => 'object',
					'description' => __( 'Updated global styles data', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'      => array(
							'type'        => 'integer',
							'description' => __( 'The global styles post ID', 'mcp-adapter-initializer' ),
						),
						'title'   => array(
							'type'        => 'string',
							'description' => __( 'The global styles title', 'mcp-adapter-initializer' ),
						),
						'content' => array(
							'type'        => 'object',
							'description' => __( 'The updated global styles configuration', 'mcp-adapter-initializer' ),
						),
						'status'  => array(
							'type'        => 'string',
							'description' => __( 'The post status', 'mcp-adapter-initializer' ),
						),
						'date'    => array(
							'type'        => 'string',
							'description' => __( 'The last modified date', 'mcp-adapter-initializer' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the tool
	 *
	 * @param array $input Tool input parameters.
	 * @return array
	 */
	public function execute( array $input ): array {
		try {
			// Validate required parameters
			$global_styles_id = isset( $input['id'] ) ? absint( $input['id'] ) : 0;

			if ( empty( $global_styles_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Global styles ID is required', 'mcp-adapter-initializer' ),
				);
			}

			// Get the existing global styles post
			$global_styles_post = get_post( $global_styles_id );

			if ( ! $global_styles_post || 'wp_global_styles' !== $global_styles_post->post_type ) {
				return array(
					'success' => false,
					'message' => __( 'Global styles post not found or invalid ID', 'mcp-adapter-initializer' ),
				);
			}

			// Get existing content and merge with new data
			$existing_content = array();
			if ( ! empty( $global_styles_post->post_content ) ) {
				$decoded = json_decode( $global_styles_post->post_content, true );
				if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
					$existing_content = $decoded;
				} else {
					// Log the error but continue with empty array (effectively overwrites corrupted data)
					error_log(
						sprintf(
							'Global styles post %d has corrupted JSON content. Starting fresh. Error: %s',
							$global_styles_id,
							json_last_error_msg()
						)
					);
				}
			}
			// Prepare the new content
			$new_content = $existing_content;
			$overwrite   = isset( $input['overwrite'] ) && true === $input['overwrite'];

			// Update styles if provided
			if ( isset( $input['styles'] ) && is_array( $input['styles'] ) ) {
				if ( $overwrite ) {
					// Replace entire styles section
					$new_content['styles'] = $input['styles'];
				} else {
					// Merge with existing styles
					if ( ! isset( $new_content['styles'] ) ) {
						$new_content['styles'] = array();
					}
					$new_content['styles'] = $this->deep_merge_arrays( $new_content['styles'], $input['styles'] );
				}
			}

			// Update settings if provided
			if ( isset( $input['settings'] ) && is_array( $input['settings'] ) ) {
				if ( $overwrite ) {
					// Replace entire settings section
					$new_content['settings'] = $input['settings'];
				} else {
					// Merge with existing settings
					if ( ! isset( $new_content['settings'] ) ) {
						$new_content['settings'] = array();
					}
					$new_content['settings'] = $this->deep_merge_arrays( $new_content['settings'], $input['settings'] );
				}

				// Download Google Fonts if font families are being updated
				if ( isset( $new_content['settings']['typography']['fontFamilies'] ) ) {
					$font_downloader = new Font_Downloader();

					$new_content['settings']['typography']['fontFamilies'] = $font_downloader->process_font_families(
						$new_content['settings']['typography']['fontFamilies']
					);
				}
			}

			// Prepare post data for update
			$post_data = array(
				'ID'           => $global_styles_id,
				'post_content' => wp_json_encode( $new_content ),
			);

			// Update title if provided
			if ( isset( $input['title'] ) && ! empty( $input['title'] ) ) {
				$post_data['post_title'] = sanitize_text_field( $input['title'] );
			}

			// Update the global styles post
			$update_result = wp_update_post( $post_data );

			if ( is_wp_error( $update_result ) ) {
				return array(
					'success' => false,
					'message' => sprintf(
						__( 'Failed to update global styles: %s', 'mcp-adapter-initializer' ),
						$update_result->get_error_message()
					),
				);
			}

			if ( ! $update_result ) {
				return array(
					'success' => false,
					'message' => __( 'Failed to update global styles', 'mcp-adapter-initializer' ),
				);
			}

			// Get the updated post
			$updated_post    = get_post( $update_result );
			$updated_content = ! empty( $updated_post->post_content ) ? json_decode( $updated_post->post_content, true ) : array();

			return array(
				'success' => true,
				'data'    => array(
					'id'      => (int) $updated_post->ID,
					'title'   => $updated_post->post_title,
					'content' => $updated_content,
					'status'  => $updated_post->post_status,
					'date'    => $updated_post->post_modified,
				),
				'message' => __( 'Global styles updated successfully', 'mcp-adapter-initializer' ),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Error updating global styles: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
	}

	/**
	 * Deep merge two arrays recursively with intelligent array handling
	 *
	 * @param array $existing The existing array.
	 * @param array $new_data The new array to merge.
	 * @return array
	 */
	private function deep_merge_arrays( $existing, $new_data ) {
		foreach ( $new_data as $key => $value ) {
			if ( is_array( $value ) && isset( $existing[ $key ] ) && is_array( $existing[ $key ] ) ) {
				// Check if this is a numeric-indexed array of objects/arrays
				// These should be replaced, not merged (e.g., font families, color palettes)
				if ( $this->is_list_of_objects( $value ) ) {
					// Replace the entire array - don't merge by index
					$existing[ $key ] = $value;
				} else {
					// Recursively merge associative arrays/objects
					$existing[ $key ] = $this->deep_merge_arrays( $existing[ $key ], $value );
				}
			} else {
				$existing[ $key ] = $value;
			}
		}
		return $existing;
	}

	/**
	 * Check if an array is a numeric-indexed list of objects/arrays
	 *
	 * This identifies arrays like:
	 * - Font families: [{ name: "Oswald", ... }, { name: "Quattrocento", ... }]
	 * - Color palettes: [{ color: "#FFF", ... }, { color: "#000", ... }]
	 *
	 * These should be replaced entirely, not merged by index.
	 *
	 * @param array $value The array to check.
	 * @return bool True if this is a list of objects that should be replaced.
	 */
	private function is_list_of_objects( $value ) {
		// Empty arrays are not lists of objects
		if ( empty( $value ) ) {
			return false;
		}

		// Check if array has sequential numeric keys starting from 0
		$keys = array_keys( $value );
		if ( range( 0, count( $value ) - 1 ) !== $keys ) {
			// Not a numeric-indexed array (it's associative)
			return false;
		}

		// Check if the first element is an array/object
		// If the first element is an array, assume the whole array is a list of objects
		return is_array( $value[0] );
	}
}
