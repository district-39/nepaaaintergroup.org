<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Server;

use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\ErrorHandling\McpErrorFactory;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\HttpRequestContext;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\JsonRpcResponseBuilder;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\McpTransportContext;
use Throwable;
use WP_REST_Response;

/**
 * Stateless HTTP request handler for JWT-authenticated MCP requests.
 *
 * Replaces the dependency's HttpRequestHandler for the JWT transport path.
 * The only difference is that session validation via SessionManager is removed:
 * JWT authentication (enforced by the transport's permission_callback) is the
 * sole trust boundary, so no database-backed session is needed.
 *
 * @package mcp-adapter-initializer
 */
class Stateless_Http_Request_Handler {

	private const TRANSPORT_NAME = 'stateless-jwt';

	/**
	 * The transport context.
	 *
	 * @var McpTransportContext
	 */
	public McpTransportContext $transport_context;

	/**
	 * Constructor.
	 *
	 * @param McpTransportContext $transport_context The transport context.
	 */
	public function __construct( McpTransportContext $transport_context ) {
		$this->transport_context = $transport_context;
	}

	/**
	 * Route an HTTP request to the appropriate handler.
	 *
	 * Only POST is expected (the transport registers POST exclusively).
	 *
	 * @param HttpRequestContext $context The HTTP request context.
	 *
	 * @return WP_REST_Response HTTP response.
	 */
	public function handle_request( HttpRequestContext $context ): WP_REST_Response {
		if ( 'POST' !== $context->method ) {
			return new WP_REST_Response(
				McpErrorFactory::internal_error( 0, 'Method not allowed' ),
				405
			);
		}

		return $this->handle_mcp_request( $context );
	}

	/**
	 * Handle an MCP POST request.
	 *
	 * @param HttpRequestContext $context The HTTP request context.
	 *
	 * @return WP_REST_Response MCP response.
	 */
	private function handle_mcp_request( HttpRequestContext $context ): WP_REST_Response {
		try {
			if ( null === $context->body ) {
				return new WP_REST_Response(
					McpErrorFactory::parse_error( 0, 'Invalid JSON in request body' ),
					400
				);
			}

			return $this->process_mcp_messages( $context );
		} catch ( Throwable $exception ) {
			$this->transport_context->mcp_server->error_handler->log(
				'Unexpected error in stateless request handler',
				array(
					'transport' => self::TRANSPORT_NAME,
					'server_id' => $this->transport_context->mcp_server->get_server_id(),
					'error'     => $exception->getMessage(),
				)
			);

			return new WP_REST_Response(
				McpErrorFactory::internal_error( 0, 'Handler error occurred' ),
				500
			);
		}
	}

	/**
	 * Process JSON-RPC messages from the request body.
	 *
	 * @param HttpRequestContext $context The HTTP request context.
	 *
	 * @return WP_REST_Response MCP response.
	 */
	private function process_mcp_messages( HttpRequestContext $context ): WP_REST_Response {
		$is_batch = JsonRpcResponseBuilder::is_batch_request( $context->body );
		$messages = JsonRpcResponseBuilder::normalize_messages( $context->body );

		$response_body = JsonRpcResponseBuilder::process_messages(
			$messages,
			$is_batch,
			function ( array $message ) use ( $context ) {
				return $this->process_single_message( $message, $context );
			}
		);

		if ( ! $is_batch && isset( $response_body['error'] ) ) {
			return new WP_REST_Response(
				$response_body,
				McpErrorFactory::get_http_status_for_error( $response_body )
			);
		}

		return new WP_REST_Response( $response_body, 200 );
	}

	/**
	 * Process a single JSON-RPC message.
	 *
	 * @param array              $message The JSON-RPC message.
	 * @param HttpRequestContext $context The HTTP request context.
	 *
	 * @return array|null JSON-RPC response, or null for notifications.
	 */
	private function process_single_message( array $message, HttpRequestContext $context ): ?array {
		$validation = McpErrorFactory::validate_jsonrpc_message( $message );
		if ( isset( $validation['error'] ) ) {
			return $validation;
		}

		if ( isset( $message['method'] ) && ! isset( $message['id'] ) ) {
			return null;
		}

		if ( isset( $message['method'] ) && isset( $message['id'] ) ) {
			return $this->route_request( $message, $context );
		}

		return null;
	}

	/**
	 * Route a JSON-RPC request to the MCP handler.
	 *
	 * Unlike HttpRequestHandler::process_jsonrpc_request(), this method does
	 * NOT call HttpSessionValidator::validate_session(). The JWT verified in
	 * the transport's permission_callback is the sole authentication gate.
	 *
	 * @param array              $message The JSON-RPC message.
	 * @param HttpRequestContext $context The HTTP request context.
	 *
	 * @return array JSON-RPC response.
	 */
	private function route_request( array $message, HttpRequestContext $context ): array {
		$request_id = $message['id'];
		$method     = $message['method'];
		$params     = $message['params'] ?? array();

		$result = $this->transport_context->request_router->route_request(
			$method,
			$params,
			$request_id,
			self::TRANSPORT_NAME,
			$context
		);

		unset( $result['_session_id'] );

		if ( isset( $result['error'] ) ) {
			return JsonRpcResponseBuilder::create_error_response( $request_id, $result['error'] );
		}

		return JsonRpcResponseBuilder::create_success_response( $request_id, $result );
	}
}
