<?php
/**
 * Iframe Support Plugin
 *
 * Combines iframe access control and cookie handling functionality.
 * Manages iframe embedding security through CSP frame-ancestors and ensures
 * proper cookie attributes for iframe contexts.
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Iframe_Context_Detector;

/**
 * Handles iframe embedding security and cookie management
 *
 * This class provides two main functionalities:
 * 1. Controls iframe embedding access through CSP frame-ancestors by removing
 *    WordPress's default X-Frame-Options header and replacing it with a
 *    Content-Security-Policy header that explicitly allows embedding only from
 *    whitelisted origins.
 * 2. Handles cookie modifications for iframe contexts to ensure proper SameSite
 *    attributes are set on all WordPress cookies.
 */
class Iframe_Support {

	/**
	 * Initialize the iframe handler by registering all hooks
	 *
	 * This method should be called to activate all iframe-related functionality.
	 * Using a static init method allows for better control over when hooks are
	 * attached and makes it easier to disable or detach hooks if needed.
	 */
	public static function init(): void {
		$instance = new self();

		// Configure session for iframe context.
		$instance->configure_session();

		// === ACCESS CONTROL HOOKS ===
		// WordPress core attaches send_frame_options_header() to admin_init and login_init
		// at priority 10. We hook earlier (priority 5) to remove those hooks and set our
		// own CSP headers before WordPress core can set X-Frame-Options.
		add_action( 'admin_init', array( $instance, 'set_frame_ancestors_policy' ), 5 );
		add_action( 'login_init', array( $instance, 'set_frame_ancestors_policy' ), 5 );

		// For frontend and other contexts (REST API, AJAX, etc.), send_headers is the
		// primary hook that fires before headers are sent to the browser.
		add_action( 'send_headers', array( $instance, 'set_frame_ancestors_policy' ), 999 );

		// The wp_headers filter is the most reliable method as WordPress core uses this
		// to modify the headers array before sending. This acts as our primary defense
		// and should cover all contexts where headers are properly handled through WP APIs.
		add_filter( 'wp_headers', array( $instance, 'filter_frame_headers' ), 999 );

		// Template redirect hook at maximum priority to override WooCommerce and other plugins
		// that may set security headers on specific pages (my-account, checkout, etc.).
		// This ensures our CSP frame-ancestors policy is applied AFTER any plugin-specific headers.
		add_action( 'template_redirect', array( $instance, 'set_frame_ancestors_policy' ), PHP_INT_MAX );

		// === COOKIE HANDLING HOOKS ===
		// The shutdown function is the most reliable approach as it catches ALL WordPress
		// cookies set during the request and modifies them to include SameSite=None; Secure;
		// Partitioned attributes. This handles cookies set directly via setcookie() by WordPress
		// core (like wp-settings-*, wp_lang, wordpress_test_cookie) which have no hooks.
		add_action( 'shutdown', array( $instance, 'modify_all_cookie_headers' ), PHP_INT_MAX );

		// Hook into WordPress auth cookie setting actions. These fire BEFORE setcookie() is
		// called (pluggable.php:1069, 1086), allowing us to set our own cookies with proper
		// attributes that will be sent alongside (or instead of) the core cookies.
		add_action( 'set_auth_cookie', array( $instance, 'handle_auth_cookie' ), 10, 6 );
		add_action( 'set_logged_in_cookie', array( $instance, 'handle_logged_in_cookie' ), 10, 6 );
	}

	/**
	 * Configure session cookie settings for iframe context
	 */
	private function configure_session(): void {
		if ( ! headers_sent() ) {
			// Use session_set_cookie_params() for proper session cookie configuration.
			session_set_cookie_params(
				array(
					'lifetime' => 0,
					'path'     => '/',
					'domain'   => '',
					'secure'   => true,
					'httponly' => true,
					'samesite' => 'None',
				)
			);
		}
	}

	// ========================================================================
	// ACCESS CONTROL METHODS
	// ========================================================================

