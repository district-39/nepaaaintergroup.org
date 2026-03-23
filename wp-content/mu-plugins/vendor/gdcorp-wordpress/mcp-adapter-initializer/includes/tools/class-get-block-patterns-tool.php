<?php
/**
 * Get Block Patterns Tool Class
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
 * Get Block Patterns Tool
 *
 * Handles the registration and execution of the get block patterns ability
 * for the MCP adapter.
 */
class Get_Block_Patterns_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/get-block-patterns';

	/**
	 * Tool instance
	 *
	 * @var Get_Block_Patterns_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Get_Block_Patterns_Tool
	 */
	public static function get_instance(): Get_Block_Patterns_Tool {
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
	 * Register the get block patterns ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Block Patterns', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves all registered block patterns on the site', 'mcp-adapter-initializer' ),
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
			'properties' => array(),
			'required'   => array(),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Block patterns retrieval result', 'mcp-adapter-initializer' ),
			array(
				'block_patterns' => array(
					'type'        => 'array',
					'description' => __( 'Array of registered block patterns', 'mcp-adapter-initializer' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'          => array(
								'type'        => 'string',
								'description' => __( 'The pattern name', 'mcp-adapter-initializer' ),
							),
							'title'         => array(
								'type'        => 'string',
								'description' => __( 'The pattern title', 'mcp-adapter-initializer' ),
							),
							'description'   => array(
								'type'        => 'string',
								'description' => __( 'The pattern description', 'mcp-adapter-initializer' ),
							),
							'content'       => array(
								'type'        => 'string',
								'description' => __( 'The pattern HTML content', 'mcp-adapter-initializer' ),
							),
							'categories'    => array(
								'type'        => 'array',
								'description' => __( 'Array of pattern categories', 'mcp-adapter-initializer' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'keywords'      => array(
								'type'        => 'array',
								'description' => __( 'Array of pattern keywords', 'mcp-adapter-initializer' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'viewportWidth' => array(
								'type'        => 'integer',
								'description' => __( 'The viewport width for preview', 'mcp-adapter-initializer' ),
							),
							'blockTypes'    => array(
								'type'        => 'array',
								'description' => __( 'Array of supported block types', 'mcp-adapter-initializer' ),
								'items'       => array(
									'type' => 'string',
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the get block patterns tool
	 *
	 * @param array $input Input parameters (none required)
	 * @return array Block patterns result or error
	 */
	public function execute( array $input ): array {
		$registry            = \WP_Block_Patterns_Registry::get_instance();
		$registered_patterns = $registry->get_all_registered();

		// return array( 'test' => $registered_patterns[0]['name'] );

		$block_patterns = array();

		foreach ( $registered_patterns as $pattern_data ) {
			$block_patterns[] = array(
				'name'        => $pattern_data['name'] ?? '',
				'title'       => $pattern_data['title'] ?? '',
				'description' => $pattern_data['description'] ?? '',
				'content'     => $pattern_data['content'] ?? '',
				'categories'  => $pattern_data['categories'] ?? array(),
				'keywords'    => $pattern_data['keywords'] ?? array(),
				'blockTypes'  => $pattern_data['blockTypes'] ?? array(),
			);
		}

		return array(
			'success'        => true,
			'block_patterns' => $block_patterns,
			// translators: %d is the number of block patterns found
			'message'        => sprintf( __( 'Retrieved %d registered block patterns', 'mcp-adapter-initializer' ), count( $block_patterns ) ),
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
