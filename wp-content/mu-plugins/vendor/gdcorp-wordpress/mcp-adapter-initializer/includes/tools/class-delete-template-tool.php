<?php
/**
 * Delete Template Tool
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
 * Delete Template Tool Class
 *
 * Provides functionality to delete block templates using WordPress core
 * REST API controller. Deletion removes the database override, causing
 * the template to fall back to the theme's filesystem version.
 */
class Delete_Template_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/delete-template';

	/**
	 * Singleton instance
	 *
	 * @var Delete_Template_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Delete_Template_Tool
	 */
	public static function get_instance(): Delete_Template_Tool {
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
	 * Register the delete template ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Template', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deletes a block template (resets to theme default by removing DB override)', 'mcp-adapter-initializer' ),
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
				'id'    => array(
					'type'        => 'string',
					'description' => __( 'Template ID in format theme//slug (e.g., "twentytwentyfive//page")', 'mcp-adapter-initializer' ),
				),
				'force' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to force deletion (bypass trash)', 'mcp-adapter-initializer' ),
					'default'     => false,
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
			__( 'Template deletion result', 'mcp-adapter-initializer' ),
			array(
				'deleted'  => array(
					'type'        => 'object',
					'description' => __( 'Information about the deleted template', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'     => array(
							'type'        => 'string',
							'description' => __( 'The deleted template ID', 'mcp-adapter-initializer' ),
						),
						'status' => array(
							'type'        => 'string',
							'description' => __( 'The previous status of the template', 'mcp-adapter-initializer' ),
						),
					),
				),
				'previous' => array(
					'type'        => 'object',
					'description' => __( 'The template data before deletion', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the delete template tool
	 *
	 * Uses WordPress core REST API controller for template operations.
	 * Permissions are handled automatically via Base_Tool::execute_with_admin().
	 *
	 * @param array $input Input parameters
	 * @return array Deletion result or error
	 */
	public function execute( array $input ): array {
		try {
			$template_id = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';
			$force       = isset( $input['force'] ) ? (bool) $input['force'] : false;

			if ( empty( $template_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Template ID is required', 'mcp-adapter-initializer' ),
				);
			}

			// Check if template exists using WordPress core function
			$existing_template = get_block_template( $template_id, 'wp_template' );

			if ( ! $existing_template ) {
				return array(
					'success' => false,
					'message' => sprintf(
						__( 'Template "%s" not found', 'mcp-adapter-initializer' ),
						$template_id
					),
				);
			}

			// Store previous data before deletion
			$previous = array(
				'id'          => $existing_template->id,
				'slug'        => $existing_template->slug,
				'theme'       => $existing_template->theme,
				'source'      => $existing_template->source,
				'title'       => $existing_template->title,
				'description' => $existing_template->description,
			);

			// Check if this is a theme-only template (no DB override)
			// If source is 'theme' and there's no wp_id, it's theme-only
			if ( 'theme' === $existing_template->source && empty( $existing_template->wp_id ) ) {
				return array(
					'success' => true,
					'message' => sprintf(
						__( 'Template "%s" is theme-only (no customizations to reset)', 'mcp-adapter-initializer' ),
						$template_id
					),
					'deleted' => array(
						'id'     => $template_id,
						'status' => 'theme-only',
					),
				);
			}

			// Prepare request object for WordPress REST API
			$request = new \WP_REST_Request( 'DELETE', '/wp/v2/templates/' . $template_id );
			$request->set_param( 'id', $template_id );
			$request->set_param( 'force', $force );

			// Use WordPress REST controller
			$controller = new \WP_REST_Templates_Controller( 'wp_template' );
			$response   = $controller->delete_item( $request );

			// Handle WordPress REST API errors
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			return array(
				'success'  => true,
				'message'  => sprintf(
					__( 'Template "%s" reset to theme default', 'mcp-adapter-initializer' ),
					$template_id
				),
				'deleted'  => array(
					'id'     => $template_id,
					'status' => 'reset',
				),
				'previous' => $previous,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Error deleting template: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
	}
}