	/**
	 * Remove X-Frame-Options and set CSP frame-ancestors policy
	 */
	public function set_frame_ancestors_policy(): void {
		if ( ! headers_sent() ) {
			header_remove( 'X-Frame-Options' );

			// Also remove the WordPress core hooks that send X-Frame-Options.
			remove_action( 'admin_init', 'send_frame_options_header' );
			remove_action( 'login_init', 'send_frame_options_header' );

			// Set comprehensive Content-Security-Policy with multiple directives.
			$csp_value = $this->build_csp_header();
			header( 'Content-Security-Policy: ' . $csp_value, true );
		}
	}

	/**
	 * Build Content-Security-Policy header value
	 *
	 * Only sets the frame-ancestors directive to control which origins can embed
	 * the site in an iframe. Other directives (connect-src, script-src, etc.) are
	 * intentionally omitted to avoid breaking customer plugins that load resources
	 * from external CDNs.
	 *
	 * @return string CSP header value with frame-ancestors directive.
	 */
	private function build_csp_header(): string {
		$parent_origin   = Iframe_Context_Detector::get_parent_origin();
		$allowed_origins = Iframe_Context_Detector::get_allowed_parent_origins();

		/**
		 * Ensure parent origin is included in allowed origins.
		 * Regardless of fact that this scenario should be covered by 'self'.
		 */
		if ( ! empty( $parent_origin ) && ! in_array( $parent_origin, $allowed_origins, true ) ) {
			$allowed_origins[] = $parent_origin;
		}

		/**
		 * Combine allowed origins for frame-ancestors directive.
		 * Reason for this approach is the fact we expect nested iframes from multiple
		 * origins in some scenarios.
		 *
		 * airo.ai contains iframe to airo-sentinel.godaddy.com which in turn contains iframe to tmp WordPress website.
		 */
		$parent_origins = implode( ' ', $allowed_origins );

		return sprintf( "frame-ancestors 'self' %s", $parent_origins );
	}

	/**
	 * Filter frame-related headers and add CSP policy
	 *
	 * @param mixed $headers The headers array.
	 *
	 * @return array Modified headers array.
	 */
	public function filter_frame_headers( $headers ): array {
		if ( is_array( $headers ) ) {
			// Remove X-Frame-Options.
			if ( isset( $headers['X-Frame-Options'] ) ) {
				unset( $headers['X-Frame-Options'] );
			}

			// Add comprehensive Content-Security-Policy.
			$headers['Content-Security-Policy'] = $this->build_csp_header();
		}

		return $headers;
	}

	// ========================================================================
	// COOKIE HANDLING METHODS
	// ========================================================================

	/**
	 * Modify all Set-Cookie headers at shutdown to add SameSite=None; Secure; Partitioned
	 */
	public function modify_all_cookie_headers(): void {
		// Exit early if headers have already been sent.
		if ( headers_sent() ) {
			return;
		}

		$headers  = headers_list();
		$modified = false;

		// Check if any WordPress cookies need modification.
		foreach ( $headers as $header ) {
			if ( 0 === stripos( $header, 'Set-Cookie:' ) ) {
				$cookie_value = substr( $header, 12 );

				if ( $this->is_wordpress_cookie( $cookie_value ) &&
				     false === strpos( $cookie_value, 'SameSite=' ) ) {
					$modified = true;
					break;
				}
			}
		}

		// Rebuild all Set-Cookie headers if modification needed.
		if ( $modified ) {
			header_remove( 'Set-Cookie' );

			foreach ( $headers as $header ) {
				if ( 0 === stripos( $header, 'Set-Cookie:' ) ) {
					$cookie_value = substr( $header, 12 );

					if ( $this->is_wordpress_cookie( $cookie_value ) &&
					     false === strpos( $cookie_value, 'SameSite=' ) ) {
						$cookie_value .= '; SameSite=None; Secure; Partitioned';
					}

					header( 'Set-Cookie: ' . $cookie_value, false );
				}
			}
		}
	}

	/**
	 * Check if cookie is a WordPress cookie
	 *
	 * @param string $cookie_value Cookie value.
	 *
	 * @return bool
	 */
	private function is_wordpress_cookie( string $cookie_value ): bool {
		return (bool) preg_match( '/^(wordpress_[^=]+|wp-settings-[^=]+|wp_lang|comment_[^=]+)=/', $cookie_value );
	}

