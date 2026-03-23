<?php

class APVC_Query {

	const CAMPAIGN_URLS       = 'campaign_urls';
	const CAMPAIGNS           = 'campaigns';
	const REFERRER_TYPES      = 'referrer_types';
	const REFERRERS           = 'referrers';
	const VIEWS_DATA          = 'views_data';
	const VIEWS               = 'page_views';
	const VISITORS            = 'users';
	const SESSIONS            = 'user_sessions';
	const WC_ORDERS           = 'ecommerce_orders';
	const SHORTCODES          = 'shortcodes';
	const EXPORTED_DATA       = 'exported_data';
	const SMART_NOTIFICATIONS = 'smart_notifications';
	const OLD_STATS_TABLE     = 'avc_page_visit_history';
	const CITIES              = 'cities';
	const COUNTRIES           = 'countries';
	const STATES              = 'states';

	public function __construct() {}

	public static function get_table_name( string $name, string $legacy = 'no' ) {
		global $wpdb;
		$prefix     = $wpdb->prefix;
		$reflection = new \ReflectionClass( static::class );
		$constants  = $reflection->getConstants();

		if ( in_array( $name, $constants ) && $legacy == 'no' ) {
			return $prefix . 'apvc_' . $name;
		} else if ( in_array( $name, $constants ) && $legacy == 'yes' ) {
			return $prefix . $name;
		} else {
			return null;
		}
	}

