<?php /**
 * @version 1.0
 * @description AJX_Bookings
 * @category  AJX_Bookings Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2020-01-23
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WPBC_AJX_Bookings {

	/**
	 * Static Variables
	 *
	 * @var string[]
	 */
	public static $data_separator = array(
		'r_separator' => '~',
		'f_separator' => '^',
	);


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  JS | CSS files | Tpl loading  /// "  >

	// JS | CSS  =======================================================================================================.

	/**
	 * Define HOOKs for loading CSS and  JavaScript files
	 */
	public function init_load_css_js_tpl() {

		// Load only  at  AJX_Bookings Settings Page.
		if ( 'vm_booking_listing' === wpbc_get_default_saved_view_mode_for_wpbc_page() ) {

			add_action( 'wpbc_enqueue_js_files', array( $this, 'js_load_files' ), 50 );
			add_action( 'wpbc_enqueue_css_files', array( $this, 'enqueue_css_files' ), 50 );

			add_action( 'wpbc_hook_settings_page_footer', array( $this, 'hook__page_footer_tmpl' ) );
		}
	}


	/**
	 * JSS.
	 *
	 * @param string $where_to_load - slug.
	 *
	 * @return void
	 */
	public function js_load_files( $where_to_load ) {

		$in_footer = true;

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ), true ) ) ) {

			wp_enqueue_script( 'wpbc-booking_ajx_toolbar_hooks', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings__hooks.js', array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc-booking_ajx_listing', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings__listing.js', array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc-booking_ajx_actions', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings__actions.js', array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc-boo_listing_ajx_actions', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/boo_listing__actions.js', array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
		}
	}


	/**
	 * CSS
	 *
	 * @param string $where_to_load - slug.
	 *
	 * @return void
	 */
	public function enqueue_css_files( $where_to_load ) {
	}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  Templates  /// "  >

	// Templates ===================================================================================================

	/**
	 * Templates at footer of page
	 *
	 * @param string $page -  'wpbc-ajx_booking'.
	 */
	public function hook__page_footer_tmpl( $page ) {

		if ( 'wpbc-ajx_booking' === $page ) {              // it's from >>  do_action( 'wpbc_hook_settings_page_footer', 'wpbc-ajx_booking' );.

			wpbc_template__booking_listing_header();
			wpbc_template__booking_listing_footer();
			wpbc_template__booking_listing_row();

			$this->template__content_data();
		}
	}

	private function template__content_data(){

		// Content Data.
		?><script type="text/html" id="tmpl-wpbc_content_data">
			<strong>{{data.key}}</strong>:<span class="fieldvalue {{data.key}}<#
			if ( 	( data.keyword != '' )
				 && ( undefined != data.value )
				 && (  -1 != data.value.toLowerCase().indexOf( data.keyword.trim().toLowerCase() )  )
			) {
				#> fieldsearchvalue<#
			}
			if ( 	( undefined != data.value )
				 && (  -1 != data.value.toLowerCase().indexOf( 'refund' )  )
			) {
				#> _refund<#
			}
			#>">{{{data.value}}}</span>&nbsp;&nbsp;
		</script><?php
	}

	// </editor-fold>


	// <editor-fold     defaultstate="collapsed"                        desc=" ///  A J A X  /// "  >

	// A J A X =========================================================================================================.

	/**
	 * Define HOOKs for start  loading Ajax
	 */
	public function define_ajax_hook() {

		// Ajax Handlers. Note. "locale_for_ajax" rechecked in wpbc-ajax.php.
		add_action( 'wp_ajax_' . 'WPBC_AJX_BOOKING_LISTING', array( $this, 'ajax_' . 'WPBC_AJX_BOOKING_LISTING' ) );        // Admin & Client (logged in usres).

		// Ajax Handlers for actions.
		add_action( 'wp_ajax_' . 'WPBC_AJX_BOOKING_ACTIONS', 'wpbc_ajax_' . 'WPBC_AJX_BOOKING_ACTIONS' );
		// add_action( 'wp_ajax_nopriv_' . 'WPBC_AJX_BOOKING_LISTING', array( $this, 'ajax_' . 'WPBC_AJX_BOOKING_LISTING' ) );	    // Client         (not logged in).
	}



		/**
		 * Ajax - Get Listing Data and Response to JS script
		 */
		public function ajax_WPBC_AJX_BOOKING_LISTING() {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['search_params'] ) || empty( $_POST['search_params'] ) ) { exit; }

			// Security  -----------------------------------------------------------------------------------------------    // in Ajax Post:   'nonce': wpbc_ajx_booking_listing.get_secure_param( 'nonce' ),.
			$action_name    = 'wpbc_ajx_booking_listing_ajx' . '_wpbcnonce';
			$nonce_post_key = 'nonce';
			$result_check   = check_ajax_referer( $action_name, $nonce_post_key );

			$user_id = ( isset( $_REQUEST['wpbc_ajx_user_id'] ) ) ? intval( $_REQUEST['wpbc_ajx_user_id'] ) : wpbc_get_current_user_id();  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

			/**
			 * SQL  ---------------------------------------------------------------------------
			 *
			 * in Ajax Post:  'search_params': wpbc_ajx_booking_listing.search_get_all_params()
			 *
			 * Use prefix "search_params", if Ajax sent -
			 *                 $_REQUEST['search_params']['page_num'], $_REQUEST['search_params']['page_items_count'],..
			 */

			$user_request = new WPBC_AJX__REQUEST(
				array(
					'db_option_name'          => 'booking_listing_request_params',
					'user_id'                 => $user_id,                            // FixIn: 9.4.3.2.
					'request_rules_structure' => wpbc_ajx_get__request_params__names_default(),
				)
			);

			$request_prefix = 'search_params';
			$request_params = $user_request->get_sanitized__in_request__value_or_default( $request_prefix );            // NOT Direct: $_REQUEST['search_params']['resource_id'].


			$data_arr = wpbc_ajx_get_booking_data_arr( $request_params );

			$new_bookings_count = wpbc_db_get_number_new_bookings();

			if ( 'make_reset' === $request_params['ui_reset'] ) {

				$is_reseted = wpbc_ajx__user_request_params__delete( $user_id );

				$request_params['ui_reset']     = $is_reseted ? 'reset_done' : 'reset_error';
				$request_params['ui_reset_url'] = wpbc_get_bookings_url() . '&tab=vm_booking_listing';

			} else {
				$is_success_update = wpbc_ajx__user_request_params__save( $request_params, $user_id );    // - $request_params - serialized here automatically.
			}

			// ---------------------------------------------------------------------------------------------------------.
			// Send JSON. Its will make "wp_json_encode" - so pass only array, and This function call wp_die( '', '', array( 'response' => null, ) )   Pass JS OBJ: response_data in "jQuery.post( " function on success.
			wp_send_json(
				array(
					'ajx_count'              => $data_arr['count'],
					'ajx_items'              => $data_arr['data_arr'],
					'ajx_booking_resources'  => $data_arr['booking_resources'],
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					'ajx_search_params'      => $_REQUEST['search_params'],
					'ajx_cleaned_params'     => $request_params,
					'ajx_new_bookings_count' => $new_bookings_count,
				)
			);
		}

	// </editor-fold>


}


