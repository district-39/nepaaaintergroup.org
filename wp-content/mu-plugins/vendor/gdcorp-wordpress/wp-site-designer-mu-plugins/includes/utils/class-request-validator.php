<?php
/**
 * Request Validator Utility
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Utils;

use GoDaddy\WordPress\Plugins\SiteDesigner\Models\Request_Origin;
use WP_Error;

/**
 * Validates request origins and handles rate limiting for API security
 *
 * Provides utilities for:
 * - Request data parsing and storage
 * - Origin header validation for CORS security
 * - URL parsing and comparison
 * - Rate limiting using WordPress transients
 */
class Request_Validator {

	/**
	 * Configuration instance
	 *
	 * @var Config
	 */
	protected Config $config;

	/**
	 * Full request URL
	 *
	 * @var string
	 */
	protected string $url = '';

	/**
	 * Request scheme (http or https)
	 *
	 * @var string
	 */
	protected string $scheme = '';

	/**
	 * Request path
	 *
	 * @var string
	 */
	protected string $path = '';

	/**
	 * Query parameters as array
	 *
	 * @var array<string, mixed>
	 */
	protected array $query_args = array();

	/**
	 * Request headers as array
	 *
	 * @var array<string, string>
	 */
	protected array $headers = array();

	/**
	 * Request cookies as array
	 *
	 * @var array<string, string>
	 */
	protected array $cookies = array();

	/**
	 * Parsed origin (scheme, host, port)
	 *
	 * @var ?Request_Origin
	 */
	protected ?Request_Origin $origin = null;

	/**
	 * Raw server data for reference
	 *
	 * @var array<string, mixed>
	 */
	protected array $server_data = array();

	/**
	 * Initialize Request_Validator with server data
	 *
	 * Takes optional server data array, defaults to $_SERVER if empty.
	 * This design allows for easy testing by passing mock data.
	 *
	 * @param Config $config Configuration instance.
	 */
	public function __construct( Config $config ) {
		$this->config      = $config;
		$this->server_data = $_SERVER;
	}

	/**
	 * Parse server data and populate all object properties
	 *
	 * Extracts and sanitizes:
	 * - URL components (scheme, host, path, query)
	 * - Domain and subdomain
	 * - Headers from HTTP_* server variables
	 * - Cookies
	 * - Origin information
	 *
	 * @param array $server_data Server variables ($_SERVER).
	 *
	 * @return void
	 */
	public function parse( array $server_data = array() ): void {
		if ( ! empty( $server_data ) ) {
			$this->server_data = $server_data;
		}
		// Parse URL components.
		$this->parse_url_components();

		// Parse headers from $_SERVER.
		$this->parse_headers();

		// Parse cookies.
		$this->parse_cookies();

		// Parse origin.
		$this->parse_origin_from_headers();
	}

	/**
	 * Parse URL components from server data
	 *
	 * Constructs the full URL and extracts path and query parameters.
	 *
	 * @return void
	 */
	public function parse_url_components(): void {
		// Determine scheme.
		$scheme = 'http';
		if ( ! empty( $this->server_data['HTTPS'] ) && 'off' !== strtolower( $this->server_data['HTTPS'] ) ) {
			$scheme = 'https';
		} elseif ( ! empty( $this->server_data['HTTP_X_FORWARDED_PROTO'] ) && 'https' === strtolower( $this->server_data['HTTP_X_FORWARDED_PROTO'] ) ) {
			$scheme = 'https';
		}

		// Store scheme.
		$this->scheme = $scheme;

		// Get host.
		$host = $this->server_data['HTTP_HOST'] ?? $this->server_data['SERVER_NAME'] ?? '';
		if ( ! empty( $host ) ) {
			$host = sanitize_text_field( wp_unslash( $host ) );
		}

		// Get request URI.
		$request_uri = $this->server_data['REQUEST_URI'] ?? '';
		if ( ! empty( $request_uri ) ) {
			$request_uri = sanitize_text_field( wp_unslash( $request_uri ) );
		}

		// Construct full URL.
		$this->url = $scheme . '://' . $host . $request_uri;

		// Parse path (without query string).
		$uri_parts  = explode( '?', $request_uri, 2 );
		$this->path = $uri_parts[0];

		// Parse query arguments.
		if ( isset( $uri_parts[1] ) ) {
			parse_str( $uri_parts[1], $this->query_args );
			// Sanitize query args.
			$this->query_args = array_map( 'sanitize_text_field', $this->query_args );
		}
	}

