<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Content of Booking Listing page
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



/**
	 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_CalendarOverview extends WPBC_Page_Structure {
        
    private $timeline;

    public function in_page() {
        return 'wpbc';
    }

    public function tabs() {
        
		$is_full_screen = WPBC_User_Custom_Data_Saver::get_user_data_value( wpbc_get_current_user_id(), 'is_full_screen' );
		$is_full_screen = ( 'On' === $is_full_screen );

        $tabs = array();
        $tabs[ 'vm_calendar' ] = array(
			'is_show_top_path'                   => false,                               // true | false.  By default value is: false.
			'is_show_top_navigation'             => true,                                // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => ( $is_full_screen ) ? 'compact' : 'min',   // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => false,                               // Header - Title.  If false, than hidden.
			'page_description'                   => false,                               // Header - Title Description.  If false, than hidden.
                              'title' => __('Timeline View','booking')            // Title of TAB
                            , 'hint' => __('Timeline View', 'booking')                      // Hint
                            , 'link' => ''                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            , 'icon' => ''                                      // Icon - link to the real PNG img
                            , 'font_icon' => 'wpbc_icn_rotate_90 wpbc_icn_align_vertical_bottom'             			// CSS definition  of forn Icon		// FixIn: 9.5.5.3.
                                                        , 'default' => false                                 // Is this tab activated by default or not: true || false.
                            , 'disabled' => false                               // Is this tab disbaled: true || false. 
                            , 'hided'   => true                                 // Is this tab hided: true || false.
                            , 'subtabs' => array()
            
        );
//        $tabs[ 'add-booking' ] = array(
//			'is_show_top_path'                   => false,                                 // true | false.  By default value is: false.
//			'left_navigation__default_view_mode' => 'min',                             // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
//			'page_title'                         => __( 'Add New Booking', 'booking' ),        // Header - Title.  If false, than hidden.
//			'page_description'                   => __( 'Manually add new bookings from the Admin Panel.', 'booking' ), // Header - Title Description.  If false, than hidden.
//			'title'                              => __( 'Add booking', 'booking' ),        // Title of TAB.
//			'hint'                               => __( 'Add booking', 'booking' ),        // Hint.
// 			'position'                           => '',                                    // Can be: 'left'  |  'right'  |  ''.
//			'css_classes'                        => '',                                    // this is CSS class(es).
//			'icon'                               => '',                                    // Icon - link to the real PNG img.
//			'font_icon'                          => 'wpbc-bi-plus',               // CSS definition  of forn Icon.
// 			'default'                            => false,                                 // Is this tab disbaled: true || false.
// 			'disabled'                           => false,                                 // Is this tab disbaled: true || false.
// 			'hided'                              => !false,                                  // Is this tab hided: true || false.
// 			'subtabs'                            => array()
//                            , 'link' =>  wpbc_get_new_booking_url__base(  )                                        // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
//							//, 'font_icon_right' => 'wpbc-bi-question-circle'
// 							, 'folder_style'     => 'order:101;',
//        );
//

        // $subtabs = array();                
        // $tabs[ 'bookings' ][ 'subtabs' ] = $subtabs;
        
        return $tabs;        
    }


    public function content() {                
        
        wpbc_check_request_paramters();                                         //Cleanup REQUEST parameters        // FixIn: 6.2.1.4.
        
        do_action( 'wpbc_hook_booking_page_header', 'timeline' );               // Define Notices Section and show some static messages, if needed.
                                        
        if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) return false;  // Check if MU user activated,  otherwise show Warning message.

        if ( ! wpbc_set_default_resource_to__get() ) return false;              // Define default booking resources for $_ GET  and  check if booking resource belong to user.
        
        ?><span class="wpdevelop"><?php                                         // BS UI CSS Class
        
        wpbc_js_for_bookings_page();                                            // JavaScript functions
        

        make_bk_action( 'wpbc_check_request_param__wh_booking_type' );          // Setting $_REQUEST['wh_booking_type'] - remove empty and duplicates ID of booking resources in this list        
        
        make_bk_action( 'check_for_resources_of_notsuperadmin_in_booking_listing' );    // If "Regular User",  then filter resources in $_REQUEST['wh_booking_type'] to show only resources of this user
        
        wpbc_set_request_params_for_timeline();                                 // Set initial $_REQUEST['view_days_num'] depend from selected booking resources

		//   T o o l b a r s   /////////////////////////////////////////////////
		wpbc_ui__timeline__resource_selection();


            // Show    T i m e L i n e   ///////////////////////////////////////

			// FixIn: 9.9.0.18.
			$server_zone = date_default_timezone_get();                                                                         // If in 'Theme' or 'other plugin' set  default timezone other than UTC. Save it.
			if ( 'UTC' !== $server_zone ) {                                                                                     // Needed for WP date functions  - set timezone to  UTC
				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
				@date_default_timezone_set( 'UTC' );
			}

			// FixIn: 8.6.1.13.
			$this->timeline = new WPBC_TimelineFlex();

			$this->timeline->admin_init();                                      // Define all REQUEST parameters and get bookings

			$this->timeline->show_timeline();

			// FixIn: 9.9.0.18.
			if ( 'UTC' !== $server_zone ) {                                                                                     // Back  to  previos state,  if it was changed.
				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
				@date_default_timezone_set( $server_zone );
			}

            ////////////////////////////////////////////////////////////////////

        	wpbc_welcome_panel();                                                   // Welcome Panel (links)

            wpbc_show_booking_footer();           
        
        ?></span><!-- wpdevelop class --><?php 
    }

}
add_action('wpbc_menu_created', array( new WPBC_Page_CalendarOverview() , '__construct') );    // Executed after creation of Menu
