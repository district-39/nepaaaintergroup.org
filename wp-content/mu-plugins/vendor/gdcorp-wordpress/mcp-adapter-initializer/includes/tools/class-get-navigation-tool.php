<?php
/**
 * Get Navigation Tool
 *
 * Retrieves a specific navigation post by ID
 *
 * @package MCP_Adapter_Initializer
 * @subpackage Tools
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get Navigation Tool Class
 *
 * Provides functionality to retrieve a specific navigation post with
 * all its properties and content.
 */
class Get_Navigation_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/get-navigation';

	/**
	 * Singleton instance
	 *
	 * @var Get_Navigation_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Get_Navigation_Tool
	 */
	public static function get_instance(): Get_Navigation_Tool {
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
	 * Register the get navigation ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Navigation', 'mcp-adapter-initializer' ),
				'description'         => __( 'Retrieves a specific navigation post by ID', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'content-management',
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
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the post', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'context'  => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'mcp-adapter-initializer' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'password' => array(
					'type'        => 'string',
					'description' => __( 'The password for the post if it is password protected', 'mcp-adapter-initializer' ),
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
			__( 'Navigation retrieval result', 'mcp-adapter-initializer' ),
			array(
				'navigation' => array(
					'type'        => 'object',
					'description' => __( 'The navigation data', 'mcp-adapter-initializer' ),
					'properties'  => array(
						'id'           => array(
							'type'        => 'integer',
							'description' => __( 'Unique identifier for the post', 'mcp-adapter-initializer' ),
						),
						'date'         => array(
							'type'        => 'string',
							'description' => __( 'The date the post was published, in the site\'s timezone', 'mcp-adapter-initializer' ),
						),
						'date_gmt'     => array(
							'type'        => 'string',
							'description' => __( 'The date the post was published, as GMT', 'mcp-adapter-initializer' ),
						),
						'guid'         => array(
							'type'        => 'object',
							'description' => __( 'The globally unique identifier for the post', 'mcp-adapter-initializer' ),
						),
						'modified'     => array(
							'type'        => 'string',
							'description' => __( 'The date the post was last modified, in the site\'s timezone', 'mcp-adapter-initializer' ),
						),
						'modified_gmt' => array(
							'type'        => 'string',
							'description' => __( 'The date the post was last modified, as GMT', 'mcp-adapter-initializer' ),
						),
						'slug'         => array(
							'type'        => 'string',
							'description' => __( 'An alphanumeric identifier for the post unique to its type', 'mcp-adapter-initializer' ),
						),
						'status'       => array(
							'type'        => 'string',
							'description' => __( 'A named status for the post', 'mcp-adapter-initializer' ),
						),
						'type'         => array(
							'type'        => 'string',
							'description' => __( 'Type of post', 'mcp-adapter-initializer' ),
						),
						'link'         => array(
							'type'        => 'string',
							'description' => __( 'URL to the post', 'mcp-adapter-initializer' ),
						),
						'title'        => array(
							'type'        => 'object',
							'description' => __( 'The title for the post', 'mcp-adapter-initializer' ),
						),
						'content'      => array(
							'type'        => 'object',
							'description' => __( 'The content for the post', 'mcp-adapter-initializer' ),
						),
						'template'     => array(
							'type'        => 'string',
							'description' => __( 'The theme file to use to display the post', 'mcp-adapter-initializer' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Execute the get navigation tool
	 *
	 * @param array $input Input parameters
	 * @return array Navigation data or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Navigation ID is required', 'mcp-adapter-initializer' ),
			);
		}

		$navigation_id = (int) $input['id'];
		$context       = isset( $input['context'] ) ? $input['context'] : 'view';

		// Get the navigation post
		$post = get_post( $navigation_id );

		if ( ! $post || 'wp_navigation' !== $post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Navigation with ID %d not found', 'mcp-adapter-initializer' ), $navigation_id ),
			);
		}

		// Check if post is password protected
		if ( ! empty( $post->post_password ) ) {
			$password = isset( $input['password'] ) ? $input['password'] : '';
			if ( $password !== $post->post_password ) {
				return array(
					'success' => false,
					'message' => __( 'Incorrect password for protected navigation', 'mcp-adapter-initializer' ),
				);
			}
		}

		// Check permissions
		if ( 'edit' === $context && ! current_user_can( 'edit_post', $navigation_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to edit this navigation', 'mcp-adapter-initializer' ),
			);
		}

		return array(
			'success'    => true,
			'navigation' => $this->build_navigation_data( $post, $context ),
		);
	}

	/**
	 * Build navigation data based on context
	 *
	 * @param \WP_Post $post Post object
	 * @param string   $context Response context
	 * @return array Navigation data
	 */
	private function build_navigation_data( \WP_Post $post, string $context ): array {
		// Base data available in all contexts
		$data = array(
			'id'           => $post->ID,
			'date'         => $post->post_date,
			'date_gmt'     => $post->post_date_gmt,
			'guid'         => array(
				'rendered' => $post->guid,
				'raw'      => $post->guid,
			),
			'modified'     => $post->post_modified,
			'modified_gmt' => $post->post_modified_gmt,
			'slug'         => $post->post_name,
			'status'       => $post->post_status,
			'type'         => $post->post_type,
			'link'         => get_permalink( $post->ID ),
			'title'        => array(
				'rendered' => get_the_title( $post->ID ),
			),
		);

		// Add additional fields based on context
		if ( 'edit' === $context ) {
			$data['title']['raw'] = $post->post_title;
			$data['content']      = array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			);
			$data['template']     = get_page_template_slug( $post->ID );
		} elseif ( 'view' === $context ) {
			$data['content']  = array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
			$data['template'] = get_page_template_slug( $post->ID );
		} elseif ( 'embed' === $context ) {
			$data['content'] = array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		return $data;
	}
}
