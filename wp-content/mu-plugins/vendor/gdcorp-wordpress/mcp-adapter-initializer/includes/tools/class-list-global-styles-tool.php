<?php
/**
 * List Global Styles Tool Class
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
 * List Global Styles Tool
 *
 * Handles the registration and execution of the list global styles ability
 * for the MCP adapter.
 */
class List_Global_Styles_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/list-global-styles';

	/**
	 * Tool instance
	 *
	 * @var List_Global_Styles_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return List_Global_Styles_Tool
	 */
	public static function get_instance(): List_Global_Styles_Tool {
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
	 * Register the list global styles ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Global Styles', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a list of all global styles configurations, optionally filtered by theme', 'mcp-adapter-initializer' ),
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
				'theme'  => array(
					'type'        => 'string',
					'description' => __( 'Optional theme slug to filter global styles by specific theme', 'mcp-adapter-initializer' ),
				),
				'status' => array(
					'type'        => 'string',
					'description' => __( 'Filter by post status (publish, draft, etc.). Defaults to all statuses', 'mcp-adapter-initializer' ),
					'enum'        => array( 'publish', 'draft', 'any' ),
					'default'     => 'any',
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
			__( 'Global styles list result', 'mcp-adapter-initializer' ),
			array(
				'styles' => array(
					'type'        => 'array',
					'description' => __( 'Array of global styles information', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'       => array(
								'type'        => 'integer',
								'description' => __( 'The global styles post ID', 'mcp-adapter-initializer' ),
							),
							'title'    => array(
								'type'        => 'string',
								'description' => __( 'The global styles title', 'mcp-adapter-initializer' ),
							),
							'theme'    => array(
								'type'        => 'string',
								'description' => __( 'The theme slug this style belongs to', 'mcp-adapter-initializer' ),
							),
							'status'   => array(
								'type'        => 'string',
								'description' => __( 'The post status (publish, draft, etc.)', 'mcp-adapter-initializer' ),
							),
							'date'     => array(
								'type'        => 'string',
								'description' => __( 'The creation date', 'mcp-adapter-initializer' ),
							),
							'modified' => array(
								'type'        => 'string',
								'description' => __( 'The last modified date', 'mcp-adapter-initializer' ),
							),
						),
					),
				),
				'total'  => array(
					'type'        => 'integer',
					'description' => __( 'Total number of global styles found', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the list global styles tool
	 *
	 * @param array $input Input parameters
	 * @return array Global styles list result or error
	 */
	public function execute( array $input ): array {
		try {
			// Get input parameters
			$theme  = isset( $input['theme'] ) ? sanitize_text_field( $input['theme'] ) : '';
			$status = isset( $input['status'] ) ? $input['status'] : 'any';

			// Determine post status to query
			$post_status = 'any' === $status ? array( 'publish', 'draft' ) : $status;

			// Build query arguments
			$query_args = array(
				'post_type'      => 'wp_global_styles',
				'post_status'    => $post_status,
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			// If theme filter is provided, add meta query
			if ( ! empty( $theme ) ) {
				$query_args['meta_query'] = array(
					array(
						'key'     => 'theme',
						'value'   => $theme,
						'compare' => '=',
					),
				);
			}

			// Execute the query
			$global_styles = get_posts( $query_args );

			if ( empty( $global_styles ) ) {
				$message = ! empty( $theme )
					/* translators: %s: theme slug */
					? sprintf( __( 'No global styles found for theme: %s', 'mcp-adapter-initializer' ), $theme )
					: __( 'No global styles found', 'mcp-adapter-initializer' );

				return array(
					'success' => true,
					'styles'  => array(),
					'total'   => 0,
					'message' => $message,
				);
			}

			// Format the results
			$styles = array();
			foreach ( $global_styles as $style ) {
				$theme_slug = get_post_meta( $style->ID, 'theme', true );

				// If no theme filter or matches the filter
				if ( empty( $theme ) || $theme_slug === $theme ) {
					$styles[] = array(
						'id'       => (int) $style->ID,
						'title'    => $style->post_title,
						'theme'    => $theme_slug ? $theme_slug : '',
						'status'   => $style->post_status,
						'date'     => $style->post_date,
						'modified' => $style->post_modified,
					);
				}
			}

			$total = count( $styles );

			$message = ! empty( $theme )
				/* translators: 1: number of styles, 2: theme slug */
				? sprintf( __( 'Found %1$d global styles for theme: %2$s', 'mcp-adapter-initializer' ), $total, $theme )
				/* translators: %d: number of styles */
				: sprintf( __( 'Found %d global styles', 'mcp-adapter-initializer' ), $total );

			return array(
				'success' => true,
				'styles'  => $styles,
				'total'   => $total,
				'message' => $message,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error retrieving global styles: %s', 'mcp-adapter-initializer' ),
					$e->getMessage()
				),
			);
		}
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