	/**
	 * Set a cookie with SameSite=None and Partitioned attributes
	 *
	 * @param string $name Cookie name.
	 * @param string $value Cookie value.
	 * @param int $expire Expiration timestamp.
	 * @param string $path Cookie path.
	 * @param string $domain Cookie domain.
	 * @param bool $secure Whether cookie is secure.
	 * @param bool $httponly Whether cookie is HTTP only.
	 *
	 * @return void
	 */
	private function set_cookie( string $name, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = true, bool $httponly = false ): void {
		if ( headers_sent() ) {
			return;
		}

		$cookie_string = $name . '=' . rawurlencode( $value );

		if ( $expire > 0 ) {
			$cookie_string .= '; expires=' . gmdate( 'D, d M Y H:i:s \G\M\T', $expire );
		}

		$cookie_string .= '; path=' . $path;

		if ( ! empty( $domain ) ) {
			$cookie_string .= '; domain=' . $domain;
		}

		if ( $secure ) {
			$cookie_string .= '; Secure';
		}

		if ( $httponly ) {
			$cookie_string .= '; HttpOnly';
		}

		$cookie_string .= '; SameSite=None; Partitioned';

		header( 'Set-Cookie: ' . $cookie_string, false );
	}

	/**
	 * Handle auth cookie setting
	 *
	 * @param string $auth_cookie Auth cookie value.
	 * @param int $expire Expiration timestamp.
	 * @param int $expiration Expiration timestamp.
	 * @param int $user_id User ID.
	 * @param string $scheme Cookie scheme.
	 * @param string $token Token.
	 */
	public function handle_auth_cookie( string $auth_cookie, int $expire, int $expiration, int $user_id, string $scheme, string $token ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- WordPress hook callback requires specific signature.
		if ( ! $this->are_constants_defined() ) {
			return;
		}

		$cookie_name = 'secure_auth' === $scheme
			? 'wordpress_sec_' . COOKIEHASH
			: 'wordpress_' . COOKIEHASH;

		$paths = array(
			ADMIN_COOKIE_PATH,
			PLUGINS_COOKIE_PATH,
			COOKIEPATH,
			SITECOOKIEPATH,
		);

		foreach ( $paths as $path ) {
			$this->set_cookie( $cookie_name, $auth_cookie, $expire, $path, COOKIE_DOMAIN, true, false );
		}
	}

	/**
	 * Handle logged-in cookie setting
	 *
	 * @param string $logged_in_cookie Logged in cookie value.
	 * @param int $expire Expiration timestamp.
	 * @param int $expiration Expiration timestamp.
	 * @param int $user_id User ID.
	 * @param string $scheme Cookie scheme.
	 * @param string $token Token.
	 */
	public function handle_logged_in_cookie( string $logged_in_cookie, int $expire, int $expiration, int $user_id, string $scheme, string $token ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- WordPress hook callback requires specific signature.
		if ( ! $this->are_constants_defined() ) {
			return;
		}

		$cookie_name = 'wordpress_logged_in_' . COOKIEHASH;
		$paths       = array( COOKIEPATH, SITECOOKIEPATH );

		foreach ( $paths as $path ) {
			$this->set_cookie( $cookie_name, $logged_in_cookie, $expire, $path, COOKIE_DOMAIN, true, false );
		}
	}

	/**
	 * Check if required WordPress constants are defined
	 *
	 * @return bool
	 */
	private function are_constants_defined(): bool {
		// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment -- Aligned for readability.
		return defined( 'COOKIEHASH' ) &&
		       defined( 'ADMIN_COOKIE_PATH' ) &&
		       defined( 'PLUGINS_COOKIE_PATH' ) &&
		       defined( 'COOKIEPATH' ) &&
		       defined( 'SITECOOKIEPATH' ) &&
		       defined( 'COOKIE_DOMAIN' );
		// phpcs:enable
	}
}
