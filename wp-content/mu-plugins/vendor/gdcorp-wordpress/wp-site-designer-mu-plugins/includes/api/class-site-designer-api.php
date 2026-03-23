<?php
/**
 * Activation Endpoint API
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Api;

use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\JWT_Auth;
use GoDaddy\WordPress\Plugins\SiteDesigner\Auth\Signature_Auth;
use GoDaddy\WordPress\Plugins\SiteDesigner\Utils\Request_Validator;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST API endpoint for Site Designer activation/deactivation
 */
class Site_Designer_Api {

	/**
	 * JWT authenticator instance
	 *
	 * @var JWT_Auth
	 */
	private JWT_Auth $jwt_auth;

	/**
	 * Signature authenticator instance
	 *
	 * @var Signature_Auth
	 */
	private Signature_Auth $signature_auth;

	/**
	 * Request validator instance
	 *
	 * @var Request_Validator
	 */
	private Request_Validator $request_validator;

	/**
	 * Constructor
	 *
	 * @param Request_Validator $request_validator Request validator instance.
	 * @param JWT_Auth          $jwt_auth JWT authenticator instance.
	 * @param Signature_Auth    $signature_auth Signature authenticator instance.
	 */
	public function __construct( Request_Validator $request_validator, JWT_Auth $jwt_auth, Signature_Auth $signature_auth ) {
		$this->request_validator = $request_validator;
		$this->jwt_auth          = $jwt_auth;
		$this->signature_auth    = $signature_auth;
	}

	/**
	 * Register hooks
	 */
	public function register_endpoints(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_filter( 'gdl_unrestricted_rest_endpoints', array( $this, 'add_unrestricted_endpoints' ) );
	}

	/**
	 * Register REST API routes
	 */
	public function register_routes(): void {
		register_rest_route(
			'wp-site-designer/v1',
			'/activate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_activation' ),
				'permission_callback' => function ( $request ) {
					return $this->with_rate_limit( $request, 50, 300 ); // 50 per 5 minutes
				},
			)
		);

		register_rest_route(
			'wp-site-designer/v1',
			'/deactivate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_deactivation' ),
				'permission_callback' => function ( $request ) {
					return $this->with_rate_limit( $request, 50, 300 ); // 50 per 5 minutes
				},
			)
		);

		register_rest_route(
			'wp-site-designer/v1',
			'/status',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'handle_status' ),
				'permission_callback' => function ( $request ) {
					return $this->with_rate_limit( $request, 60, 60 ); // 50 per 5 minutes
				},
			)
		);
	}

	/**
	 * Validate request with rate limiting.
	 *
	 * Rate limit identifier priority:
	 * 1. site_id - Best option, directly identifies the site
	 * 2. x-origin - When site_id is absent, identifies the WordPress domain and is used as a general fallback
	 * 3. JWT hash - For JWT auth, same token = same bucket and used only after header-based identifiers
	 *
	 * Note: We intentionally do NOT use nonce as identifier since nonces are
	 * unique per request, which would bypass rate limiting entirely.
	 *
	 * Return type omitted (not WP_Error|bool) for PHP 7.4 compatibility - union types require PHP 8.0+
	 *
	 * @param WP_REST_Request $request Request object.
	 * @param int             $max_requests Maximum requests allowed.
	 * @param int             $window_seconds Time window in seconds.
	 *
	 * @return bool|WP_Error
	 */
	public function with_rate_limit( WP_REST_Request $request, int $max_requests, int $window_seconds ) {
		$site_id = $request->get_header( 'X-Site-ID' ) ?? $request->get_param( 'site_id' ) ?? '';
		$origin  = $request->get_header( 'X-Origin' ); // Site domain for signature auth.
		$jwt     = $request->get_header( 'X-GD-JWT' );

		// Determine rate limit identifier with fallback chain.
		if ( ! empty( $site_id ) ) {
			$identifier = $site_id;
		} elseif ( ! empty( $origin ) ) {
			$identifier = hash( 'sha256', $origin );
		} elseif ( ! empty( $jwt ) ) {
			$identifier = hash( 'sha256', $jwt );
		} else {
			// No valid identifier available - deny request.
			return new WP_Error(
				'missing_identifier',
				'Request missing required identification headers.',
				array( 'status' => 400 )
			);
		}

		if ( ! $this->request_validator->check_rate_limit_sliding( $identifier, $max_requests, $window_seconds ) ) {
			return new WP_Error(
				'rate_limit_exceeded',
				'Too many requests. Please try again later.',
				array( 'status' => 429 )
			);
		}

		return $this->validate_request( $request );
	}

	/**
	 * Validate request using signature auth (primary) or JWT (fallback).
	 *
	 * Tries signature-based authentication first if signature headers are present.
	 * Falls back to JWT authentication if signature headers are not present.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool True if authentication succeeds, false otherwise.
	 */
	public function validate_request( WP_REST_Request $request ): bool {
		// First check if origin is allowed at all.
		if ( ! $this->request_validator->is_allowed_api_origin() ) {
			return false;
		}

		// Get all headers for signature validation.
		$headers = $request->get_headers();

		// Try signature auth first if headers are present.
		if ( $this->signature_auth->has_signature_headers( $headers ) ) {
			return $this->signature_auth->authenticate_request( $headers );
		}

		// Fallback to JWT authentication.
		return $this->validate_jwt( $request );
	}

	/**
	 * Validate JWT from request headers (fallback authentication).
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool
	 */
	public function validate_jwt( WP_REST_Request $request ): bool {
		// Confirm that header exists.
		$jwt = $request->get_header( 'X-GD-JWT' );
		if ( empty( $jwt ) ) {
			return false;
		}

		$site_id = $request->get_header( 'X-Site-ID' ) ?? $request->get_param( 'site_id' ) ?? '';

		return $this->jwt_auth->authenticate_request( $jwt, $site_id );
	}

	/**
	 * Handle activation request
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function handle_activation( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Parameter required by WordPress REST API callback signature.
		update_option( 'wp_site_designer_activated', true, true );
		update_option( \GDMU_SITE_DESIGNER_PRESENT_OPTION, '1', false );

		return rest_ensure_response(
			array(
				'status'    => 'activated',
				'timestamp' => time(),
			)
		);
	}

	/**
	 * Handle deactivation request
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function handle_deactivation( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Parameter required by WordPress REST API callback signature.
		delete_option( 'wp_site_designer_activated' );

		return rest_ensure_response(
			array(
				'status'    => 'deactivated',
				'timestamp' => time(),
			)
		);
	}

	/**
	 * Handle status request
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function handle_status( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Parameter required by WordPress REST API callback signature.
		$is_activated = (bool) get_option( 'wp_site_designer_activated', false );

		return rest_ensure_response(
			array(
				'activated' => $is_activated,
				'timestamp' => time(),
			)
		);
	}

	/**
	 * Add unrestricted endpoints
	 *
	 * @param array $endpoints Endpoints array.
	 *
	 * @return array
	 */
	public function add_unrestricted_endpoints( array $endpoints ): array {

		$endpoints[] = '/wp-site-designer/v1';

		return $endpoints;
	}
}
