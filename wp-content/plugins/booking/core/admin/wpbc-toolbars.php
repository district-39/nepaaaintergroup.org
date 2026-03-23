<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Toolbar. Data for UI Elements at Booking Calendar admin pages
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2015-11-16
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


// ---------------------------------------------------------------------------------------------------------------------
// == T o o l b a r s ==
// ---------------------------------------------------------------------------------------------------------------------


/** T o o l b a r   C o n t a i n e r   f o r   Add New Booking */
function wpbc_add_new_booking_toolbar() {

    wpbc_clear_div();

    //  Toolbar ////////////////////////////////////////////////////////////////

    ?><div id="toolbar_booking_listing" style="position:relative;"><?php

        wpbc_bs_toolbar_tabs_html_container_start();

            // <editor-fold     defaultstate="collapsed"                        desc=" T O P    T A B s "  >

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
            if ( ! isset( $_REQUEST['toolbar'] ) )  $_REQUEST['toolbar'] = 'filter';
            $selected_tab = sanitize_text_field( wp_unslash( $_REQUEST['toolbar'] ) );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */

            wpbc_bs_display_tab(   array(
                                                'title'         => __('Options', 'booking')
                                                // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#filter_toolbar_container').show();"
                                                                    . "jQuery('#toolbar_booking_listing .nav-tab').removeClass('nav-tab-active');"						// FixIn: 9.8.15.2.
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'wpbc_icn_adjust'
                                                , 'default'     => ( $selected_tab == 'filter' ) ? true : false

                                ) );

            wpbc_bs_display_tab(   array(
                                                'title'         => __('Calendar View', 'booking')
                                                // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                , 'onclick'     =>  "jQuery('.visibility_container').hide();"
                                                                    . "jQuery('#calendar_size_toolbar_container').show();"
                                                                    . "jQuery('#toolbar_booking_listing .nav-tab').removeClass('nav-tab-active');"						// FixIn: 9.8.15.2.
                                                                    . "jQuery(this).addClass('nav-tab-active');"
                                                                    . "jQuery('.nav-tab i.icon-white').removeClass('icon-white');"
                                                                    . "jQuery('.nav-tab-active i').addClass('icon-white');"
                                                , 'font_icon'   => 'wpbc-bi-calendar2'
                                                , 'default'     => ( $selected_tab == 'calendar' ) ? true : false

                                ) );


            wpbc_bs_dropdown_menu_help();

            // </editor-fold>

        wpbc_bs_toolbar_tabs_html_container_end();

        ////////////////////////////////////////////////////////////////////////

        wpbc_bs_toolbar_sub_html_container_start();

        //  T o o l b a r
        ?><div id="filter_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'filter' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ( function_exists( 'wpbc_toolbar_btn__resource_selection' ) ) && ( empty( $_GET['booking_hash'] ) ) ) {
				// Do not show resource seleciton  if editing booking.	// FixIn: 7.1.2.10.

				wpbc_toolbar_btn__resource_selection();
			}

            if (  function_exists( 'wpbc_toolbar_btn__form_selection' ) )
                wpbc_toolbar_btn__form_selection();

            ////////////////////////////////////////////////////////////////////
            ?><div class="clear-for-mobile"></div><?php

            ?><div class="control-group wpbc-no-padding" style="float:right;margin-right: 0;margin-left: 15px;"><?php

				wpbc_toolbar_btn__add_past_booking();

				if ( function_exists( 'wpbc_toolbar_btn__auto_fill' ) ) {
					wpbc_toolbar_btn__auto_fill();
				}

				wpbc_toolbar_btn__add_new_booking();

            ?></div><?php
            ////////////////////////////////////////////////////////////////////

        ?></div><?php
        ?><div id="calendar_size_toolbar_container" class="visibility_container clearfix-height" style="display:<?php echo ( $selected_tab == 'calendar_size' ) ? 'block' : 'none'  ?>;margin-top:-5px;"><?php
			/*
            ?><span class="advanced_booking_filter" style="display:none;"><div class="clear" style="width:100%;border-bottom:1px solid #ccc;height:10px;"></div><?php
			*/
                // Get possible saved previous "Custom User Calendar data"
                $user_calendar_options = get_user_option( 'booking_custom_' . 'add_booking_calendar_options', wpbc_get_current_user_id() );

                if ( $user_calendar_options === false ) {                       // Default, if no saved previously.
                    $user_calendar_options = array();
                    $user_calendar_options['calendar_months_count'] = 1;
                    $user_calendar_options['calendar_months_num_in_1_row'] = 0 ;
                    $user_calendar_options['calendar_width'] = '';
                    $user_calendar_options['calendar_widthunits'] = 'px';
                    $user_calendar_options['calendar_cell_height'] = '';
                    $user_calendar_options['calendar_cell_heightunits'] = 'px';
                } else {
                    $user_calendar_options = maybe_unserialize( $user_calendar_options );
                }

                wpbc_toolbar_btn__calendar_months_number_selection( $user_calendar_options );

                wpbc_toolbar_btn__calendar_months_num_in_1_row_selection( $user_calendar_options );

                wpbc_toolbar_btn__calendar_width( $user_calendar_options );

                wpbc_toolbar_btn__calendar_cell_height( $user_calendar_options );

                wpbc_toolbar_btn__calendar_options_save();

				wpbc_toolbar_btn__calendar_options_reset();
/*
            ?><div class="clear"></div></span><?php


            wpbc_clear_div();

            wpbc_toolbar_expand_collapse_btn( 'advanced_booking_filter' );
*/
        ?></div><?php


        wpbc_bs_toolbar_sub_html_container_end();

        wpbc_toolbar_is_send_emails_btn();

    ?></div><?php

    wpbc_clear_div();

}


// ---------------------------------------------------------------------------------------------------------------------
//   HTML elements for Toolbar
// ---------------------------------------------------------------------------------------------------------------------

/**
	 * Expand or Collapse Advanced Filter set
 *
 * @param string $css_class_of_expand_element - CSS Class of element section  to  show or hide
 */
function wpbc_toolbar_expand_collapse_btn( $css_class_of_expand_element ) {

      ?><span id="show_link_advanced_booking_filter" class="tab-bottom tooltip_right advanced_booking_filter"
            title="<?php esc_attr_e('Expand Advanced Toolbar' ,'booking'); ?>"
            ><a href="javascript:void(0)"
                onclick="javascript:jQuery('.<?php echo esc_js( $css_class_of_expand_element ); ?>').show();
                                    jQuery('#show_link_advanced_booking_filter').hide();
                                    jQuery('#hide_link_advanced_booking_filter').show();"><i
                    class="wpbc_icn_expand_more"></i></a></span>
        <span id="hide_link_advanced_booking_filter" class="tab-bottom tooltip_right advanced_booking_filter" style="display:none;"
            title="<?php esc_attr_e('Collapse Advanced Toolbar' ,'booking'); ?>"
            ><a href="javascript:void(0)"
                onclick="javascript:jQuery('.<?php echo esc_js( $css_class_of_expand_element ); ?>').hide();
                                    jQuery('#hide_link_advanced_booking_filter').hide();
                                    jQuery('#show_link_advanced_booking_filter').show();"><i
                    class="wpbc_icn_expand_less"></i></a></span><?php

}


/**
 * Checkbox - sending emails or not
 *
 * @param $style  Styles of this element group
 *
 * @return void
 */
