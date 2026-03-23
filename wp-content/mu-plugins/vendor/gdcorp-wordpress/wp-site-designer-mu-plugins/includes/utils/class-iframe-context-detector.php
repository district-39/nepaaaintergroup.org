<?php
/**
 * Iframe Context Detector Utility
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Utils;

/**
 * Detects if the current request is from Site Designer iframe
 */
class Iframe_Context_Detector {

	/**
	 * Singleton instance
	 *
	 * @var Iframe_Context_Detector|null
	 */
	private static ?self $instance = null;

	/**
	 * Request validator instance
	 *
	 * @var Request_Validator
	 */
	protected Request_Validator $request_validator;

	/**
	 * Allowed parent origins from configuration
	 *
	 * @var array
	 */
	protected array $allowed_origins;

	/**
	 * Cached validation result
	 *
	 * @var bool|null
	 */
	private ?bool $is_valid = null;

	/**
	 * Constructor
	 *
	 * @param Request_Validator $request_validator Request validator instance.
	 */
	protected function __construct( Request_Validator $request_validator ) {
		$this->request_validator = $request_validator;
	}

	/**
	 * Initialize singleton instance
	 *
	 * Ensures only one instance of the detector exists throughout the request lifecycle.
	 *
	 * @param Request_Validator|null $request_validator Optional request validator instance.
	 *
	 * @return self
	 */
	public static function init( ?Request_Validator $request_validator = null ): self {
		if ( is_null( self::$instance ) ) {
			// Create Request_Validator if not provided.

			$config           = new Config();
			$config_file_path = dirname( __DIR__, 2 ) . '/config/site-designer.json';
			$config->load_from_json( $config_file_path );

			if ( is_null( $request_validator ) ) {
				$request_validator = new Request_Validator( $config );
				$request_validator->parse();
			}

			self::$instance = new self( $request_validator );
			self::$instance->allowed_origins = $config->get_iframe_origins();
		}

		return self::$instance;
	}

	/**
	 * Check if current request is a valid Site Designer request
	 *
	 * Uses caching to avoid redundant validation checks within the same request.
	 *
	 * @param Request_Validator|null $request_validator Optional request validator instance.
	 *
	 * @return bool True if request is valid Site Designer iframe request, false otherwise.
	 */
	public static function is_valid_request( ?Request_Validator $request_validator = null ): bool {
		$instance = self::init( $request_validator );

		// Return cached result if already validated.
		if ( null !== $instance->is_valid ) {
			return $instance->is_valid;
		}

		// Perform validation and cache the result.
		$instance->is_valid = $instance->validate_request();

		return $instance->is_valid;
	}

	/**
	 * Get allowed parent window origins for non-iframe contexts.
	 *
	 * This method reads the allowed origins directly from the Site Designer
	 * configuration instead of using the cached iframe context detector
	 * instance. Use this in contexts such as popup windows where the iframe
	 * context is not available or `get_allowed_origins()` would not apply.
	 *
	 * @return array List of allowed parent origins.
	 */
	public static function get_allowed_parent_origins(): array {
		// Ensure instance is initialized.
		if ( null === self::$instance ) {
			self::init();
		}

		return self::$instance->allowed_origins;
	}

