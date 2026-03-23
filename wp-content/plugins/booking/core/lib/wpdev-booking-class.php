<?php
/**
 * @file: wp-content/plugins/booking/core/lib/wpdev-booking-class.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

class wpdev_booking {

    public $popover_front_end_js_is_writed;		//FixIn: Flex TimeLine 1.0		-- previos this was private and not public property

    // <editor-fold defaultstate="collapsed" desc="  C O N S T R U C T O R  &  P r o p e r t i e s ">

    var $wpdev_bk_personal;

    function __construct() {

		$this->popover_front_end_js_is_writed = false;
		$this->wpdev_bk_personal              = false;

	    if ( class_exists( 'wpdev_bk_personal' ) ) {
		    $this->wpdev_bk_personal = new wpdev_bk_personal();
	    }

	    add_action( 'init', array( $this, 'wpbc_shortcodes_init' ), 9999 );              // <- priority  to  load it last
    }
    // </editor-fold>


	/**
	 *  S H O R T C O D E s      Init
	 *
	 * @return void
	 */
	function wpbc_shortcodes_init(){
	    add_shortcode( 'bookingedit',            array( $this, 'bookingedit_shortcode' ) );
	    add_shortcode( 'bookingsearch',          array( $this, 'bookingsearch_shortcode' ) );
	    add_shortcode( 'bookingsearchresults',   array( $this, 'bookingsearchresults_shortcode' ) );
	    add_shortcode( 'bookingselect',          array( $this, 'bookingselect_shortcode' ) );
	    add_shortcode( 'bookingresource',        array( $this, 'bookingresource_shortcode' ) );
	    add_shortcode( 'bookingtimeline',        array( $this, 'bookingtimeline_shortcode' ) );
	    add_shortcode( 'bookingcustomerlisting', array( $this, 'bookingcustomerlisting_shortcode' ) );					// FixIn: 8.1.3.5.
	}


    // <editor-fold defaultstate="collapsed" desc="   S H O R T    C O D E S ">

	// FixIn: 8.1.3.5.
	/** Listing customners bookings in timeline view
	 *
	 * @param $attr	- The same parameters as for bookingtimeline shortcode (function)
	 *
	 * @return mixed|string|void
	 */
	function bookingcustomerlisting_shortcode( $attr ){

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return '<strong>' . esc_html__('This shortcode available in Pro versions,  only!' ,'booking') . '</strong> ';
		}

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingcustomerlisting', $attr );      // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );          //FixIn: 9.7.3.6.1

		// FixIn: 8.4.5.11.
		if (! is_array($attr)) {
			$attr = array();
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ( isset( $_GET['booking_hash'] ) ) || ( isset( $attr['booking_hash'] ) ) ) {


			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			if ( isset( $_GET['booking_hash'] ) ) {
				$get_booking_hash = ( ( isset( $_GET['booking_hash'] ) ) ? sanitize_text_field( wp_unslash( $_GET['booking_hash'] ) ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
				$my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $get_booking_hash );

				$attr['booking_hash'] = $get_booking_hash;
			} else {
				$my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $attr['booking_hash'] );
			}

			if ( $my_booking_id_type !== false ) {

				if ( ! isset( $attr['type' ] ) ) {																		// 8.1.3.5.2

					$br_list = wpbc_get_all_booking_resources_list();
					$br_list = array_keys( $br_list );
					$br_list = implode(',',$br_list);
					$attr['type' ] = $br_list;		//wpbc_get_default_resource();
				}
				if ( ! isset( $attr['view_days_num' ] ) ) {
					$attr['view_days_num' ] = 30;
				}
				if ( ! isset( $attr['scroll_start_date' ] ) ) {
					$attr['scroll_start_date' ] = '';
				}
				if ( ! isset( $attr['scroll_day' ] ) ) {
					$attr['scroll_day' ] = 0;
				}
				if ( ! isset( $attr['scroll_month' ] ) ) {
					$attr['scroll_month' ] = 0;
				}
				if ( ! isset( $attr['header_title' ] ) ) {
					$attr['header_title' ] = __( 'My bookings' , 'booking');
				}

				$timeline_results = $this->bookingtimeline_shortcode( $attr );

				return $timeline_results ;

			} else {
				return '<div class="wpbc_after_booking_thank_you_section"><div class="wpbc_ty__container"><div class="wpbc_ty__header"><strong>' . esc_html__('Oops!' ,'booking') . '</strong> ' . esc_html__('We could not find your booking. The link you used may be incorrect or has expired. If you need assistance, please contact our support team.' ,'booking') . '</div></div></div>';
			}

		} else {
			return __( 'This page can only be accessed through links in emails related to your booking.', 'booking' )
			       . ' <br/><em>'
			       /* translators: 1: ... */
			       . sprintf( __( 'Please check more about configuration at  %1$sthis page%2$s', 'booking' ), '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/" target="_blank">', '</a>.' )
			       . '</em>';
		}
	}

	/**
	 * TimeLine shortcode
	 *
	 * @param type $attr
	 *
	 * @return type
	 *
	 * Shortcodes exmaples:
	 *
	 *
	 ** Matrix:
	 * 1 Month View Mode:
	 * [bookingtimeline type="3,4,1,5,6,7,8,9,2,10,11,12,14" view_days_num=30 scroll_start_date="" scroll_month=0 header_title='All Bookings']
	 * 2 Months View Mode:
	 * [bookingtimeline type="1,5,6,7,8,9,2,10,11,12,3,4,14" view_days_num=60 scroll_start_date="" scroll_month=-1 header_title='All Bookings']
	 * 1 Week View Mode:
	 * [bookingtimeline type="3,4" view_days_num=7 scroll_start_date="" scroll_day=-7 header_title='All Bookings']
	 * 1 Day View Mode:
	 * [bookingtimeline type="3,4" view_days_num=1 scroll_start_date="" scroll_day=0 header_title='All Bookings']
	 ** Single:
	 * 1 Month  View Mode:
	 * [bookingtimeline type="4" view_days_num=30 scroll_start_date="" scroll_day=-15 scroll_month=0 header_title='All Bookings']
	 * 3 Months View Mode:
	 * [bookingtimeline type="4" view_days_num=90 scroll_start_date="" scroll_day=-30]
	 * 1 Year View Mode:
	 * [bookingtimeline type="4" view_days_num=365 scroll_start_date="" scroll_month=-3]
	 */
    function bookingtimeline_shortcode($attr) {

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingtimeline', $attr );      // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );          //FixIn: 9.7.3.6.1

    	// FixIn: 8.6.1.13.
		$timeline_results = bookingflextimeline_shortcode($attr);
		return $timeline_results;
    }


    // Show booking form for editing
    function bookingedit_shortcode($attr) {

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingedit', $attr );      // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );          //FixIn: 9.7.3.6.1


        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache

	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	    if ( isset( $_GET['wpbc_hash'] ) ) {

	    	if ( function_exists( 'wpbc_parse_one_way_hash' ) ) {

				$get_wpbc_hash = ( ( isset( $_GET['wpbc_hash'] ) ) ? sanitize_text_field( wp_unslash( $_GET['wpbc_hash'] ) ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */

			    $one_way_hash_response = wpbc_parse_one_way_hash( $get_wpbc_hash );

			    return $one_way_hash_response;
		    }
	    }

        $my_boook_count = get_bk_option( 'booking_client_cal_count' );
        $my_boook_type = 1;
        $my_booking_form = 'standard';
        $bk_otions = array();
        if ( isset( $attr['nummonths'] ) )   { $my_boook_count = intval( $attr['nummonths'] );  }
		if ( isset( $attr['resource_id'] ) ) {  $attr['type'] = intval( $attr['resource_id']); }
        if ( isset( $attr['type'] ) )        { $my_boook_type = intval( $attr['type']);        }
        if ( isset( $attr['form_type'] ) )   { $my_booking_form = $attr['form_type']; }
		if ( isset( $attr['agregate'] ) && ( ! empty( $attr['agregate'] ) ) ) {  // FixIn: 7.0.1.26.
			$my_boook_type .= ';' . $attr['agregate'];
		}
		if ( isset( $attr['aggregate'] ) && ( ! empty( $attr['aggregate'] ) ) ) {
			$my_boook_type .= ';' . $attr['aggregate'];
		}
		// Escape any XSS in aggregate parameter.
		$my_boook_type = str_replace( ',', ';', wpbc_clean_digit_or_csd( $my_boook_type ) );

		if ( isset( $attr['options'] ) ) { $bk_otions = $attr['options']; }


        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        if (isset($_GET['booking_hash'])) {
			$get_booking_hash = ( ( isset( $_GET['booking_hash'] ) ) ? sanitize_text_field( wp_unslash( $_GET['booking_hash'] ) ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
            $my_booking_id_type = wpbc_hash__get_booking_id__resource_id( $get_booking_hash );
            if ($my_booking_id_type !== false) {
	            $my_edited_bk_id = $my_booking_id_type[0];
	            $my_boook_type   = $my_booking_id_type[1];
                if ($my_boook_type == '') return '<div class="wpbc_after_booking_thank_you_section"><div class="wpbc_ty__container"><div class="wpbc_ty__header"><strong>' . esc_html__('Oops!' ,'booking') . '</strong> ' . esc_html__('We could not find your booking. The link you used may be incorrect or has expired. If you need assistance, please contact our support team.' ,'booking') . '</div></div></div>';
            } else {
                return '<div class="wpbc_after_booking_thank_you_section"><div class="wpbc_ty__container"><div class="wpbc_ty__header"><strong>' . esc_html__('Oops!' ,'booking') . '</strong> ' . esc_html__('We could not find your booking. The link you used may be incorrect or has expired. If you need assistance, please contact our support team.' ,'booking') . '</div></div></div>';
            }

        } else {
            return __('This page can only be accessed through links in emails related to your booking.' ,'booking')
                    . ' <br/><em>'
                        /* translators: 1: ... */
                        . sprintf( __( 'Please check more about configuration at  %1$sthis page%2$s', 'booking' )
									, '<a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/" target="_blank">' , '</a>.')
                    . '</em>';
        }


        $res = wpbc_get_rendered_booking_form_html($my_boook_type,$my_boook_count, 0 , $my_booking_form, '', false, $bk_otions );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        if (isset($_GET['booking_pay'])) {
            // Payment form
			if ( 'On' == get_bk_option( 'booking_super_admin_receive_regular_user_payments' ) ){								// FixIn: 9.2.3.8.
				make_bk_action('make_force_using_this_user', -999 );      													// '-999' - This ID "by default" is the ID of super booking admin user
			}

			$get_booking_hash = ( ( isset( $_GET['booking_hash'] ) ) ? sanitize_text_field( wp_unslash( $_GET['booking_hash'] ) ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
			$res = wpbc_do_shortcode__booking_confirm( array(
																'booking_hash' => $get_booking_hash
															) );

			if ( 'On' == get_bk_option( 'booking_super_admin_receive_regular_user_payments' ) ){								// FixIn: 9.2.3.8.
				make_bk_action( 'finish_force_using_this_user' );
			}

        }

        return $res;
    }

    // Search form
    function bookingsearch_shortcode($attr) {

	    if ( ! class_exists( 'wpdev_bk_personal' ) ) {
		    return '<strong>' . esc_html__( 'This shortcode available in Pro versions,  only!', 'booking' ) . '</strong> ';
	    }

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingsearch', $attr );      // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );          //FixIn: 9.7.3.6.1

	    $search_form = '';

	    if ( function_exists( 'wpbc_search_avy__show_search_form' ) ) {

			ob_start();

			$search_form_content = wpbc_search_avy__show_search_form( $attr );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $search_form_content;

		    $search_form = ob_get_clean();
	    }

        return $search_form ;
    }

	/**
	 * Search Results Shortcode   --   Show 'Search Results'    at      New Page
	 *
	 * @param $attr
	 *
	 * @return string
	 */
    function bookingsearchresults_shortcode($attr) {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return '<strong>' . esc_html__('This shortcode available in Pro versions,  only!' ,'booking') . '</strong> ';
		}

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingsearchresults', $attr );                                     // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );                                                                  //FixIn: 9.7.3.6.1

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();                                        // Init booking resources cache

	    $search_results_to_show = '';
	    if ( function_exists( 'wpbc_search_avy__show_search_results' ) ) {

		    ob_start();

		    wpbc_search_avy__show_search_results( $attr );                                                              // FixIn: 10.0.0.37.

		    $search_results_to_show .= ob_get_clean();
	    }

        return $search_results_to_show;
    }

    // Select Booking form using the selectbox
    function bookingselect_shortcode($attr) {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return '<strong>' . esc_html__('This shortcode available in Pro versions,  only!' ,'booking') . '</strong> ';
		}

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingselect', $attr );      // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );          //FixIn: 9.7.3.6.1

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache

        $search_form = apply_bk_filter('wpdev_get_booking_select_form','', $attr );

        return $search_form ;
    }

    // Select Booking form using the selectbox
    function bookingresource_shortcode($attr) {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return '<strong>' . esc_html__('This shortcode available in Pro versions,  only!' ,'booking') . '</strong> ';
		}

	    if ( wpbc_is_on_edit_page() ) {
		    return wpbc_get_preview_for_shortcode( 'bookingresource', $attr );      // FixIn: 9.9.0.39.
	    }

		$attr = wpbc_escape_shortcode_params( $attr );          //FixIn: 9.7.3.6.1

        //if ( function_exists( 'wpbc_br_cache' ) ) $br_cache = wpbc_br_cache();  // Init booking resources cache

        $search_form = apply_bk_filter('wpbc_booking_resource_info','', $attr );

        return $search_form ;
    }

    // </editor-fold>
}