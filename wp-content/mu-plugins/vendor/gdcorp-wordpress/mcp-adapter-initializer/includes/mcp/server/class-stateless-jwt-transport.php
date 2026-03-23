<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Server;

use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Auth\Auth_Helper;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Contracts\McpRestTransportInterface;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\McpTransportContext;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\McpTransportHelperTrait;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Stateless JWT Transport for MCP communication.
 *
 * Handles MCP requests authenticated via JWT without any database-backed
 * session management. Uses Stateless_Http_Request_Handler which bypasses
 * SessionManager entirely, eliminating the race condition that occurs when
 * concurrent requests from the same user each try to create a session.
 *
 * @package mcp-adapter-initializer
 */
class Stateless_JWT_Transport implements McpRestTransportInterface {
	use McpTransportHelperTrait;

	/**
	 * The stateless HTTP request handler.
	 *
	 * @var Stateless_Http_Request_Handler
	 */
	protected Stateless_Http_Request_Handler $request_handler;

	/**
	 * Constructor.
	 *
	 * @param McpTransportContext $transport_context The transport context.
	 */
	public function __construct( McpTransportContext $transport_context ) {
		$this->request_handler = new Stateless_Http_Request_Handler( $transport_context );
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 16 );
	}

	/**
	 * Register MCP HTTP routes
	 */
	public function register_routes(): void {
		// Get server info from request handler's transport context.
		$server = $this->request_handler->transport_context->mcp_server;

		// Single endpoint for MCP communication.
		register_rest_route(
			$server->get_server_route_namespace(),
			$server->get_server_route(),
			array(
				'methods'             => array( 'POST' ),
				'callback'            => array( $this, 'handle_request' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => $this->get_endpoint_args(),
				'schema'              => array( $this, 'get_endpoint_schema' ),
			)
		);
	}

	/**
	 * Get endpoint arguments schema.
	 *
	 * @return array<string, mixed> Arguments for the endpoint.
	 */
	public function get_endpoint_args(): array {
		return array(
			'method' => array(
				'description'       => __( 'The MCP method to call (e.g., tools/list, tools/call)', 'mcp-adapter-initializer' ),
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'params' => array(
				'description' => __( 'Parameters for the MCP method', 'mcp-adapter-initializer' ),
				'type'        => array( 'object', 'null' ),
				'default'     => null,
			),
		);
	}

	/**
	 * Get endpoint response schema.
	 *
	 * @return array<string, mixed> Schema for the endpoint.
	 */
	public function get_endpoint_schema(): array {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'mcp-request',
			'type'       => 'object',
			'properties' => array(
				'jsonrpc' => array(
					'description' => __( 'JSON-RPC version', 'mcp-adapter-initializer' ),
					'type'        => 'string',
					'readonly'    => true,
				),
				'id'      => array(
					'description' => __( 'Request ID', 'mcp-adapter-initializer' ),
					'type'        => array( 'string', 'integer', 'null' ),
					'readonly'    => true,
				),
				'result'  => array(
					'description' => __( 'The result of the MCP method call', 'mcp-adapter-initializer' ),
					'type'        => array( 'object', 'array', 'string', 'integer', 'boolean', 'null' ),
					'readonly'    => true,
				),
				'error'   => array(
					'description' => __( 'Error information if the call failed', 'mcp-adapter-initializer' ),
					'type'        => 'object',
					'readonly'    => true,
				),
			),
		);
	}

	/**
	 * Check permission for the MCP request.
	 *
	 * @param WP_REST_Request $request The REST request.
	 *
	 * @return bool True if the request is permitted, false otherwise.
	 */
	public function check_permission( WP_REST_Request $request ): bool {
		return Auth_Helper::authenticate_request();
	}

	/**
	 * Handle the MCP request.
	 *
	 * @param WP_REST_Request $request The REST request.
	 *
	 * @return WP_REST_Response The REST response.
	 */
	public function handle_request( WP_REST_Request $request ): WP_REST_Response {
		$context = new Stateless_Request_Context( $request );

		return $this->request_handler->handle_request( $context );
	}
}
