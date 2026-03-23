<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Server;

use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\HttpRequestContext;
use WP_REST_Request;

/**
 * Stateless Request Context for MCP requests authenticated via JWT.
 *
 * Generates a deterministic, per-user session identifier without any database
 * interaction. The downstream pipeline (RequestRouter) checks session_id to
 * decide whether to create a DB-backed session on "initialize"; a non-null
 * value here causes it to skip that path entirely, keeping the JWT flow
 * fully stateless.
 *
 * Session validation is handled by the accompanying
 * Stateless_Http_Request_Handler which bypasses SessionManager altogether.
 *
 * IMPORTANT: This class assumes the current WordPress user has already been
 * set by the transport's permission_callback (via Auth_Helper). It must not
 * be instantiated before authentication has occurred.
 *
 * @package mcp-adapter-initializer
 */
class Stateless_Request_Context extends HttpRequestContext {

	/**
	 * Prefix applied to every deterministic session ID so it can be
	 * distinguished from UUIDs created by SessionManager.
	 *
	 * @var string
	 */
	private const SESSION_PREFIX = 'jwt-';

	/**
	 * Constructor.
	 *
	 * Relies on the transport's permission_callback having already
	 * authenticated the request and set the current user via
	 * Auth_Helper::authenticate_request().
	 *
	 * @param WP_REST_Request $request The REST request.
	 */
	public function __construct( WP_REST_Request $request ) {
		if ( is_user_logged_in() ) {
			$request->set_header(
				'Mcp-Session-Id',
				self::generate_session_id( get_current_user_id() )
			);
		}

		parent::__construct( $request );
	}

	/**
	 * Derive a stable session identifier from the user ID.
	 *
	 * The result is deterministic: every concurrent request from the same
	 * user produces the same value, so there is nothing to race on.
	 *
	 * @param int $user_id The authenticated user ID.
	 *
	 * @return string Deterministic session identifier.
	 */
	private static function generate_session_id( int $user_id ): string {
		return self::SESSION_PREFIX . wp_hash( 'mcp-stateless-session-' . $user_id );
	}
}