	/**
	 * Get the validated parent origin string
	 *
	 * Returns the origin of the request that was validated as coming from Site Designer.
	 * Only returns a value if is_valid_request() has been called and returned true.
	 *
	 * This is useful for postMessage communication where you need the exact origin
	 * without exposing the full allowed origins list in JavaScript.
	 *
	 * @return string|null The origin string (e.g., 'https://example.com'), or null if not validated.
	 */
	public static function get_parent_origin(): ?string {
		// Ensure instance is initialized.
		if ( null === self::$instance ) {
			self::init();
		}

		// If not validated yet, try to validate now.
		if ( null === self::$instance->is_valid ) {
			self::is_valid_request();
		}

		// Only return origin if request was validated as valid.
		if ( ! self::$instance->is_valid ) {
			return null;
		}

		$origin = self::$instance->request_validator->get_origin();

		// Additional check: ensure the origin is actually from an allowed parent origin.
		// Filter out origins that match our own WordPress site URL (iframe's own origin).
		if ( $origin ) {
			$origin_string    = $origin->get_origin_string();
			$wordpress_origin = self::get_wordpress_origin();

			// If the origin matches WordPress's own URL, it's not the parent - return null.
			if ( $origin_string === $wordpress_origin ) {
				// Try to get from session/cache if available.
				return self::get_cached_parent_origin();
			}

			// Cache this valid parent origin for future use.
			self::cache_parent_origin( $origin_string );

			return $origin_string;
		}

		// Try to get from session/cache if parsing failed.
		return self::get_cached_parent_origin();
	}

	/**
	 * Get WordPress's own origin (the iframe's origin)
	 *
	 * @return string WordPress origin.
	 */
	protected static function get_wordpress_origin(): string {
		// Determine scheme, checking both HTTPS server variable and X-Forwarded-Proto header
		// for sites behind proxies/load balancers (CDNs, reverse proxies, etc.).
		$scheme = 'http';
		if ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) ) ) {
			$scheme = 'https';
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ) ) ) {
			$scheme = 'https';
		}

		$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

		return $scheme . '://' . $host;
	}

	/**
	 * Cache the parent origin in a transient
	 *
	 * Uses a transient keyed by the current WordPress host to store the parent origin.
	 * This allows the parent origin to persist across requests without relying on sessions.
	 *
	 * @param string $origin The parent origin to cache.
	 * @return void
	 */
	protected static function cache_parent_origin( string $origin ): void {
		$cache_key     = 'site_designer_parent_origin_' . md5( self::get_wordpress_origin() );
		$cache_timeout = defined( 'HOUR_IN_SECONDS' ) ? HOUR_IN_SECONDS * 24 : 86400;
		set_transient( $cache_key, $origin, $cache_timeout );
	}

	/**
	 * Get cached parent origin from transient
	 *
	 * @return string|null The cached parent origin, or null if not found.
	 */
	protected static function get_cached_parent_origin(): ?string {
		$cache_key = 'site_designer_parent_origin_' . md5( self::get_wordpress_origin() );
		$cached    = get_transient( $cache_key );

		return is_string( $cached ) ? $cached : null;
	}

	/**
	 * Validate if request is from Site Designer iframe
	 *
	 * Performs multistep validation:
	 * 1. Checks if plugin is activated
	 * 2. Verifies Sec-Fetch-Dest header indicates iframe context
	 * 3. Validates origin against allowed origins list
	 *
	 * @return bool True if all validation checks pass, false otherwise.
	 */
	protected function validate_request(): bool {
		// First check if plugin is activated; options flag in the database.
		if ( ! self::is_plugin_activated() ) {
			return false;
		}

		// Verify request is coming from an iframe context.
		if ( ! $this->is_iframe() ) {
			return false;
		}

		// Validate the origin is in our allowed list (delegate to Request_Validator).
		return ( $this->request_validator->is_allowed_http_origin() || $this->request_validator->is_same_origin() );
	}

	/**
	 * Check if plugin is activated
	 *
	 * Reads the activation flag from WordPress options table.
	 *
	 * @return bool True if plugin is activated, false otherwise.
	 */
	public static function is_plugin_activated(): bool {
		return (bool) get_option( 'wp_site_designer_activated', false );
	}

	/**
	 * Check if the request fetch destination is an iframe
	 *
	 * Uses the Sec-Fetch-Dest security header to determine if the browser
	 * initiated this request from within an iframe context.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Sec-Fetch-Dest
	 * @return bool True if Sec-Fetch-Dest header indicates iframe context.
	 */
	protected function is_iframe(): bool {
		$sec_fetch_dest = $this->request_validator->get_header( 'HTTP_SEC_FETCH_DEST' );
		return 'iframe' === $sec_fetch_dest;
	}
}
