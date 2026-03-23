<?php
/**
 * Trait for accessing JWT claims, especially customer ID
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Managers;

use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthException;

/**
 * Provides methods to decode JWT and access claims like customer ID.
 */
trait Has_Customer_Id {

	/**
	 * Stores the last decoded raw payload data for accessing additional claims.
	 *
	 * @var array
	 */
	protected array $payload = array();

	/**
	 * Get a specific claim from the last validated JWT payload.
	 *
	 * Handles both basic tokens (claim at top level) and delegation tokens
	 * (e2s, s2s, e2s2s) where claims are nested under the auth type key.
	 *
	 * @param string $claim_name The claim name (e.g., 'cid', 'sub', 'iss').
	 *
	 * @return string The claim value.
	 *
	 * @throws AuthException If no JWT has been validated yet or if the requested claim does not exist.
	 */
	public function getClaim( string $claim_name ): string {
		if ( empty( $this->payload ) ) {
			throw new AuthException( 'No JWT has been validated yet.' );
		}

		// For basic tokens, claim is at the top level.
		if ( isset( $this->payload[ $claim_name ] ) ) {
			return (string) $this->payload[ $claim_name ];
		}

		// For delegation tokens (e2s, s2s, e2s2s), claims are nested under the auth type key.
		$auth_type = $this->payload['auth'] ?? 'basic';

		if ( 'basic' !== $auth_type && isset( $this->payload[ $auth_type ] ) && is_array( $this->payload[ $auth_type ] ) ) {
			if ( isset( $this->payload[ $auth_type ][ $claim_name ] ) ) {
				return (string) $this->payload[ $auth_type ][ $claim_name ];
			}
		}

		throw new AuthException( sprintf( 'Claim %s has not been set.', $claim_name ) );
	}

	/**
	 * Get the customer ID (cid) from the last validated JWT.
	 *
	 * @return string
	 */
	public function getCustomerId(): string {
		try {
			$cid = $this->getClaim( 'cid' );
		} catch ( AuthException $e ) {
			$cid = '';
		}

		return $cid;
	}

	/**
	 * Get all raw claims from the last validated JWT payload.
	 *
	 * @return array
	 */
	public function getRawPayload(): array {
		return $this->payload;
	}

	/**
	 * Decode JWT payload to extract claims.
	 * Uses base64url decoding consistent with AuthManager::base64DecodeUrl().
	 *
	 * @param string $jwt The JWT token.
	 *
	 * @return array The decoded payload array or empty array on failure.
	 */
	private function decodePayload( string $jwt ): array {
		$parts = explode( '.', $jwt );

		if ( count( $parts ) !== 3 ) {
			return array();
		}

		// Decode payload using base64url decoding (same as AuthLib).
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Required for JWT decoding.
		$payload_json = base64_decode( strtr( $parts[1], '-_', '+/' ) );

		if ( ! $payload_json ) {
			return array();
		}

		$payload_data = json_decode( $payload_json, true );

		if ( ! is_array( $payload_data ) ) {
			return array();
		}

		return $payload_data;
	}
}
