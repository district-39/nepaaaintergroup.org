<?php
/**
 * WP Public API Client
 *
 * HTTP client for communicating with the WP Public API for signature validation.
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Auth;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Config;
use WP_Error;

/**
 * HTTP client for WP Public API signature validation.
 *
 * Handles communication with the WP Public API /validate endpoint
 * for request signature verification.
 */
class WP_Public_Api_Client {

	/**
	 * Configuration instance.
	 *
	 * @var Config
	 */
	protected Config $config;

	/**
	 * HTTP request timeout in seconds.
	 *
	 * @var int
	 */
	protected int $timeout = 3;

	/**
	 * Cache of validation results by nonce (for current request only).
	 * Prevents duplicate API calls when permission callback is called multiple times.
	 *
	 * @var array<string, bool>
	 */
	protected array $validation_cache = array();

	/**
	 * Constructor.
	 *
	 * @param Config $config Configuration instance.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Validate signature by forwarding headers to WP Public API.
	 *
	 * Sends the signature headers to the WP Public API /validate endpoint
	 * and returns whether the signature is valid.
	 *
	 * @param array $signature_headers Array with signature header values.
	 *                                 Expected keys: 'nonce', 'origin', 'signature', 'bodyhash'.
	 *
	 * @return bool True if signature is valid, false otherwise.
	 */
	public function validate_signature( array $signature_headers ): bool {
		$nonce = $signature_headers['nonce'] ?? '';

		// Check cache to avoid duplicate API calls for the same nonce in the same request.
		if ( isset( $this->validation_cache[ $nonce ] ) ) {
			return $this->validation_cache[ $nonce ];
		}

		$validation_url = $this->get_validation_url();

		if ( empty( $validation_url ) ) {
			$this->log_error( 'WP Public API URL not configured' );
			$this->validation_cache[ $nonce ] = false;
			return false;
		}

		// Map to WP Public API header format (x-wp-* prefix).
		$request_headers = array(
			'x-wp-nonce'     => $signature_headers['nonce'] ?? '',
			'x-wp-origin'    => $signature_headers['origin'] ?? '',
			'x-wp-signature' => $signature_headers['signature'] ?? '',
			'x-wp-bodyhash'  => $signature_headers['bodyhash'] ?? '',
			'Content-Type'   => 'application/json',
		);

		$response = $this->make_request( $validation_url, $request_headers );

		if ( is_wp_error( $response ) ) {
			$this->log_error( 'WP Public API request failed: ' . $response->get_error_message() );
			$this->validation_cache[ $nonce ] = false;
			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$body_data     = json_decode( $response_body, true );

		// Check for successful response.
		if ( 200 !== $response_code ) {
			$this->log_error( "WP Public API returned status {$response_code}: {$response_body}" );
			$this->validation_cache[ $nonce ] = false;
			return false;
		}

		// Guard against malformed JSON or non-array responses.
		if ( ! is_array( $body_data ) ) {
			$this->log_error( 'WP Public API returned invalid JSON response' );
			$this->validation_cache[ $nonce ] = false;
			return false;
		}

		// Check validation result.
		$validated = $body_data['validated'] ?? false;

		if ( ! $validated ) {
			$this->log_error( 'Signature validation failed' );
			$this->validation_cache[ $nonce ] = false;
			return false;
		}

		// Cache successful validation.
		$this->validation_cache[ $nonce ] = true;
		return true;
	}

	/**
	 * Get the WP Public API validation endpoint URL.
	 *
	 * @return string The full URL to the /validate endpoint.
	 */
	protected function get_validation_url(): string {
		$base_url = $this->config->get_wp_public_api_url();

		if ( empty( $base_url ) ) {
			return '';
		}

		// Ensure no trailing slash and append /validate.
		return rtrim( $base_url, '/' ) . '/validate';
	}

	/**
	 * Make HTTP POST request to the validation endpoint.
	 *
	 * @param string $url     The URL to send the request to.
	 * @param array  $headers The headers to include in the request.
	 *
	 * @return array|WP_Error The response array or WP_Error on failure.
	 */
	protected function make_request( string $url, array $headers ) {
		$args = array(
			'method'  => 'POST',
			'headers' => $headers,
			'timeout' => $this->timeout,
			'body'    => '{}', // Empty JSON body for validation request.
		);

		return wp_remote_request( $url, $args );
	}

	/**
	 * Log error message for debugging.
	 *
	 * @param string $message The error message to log.
	 *
	 * @return void
	 */
	protected function log_error( string $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging.
			error_log( '[WP Public API Client] ' . $message );
		}
	}
}