	/**
	 * Parse headers from server data
	 *
	 * Extracts HTTP headers from $_SERVER variables (HTTP_* keys).
	 * Sanitizes and caches them in the headers property.
	 *
	 * @return void
	 */
	protected function parse_headers(): void {
		foreach ( $this->server_data as $key => $value ) {
			// Only process HTTP_* headers.
			if ( 0 === strpos( $key, 'HTTP_' ) ) {
				// Sanitize header value.
				if ( is_string( $value ) ) {
					$this->headers[ $key ] = sanitize_text_field( wp_unslash( $value ) );
				}
			}
		}

		// Also include CONTENT_TYPE and CONTENT_LENGTH if present.
		if ( isset( $this->server_data['CONTENT_TYPE'] ) ) {
			$this->headers['CONTENT_TYPE'] = sanitize_text_field( wp_unslash( $this->server_data['CONTENT_TYPE'] ) );
		}
		if ( isset( $this->server_data['CONTENT_LENGTH'] ) ) {
			$this->headers['CONTENT_LENGTH'] = sanitize_text_field( wp_unslash( $this->server_data['CONTENT_LENGTH'] ) );
		}
	}

	/**
	 * Parse cookies from server data
	 *
	 * Extracts cookies from HTTP_COOKIE or $_COOKIE.
	 *
	 * @return void
	 */
	protected function parse_cookies(): void {
		// Use $_COOKIE if available for better parsing.
		if ( isset( $_COOKIE ) && is_array( $_COOKIE ) ) {
			foreach ( $_COOKIE as $name => $value ) {
				if ( is_string( $value ) ) {
					$this->cookies[ sanitize_text_field( $name ) ] = sanitize_text_field( $value );
				}
			}
		} elseif ( isset( $this->server_data['HTTP_COOKIE'] ) ) {
			// Manual parsing if $_COOKIE not available.
			$cookie_string = $this->server_data['HTTP_COOKIE'];
			$cookie_pairs  = explode( ';', $cookie_string );

			foreach ( $cookie_pairs as $pair ) {
				$pair_parts = explode( '=', trim( $pair ), 2 );
				if ( count( $pair_parts ) === 2 ) {
					$name                   = sanitize_text_field( $pair_parts[0] );
					$value                  = sanitize_text_field( $pair_parts[1] );
					$this->cookies[ $name ] = $value;
				}
			}
		}
	}

	/**
	 * Parse origin from headers
	 *
	 * Extracts origin from HTTP_ORIGIN or HTTP_REFERER headers.
	 * Only stores the origin if it's valid (not a WP_Error).
	 *
	 * @return void
	 */
	protected function parse_origin_from_headers(): void {
		$origin_header = $this->get_header( 'HTTP_ORIGIN' );

		// Fallback to Referer if Origin not set.
		if ( empty( $origin_header ) ) {
			$origin_header = $this->get_header( 'HTTP_REFERER' );
		}

		if ( ! empty( $origin_header ) ) {
			$parsed_origin = $this->parse_origin( $origin_header );

			// Only store if parsing was successful.
			if ( ! is_wp_error( $parsed_origin ) ) {
				$this->origin = $parsed_origin;
			}
		}
	}

	/**
	 * Get full request URL
	 *
	 * @return string
	 */
	public function get_url(): string {
		return $this->url;
	}

	/**
	 * Get request scheme
	 *
	 * @return string
	 */
	public function get_scheme(): string {
		return $this->scheme;
	}

	/**
	 * Get request path
	 *
	 * @return string
	 */
	public function get_path(): string {
		return $this->path;
	}

	/**
	 * Get query arguments
	 *
	 * @return array<string, mixed>
	 */
	public function get_query_args(): array {
		return $this->query_args;
	}

	/**
	 * Get all headers
	 *
	 * @return array<string, string>
	 */
	public function get_headers(): array {
		return $this->headers;
	}

	/**
	 * Get specific header value
	 *
	 * Performs lookup from cached headers array.
	 *
	 * @param string $header_key The header key (e.g., 'HTTP_ORIGIN', 'HTTP_REFERER').
	 *
	 * @return string The sanitized header value, or empty string if not found.
	 */
	public function get_header( string $header_key ): string {
		return $this->headers[ $header_key ] ?? '';
	}

	/**
	 * Get all cookies
	 *
	 * @return array<string, string>
	 */
	public function get_cookies(): array {
		return $this->cookies;
	}

	/**
	 * Get specific cookie value
	 *
	 * @param string $cookie_name The cookie name.
	 *
	 * @return string The cookie value, or empty string if not found.
	 */
	public function get_cookie( string $cookie_name ): string {
		return $this->cookies[ $cookie_name ] ?? '';
	}