function wpbc_toolbar_is_send_emails_btn( $style = 'position:absolute;right:0px;margin-top:10px;' ) {

	?><div class="btn-group" style="<?php echo esc_attr( $style ); ?>"><?php

	$el_id = 'is_send_email_for_pending';

	$el_value = ( get_bk_option( 'booking_send_emails_off_addbooking' ) !== 'On' ) ? 'On' : 'Off';

	$params_checkbox = array(
							  'id'       => $el_id 		// HTML ID  of element
							, 'name'     => $el_id
							, 'label'    => array( 'title' => __( 'Emails sending', 'booking' ) , 'position' => 'right' )           // FixIn: 9.6.1.5.
							, 'style'    => '' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							, 'disabled' => false
							, 'attr'     => array() 							// Any  additional attributes, if this radio | checkbox element
							, 'legend'   => ''									// aria-label parameter
							, 'value'    => $el_value 							// Some Value from optins array that selected by default
							, 'selected' => ( ( 'On' == $el_value ) ? true : false )		// Selected or not
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
							//, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( {'ui_usr__send_emails': (jQuery( this ).is(':checked') ? 'send' : 'not_send') } );"					// JavaScript code
							, 'hint' 	=> array( 'title' => __('Send email notification to customer about this operation' ,'booking') , 'position' => 'top' )
						);

	wpbc_flex_toggle( $params_checkbox );

	?></div><?php
}



// ---------------------------------------------------------------------------------------------------------------------
//   U I    E l e m e n t s
// ---------------------------------------------------------------------------------------------------------------------

/** Help   -   Drop Down Menu  -  T a b  */
function wpbc_bs_dropdown_menu_help() {

    wpbc_bs_dropdown_menu( array(
                                        'title' => __( 'Help', 'booking' )
                                      , 'font_icon' => 'wpbc_icn_support'												//FixIn: 9.0.1.4	'glyphicon glyphicon-question-sign'
                                      , 'position' => 'right'
                                      , 'items' => array(
                                               array( 'type' => 'link', 'title' => __('FAQ', 'booking'), 'url' => 'https://wpbookingcalendar.com/faq/' )
                                             , array( 'type' => 'link', 'title' => __('Support Forum', 'booking'), 'url' => 'https://wpbookingcalendar.com/support/' )
											 , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => __('Contact Support', 'booking'), 'url'  => 'mailto:support@wpbookingcalendar.com'
																												   , 'attr' => array( 'style' => 'font-weight: 600;' ) )
                                             , array( 'type' => 'divider' )
                                             , array( 'type' => 'link', 'title' => "What's New"/*__('Get Started')*/, 'url' => esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about' ), 'index.php' ) ) ) )
                                             , array( 'type' => 'link', 'title' => __('About', 'booking')
																		// , 'url' => wpbc_up_link()
																		, 'url' =>  'https://wpbookingcalendar.com/' //esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about-premium' ), 'index.php' ) ) )
                                                                        , 'attr' => array(
                                                                            //  'target' => '_blank'
                                                                            // 'style' => 'font-weight: 600;'
                                                                        )
                                                    )
                                        )
                        ) );
}


// ---------------------------------------------------------------------------------------------------------------------
// Toolbar   Actions    B u t t o n s   -   T i m e l i n e   //////////////////
// ---------------------------------------------------------------------------------------------------------------------




/** Navigation Timeline   -   B u t t o n s */
function wpbc_toolbar_btn__timeline_navigation() {

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    if  ((isset($_REQUEST['wh_booking_type'])) && ( strpos($_REQUEST['wh_booking_type'], ',') !== false ) )
            $is_show_resources_matrix = true;
    else    $is_show_resources_matrix = false;


    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_REQUEST['view_days_num'] ) )
         $view_days_num = intval( $_REQUEST['view_days_num'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    else $view_days_num = get_bk_option( 'booking_view_days_num');


    $bk_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false ), array('scroll_month', 'scroll_day') );