/**
 * Just for loading CSS and  JavaScript files
 */
if ( true ) {
	$wpbc_ajx_booking_loading = new WPBC_AJX_Bookings();
	$wpbc_ajx_booking_loading->init_load_css_js_tpl();
	$wpbc_ajx_booking_loading->define_ajax_hook();
}




////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// API Hooks
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Get booking data by  booking_ID
 *
 * @param $booking_id int
 *
 * @return false | stdClass Object (
										[booking_db] => stdClass Object (
												[booking_id] => 130
												[booking_options] =>
												[trash] => 0
												[sync_gid] => 15l1028ii1v95ctbpgcf2r2s2h_20171201
												[is_new] => 0
												[status] =>
												[sort_date] => 2022-08-08 00:00:00
												[modification_date] => 2022-07-24 11:04:32
												[form] => text^name1^Jessica~text^secondname1^....
												[hash] => 1d7dc4e06d95726bf9
												[booking_type] => 1
												[remark] =>
												[cost] => 100.00
												[pay_status] => Failed
												[pay_request] => 0
											)
										[id] => 130
										[approved] => 0
										[dates] 				=> Array ( [0] => '2022-08-08 00:00:00' [1] => '2022-08-09 00:00:00' [2] => '2022-08-10 00:00:00' )
										[child_id] 				=> Array ( [0] => '' [1] => '' [2] => '' )
										[short_dates] 			=> Array ( [0] => '2022-08-08 00:00:00' [1] => '-' [2] => '2022-08-10 00:00:00' )
										[short_dates_child_id] 	=> Array ( [0] => '' [1] => '' [2] => '' )
										[parsed_fields] => Array (
												[name] => Jessica
												[secondname] => Simson
												...
												[booking_id] => 130
												[trash] => 0
												[sync_gid] => 15l1028ii1v95ctbpgcf2r2s2h_20171201
												[is_new] => 0
												[status] =>
												[sort_date] => 2022-08-08 00:00:00
												[modification_date] => July 24, 2022 11:04
												[hash] => 1d7dc4e06d95726bf9060a66235b7dc6
												[booking_type] => 1
												[remark] =>
												[cost] => 100.00
												[pay_status] => Failed
												[pay_request] => 0
												[id] => 130
												[approved] => 0
												[booking_options] =>
												[is_paid] => 0
												[pay_print_status] => Failed
												[currency_symbol] => $
												[resource_title] => Standard
												[resource_id] => 1
												[resource_owner_user] => 1
												[google_calendar_link] => https://calendar.google.com/calendar/r/eventedit?text=...
											)
										[templates] => Array (
												[form_show] => 		First Name:Jessica
																	Last Name:Simson
																	Email:simson@gmail.com
																	Phone:724 895 34 88
																	Address:Oliver street 10
																	City:Manchester
																	Post code:78998
																	Country:UK
																	Adults: 2
																	Children: 0
																	Details:   I want a room with a terrace

												[form_show_nohtml] =>
																	First Name:Jessica
																	Last Name:Simson
																	Email:simson@gmail.com
																	Phone:724 895 34 88
																	Address:Oliver street 10
																	City:Manchester
																	Post code:78998
																	Country:UK
																	Adults: 2
																	Children: 0
																	Details:   I want a room with a terrace
												[short_dates_content] => August 8, 2022 - August 10, 2022
												[wide_dates_content] => August 8, 2022, August 9, 2022, August 10, 2022
												[payment_label_template] => Payment Failed
											)
									)
 */
