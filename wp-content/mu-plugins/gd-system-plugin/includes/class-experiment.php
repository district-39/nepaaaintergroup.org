<?php

namespace WPaaS;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Experiment {

	/**
	 * Transient key prefix for caching experiment API responses.
	 */
	const TRANSIENT_KEY_PREFIX = 'wpaas_experiment_';

	/**
	 * Default cache duration in seconds (10 days).
	 */
	const DEFAULT_CACHE_TTL = 864000;

	/**
	 * Cache duration for failed API calls in seconds (5 minutes).
	 */
	const FAILURE_CACHE_TTL = 300;

	/**
	 * Instance of the API.
	 *
	 * @var API_Interface
	 */
	private $api;

	/**
	 * In-memory cache of experiment results for the current request.
	 *
	 * @var array
	 */
	private $results = [];

	/**
	 * @param API_Interface $api
	 */
	public function __construct( API_Interface $api ) {

		$this->api = $api;

	}

	/**
	 * Check whether an experiment is enabled for the current account.
	 *
	 * Results are cached in a WordPress transient keyed by experiment name
	 * and account UID so that changing the experiment name automatically
	 * invalidates stale cache entries.
	 *
	 * @param string $exp_name Experiment name passed to the API.
	 * @param int    $cache_ttl Cache lifetime in seconds (default 10 days).
	 *
	 * @return bool
	 */
	public function is_enabled( $exp_name, $cache_ttl = self::DEFAULT_CACHE_TTL ) {

		if ( isset( $this->results[ $exp_name ] ) ) {
			return $this->results[ $exp_name ];
		}

		$account_uid = defined( 'GD_ACCOUNT_UID' ) ? GD_ACCOUNT_UID : '';

		if ( empty( $account_uid ) ) {
			return false;
		}

		$transient_key = self::TRANSIENT_KEY_PREFIX . $exp_name . '_' . $account_uid;

		$cached = get_transient( $transient_key );

		if ( false !== $cached ) {
			$this->results[ $exp_name ] = (bool) $cached;

			return $this->results[ $exp_name ];
		}

		$show = $this->api->get_experiment( $exp_name );

		if ( null === $show ) {
			set_transient( $transient_key, 0, self::FAILURE_CACHE_TTL );
			$this->results[ $exp_name ] = false;

			return false;
		}

		set_transient( $transient_key, $show ? 1 : 0, $cache_ttl );
		$this->results[ $exp_name ] = $show;

		return $show;

	}

}
