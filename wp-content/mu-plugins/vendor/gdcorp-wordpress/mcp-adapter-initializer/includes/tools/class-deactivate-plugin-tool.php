<?php
/**
 * Deactivate Plugin Tool Class
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
 * Deactivate Plugin Tool
 *
 * Handles the registration and execution of the deactivate plugin ability
 * for the MCP adapter.
 */
class Deactivate_Plugin_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/deactivate-plugin';

	/**
	 * Tool instance
	 *
	 * @var Deactivate_Plugin_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Deactivate_Plugin_Tool
	 */
	public static function get_instance(): Deactivate_Plugin_Tool {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {
	}

	/**
	 * Register the deactivate plugin ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Deactivate Plugin', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deactivates a WordPress plugin by plugin slug, with optional uninstall', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'plugin-management',
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
				'plugin_slug' => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug to deactivate (e.g., "hello-dolly", "akismet")', 'mcp-adapter-initializer' ),
					'minLength'   => 1,
				),
				'uninstall'   => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to also uninstall the plugin after deactivation (optional, defaults to false)', 'mcp-adapter-initializer' ),
				),
			),
			'required'   => array( 'plugin_slug' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Plugin deactivation result', 'mcp-adapter-initializer' ),
			array(
				'plugin' => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug that was processed', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the deactivate plugin tool
	 *
	 * @param array $input Input parameters
	 *
	 * @return array Plugin deactivation result
	 */
	public function execute( array $input ): array {
		// Validate input
		if ( empty( $input['plugin_slug'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin slug is required', 'mcp-adapter-initializer' ),
				'plugin'  => '',
			);
		}

		$plugin_slug  = sanitize_text_field( $input['plugin_slug'] );
		$do_uninstall = ! empty( $input['uninstall'] ) && true === $input['uninstall'];

		// Check if user has permission to deactivate plugins
		if ( ! current_user_can( 'deactivate_plugins' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to deactivate plugins', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		// Include necessary WordPress functions
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		// Find the plugin file
		$plugin_file = $this->find_plugin_file( $plugin_slug );

		if ( ! $plugin_file ) {
			return array(
				'success' => true,
				'message' => __( 'Plugin is not currently installed', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		// Check if plugin is currently active
		if ( ! is_plugin_active( $plugin_file ) ) {
			return array(
				'success' => true,
				'message' => __( 'Plugin is not currently active', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		// Deactivate the plugin
		deactivate_plugins( array( $plugin_file ) );

		// Verify deactivation was successful and return only if uninstall not requested
		if ( is_plugin_active( $plugin_file ) && ! $do_uninstall ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to deactivate plugin', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		$success_message = __( 'Plugin deactivated successfully', 'mcp-adapter-initializer' );

		// Check if uninstall was requested
		if ( ! empty( $input['uninstall'] ) && true === $input['uninstall'] ) {
			$uninstall_result = $this->uninstall_plugin( $plugin_file, $plugin_slug );
			if ( $uninstall_result['success'] ) {
				$success_message .= '. ' . $uninstall_result['message'];
			} else {
				return array(
					'success' => false,
					'message' => __( 'Plugin deactivated but uninstall failed: ', 'mcp-adapter-initializer' ) . $uninstall_result['message'],
					'plugin'  => $plugin_slug,
				);
			}
		}

		return array(
			'success' => true,
			'message' => $success_message,
			'plugin'  => $plugin_slug,
		);
	}

	/**
	 * Uninstall a plugin
	 *
	 * @param string $plugin_file The plugin file path
	 * @param string $plugin_slug The plugin slug
	 *
	 * @return array Uninstall result
	 */
	private function uninstall_plugin( string $plugin_file, string $plugin_slug ): array {
		// Check if user has permission to delete plugins
		if ( ! current_user_can( 'delete_plugins' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to uninstall plugins', 'mcp-adapter-initializer' ),
			);
		}

		// Include necessary WordPress functions
		if ( ! function_exists( 'delete_plugins' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			$this->load_admin_file( 'file.php' );
		}

		// Ensure plugin is deactivated before uninstalling
		if ( is_plugin_active( $plugin_file ) ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin must be deactivated before uninstalling', 'mcp-adapter-initializer' ),
			);
		}

		// Delete the plugin
		$result = delete_plugins( array( $plugin_file ) );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to uninstall plugin: %s', 'mcp-adapter-initializer' ), $result->get_error_message() ),
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Plugin uninstalled successfully', 'mcp-adapter-initializer' ),
		);
	}

	/**
	 * Find plugin file by slug
	 *
	 * @param string $plugin_slug Plugin slug
	 *
	 * @return string|false Plugin file path or false if not found
	 */
	private function find_plugin_file( string $plugin_slug ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		$all_plugins = get_plugins();

		// First, try the most common pattern: slug/slug.php
		$common_path = $plugin_slug . '/' . $plugin_slug . '.php';
		if ( isset( $all_plugins[ $common_path ] ) ) {
			return $common_path;
		}

		// Search through all plugins to find one with matching folder
		foreach ( array_keys( $all_plugins ) as $plugin_file ) {
			$plugin_dir = dirname( $plugin_file );
			if ( $plugin_dir === $plugin_slug ) {
				return $plugin_file;
			}
		}

		return false;
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {
	}

	/**
	 * Prevent unserialization
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}
}
