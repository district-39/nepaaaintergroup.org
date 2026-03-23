<?php
/**
 * List Plugins Tool Class
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
 * List Plugins Tool
 *
 * Handles the registration and execution of the list plugins ability
 * for the MCP adapter. Provides functionality similar to the WordPress
 * REST API /wp/v2/plugins endpoint.
 */
class List_Plugins_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/list-plugins';

	/**
	 * Tool instance
	 *
	 * @var List_Plugins_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Plugins_Tool
	 */
	public static function get_instance(): List_Plugins_Tool {
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
	 * Register the list plugins ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Plugins', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of installed WordPress plugins with their status and information', 'mcp-adapter-initializer' ),
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
				'context' => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'search'  => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'mcp-adapter-initializer' ),
				),
				'status'  => array(
					'type'        => 'string',
					'description' => __( 'Limits results to plugins with the given status', 'mcp-adapter-initializer' ),
					'enum'        => array( 'inactive', 'active' ),
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
			__( 'Plugin list result', 'mcp-adapter-initializer' ),
			array(
				'plugins' => array(
					'type'        => 'array',
					'description' => __( 'List of plugins', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'plugin'       => array(
								'type'        => 'string',
								'description' => __( 'The plugin file', 'mcp-adapter-initializer' ),
							),
							'status'       => array(
								'type'        => 'string',
								'description' => __( 'The plugin activation status', 'mcp-adapter-initializer' ),
							),
							'name'         => array(
								'type'        => 'string',
								'description' => __( 'The plugin name', 'mcp-adapter-initializer' ),
							),
							'plugin_uri'   => array(
								'type'        => 'string',
								'description' => __( 'The plugin\'s website address', 'mcp-adapter-initializer' ),
							),
							'author'       => array(
								'type'        => 'string',
								'description' => __( 'The plugin author', 'mcp-adapter-initializer' ),
							),
							'author_uri'   => array(
								'type'        => 'string',
								'description' => __( 'Plugin author\'s website address', 'mcp-adapter-initializer' ),
							),
							'description'  => array(
								'type'        => 'string',
								'description' => __( 'The plugin description', 'mcp-adapter-initializer' ),
							),
							'version'      => array(
								'type'        => 'string',
								'description' => __( 'The plugin version number', 'mcp-adapter-initializer' ),
							),
							'network_only' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether the plugin can only be activated network-wide', 'mcp-adapter-initializer' ),
							),
							'requires_wp'  => array(
								'type'        => 'string',
								'description' => __( 'Minimum required version of WordPress', 'mcp-adapter-initializer' ),
							),
							'requires_php' => array(
								'type'        => 'string',
								'description' => __( 'Minimum required version of PHP', 'mcp-adapter-initializer' ),
							),
							'textdomain'   => array(
								'type'        => 'string',
								'description' => __( 'The plugin\'s text domain', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
				'total'   => array(
					'type'        => 'integer',
					'description' => __( 'Total number of plugins matching the criteria', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the list plugins tool
	 *
	 * @param array $input Input parameters
	 * @return array List of plugins
	 */
	public function execute( array $input ): array {
		// Check if user has permission to view plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to view plugins', 'mcp-adapter-initializer' ),
				'plugins' => array(),
				'total'   => 0,
			);
		}

		// Include necessary WordPress functions
		if ( ! function_exists( 'get_plugins' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		// Get all plugins
		$all_plugins = get_plugins();
		$plugins     = array();

		// Get active plugins
		$active_plugins = get_option( 'active_plugins', array() );

		// Process each plugin
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$is_active = in_array( $plugin_file, $active_plugins, true );
			$status    = $is_active ? 'active' : 'inactive';

			// Apply status filter if provided
			if ( ! empty( $input['status'] ) && $input['status'] !== $status ) {
				continue;
			}

			// Apply search filter if provided
			if ( ! empty( $input['search'] ) ) {
				$search = strtolower( $input['search'] );
				$name   = strtolower( $plugin_data['Name'] );
				$desc   = strtolower( $plugin_data['Description'] );

				if ( strpos( $name, $search ) === false && strpos( $desc, $search ) === false ) {
					continue;
				}
			}

			// Build plugin information
			$plugin_info = array(
				'plugin'       => $plugin_file,
				'status'       => $status,
				'name'         => $plugin_data['Name'],
				'plugin_uri'   => $plugin_data['PluginURI'],
				'author'       => $plugin_data['AuthorName'],
				'author_uri'   => $plugin_data['AuthorURI'],
				'description'  => $plugin_data['Description'],
				'version'      => $plugin_data['Version'],
				'network_only' => $plugin_data['Network'],
				'requires_wp'  => $plugin_data['RequiresWP'],
				'requires_php' => $plugin_data['RequiresPHP'],
				'textdomain'   => $plugin_data['TextDomain'],
			);

			// Apply context filtering
			$context = ! empty( $input['context'] ) ? $input['context'] : 'view';
			if ( 'embed' === $context ) {
				// For embed context, return minimal data
				$plugin_info = array(
					'plugin' => $plugin_info['plugin'],
					'status' => $plugin_info['status'],
					'name'   => $plugin_info['name'],
				);
			}

			$plugins[] = $plugin_info;
		}

		return array(
			'success' => true,
			'plugins' => $plugins,
			'total'   => count( $plugins ),
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