//debuge($_REQUEST, $bk_admin_url);

    // Get Data For buttons
    if (! $is_show_resources_matrix) {

        switch ($view_days_num) {
            case '90':
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                        '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+7 ),
                                        '&scroll_day='.intval($scroll_day+4*7) );
                $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                         __('Previous week' ,'booking'),
                                         __('Current week' ,'booking'),
                                         __('Next week' ,'booking'),
                                         __('Next 4 weeks' ,'booking') );
                break;
            case '30':
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                else $scroll_day = 0;

	            // FixIn: 8.9.4.3.
	            // Here we need to define number of days to scroll depends from selected number of days to show.
	            $days_num_to_scroll = intval( get_bk_option( 'booking_calendar_overview__day_mode__days_number_show' ) );;
	            if ( empty( $days_num_to_scroll ) ) {
		            $days_num_to_scroll = 7;
	            }

                $scroll_params = array( '&scroll_day='.intval( $scroll_day - $days_num_to_scroll * 2 ),
                                        '&scroll_day='.intval( $scroll_day - $days_num_to_scroll ),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval( $scroll_day + $days_num_to_scroll ),
                                        '&scroll_day='.intval( $scroll_day + $days_num_to_scroll *2 ) );
                $scroll_titles = array(  __( 'Previous', 'booking' ) . ' ' . ( 2 * $days_num_to_scroll ) . ' ' . __( 'days', 'booking' ),
	                					 __( 'Previous', 'booking' ) . ' ' . $days_num_to_scroll . ' ' . __( 'days', 'booking' ),
                                         __('Current week' ,'booking'),
                                         __( 'Next', 'booking' ) . ' ' . $days_num_to_scroll . ' ' . __( 'days', 'booking' ),
                                         __( 'Next', 'booking' ) . ' ' . ( 2 * $days_num_to_scroll ) . ' ' . __( 'days', 'booking' ) );
                break;
            default:  // 365
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (! isset($_REQUEST['scroll_month'])) $_REQUEST['scroll_month'] = 0;
                $scroll_month = intval( $_REQUEST['scroll_month'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                        '&scroll_month='.intval($scroll_month-1),
                                        '&scroll_month=0',
                                        '&scroll_month='.intval($scroll_month+1 ),
                                        '&scroll_month='.intval($scroll_month+3) );
                $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                         __('Previous month' ,'booking'),
                                         __('Current month' ,'booking'),
                                         __('Next month' ,'booking'),
                                         __('Next 3 months' ,'booking') );
                break;
        }
    } else { // Matrix

        switch ($view_days_num) {
            case '1': //Day
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day='.intval($scroll_day-1),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+1 ),
                                        '&scroll_day='.intval($scroll_day+7) );
                $scroll_titles = array(  __('Previous 7 days' ,'booking'),
                                         __('Previous day' ,'booking'),
                                         __('Current day' ,'booking'),
                                         __('Next day' ,'booking'),
                                         __('Next 7 days' ,'booking') );
                break;

            case '7': //Week
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (isset($_REQUEST['scroll_day'])) $scroll_day = intval( $_REQUEST['scroll_day'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                else $scroll_day = 0;
                $scroll_params = array( '&scroll_day='.intval($scroll_day-4*7),
                                        '&scroll_day='.intval($scroll_day-7),
                                        '&scroll_day=0',
                                        '&scroll_day='.intval($scroll_day+7 ),
                                        '&scroll_day='.intval($scroll_day+4*7) );
                $scroll_titles = array(  __('Previous 4 weeks' ,'booking'),
                                         __('Previous week' ,'booking'),
                                         __('Current week' ,'booking'),
                                         __('Next week' ,'booking'),
                                         __('Next 4 weeks' ,'booking') );
                break;

            case '30':
            case '60':
            case '90': //3 months
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (! isset($_REQUEST['scroll_month'])) $_REQUEST['scroll_month'] = 0;
                $scroll_month = intval( $_REQUEST['scroll_month'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                        '&scroll_month='.intval($scroll_month-1),
                                        '&scroll_month=0',
                                        '&scroll_month='.intval($scroll_month+1 ),
                                        '&scroll_month='.intval($scroll_month+3) );
                $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                         __('Previous month' ,'booking'),
                                         __('Current month' ,'booking'),
                                         __('Next month' ,'booking'),
                                         __('Next 3 months' ,'booking') );
                break;

            default:  // 30, 60, 90...
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                if (! isset($_REQUEST['scroll_month'])) $_REQUEST['scroll_month'] = 0;
                $scroll_month = intval( $_REQUEST['scroll_month'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
                $scroll_params = array( '&scroll_month='.intval($scroll_month-3),
                                        '&scroll_month='.intval($scroll_month-1),
                                        '&scroll_month=0',
                                        '&scroll_month='.intval($scroll_month+1 ),
                                        '&scroll_month='.intval($scroll_month+3) );
                $scroll_titles = array(  __('Previous 3 months' ,'booking'),
                                         __('Previous month' ,'booking'),
                                         __('Current month' ,'booking'),
                                         __('Next month' ,'booking'),
                                         __('Next 3 months' ,'booking') );
                break;
        }
    }


    $params = array(
                      'label_for' => 'calendar_overview_navigation'                              // "For" parameter  of button group element
                    , 'label' => '' //__('Calendar Navigation', 'booking')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[0] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[0]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary wpbc_button_no_background'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'wpbc_icn_keyboard_double_arrow_left'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[1] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[1]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary wpbc_button_no_background'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'wpbc_icn_keyboard_arrow_left'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'dropdown'
                                            , 'id' => 'timeline_navigation_date'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => __('Custom' ,'booking') , 'position' => 'top' ) // Hint
                                            , 'class' => 'button-secondary wpbc_button_no_background'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'wpbc_icn_gps_fixed'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                            , 'options' => array(
                                                      $scroll_titles[2] => "window.location.href='"
                                                                            . wpbc_get_params_in_url( wpbc_get_bookings_url( false )
                                                                                                    , array('scroll_month', 'scroll_day', 'scroll_start_date') )
                                                                            . $scroll_params[2] . "'"
                                                    , 'divider1' => 'divider'
                                                    , 'custom' => array( array(  'type' => 'group', 'class' => 'input-group text-group')
                                                                        , array(
                                                                                'type'          => 'text'
                                                                                , 'id'          => 'calendar_overview_navigation_currentdate'
                                                                                , 'name'        => 'calendar_overview_navigation_currentdate'
                                                                                , 'label'       => __('Start Date' ,'booking') . ':'
                                                                                , 'disabled'    => false
                                                                                , 'class'       => 'wpdevbk-filters-section-calendar'
                                                                                , 'style'       => ''
                                                                                , 'placeholder' => gmdate('Y-m-d')
                                                                                , 'attr'        => array()
                                                                                , 'value' => ''
                                                                              )
                                                                        )
                                                    , 'divider2' => 'divider'
                                                    , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group0', 'style'=>'display: flex;flex-flow: row nowrap;align-items: center;justify-content: flex-end;' ),
                                                                        array(
                                                                                  'type' => 'button'
                                                                                , 'title' => __('Apply' ,'booking') // Title of the button
                                                                                , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                , 'action' => "jQuery('#calendar_overview_navigation_container').hide();
                                                                                               window.location.href='"
                                                                                               . wpbc_get_params_in_url( wpbc_get_bookings_url( false )
                                                                                                                        , array('scroll_month', 'scroll_day', 'scroll_start_date') )
                                                                                               . "&scroll_start_date=' + jQuery('#calendar_overview_navigation_currentdate').val();"

                                                                                , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                                , 'icon' => ''
                                                                                , 'font_icon' => ''
                                                                                , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                , 'style' => ''                     // Any CSS class here
                                                                                , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                , 'attr' => array()

                                                                              )
                                                                        , array(
                                                                                  'type' => 'button'
                                                                                , 'title' => __('Close' ,'booking')                     // Title of the button
                                                                                , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                                , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                                , 'icon' => ''
                                                                                , 'font_icon' => ''
                                                                                , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                , 'style' => 'margin:0 10px;'                     // Any CSS class here
                                                                                , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                , 'attr' => array()
                                                                              )
                                                                        )
                                               )
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[3] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[3]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary wpbc_button_no_background'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'wpbc_icn_keyboard_arrow_right'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => ''                                              // Title of the button
                                            , 'hint' => array( 'title' => $scroll_titles[4] , 'position' => 'top' ) // Hint
                                            , 'link' => $bk_admin_url .$scroll_params[4]                // Direct link or skip  it
                                            , 'action' => ""                                            // Some JavaScript to execure, for example run  the function
                                            , 'class' => 'button-secondary wpbc_button_no_background'                             // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => 'wpbc_icn_keyboard_double_arrow_right'
                                            , 'icon_position' => 'left'                                // Position  of icon relative to Text: left | right
                                            , 'style' => ''                                             // Any CSS class here
                                            , 'mobile_show_text' => false                               // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                    )
    );

    wpbc_bs_button_group( $params );
}



// ---------------------------------------------------------------------------------------------------------------------
// Toolbar   Options    B u t t o n s   -   Add New Booking   //////////////////
// ---------------------------------------------------------------------------------------------------------------------