function wpbc_search_booking_by_id( $booking_id ) {

	$booking_id = intval( $booking_id );

	if ( ! empty( $booking_id ) ) {
		$booking_data = wpbc_search_booking_by_keyword( 'id:' . $booking_id );

		if ( ! empty( $booking_data['data_arr'] ) ) {
			return $booking_data['data_arr'][0];
		}
	}

	return false;
}

/**
 * Search specific booking(s) by Keyword
 *
 * @param string   	$keyword								'email@serv.com'
 * @param array 	$search_params		default array()		 array( 'source' => 'csv' )
 *
 * @return array(
				[data_arr] => Array (
									[0] => Array(
											[booking_id] => 2772
											[product_name] => Personal
											[date_placed] => 2019-10-16
											[order] => XXA3443ASDDA-232423-423423
											[email] => email@serv.com
											[_license_key] => 74826576578436
											[full_product_name] => Personal (single site)
											 ....
										)
					)
				[count] => 1
 *              
 */
 function wpbc_search_booking_by_keyword( $keyword , $search_params = array() ){

	 $ajx_booking_listing = new WPBC_AJX_Bookings;

	 $request_params = array(
		 'page_num'         => 1,
		 'page_items_count' => 99999,
		 'sort'             => 'booking_id',
		 'sort_type'        => 'DESC',
		 'keyword'          => ''
	 );

	 // Get Default Parameters
	 $default_param_values = wpbc_ajx_get__request_params__names_default( 'default' );
	 $request_params       = wp_parse_args( $request_params, $default_param_values );

	// Get Search Parameters,  if passed into  function
	 $request_params = wp_parse_args( $search_params, $request_params );

	 $request_params['keyword'] = wpbc_sanitize_text( $keyword );

	 $ajx_booking_arr = wpbc_ajx_get_booking_data_arr( $request_params );

	 return $ajx_booking_arr;
 }
 add_filter( 'wpbc_search_booking_by_keyword' , 'wpbc_search_booking_by_keyword' ,10, 2 );

/**
 * DevApi:   apply_filters( 'wpbc_search_booking_by_keyword',  ' d1ca3d0b476c ', array( 'source' => 'csv' )  );
 */
