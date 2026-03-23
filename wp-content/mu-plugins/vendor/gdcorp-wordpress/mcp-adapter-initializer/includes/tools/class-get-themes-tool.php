<?php
/**
 * Get Themes Tool Class
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
 * Get Themes Tool
 *
 * Handles the registration and execution of the get themes ability
 * for the MCP adapter.
 */
class Get_Themes_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/get-themes';

	/**
	 * Tool instance
	 *
	 * @var Get_Themes_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Get_Themes_Tool
	 */
	public static function get_instance(): Get_Themes_Tool {
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
	 * Register the get themes ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Themes', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves themes installed on the site, optionally filtered to active theme only', 'mcp-adapter-initializer' ),
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
				'active' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to return only the active theme (true) or all installed themes (false)', 'mcp-adapter-initializer' ),
				),
			),
			'required'   => array( 'active' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Themes retrieval result', 'mcp-adapter-initializer' ),
			array(
				'themes' => array(
					'type'        => 'array',
					'description' => __( 'Array of theme information', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'             => array(
								'type'        => 'string',
								'description' => __( 'The theme name', 'mcp-adapter-initializer' ),
							),
							'title'            => array(
								'type'        => 'string',
								'description' => __( 'The theme title', 'mcp-adapter-initializer' ),
							),
							'description'      => array(
								'type'        => 'string',
								'description' => __( 'The theme description', 'mcp-adapter-initializer' ),
							),
							'version'          => array(
								'type'        => 'string',
								'description' => __( 'The theme version', 'mcp-adapter-initializer' ),
							),
							'author'           => array(
								'type'        => 'string',
								'description' => __( 'The theme author', 'mcp-adapter-initializer' ),
							),
							'author_uri'       => array(
								'type'        => 'string',
								'description' => __( 'The theme author URI', 'mcp-adapter-initializer' ),
							),
							'theme_uri'        => array(
								'type'        => 'string',
								'description' => __( 'The theme URI', 'mcp-adapter-initializer' ),
							),
							'stylesheet'       => array(
								'type'        => 'string',
								'description' => __( 'The theme stylesheet directory', 'mcp-adapter-initializer' ),
							),
							'template'         => array(
								'type'        => 'string',
								'description' => __( 'The theme template directory', 'mcp-adapter-initializer' ),
							),
							'status'           => array(
								'type'        => 'string',
								'description' => __( 'The theme status (active, inactive)', 'mcp-adapter-initializer' ),
							),
							'tags'             => array(
								'type'        => 'array',
								'description' => __( 'Array of theme tags', 'mcp-adapter-initializer' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'is_block_theme'   => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this is a block-based theme (FSE)', 'mcp-adapter-initializer' ),
							),
							'global_styles_id' => array(
								'type'        => 'integer',
								'description' => __( 'The ID of the wp_global_styles post for this theme (active themes only)', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the get themes tool
	 *
	 * @param array $input Input parameters
	 * @return array Themes result or error
	 */
	public function execute( array $input ): array {
		// Validate required parameter
		if ( ! isset( $input['active'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'The "active" parameter is required', 'mcp-adapter-initializer' ),
			);
		}

		$active_only = (bool) $input['active'];

		if ( $active_only ) {
			// Get only the active theme
			$current_theme = wp_get_theme();

			if ( ! $current_theme->exists() ) {
				return array(
					'success' => false,
					'message' => __( 'Unable to retrieve active theme information', 'mcp-adapter-initializer' ),
				);
			}

			$themes = array( $this->format_theme_data( $current_theme, true ) );
			// translators: %s is the theme name
			$message = sprintf( __( 'Retrieved active theme: %s', 'mcp-adapter-initializer' ), $current_theme->get( 'Name' ) );
		} else {
			// Get all installed themes
			$all_themes    = wp_get_themes();
			$current_theme = get_stylesheet();

			if ( empty( $all_themes ) ) {
				return array(
					'success' => false,
					'message' => __( 'No themes found on this site', 'mcp-adapter-initializer' ),
				);
			}

			$themes = array();

			foreach ( $all_themes as $theme_slug => $theme ) {
				$is_active = ( $theme_slug === $current_theme );
				$themes[]  = $this->format_theme_data( $theme, $is_active );
			}

			// translators: %d is the number of themes found
			$message = sprintf( __( 'Retrieved %d installed themes', 'mcp-adapter-initializer' ), count( $themes ) );
		}

		return array(
			'success' => true,
			'themes'  => $themes,
			'message' => $message,
		);
	}

	/**
	 * Format theme data for consistent output
	 *
	 * @param WP_Theme $theme The theme object
	 * @param bool     $is_active Whether this is the active theme
	 * @return array Formatted theme data
	 */
	private function format_theme_data( $theme, $is_active = false ): array {
		$theme_data = array(
			'name'        => $theme->get_stylesheet(),
			'title'       => $theme->get( 'Name' ),
			'description' => $theme->get( 'Description' ),
			'version'     => $theme->get( 'Version' ),
			'author'      => $theme->get( 'Author' ),
			'author_uri'  => $theme->get( 'AuthorURI' ),
			'theme_uri'   => $theme->get( 'ThemeURI' ),
			'stylesheet'  => $theme->get_stylesheet(),
			'template'    => $theme->get_template(),
			'status'      => $is_active ? 'active' : 'inactive',
			'tags'        => $theme->get( 'Tags' ) ? $theme->get( 'Tags' ) : array(),
		);

		// Add FSE detection for all themes (matches WordPress core REST API behavior)
		if ( method_exists( $theme, 'is_block_theme' ) ) {
			$theme_data['is_block_theme'] = $theme->is_block_theme();
		}

		// If this is the active theme, ensure global styles post exists
		// This matches WordPress REST API /wp/v2/themes behavior
		if ( $is_active ) {
			$global_styles_id = $this->ensure_global_styles_post_exists( $theme );
			if ( $global_styles_id ) {
				$theme_data['global_styles_id'] = $global_styles_id;
			}
		}

		return $theme_data;
	}

	/**
	 * Ensure a wp_global_styles post exists for the theme
	 *
	 * This matches the behavior of WordPress REST API /wp/v2/themes endpoint,
	 * which automatically creates a global styles post when queried.
	 *
	 * @param WP_Theme $theme The theme object
	 * @return int|null The global styles post ID, or null on failure
	 */
	private function ensure_global_styles_post_exists( $theme ): ?int {
		// Use WordPress's built-in resolver
		// This is what the REST API uses internally and handles creation automatically
		if ( ! class_exists( 'WP_Theme_JSON_Resolver' ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log(
				sprintf(
					'[gd-mcp-get-themes] WP_Theme_JSON_Resolver class not found for theme: %s',
					$theme->get_stylesheet()
				)
			);
			return null;
		}

		$post_id = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

		if ( $post_id ) {
			return $post_id;
		}

		// If resolver didn't return an ID, something went wrong
		// Log for debugging but don't fail the entire request
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log(
			sprintf(
				'[gd-mcp-get-themes] Failed to get/create global styles post for theme: %s',
				$theme->get_stylesheet()
			)
		);

		return null;
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
