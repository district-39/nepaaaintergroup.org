<?php
/**
 * JWT Authentication Handler
 *
 * @package mcp-adapter-initializer
 * @since 0.1.1
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth\AuthKeyFileCache;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\GoDaddy\Auth\AuthManager;

/**
 * Handles JWT authentication for MCP requests
 */
class MCP_JWT_Authenticator {

	/**
	 * Authenticate MCP requests with a JWT in the X-GD-JWT header
	 *
	 * @param string $jwt The JWT token from the request header.
	 * @param string $site_id The site ID from the request header.
	 *
	 * @return bool Whether request is authenticated
	 */
	public function authenticate_request( string $jwt, string $site_id ): bool {
		if ( empty( $jwt ) || empty( $site_id ) ) {
			return false;
		}

		try {
			$env = $this->get_environment();

			if ( ! defined( 'GD_MCP_AUTH_WITH_SSO' ) && 'prod' !== $env ) {
				return $this->validate_jwt_offline( $jwt, $site_id );
			}

			// Production: Use full SSO validation.
			return $this->validate_jwt_with_sso( $jwt, $site_id, $env );

		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Get the current environment
	 *
	 * @return string Environment: 'prod', 'test', or 'dev'
	 */
	private function get_environment(): string {
		$env = getenv( 'SERVER_ENV' );
		if ( $env ) {
			return $env;
		}

		if ( defined( 'GD_TEMP_DOMAIN' ) && strpos( constant( 'GD_TEMP_DOMAIN' ), '.ide' ) !== false ) {
			return 'test';
		}

		return 'prod';
	}

	/**
	 * Validate JWT using SSO endpoints (production)
	 *
	 * @param string $jwt The JWT token.
	 * @param string $site_id The site ID to validate against.
	 * @param string $env The current environment.
	 *
	 * @return bool
	 */
	private function validate_jwt_with_sso( string $jwt, string $site_id, string $env ): bool {
		// Initialize the auth manager with cache and app code.
		$upload_dir   = function_exists( 'wp_upload_dir' ) ? wp_upload_dir() : array( 'basedir' => sys_get_temp_dir() );
		$cache_dir    = $upload_dir['basedir'] . '/gd-auth-cache';
		$cache        = new AuthKeyFileCache( $cache_dir, 60 * 60 * 12 ); // 12 hour TTL.
		$auth_manager = new AuthManager( null, $cache, 'gd-mcp' );
		$auth_host    = 'prod' === $env ? 'sso.godaddy.com' : "sso.$env-godaddy.com";

		$payload = $auth_manager->getAuthPayloadShopper( $auth_host, $jwt, array( 'basic', 'e2s', 's2s', 'e2s2s' ), 1 );

		// Return true if we got a valid payload.
		if ( null === $payload ) {
			return false;
		}

		return $this->validate_customer_and_site( $jwt, $site_id );
	}

	/**
	 * Offline JWT validation for dev/test environments.
	 * Validates JWT structure, claims, and basic signature verification.
	 *
	 * @param string $jwt The JWT token.
	 * @param string $site_id The site ID to validate against.
	 *
	 * @return bool
	 */
	private function validate_jwt_offline( string $jwt, string $site_id ): bool {
		$parts = explode( '.', $jwt );

		if ( count( $parts ) !== 3 ) {
			return false;
		}

		// Basic signature validation - ensure it's not completely fabricated.
		if ( ! $this->validate_jwt_signature_basic( $jwt ) ) {
			return false;
		}

		// Decode the payload.
		$payload_encoded = $parts[1];
		$payload_json    = base64_decode( strtr( $payload_encoded, '-_', '+/' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- Needed for JWT decoding.

		if ( ! $payload_json ) {
			return false;
		}

		$payload = json_decode( $payload_json, true );

		if ( ! $payload ) {
			return false;
		}

		// Extract customer ID from payload (handles both basic and delegation tokens).
		$payload_customer_id = $this->extract_claim( $payload, 'cid' );

		if ( null === $payload_customer_id ) {
			return false;
		}

		// Validate GoDaddy-specific claims to ensure this is a legitimate GoDaddy JWT.
		if ( ! $this->validate_godaddy_jwt_claims( $payload ) ) {
			return false;
		}

		// this has to be conditional for non-prod environments as some tokens may not have expiration claim set.
		if ( isset( $payload['exp'] ) ) {
			// Add 12 hours to payload expiration for dev/test.
			$payload_expiration = $payload['exp'] + ( 12 * 60 * 60 );

			// Check if current time is beyond the extended expiration.
			$current_time = time();
			if ( $current_time > $payload_expiration ) {
				return false;
			}
		}

		// Validate customer ID and site ID match.
		$config_data        = defined( 'configData' ) ? json_decode( constant( 'configData' ), true ) : array();
		$config_customer_id = $config_data['GD_CUSTOMER_ID'] ?? ( defined( 'GD_CUSTOMER_ID' ) ? constant( 'GD_CUSTOMER_ID' ) : null );
		$config_site_id     = $config_data['GD_ACCOUNT_UID'] ?? ( defined( 'GD_ACCOUNT_UID' ) ? constant( 'GD_ACCOUNT_UID' ) : null );

		if ( $config_customer_id !== $payload_customer_id ) {
			return false;
		}

		if ( $config_site_id !== $site_id ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate that the JWT belongs to the correct customer.
	 *
	 * @param string $jwt The JWT token.
	 * @param string $site_id The site ID to validate against.
	 *
	 * @return bool
	 */
	private function validate_customer_and_site( string $jwt, string $site_id ): bool {
		$parts = explode( '.', $jwt );

		if ( count( $parts ) !== 3 ) {
			return false;
		}

		$payload = base64_decode( strtr( $parts[1], '-_', '+/' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- Needed for JWT decoding.

		$json_payload = json_decode( $payload, true );

		// Extract customer ID from payload (handles both basic and delegation tokens).
		$payload_customer_id = $this->extract_claim( $json_payload, 'cid' );

		if ( null === $payload_customer_id ) {
			return false;
		}

		$config_data        = defined( 'configData' ) ? json_decode( constant( 'configData' ), true ) : array();
		$config_customer_id = $config_data['GD_CUSTOMER_ID'] ?? ( defined( 'GD_CUSTOMER_ID' ) ? constant( 'GD_CUSTOMER_ID' ) : null );
		$config_site_id     = $config_data['GD_ACCOUNT_UID'] ?? ( defined( 'GD_ACCOUNT_UID' ) ? constant( 'GD_ACCOUNT_UID' ) : null );

		return ( $config_customer_id === $payload_customer_id && $config_site_id === $site_id );
	}

	/**
	 * Basic JWT signature validation to prevent completely fabricated tokens.
	 *
	 * @param string $jwt The JWT token.
	 *
	 * @return bool
	 */
	private function validate_jwt_signature_basic( string $jwt ): bool {
		$parts = explode( '.', $jwt );

		if ( count( $parts ) !== 3 ) {
			return false;
		}

		$signature = $parts[2];

		// Basic checks for valid base64url encoding.
		if ( empty( $signature ) || strlen( $signature ) < 20 ) {
			return false;
		}

		// Check if signature contains valid base64url characters.
		if ( ! preg_match( '/^[A-Za-z0-9_-]+$/', $signature ) ) {
			return false;
		}

		// Decode header to check algorithm.
		$header_json = base64_decode( strtr( $parts[0], '-_', '+/' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
		$header      = json_decode( $header_json, true );

		if ( ! $header || ! isset( $header['alg'] ) ) {
			return false;
		}

		// Only allow specific algorithms.
		$allowed_algorithms = array( 'RS256', 'RS384', 'RS512', 'ES256', 'ES384', 'ES512' );
		if ( ! in_array( $header['alg'], $allowed_algorithms, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate GoDaddy-specific JWT claims when no explicit issuer is present.
	 *
	 * @param array $payload The decoded JWT payload.
	 *
	 * @return bool
	 */
	private function validate_godaddy_jwt_claims( array $payload ): bool {
		// Check for GoDaddy-specific claims that indicate this is a legitimate GoDaddy JWT.
		$required_gd_claims = array(
			'shopperId', // GoDaddy shopper ID.
			'plid', // Platform ID.
			'plt', // Platform type.
			'shard', // GoDaddy shard identifier.
		);

		// At least some GoDaddy-specific claims should be present.
		$found_claims = 0;
		foreach ( $required_gd_claims as $claim ) {
			if ( null !== $this->extract_claim( $payload, $claim ) ) {
				++$found_claims;
			}
		}

		// Require at least 2 GoDaddy-specific claims to be present.
		if ( $found_claims < 2 ) {
			return false;
		}

		// Validate platform type if present (should be numeric).
		$plt = $this->extract_claim( $payload, 'plt' );
		if ( null !== $plt && ! is_numeric( $plt ) ) {
			return false;
		}

		// Validate shard format if present (should be 4-digit string or null for delegation).
		$shard = $this->extract_claim( $payload, 'shard' );
		if ( null !== $shard && ! preg_match( '/^\d{4}$/', (string) $shard ) ) {
			return false;
		}

		// Validate JWT type if present (should be 'idp' for identity provider).
		if ( isset( $payload['typ'] ) && 'idp' !== $payload['typ'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Extract a claim from JWT payload.
	 * Handles both basic tokens (claim at top level) and delegation tokens (claim nested under auth type).
	 *
	 * @param array  $payload The decoded JWT payload.
	 * @param string $claim_name The name of the claim to extract.
	 *
	 * @return mixed The claim value, or null if not found.
	 */
	private function extract_claim( array $payload, string $claim_name ) {
		// For basic tokens, claim is at the top level.
		if ( isset( $payload[ $claim_name ] ) ) {
			return $payload[ $claim_name ];
		}

		// For delegation tokens (e2s, s2s, e2s2s), claim is nested under the auth type key.
		$auth_type = $payload['auth'] ?? 'basic';

		if ( 'basic' !== $auth_type && isset( $payload[ $auth_type ] ) && is_array( $payload[ $auth_type ] ) ) {
			if ( isset( $payload[ $auth_type ][ $claim_name ] ) ) {
				return $payload[ $auth_type ][ $claim_name ];
			}
		}

		return null;
	}
}
