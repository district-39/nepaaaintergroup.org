<?php
/**
 * Get Global Styles Tool Class
 *
 * @package     mcp-adapter-initializer
 * @author      GoDaddy
 * @copyright   2025 GoDaddy
 * @license     GPL-2.0-or-later
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

/**
 * Get Global Styles Tool
 *
 * Handles the registration and execution of the get global styles ability
 * for the MCP adapter.
 */
class Get_Global_Styles_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/get-global-styles';

	/**
	 * Singleton instance
	 *
	 * @var Get_Global_Styles_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance
	 *
	 * @return Get_Global_Styles_Tool
	 */
	public static function get_instance(): Get_Global_Styles_Tool {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {}

	/**
	 * Register the get global styles ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Global Styles', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves global style configuration by ID', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Get the tool ID
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
				'id'      => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => 'The ID of the global style to retrieve',
				),
				'context' => array(
					'type'        => 'string',
					'enum'        => array( 'view', 'edit', 'embed' ),
					'default'     => 'view',
					'description' => 'The context in which the request is made',
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
			__( 'Global styles retrieval result', 'mcp-adapter-initializer' ),
			array(
				'data' => array(
					'type'        => 'object',
					'description' => __( 'Global style data', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'           => array(
							'type'        => 'integer',
							'description' => __( 'The global style ID', 'mcp-adapter-initializer' ),
						),
						'title'        => array(
							'type'        => 'object',
							'description' => __( 'The global style title object', 'mcp-adapter-initializer' ),
							'properties'  => array(
								'rendered' => array(
									'type'        => 'string',
									'description' => __( 'Rendered title', 'mcp-adapter-initializer' ),
								),
								'raw'      => array(
									'type'        => 'string',
									'description' => __( 'Raw title (edit context only)', 'mcp-adapter-initializer' ),
								),
							),
						),
						'status'       => array(
							'type'        => 'string',
							'description' => __( 'The global style post status', 'mcp-adapter-initializer' ),
						),
						'date'         => array(
							'type'        => 'string',
							'description' => __( 'The global style creation date', 'mcp-adapter-initializer' ),
						),
						'date_gmt'     => array(
							'type'        => 'string',
							'description' => __( 'The global style creation date in GMT (edit context only)', 'mcp-adapter-initializer' ),
						),
						'modified'     => array(
							'type'        => 'string',
							'description' => __( 'The global style modification date', 'mcp-adapter-initializer' ),
						),
						'modified_gmt' => array(
							'type'        => 'string',
							'description' => __( 'The global style modification date in GMT (edit context only)', 'mcp-adapter-initializer' ),
						),
						'slug'         => array(
							'type'        => 'string',
							'description' => __( 'The global style slug (edit context only)', 'mcp-adapter-initializer' ),
						),
						'settings'     => array(
							'type'        => 'object',
							'description' => __( 'The global style settings object', 'mcp-adapter-initializer' ),
						),
						'styles'       => array(
							'type'        => 'object',
							'description' => __( 'The global style styles object', 'mcp-adapter-initializer' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the tool
	 *
	 * @param array $input Tool input parameters.
	 * @return array
	 */
	public function execute( array $input ): array {
		try {
			// Get the ID and context from input
			$style_id = isset( $input['id'] ) ? absint( $input['id'] ) : 0;
			$context  = isset( $input['context'] ) ? sanitize_text_field( $input['context'] ) : 'view';

			if ( empty( $style_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Style ID is required', 'mcp-adapter-initializer' ),
				);
			}

			// Retrieve the global style post
			$global_style = get_post( $style_id );

			if ( ! $global_style || 'wp_global_styles' !== $global_style->post_type ) {
				return array(
					'success' => false,
					'message' => __( 'Global style not found or invalid ID', 'mcp-adapter-initializer' ),
				);
			}

			// Parse the style content
			$style_content = ! empty( $global_style->post_content ) ? json_decode( $global_style->post_content, true ) : array();

			// Base data available in all contexts
			$response_data = array(
				'id'    => (int) $global_style->ID,
				'title' => array(
					'rendered' => $global_style->post_title,
				),
			);

			// Apply context-specific filtering
			if ( 'edit' === $context ) {
				// Edit context: includes all fields for editing
				$response_data['title']['raw'] = $global_style->post_title;
				$response_data['status']       = $global_style->post_status;
				$response_data['date']         = $global_style->post_date;
				$response_data['date_gmt']     = $global_style->post_date_gmt;
				$response_data['modified']     = $global_style->post_modified;
				$response_data['modified_gmt'] = $global_style->post_modified_gmt;
				$response_data['slug']         = $global_style->post_name;
				$response_data['settings']     = isset( $style_content['settings'] ) ? $style_content['settings'] : new \stdClass();
				$response_data['styles']       = isset( $style_content['styles'] ) ? $style_content['styles'] : new \stdClass();
			} elseif ( 'embed' === $context ) {
				// Embed context: minimal data for embedding (only id and title)
				// Response data already has the minimal fields
			} else {
				// View context (default): standard public fields
				$response_data['status']   = $global_style->post_status;
				$response_data['date']     = $global_style->post_date;
				$response_data['modified'] = $global_style->post_modified;
				$response_data['settings'] = isset( $style_content['settings'] ) ? $style_content['settings'] : new \stdClass();
				$response_data['styles']   = isset( $style_content['styles'] ) ? $style_content['styles'] : new \stdClass();
			}

			// Prepare the response
			$response = array(
				'success' => true,
				'data'    => $response_data,
			);

			return $response;

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				/* translators: %s: Error message */
				'message' => sprintf( __( 'Error retrieving global style: %s', 'mcp-adapter-initializer' ), $e->getMessage() ),
			);
		}
	}

	/**
	 * Get all global styles (for reference)
	 *
	 * @return array
	 */
	public function get_all_global_styles() {
		$global_styles = get_posts(
			array(
				'post_type'      => 'wp_global_styles',
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$styles = array();

		foreach ( $global_styles as $style ) {
			$styles[] = array(
				'id'     => (int) $style->ID,
				'title'  => $style->post_title,
				'status' => $style->post_status,
				'date'   => $style->post_date,
			);
		}

		return $styles;
	}
}