	/**
	 * Get parsed origin
	 *
	 * Returns the parsed origin as a Request_Origin object or null if not set/invalid.
	 *
	 * @return Request_Origin|null
	 */
	public function get_origin(): ?Request_Origin {
		return $this->origin;
	}

	/**
	 * Validate Origin header for server-to-server API requests
	 *
	 * Checks if the stored Origin header is in the allowed origins list.
	 * Used for CORS validation in REST API endpoints and AJAX requests.
	 *
	 * @return bool True if origin is valid and allowed, false otherwise.
	 */
	public function validate_origin_header(): bool {
		$origin_header = $this->get_header( 'HTTP_ORIGIN' );

		// Origin header is required for validation.
		if ( empty( $origin_header ) ) {
			return false;
		}

		return $this->is_allowed_http_origin();
	}

	/**
	 * Check if origin is in allowed list
	 *
	 * Compares the stored origin against the whitelist of allowed origins.
	 * Comparison includes scheme (http/https), host, and port matching.
	 * Supports wildcard subdomain matching (e.g., *.example.com matches app.example.com).
	 *
	 * @param array $allowed_origins The allowed origins to check against.
	 *
	 * @return bool True if origin is allowed, false otherwise.
	 */
	public function is_allowed_origin( array $allowed_origins ): bool {
		// Get the already-parsed origin from the request.
		$origin_parsed = $this->get_origin();

		// No origin to validate.
		if ( null === $origin_parsed ) {
			return false;
		}

		// Get allowed origins from constants, filterable by plugins/themes.
		// $allowed_origins = apply_filters( 'wp_site_designer_allowed_origins', $allowed_origins, $this->origin );
		// Commented due to security concerns where one can inject unwanted origins via filter.

		foreach ( $allowed_origins as $allowed_origin_url ) {
			// Parse the allowed origin.
			$allowed_parsed = $this->parse_origin( $allowed_origin_url );

			// Skip invalid allowed origin entries.
			if ( is_wp_error( $allowed_parsed ) ) {
				continue;
			}

			// Check if this is a wildcard pattern.
			$is_wildcard = str_contains( $allowed_origin_url, '*' );

			if ( $is_wildcard ) {
				// Handle wildcard matching.
				if ( $this->match_wildcard_origin( $origin_parsed, $allowed_parsed ) ) {
					return true;
				}
			} elseif ( $this->match_origins( $origin_parsed, $allowed_parsed ) ) {
				// Exact origin matching.
				return true;
			}
		}

		// Origin not found in allowed list.
		return false;
	}

	/**
	 * Check if origin is in allowed general HTTP origins
	 *
	 * @return bool True if origin is allowed, false otherwise.
	 */
	public function is_allowed_http_origin(): bool {
		return $this->is_allowed_origin( $this->config->get_iframe_origins() );
	}

	/**
	 * Check if origin is in allowed API origins
	 *
	 * @return bool True if origin is allowed, false otherwise.
	 */
	public function is_allowed_api_origin(): bool {
		return $this->is_allowed_origin( $this->config->get_api_origins() );
	}

	/**
	 * Validate request origin against registered site URL.
	 *
	 * @return bool
	 */
	public function is_same_origin(): bool {
		$site_url    = get_site_url();
		$site_origin = $this->parse_origin( $site_url );

		$request_origin = $this->get_origin();

		if ( null === $request_origin || is_wp_error( $site_origin ) ) {
			return false;
		}

		return $this->match_origins( $request_origin, $site_origin );
	}

	/**
	 * Match origin against wildcard pattern
	 *
	 * Supports patterns like https://*.example.com matching https://app.example.com
	 *
	 * @param Request_Origin $origin_parsed Parsed origin object to test.
	 * @param Request_Origin $pattern_parsed Parsed wildcard pattern origin.
	 *
	 * @return bool True if origin matches wildcard pattern, false otherwise.
	 */
	protected function match_wildcard_origin( Request_Origin $origin_parsed, Request_Origin $pattern_parsed ): bool {
		// Get origin components.
		$origin_scheme = $origin_parsed->get_scheme();
		$origin_host   = $origin_parsed->get_host();
		$origin_port   = $origin_parsed->get_port();

		// Get pattern components.
		$pattern_scheme = $pattern_parsed->get_scheme();
		$pattern_host   = $pattern_parsed->get_host();
		$pattern_port   = $pattern_parsed->get_port();

		// Scheme and port must match exactly.
		if ( $origin_scheme !== $pattern_scheme || $origin_port !== $pattern_port ) {
			return false;
		}

		// Convert wildcard host pattern to regex.
		$pattern_regex = str_replace( array( '.', '*' ), array( '\.', '.*' ), $pattern_host );

		// Match the host against the pattern.
		return 1 === preg_match( '/^' . $pattern_regex . '$/i', $origin_host );
	}

