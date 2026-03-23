<?php
/**
 * Request Origin Model
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Models;

/**
 * Request Origin Model
 *
 * Represents a parsed request origin with scheme, host, and port components.
 * Used for validating and comparing origins in CORS and iframe embedding contexts.
 */
class Request_Origin {
	/**
	 * The scheme of the request origin (e.g., 'https').
	 *
	 * @var string
	 */
	protected string $scheme = '';
	/**
	 * The host of the request origin (e.g., 'example.com').
	 *
	 * @var string
	 */
	protected string $host = '';

	/**
	 * The port of the request origin (e.g., '443').
	 *
	 * @var int
	 */
	protected int $port = 0;

	/**
	 * The domain of the request origin (e.g., 'example.com').
	 *
	 * @var string
	 */
	protected string $domain = '';

	/**
	 * The subdomain of the request origin (e.g., 'subdomain').
	 *
	 * @var string
	 */
	protected string $subdomain = '';

	/**
	 * Constructor.
	 *
	 * @param string $scheme The scheme of the request origin.
	 * @param string $host The host of the request origin.
	 * @param int    $port The port of the request origin.
	 */
	public function __construct( string $scheme, string $host, int $port ) {
		$this->scheme = $scheme;
		$this->host   = $host;
		$this->port   = $port;
	}

	/**
	 * Get the full origin string (e.g., 'https://example.com:443').
	 *
	 * @return string The full origin string.
	 */
	public function get_origin_string(): string {
		$origin = $this->scheme . '://' . $this->host;
		if ( ( 'http' === $this->scheme && 80 !== $this->port ) || ( 'https' === $this->scheme && 443 !== $this->port ) ) {
			$origin .= ':' . $this->port;
		}

		return $origin;
	}

	/**
	 * Get the scheme of the request origin.
	 *
	 * @return string The scheme of the request origin.
	 */
	public function get_scheme(): string {
		return $this->scheme;
	}

	/**
	 * Get the host of the request origin.
	 *
	 * @return string The host of the request origin.
	 */
	public function get_host(): string {
		return $this->host;
	}

	/**
	 * Get the port of the request origin.
	 *
	 * @return int The port of the request origin.
	 */
	public function get_port(): int {
		return $this->port;
	}
}
