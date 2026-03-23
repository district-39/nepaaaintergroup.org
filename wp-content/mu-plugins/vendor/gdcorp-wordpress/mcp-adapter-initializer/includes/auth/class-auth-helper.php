<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Auth;

use MCP_JWT_Authenticator;

/**
 * Authentication helper class for MCP requests
 *
 * Supports two authentication methods:
 * 1. WP Request Signature (primary) - HMAC-based signature validated via WP Public API
 * 2. JWT Authentication (fallback) - GoDaddy JWT token validation
 *
 * If signature headers are present but invalid, the request is rejected (no fallback to JWT).
 * JWT fallback only occurs when signature headers are NOT present.
 */
class Auth_Helper {
	/**
	 * Cached authentication result for the current request
	 *
	 * Prevents multiple authentication attempts (which would fail due to nonce replay protection)
	 * when authenticate_request() is called multiple times during a single HTTP request.
	 *
	 * @var bool|null
	 */
	private static $auth_result_cache = null;

	/**
	 * Authenticate MCP requests
	 *
	 * Authentication priority:
	 * 1. If signature headers present: validate signature (no JWT fallback on failure)
	 * 2. If no signature headers: fallback to JWT authentication
	 *
	 * Results are cached for the duration of the request to prevent nonce reuse errors.
	 *
	 * @return bool Whether request is authenticated
	 */
	public static function authenticate_request(): bool {
		// Return cached result if already authenticated during this request.
		if ( null !== self::$auth_result_cache ) {
			return self::$auth_result_cache;
		}

		// 1. Try WP Request Signature authentication first.
		$signature_auth = new Signature_Auth();

		if ( $signature_auth->has_signature_headers() ) {
			// Signature headers present - use signature auth only (no JWT fallback).
			if ( $signature_auth->authenticate_request() ) {
				self::$auth_result_cache = self::set_admin_user_context();
				return self::$auth_result_cache;
			}
			// Signature headers present but invalid - reject immediately.
			self::$auth_result_cache = false;
			return false;
		}

		// 2. No signature headers - fallback to JWT authentication.
		self::$auth_result_cache = self::authenticate_with_jwt();
		return self::$auth_result_cache;
	}

	/**
	 * Authenticate using JWT (existing approach, unchanged)
	 *
	 * @return bool Whether JWT authentication succeeded
	 */
	private static function authenticate_with_jwt(): bool {
		$jwt     = isset( $_SERVER['HTTP_X_GD_JWT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_GD_JWT'] ) ) : '';
		$site_id = isset( $_SERVER['HTTP_X_GD_SITE_ID'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_GD_SITE_ID'] ) ) : '';

		$authenticator = new MCP_JWT_Authenticator();

		if ( ! $authenticator->authenticate_request( $jwt, $site_id ) ) {
			return false;
		}

		return self::set_admin_user_context();
	}

	/**
	 * Set the current user to admin context
	 *
	 * @return bool True if admin user was found and set
	 */
	private static function set_admin_user_context(): bool {
		// Set the current user to the oldest admin user found in database.
		$admin_users = get_users(
			array(
				'role'    => 'administrator',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'number'  => 1,
			)
		);

		if ( empty( $admin_users ) ) {
			return false;
		}

		$admin_user = current( $admin_users );

		wp_set_current_user( $admin_user->ID );

		return true;
	}
}
