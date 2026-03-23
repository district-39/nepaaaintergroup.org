<?php
/**
 * Create Navigation Tool
 *
 * Creates a new navigation post
 *
 * @package MCP_Adapter_Initializer
 * @subpackage Tools
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Create Navigation Tool Class
 *
 * Provides functionality to create navigation posts with title, content,
 * status, and other properties.
 */
class Create_Navigation_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 */
	const TOOL_ID = 'gd-mcp/create-navigation';

	/**
	 * Singleton instance
	 *
	 * @var Create_Navigation_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Create_Navigation_Tool
	 */
	public static function get_instance(): Create_Navigation_Tool {
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
	 * Register the create navigation ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Create Navigation', 'mcp-adapter-initializer' ),
				'description'         => __( 'Creates a new navigation post', 'mcp-adapter-initializer' ),
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
				'date'     => array(
					'type'        => 'string',
					'description' => __( 'The date the post was published, in the site\'s timezone', 'mcp-adapter-initializer' ),
				),
				'date_gmt' => array(
					'type'        => 'string',
					'description' => __( 'The date the post was published, as GMT', 'mcp-adapter-initializer' ),
				),
				'slug'     => array(
					'type'        => 'string',
					'description' => __( 'An alphanumeric identifier for the post unique to its type', 'mcp-adapter-initializer' ),
				),
				'status'   => array(
					'type'        => 'string',
					'description' => __( 'A named status for the post', 'mcp-adapter-initializer' ),
					'enum'        => array( 'publish', 'future', 'draft', 'pending', 'private' ),
					'default'     => 'publish',
				),
				'password' => array(
					'type'        => 'string',
					'description' => __( 'A password to protect access to the content and excerpt', 'mcp-adapter-initializer' ),
				),
				'title'    => array(
					'type'        => 'string',
					'description' => __( 'The title for the post', 'mcp-adapter-initializer' ),
				),
				'content'  => array(
					'type'        => 'string',
					'description' => __( 'The content for the post', 'mcp-adapter-initializer' ),
				),
				'template' => array(
					'type'        => 'string',
					'description' => __( 'The theme file to use to display the post', 'mcp-adapter-initializer' ),
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
			__( 'Navigation creation result', 'mcp-adapter-initializer' ),
			array(
				'navigation' => array(
					'type'        => 'object',
					'description' => __( 'The created navigation data', 'mcp-adapter-initializer' ),
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
	 * Execute the create navigation tool
	 *
	 * @param array $input Input parameters
	 * @return array Creation result or error
	 */
	public function execute( array $input ): array {
		// Check permissions
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to create navigation menus', 'mcp-adapter-initializer' ),
			);
		}

		// Prepare post data
		$post_data = array(
			'post_type'   => 'wp_navigation',
			'post_status' => isset( $input['status'] ) ? $input['status'] : 'publish',
		);

		// Title
		if ( ! empty( $input['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $input['title'] );
		}

		// Content
		if ( ! empty( $input['content'] ) ) {
			$post_data['post_content'] = $input['content'];
		}

		// Slug
		if ( ! empty( $input['slug'] ) ) {
			$post_data['post_name'] = sanitize_title( $input['slug'] );
		}

		// Password
		if ( ! empty( $input['password'] ) ) {
			$post_data['post_password'] = $input['password'];
		}

		// Date
		if ( ! empty( $input['date'] ) ) {
			$post_data['post_date'] = $input['date'];
		}

		// Date GMT
		if ( ! empty( $input['date_gmt'] ) ) {
			$post_data['post_date_gmt'] = $input['date_gmt'];
		}

		// Create the post
		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return array(
				'success' => false,
				'message' => $post_id->get_error_message(),
			);
		}

		// Set template if provided
		if ( ! empty( $input['template'] ) ) {
			update_post_meta( $post_id, '_wp_page_template', sanitize_text_field( $input['template'] ) );
		}

		// Get the created post
		$post = get_post( $post_id );

		return array(
			'success'    => true,
			'navigation' => $this->build_navigation_data( $post ),
			'message'    => __( 'Navigation created successfully', 'mcp-adapter-initializer' ),
		);
	}

	/**
	 * Build navigation data for response
	 *
	 * @param \WP_Post $post Post object
	 * @return array Navigation data
	 */
	private function build_navigation_data( \WP_Post $post ): array {
		return array(
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
				'raw'      => $post->post_title,
			),
			'content'      => array(
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			),
			'template'     => get_page_template_slug( $post->ID ),
		);
	}
}
