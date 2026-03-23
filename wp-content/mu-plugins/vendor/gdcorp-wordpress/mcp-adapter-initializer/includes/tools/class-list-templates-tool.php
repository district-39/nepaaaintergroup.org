<?php
/**
 * List Templates Tool
 *
 * @package     mcp-adapter-initializer
 * @author      GoDaddy
 * @copyright   2026 GoDaddy
 * @license     GPL-2.0-or-later
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * List Templates Tool Class
 *
 * Provides functionality to list block templates using WordPress core
 * REST API controller for wp_template post type.
 */
class List_Templates_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/list-templates';

	/**
	 * Singleton instance
	 *
	 * @var List_Templates_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Templates_Tool
	 */
	public static function get_instance(): List_Templates_Tool {
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
	 * Register the list templates ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Templates', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of block templates with filtering options', 'mcp-adapter-initializer' ),
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
				'context' => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'edit',
				),
				'wp_id'   => array(
					'type'        => 'integer',
					'description' => __( 'Limit to the specified post ID (numeric wp_posts.ID)', 'mcp-adapter-initializer' ),
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
			__( 'Templates list result', 'mcp-adapter-initializer' ),
			array(
				'templates' => array(
					'type'        => 'array',
					'description' => __( 'Array of template objects', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'             => array(
								'type'        => 'string',
								'description' => __( 'Template ID in format theme//slug', 'mcp-adapter-initializer' ),
							),
							'slug'           => array(
								'type'        => 'string',
								'description' => __( 'Template slug', 'mcp-adapter-initializer' ),
							),
							'theme'          => array(
								'type'        => 'string',
								'description' => __( 'Theme slug', 'mcp-adapter-initializer' ),
							),
							'type'           => array(
								'type'        => 'string',
								'description' => __( 'Post type (wp_template)', 'mcp-adapter-initializer' ),
							),
							'source'         => array(
								'type'        => 'string',
								'description' => __( 'Source of the template (theme, custom, plugin)', 'mcp-adapter-initializer' ),
							),
							'origin'         => array(
								'type'        => 'string',
								'description' => __( 'Theme/plugin that originally provided the template', 'mcp-adapter-initializer' ),
							),
							'content'        => array(
								'type'        => 'object',
								'description' => __( 'Template content', 'mcp-adapter-initializer' ),
							),
							'title'          => array(
								'type'        => 'object',
								'description' => __( 'Template title', 'mcp-adapter-initializer' ),
							),
							'description'    => array(
								'type'        => 'string',
								'description' => __( 'Template description', 'mcp-adapter-initializer' ),
							),
							'status'         => array(
								'type'        => 'string',
								'description' => __( 'Template status', 'mcp-adapter-initializer' ),
							),
							'wp_id'          => array(
								'type'        => 'integer',
								'description' => __( 'Numeric post ID in wp_posts table', 'mcp-adapter-initializer' ),
							),
							'has_theme_file' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether a theme file exists for this template', 'mcp-adapter-initializer' ),
							),
							'is_custom'      => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this is a user-customized template', 'mcp-adapter-initializer' ),
							),
							'author'         => array(
								'type'        => 'integer',
								'description' => __( 'Author ID', 'mcp-adapter-initializer' ),
							),
							'modified'       => array(
								'type'        => 'string',
								'description' => __( 'Last modified date', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the list templates tool
	 *
	 * Uses WordPress core REST API controller for template operations.
	 * Permissions are handled automatically via Base_Tool::execute_with_admin().
	 *
	 * @param array $input Input parameters
	 * @return array List of templates or error
	 */
	public function execute( array $input ): array {
		try {
			$context = isset( $input['context'] ) ? $input['context'] : 'edit';
			$wp_id   = isset( $input['wp_id'] ) ? (int) $input['wp_id'] : null;

			// Prepare request object for WordPress REST API
			$request = new \WP_REST_Request( 'GET', '/wp/v2/templates' );
			$request->set_param( 'context', $context );
			if ( $wp_id ) {
				$request->set_param( 'wp_id', $wp_id );
			}

			// Use WordPress REST controller
			$controller = new \WP_REST_Templates_Controller( 'wp_template' );
			$response   = $controller->get_items( $request );

			// Handle WordPress REST API errors
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$templates = $response->get_data();
			foreach ( $templates as &$template ) {
				if ( array_key_exists( 'origin', $template ) && ! is_string( $template['origin'] ) ) {
					unset( $template['origin'] );
				}
				if ( array_key_exists( 'modified', $template ) && ! is_string( $template['modified'] ) ) {
					unset( $template['modified'] );
				}
			}
			unset( $template );

			return array(
				'success'   => true,
				'templates' => $templates,
				'message'   => sprintf(
					__( 'Retrieved %d template(s)', 'mcp-adapter-initializer' ),
					count( $templates )
				),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Error listing templates: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
	}
}