/** Genereate URL based on GET parameters */
function wpbc_get_new_booking_url__base( $skip_parameters = array() ) {

    $link_base = wpbc_get_new_booking_url( true, false );

    $link_params = array();
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( ( isset( $_GET['booking_type'] ) ) && ( $_GET['booking_type'] > 0 ) )      $link_params['booking_type'] = sanitize_text_field( wp_unslash( $_GET['booking_type'] ) );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_GET['booking_hash'] ) )                   $link_params['booking_hash'] = sanitize_text_field( wp_unslash( $_GET['booking_hash'] ) );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_GET['parent_res'] ) )                     $link_params['parent_res'] = sanitize_text_field( wp_unslash( $_GET['parent_res'] ) );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_GET['booking_form'] ) )                   $link_params['booking_form'] = sanitize_text_field( wp_unslash( $_GET['booking_form'] ) );  /* phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing */ /* FixIn: sanitize_unslash */
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_GET['calendar_months_count'] ) )          $link_params['calendar_months_count'] = intval( $_GET['calendar_months_count'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_GET['calendar_months_num_in_1_row'] ) )   $link_params['calendar_months_num_in_1_row'] = intval( $_GET['calendar_months_num_in_1_row'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing


    foreach ( $link_params as $key => $value ) {

        if ( ! in_array( $key, $skip_parameters) ) {
            $link_base .= '&' . $key . '=' . $value;
        }
    }

    return $link_base;
}

/** Selection Number of visible months */
function wpbc_toolbar_btn__calendar_months_number_selection( $user_calendar_options = array() ) {

    $text_label = __('Visible months' ,'booking') .':' ;

    $form_options = array(  1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12 );

    $parameter_name = 'calendar_months_count';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval ( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = 1;

    $link_base = wpbc_get_new_booking_url__base( array( $parameter_name ) ) . '&' . $parameter_name . '=' ;

    $on_change = '';    //'location.href=\'' . $link_base . '\' + this.value;';


    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'options' => $form_options                // Associated array  of titles and values
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                        , 'onchange' => $on_change
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding" style="width:auto;"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


/** Selection Number of calendar months in one row */
function wpbc_toolbar_btn__calendar_months_num_in_1_row_selection( $user_calendar_options = array() ) {

    $text_label = __('Number of months in one row' ,'booking') . ':';
    $form_options = array( 0 => __('All', 'booking'), 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 11 => 11, 12 => 12 );

    $parameter_name = 'calendar_months_num_in_1_row';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval ( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = 0;

    $link_base = wpbc_get_new_booking_url__base( array( $parameter_name ) ) . '&' . $parameter_name . '=' ;

    $on_change = ''; // 'location.href=\'' . $link_base . '\' + this.value;';


    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'options' => $form_options                // Associated array  of titles and values
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                        , 'onchange' => $on_change
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


function wpbc_toolbar_btn__calendar_width( $user_calendar_options = array() ){

    $text_label     = __('Maximum width of calendar' ,'booking') . ':';
    $parameter_name = 'calendar_width';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = '';

    if ( isset( $user_calendar_options[$parameter_name . 'units'] ) )    $selected_value_units = esc_attr( $user_calendar_options[ $parameter_name . 'units' ]  );
    else                                                                 $selected_value_units = '';

    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'text'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'placeholder' => '100%'
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name . 'units'                // HTML ID  of element
                                        , 'options' => array( 'px' => 'px', 'percent' => '%' )  // Associated array  of titles and values
                                        , 'value' =>   $selected_value_units              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}

function wpbc_toolbar_btn__calendar_cell_height( $user_calendar_options = array() ){

    $text_label     = __('Calendar cell height' ,'booking') . ':';
    $parameter_name = 'calendar_cell_height';

    if ( isset( $user_calendar_options[$parameter_name] ) )    $selected_value = intval( $user_calendar_options[ $parameter_name ]  );
    else                                                       $selected_value = '';

    if ( isset( $user_calendar_options[$parameter_name . 'units'] ) )    $selected_value_units = esc_attr( $user_calendar_options[ $parameter_name . 'units' ]  );
    else                                                                 $selected_value_units = '';

    $params = array(
                      'label_for' => $parameter_name                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text' => $text_label
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'            // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'text'
                                        , 'id' =>      $parameter_name              // HTML ID  of element
                                        , 'value' =>   $selected_value              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'placeholder' => '48px'
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id' =>      $parameter_name . 'units'                // HTML ID  of element
                                        , 'options' => array( 'px' => 'px', 'percent' => '%' )  // Associated array  of titles and values
                                        , 'value' =>   $selected_value_units              // Some Value from optins array that selected by default
                                        , 'style' => 'width: 5em;'                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    )

                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


/** Add New Booking   Button*/
function wpbc_toolbar_btn__calendar_options_save() {

    ?><div class="control-group wpbc-no-padding"><?php
    ?><a	id="toolbar_btn__calendar_options_save"
             class="button button-primary "
             href="javascript:void(0)"
             onclick="javascript:var data_params = {};
			data_params.calendar_months_count = jQuery('#calendar_months_count').val();
			data_params.calendar_months_num_in_1_row = jQuery('#calendar_months_num_in_1_row').val();
			data_params.calendar_width = jQuery('#calendar_width').val();
			data_params.calendar_widthunits = jQuery('#calendar_widthunits').val();
			data_params.calendar_cell_height = jQuery('#calendar_cell_height').val();
			data_params.calendar_cell_heightunits = jQuery('#calendar_cell_heightunits').val();
			var ajax_data_params = jQuery.param( data_params );
                        wpbc_save_custom_user_data(<?php echo esc_js( wpbc_get_current_user_id() ); ?>
                                                , '<?php echo 'add_booking_calendar_options'; ?>'
                                                , ajax_data_params
                                                , 1
                                                );"
             ><?php esc_html_e('Save' , 'booking' ); ?></a><?php
    ?></div><?php
}


/** Add New Booking   Button*/
function wpbc_toolbar_btn__calendar_options_reset() {

    ?><div class="control-group wpbc-no-padding"><?php
    ?><a
             class="button button-secondary "
             href="javascript:void(0)"
             onclick="javascript:jQuery('#calendar_months_count').val('1');
								 jQuery('#calendar_months_num_in_1_row').val('0');
								 jQuery('#calendar_width').val('0');
								 jQuery('#calendar_widthunits').val('px');
								 jQuery('#calendar_cell_height').val('0');
								 jQuery('#calendar_cell_heightunits').val('px');
								 jQuery( '#toolbar_btn__calendar_options_save').trigger('click');
								"
             ><?php esc_html_e('Reset' , 'booking' ); ?></a><?php
    ?></div><?php
}

/**
 * Button for ability to  add bookings in the past.
 *
 * @return void
 */
function wpbc_toolbar_btn__add_past_booking() {

	if ( isset( $_GET['allow_past'] ) ) {                                                                               // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		return;
	}

	$link = wpbc_get_new_booking_url();

	$link .= ( ! empty( $_REQUEST['booking_type'] ) ) ? '&booking_type=' . intval( $_REQUEST['booking_type'] ) : '';                               // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	$link .= ( ! empty( $_REQUEST['booking_form'] ) ) ? '&booking_form=' . sanitize_text_field( wp_unslash( $_REQUEST['booking_form'] ) ) : '';    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	$link .= ( ! empty( $_REQUEST['booking_hash'] ) ) ? '&booking_form=' . sanitize_text_field( wp_unslash( $_REQUEST['booking_hash'] ) ) : '';    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	$link .= ( ! empty( $_REQUEST['is_show_payment_form'] ) ) ? '&is_show_payment_form=Off' : '';                                                  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	$link .= ( ! empty( $_REQUEST['parent_res'] ) ) ? '&parent_res=' . intval( $_REQUEST['parent_res'] ) : '';                                     // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

	$el_id    = 'is_allow_bookings_in_past';
	$el_value = 'Off';

	$params_checkbox = array(
							  'id'       => $el_id 		// HTML ID  of element
							, 'name'     => $el_id
							, 'label'    => array( 'title' => __( 'Allow booking in the past', 'booking' ) , 'position' => 'right' )           // FixIn: 9.6.1.5.
							, 'style'    => '' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							, 'disabled' => false
							, 'attr'     => array() 							// Any  additional attributes, if this radio | checkbox element
							, 'legend'   => ''									// aria-label parameter
							, 'value'    => $el_value 							// Some Value from optins array that selected by default
							, 'selected' => ( ( 'On' == $el_value ) ? true : false )		// Selected or not
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
							, 'onchange' => "window.location.href='". $link . "&allow_past=1';"					// JavaScript code
							, 'hint' 	=> array( 'title' => __('Allow booking in the past' ,'booking') , 'position' => 'top' )
						);

	?><div class="btn-group" style="display: inline-flex;min-height: 31px;flex-flow: row nowrap;align-items: center;"><?php

	wpbc_flex_toggle( $params_checkbox );

	?></div><?php
}


/** Add New Booking   Button*/
function wpbc_toolbar_btn__add_new_booking() {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_GET['booking_type'] ) ) {
		$bk_type = intval( $_GET['booking_type'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	} else {
		$bk_type = 1;
	}

	?><a
	class="button-primary button wpbc_submit_button"
	href="javascript:void(0)"
	onclick="wpbc_booking_form_submit(
		document.getElementById('booking_form<?php echo intval( $bk_type ); ?>' )
		, <?php echo intval( $bk_type ); ?>
		, '<?php echo esc_js( wpbc_get_maybe_reloaded_booking_locale() ); ?>'
		);"
	><?php esc_html_e( 'Add booking', 'booking' ); ?></a><?php
}


/** Checkbox - sending emails or not - duplicated button, usually at the bottom of page*/
function wpbc_toolbar_is_send_emails_btn_duplicated() {

	?><div class="btn-group" style="margin-top:10px;"><?php

	$el_id = 'is_send_email_for_new_booking';

	$el_value = ( get_bk_option( 'booking_send_emails_off_addbooking' ) !== 'On' ) ? 'On' : 'Off';

	$params_checkbox = array(
							  'id'       => $el_id 		// HTML ID  of element
							, 'name'     => $el_id
							, 'label'    => array( 'title' => __( 'Emails sending', 'booking' ) , 'position' => 'right' )           // FixIn: 9.6.1.5.
							, 'style'    => '' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							, 'disabled' => false
							, 'attr'     => array() 							// Any  additional attributes, if this radio | checkbox element
							, 'legend'   => ''									// aria-label parameter
							, 'value'    => $el_value 							// Some Value from optins array that selected by default
							, 'selected' => ( ( 'On' == $el_value ) ? true : false )		// Selected or not
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
							, 'onchange' => "document.getElementById('is_send_email_for_pending').checked = this.checked;"					// JavaScript code
							, 'hint' 	=> array( 'title' => __('Send email notification to customer about this operation' ,'booking') , 'position' => 'top' )
						);

	wpbc_flex_toggle( $params_checkbox );

	?></div><?php

	?>
    <script type="text/javascript">
        jQuery( '#is_send_email_for_pending' ).on('change', function() {
			document.getElementById( 'is_send_email_for_new_booking' ).checked = jQuery( '#is_send_email_for_pending' ).is( ':checked' );
        });
    </script>
    <?php
}


/**
	 * Show Link (button) for adding booking to Google Calendar
 *
 * @param int $booking_id
 * @param array $button_attr
 * @param bool $echo
 * @return string
 */
function wpbc_btn_add_booking_to_google_calendar( $booking_data, $button_attr = array(), $echo = true ) {					// FixIn: 7.1.2.5.

//debuge($booking_data);
	if ( ! $echo ) {
            ob_start();
	}

	$defaults = array(
		  'title' => __( 'Add to Google Calendar', 'booking' )
		, 'hint' => __( 'Add to Google Calendar', 'booking' )
		, 'class' => 'button-secondary button'
		, 'is_show_icon' => true
		, 'is_only_url'  => false
	);
	$button_attr = wp_parse_args( $button_attr, $defaults );

	$params = array();
	$params['timezone'] = get_bk_option('booking_gcal_timezone');

	$booking_gcal_events_form_fields = get_bk_option( 'booking_gcal_events_form_fields');
	if ( is_serialized( $booking_gcal_events_form_fields ) )
		$booking_gcal_events_form_fields = unserialize( $booking_gcal_events_form_fields );

	/**
	 * Array
        (
            [title] => text^name
            [description] => textarea^details
            [where] => text^
        )
	 */


	// Fields
	$fields = array( 'title' => '', 'description' => '', 'where' => '' );

	foreach ( $fields as $key_name => $field_value ) {

		if ( ! empty( $booking_gcal_events_form_fields[ $key_name ] ) ) {

			$field_name = explode( '^', $booking_gcal_events_form_fields[ $key_name ] );

			$field_name = $field_name[ ( count( $field_name ) - 1 ) ];                                                  // FixIn: 8.7.7.6.

			if (   (! empty($field_name))
				&& (! empty($booking_data['form_data']))
				&& (! empty($booking_data['form_data']['_all_fields_']))
				&& (! empty($booking_data['form_data']['_all_fields_'][ $field_name ]))
				) {

					if ( 'description' === $key_name ) {                                                                // FixIn: 8.1.3.2.
						if ( isset( $booking_data['form_show'] ) ) {                                                    // FixIn: 8.7.3.14.
							// FixIn: 8.7.11.4.
							$fields[ $key_name ] = $booking_data['form_show'];
							$fields[ $key_name ] = htmlspecialchars_decode($fields[ $key_name ], ENT_QUOTES );
							$fields[ $key_name ] = urlencode($fields[ $key_name ]);
							$fields[ $key_name ] = htmlentities($fields[ $key_name ] );
							$fields[ $key_name ] = htmlspecialchars_decode ( $fields[ $key_name ], ENT_NOQUOTES );
						}
					} else {
						// FixIn: 8.7.11.4.
						$fields[ $key_name ] = $booking_data['form_data']['_all_fields_'][ $field_name ];
						$fields[ $key_name ] = htmlspecialchars_decode($fields[ $key_name ], ENT_QUOTES );
						// Convert here from  usual  symbols to URL symbols https://www.url-encode-decode.com/
//						$fields[ $key_name ] = str_replace(    array( '%','#', '+', '&' )
//							                                 , array( '%25','%23', '%2B', '%26')
//							     							 , $fields[ $key_name ]
//												);
						$fields[ $key_name ] = urlencode($fields[ $key_name ]);
						$fields[ $key_name ] = htmlentities($fields[ $key_name ] );
						$fields[ $key_name ] = htmlspecialchars_decode ( $fields[ $key_name ], ENT_NOQUOTES );
					}
			}
		}
	}
//debuge($booking_gcal_events_form_fields, $fields,$booking_data['form_data']);

	// Dates.

	$check_in_timestamp = $check_out_timestamp = '';
	if ( ! empty( $booking_data[ 'dates_short' ] ) ) {


		/* all day events, you can use 20161208/20161209 - note that the old google documentation gets it wrong.
		 * You must use the following date as the end date for a one day all day event,
		 * or +1 day to whatever you want the end date to be.
		 */

		$check_in_timestamp  = strtotime( $booking_data[ 'dates_short' ][ 0 ], current_time( 'timestamp' ) );
		if ( trim( substr( $booking_data[ 'dates_short' ][ 0 ], 11 ) ) == '00:00:00' ) {
			$check_in_timestamp = gmdate( "Ymd", $check_in_timestamp );		// All day
		} else {
			$check_in_timestamp = gmdate( "Ymd\THis", $check_in_timestamp );
			//$check_in_timestamp = gmdate( "Ymd\THis\Z", $check_in_timestamp );
		}

		$check_out_timestamp = strtotime( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], current_time( 'timestamp' ) );
		if ( trim( substr( $booking_data[ 'dates_short' ][ ( count( $booking_data[ 'dates_short' ] ) - 1 ) ], 11 ) ) == '00:00:00' ) {
			$check_out_timestamp = strtotime( '+1 day', $check_out_timestamp );
			$check_out_timestamp = gmdate( "Ymd", $check_out_timestamp );		// All day
		} else {
			$check_out_timestamp = gmdate( "Ymd\THis", $check_out_timestamp );
			//$check_out_timestamp = gmdate( "Ymd\THis\Z", $check_out_timestamp );
		}

	}

	//debuge($check_in_timestamp,$check_out_timestamp, $fields );die;
    //Convert an ISO date/time to a UNIX timestamp
    //function iso_to_ts( $iso ) {
    //    sscanf( $iso, "%u-%u-%uT%u:%u:%uZ", $year, $month, $day, $hour, $minute, $second );
    //    return mktime( intval($hour), intval($minute), intval($second), intval($month), intval($day), intval($year) );
	// 20140127T224000Z
	// gmdate("Ymd\THis\Z", time());

	/**
action:
    action=TEMPLATE
    A default required parameter.

src:
    Example: src=default%40gmail.com
    Format: src=text
    This is not covered by Google help but is an optional parameter in order to add an event to a shared calendar rather than a user's default.

text:
    Example: text=Garden%20Waste%20Collection
    Format: text=text
    This is a required parameter giving the event title.

dates:
    Example: dates=20090621T063000Z/20090621T080000Z (i.e. an event on 21 June 2009 from 7.30am to 9.0am British Summer Time (=GMT+1)).
    Format: dates=YYYYMMDDToHHMMSSZ/YYYYMMDDToHHMMSSZ
    This required parameter gives the start and end dates and times (in Greenwich Mean Time) for the event.

location:
    Example: location=Home
    Format: location=text
    The obvious location field.

trp:
    Example: trp=false
    Format: trp=true/false
    Show event as busy (true) or available (false)

sprop:
    Example: sprop=http%3A%2F%2Fwww.me.org
    Example: sprop=name:Home%20Page
    Format: sprop=website and/or sprop=name:website_name
	 */

//	$link_add2gcal  = 'http://www.google.com/calendar/event?action=TEMPLATE';
//	$link_add2gcal .= '&text=' . $fields['title'];
	// FixIn: 8.7.3.10.
	$link_add2gcal = 'https://calendar.google.com/calendar/r/eventedit?';
	$link_add2gcal .= 'text=' . $fields['title'];							// FixIn: 8.7.11.4.
	//$link_add2gcal .= '&dates=[start-custom format='Ymd\\THi00\\Z']/[end-custom format='Ymd\\THi00\\Z']';
	$link_add2gcal .= '&dates=' . $check_in_timestamp . '/' . $check_out_timestamp;
	$link_add2gcal .= '&details='  . ( ( 'On' !== get_bk_option( 'booking_g_cal_export_no_data' ) ) ? $fields['description'] : '' );		// FixIn: 10.3.0.1.
	$link_add2gcal .= '&location=' . ( ( 'On' !== get_bk_option( 'booking_g_cal_export_no_data' ) ) ? $fields['where'] : '' );
	$link_add2gcal .= '&trp=false';
	if ( ! empty( $params['timezone'] ) ) {
		$link_add2gcal .= '&ctz=' . str_replace( '%', '%25', $params['timezone'] );                   //FixIn: 8.7.3.10				//TimeZone
	}


	//$link_add2gcal .= '&sprop=';
	//$link_add2gcal .= '&sprop=name:';

	if ( $button_attr['is_only_url'] ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $link_add2gcal;
	} else {

		?><a href="<?php echo esc_url( $link_add2gcal ); ?>" target="_blank" rel="nofollow"
			class="tooltip_top <?php echo esc_attr( $button_attr['class'] ) ?>"
			title="<?php echo esc_attr( $button_attr['hint'] ); ?>"
		><?php
		if ( $button_attr['is_show_icon'] ) {
		?><i class="wpbc_icn_event"></i><?php								//FixIn: 9.0.1.4	glyphicon icon-1x glyphicon-export
		} else {
			echo esc_js( $button_attr['title'] );
		}
		?></a><?php
	}

	if ( ! $echo ) {
		return ob_get_clean();
	}

}
// ---------------------------------------------------------------------------------------------------------------------
// Toolbar   Other UI elements - General
// ---------------------------------------------------------------------------------------------------------------------

/**
	 * Selection elements in toolbar UI selectbox
 *
 * @param array $params
 *
 * Exmaple:
            wpbc_toolbar_btn__selection_element( array(
                                                            'name' => 'resources_count'
                                                          , 'title' => __('Resources count' ,'booking') . ':'
                                                          , 'selected' => 1
                                                          , 'options' => array_combine( range(1, 201) ,range(1, 201) )
                                            ) ) ;

 */
function wpbc_toolbar_btn__selection_element( $params ) {

    $defaults = array(
                          'name'        => 'random_' . wp_rand( 1000, 10000 )
                        , 'title'       => __('Total', 'booking') . ':'
                        , 'on_change'   => ''                                    //'location.href=\'' . $link_base . '\' + this.value;';    //$link_base = wpbc_get_new_booking_url__base( array( $params['name'] ) ) . '&' . $params['name'] . '=' ;
                        , 'options'     => array()
                        , 'selected'    => 0
                    );
    $params = wp_parse_args( $params, $defaults );




    for ( $i = 1; $i < 201; $i++ ) {
        $form_options[ $i ] = $i;
    }

    $params = array(
                      'label_for' => $params['name']                                // "For" parameter  of label element
                    , 'label' => ''                                                 // Label above the input group
                    , 'style' => ''                                                 // CSS Style of entire div element
                    , 'items' => array(
                                    array(
                                        'type' => 'addon'
                                        , 'element' => 'text'                       // text | radio | checkbox
                                        , 'text'  => $params['title']
                                        , 'class' => ''                             // Any CSS class here
                                        , 'style' => 'font-weight:600;'             // CSS Style of entire div element
                                    )
                                    , array(
                                          'type' => 'select'
                                        , 'id'   =>      $params['name']              // HTML ID  of element
                                        , 'name' =>      $params['name']              // HTML ID  of element
                                        , 'options' => $params['options']           // Associated array  of titles and values
                                        , 'value' =>   $params['selected']          // Some Value from optins array that selected by default
                                        , 'style' => ''                             // CSS of select element
                                        , 'class' => ''                             // CSS Class of select element
                                        , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                        , 'onchange' => $params['on_change']
                                    )
                    )
              );
    ?><div class="control-group wpbc-no-padding"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php
}


// ---------------------------------------------------------------------------------------------------------------------
// Toolbar     S e a r c h    F o r m     at    Top  Right side of Settings page
// ---------------------------------------------------------------------------------------------------------------------

// FixIn: 8.0.1.12.
/**
 * Add hidden input SEARCH KEY field into  main form, if previosly was searching by ID or Title
 * @param array $params			=>  array( 'search_get_key'  => 'wh_resource_id' )
 */
function wpbc_hidden_search_by_id_field_in_main_form( $params = array() ){

	$defaults = array(
	    				'search_get_key'  => 'wh_search_id'
				);
	$params = wp_parse_args( $params, $defaults );


	$search_form_value = '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_REQUEST[ $params[ 'search_get_key' ] ] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$wh_resource_id = wpbc_clean_digit_or_csd( $_REQUEST[ $params['search_get_key'] ] );                            // '12,0,45,9' or '10'.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$wh_resource_title = wpbc_clean_string_for_form( $_REQUEST[ $params['search_get_key'] ] );                      // Clean string.

		if ( ! empty( $wh_resource_id ) ) {
			$search_form_value = $wh_resource_id;
		} else {
			$search_form_value = $wh_resource_title;
		}
	}

	if ( '' !== $search_form_value ) {
		?><input name="<?php echo esc_attr( $params['search_get_key'] ); ?>" value="<?php echo esc_attr( $search_form_value ); ?>" type="hidden"><?php
	}
}

/**
	 * Real Search booking data  by ID | Title (at top right side of page)
 *
 * @param array $params - array of parameters
 * Exmaple:
                wpbc_toolbar_search_by_id__top_form( array(
                                                            'search_form_id' => 'wpbc_seasonfilters_search_form'
                                                          , 'search_get_key' => 'wh_search_id'
                                                          , 'is_pseudo'      => false
                                                    ) );

 */
function wpbc_toolbar_search_by_id__top_form( $params ) {

    $defaults = array(
                          'search_form_id'  => 'wpbc_seasonfilters_search_form'
                        , 'search_get_key'  => 'wh_search_id'
                        , 'is_pseudo'       => false                                    //'location.href=\'' . $link_base . '\' + this.value;';    //$link_base = wpbc_get_new_booking_url__base( array( $params['name'] ) ) . '&' . $params['name'] . '=' ;
						, 'container_style' => 'position: absolute; right: 20px; top: 0px;z-index: 0;' 					// FixIn: 10.0.0.29.
                    );
    $params = wp_parse_args( $params, $defaults );


    $exclude_params         = array();                                          //array('page_num', 'orderby', 'order');  - if using "only_these_parameters",  then this parameter does NOT require
    $only_these_parameters  = array( 'page', 'tab', 'subtab', $params[ 'search_get_key' ] );        //FixIn: 8.1.1.11	-	added , 'subtab'	- ability to  search  booking resources in sub tab  pages in settings
    $wpbc_admin_url = wpbc_get_params_in_url( wpbc_get_bookings_url( false, false ), $exclude_params, $only_these_parameters );


    $search_form_value = '';
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_REQUEST[ $params[ 'search_get_key' ] ] ) ) {
        $wh_resource_id    = wpbc_clean_digit_or_csd( $_REQUEST[ $params[ 'search_get_key' ] ] );  // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized          // '12,0,45,9' or '10'
        $wh_resource_title = wpbc_clean_string_for_form( $_REQUEST[ $params[ 'search_get_key' ] ] );  // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized       // Clean string
        if ( ! empty( $wh_resource_id ) ) {
            $search_form_value = $wh_resource_id;
        } else {
            $search_form_value = $wh_resource_title;
        }
    }


    wpbc_clear_div();

    ?>
    <span class="wpdevelop">

    <?php if ( ! $params['is_pseudo'] ) { ?>
        <div style="<?php echo esc_attr( $params['container_style'] ); ?>">
            <form action="<?php echo esc_url( $wpbc_admin_url ); ?>" method="post" id="<?php echo esc_attr( $params[ 'search_form_id' ] ); ?>"  name="<?php echo esc_attr( $params[ 'search_form_id' ] ); ?>"  >
            <?php
    } else {
      ?><div style="float:right;" id="<?php echo esc_attr( $params['search_form_id'] . '_pseudo' ); ?>"><?php
    }


if(0){
                $params_for_element = array(  'label_for' => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
                                          , 'label' => ''//__('Keyword:', 'booking')
                                          , 'items' => array(
                                                                array(   'type' => 'text'
                                                                       , 'id' => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
                                                                       , 'value' => $search_form_value
                                                                       , 'placeholder' => __('ID or Title', 'booking')
                                                                    )
                                                                , array(
                                                                    'type' => 'button'
                                                                    , 'title' => __('Go', 'booking')
                                                                    , 'class' => 'button-secondary'
                                                                    , 'font_icon' => 'wpbc_icn_search'
                                                                    , 'icon_position' => 'right'
                                                                    , 'action' => ( ( ! $params['is_pseudo'] ) ? "jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );"
                                                                                                             : "jQuery('#" . $params[ 'search_get_key' ] . "').val( jQuery('#" . $params[ 'search_get_key' ] . "_pseudo').val() ); jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );" )           //Submit real form  at the top of page.
                                                                    )
                                                        )
                                    );

                ?><div class="control-group wpbc-no-padding" ><?php
                          wpbc_bs_input_group( $params_for_element );
                ?></div><?php
} else {
		?><div id="booking_resources_toolbar_container" class="wpbc_ajx_toolbar wpbc_background_transparent"><?php

				?><div class="ui_container    ui_container_toolbar		ui_container_small    ui_container_options    ui_container_options_row_1"
					   style=" display: flex"><?php

					?><div class="ui_group"><?php

				// Search Keyword text  field
				?><div class="ui_element"><?php

					$el_id = 'booking_resource_search_keyword';

					$default_value = $search_form_value;

					$params_for_element = array(
									'type'          => 'text'
									, 'id'          =>  $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
									, 'name'        => $params[ 'search_get_key' ] . ( ( $params['is_pseudo'] ) ?  '_pseudo' : '' )
									, 'label'       => ''
									, 'disabled'    => false
									, 'class'       => ''
									, 'style'       => 'min-width:100px;'
									, 'placeholder' =>  __('ID or Title' ,'booking')
									, 'attr'        => array( 'maxlength' => '200' )
									, 'value' 		=> $default_value
									, 'onfocus' 	=> ''
					);

					wpbc_flex_text( $params_for_element );

				?></div><?php


				// Reset  keyword
				$params_for_element  =  array(
					'type'             => 'button' ,
					'title'            => '',//__( 'Reset', 'booking' ) . '&nbsp;&nbsp;',  											// Title of the button
					'hint'             => array( 'title' => __( 'Reset keyword text field', 'booking' ), 'position' => 'top' ),  	// Hint
					'link'             => 'javascript:void(0)',  																	// Direct link or skip  it
//					'action'           => "jQuery( '#booking_resource_search_keyword').val('');
//										   window.location.href='" . $option_params['url'] . "';",									// JavaScript
					'action' => ( ( ! $params['is_pseudo'] ) ? "jQuery('#" . $params[ 'search_get_key' ] . "').val( '' ); jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );"
															 : "jQuery('#" . $params[ 'search_get_key' ] . "').val( '' ); jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );" ) ,
					'icon' 			   => array(
												'icon_font' => 'wpbc_icn_close', //'wpbc_icn_rotate_left',
												'position'  => 'left',
												'icon_img'  => ''
											),
					'class'            => 'wpbc_button_as_icon',  																	// ''  | 'wpbc_ui_button_primary'
					'style'            => '', 																						// Any CSS class here
					'mobile_show_text' => true,																						// Show  or hide text,  when viewing on Mobile devices (small window size).
					'attr'             => array()
				);

				?><div class="ui_element" style="flex: 0 1 auto;margin-left: -50px;z-index: 1;"><?php
					wpbc_flex_button( $params_for_element );
				?></div><?php


				// Go  button
				?><div class="ui_element"><?php

					$booking_action = 'booking_resource_search_keyword_btn';

					$el_id = 'ui_btn_' . $booking_action;

					// Escaped construction of search keyword. // FixIn: 9.9.0.11.
					$params_for_element  =  array(
						'type'             => 'button' ,
						'title'            => '',//__( 'Search', 'booking' ) . '&nbsp;&nbsp;',  											// Title of the button
						'hint'             => array( 'title' =>  __('Search' ,'booking'), 'position' => 'top' ),  	// Hint
						'link'             => 'javascript:void(0)',  																	// Direct link or skip  it
						//'link'             => $option_params['url'] . '&wh_resource_id=sdfs+f',
//						'action'           => "if (jQuery('#booking_resource_search_keyword' ).val() == '' ) {
//													wpbc_field_highlight( '#booking_resource_search_keyword' );
//											  } else {
//											  		window.location.href='" . $option_params['url'] . "&wh_resource_id='+window.encodeURIComponent(jQuery('#booking_resource_search_keyword' ).val());
//											  }" ,
						'action' => ( ( ! $params['is_pseudo'] ) ? "jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );"
																 : "jQuery('#" . $params[ 'search_get_key' ] . "').val( jQuery('#" . $params[ 'search_get_key' ] . "_pseudo').val() ); jQuery('#". $params[ 'search_form_id' ] ."').trigger( 'submit' );" ) ,

						'icon' 			   => array(
													'icon_font' => 'wpbc_icn_search',
													'position'  => 'left',
													'icon_img'  => ''
												),
						'class'            => 'wpbc_ui_button _primary',  																						// ''  | 'wpbc_ui_button_primary'
						'style'            => '',																						// Any CSS class here
						'mobile_show_text' => true,																						// Show  or hide text,  when viewing on Mobile devices (small window size).
						'attr'             => array( 'id' => $el_id )
					);

					wpbc_flex_button( $params_for_element );

				?></div><?php

					?></div><?php

				?></div><?php

			?></div><?php

}




            if ( ! $params['is_pseudo'] ) { ?>
            </form>
            <?php } ?>
            <?php wpbc_clear_div(); ?>

            <?php
                if ( $params['is_pseudo'] ) {
                    // Required for opening specific page NUM during saving ////////
                    ?><input type="hidden" value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $search_form_value; ?>" name="<?php echo esc_attr( $params[ 'search_get_key' ] ); ?>" /><?php
                    ?><div class="clear" style="height:20px;"></div><?php
                }
            ?>
        </div>
    </span>
    <?php

    if ( $params['is_pseudo'] ) {

        // Hide pseudo form, if real  search  form does not exist
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                if ( jQuery('#<?php echo esc_js( $params[ 'search_form_id' ] ); ?>').length == 0 ) {
                    jQuery('#<?php echo esc_js( $params['search_form_id'] . '_pseudo' ); ?>').hide();
                }
            });
        </script>
        <?php
    }
}


