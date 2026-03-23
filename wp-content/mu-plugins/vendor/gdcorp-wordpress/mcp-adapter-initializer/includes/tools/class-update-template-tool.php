<?php
/**
 * Update Template Tool Class
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
 * Update Template Tool
 *
 * Handles the registration and execution of the update template ability
 * for the MCP adapter.
 */
class Update_Template_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/update-template';

	/**
	 * Tool instance
	 *
	 * @var Update_Template_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Update_Template_Tool
	 */
	public static function get_instance(): Update_Template_Tool {
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
	 * Register the update template ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Template', 'mcp-adapter-initializer' ),
				'description'         => __( 'Updates a template in the database with new HTML content. Creates the template if it does not exist.', 'mcp-adapter-initializer' ),
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
				'theme'         => array(
					'type'        => 'string',
					'description' => __( 'The theme slug where the template belongs', 'mcp-adapter-initializer' ),
				),
				'id'            => array(
					'type'        => 'string',
					'description' => __( 'The template ID (optional if template_name is provided)', 'mcp-adapter-initializer' ),
				),
				'template_name' => array(
					'type'        => 'string',
					'description' => __( 'The template name/slug (optional if id is provided, e.g., "page", "single")', 'mcp-adapter-initializer' ),
				),
				'html'          => array(
					'type'        => 'string',
					'description' => __( 'The new HTML content for the template', 'mcp-adapter-initializer' ),
				),
			),
			'required'   => array( 'theme', 'html' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Template update result', 'mcp-adapter-initializer' ),
			array(
				'data' => array(
					'type'        => 'object',
					'description' => __( 'Template data', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'      => array(
							'type'        => 'string',
							'description' => __( 'The template ID', 'mcp-adapter-initializer' ),
						),
						'slug'    => array(
							'type'        => 'string',
							'description' => __( 'The template slug', 'mcp-adapter-initializer' ),
						),
						'theme'   => array(
							'type'        => 'string',
							'description' => __( 'The theme slug', 'mcp-adapter-initializer' ),
						),
						'content' => array(
							'type'        => 'string',
							'description' => __( 'The updated HTML content', 'mcp-adapter-initializer' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the tool
	 *
	 * Uses WordPress core REST API controller for template operations.
	 * Permissions are handled automatically via Base_Tool::execute_with_admin().
	 *
	 * @param array $input Tool input parameters.
	 * @return array
	 */
	public function execute( array $input ): array {
		try {
			// Validate required parameters.
			$theme         = isset( $input['theme'] ) ? sanitize_text_field( $input['theme'] ) : '';
			$template_id   = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';
			$template_name = isset( $input['template_name'] ) ? sanitize_text_field( $input['template_name'] ) : '';
			$html_content  = isset( $input['html'] ) ? $input['html'] : '';

			if ( empty( $theme ) ) {
				return array(
					'success' => false,
					'message' => __( 'Theme parameter is required', 'mcp-adapter-initializer' ),
				);
			}

			if ( empty( $template_id ) && empty( $template_name ) ) {
				return array(
					'success' => false,
					'message' => __( 'Either template ID or template_name is required', 'mcp-adapter-initializer' ),
				);
			}

			if ( empty( $html_content ) ) {
				return array(
					'success' => false,
					'message' => __( 'HTML content is required', 'mcp-adapter-initializer' ),
				);
			}

			if ( ! empty( $template_id ) ) {
				$wp_template_id = (string) $template_id;
				// Extract slug from template ID.
				$parts = explode( '//', $wp_template_id );
				$slug  = end( $parts );
			} else {
				// Build template ID in WordPress format: theme//slug.
				$wp_template_id = $theme . '//' . $template_name;
				$slug           = $template_name;
			}

			/*
			 * Use WordPress core function to check if template exists.
			 * This handles both database and file system lookups automatically.
			 */
			$existing_template = get_block_template( $wp_template_id, 'wp_template' );

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'POST', '/wp/v2/templates' );
			$request->set_param( 'id', $wp_template_id );
			$request->set_param( 'theme', $theme );
			$request->set_param( 'slug', $slug );
			$request->set_param( 'content', $html_content );

			/*
			 * Use WordPress REST controller for create/update operations.
			 * Permission checks pass automatically because we're running as admin
			 * via Base_Tool::execute_with_admin().
			 */
			$controller = new \WP_REST_Templates_Controller( 'wp_template' );

			if ( $existing_template ) {
				// Update existing template using WordPress core.
				$response = $controller->update_item( $request );
			} else {
				// Create new template using WordPress core.
				$response = $controller->create_item( $request );
			}

			// Handle WordPress REST API errors.
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$data = $response->get_data();

			return array(
				'success' => true,
				'data'    => array(
					'id'      => $data['id'],
					'slug'    => $data['slug'],
					'theme'   => $data['theme'],
					'content' => $data['content']['raw'],
				),
				'message' => __( 'Template updated successfully', 'mcp-adapter-initializer' ),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Error updating template: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
	}
}
