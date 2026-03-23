<?php
/**
 * List Template Part Revisions Tool
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
 * List Template Part Revisions Tool Class
 *
 * Provides functionality to list revisions for a block template part using
 * WordPress core REST API controller.
 */
class List_Template_Part_Revisions_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/list-template-part-revisions';

	/**
	 * Singleton instance
	 *
	 * @var List_Template_Part_Revisions_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Template_Part_Revisions_Tool
	 */
	public static function get_instance(): List_Template_Part_Revisions_Tool {
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
	 * Register the list template part revisions ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Template Part Revisions', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves revisions for a block template part', 'mcp-adapter-initializer' ),
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
				'id'       => array(
					'type'        => 'string',
					'description' => __( 'Template part ID in format theme//slug', 'mcp-adapter-initializer' ),
				),
				'per_page' => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to return', 'mcp-adapter-initializer' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'page'     => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'mcp-adapter-initializer' ),
					'default'     => 1,
					'minimum'     => 1,
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
			__( 'Template part revisions list result', 'mcp-adapter-initializer' ),
			array(
				'revisions'   => array(
					'type'        => 'array',
					'description' => __( 'Array of revision objects', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array(
								'type'        => 'integer',
								'description' => __( 'Revision ID', 'mcp-adapter-initializer' ),
							),
							'parent' => array(
								'type'        => 'integer',
								'description' => __( 'Parent template part wp_id', 'mcp-adapter-initializer' ),
							),
							'author' => array(
								'type'        => 'integer',
								'description' => __( 'Author ID', 'mcp-adapter-initializer' ),
							),
							'date'   => array(
								'type'        => 'string',
								'description' => __( 'Revision date', 'mcp-adapter-initializer' ),
							),
							'slug'   => array(
								'type'        => 'string',
								'description' => __( 'Revision slug', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of revisions', 'mcp-adapter-initializer' ),
				),
				'total_pages' => array(
					'type'        => 'integer',
					'description' => __( 'Total number of pages', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the list template part revisions tool
	 *
	 * Uses WordPress core REST API controller for template part revision operations.
	 * Permissions are handled automatically via Base_Tool::execute_with_admin().
	 *
	 * @param array $input Input parameters
	 * @return array List of revisions or error
	 */
	public function execute( array $input ): array {
		try {
			$template_part_id = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';

			if ( empty( $template_part_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Template part ID is required', 'mcp-adapter-initializer' ),
				);
			}

			// Get the template part to find its wp_id
			$template_part = get_block_template( $template_part_id, 'wp_template_part' );

			if ( ! $template_part ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Template part "%s" not found', 'mcp-adapter-initializer' ), $template_part_id ),
				);
			}

			if ( empty( $template_part->wp_id ) ) {
				return array(
					'success'   => true,
					'revisions' => array(),
					'total'     => 0,
					'message'   => sprintf(
						__( 'No revisions found for template part "%s"', 'mcp-adapter-initializer' ),
						$template_part_id
					),
				);
			}

			// Prepare request object for WordPress REST API
			$request = new \WP_REST_Request( 'GET', '/wp/v2/template-parts/' . $template_part_id . '/revisions' );
			$request->set_param( 'parent', $template_part->wp_id );
			$request->set_param( 'per_page', isset( $input['per_page'] ) ? (int) $input['per_page'] : 10 );
			$request->set_param( 'page', isset( $input['page'] ) ? (int) $input['page'] : 1 );

			// Use WordPress REST controller for revisions
			$controller = new \WP_REST_Revisions_Controller( 'wp_template_part' );
			$response   = $controller->get_items( $request );

			// Handle WordPress REST API errors
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$revisions = $response->get_data();

			return array(
				'success'     => true,
				'revisions'   => $revisions,
				'total'       => count( $revisions ),
				'total_pages' => 1,
				'message'     => sprintf(
					__( 'Retrieved %1$d revision(s) for template part "%2$s"', 'mcp-adapter-initializer' ),
					count( $revisions ),
					$template_part_id
				),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					__( 'Error listing template part revisions: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
	}
}