// FixIn: 9.6.3.5.

// ---------------------------------------------------------------------------------------------------------------------
// JS & CSS
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Load support JavaScript for "Bookings" page  ::  JS Tooltips, Popover, Datepicker
 */
function wpbc_js_for_bookings_page() {

	wpbc_bs_javascript_tooltips();		// JS Tooltips
	wpbc_bs_javascript_popover();		// JS Popover
	wpbc_datepicker_js();				// JS  Datepicker
	wpbc_datepicker_css();				// CSS DatePicker
}


/** Datepicker activation JavaScript */
function wpbc_datepicker_js() {
	?>
	<script type="text/javascript">
		<?php
		echo wpbc_jq_ready_start();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
			if ( 'function' === typeof( jQuery('input.wpdevbk-filters-section-calendar').datepick ) ) {
				jQuery( 'input.wpdevbk-filters-section-calendar' ).datepick(
					{
						beforeShowDay   : function ( date ) {
							return [true, 'date_available'];
						},
						onSelect        : function ( string_dates, js_dates_arr ){  /**
																	 *	string_dates   =   '23.08.2023 - 26.08.2023'    |    '23.08.2023 - 23.08.2023'    |    '19.09.2023, 24.08.2023, 30.09.2023'
																	 *  js_dates_arr   =   range: [ Date (Aug 23 2023), Date (Aug 25 2023)]     |     multiple: [ Date(Oct 24 2023), Date(Oct 20 2023), Date(Oct 16 2023) ]
																	 */
									return wpbc_filters_section_calendar_selected_day( string_dates, {}, this );
						},
						showOn          : 'focus',
						multiSelect     : 0,
						numberOfMonths  : 1,
						stepMonths      : 1,
						prevText        : '&lsaquo;',
						nextText        : '&rsaquo;',
						dateFormat      : 'yy-mm-dd',
						changeMonth     : false,
						changeYear      : false,
						minDate         : null,
						maxDate         : null, 																		//'1Y',
						showStatus      : false,
						multiSeparator  : ', ',
						closeAtTop      : null,																			//!false,
						firstDay        : <?php echo intval( get_bk_option( 'booking_start_day_weeek' ) ); ?>,
						gotoCurrent     : false,
						hideIfNoPrevNext: true,
						useThemeRoller  : false,
						mandatory       : true
					}
				);
				function wpbc_filters_section_calendar_selected_day( string_dates, params_arr, _this ) {
					// FixIn: 10.11.4.2.
					if ( 'ui_wh_booking_date_checkin' === jQuery( _this ).attr( 'id' ) ) {
						setTimeout( function () {
							jQuery( '#ui_wh_booking_date_checkout' ).val( string_dates );
							jQuery( '#ui_wh_booking_date_checkout' ).trigger( 'focus' );
						}, 100 );
					}
				}
			} else {
				console.log( 'WPBC Error. JavaScript library "datepick" was not defined.' );
			}
		<?php
		echo wpbc_jq_ready_end();   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</script>
	<?php
}


