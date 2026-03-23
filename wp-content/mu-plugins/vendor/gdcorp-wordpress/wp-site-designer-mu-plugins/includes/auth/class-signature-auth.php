<?php
/**
 * Signature Authentication Handler
 *
 * Handles request signature validation for Site Designer API requests.
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Auth;

/**
 * Handles signature-based authentication for Site Designer activation requests.
 *
 * Validates requests by extracting signature headers and forwarding them
 * to the WP Public API for cryptographic verification.
 *
 * Headers expected from client:
 * - x-nonce: {random}_{timestamp} - Unique request identifier
 * - x-origin: Site domain (e.g., example.com)
 * - x-signature: HMAC-SHA256 signature (64 hex chars)
 * - x-bodyhash: SHA256 hash of request body (64 hex chars)
 */
class Signature_Auth {

	/**
	 * Header name for nonce.
	 *
	 * @var string
	 */
	const HEADER_NONCE = 'x-nonce';

	/**
	 * Header name for origin.
	 *
	 * @var string
	 */
	const HEADER_ORIGIN = 'x-origin';

	/**
	 * Header name for signature.
	 *
	 * @var string
	 */
	const HEADER_SIGNATURE = 'x-signature';

	/**
	 * Header name for body hash.
	 *
	 * @var string
	 */
	const HEADER_BODYHASH = 'x-bodyhash';

	/**
	 * Required length for hex-encoded SHA256 hash.
	 *
	 * @var int
	 */
	const HEX_HASH_LENGTH = 64;

	/**
	 * WP Public API client instance.
	 *
	 * @var WP_Public_Api_Client
	 */
	protected WP_Public_Api_Client $api_client;

	/**
	 * Constructor.
	 *
	 * @param WP_Public_Api_Client $api_client WP Public API client instance.
	 */
	public function __construct( WP_Public_Api_Client $api_client ) {
		$this->api_client = $api_client;
	}

	/**
	 * Authenticate a request using signature validation.
	 *
	 * Extracts signature headers from the request and validates them
	 * via the WP Public API. 
	 * 
	 * Body integrity verification is is done via the x-bodyhash header.
	 *
	 * @param array $headers Request headers array (from WP_REST_Request::get_headers() or $_SERVER).
	 *
	 * @return bool True if signature is valid, false otherwise.
	 */
	public function authenticate_request( array $headers ): bool {
		// Extract and validate signature headers.
		$signature_headers = $this->extract_signature_headers( $headers );

		if ( empty( $signature_headers ) ) {
			$this->log_error( 'Failed to extract valid signature headers' );
			return false;
		}

		// Validate header formats.
		if ( ! $this->validate_header_formats( $signature_headers ) ) {
			$this->log_error( 'Invalid signature header format' );
			return false;
		}

		// Forward to WP Public API for validation.
		return $this->api_client->validate_signature( $signature_headers );
	}

	/**
	 * Check if the request contains signature headers.
	 *
	 * @param array $headers Request headers array.
	 *
	 * @return bool True if all required signature headers are present.
	 */
	public function has_signature_headers( array $headers ): bool {
		$normalized = $this->normalize_headers( $headers );

		return isset( $normalized[ self::HEADER_NONCE ] ) &&
			isset( $normalized[ self::HEADER_ORIGIN ] ) &&
			isset( $normalized[ self::HEADER_SIGNATURE ] ) &&
			isset( $normalized[ self::HEADER_BODYHASH ] );
	}

	/**
	 * Extract signature headers from request.
	 *
	 * Handles both WP_REST_Request header format and $_SERVER format.
	 *
	 * @param array $headers Request headers array.
	 *
	 * @return array Extracted signature headers or empty array if missing.
	 */
	protected function extract_signature_headers( array $headers ): array {
		$normalized = $this->normalize_headers( $headers );

		// Check if all required headers are present (directly on normalized array to avoid re-normalizing).
		if ( ! isset( $normalized[ self::HEADER_NONCE ] ) ||
			! isset( $normalized[ self::HEADER_ORIGIN ] ) ||
			! isset( $normalized[ self::HEADER_SIGNATURE ] ) ||
			! isset( $normalized[ self::HEADER_BODYHASH ] ) ) {
			return array();
		}

		return array(
			'nonce'     => $normalized[ self::HEADER_NONCE ],
			'origin'    => $normalized[ self::HEADER_ORIGIN ],
			'signature' => $normalized[ self::HEADER_SIGNATURE ],
			'bodyhash'  => $normalized[ self::HEADER_BODYHASH ],
		);
	}

	/**
	 * Normalize headers to lowercase with x- prefix format.
	 *
	 * Handles conversion from:
	 * - WP_REST_Request format: ['x_nonce' => ['value']] or ['x-nonce' => 'value']
	 * - $_SERVER format: ['HTTP_X_NONCE' => 'value']
	 *
	 * @param array $headers Raw headers array.
	 *
	 * @return array Normalized headers with lowercase keys.
	 */
	protected function normalize_headers( array $headers ): array {
		$normalized = array();

		foreach ( $headers as $key => $value ) {
			// Handle array values (WP_REST_Request format).
			if ( is_array( $value ) ) {
				$value = $value[0] ?? '';
			}

			// Convert key to lowercase.
			$key = strtolower( $key );

			// Handle $_SERVER HTTP_X_* format.
			if ( 0 === strpos( $key, 'http_x_' ) ) {
				// Convert http_x_nonce to x-nonce.
				$key = 'x-' . str_replace( '_', '-', substr( $key, 7 ) );
			} elseif ( 0 === strpos( $key, 'x_' ) ) {
				// Convert x_nonce to x-nonce (WP_REST_Request underscore format).
				$key = 'x-' . str_replace( '_', '-', substr( $key, 2 ) );
			}

			$normalized[ $key ] = sanitize_text_field( (string) $value );
		}

		return $normalized;
	}

	/**
	 * Validate the format of signature headers.
	 *
	 * Ensures headers meet expected format requirements:
	 * - nonce: {random}_{timestamp} format
	 * - signature: 64-character hex string
	 * - bodyhash: 64-character hex string
	 *
	 * @param array $headers Extracted signature headers.
	 *
	 * @return bool True if all headers have valid format.
	 */
	protected function validate_header_formats( array $headers ): bool {
		// Validate nonce format: {random}_{timestamp}.
		$nonce = $headers['nonce'] ?? '';
		if ( empty( $nonce ) || strpos( $nonce, '_' ) === false ) {
			return false;
		}

		// Validate signature is 64-char hex.
		$signature = $headers['signature'] ?? '';
		if ( ! $this->is_valid_hex_hash( $signature ) ) {
			return false;
		}

		// Validate bodyhash is 64-char hex.
		$bodyhash = $headers['bodyhash'] ?? '';
		if ( ! $this->is_valid_hex_hash( $bodyhash ) ) {
			return false;
		}

		// Origin should be non-empty.
		$origin = $headers['origin'] ?? '';
		if ( empty( $origin ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if a string is a valid 64-character hex hash.
	 *
	 * @param string $value The value to check.
	 *
	 * @return bool True if valid hex hash.
	 */
	protected function is_valid_hex_hash( string $value ): bool {
		if ( strlen( $value ) !== self::HEX_HASH_LENGTH ) {
			return false;
		}

		return ctype_xdigit( $value );
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
			error_log( '[Signature Auth] ' . $message );
		}
	}
}