	public static function create_country($user_data = array()) {
		global $wpdb;
		$countries_table = self::get_table_name(self::COUNTRIES);

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT country_id FROM $countries_table WHERE country_code = %s AND country = %s AND continent = %s",
				$user_data['country_code'],
				$user_data['country'],
				$user_data['continent']
			)
		);

		if (!empty($existing)) {
			return $existing;
		}

		$countries_sql = $wpdb->prepare(
			"INSERT INTO $countries_table (country_code, country, continent) VALUES (%s, %s, %s)",
			$user_data['country_code'],
			$user_data['country'],
			$user_data['continent']
		);

		$wpdb->query($countries_sql);
		return $wpdb->insert_id;
	}

	public static function create_city($user_data = array()) {
		global $wpdb;
		$cities_table = self::get_table_name(self::CITIES);

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT city_id FROM $cities_table WHERE city = %s",
				$user_data['city']
			)
		);

		if (!empty($existing)) {
			return $existing;
		}

		$cities_sql = $wpdb->prepare(
			"INSERT INTO $cities_table (country_id, city) VALUES (%d, %s)",
			$user_data['country_id'],
			$user_data['city']
		);

		$wpdb->query($cities_sql);
		return $wpdb->insert_id;
	}

	public static function create_state($user_data = array()) {
		global $wpdb;
		$states_table = self::get_table_name(self::STATES);

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT state_id FROM $states_table WHERE state_name = %s",
				$user_data['state_name']
			)
		);

		if (!empty($existing)) {
			return $existing;
		}

		$states_sql = $wpdb->prepare(
			"INSERT INTO $states_table (country_id, state_name) VALUES (%d, %s)",
			$user_data['country_id'],
			$user_data['state_name']
		);

		$wpdb->query($states_sql);
		return $wpdb->insert_id;
	}

	public static function create_visitor($user_data = array()) {
		// Check if $user_data is null or not an array
		if (is_null($user_data) || !is_array($user_data)) {
			return;
		}

		global $wpdb;
		$visitors_table = self::get_table_name(self::VISITORS);

		// Check if the visitor data already exists
		$existing_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $visitors_table WHERE visitor_id = %s AND country_code = %s AND city = %s AND subdivision = %s AND country = %s AND continent = %s AND site_id = %d",
				$user_data['visitor_id'],
				$user_data['country_code'],
				$user_data['city'],
				$user_data['subdivision'],
				$user_data['country'],
				$user_data['continent'],
				$user_data['site_id']
			)
		);

		if ($existing_count > 0) {
			// Data already exists, no need to insert again
			return;
		}

		// Insert new visitor data
		$visitor_sql = $wpdb->prepare(
			"INSERT INTO $visitors_table (visitor_id, country_code, city, subdivision, country, continent, site_id) VALUES (%s, %s, %s, %s, %s, %s, %d)",
			$user_data['visitor_id'],
			$user_data['country_code'],
			$user_data['city'],
			$user_data['subdivision'],
			$user_data['country'],
			$user_data['continent'],
			$user_data['site_id']
		);

		$wpdb->query($visitor_sql);

		return $wpdb->insert_id;
	}


	/**
	 * Get the most recent user session ID for a specific visitor within the last 30 minutes.
	 *
	 * @param string $visitor_id The unique identifier of the visitor.
	 *
	 * @return string|null The ID of the most recent user session within the last 30 minutes, or null if $visitor_id is not valid.
	 */
	public static function get_current_user_session($visitor_id) {
		// Check if $visitor_id is null
		if (is_null($visitor_id)) {
			return null;
		}

		global $wpdb;
		$sessions_table = self::get_table_name(self::SESSIONS);

		// Query to retrieve the most recent user session ID within the last 30 minutes
		$existing_data = $wpdb->prepare(
			"SELECT user_session_id FROM $sessions_table WHERE visitor_id = %s AND created_at > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 30 MINUTE) ORDER BY created_at DESC LIMIT 1",
			$visitor_id
		);

		// Execute the query and return the result
		return $wpdb->get_var($existing_data);
	}


	/**
	 * Create a new user session record in the database.
	 *
	 * @param array $args An associative array containing user session data:
	 *                    - 'visitor_id' (string): The unique identifier of the visitor.
	 *                    - 'referrer_id' (string): The identifier of the referrer (if applicable).
	 *                    - 'campaign_id' (string): The identifier of the campaign (if applicable).
	 *                    - 'created_at' (string): The timestamp when the session was created.
	 *                    - 'device_type' (string): The type of the user's device (e.g., mobile, desktop).
	 *                    - 'device_os' (string): The operating system of the user's device.
	 *                    - 'device_browser' (string): The user's browser.
	 *                    - 'device_browser_ver' (string): The version of the user's browser.
	 *                    - 'city_id' (int): The ID of the city associated with the session.
	 *                    - 'state_id' (int): The ID of the state/province associated with the session.
	 *                    - 'country_id' (int): The ID of the country associated with the session.
	 *                    - 'site_id' (int): The ID of the site associated with the session.
	 *                    - 'ended_at' (string): The timestamp when the session ended (if applicable).
	 *
	 * @return int|null The ID of the newly created user session record, or null if the input data is not valid.
	 */
	public static function create_user_session($args) {
		global $wpdb;
		$sessions_table = self::get_table_name(self::SESSIONS);

		// Prepare the SQL statement for inserting a new user session
		$session_sql = $wpdb->prepare(
			"INSERT INTO $sessions_table (visitor_id, referrer_id, campaign_id, created_at, device_type, device_os, device_browser, device_browser_ver, city_id, state_id, country_id, site_id, session_ended_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %s)",
			$args['visitor_id'],
			$args['referrer_id'],
			$args['campaign_id'],
			$args['created_at'],
			$args['device_type'],
			$args['device_os'],
			$args['device_browser'],
			$args['device_browser_ver'],
			$args['city_id'],
			$args['state_id'],
			$args['country_id'],
			$args['site_id'],
			$args['ended_at']
		);

		// Execute the SQL query to insert the user session data
		$wpdb->query($session_sql);

		// Return the ID of the newly created user session record
		return $wpdb->insert_id;
	}


	public static function get_view_count( $post_id ) {
		if ( is_null( $post_id ) ) {
			return 0;
		}

		global $wpdb;
		$views_table = self::get_table_name( self::VIEWS_DATA );
		$viewCount   = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT views FROM $views_table WHERE singular_id = %d",
				$post_id
			)
		);
		return $viewCount ? $viewCount : 0;
	}

	public static function create_referrer( $domain_name ) {
		if ( is_null( $domain_name ) ) {
			return null;
		}

		global $wpdb;
		$referrers_table = self::get_table_name( self::REFERRERS );
		$referrers_sql   = $wpdb->prepare(
			'INSERT INTO ' . $referrers_table . ' (domain_name) VALUES ( %s )',
			$domain_name
		);
		$wpdb->query( $referrers_sql );
		return $wpdb->insert_id;
	}

	public static function get_referrer( $domain_name ) {
		if ( is_null( $domain_name ) ) {
			return;
		}

		global $wpdb;
		$referrers_table = self::get_table_name( self::REFERRERS );
		$existing_data   = $wpdb->prepare(
			"SELECT id FROM $referrers_table WHERE domain_name = %s LIMIT 1",
			$domain_name
		);
		return $wpdb->get_row( $existing_data );
	}

	public static function create_view( $args ) {
		if ( is_null( $args ) ) {
			return;
		}

		global $wpdb;
		$views_table      = self::get_table_name( self::VIEWS );
		$views_data_table = self::get_table_name( self::VIEWS_DATA );

		$views_sql = $wpdb->prepare(
			'INSERT INTO ' . $views_table . ' (page_id, viewed_at, page, user_session_id) VALUES ( %s, %s, %s, %s )',
			$args['page_id'],
			$args['viewed_at'],
			$args['page'],
			$args['user_session_id']
		);
		$wpdb->query( $views_sql );

		$views_sql      = $wpdb->prepare(
			'SELECT COUNT(*) FROM `' . $views_table . '` WHERE `page_id` = %d',
			$args['page_id']
		);
		$existing_count = $wpdb->get_var( $views_sql );
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE $views_data_table SET views = %d WHERE singular_id = %d",
				absint( $existing_count ),
				$args['page_id']
			)
		);

		return $wpdb->insert_id;
	}

	public static function get_dashboard_data( $args ) {
		if ( is_null( $args ) ) {
			return;
		}
	}

	public static function get_end_date( $end_date ) {
		return empty( $end_date ) ? new \DateTime() : new \DateTime( $end_date );
	}

	public static function get_yearly_stats_query( $start_date = '', $end_date = '' ) {
		global $wpdb;

		if( empty( $start_date ) ){
			$start_date = new DateTime();
			$start_date->setDate($start_date->format('Y'), 1, 1);
			$old_date = new DateTime();
			$old_date->setDate($start_date->format('Y'), 1, 1);
			$old_date->modify('-1 year');
		}
		$end_date   = self::get_end_date( $end_date );

		$start_date = APVC_Timezone_Helpers::start_of_locale_day( $start_date );
		$end_date   = APVC_Timezone_Helpers::end_of_locale_day( $end_date );

		$views_data    = self::get_table_name( self::VIEWS );
		$current = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$start_date->format( APVC_DATE_FORMAT ),
			$end_date->format( APVC_DATE_FORMAT )
		) );

		$previous = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$old_date->format( APVC_DATE_FORMAT ),
			$start_date->format( APVC_DATE_FORMAT )
		) );

		$difference =  $current - $previous;
		if( $difference <= -1 ){
			$status = 'neg';
		} else {
			$status = 'pos';
		}
		return [ 'current' => APVC_Admin_Helpers::filter_count_format($current), 'previous' => APVC_Admin_Helpers::filter_count_format($previous), 'status' => $status, 'difference' => APVC_Admin_Helpers::filter_count_format($difference) ];
	}

	public static function get_monthly_stats_query( $start_date = '', $end_date = '' ) {
		global $wpdb;

		if( empty( $start_date ) ){
			$start_date = new DateTime( 'first day of this month 00:00:00' );
			$old_date = new DateTime( 'first day of last month 00:00:00' );
		}
		$end_date   = self::get_end_date( $end_date );

		$start_date = APVC_Timezone_Helpers::start_of_locale_day( $start_date );
		$end_date   = APVC_Timezone_Helpers::end_of_locale_day( $end_date );

		$views_data    = self::get_table_name( self::VIEWS );
		$current = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$start_date->format( APVC_DATE_FORMAT ),
			$end_date->format( APVC_DATE_FORMAT )
		) );

		$previous = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$old_date->format( APVC_DATE_FORMAT ),
			$start_date->format( APVC_DATE_FORMAT )
		) );

		$difference =  $current - $previous;
		if( $difference <= -1 ){
			$status = 'neg';
		} else {
			$status = 'pos';
		}

		return [ 'current' => APVC_Admin_Helpers::filter_count_format($current), 'previous' => APVC_Admin_Helpers::filter_count_format($previous), 'status' => $status, 'difference' => APVC_Admin_Helpers::filter_count_format($difference) ];
	}

	public static function get_weekly_stats_query( $start_date = '', $end_date = '' ) {
		global $wpdb;
		$first_day_of_the_week = APVC_Timezone_Helpers::get_wp_day_name( get_option( 'start_of_week', true ) );

		$start_date            = empty( $start_date ) ? new \DateTime( $first_day_of_the_week . ' last week 00:00:00' ) : $start_date;
		$end_date              = self::get_end_date( $end_date );
		$old_date = new DateTime( $first_day_of_the_week.' last week 00:00:00' );
		$old_date->modify('-1 week');

		$start_date = APVC_Timezone_Helpers::start_of_locale_day( $start_date );
		$end_date   = APVC_Timezone_Helpers::end_of_locale_day( $end_date );

		$views_data    = self::get_table_name( self::VIEWS );
		$current = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$start_date->format( APVC_DATE_FORMAT ),
			$end_date->format( APVC_DATE_FORMAT )
		) );

		$previous = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$old_date->format( APVC_DATE_FORMAT ),
			$start_date->format( APVC_DATE_FORMAT )
		) );

		$difference =  $current - $previous;
		if( $difference <= -1 ){
			$status = 'neg';
		} else {
			$status = 'pos';
		}

		return [ 'current' => APVC_Admin_Helpers::filter_count_format($current), 'previous' => APVC_Admin_Helpers::filter_count_format($previous), 'status' => $status, 'difference' => APVC_Admin_Helpers::filter_count_format($difference) ];
	}

	public static function get_daily_stats_query( $start_date = '', $end_date = '' ) {
		global $wpdb;

		$start_date_bs =  new \DateTime( 'today 00:00:00' );
		$end_date_bs   = self::get_end_date( $end_date );

		$old_date = new DateTime( 'yesterday' );
		$old_start_date = new DateTime( 'yesterday 23:59:59' );

		$start_date = APVC_Timezone_Helpers::start_of_locale_day( $start_date_bs );
		$end_date   = APVC_Timezone_Helpers::end_of_locale_day( $end_date_bs );
		$start_date->setTime( 0, 0, 0 );

		$views_data    = self::get_table_name( self::VIEWS );
		$current = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$start_date->format( APVC_DATE_FORMAT ),
			$end_date->format( APVC_DATE_FORMAT )
		) );

		$previous = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $views_data WHERE viewed_at BETWEEN %s AND %s",
			$old_date->format( APVC_DATE_FORMAT ),
			$old_start_date->format( APVC_DATE_FORMAT )
		) );

		$difference =  $current - $previous;
		if( $difference <= -1 ){
			$status = 'neg';
		} else {
			$status = 'pos';
		}
		return [ 'current' => APVC_Admin_Helpers::filter_count_format($current), 'previous' => APVC_Admin_Helpers::filter_count_format($previous), 'status' => $status, 'difference' => APVC_Admin_Helpers::filter_count_format($difference) ];
	}


	public static function get_all_states_with_range( $start_date = '', $end_date = '', $type = '' ) {
		global $wpdb;

		$start_date_bs = empty( $start_date ) ? new \DateTime( '-30 days 00:00:00' ) : new \DateTime( $start_date );
		$end_date_bs   = self::get_end_date( $end_date );

		$start_date = APVC_Timezone_Helpers::start_of_locale_day( $start_date_bs );
		$end_date   = APVC_Timezone_Helpers::end_of_locale_day( $end_date_bs );

		$views_data = self::get_table_name( self::VIEWS );
		$sessions   = self::get_table_name( self::SESSIONS );
		$users      = self::get_table_name( self::VISITORS );

		if ( 'refer' === $type ) {
			$existing_data = $wpdb->prepare(
				"SELECT DATE_FORMAT(CONVERT_TZ(views.viewed_at, '+00:00', '+05:30'), '%e/%m') as date, COUNT(*) as views, COUNT(DISTINCT sessions.visitor_id) as visitors, COUNT(DISTINCT sessions.visitor_id) as unq_visitors, COUNT(DISTINCT sessions.user_session_id) as sessions FROM $views_data AS views LEFT JOIN $sessions AS sessions ON views.user_session_id = sessions.user_session_id WHERE views.viewed_at BETWEEN %s AND %s  AND (1 = 1 OR views.page_id IN ('0') ) AND sessions.country_id IS NOT NULL AND sessions.referrer_id != 0 GROUP BY DATE(CONVERT_TZ(views.viewed_at, '+00:00', '+05:30'))",
				$start_date->format( APVC_DATE_FORMAT ),
				$end_date->format( APVC_DATE_FORMAT )
			);
			return $wpdb->get_results( $existing_data, ARRAY_A );
		}

		$existing_data = $wpdb->prepare(
			"SELECT DATE_FORMAT(CONVERT_TZ(views.viewed_at, '+00:00', '+05:30'), '%e/%m') as date, COUNT(*) as views, COUNT(DISTINCT sessions.visitor_id) as visitors, COUNT(DISTINCT sessions.visitor_id) as unq_visitors, COUNT(DISTINCT sessions.user_session_id) as sessions FROM $views_data AS views LEFT JOIN $sessions AS sessions ON views.user_session_id = sessions.user_session_id WHERE views.viewed_at BETWEEN %s AND %s  AND (1 = 1 OR views.page_id IN ('0') ) AND sessions.country_id IS NOT NULL GROUP BY DATE(CONVERT_TZ(views.viewed_at, '+00:00', '+05:30'))",
			$start_date->format( APVC_DATE_FORMAT ),
			$end_date->format( APVC_DATE_FORMAT )
		);

		return $wpdb->get_results( $existing_data, ARRAY_A );
	}

	public static function get_dashboard_visitors_with_range_by_field( $field = '', $start_data = '', $end_date = '', $type = 'dashboard' ) {
		return array_sum( array_column( self::get_all_states_with_range( $start_data, $end_date, $type ), $field ) );
	}

	public static function get_dashboard_list_posts( $limit, $args = array() ) {
		global $wpdb;
		$views_data_table = self::get_table_name( self::VIEWS_DATA );
		$sessions_table   = self::get_table_name( self::SESSIONS );
		if ( isset( $args['post_type'] ) ) {
			$conditions = " WHERE stored_type = '" . $args['post_type'] . "'";
		} else {
			$conditions = '';
		}
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . $views_data_table . ' ' . $conditions . ' ORDER BY views DESC LIMIT %d',
				$limit
			)
		);
	}

	public static function get_dashboard_list_posts_with_pagination( $limit, $page, $args = array() ) {
		global $wpdb;
		$views_data_table = self::get_table_name( self::VIEWS_DATA );
		$views_data_table = self::get_table_name( self::VIEWS_DATA );


		if ( !empty( $args['post_type'] ) ) {
			$conditions = " WHERE stored_type = '" . $args['post_type'] . "'";
		} else {
			$conditions = '';
		}

		$lists = $wpdb->get_results( $wpdb->prepare(
					'SELECT * FROM ' . $views_data_table . ' ' . $conditions . ' ORDER BY views DESC LIMIT %d OFFSET %d',
					$page,
					$limit
				)
		);

		$final_array = array();
		$index = 0;
		foreach( $lists as $list ){
			$final_array[ $index ]['id'] = $list->id;
			$final_array[ $index ]['views'] = APVC_Admin_Helpers::filter_count_format( $list->views );;
			$final_array[ $index ]['stored_title'] = $list->stored_title;
			$final_array[ $index ]['singular_id'] = $list->singular_id;
			$final_array[ $index ]['stored_url'] = $list->stored_url;
			$final_array[ $index ]['stored_type_label'] = $list->stored_type_label;
			$final_array[ $index ]['starting_count'] = APVC_Admin_Helpers::filter_count_format( APVC_Query::get_starting_count( $list->singular_id ) );;
			$final_array[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( APVC_Query::get_post_total_sessions( $list->singular_id ) );
			$index++;
		}
		return $final_array;
	}

	public static function get_post_total_sessions( $page_id ) {
		global $wpdb;
		$views_table = self::get_table_name( self::VIEWS );
		return $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(DISTINCT(user_session_id)) FROM ' . $views_table . ' WHERE `page_id` = %d ',
				$page_id
			)
		);
	}

	public static function get_detailed_reports( $args, $limit ) {
		global $wpdb;
		$views            = self::get_table_name( self::VIEWS );
		$views_data_table = self::get_table_name( self::VIEWS_DATA );
		$user_sessions    = self::get_table_name( self::SESSIONS );
		$cities           = self::get_table_name( self::CITIES );
		$states           = self::get_table_name( self::STATES );
		$countries        = self::get_table_name( self::COUNTRIES );

		$condition      = '';
		$spread         = array();
		$spread['p_id'] = $args['p_id'];

		if ( isset( $args['startDate'] ) && isset( $args['endDate'] ) && ! empty( $args['startDate'] ) ) {
			$condition           .= ' AND ( ' . $views . '.viewed_at BETWEEN %s AND %s ) ';
			$spread['startDate'] = $args['startDate'] . ' 00:00:00';
			$spread['endDate']   = $args['endDate'] . ' 23:59:59';
		}
		if ( isset( $args['device_type'] ) && ! empty( $args['device_type'] ) ) {
			$condition             .= ' AND ' . $user_sessions . '.device_type = %s ';
			$spread['device_type'] = $args['device_type'];
		}
		if ( isset( $args['device_os'] ) && ! empty( $args['device_os'] ) ) {
			$condition           .= ' AND ' . $user_sessions . '.device_os = %s ';
			$spread['device_os'] = $args['device_os'];
		}
		if ( isset( $args['device_br'] ) && ! empty( $args['device_br'] ) ) {
			$condition           .= ' AND ' . $user_sessions . '.device_browser = %s ';
			$spread['device_br'] = $args['device_br'];
		}
		$spread['limit'] = $limit;

		$query = $wpdb->prepare(
			'SELECT * FROM ' . $views . ' INNER JOIN ' . $views_data_table . ' ON ' . $views . '.page_id = ' . $views_data_table . '.singular_id LEFT JOIN ' . $user_sessions . ' ON ' . $views . '.user_session_id = ' . $user_sessions . '.user_session_id LEFT JOIN ' . $cities . ' ON ' . $user_sessions . '.city_id = ' . $cities . '.city_id LEFT JOIN ' . $states . ' ON ' . $user_sessions . '.state_id = ' . $states . '.state_id LEFT JOIN ' . $countries . ' ON ' . $user_sessions . '.country_id = ' . $countries . '.country_id WHERE ' . $views . '.page_id = %d ' . $condition . ' ORDER BY ' . $views . '.viewed_at DESC LIMIT %d',
			$spread
		);
		return $wpdb->get_results( $query );
	}

	public static function get_detailed_reports_with_pagi( $p_id, $limit, $page = 20, $args = array() ) {
		global $wpdb;

		$views            = self::get_table_name( self::VIEWS );
		$views_data_table = self::get_table_name( self::VIEWS_DATA );
		$user_sessions    = self::get_table_name( self::SESSIONS );
		$cities           = self::get_table_name( self::CITIES );
		$states           = self::get_table_name( self::STATES );
		$countries        = self::get_table_name( self::COUNTRIES );

		$condition      = '';
		$spread         = array();
		$spread['p_id'] = absint( sanitize_text_field( $p_id ) );

		if ( isset( $args['startDate'] ) && isset( $args['endDate'] ) && ! empty( $args['startDate'] ) ) {
			$spread['startDate'] = sanitize_text_field( $args['startDate'] ) . ' 00:00:00';
			$spread['endDate']   = sanitize_text_field( $args['endDate'] ) . ' 23:59:59';
			$condition           .= ' AND ( ' . $views . '.viewed_at BETWEEN ' . $spread['startDate'] . ' AND ' . $spread['endDate'] . ' ) ';

		}
		if ( isset( $args['device_type'] ) && ! empty( $args['device_type'] ) ) {
			$spread['device_type'] = sanitize_text_field( $args['device_type'] );
			$condition             .= ' AND ' . $user_sessions . '.device_type = ' . $spread['device_type'] . ' ';

		}
		if ( isset( $args['device_os'] ) && ! empty( $args['device_os'] ) ) {
			$spread['device_os'] = sanitize_text_field( $args['device_os'] );
			$condition           .= ' AND ' . $user_sessions . '.device_os = ' . $spread['device_os'] . ' ';

		}
		if ( isset( $args['device_br'] ) && ! empty( $args['device_br'] ) ) {
			$spread['device_br'] = sanitize_text_field( $args['device_br'] );
			$condition           .= ' AND ' . $user_sessions . '.device_browser = ' . $spread['device_br'] . ' ';

		}
		$spread['offset'] = absint( $page );
		$spread['limit']  = absint( $limit );

		$query = 'SELECT * FROM ' . $views . ' INNER JOIN ' . $views_data_table . ' ON ' . $views . '.page_id = ' . $views_data_table . '.singular_id INNER JOIN ' . $user_sessions . ' ON ' . $views . '.user_session_id = ' . $user_sessions . '.user_session_id LEFT JOIN ' . $cities . ' ON ' . $user_sessions . '.city_id = ' . $cities . '.city_id LEFT JOIN ' . $states . ' ON ' . $user_sessions . '.state_id = ' . $states . '.state_id LEFT JOIN ' . $countries . ' ON ' . $user_sessions . '.country_id = ' . $countries . '.country_id WHERE ' . $views . '.page_id = ' . $spread['p_id'] . ' ' . $condition . ' ORDER BY ' . $views . '.viewed_at DESC LIMIT ' . $spread['offset'] . ' OFFSET ' . $spread['limit'];

		return $wpdb->get_results( $query );
	}

	public static function get_device_list() {
		global $wpdb;
		$user_sessions = self::get_table_name( self::SESSIONS );
		return $wpdb->get_results(
			'SELECT device_type FROM ' . $user_sessions . ' GROUP BY device_type ASC'
		);
	}

	public static function get_device_os_list() {
		global $wpdb;
		$user_sessions = self::get_table_name( self::SESSIONS );
		return $wpdb->get_results( 'SELECT device_os FROM ' . $user_sessions . ' GROUP BY device_os ASC' );
	}

	public static function get_device_br_list() {
		global $wpdb;
		$user_sessions = self::get_table_name( self::SESSIONS );
		return $wpdb->get_results( 'SELECT device_browser FROM ' . $user_sessions . ' WHERE device_browser IS NOT NULL AND device_browser != "" GROUP BY device_browser ASC' );
	}

	public static function get_trending_referrer_name( $startDate = '', $endDate = '' ) {
		global $wpdb;
		$user_sessions        = self::get_table_name( self::SESSIONS );
		$referrers_table      = self::get_table_name( self::REFERRERS );
		$referrer_types_table = self::get_table_name( self::REFERRER_TYPES );
		$condition            = '';
		$spread               = array();

		if ( ! empty( $startDate ) && ! empty( $endDate ) ) {
			$condition           .= ' WHERE ( ' . $user_sessions . '.created_at BETWEEN %s AND %s ) ';
			$spread['startDate'] = $startDate . ' 00:00:00';
			$spread['endDate']   = $endDate . ' 23:59:59';
		}

		if ( ! empty( $spread ) ) {
			$ref = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT `referrer_id`, COUNT(`referrer_id`) FROM ' . $user_sessions . ' ' . $condition . ' GROUP BY referrer_id HAVING COUNT(`referrer_id`) > 1',
					$spread['startDate'],
					$spread['endDate']
				)
			);
		} else {
			$ref = $wpdb->get_var( 'SELECT `referrer_id`, COUNT(`referrer_id`) FROM ' . $user_sessions . ' GROUP BY referrer_id HAVING COUNT(`referrer_id`) > 1' );
		}

		if ( 0 === (int) $ref ) {
			return __( 'Direct', 'apvc' );
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				'SELECT CONCAT (' . $referrer_types_table . '.name, "/", ' . $referrer_types_table . '.type ) FROM ' . $referrers_table . ' INNER JOIN ' . $referrer_types_table . ' ON ' . $referrers_table . '.domain_name = ' . $referrer_types_table . '.domain_to_match WHERE `id` = %d',
				$ref
			)
		);
	}


	public static function get_referrers_list() {
		global $wpdb;
		$user_sessions        = self::get_table_name( self::SESSIONS );
		$referrers_table      = self::get_table_name( self::REFERRERS );
		$referrer_types_table = self::get_table_name( self::REFERRER_TYPES );

		$ref = $wpdb->get_results( 'SELECT * FROM ' . $user_sessions . ' GROUP BY referrer_id' );

		$referrers_list = array();
		if ( ! empty( $ref ) && count( $ref ) > 0 ) {
			$index = 0;
			foreach ( $ref as $refer ) {

				if ( 0 === (int) $refer->referrer_id ) {
					$referrers_list[ $index ]['referrer'] = __( 'Direct', 'apvc' );
					$referrers_list[ $index ]['type']     = __( 'Direct', 'apvc' );
					$referrers_list[ $index ]['visitors'] = APVC_Admin_Helpers::filter_count_format( APVC_Helper_Functions::getTotalVisitorsForRef( absint( $refer->referrer_id ) ) );
					$referrers_list[ $index ]['views']    = APVC_Admin_Helpers::filter_count_format( APVC_Helper_Functions::getTotalViewsForRef( absint( $refer->referrer_id ) ) );
				} else {

					$refer_name = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT `domain_name` FROM ' . $referrers_table . ' WHERE `id` = %d',
							absint( $refer->referrer_id )
						)
					);

					$ref_data = $wpdb->get_row(
						$wpdb->prepare(
							'SELECT * FROM ' . $referrer_types_table . ' WHERE `domain_to_match` = %s',
							$refer_name
						)
					);

					if ( ! empty( $ref_data->name ) ) {
						$referrers_list[ $index ]['referrer'] = $ref_data->name;
						$referrers_list[ $index ]['type']     = $ref_data->type;
						$referrers_list[ $index ]['visitors'] = APVC_Admin_Helpers::filter_count_format( APVC_Helper_Functions::getTotalVisitorsForRef( absint( $refer->referrer_id ) ) );
						$referrers_list[ $index ]['views']    = APVC_Admin_Helpers::filter_count_format( APVC_Helper_Functions::getTotalViewsForRef( absint( $refer->referrer_id ) ) );
					}
				}
				$index++;
			}
		}

		return $referrers_list;
	}

	public static function save_shortcode( $title, $shortcode ) {
		global $wpdb;
		$shortcodes_table = self::get_table_name( self::SHORTCODES );
		$shortcodes_sql   = $wpdb->prepare(
			'INSERT INTO ' . $shortcodes_table . ' (title, shortcode, date ) VALUES ( %s, %s, %s )',
			$title,
			$shortcode,
			current_time( 'mysql' )
		);

		$wpdb->query( $shortcodes_sql );
		return $wpdb->insert_id;
	}

	public static function delete_shortcode( $id ) {
		global $wpdb;
		$shortcodes_table = self::get_table_name( self::SHORTCODES );
		if ( 0 < $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $shortcodes_table . ' WHERE `id` = %d', $id ) ) ) {
			return $wpdb->query(
				$wpdb->prepare(
					'DELETE FROM ' . $shortcodes_table . ' WHERE `id` = %d',
					$id
				)
			);
		}
	}

	public static function get_custom_shortcodes() {
		global $wpdb;
		$shortcodes_table = self::get_table_name( self::SHORTCODES );
		$shortcodes_sql   = 'SELECT * FROM ' . $shortcodes_table;
		return $wpdb->get_results( 'SELECT * FROM ' . $shortcodes_table );
	}

	public static function get_current_page_counts( $post_id ) {
		global $wpdb;
		$views_data  = self::get_table_name( self::VIEWS_DATA );
		$count_query = $wpdb->prepare(
			'SELECT `views` FROM ' . $views_data . ' WHERE `singular_id` = %d',
			$post_id
		);
		$count       = $wpdb->get_var( $count_query );

		$sCount = get_post_meta( $post_id, 'apvc_starting_count', true );
		if ( isset( $sCount ) && 0 < $sCount ) {
			$count = absint( $count ) + absint( $sCount );
		}
		return APVC_Admin_Helpers::filter_count_format( absint( $count ) );
	}

	public static function get_current_page_today_counts( $post_id ) {
		global $wpdb;
		$start_date_bs = new \DateTime( 'today 00:00:00' );
		$end_date_bs   = new \DateTime( 'today 23:59:00' );

		$start_date = APVC_Timezone_Helpers::start_of_locale_day( $start_date_bs );
		$end_date   = APVC_Timezone_Helpers::end_of_locale_day( $end_date_bs );

		$views_data    = self::get_table_name( self::VIEWS );
		$existing_data = $wpdb->prepare(
			"SELECT COUNT(*) as views FROM $views_data WHERE `page_id` = %d AND `viewed_at` BETWEEN %s AND %s",
			$post_id,
			$start_date->format( APVC_DATE_FORMAT ),
			$end_date->format( APVC_DATE_FORMAT )
		);

		return APVC_Admin_Helpers::filter_count_format( absint( $wpdb->get_var( $existing_data ) ) );
	}

	public static function get_total_counts() {
		global $wpdb;
		$views_data  = self::get_table_name( self::VIEWS_DATA );
		$count_query = 'SELECT SUM(`views`) FROM ' . $views_data;
		return APVC_Admin_Helpers::filter_count_format( ( $wpdb->get_var( $count_query ) ) ?? 0 );
	}

	public static function get_starting_count( $id ) {
		return APVC_Admin_Helpers::filter_count_format( ( get_post_meta( $id, 'apvc_starting_count', true ) ) ? get_post_meta( $id, 'apvc_starting_count', true ) : 0 );
	}

	public static function set_starting_count( $id, $count = 0 ) {
		return update_post_meta( $id, 'apvc_starting_count', absint( $count ) );
	}

	public static function reset_everything() {
		global $wpdb;

		$views      = self::get_table_name( self::VIEWS );
		$views_data = self::get_table_name( self::VIEWS_DATA );
		$referrers  = self::get_table_name( self::REFERRERS );
		$visitors   = self::get_table_name( self::VISITORS );
		$sessions   = self::get_table_name( self::SESSIONS );
		$e_commerce = self::get_table_name( self::WC_ORDERS );
		$cities = self::get_table_name( self::CITIES );
		$states = self::get_table_name( self::STATES );
		$countries = self::get_table_name( self::COUNTRIES );

		$wpdb->query( 'TRUNCATE TABLE `' . $views . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $views_data . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $referrers . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $visitors . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $sessions . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $e_commerce . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $cities . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $states . '`' );
		$wpdb->query( 'TRUNCATE TABLE `' . $countries . '`' );

		delete_transient( 'apvc_trend_top_pages' );
		delete_transient( 'apvc_trend_top_posts' );
		delete_transient( 'apvc_trend_top_countries' );
		delete_transient( 'apvc_trend_top_states' );
		delete_transient( 'apvc_trend_top_cities' );
		delete_transient( 'apvc_trend_top_devices' );
		delete_transient( 'apvc_trend_top_devices_os' );
		delete_transient( 'apvc_trend_top_browsers' );
		delete_transient( 'apvc_trend_refferer' );
		delete_transient( 'apvc_trend_refferer_list' );

		return wp_send_json_success( 'Success' );
	}

	public static function get_top_articles( $type ) {
		global $wpdb;
		$views_data = self::get_table_name( self::VIEWS_DATA );
		$pages      = $wpdb->get_results( $wpdb->prepare( 'SELECT `singular_id` as page_id, `stored_title` as page_title, `stored_url` as page_url, `views` FROM ' . $views_data . ' WHERE `stored_type` = %s ORDER BY `views` DESC LIMIT 10', $type ) );

		$pages_list = array();
		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$pages_list[ $index ]['page_id']       = $page->page_id;
				$pages_list[ $index ]['page_title']    = ( 45 < strlen( $page->page_title ) ) ? substr( $page->page_title, 0, 45 ) . '...' : $page->page_title;
				$pages_list[ $index ]['page_url']      = $page->page_url;
				$pages_list[ $index ]['views']         = APVC_Admin_Helpers::filter_count_format( $page->views );
				$pages_list[ $index ]['visitors']      = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_trending( absint( $page->page_id ) ) );
				$pages_list[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_trending( absint( $page->page_id ) ) );
				$index++;
			}
		}

		return $pages_list;
	}

	public static function get_user_sessions_for_trending( $page_id ) {
		global $wpdb;
		$views_tbl = self::get_table_name( self::VIEWS );
		return count( $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT(user_session_id) as sessions FROM ' . $views_tbl . ' WHERE `page_id` = %d GROUP BY `user_session_id`', $page_id ) ) );
	}

	public static function get_visitors_for_trending( $page_id ) {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );

		return count( $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT(visitor_id) as visitors FROM ' . $user_sessions . ' LEFT JOIN ' . $views_tbl . ' ON ' . $user_sessions . '.user_session_id = ' . $views_tbl . '.user_session_id WHERE `page_id` = %d GROUP BY `visitor_id`', $page_id ) ) );
	}

	public static function get_top_country() {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );
		$countries     = self::get_table_name( self::COUNTRIES );

		$pages    = $wpdb->get_results( 'SELECT COUNT(*) as views, country FROM ' . $countries . ' LEFT JOIN ' . $user_sessions . ' ON ' . $countries . '.country_id = ' . $user_sessions . '.country_id LEFT JOIN ' . $views_tbl . ' ON ' . $views_tbl . '.user_session_id = ' . $user_sessions . '.user_session_id WHERE country IS NOT NULL GROUP BY country ORDER BY views DESC LIMIT 10' );
		$cnt_list = array();

		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$country_id                          = self::get_country_id( $page->country );
				$cnt_list[ $index ]['country']       = $page->country;
				$cnt_list[ $index ]['views']         = APVC_Admin_Helpers::filter_count_format( $page->views );
				$cnt_list[ $index ]['visitors']      = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_country( $country_id ) );
				$cnt_list[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_country( $country_id ) );
				$index++;
			}
		}

		return $cnt_list;
	}

	public static function get_visitors_for_country( $country ) {
		global $wpdb;
		$sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(country_id) as country_id FROM ' . $sessions_tbl . ' WHERE `country_id` = %d GROUP BY visitor_id', $country ) );
	}

	public static function get_country_id( $country ) {
		global $wpdb;
		$country_tbl = self::get_table_name( self::COUNTRIES );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT country_id as country FROM ' . $country_tbl . ' WHERE `country` = %s', $country ) );
	}

	public static function get_user_sessions_for_country( $country ) {
		global $wpdb;
		$user_sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(user_session_id) as users FROM ' . $user_sessions_tbl . ' WHERE `country_id` = %d', $country ) );
	}

	public static function get_top_states() {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );
		$states        = self::get_table_name( self::STATES );

		$pages    = $wpdb->get_results( 'SELECT COUNT(*) as views, state_name FROM ' . $states . ' LEFT JOIN ' . $user_sessions . ' ON ' . $states . '.state_id = ' . $user_sessions . '.state_id LEFT JOIN ' . $views_tbl . ' ON ' . $views_tbl . '.user_session_id = ' . $user_sessions . '.user_session_id WHERE state_name IS NOT NULL GROUP BY state_name ORDER BY views DESC LIMIT 10' );
		$cnt_list = array();
		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$state_id                            = APVC_Helper_Functions::get_state_id( $page->state_name );
				$cnt_list[ $index ]['state']         = $page->state_name;
				$cnt_list[ $index ]['views']         = APVC_Admin_Helpers::filter_count_format( $page->views );
				$cnt_list[ $index ]['visitors']      = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_states( $state_id ) );
				$cnt_list[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_states( $state_id ) );
				$index++;
			}
		}

		return $cnt_list;
	}

	public static function get_visitors_for_states( $state_id ) {
		global $wpdb;
		$sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(state_id) FROM ' . $sessions_tbl . ' WHERE `state_id` = %d', $state_id ) );
	}

	public static function get_user_sessions_for_states( $state_id ) {
		global $wpdb;
		$user_sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(user_session_id) FROM ' . $user_sessions_tbl . ' WHERE `state_id` = %d GROUP BY user_session_id', $state_id ) );
	}

	public static function get_top_cities() {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );
		$cities        = self::get_table_name( self::CITIES );

		$pages     = $wpdb->get_results( 'SELECT COUNT(*) as views, city FROM ' . $cities . ' LEFT JOIN ' . $user_sessions . ' ON ' . $cities . '.city_id = ' . $user_sessions . '.city_id LEFT JOIN ' . $views_tbl . ' ON ' . $views_tbl . '.user_session_id = ' . $user_sessions . '.user_session_id WHERE city IS NOT NULL GROUP BY city ORDER BY views DESC LIMIT 10' );
		$city_list = array();

		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$city_id                              = APVC_Helper_Functions::get_city_id( $page->city );
				$city_list[ $index ]['city']          = $page->city;
				$city_list[ $index ]['views']         = APVC_Admin_Helpers::filter_count_format( $page->views );
				$city_list[ $index ]['visitors']      = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_states( $city_id ) );
				$city_list[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_states( $city_id ) );
				$index++;
			}
		}

		return $city_list;
	}

	public static function get_visitors_for_cities( $city_id ) {
		global $wpdb;
		$sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(city_id) FROM ' . $sessions_tbl . ' WHERE `city_id` = %d', $city_id ) );
	}

	public static function get_user_sessions_for_cities( $city_id ) {
		global $wpdb;
		$user_sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(user_session_id) FROM ' . $user_sessions_tbl . ' WHERE `city_id` = %d GROUP BY user_session_id', $city_id ) );
	}

	public static function get_top_devices() {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );
		$pages         = $wpdb->get_results( 'SELECT COUNT(*) as views, device_type FROM ' . $user_sessions . ' LEFT JOIN ' . $views_tbl . ' ON ' . $user_sessions . '.user_session_id = ' . $views_tbl . '.user_session_id WHERE device_type != "" GROUP BY device_type ORDER BY views DESC LIMIT 10' );
		$dvc_list      = array();
		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$dvc_list[ $index ]['device']        = $page->device_type;
				$dvc_list[ $index ]['views']         = APVC_Admin_Helpers::filter_count_format( $page->views );
				$dvc_list[ $index ]['visitors']      = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_devices( $page->device_type ) );
				$dvc_list[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_devices( $page->device_type ) );
				$index++;
			}
		}

		return $dvc_list;
	}

	public static function get_visitors_for_devices( $device_type ) {
		global $wpdb;
		$sessions_tbl = self::get_table_name( self::SESSIONS );
		return count( $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT(visitor_id) as visitor_id FROM ' . $sessions_tbl . ' WHERE `device_type` = %s GROUP BY `visitor_id`', $device_type ) ) );
	}

	public static function get_user_sessions_for_devices( $device_type ) {
		global $wpdb;
		$user_sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(user_session_id) as users FROM ' . $user_sessions_tbl . ' WHERE `device_type` = %s', $device_type ) );
	}

	public static function get_top_devices_os() {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );
		$pages         = $wpdb->get_results( 'SELECT COUNT(*) as views, device_os FROM ' . $user_sessions . ' LEFT JOIN ' . $views_tbl . ' ON ' . $user_sessions . '.user_session_id = ' . $views_tbl . '.user_session_id WHERE device_os != "" GROUP BY device_os ORDER BY views DESC LIMIT 10' );

		$dvc_list = array();
		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$dvc_list[ $index ]['device_os']     = $page->device_os;
				$dvc_list[ $index ]['views']         = APVC_Admin_Helpers::filter_count_format( $page->views );
				$dvc_list[ $index ]['visitors']      = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_device_os( $page->device_os ) );
				$dvc_list[ $index ]['user_sessions'] = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_device_os( $page->device_os ) );
				$index++;
			}
		}

		return $dvc_list;
	}

	public static function get_visitors_for_device_os( $device_os ) {
		global $wpdb;
		$sessions_tbl = self::get_table_name( self::SESSIONS );
		return count( $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT(visitor_id) as visitor_id FROM ' . $sessions_tbl . ' WHERE `device_os` = %s GROUP BY `visitor_id`', $device_os ) ) );
	}

	public static function get_user_sessions_for_device_os( $device_os ) {
		global $wpdb;
		$user_sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(user_session_id) as users FROM ' . $user_sessions_tbl . ' WHERE `device_os` = %s', $device_os ) );
	}

	public static function get_top_browsers() {
		global $wpdb;
		$views_tbl     = self::get_table_name( self::VIEWS );
		$user_sessions = self::get_table_name( self::SESSIONS );
		$pages         = $wpdb->get_results( 'SELECT COUNT(*) as views, device_browser FROM ' . $user_sessions . ' LEFT JOIN ' . $views_tbl . ' ON ' . $user_sessions . '.user_session_id = ' . $views_tbl . '.user_session_id WHERE device_browser != "" GROUP BY device_browser ORDER BY views DESC LIMIT 10' );

		$dvc_list = array();
		if ( ! empty( $pages ) && count( $pages ) > 0 ) {
			$index = 0;
			foreach ( $pages as $page ) {
				$dvc_list[ $index ]['device_browser'] = $page->device_browser;
				$dvc_list[ $index ]['views']          = APVC_Admin_Helpers::filter_count_format( $page->views );
				$dvc_list[ $index ]['visitors']       = APVC_Admin_Helpers::filter_count_format( self::get_visitors_for_device_browser( $page->device_browser ) );
				$dvc_list[ $index ]['user_sessions']  = APVC_Admin_Helpers::filter_count_format( self::get_user_sessions_for_device_browser( $page->device_browser ) );
				$index++;
			}
		}

		return $dvc_list;
	}

	public static function get_visitors_for_device_browser( $device_browser ) {
		global $wpdb;
		$sessions_tbl = self::get_table_name( self::SESSIONS );
		return count( $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT(visitor_id) as visitor_id FROM ' . $sessions_tbl . ' WHERE `device_browser` = %s GROUP BY `visitor_id`', $device_browser ) ) );
	}

	public static function get_user_sessions_for_device_browser( $device_browser ) {
		global $wpdb;
		$user_sessions_tbl = self::get_table_name( self::SESSIONS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(user_session_id) as users FROM ' . $user_sessions_tbl . ' WHERE `device_browser` = %s', $device_browser ) );
	}

	public static function get_campaign_full_url( $code ) {
		global $wpdb;
		$campaign_urls = self::get_table_name( self::CAMPAIGN_URLS );
		return $wpdb->get_var( $wpdb->prepare( 'SELECT campaign_url as users FROM ' . $campaign_urls . ' WHERE `campaign_url_code` = %s', $code ) );
	}

}
