<?php
/**
 * Activate Theme Tool Class
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
 * Activate Theme Tool
 *
 * Handles the registration and execution of the activate theme ability
 * for the MCP adapter.
 */
class Activate_Theme_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/activate-theme';

	/**
	 * Tool instance
	 *
	 * @var Activate_Theme_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Activate_Theme_Tool
	 */
	public static function get_instance(): Activate_Theme_Tool {
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
	 * Register the activate theme ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Activate Theme', 'mcp-adapter-initializer' ),
				'description'         => __( 'Installs and activates a WordPress theme from the WordPress.org repository by theme slug, or activates an already installed theme', 'mcp-adapter-initializer' ),
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
				'theme_slug' => array(
					'type'        => 'string',
					'description' => __( 'The theme slug from WordPress.org (e.g., "twentytwentyfour", "astra")', 'mcp-adapter-initializer' ),
					'minLength'   => 1,
				),
			),
			'required'   => array( 'theme_slug' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Theme activation result', 'mcp-adapter-initializer' ),
			array(
				'theme'          => array(
					'type'        => 'string',
					'description' => __( 'The theme slug that was processed', 'mcp-adapter-initializer' ),
				),
				'version'        => array(
					'type'        => 'string',
					'description' => __( 'The version of the theme that was activated (if successful)', 'mcp-adapter-initializer' ),
				),
				'previous_theme' => array(
					'type'        => 'string',
					'description' => __( 'The theme that was previously active', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the activate theme tool
	 *
	 * @param array $input Input parameters
	 * @return array Theme activation result
	 */
	public function execute( array $input ): array {
		// Validate input
		if ( empty( $input['theme_slug'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Theme slug is required', 'mcp-adapter-initializer' ),
				'theme'   => '',
			);
		}

		$theme_slug = \sanitize_text_field( $input['theme_slug'] );

		// Get current active theme
		$previous_theme = \get_stylesheet();

		// Check if theme already exists
		$theme = \wp_get_theme( $theme_slug );

		// If theme exists and is already active
		if ( $theme->exists() && $theme_slug === $previous_theme ) {
			return array(
				'success'        => true,
				'message'        => __( 'Theme is already active', 'mcp-adapter-initializer' ),
				'theme'          => $theme_slug,
				'version'        => $theme->get( 'Version' ),
				'previous_theme' => $previous_theme,
			);
		}

		// If theme exists but is not active, just activate it
		if ( $theme->exists() ) {
			// Check if user has permission to switch themes
			if ( ! \current_user_can( 'switch_themes' ) ) {
				return array(
					'success' => false,
					'message' => __( 'You do not have permission to switch themes', 'mcp-adapter-initializer' ),
					'theme'   => $theme_slug,
				);
			}

			\switch_theme( $theme_slug );

			// Verify the theme was activated
			if ( \get_stylesheet() !== $theme_slug ) {
				return array(
					'success' => false,
					'message' => __( 'Failed to activate theme', 'mcp-adapter-initializer' ),
					'theme'   => $theme_slug,
				);
			}

			return array(
				'success'        => true,
				'message'        => __( 'Theme activated successfully', 'mcp-adapter-initializer' ),
				'theme'          => $theme_slug,
				'version'        => (string) $theme->get( 'Version' ),
				'previous_theme' => $previous_theme,
			);
		}

		// Theme doesn't exist, so install it from WordPress.org
		// Check if user has permission to install themes
		if ( ! \current_user_can( 'install_themes' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to install themes', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		// Include necessary WordPress functions
		// Load in correct order - file.php first as it has base dependencies
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			$this->load_admin_file( 'file.php' );
		}

		// Load admin functions
		if ( ! function_exists( 'get_plugins' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		// Load theme.php which contains themes_api() and switch_theme()
		if ( ! function_exists( 'themes_api' ) ) {
			$this->load_admin_file( 'theme.php' );
		}

		if ( ! class_exists( 'Theme_Upgrader' ) ) {
			$this->load_admin_file( 'class-wp-upgrader.php' );
		}

		// Query WordPress.org for theme info
		$api = \themes_api(
			'theme_information',
			array(
				'slug'   => $theme_slug,
				'fields' => array(
					'sections'                 => false,
					'tags'                     => false,
					'reviews'                  => false,
					'downloaded'               => false,
					'last_updated'             => false,
					'homepage'                 => false,
					'screenshot_url'           => false,
					'screenshots'              => false,
					'active_installs'          => false,
					'parent'                   => false,
					'requires'                 => false,
					'requires_php'             => false,
					'rating'                   => false,
					'ratings'                  => false,
					'num_ratings'              => false,
					'support_threads'          => false,
					'support_threads_resolved' => false,
				),
			)
		);

		if ( \is_wp_error( $api ) ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: 1: theme slug, 2: error message */
					__( 'Theme "%1$s" not found on WordPress.org: %2$s', 'mcp-adapter-initializer' ),
					$theme_slug,
					$api->get_error_message()
				),
				'theme'   => $theme_slug,
			);
		}

		// Initialize filesystem for theme installation
		\add_filter(
			'filesystem_method',
			function () {
				return 'direct';
			}
		);
		if ( ! \WP_Filesystem() ) {
			return array(
				'success' => false,
				'message' => __( 'Could not initialize filesystem', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		// Install the theme using Automatic_Upgrader_Skin for non-interactive context
		$upgrader = new \Theme_Upgrader( new \Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( \is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Failed to install theme: %s', 'mcp-adapter-initializer' ),
					$result->get_error_message()
				),
				'theme'   => $theme_slug,
			);
		}

		if ( true !== $result ) {
			return array(
				'success' => false,
				'message' => __( 'Theme installation failed', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		// Verify theme was installed
		$theme = \wp_get_theme( $theme_slug );
		if ( ! $theme->exists() ) {
			return array(
				'success' => false,
				'message' => __( 'Theme installed but could not be located for activation', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		// Activate the theme
		\switch_theme( $theme_slug );

		// Verify the theme was activated
		if ( \get_stylesheet() !== $theme_slug ) {
			return array(
				'success' => false,
				'message' => __( 'Theme installed but failed to activate', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		return array(
			'success'        => true,
			'message'        => __( 'Theme installed and activated successfully', 'mcp-adapter-initializer' ),
			'theme'          => $theme_slug,
			'version'        => $theme->get( 'Version' ),
			'previous_theme' => $previous_theme,
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
