<?php
/**
 * Delete Template Part Tool
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
 * Delete Template Part Tool Class
 *
 * Provides functionality to delete block template parts using WordPress core
 * REST API controller. Deletion removes the database override, causing
 * the template part to fall back to the theme's filesystem version.
 */
class Delete_Template_Part_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/delete-template-part';

	/**
	 * Singleton instance
	 *
	 * @var Delete_Template_Part_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Delete_Template_Part_Tool
	 */
	public static function get_instance(): Delete_Template_Part_Tool {
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
	 * Register the delete template part ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Template Part', 'mcp-adapter-initializer' ),
				'description'         => __( 'Deletes a block template part (resets to theme default by removing DB override)', 'mcp-adapter-initializer' ),
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
					'description' => __( 'Template part ID in format theme//slug (e.g., "twentytwentyfive//header")', 'mcp-adapter-initializer' ),
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
			__( 'Template part deletion result', 'mcp-adapter-initializer' ),
			array(
				'deleted'  => array(
					'type'        => 'object',
					'description' => __( 'Information about the deleted template part', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'     => array(
							'type'        => 'string',
							'description' => __( 'The deleted template part ID', 'mcp-adapter-initializer' ),
						),
						'status' => array(
							'type'        => 'string',
							'description' => __( 'The previous status of the template part', 'mcp-adapter-initializer' ),
						),
					),
				),
				'previous' => array(
					'type'        => 'object',
					'description' => __( 'The template part data before deletion', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the delete template part tool
	 *
	 * Uses WordPress core REST API controller for template part operations.
	 * Permissions are handled automatically via Base_Tool::execute_with_admin().
	 *
	 * @param array $input Input parameters
	 * @return array Deletion result or error
	 */
	public function execute( array $input ): array {
		try {
			$template_part_id = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';
			$force            = isset( $input['force'] ) ? (bool) $input['force'] : false;

			if ( empty( $template_part_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Template part ID is required', 'mcp-adapter-initializer' ),
				);
			}

			// Check if template part exists using WordPress core function
			$existing_template_part = get_block_template( $template_part_id, 'wp_template_part' );

			if ( ! $existing_template_part ) {
				return array(
					'success' => false,
					'message' => sprintf(
						__( 'Template part "%s" not found', 'mcp-adapter-initializer' ),
						$template_part_id
					),
				);
			}

			// Store previous data before deletion
			$previous = array(
				'id'          => $existing_template_part->id,
				'slug'        => $existing_template_part->slug,
				'theme'       => $existing_template_part->theme,
				'source'      => $existing_template_part->source,
				'title'       => $existing_template_part->title,
				'description' => $existing_template_part->description,
				'area'        => $existing_template_part->area ?? 'uncategorized',
			);

			// Check if this is a theme-only template part (no DB override)
			// If source is 'theme' and there's no wp_id, it's theme-only
			if ( 'theme' === $existing_template_part->source && empty( $existing_template_part->wp_id ) ) {
				return array(
					'success' => true,
					'message' => sprintf(
						__( 'Template part "%s" is theme-only (no customizations to reset)', 'mcp-adapter-initializer' ),
						$template_part_id
					),
					'deleted' => array(
						'id'     => $template_part_id,
						'status' => 'theme-only',
					),
				);
			}

			// Prepare request object for WordPress REST API
			$request = new \WP_REST_Request( 'DELETE', '/wp/v2/template-parts/' . $template_part_id );
			$request->set_param( 'id', $template_part_id );
			$request->set_param( 'force', $force );

			// Use WordPress REST controller
			$controller = new \WP_REST_Templates_Controller( 'wp_template_part' );
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
					__( 'Template part "%s" reset to theme default', 'mcp-adapter-initializer' ),
					$template_part_id
				),
				'deleted'  => array(
					'id'     => $template_part_id,
					'status' => 'reset',
				),
				'previous' => $previous,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Error deleting template part: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
	}
}
