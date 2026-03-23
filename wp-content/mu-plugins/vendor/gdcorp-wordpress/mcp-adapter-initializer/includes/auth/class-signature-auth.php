<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Auth;

/**
 * WP Request Signature Authenticator
 *
 * Validates requests using WP Request Signature headers by forwarding
 * to the WP Public API /validate endpoint.
 *
 * @package mcp-adapter-initializer
 * @since 1.2.0
 */
class Signature_Auth {

	/**
	 * Required signature headers
	 *
	 * @var array
	 */
	private const REQUIRED_HEADERS = array(
		'HTTP_X_NONCE',
		'HTTP_X_ORIGIN',
		'HTTP_X_SIGNATURE',
		'HTTP_X_BODYHASH',
	);

	/**
	 * Fixed timeout in seconds for all API requests
	 *
	 * @var int
	 */
	private const REQUEST_TIMEOUT = 3;

	/**
	 * Check if the request has signature headers
	 *
	 * @return bool True if all required signature headers are present
	 */
	public function has_signature_headers(): bool {
		foreach ( self::REQUIRED_HEADERS as $header ) {
			if ( empty( $_SERVER[ $header ] ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Authenticate the request using WP Request Signature
	 *
	 * Assumes signature headers are present (caller should verify via has_signature_headers() first).
	 *
	 * @return bool True if signature is valid
	 */
	public function authenticate_request(): bool {
		$headers = $this->get_validation_headers();

		return $this->validate_signature( $headers );
	}

	/**
	 * Get headers formatted for WP Public API validation
	 *
	 * Maps incoming headers (x-nonce, x-origin, etc.) to the format
	 * expected by WP Public API (x-wp-nonce, x-wp-origin, etc.)
	 *
	 * @return array Headers for validation request
	 */
	private function get_validation_headers(): array {
		return array(
			'x-wp-nonce'     => $this->get_header( 'HTTP_X_NONCE' ),
			'x-wp-origin'    => $this->get_header( 'HTTP_X_ORIGIN' ),
			'x-wp-signature' => $this->get_header( 'HTTP_X_SIGNATURE' ),
			'x-wp-bodyhash'  => $this->get_header( 'HTTP_X_BODYHASH' ),
		);
	}

	/**
	 * Get a sanitized header value
	 *
	 * @param string $header_key The $_SERVER key for the header.
	 * @return string The sanitized header value
	 */
	private function get_header( string $header_key ): string {
		if ( ! isset( $_SERVER[ $header_key ] ) ) {
			return '';
		}
		return sanitize_text_field( wp_unslash( $_SERVER[ $header_key ] ) );
	}

	/**
	 * Validate signature by calling WP Public API
	 *
	 * Makes a single validation request with a fixed 3-second timeout.
	 * No retries are performed to prevent blocking worker threads.
	 *
	 * @param array $headers Headers to forward to validation endpoint.
	 * @return bool True if signature is valid
	 */
	private function validate_signature( array $headers ): bool {
		$validate_url = $this->get_wp_public_api_url() . '/validate';

		$response = wp_remote_post(
			$validate_url,
			array(
				'headers' => $headers,
				'timeout' => self::REQUEST_TIMEOUT,
			)
		);

		// Check for WP_Error (network issues, timeouts, etc.).
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$this->log_auth_attempt( false, 'WP Public API request failed: ' . $error_message );
			return false;
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		// Only 200 status code is considered successful.
		if ( 200 !== $status_code ) {
			$this->log_auth_attempt( false, 'WP Public API returned status: ' . $status_code );
			return false;
		}

		// Success - parse and validate response.
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			$this->log_auth_attempt( false, 'Invalid response from WP Public API' );
			return false;
		}

		$validated = $body['validated'] ?? false;

		$this->log_auth_attempt( $validated, $validated ? '' : 'Signature validation failed' );

		return (bool) $validated;
	}

	/**
	 * Get the WP Public API URL based on environment
	 *
	 * @return string The WP Public API base URL (without /validate suffix)
	 */
	private function get_wp_public_api_url(): string {
		// Allow override via constant (should be base URL without /validate suffix).
		if ( defined( 'GD_WP_PUBLIC_API_URL' ) ) {
			return constant( 'GD_WP_PUBLIC_API_URL' );
		}

		$env = $this->get_environment();

		switch ( $env ) {
			case 'dev':
				return 'https://wp-api.wpsecurity.dev-godaddy.com/api/v1';
			case 'test':
				return 'https://wp-api.wpsecurity.test-godaddy.com/api/v1';
			default:
				return 'https://wp-api.wpsecurity.godaddy.com/api/v1';
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
			$env = strtolower( $env );
			if ( in_array( $env, array( 'prod', 'test', 'dev' ), true ) ) {
				return $env;
			}
		}

		if ( defined( 'GD_TEMP_DOMAIN' ) && strpos( constant( 'GD_TEMP_DOMAIN' ), '.ide' ) !== false ) {
			return 'test';
		}

		return 'prod';
	}

	/**
	 * Log authentication attempt for debugging
	 *
	 * @param bool   $success Whether authentication succeeded.
	 * @param string $reason  Reason for failure (if applicable).
	 */
	private function log_auth_attempt( bool $success, string $reason = '' ): void {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$message = sprintf(
			'MCP Auth [Signature]: %s%s',
			$success ? 'SUCCESS' : 'FAILED',
			$reason ? " - $reason" : ''
		);

		error_log( $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
