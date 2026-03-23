<?php
/**
 * Activate Plugin Tool Class
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
 * Activate Plugin Tool
 *
 * Handles the registration and execution of the activate plugin ability
 * for the MCP adapter.
 */
class Activate_Plugin_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/activate-plugin';

	/**
	 * Tool instance
	 *
	 * @var Activate_Plugin_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Activate_Plugin_Tool
	 */
	public static function get_instance(): Activate_Plugin_Tool {
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
	 * Register the activate plugin ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Activate Plugin', 'mcp-adapter-initializer' ),
				'description'         => __( 'Installs and activates a WordPress plugin from the WordPress.org repository by plugin slug', 'mcp-adapter-initializer' ),
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
					'description' => __( 'The plugin slug from WordPress.org (e.g., "hello-dolly", "akismet")', 'mcp-adapter-initializer' ),
					'minLength'   => 1,
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
			__( 'Plugin activation result', 'mcp-adapter-initializer' ),
			array(
				'plugin'  => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug that was processed', 'mcp-adapter-initializer' ),
				),
				'version' => array(
					'type'        => 'string',
					'description' => __( 'The version of the plugin that was installed (if successful)', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the activate plugin tool
	 *
	 * @param array $input Input parameters
	 * @return array Plugin activation result
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

		$plugin_slug = sanitize_text_field( $input['plugin_slug'] );

		// Check if user has permission to install plugins
		if ( ! current_user_can( 'install_plugins' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to install plugins', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		// Include necessary WordPress functions
		if ( ! function_exists( 'plugins_api' ) ) {
			$this->load_admin_file( 'plugin-install.php' );
		}

		if ( ! function_exists( 'download_url' ) ) {
			$this->load_admin_file( 'file.php' );
		}

		if ( ! class_exists( 'Plugin_Upgrader' ) ) {
			$this->load_admin_file( 'class-wp-upgrader.php' );
		}

		if ( ! function_exists( 'activate_plugin' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		// Check if plugin already exists and is active
		$plugin_file = $this->find_plugin_file( $plugin_slug );
		if ( $plugin_file && is_plugin_active( $plugin_file ) ) {
			return array(
				'success' => true,
				'message' => __( 'Plugin is already active', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
				'version' => $this->get_plugin_version( $plugin_file ),
			);
		}

		// If plugin exists but is not active, just activate it
		if ( $plugin_file ) {
			$activation_result = activate_plugin( $plugin_file );
			if ( is_wp_error( $activation_result ) ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Failed to activate plugin: %s', 'mcp-adapter-initializer' ), $activation_result->get_error_message() ),
					'plugin'  => $plugin_slug,
				);
			}

			return array(
				'success' => true,
				'message' => __( 'Plugin activated successfully', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
				'version' => $this->get_plugin_version( $plugin_file ),
			);
		}

		// Plugin doesn't exist, so install it from WordPress.org
		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin_slug,
				'fields' => array(
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( is_wp_error( $api ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Plugin "%1$s" not found on WordPress.org: %2$s', 'mcp-adapter-initializer' ), $plugin_slug, $api->get_error_message() ),
				'plugin'  => $plugin_slug,
			);
		}

		// Install the plugin
		$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to install plugin: %s', 'mcp-adapter-initializer' ), $result->get_error_message() ),
				'plugin'  => $plugin_slug,
			);
		}

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin installation failed for unknown reason', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		// Find the installed plugin file
		$plugin_file = $this->find_plugin_file( $plugin_slug );
		if ( ! $plugin_file ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin installed but could not be located for activation', 'mcp-adapter-initializer' ),
				'plugin'  => $plugin_slug,
			);
		}

		// Activate the plugin
		$activation_result = activate_plugin( $plugin_file );
		if ( is_wp_error( $activation_result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Plugin installed but failed to activate: %s', 'mcp-adapter-initializer' ), $activation_result->get_error_message() ),
				'plugin'  => $plugin_slug,
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Plugin installed and activated successfully', 'mcp-adapter-initializer' ),
			'plugin'  => $plugin_slug,
			'version' => $this->get_plugin_version( $plugin_file ),
		);
	}

	/**
	 * Find plugin file by slug
	 *
	 * @param string $plugin_slug Plugin slug
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
	 * Get plugin version
	 *
	 * @param string $plugin_file Plugin file path
	 * @return string Plugin version
	 */
	private function get_plugin_version( string $plugin_file ): string {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file );
		return $plugin_data['Version'] ?? '';
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
