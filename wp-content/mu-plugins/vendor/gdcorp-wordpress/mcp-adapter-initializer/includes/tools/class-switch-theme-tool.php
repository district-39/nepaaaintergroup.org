<?php
/**
 * Switch Theme Tool Class
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
 * Switch Theme Tool
 *
 * Handles the registration and execution of the switch theme ability
 * for the MCP adapter.
 */
class Switch_Theme_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/switch-theme';

	/**
	 * Tool instance
	 *
	 * @var Switch_Theme_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Switch_Theme_Tool
	 */
	public static function get_instance(): Switch_Theme_Tool {
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
	 * Register the switch theme ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Switch Theme', 'mcp-adapter-initializer' ),
				'description'         => __( 'Switches to a different installed theme by theme slug', 'mcp-adapter-initializer' ),
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
					'description' => __( 'The theme slug to switch to (must be already installed)', 'mcp-adapter-initializer' ),
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
			__( 'Theme switch result', 'mcp-adapter-initializer' ),
			array(
				'theme'          => array(
					'type'        => 'string',
					'description' => __( 'The theme slug that is now active', 'mcp-adapter-initializer' ),
				),
				'previous_theme' => array(
					'type'        => 'string',
					'description' => __( 'The theme that was previously active', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the switch theme tool
	 *
	 * @param array $input Input parameters
	 * @return array Theme switch result
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

		// Check if user has permission to switch themes
		if ( ! \current_user_can( 'switch_themes' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to switch themes', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		// Include necessary WordPress functions
		if ( ! function_exists( 'switch_theme' ) ) {
			$this->load_admin_file( 'theme.php' );
		}

		// Get current active theme
		$previous_theme = \get_stylesheet();

		// Check if the requested theme is already active
		if ( $theme_slug === $previous_theme ) {
			return array(
				'success'        => true,
				'message'        => __( 'Theme is already active', 'mcp-adapter-initializer' ),
				'theme'          => $theme_slug,
				'previous_theme' => $previous_theme,
			);
		}

		// Check if theme exists
		$theme = \wp_get_theme( $theme_slug );
		if ( ! $theme->exists() ) {
			return array(
				'success' => false,
				'message' => __( 'Theme is not installed. Use the activate-theme tool to install and activate a theme from WordPress.org', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		// Switch to the theme
		\switch_theme( $theme_slug );

		// Verify the theme was activated
		if ( \get_stylesheet() !== $theme_slug ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to switch theme', 'mcp-adapter-initializer' ),
				'theme'   => $theme_slug,
			);
		}

		return array(
			'success'        => true,
			'message'        => sprintf(
				/* translators: 1: new theme name, 2: previous theme name */
				__( 'Successfully switched from "%2$s" to "%1$s"', 'mcp-adapter-initializer' ),
				$theme->get( 'Name' ),
				\wp_get_theme( $previous_theme )->get( 'Name' )
			),
			'theme'          => $theme_slug,
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