/** Support CSS - datepick,  etc... */
function wpbc_datepicker_css(){

	// FixIn: 9.7.3.4
	$width = 300;
	$height = 40;
    ?>
    <style type="text/css">
        #datepick-div {
            max-width: <?php echo intval( $width );  ?>px !important;
			width:100%;
			margin-top: 2px;
			background: #fff;
		}
		#datepick-div .datepick-days-cell {
			height: <?php echo intval( $height );  ?>px !important;
		}
		#datepick-div .datepick-control {
			display: none;
            font-size: 10px;
            text-align: center;
		}
        #datepick-div .datepick .datepick-days-cell a{
            font-size: 12px;
        }
        #datepick-div table.datepick tr td {
            padding: 0 !important;
        }
        #datepick-div .datepick-one-month {
            height: auto;
        }
    </style>
    <?php
}


/** Sortable Table JavaScript */
function wpbc_sortable_js() {
    ?>
    <script type="text/javascript">
        // Activate Sortable Functionality
        jQuery( document ).ready(function(){

            jQuery('.wpbc_input_table tbody th').css('cursor','move');

            jQuery('.wpbc_input_table tbody td.sort').css('cursor','move');

            jQuery('.wpbc_input_table.sortable tbody').sortable({
                    items:'tr',
                    cursor:'move',
                    axis:'y',
                    scrollSensitivity:40,
                    forcePlaceholderSize: true,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: '.wpbc_sortable_table .sort',
                    start:function(event,ui){
                            ui.item.css('background-color','#f6f6f6');
                    },
                    stop:function(event,ui){
                            ui.item.removeAttr('style');
                    }
            });
        });
    </script>
    <?php

}