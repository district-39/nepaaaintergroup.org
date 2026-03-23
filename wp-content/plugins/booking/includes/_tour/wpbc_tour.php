<?php
/**
 * @version     1.0
 * @package     Tour
 * @subpackage  Tour in Booking Calendar
 * @category    Tour
 *
 * @author      wpdevelop
 * @link        https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified    2024-07-31
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}                                             // Exit if accessed directly

// FixIn: 10.4.0.1.

class WPBC_Tour_01 {

	// <editor-fold     defaultstate="collapsed"                        desc=" ///  A J A X  /// "  >

	// A J A X =====================================================================================================

	/**
	 * Define HOOKs for start  loading Ajax
	 */
	public function define_ajax_hook() {

		// Ajax Handlers.		Note. "locale_for_ajax" rechecked in wpbc-ajax.php
		add_action( 'wp_ajax_' . 'WPBC_AJX_FEEDBACK', array( $this, 'ajax_' . 'ajax_WPBC_AJX_Response' ) );        // Admin & Client (logged in usres)

		// Ajax Handlers for actions
		// add_action( 'wp_ajax_nopriv_' . 'WPBC_AJX_BOOKING_LISTING', array( $this, 'ajax_' . 'WPBC_AJX_BOOKING_LISTING' ) );	    // Client         (not logged in)
	}


	/**
	 * Ajax - Get Listing Data and Response to JS script
	 */
	public function ajax_WPBC_AJX_Response() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['action_params'] ) || empty( $_POST['action_params'] ) ) {
			exit;
		}

		$ajax_errors = new WPBC_AJAX_ERROR_CATCHING();

		// Security  -----------------------------------------------------------------------------------------------    // in Ajax Post:   'nonce': wpbc_ajx_booking_listing.get_secure_param( 'nonce' ),
		$action_name    = 'wpbc_ajx__feedback_ajx' . '_wpbcnonce';
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

		$user_request   = new WPBC_AJX__REQUEST( array(
				'db_option_name'          => 'booking_feedback_request_params',
				'user_id'                 => $user_id,
				'request_rules_structure' => array(
					'booking_action'        => array( 'validate' => array( 'feedback_01' ), 'default' => 'none' ),
					'feedback_stars'        => array( 'validate' => 'd', 'default' => 0 ),
					'feedback__note'        => array( 'validate' => 's', 'default' => '' ),
					'ui_clicked_element_id' => array( 'validate' => 's', 'default' => '' )
				)
			)
		);
		$request_prefix = 'action_params';
		$request_params = $user_request->get_sanitized__in_request__value_or_default( $request_prefix );                // NOT Direct: 	$_REQUEST['action_params']['feedback_stars']

		//----------------------------------------------------------------------------------------------------------

		$action_result = wpbc_booking_do_action__feedback_01( $request_params
			, array(
				'user_id' => $user_id
			)
		);

		$defaults      = array(
			'new_listing_params'   => false,        // required for Import Google Calendar bookings
			'after_action_result'  => false,
			/* translators: 1: ... */
			'after_action_message' => sprintf( __( 'No actions %s has been processed.', 'booking' )
				, ' <strong>' . $request_params['booking_action'] . '</strong> ' )
		);
		$action_result = wp_parse_args( $action_result, $defaults );

		// Check if there were some errors  --------------------------------------------------------------------------------
		$error_messages = $ajax_errors->get_error_messages();
		if ( ! empty( $error_messages ) ) {
			$action_result['after_action_message'] .= $error_messages;
		}


		//------------------------------------------------------------------------------------------------------------------
		// Send JSON. Its will make "wp_json_encode" - so pass only array, and This function call wp_die( '', '', array( 'response' => null, ) )		Pass JS OBJ: response_data in "jQuery.post( " function on success.
		wp_send_json( array(
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			'ajx_action_params'                      => $_REQUEST['action_params'],                     // Do not clean input parameters
			'ajx_cleaned_params'                     => $request_params,	                            // Cleaned input parameters
			'ajx_after_action_message'               => $action_result['after_action_message'],	        // Message to  show
			'ajx_after_action_result'                => (int) $action_result['after_action_result'],	// Result key 				0 | 1
			'ajx_after_action_result_all_params_arr' => $action_result	                                // All result parameters
		) );
	}

	// </editor-fold>


	/**
	 * Define HOOKs for loading CSS and  JavaScript files
	 */
	public function init_load_css_js() {

		//TODO: Uncomment this:     // JS & CSS Load in all Plugins menus
		//		add_action( 'wpbc_enqueue_js_files', array(  $this, 'js_load_files' ),      50 );
		//		add_action( 'wpbc_enqueue_css_files', array( $this, 'enqueue_css_files' ),  50 );


		add_action( 'admin_enqueue_scripts', array( $this, 'load_tour_in_plugins' ) );

		// Template
		//add_action( 'wpbc_hook_settings_page_footer', array( $this,	'wpbc_hidden_template__content_for_feedback_01'	), 50 );
	}

		public function load_tour_in_plugins($hook) {

			$pages = array( 'plugins.php' );

			if ( ! in_array( $hook, $pages ) ) {
				return;
			}

			$this->js_load_files( 'admin' );
			$this->enqueue_css_files( 'admin' );
		}


	/** JSS */
	public function js_load_files( $where_to_load ) {

		$in_footer = true;

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_script( 'wpbc_tether',    wpbc_plugin_url( '/vendors/tether/tether.js' ),                    array( 'jquery' ),          WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc_shepherd',  wpbc_plugin_url( '/vendors/tether-shepherd/shepherd.js' ),         array( 'wpbc_tether' ),     WP_BK_VERSION_NUM, $in_footer );
			wp_enqueue_script( 'wpbc_tour_01',   trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/wpbc_tour.js',  array( 'wpbc_shepherd' ),   WP_BK_VERSION_NUM, $in_footer );

			$tour_data = array(
				'plugins_page'    => array(
					/* translators: 1: ... */
					'title'  => sprintf( __( 'Welcome to %s', 'booking' ), '<strong>WP Booking Calendar</strong>' ),
					'text'   => sprintf( __('We\'ll guide you through the steps to set up WP Booking Calendar on your site.','booking'), '<strong>WP Booking Calendar</strong>' ),
					'button' => array(
						'text' => __( 'Let\'s go', 'booking' ),
						'url'  => wpbc_get_setup_wizard_page_url()      //  URL to  start  new Guide: wpbc_get_settings_url() . '&wpbc_setup_wizard=reset&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' )
					)
				),
				'setup_page'      => array(
					'title'       => 'Step 1 / 10',
					'text'        => 'To make a simple backup to your server, press this button. Or to set up regular backups and remote storage, go to <strong>settings</strong> ',
				),
				'button_end_tour' => array( 'text' => __( 'End Tour', 'booking' ) ),
				'button_next'     => array( 'text' => __( 'Next', 'booking' ) )
			);
			wp_localize_script( 'wpbc_tour_01', 'wpbc_tour_i18n', $tour_data );
		}
	}

	/** CSS */
	public function enqueue_css_files( $where_to_load ) {

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_style( 'wpbc_tour_01',       trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/wpbc-tour.css',       array(), WP_BK_VERSION_NUM );

		}
	}
}


/**
 * Just for loading CSS and  JavaScript files   and  define Ajax hook
 */
if (
	( ! wpbc_is_this_demo() ) &&
	( wpbc_setup_wizard_page__is_need_start() ) &&
	( wpbc_is_user_can_access_wizard_page() )
) {
	$wpbc_js_css_loading = new WPBC_Tour_01;
	$wpbc_js_css_loading->init_load_css_js();
}