	/**
	 * Parse and normalize origin components (scheme, host, port)
	 *
	 * Extracts the scheme, host, and port from an origin URL using parse_url().
	 * Returns null if the URL is malformed or missing required components.
	 *
	 * Examples:
	 * - 'https://example.com' -> ['scheme' => 'https', 'host' => 'example.com', 'port' => 443]
	 * - 'http://localhost:8080' -> ['scheme' => 'http', 'host' => 'localhost', 'port' => 8080]
	 *
	 * @param string $origin The origin URL.
	 *
	 * @return Request_Origin|WP_Error{
	 *
	 * } Array with 'scheme', 'host', 'port' keys, or null if invalid.
	 */
	public function parse_origin( string $origin ) {
		if ( empty( $origin ) ) {
			return new WP_Error( 'empty_origin', __( 'Empty Origin', 'wp-site-designer-mu-plugins' ) );
		}

		// Use PHP's parse_url to extract components.
		$origin_url = parse_url( $origin );
		$scheme     = $origin_url['scheme'] ?? '';
		$host       = $origin_url['host'] ?? '';

		// Scheme and host are required for a valid origin.
		if ( empty( $scheme ) || empty( $host ) ) {
			return new WP_Error( 'invalid_origin', __( 'Invalid Origin Format', 'wp-site-designer-mu-plugins' ) );
		}

		// If port is not set, use scheme to determine default port.
		if ( ! isset( $origin_url['port'] ) ) {
			switch ( $scheme ) {
				case 'http':
					$port = 80;
					break;
				case 'https':
					$port = 443;
					break;
				default:
					$port = 0;
			}
		} else {
			$port = intval( $origin_url['port'] );
		}

		return new Request_Origin( $scheme, $host, $port );
	}

	/**
	 * Compare two parsed origins for equality
	 *
	 * Performs exact matching on scheme, host, and port.
	 * Both origins must be Request_Origin objects.
	 *
	 * @param Request_Origin $origin1 First origin object.
	 * @param Request_Origin $origin2 Second origin object.
	 *
	 * @return bool True if all components match exactly, false otherwise.
	 */
	public function match_origins( Request_Origin $origin1, Request_Origin $origin2 ): bool {
		// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment -- Aligned for readability.
		return $origin1->get_scheme() === $origin2->get_scheme() &&
				$origin1->get_host() === $origin2->get_host() &&
				$origin1->get_port() === $origin2->get_port();
		// phpcs:enable
	}

	/**
	 * Check rate limit using fixed window counter
	 *
	 * Implements a fixed-window rate limiting algorithm using WordPress transients.
	 * The time window is divided into fixed buckets, and requests are counted per bucket.
	 *
	 * Note: This uses transients, which may have race conditions in high-concurrency scenarios.
	 * The implementation may allow slightly over the limit, but this is acceptable for most use cases.
	 *
	 * Example usage:
	 * - Rate limit by IP: check_rate_limit_sliding( $ip_address, 10, 60 )
	 * - Rate limit by user: check_rate_limit_sliding( 'user_' . $user_id, 100, 3600 )
	 *
	 * @param string $identifier Unique identifier (IP address, customer_id, JWT hash, etc.).
	 * @param int    $max_requests Maximum requests allowed within the time window. Default 10.
	 * @param int    $window_seconds Time window in seconds. Default 60 (1 minute).
	 *
	 * @return bool True if within limit (request allowed), false if rate limited (request should be blocked).
	 */
	public function check_rate_limit_sliding( string $identifier, int $max_requests = 10, int $window_seconds = 60 ): bool {
		$now        = time();
		$window_key = floor( $now / $window_seconds ); // Fixed time bucket.

		// Generate unique cache key for this identifier and time window.
		$key = 'rate_limit_' . hash( 'sha256', $identifier . '_' . $window_key );

		// Get current count for this time window from cache.
		$count = (int) get_transient( $key );

		// Check if limit exceeded.
		if ( $count >= $max_requests ) {
			return false; // Rate limit exceeded.
		}

		// Increment counter (set TTL to 2x window to account for edge cases).
		// Note: Race conditions may allow slightly over limit in high-concurrency scenarios.
		set_transient( $key, $count + 1, $window_seconds * 2 );

		return true; // Request allowed.
	}
}
