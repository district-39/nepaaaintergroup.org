<?php

class APVC_Admin_Rest_API extends WP_REST_Request {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_apis' ) );
	}

	public function register_rest_apis() {
		$GLOBALS['user_id'] = get_current_user_id();

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/dashboard_stats',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_dashboard_stats_with_range' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/get_reports_stats_with_range',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_reports_stats_with_pagination' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/reports_stats',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_reports_stats_with_pagination' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/reports_det_stats',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_reports_det_stats_with_pagination' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/refer_refresh_url',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_refer_refresh_data' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/generate_shortcode',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'generate_shortcode' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/save_shortcode',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'save_shortcode' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/delete_shortcode',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'delete_shortcode' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/get_starting_count',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_starting_count' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/set_starting_count',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'set_starting_count' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/reset_everything',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'reset_everything' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/start_migration',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'start_migration' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/migrate_data',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'migrate_data' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);


		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/migrate_records',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'migrate_records_count' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/update_migration',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'update_migration' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/start_fresh',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'start_fresh' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/clean_old_stats',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'clean_old_stats' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/dash_get_yearly_stats',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'dash_get_yearly_stats' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/dash_get_monthly_stats',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'dash_get_monthly_stats' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/dash_get_weekly_stats',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'dash_get_weekly_stats' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/dash_get_daily_stats',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'dash_get_daily_stats' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_articles',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_pages' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_posts',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_posts' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_countries',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_countries' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_states',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_states' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_cities',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_cities' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_devices',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_devices' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_devices_os',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_devices_os' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trend_get_top_browsers',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trend_get_top_browsers' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trending_get_referrer',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trending_get_referrer' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

		register_rest_route(
			'apvc/' . APVC_REST_VERSION,
			'/trending_refferer_list',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'trending_get_referrer_list' ),
				'permission_callback' => function() {
					return array( $this, 'get_custom_users_data' );
				},
			)
		);

	}

	public function get_custom_users_data() {
		return get_user_by( 'id', $GLOBALS['user_id'] );
	}

	public function get_dashboard_stats_with_range( WP_REST_Request $request ) {
		if ( ! $this->get_custom_users_data() ) {
			die( __( 'Something went wrong! Please try again.', 'apvc' ) );
		}
		$start_date = $request->get_param( 'startDate' );
		$end_date   = $request->get_param( 'endDate' );
		$page   = $request->get_param( 'page' );

		$all_data               = APVC_Query::get_all_states_with_range( $start_date, $end_date, $page );
		$dash_stats             = array();
		$chart_data             = array();
		$chart_data['date']     = array_column( $all_data, 'date' );
		$chart_data['views']    = array_column( $all_data, 'views' );
		$chart_data['visitors'] = array_column( $all_data, 'visitors' );
		$chart_data['sessions'] = array_column( $all_data, 'sessions' );

		$dash_stats['views']        = array_sum( array_column( $all_data, 'views' ) );
		$dash_stats['visitors']     = array_sum( array_column( $all_data, 'visitors' ) );
		$dash_stats['sessions']     = array_sum( array_column( $all_data, 'sessions' ) );
		$dash_stats['unq_visitors'] = array_sum( array_column( $all_data, 'unq_visitors' ) );

		return array(
			'stats'     => $dash_stats,
			'all_stats' => $chart_data,
		);
	}


	public function get_reports_stats_with_pagination( WP_REST_Request $request ) {
		if ( ! $this->get_custom_users_data() ) {
			die( __( 'Something went wrong! Please try again.', 'apvc' ) );
		}
		$args              = array();
		$per_page          = $request->get_param( 'per_page' );
		$limit             = $request->get_param( 'limit' );
		$args['post_type'] = $request->get_param( 'post_type' );
		$all_data          = APVC_Query::get_dashboard_list_posts_with_pagination( $limit, $per_page, $args );
		return array( 'reports' => $all_data );
	}

	public function get_reports_det_stats_with_pagination( WP_REST_Request $request ) {
		if ( ! $this->get_custom_users_data() ) {
			die( __( 'Something went wrong! Please try again.', 'apvc' ) );
		}
		$p_id     = $request->get_param( 'p_id' );
		$per_page = $request->get_param( 'per_page' );
		$limit    = $request->get_param( 'limit' );

		$helpers = new APVC_Admin_Helpers();
		$args    = array();
		if ( $helpers::get_parameter( 'startDate' ) ) {
			$args['startDate'] = $helpers::get_parameter( 'startDate' );
			$args['endDate']   = $helpers::get_parameter( 'endDate' );
		}
		if ( $helpers::get_parameter( 'device_type' ) ) {
			$args['device_type'] = $helpers::get_parameter( 'device_type' );
		}
		if ( $helpers::get_parameter( 'device_os' ) ) {
			$args['device_os'] = $helpers::get_parameter( 'device_os' );
		}
		if ( $helpers::get_parameter( 'device_br' ) ) {
			$args['device_br'] = $helpers::get_parameter( 'device_br' );
		}
		if ( $helpers::get_parameter( 'set_filter' ) ) {
			$args['set_filter'] = $helpers::get_parameter( 'set_filter' );
		}
		if ( $helpers::get_parameter( 'p_id' ) ) {
			$args['p_id'] = $helpers::get_parameter( 'p_id' );
		}

		$all_data = APVC_Query::get_detailed_reports_with_pagi( $p_id, $limit, $per_page, $args );
		return array( 'reports' => $all_data );
	}

	public function get_refer_refresh_data( WP_REST_Request $request ) {
		$startDate = $request->get_param( 'startDate' );
		$endDate   = $request->get_param( 'endDate' );
		return APVC_Query::get_trending_referrer_name( $startDate, $endDate );
	}

	public function generate_shortcode( WP_REST_Request $request ) {

		$border_size          = $request->get_param( 'border_size' );
		$border_style         = $request->get_param( 'border-style' );
		$border_color         = $request->get_param( 'border_color' );
		$border_radius        = $request->get_param( 'border_radius' );
		$font_color           = $request->get_param( 'font_color' );
		$background_color     = $request->get_param( 'background_color' );
		$font_style           = $request->get_param( 'font-style' );
		$padding              = $request->get_param( 'padding' );
		$width                = $request->get_param( 'width' );
		$for_specific_post    = $request->get_param( 'for_specific_post' );
		$show_icon    = $request->get_param( 'show_icon' );
		$show_today_visits    = $request->get_param( 'show_today_visits' );
		$show_global_visits   = $request->get_param( 'show_global_visits' );
		$show_current_total_visits    = $request->get_param( 'show_current_total_visits' );
		$counter_label        = $request->get_param( 'counter_label' );
		$today_counter_label  = $request->get_param( 'today_counter_label' );
		$global_counter_label = $request->get_param( 'global_counter_label' );
		$widget_template      = $request->get_param( 'widget-template' );

		$shortcode_start   = '[apvc_widget';
		$shortcode_content = '';
		$shortcode_end     = ']';

		if ( ! empty( $widget_template ) ) {
			$shortcode_content = ' template="' . $widget_template . '"';
		}
		if ( ! empty( $border_size ) ) {
			$shortcode_content .= ' border_size="' . $border_size . '"';
		}
		if ( ! empty( $border_style ) ) {
			$shortcode_content .= ' border_style="' . $border_style . '"';
		}
		if ( ! empty( $border_color ) ) {
			$shortcode_content .= ' border_color="' . $border_color . '"';
		}
		if ( ! empty( $border_radius ) ) {
			$shortcode_content .= ' border_radius="' . $border_radius . '"';
		}
		if ( ! empty( $font_color ) ) {
			$shortcode_content .= ' font_color="' . $font_color . '"';
		}
		if ( ! empty( $background_color ) ) {
			$shortcode_content .= ' background_color="' . $background_color . '"';
		}
		if ( ! empty( $font_style ) ) {
			$shortcode_content .= ' font_style="' . $font_style . '"';
		}
		if ( ! empty( $padding ) ) {
			$shortcode_content .= ' padding="' . $padding . '"';
		}
		if ( ! empty( $width ) ) {
			$shortcode_content .= ' width="' . $width . '"';
		}
		if ( ! empty( $for_specific_post ) ) {
			$shortcode_content .= ' for_specific_post="' . $for_specific_post . '"';
		}
		if ( ! empty( $show_current_total_visits ) ) {
			$shortcode_content .= ' show_current_page_total_visits="' . $show_current_total_visits . '"';
			if ( ! empty( $counter_label ) ) {
				$shortcode_content .= ' counter_label="' . $counter_label . '"';
			}
		}
		if ( ! empty( $show_today_visits ) ) {
			$shortcode_content .= ' show_today_visits="' . $show_today_visits . '"';
			if ( ! empty( $today_counter_label ) ) {
				$shortcode_content .= ' today_counter_label="' . $today_counter_label . '"';
			}

		}
		if ( ! empty( $show_global_visits ) ) {
			$shortcode_content .= ' show_global_visits="' . $show_global_visits . '"';
			if ( ! empty( $global_counter_label ) ) {
				$shortcode_content .= ' global_counter_label="' . $global_counter_label . '"';
			}
		}



		if ( ! empty( $show_icon ) ) {
			$shortcode_content .= ' show_icon="1"';
		}
		return wp_send_json_success( array( 'shortcode' => $shortcode_start . $shortcode_content . $shortcode_end, 'generated' => do_shortcode($shortcode_start . $shortcode_content . $shortcode_end) ) );
	}

	public function save_shortcode( WP_REST_Request $request ) {
		$shortcode_name = $request->get_param( 'shortcode_name' );
		$shortcode_val  = $request->get_param( 'shortcode_val' );
		return wp_send_json_success( array( 'results' => APVC_Query::save_shortcode( $shortcode_name, $shortcode_val ) ) );
	}

	public function delete_shortcode( WP_REST_Request $request ) {
		$delete_id = $request->get_param( 'id' );
		if ( true == APVC_Query::delete_shortcode( $delete_id ) ) {
			return wp_send_json_success( array( 'results' => __( 'Shortcode deleted successfully.', 'apvc' ) ) );
		} else {
			return wp_send_json_error( array( 'results' => __( 'Something went wrong! Please try again.', 'apvc' ) ) );
		}
	}

	public function get_starting_count( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );
		return wp_send_json_success( array( 'results' => APVC_Query::get_starting_count( $id ) ) );
	}

	public function set_starting_count( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );
		$count = $request->get_param( 'count' );
		return wp_send_json_success( array( 'results' => APVC_Query::set_starting_count( $id, $count ) ) );
	}

	public function reset_everything() {
		return APVC_Query::reset_everything();
	}

	public function start_migration() {
		global $wpdb;
		$mig = new APVC_Migration();
		$mig->start_migration();
		update_option( 'apvc_new_version_show_notice', 'no' );
		return wp_send_json_success( 'Done' );
	}

	public function migrate_data( WP_REST_Request $request ) {
		$chunkIndex = $request->get_param( 'chunkIndex' );
		$chunkSize = $request->get_param( 'chunkSize' );

		$mig = new APVC_Migration();
		return $mig->migrating_data( $chunkIndex, $chunkSize );
	}

	public function migrate_records_count( WP_REST_Request $request ) {
		$mig = new APVC_Migration();
		return wp_send_json_success( [ 'count' => $mig->migrate_records_count() ] );
	}

	public function update_migration() {
		$mig = new APVC_Migration();
		return wp_send_json_success( [ 'count' => $mig->update_migration() ] );
	}

	public function start_fresh( $request ) {
		$mig = new APVC_Migration();
		$fresh = $request->get_param( 'action' );
		return wp_send_json_success( $mig->start_fresh( $fresh ) );
	}

	public function clean_old_stats() {
		$mig = new APVC_Migration();
		return wp_send_json_success( $mig->clean_old_stats() );
	}

	public function dash_get_yearly_stats() {
		return wp_send_json_success( APVC_Query::get_yearly_stats_query() );
	}

	public function dash_get_monthly_stats() {
		return wp_send_json_success( APVC_Admin_Helpers::filter_count_format( APVC_Query::get_monthly_stats_query() ) );
	}

	public function dash_get_weekly_stats() {
		return wp_send_json_success( APVC_Admin_Helpers::filter_count_format( APVC_Query::get_weekly_stats_query() ) );
	}

	public function dash_get_daily_stats() {
		return wp_send_json_success( APVC_Admin_Helpers::filter_count_format( APVC_Query::get_daily_stats_query() ) );
	}

	public function trend_get_top_pages() {
		$ajax_data = get_transient( 'apvc_trend_top_pages' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_articles( 'page' );
			delete_transient( 'apvc_trend_top_pages' );
			set_transient( 'apvc_trend_top_pages', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trend_get_top_posts() {
		$ajax_data = get_transient( 'apvc_trend_top_posts' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_articles( 'post' );
			delete_transient( 'apvc_trend_top_posts' );
			set_transient( 'apvc_trend_top_posts', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trend_get_top_countries() {
		$ajax_data = get_transient( 'apvc_trend_top_countries' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_country();
			delete_transient( 'apvc_trend_top_countries' );
			set_transient( 'apvc_trend_top_countries', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trend_get_top_states() {
		$ajax_data = get_transient( 'apvc_trend_top_states' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_states();
			delete_transient( 'apvc_trend_top_states' );
			set_transient( 'apvc_trend_top_states', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trend_get_top_cities() {
		$ajax_data = get_transient( 'apvc_trend_top_cities' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_cities();
			delete_transient( 'apvc_trend_top_cities' );
			set_transient( 'apvc_trend_top_cities', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trend_get_top_devices() {
		$ajax_data = get_transient( 'apvc_trend_top_devices' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_devices();
			delete_transient( 'apvc_trend_top_devices' );
			set_transient( 'apvc_trend_top_devices', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trend_get_top_devices_os() {
		$ajax_data = get_transient( 'apvc_trend_top_devices_os' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_devices_os();
			delete_transient( 'apvc_trend_top_devices_os' );
			set_transient( 'apvc_trend_top_devices_os', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );

	}

	public function trend_get_top_browsers() {
		$ajax_data = get_transient( 'apvc_trend_top_browsers' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_top_browsers();
			delete_transient( 'apvc_trend_top_browsers' );
			set_transient( 'apvc_trend_top_browsers', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trending_get_referrer() {
		$ajax_data = get_transient( 'apvc_trend_refferer' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_trending_referrer_name();
			delete_transient( 'apvc_trend_refferer' );
			set_transient( 'apvc_trend_refferer', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );
	}

	public function trending_get_referrer_list() {
		$ajax_data = get_transient( 'apvc_trend_refferer_list' );
		if( false !== $ajax_data ){
			return wp_send_json_success( $ajax_data );
		} else {
			$ajax_data = APVC_Query::get_referrers_list();
			delete_transient( 'apvc_trend_refferer_list' );
			set_transient( 'apvc_trend_refferer_list', $ajax_data, 3600 );
		}

		return wp_send_json_success( $ajax_data );

	}


}
