<?php
/**
 * Update Site Options Tool Class
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
 * Update Site Options Tool
 *
 * Handles the registration and execution of the update site options ability
 * for the MCP adapter.
 */
class Update_Site_Options_Tool extends Base_Tool {
	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/update-site-options';

	/**
	 * @var self|null
	 */
	private static $instance = null;

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
	 * Register the update site options ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Site Options', 'mcp-adapter-initializer' ),
				'description'         => __( 'Updates WordPress site options. Can update a single option or multiple options in one call', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'site-management',
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
				'option_name'  => array(
					'type'        => 'string',
					'description' => __( 'The name of the option to update (for single option updates)', 'mcp-adapter-initializer' ),
				),
				'option_value' => array(
					'type'        => array( 'string', 'number', 'boolean', 'array', 'object', 'null' ),
					'description' => __( 'The value for the option (for single option updates)', 'mcp-adapter-initializer' ),
				),
				'options'      => array(
					'type'        => 'array',
					'description' => __( 'Array of options to update in bulk. Each item should have option_name and option_value', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'option_name'  => array(
								'type'        => 'string',
								'description' => __( 'The name of the option', 'mcp-adapter-initializer' ),
							),
							'option_value' => array(
								'type'        => array( 'string', 'number', 'boolean', 'array', 'object', 'null' ),
								'description' => __( 'The value for the option', 'mcp-adapter-initializer' ),
							),
						),
						'required'   => array( 'option_name', 'option_value' ),
					),
				),
			),
			'anyOf'      => array(
				array(
					'required' => array( 'option_name', 'option_value' ),
				),
				array(
					'required' => array( 'options' ),
				),
			),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Site options update result', 'mcp-adapter-initializer' ),
			array(
				'updated_count' => array(
					'type'        => 'integer',
					'description' => __( 'Number of options successfully updated', 'mcp-adapter-initializer' ),
				),
				'failed_count'  => array(
					'type'        => 'integer',
					'description' => __( 'Number of options that failed to update', 'mcp-adapter-initializer' ),
				),
				'results'       => array(
					'type'        => 'array',
					'description' => __( 'Detailed results for each option update', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'option_name' => array(
								'type'        => 'string',
								'description' => __( 'The option name', 'mcp-adapter-initializer' ),
							),
							'success'     => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this specific option update was successful', 'mcp-adapter-initializer' ),
							),
							'message'     => array(
								'type'        => 'string',
								'description' => __( 'Success or error message for this option', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the update site options tool
	 *
	 * @param array $input Input parameters
	 * @return array Update result or error
	 */
	public function execute( array $input ): array {
		// Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to manage site options', 'mcp-adapter-initializer' ),
			);
		}

		$options_to_update = array();

		// Handle single option update
		if ( isset( $input['option_name'] ) && isset( $input['option_value'] ) ) {
			$options_to_update[] = array(
				'option_name'  => $input['option_name'],
				'option_value' => $input['option_value'],
			);
		}

		// Handle bulk options update
		if ( isset( $input['options'] ) && is_array( $input['options'] ) ) {
			foreach ( $input['options'] as $option ) {
				if ( isset( $option['option_name'] ) && isset( $option['option_value'] ) ) {
					$options_to_update[] = array(
						'option_name'  => $option['option_name'],
						'option_value' => $option['option_value'],
					);
				}
			}
		}

		// Validate that we have options to update
		if ( empty( $options_to_update ) ) {
			return array(
				'success' => false,
				'message' => __( 'No valid options provided to update', 'mcp-adapter-initializer' ),
			);
		}

		$results       = array();
		$updated_count = 0;
		$failed_count  = 0;

		foreach ( $options_to_update as $option_data ) {
			$option_name  = sanitize_text_field( $option_data['option_name'] );
			$option_value = $option_data['option_value'];

			// Validate option name
			if ( empty( $option_name ) ) {
				$results[] = array(
					'option_name' => $option_name,
					'success'     => false,
					'message'     => __( 'Option name cannot be empty', 'mcp-adapter-initializer' ),
				);
				++$failed_count;
				continue;
			}

			// Check if it's a protected option that shouldn't be modified
			if ( $this->is_protected_option( $option_name ) ) {
				$results[] = array(
					'option_name' => $option_name,
					'success'     => false,
					'message'     => sprintf( __( 'Option "%s" is protected and cannot be modified', 'mcp-adapter-initializer' ), $option_name ),
				);
				++$failed_count;
				continue;
			}

			// Update the option
			$update_result = update_option( $option_name, $option_value );

			if ( $update_result ) {
				$results[] = array(
					'option_name' => $option_name,
					'success'     => true,
					'message'     => sprintf( __( 'Option "%s" updated successfully', 'mcp-adapter-initializer' ), $option_name ),
				);

				++$updated_count;
			} else {
				// update_option returns false if the value is the same or if it failed
				// Check if the current value is the same as what we're trying to set
				$current_value = get_option( $option_name );
				if ( $current_value === $option_value ) {
					$results[] = array(
						'option_name' => $option_name,
						'success'     => true,
						'message'     => sprintf( __( 'Option "%s" already has the same value', 'mcp-adapter-initializer' ), $option_name ),
					);

					++$updated_count;
				} else {
					$results[] = array(
						'option_name' => $option_name,
						'success'     => false,
						'message'     => sprintf( __( 'Failed to update option "%s"', 'mcp-adapter-initializer' ), $option_name ),
					);

					++$failed_count;
				}
			}
		}

		// Generate overall message
		if ( 0 === $failed_count ) {
			$message = sprintf( __( 'Successfully updated %d option(s)', 'mcp-adapter-initializer' ), $updated_count );
		} elseif ( 0 === $updated_count ) {
			$message = sprintf( __( 'Failed to update all %d option(s)', 'mcp-adapter-initializer' ), $failed_count );
		} else {
			$message = sprintf( __( 'Updated %1$d option(s), failed to update %2$d option(s)', 'mcp-adapter-initializer' ), $updated_count, $failed_count );
		}

		return array(
			'success'       => 0 === $failed_count,
			'updated_count' => $updated_count,
			'failed_count'  => $failed_count,
			'results'       => $results,
			'message'       => $message,
		);
	}

	/**
	 * Check if an option is protected and shouldn't be modified
	 *
	 * @param string $option_name The option name to check
	 * @return bool True if the option is protected
	 */
	private function is_protected_option( string $option_name ): bool {
		$protected_options = array(
			'db_version',
			'wp_db_version',
			'secret_key',
			'auth_key',
			'secure_auth_key',
			'logged_in_key',
			'nonce_key',
			'auth_salt',
			'secure_auth_salt',
			'logged_in_salt',
			'nonce_salt',
		);

		return in_array( $option_name, $protected_options, true );
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
