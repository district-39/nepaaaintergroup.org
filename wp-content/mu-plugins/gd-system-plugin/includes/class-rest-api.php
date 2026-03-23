<?php

namespace WPaaS;

use WP_Application_Passwords;
use WP_Error;
use WP_Http_Cookie;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class REST_API {

	const NPS_CONFIG_CACHING_INTERVAL = 24 * 60 * 60; // 24 hours

	const NPS_CONFIG_OPTION_NAME = 'wpaas_nps_config_%s_%s';

	/**
	 * Instance of the API.
	 *
	 * @var API_Interface
	 */
	private $api;

	/**
	 * Array of REST API namespaces.
	 *
	 * @var array
	 */
	private $namespaces = [];

	/**
	 * Instance of the Cache.
	 *
	 * @var Cache_V2
	 */
	private $cache;

	/**
	 * Instance of the Smart Update class.
	 *
	 * @var Smart_Update
	 */
	private $smart_updates;

	/**
	 * Array of cached validated endpoints
	 * WordPress calls twice permission_callback, once for Allow headers,
	 * and second time for the callback
	 *
	 * @var array
	 */
	private $validated = [];

	public function __construct( API_Interface $api, Cache_V2 $cache, Smart_Update $smart_updates ) {
		$this->api = $api;

		$this->namespaces['v1'] = 'wpaas/v1';
		$this->cache = $cache;
		$this->smart_updates = $smart_updates;
		add_action( 'rest_api_init', [ $this, 'toggle_xmlrpc' ] );
        add_action( 'rest_api_init', [ $this, 'toggle_llms' ] );
		add_action( 'rest_api_init', [ $this, 'flush_cache' ] );
		add_action( 'rest_api_init', [ $this, 'flush_cache_cdn_status' ] );
		add_action( 'rest_api_init', [ $this, 'dismiss_note' ] );
		add_action( 'rest_api_init', [ $this, 'dismiss_whats_new' ] );
		add_action( 'rest_api_init', [ $this, 'diagnostics_data' ] );
		add_action( 'rest_api_init', [ $this, 'feature_flag' ] );
        add_action( 'rest_api_init', [ $this, 'activity_log' ] );
        add_action( 'rest_api_init', [ $this, 'get_2_fa_status' ] );
        add_action( 'rest_api_init', [ $this, 'toggle_trustedsite' ] );
        add_action( 'rest_api_init', [ $this, 'get_trusted_site_sso' ] );
        add_action( 'rest_api_init', [ $this, 'get_nps_form_configuration' ] );
        add_action( 'rest_api_init', [ $this, 'nps_form_submit' ] );
        add_action( 'rest_api_init', [ $this, 'nps_form_dismiss' ] );
        add_action( 'rest_api_init', [ $this, 'feedback_form_submit' ] );

		if (!(defined('WPAAS_SMART_PLUGIN_UPDATE_DISABLED') && WPAAS_SMART_PLUGIN_UPDATE_DISABLED)) {
			add_action( 'rest_api_init', [ $this, 'get_failed_smart_updates' ] );
			add_action( 'rest_api_init', [ $this, 'smart_update_notice_dismiss' ] );
			add_action( 'rest_api_init', [ $this, 'plugins_to_update' ] );
			add_action( 'rest_api_init', [ $this, 'update_plugins' ] );
		}

	}
    /**
     * Validate signature
     *
     * @param  array  $headers
     *
     * @return bool
     */
    public function validate_signature( $headers ) {
		$api_url = sprintf('%s/validate', $this->api->wp_public_api_url());

		$response = Plugin::remote_request(
		  esc_url_raw(  $api_url ),
		  [
			  'method'   => 'POST',
			  'blocking' => true,
			  'headers'  => array_merge( [
				  'Accept'       => 'application/json',
				  'Content-Type' => 'application/json',
			  ], $headers ),
		  ]
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		return $body['validated'] ?? false;
    }

    public function is_administrator() {
        $user = wp_get_current_user();

        if ( in_array( 'administrator', $user->roles, true ) ) {
            return true;
        }

        return false;
    }

	/**
	 * POST route to flush cache.
	 */
	public function flush_cache() {
		register_rest_route( $this->namespaces['v1'], 'flush-cache', [
			'methods'             => 'POST',
			'permission_callback' => function($request) {
				if ( isset($this->validated['flush_cache']) ) {
					return $this->validated['flush_cache'];
				}
				$all_headers = $request->get_headers();
				$signature = [];
				$signature['x-wp-nonce'] = $all_headers['x_nonce'][0] ?? '';
				$signature['x-wp-origin'] = $all_headers['x_origin'][0] ?? '';
				$signature['x-wp-signature'] = $all_headers['x_signature'][0] ?? '';
				$signature['x-wp-bodyhash'] = $all_headers['x_bodyhash'][0] ?? '';

				if ($signature['x-wp-origin'] != GD_TEMP_DOMAIN) {
					return false;
				}
				$this->validated['flush_cache'] = $this->validate_signature($signature);

				return $this->validated['flush_cache'];
			},
			'callback'            => function () {
				$this->cache->do_ban();

				return [ 'success' => true ];
			},
		] );

	}

	/**
	 * Get route to fetch all failed plugin updates from smart update service.
	 */
	public function get_failed_smart_updates() {
		register_rest_route(
			$this->namespaces['v1'],
			'smart-updates',
			array(
				'methods'             => 'GET',
				'permission_callback' => function( $request ) {
					return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
				},
				'callback'            => function () {
					$data = $this->api->get_failed_smart_updates();
					if ( empty( $data ) ) {

						return array(
							'success' => false,
							'data'    => array()
						);

					}
					$plugins = get_plugins();
					$notices = array();

					foreach ( $data as $plugin_upgrade ) {
						if ( ! empty( $plugin_upgrade['upgradeData'] ) ) {
							if ( ! Smart_Update::isPluginUpdated( $plugins, $plugin_upgrade ) ) {
								$notices[] = array(
									'dismiss_id' => $plugin_upgrade['updateId'],
									'message'    => 'A plugin failed to update! ' . $plugin_upgrade['upgradeData'][0]['name'] . ' was updated but it caused issues on your site so we switched it back to its original state. <a href="'. get_option( 'home' ) . '/wp-admin/plugins.php' .'">Click here to update manually.</a>',
								);
							}
						}
					}
					return array(
						'success' => true,
						'data'    => $notices,
					);

				},
			)
		);

	}

    public function get_trusted_site_sso() {
        register_rest_route(
            $this->namespaces['v1'],
            'trusted-site-sso',
            array(
                'methods'             => 'GET',
                'permission_callback' => function( $request ) {
                    return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
                },
                'callback'            => function() {
                    $response = $this->api->get_trusted_site_sso_url();

                    if ( empty( $response ) || ! empty( $response['error'] ) ) {
                        return array(
                            'success' => false,
                            'data'    => array(),
                        );
                    }

                    return array(
                        'success' => true,
                        'data'    => $response,
                    );
                },
            )
        );
    }

	/**
	 * Post route to dismiss smart update notice.
	 */
	public function smart_update_notice_dismiss() {
		register_rest_route(
			$this->namespaces['v1'],
			'smart-updates/dismiss',
			array(
				'methods'             => 'POST',
				'permission_callback' => function( $request ) {
					return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
				},
				'callback'            => function ( $request ) {

					$dismiss_id = $request->get_param( 'dismiss_id' );
					$data       = $this->api->smart_update_notice_dismiss( $dismiss_id );

					return array(
						'success' => true,
						'data'    => $data,
					);

				},
			)
		);

	}

    /**
     * POST route to enable/disable XML-RPC
     */
    public function toggle_xmlrpc() {
        register_rest_route( $this->namespaces['v1'], 'toggle-xmlrpc', [
            'methods'             => 'POST',
            'permission_callback' => [ $this, 'is_administrator' ],
            'callback'            => function () {

                $is_xmlrpc_enabled = get_option( 'is_xmlrpc_enabled', 'enabled' );
                $status  = 'enabled';

                if ( 'enabled' === $is_xmlrpc_enabled ) {
                    $status = 'disabled';
                }

                update_option( 'is_xmlrpc_enabled', $status );
            },
        ] );
    }

    /**
     * POST route to enable/disable Trusted Site
     */
    public function toggle_trustedsite() {
        register_rest_route( $this->namespaces['v1'], 'toggle-trusted-site', [
            'methods'             => 'POST',
            'permission_callback' => [ $this, 'is_administrator' ],
            'callback'            => function () {

                $is_trustedsite_enabled = get_option( 'gd_trustedsite_show_badge', '1' );
                $status  = '1';

                if ( '1' === $is_trustedsite_enabled ) {
                    $status = '0';
                }

                update_option( 'gd_trustedsite_show_badge', $status );
            },
        ] );
    }

    /**
     * POST route to enable/disable LLMS.TXT
     */
    public function toggle_llms() {
        register_rest_route( $this->namespaces['v1'], 'toggle-llms', [
            'methods'             => 'POST',
            'permission_callback' => [ $this, 'is_administrator' ],
            'callback'            => function () {

                $is_llms_enabled = get_option( 'is_llms_enabled', 'disabled' );
                $status  = 'disabled';

                if ( 'disabled' === $is_llms_enabled ) {
                    $status = 'enabled';
                }

                update_option( 'is_llms_enabled', $status );

                return [ 'status' => $status ];
            },
        ] );
    }

	/**
	 * Post route to get flush cache status
	 */
	public function flush_cache_cdn_status() {
		register_rest_route(
			$this->namespaces['v1'],
			'flush-cache/status',
			[
				'methods'             => 'GET',
				'permission_callback' => [ $this, 'is_administrator' ],
				'callback'            => function () {
					$invalidation_id = get_option('gd_system_polling_invalidation_id');
					if ( $invalidation_id ) {
						$raw = $this->api->flush_cache_cdn_status( $invalidation_id );
						$status = $raw['status'] ?? 'PENDING';

						if ( $raw === null || $status === 'SUCCESS' || $status === 'FAILED') {
							update_option( 'gd_system_polling_invalidation_id', false );
						}

						if ( $raw === null || $status === 'SUCCESS' || $status === 'PENDING' ) {
							return new \WP_REST_Response(
								array(
									'code'    => 'OK',
									'message' => 'Flush is in ' . $status . ' status',
									'raw'	  => $raw,
									'data'    => array(
										'flush_status' => $status,
									),
								),
								200
							);
						} else {
							return new \WP_REST_Response(
								array(
									'code'    => 'Bad Request',
									'message' => 'Flush failed.',
									'raw'	  => $raw,
									'data'    => array(
										'flush_status' => $status,
									),
								),
								400
							);
						}
					}

					return new \WP_REST_Response(
						array(
							'code'    => 'NOT_FOUND',
							'message' => 'There is no invalidation id',
						),
						404
					);
				}
			] );
	}

	/**
	 * Post route to dismiss notification
	 *
	 * @return \WP_REST_Response
	 */
	public function dismiss_note() {
		register_rest_route(
			$this->namespaces['v1'],
			'dismiss-notice',
			[
				'methods'             => 'POST',
				'permission_callback' => [ $this, 'is_administrator' ],
				'args'                => array(
					'id' => array(
						'required'          => true,
						'description'       => 'Notice id',
						'type'              => 'string',
						'validate_callback' => function( $param, $request, $key ) {
							return is_string( $param );
						},
					),
				),
				'callback'            => function ($request) {

					$id = $request->get_param( 'id' );
					update_option( "wpaas_dismissed_$id", true );

					return new \WP_REST_Response(
						array(
							'status'    => 'OK',
						),
						200
					);
				}
			] );
	}
	/**
	 * Post route to dismiss what's new notification
	 *
	 * @return \WP_REST_Response
	 */
	public function dismiss_whats_new() {
		register_rest_route(
			$this->namespaces['v1'],
			'dismiss-whats-new',
			[
				'methods'             => 'POST',
				'permission_callback' => [ $this, 'is_administrator' ],
				'callback'            => function ($request) {
					update_option( "wpaas_whats_new_dismissed", true );

					return new \WP_REST_Response(
						array(
							'status'    => 'OK',
						),
						200
					);
				}
			] );
	}

	/**
	 * GET route to collect all diagnostics data
	 */
	public function diagnostics_data() {
		register_rest_route( $this->namespaces['v1'], 'diagnostics', [
			'methods'             => 'POST',
			'permission_callback' => function($request) {

				if ( isset($this->validated['diagnostics']) ) {
					return $this->validated['diagnostics'];
				}
				$all_headers = $request->get_headers();
				$signature = [];
				$signature['x-wp-nonce'] = $all_headers['x_nonce'][0] ?? '';
				$signature['x-wp-origin'] = $all_headers['x_origin'][0] ?? '';
				$signature['x-wp-signature'] = $all_headers['x_signature'][0] ?? '';
				$signature['x-wp-bodyhash'] = $all_headers['x_bodyhash'][0] ?? '';

				if ( $signature['x-wp-origin'] != GD_TEMP_DOMAIN ) {
					return false;
				}
				$this->validated['diagnostics'] = $this->validate_signature($signature);

				return $this->validated['diagnostics'];
			},
			'callback'            => function () {
				$diagnostics = new Diagnostics();

				return $diagnostics->get_diagnostics_data();
			},
		] );
	}

	public function feature_flag() {
		register_rest_route( $this->namespaces['v1'], 'feature_flag', [
			'methods'             => 'GET',
			'args' => [
				'name' => [
					'validate_callback' => function($param, $request, $key) {
						return is_string( $param );
					}
				],
				'default_value' => [
					'validate_callback' => function($param, $request, $key) {
						return is_bool(filter_var($param, FILTER_VALIDATE_BOOLEAN));
					}
				],
			],
			'permission_callback' => function(){
				return is_user_logged_in();
			} ,
			'callback'            => function ($request) {

				$name = $request->get_param( 'name' );
				$default_value = filter_var($request->get_param( 'default_value' ), FILTER_VALIDATE_BOOLEAN);;
				$value = $GLOBALS['wpaas_feature_flag']->get_feature_flag_value($name, $default_value);

				return new \WP_REST_Response(
					array(
						'name' => $name,
						'value' => $value,
					),
					200
				);

			},
		] );
	}

    public function activity_log() {
        // Register a new REST endpoint to retrieve data from the user_activity_log table
        register_rest_route( $this->namespaces['v1'], '/activity-logs', array(
            'methods' => 'GET',
            'permission_callback' => function($request) {
                if ( isset($this->validated['get_activity_log']) ) {
                    return $this->validated['get_activity_log'];
                }
                $all_headers = $request->get_headers();
                $signature = [];
                $signature['x-wp-nonce'] = $all_headers['x_nonce'][0] ?? '';
                $signature['x-wp-origin'] = $all_headers['x_origin'][0] ?? '';
                $signature['x-wp-signature'] = $all_headers['x_signature'][0] ?? '';
                $signature['x-wp-bodyhash'] = $all_headers['x_bodyhash'][0] ?? '';

                if ($signature['x-wp-origin'] != GD_TEMP_DOMAIN) {
                    return false;
                }
                $this->validated['get_activity_log'] = $this->validate_signature($signature);

                return $this->validated['get_activity_log'];
            },
            'args' => array(
                'number' => array(
                    'validate_callback' => function ($param, $request, $key) {
                        // Validate that the number parameter is an integer between 1 and 1000
                        return is_numeric( $param ) && intval( $param ) >= 1 && intval( $param ) <= 1000;
                    },
                    'default' => 100, // Default to returning 100 records if the number parameter is not provided or is invalid
                ),
            ),
            'callback' => function ( WP_REST_Request $request ) {
                // Get the number of records to return from the request parameters
                $number = $request->get_param( 'number' );

                global $wpdb;
                // Query the user_activity_log table to get the specified number of records, ordered by timestamp in descending order
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}wpaas_activity_log ORDER BY timestamp DESC LIMIT %d",
                        intval( $number )
                    )
                );

                // Return the results
                return new \WP_REST_Response(
                    array(
                        'data' => $results,
                    ),
                    200
                );
            }
        ) );
    }

	/**
	 * GET route to collect all plugins that require update
	 */
	public function plugins_to_update() {
		register_rest_route( $this->namespaces['v1'], 'plugin-updates', [
			'methods'             => 'GET',
			'permission_callback' => function($request) {

				if ( isset($this->validated['plugin-updates']) ) {
					return $this->validated['plugin-updates'];
				}
				$all_headers = $request->get_headers();
				$signature = [];
				$signature['x-wp-nonce'] = $all_headers['x_nonce'][0] ?? '';
				$signature['x-wp-origin'] = $all_headers['x_origin'][0] ?? '';
				$signature['x-wp-signature'] = $all_headers['x_signature'][0] ?? '';
				$signature['x-wp-bodyhash'] = $all_headers['x_bodyhash'][0] ?? '';

				if ( $signature['x-wp-origin'] != GD_TEMP_DOMAIN ) {
					return false;
				}

				$this->validated['plugin-updates'] = $this->validate_signature($signature);

				return $this->validated['plugin-updates'];
			},
			'callback'            => function () {
				return new \WP_REST_Response(
					array(
						'code' => 'OK',
						'data' => $this->smart_updates->get_plugin_updates(),
					),
					200
				);
			},
		] );
	}

	/**
	 * POST route to update list of passed plugins
	 */
	public function update_plugins() {
		register_rest_route( $this->namespaces['v1'], 'plugin-updates', [
			'methods'             => 'POST',
			'permission_callback' => function($request) {

				if ( isset($this->validated['plugin-updates']) ) {
					return $this->validated['plugin-updates'];
				}
				$all_headers = $request->get_headers();
				$signature = [];
				$signature['x-wp-nonce'] = $all_headers['x_nonce'][0] ?? '';
				$signature['x-wp-origin'] = $all_headers['x_origin'][0] ?? '';
				$signature['x-wp-signature'] = $all_headers['x_signature'][0] ?? '';
				$signature['x-wp-bodyhash'] = $all_headers['x_bodyhash'][0] ?? '';

				if ( $signature['x-wp-origin'] != GD_TEMP_DOMAIN ) {
					return false;
				}

				$this->validated['plugin-updates'] = $this->validate_signature($signature);

				return $this->validated['plugin-updates'];
			},
			'callback'            => function (WP_REST_Request $request) {
				$plugins = $request->get_param('plugins');

				return new \WP_REST_Response(
					array(
						'code' => 'OK',
						'data' => $this->smart_updates->update_plugins($plugins),
					),
					200
				);
			},
		] );
	}

    /**
     * GET route to get 2FA status
     */
    public function get_2_fa_status() {
        register_rest_route( $this->namespaces['v1'], '2fa-status', [
            'methods'             => 'GET',
            'permission_callback' => function($request) {

                if ( isset($this->validated['2fa_status']) ) {
                    return $this->validated['2fa_status'];
                }

                $all_headers = $request->get_headers();
                $signature = [];
                $signature['x-wp-nonce'] = $all_headers['x_nonce'][0] ?? '';
                $signature['x-wp-origin'] = $all_headers['x_origin'][0] ?? '';
                $signature['x-wp-signature'] = $all_headers['x_signature'][0] ?? '';
                $signature['x-wp-bodyhash'] = $all_headers['x_bodyhash'][0] ?? '';

                if ( $signature['x-wp-origin'] != GD_TEMP_DOMAIN ) {
                    return false;
                }
                $this->validated['2fa_status'] = $this->validate_signature($signature);

                return $this->validated['2fa_status'];
            },
            'callback'            => function () {
                return new \WP_REST_Response(
                    array(
                      'enabled' => get_option( 'wpsec_two_fa_status','disabled' ) === 'enabled',
                    ),
                    200
                );
            },
        ] );
    }

    public function get_nps_form_configuration() {
        register_rest_route( $this->namespaces['v1'], 'nps-form-configuration', [
            'methods'             => 'GET',
            'permission_callback' => function( $request ) {
                return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
            },
            'callback'            => function ($req) {
                $nps_form_id = $req->get_param( 'nps_form_id' );
                $raw = $this->get_cached_nps_form_configuration( $nps_form_id );

                if ( empty( $raw ) ) {
                    return new \WP_REST_Response(
                        array(
                            'code'    => 'OK',
                            'message' => 'No NPS form configuration found.',
                            'data'    => array(),
                        ),
                        200
                    );
                }

                return new \WP_REST_Response(
                    array(
                        'code'    => 'OK',
                        'message' => 'NPS form configuration found.',
                        'data'    => array(
                            'form_configuration' => $raw,
                        ),
                    ),
                    200
                );
            }
        ] );
    }

    /**
     * Get NPS form configuration with caching.
     *
     * @param string $nps_form_id
     *
     * @return array|null
     */
    private function get_cached_nps_form_configuration( $nps_form_id ) {
        if ( ! defined( 'GD_ACCOUNT_UID' ) ) {
            return $this->api->get_nps_form_configuration( $nps_form_id );
        }

        $option_name = sprintf( self::NPS_CONFIG_OPTION_NAME, $nps_form_id, GD_ACCOUNT_UID );
        $cached_data = get_option( $option_name );

        // No cache exists - fetch from API
        if ( false === $cached_data ) {
            return $this->fetch_and_cache_nps_config( $option_name, $nps_form_id );
        }

        // Cache expired or timestamp empty - fetch new config from API
        if ( empty( $cached_data['timestamp'] ) || time() - $cached_data['timestamp'] > self::NPS_CONFIG_CACHING_INTERVAL ) {
            $result = $this->fetch_and_cache_nps_config( $option_name, $nps_form_id );

            // If API failed, use old cached config and update timestamp to avoid hammering
            if ( null === $result && isset( $cached_data['config'] ) ) {
                $cached_data['timestamp'] = time();
                update_option( $option_name, $cached_data );
                return $cached_data['config'];
            }

            return $result;
        }

        // Return cached value
        return isset( $cached_data['config'] ) ? $cached_data['config'] : null;
    }

    /**
     * Fetch NPS form configuration from API and cache the result.
     *
     * @param string $option_name
     * @param string $nps_form_id
     *
     * @return array|null
     */
    private function fetch_and_cache_nps_config( $option_name, $nps_form_id ) {
        $result = $this->api->get_nps_form_configuration( $nps_form_id );

        if( null === $result ) {
            return null;
        }

        $cached_data = [
            'config'    => $result,
            'timestamp' => time(),
        ];
        update_option( $option_name, $cached_data );

        return $result;
    }

    public function nps_form_submit() {
        register_rest_route( $this->namespaces['v1'], 'nps-form-submit', [
            'methods'             => 'POST',
            'permission_callback' => function( $request ) {
                return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
            },
            'callback'            => function ( $request ) {
                $nps_form_id = $request->get_param( 'nps_form_id' );
                $json_params = $request->get_json_params();
                $raw = $this->api->nps_form_submit($json_params, $nps_form_id);

                if ( empty( $raw ) ) {
                    return new \WP_REST_Response(
                        array(
                            'code'    => 'OK',
                            'message' => 'Submit configuration form failed.',
                            'data'    => array(),
                        ),
                        200
                    );
                }

                return new \WP_REST_Response(
                    array(
                        'code'    => 'OK',
                        'message' => 'NPS form submitted.',
                        'data'    => array(),
                    ),
                    200
                );
            }
        ] );
    }

    public function nps_form_dismiss() {
        register_rest_route( $this->namespaces['v1'], 'nps-form-dismiss', [
            'methods'             => 'POST',
            'permission_callback' => function( $request ) {
                return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
            },
            'callback'            => function ( $request ) {
                $nps_form_id = $request->get_param( 'nps_form_id' );
                $raw = $this->api->nps_form_dismiss( $nps_form_id);

                if ( empty( $raw ) ) {
                    return new \WP_REST_Response(
                        array(
                            'code'    => 'OK',
                            'message' => 'Dismiss form failed.',
                            'data'    => array(),
                        ),
                        200
                    );
                }

                return new \WP_REST_Response(
                    array(
                        'code'    => 'OK',
                        'message' => 'Dismiss form successful.',
                        'data'    => array(),
                    ),
                    200
                );
            }
        ] );
    }

    public function feedback_form_submit() {
        register_rest_route( $this->namespaces['v1'], 'form-submit', [
            'methods'             => 'POST',
            'permission_callback' => function( $request ) {
                return $this->is_administrator() && wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' );
            },
            'callback'            => function ( $request ) {
                $feedback_form_id = $request->get_param( 'feedback_form_id' );
                $json_params = $request->get_json_params();
                $raw = $this->api->feedback_form_submit($json_params, $feedback_form_id);

                if ( empty( $raw ) ) {
                    return new \WP_REST_Response(
                        array(
                            'code'    => 'OK',
                            'message' => 'Submit feedback failed.',
                            'data'    => array(),
                        ),
                        200
                    );
                }

                return new \WP_REST_Response(
                    array(
                        'code'    => 'OK',
                        'message' => 'Feedback form submitted.',
                        'data'    => array(),
                    ),
                    200
                );
            }
        ] );
    }

}
