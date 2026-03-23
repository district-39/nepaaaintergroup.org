<?php
/**
 * JWT Authentication Handler
 *
 * @package wp-site-designer-mu-plugins
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Auth;

use Exception;
use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Managers\Local_Auth_Manager;
use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Managers\Sso_Auth_Manager;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthException;
use GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth\AuthKeyFileCache;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Config;

/**
 * Handles JWT authentication for Site Designer activation requests
 */
class JWT_Auth {

	/**
	 * Configuration instance.
	 *
	 * @var Config
	 */
	protected Config $config;

	/**
	 * Constructor.
	 *
	 * @param Config $config Configuration instance.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Authenticate activation requests with a JWT in the Authorization header
	 *
	 * @param string $jwt The JWT token from the request header.
	 * @param string $site_id The site ID from the request header.
	 *
	 * @return bool Whether request is authenticated
	 */
	public function authenticate_request( string $jwt, string $site_id ): bool {
		if ( empty( $jwt ) ) {
			return false;
		}

		$env = $this->config->get_env();

		try {
			// Use AuthLib's AuthManager for all environments.
			// It handles different SSO hosts (prod, test, dev) automatically.
			return $this->validate_jwt_with_auth_manager( $jwt, $site_id, $env );

		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'JWT Authentication failed: ' . $e->getMessage() );
			}

			return false;
		}
	}

	/**
	 * Validate JWT using our extended AuthManager with SSO endpoints.
	 * Works for all environments (prod, test, dev) by using the appropriate SSO host.
	 * For local environments, uses empty host to trigger offline validation.
	 *
	 * @param string $jwt The JWT token.
	 * @param string $site_id The site ID to validate against.
	 * @param string $env The current environment.
	 *
	 * @return bool
	 */
	private function validate_jwt_with_auth_manager( string $jwt, string $site_id, string $env ): bool {
		// Initialize our custom auth manager with cache and app code.
		try {
			$upload_dir = function_exists( 'wp_upload_dir' ) ? wp_upload_dir() : array( 'basedir' => sys_get_temp_dir() );
			$cache_dir  = $upload_dir['basedir'] . '/gd-auth-cache';
			$cache      = new AuthKeyFileCache( $cache_dir, 60 * 60 * 12 ); // 12 hour TTL.

			if ( Config::ENV_PRODUCTION === $env ) {
				$auth_manager = new Sso_Auth_Manager( null, $cache, 'airo-site-designer' );
			} else {
				$auth_manager = new Local_Auth_Manager( null, $cache, 'airo-site-designer' );
			}
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Failed to initialize AuthManager: ' . $e->getMessage() );
			}

			return false;
		}

		// Determine SSO host based on environment.
		$auth_host = $this->config->get_sso_url();

		try {
			// Accept all auth types: basic, s2s, e2s, e2s2s, cert2s.
			// Use level 1 (low impact) which allows tokens valid for 1-180 days depending on 'per' flag.
			$payload = $auth_manager->getAuthPayloadShopper( $auth_host, $jwt, null, 1 );
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'JWT validation failed: ' . $e->getMessage() );
			}

			return false;
		}

		// AuthManager returns null if validation fails.
		if ( null === $payload ) {
			return false;
		}

		// Validate that the token belongs to the correct customer and site.
		// Now we can get the cid directly from our custom auth manager!
		return $this->validate_customer_and_site_from_auth_manager( $auth_manager, $site_id );
	}

	/**
	 * Validate that the JWT payload belongs to the correct customer and site.
	 * Uses our custom AuthManager to access the 'cid' claim directly.
	 *
	 * @param Sso_Auth_Manager|Local_Auth_Manager $auth_manager The auth manager with validated payload.
	 * @param string                              $site_id The site ID to validate against.
	 *
	 * @return bool
	 */
	private function validate_customer_and_site_from_auth_manager( $auth_manager, string $site_id ): bool {
		try {
			// Get the customer ID directly from our custom auth manager.
			$payload_customer_id = $auth_manager->getCustomerId();
		} catch ( AuthException $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Failed to get customer ID from JWT: ' . $e->getMessage() );
			}

			return false;
		}

		if ( empty( $payload_customer_id ) ) {
			return false;
		}

		// Get expected customer ID and site ID from WordPress config.
		$config_data        = defined( 'configData' ) ? json_decode( constant( 'configData' ), true ) : array();
		$config_customer_id = isset( $config_data['GD_CUSTOMER_ID'] ) ? $config_data['GD_CUSTOMER_ID'] : ( defined( 'GD_CUSTOMER_ID' ) ? constant( 'GD_CUSTOMER_ID' ) : null );
		$config_site_id     = isset( $config_data['GD_ACCOUNT_UID'] ) ? $config_data['GD_ACCOUNT_UID'] : ( defined( 'GD_ACCOUNT_UID' ) ? constant( 'GD_ACCOUNT_UID' ) : null );

		// Validate customer ID matches.
		if ( $config_customer_id !== $payload_customer_id ) {
			return false;
		}

		// Validate site ID matches.
		if ( ! empty( $site_id ) && $config_site_id !== $site_id ) {
			return false;
		}

		return true;
	}
}
