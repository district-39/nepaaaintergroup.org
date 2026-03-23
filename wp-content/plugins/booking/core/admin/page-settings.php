<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category Content of Settings page 
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
 * Show Content
 *  Update Content
 *  Define Slug
 *  Define where to show
 */
class WPBC_Page_SettingsGeneral extends WPBC_Page_Structure {

	/**
	 * Link to Settings API obj
	 *
	 * @var bool
	 */
	private $settings_api = false;

	public function __construct() {

		if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {            // If this User not "super admin",  then  do  not load this page at all
			// If tab  was not selected or selected default,  then  redirect  it to the "form" tab.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			if ( ( isset( $_REQUEST['page'] ) && ( 'wpbc-settings' === $_REQUEST['page'] ) ) && ( ( ! isset( $_REQUEST['tab'] ) ) || ( 'general' === $_REQUEST['tab'] ) ) ) {
				$_REQUEST['tab'] = 'form';
			}
		} else {
			parent::__construct();
		}
	}

	public function in_page() {

		if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {            // If this User not "super admin",  then  do  not load this page at all.
			return (string) wp_rand( 100000, 1000000 );
		}

		return 'wpbc-settings';
	}


	/**
	 * Get Settings API class - define, show, update "Fields".
	 *
	 * @return object Settings API
	 */
	public function settings_api() {

		if ( false === $this->settings_api ) {
			$this->settings_api = new WPBC_Settings_API_General();
		}

		return $this->settings_api;
	}


	public function tabs() {

		// Init vars.
		$separator_i    = 0;
		$tabs           = array();
		$subtabs        = array();
		$subtab_default = array(
			'type'            => 'subtab',                        // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'.
			'title'           => '',                              // Example: __( 'Dashboard'                                                                   // Title of TAB.
			'page_title'      => '',                              // __( 'Search Availability'                                                                  // Title of Page.
			'hint'            => '',                              // __( 'Configure the layout and functionality of both the search form and search results.'   // Hint.
			'link'            => '',                              // wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_dashboard_tab',        // Link.
			'css_classes'     => '',                              // cls CSS .
			'font_icon'       => 'wpbc_icn_dashboard',
			'font_icon_right' => 'wpbc-bi-question-circle',                              // 'wpbc-bi-question-circle' .
			'default'         => false,                           // Is this sub tab activated by default or not: true || false.
		);

		// =============================================================================================================
		// ==  D A S H B O A R D  ==
		// =============================================================================================================
		$tabs['general'] = array(
			'is_show_top_path'                   => true,                                                              // true | false.  By default value is: false.
			'is_show_top_navigation'             => false,                                                              // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => '',                                                                 // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'General Settings', 'booking' ),                                // Header - Title.  If false, than hidden.
			// translators: 1: Booking Calendar.
			'page_description'                   => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'title'                              => __( 'Dashboard', 'booking' ),                                         // Left Vertical Menu - Title.
			// translators: 1: Booking Calendar.
			'hint'                               => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'link'                               => '',                                                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
			'default'                            => true,                                                               // Is this tab activated by default or not: true / false.
			'font_icon'                          => 'wpbc-bi-speedometer2',
			// 'font_icon_right'                    => 'wpbc-bi-question-circle',
			'css_classes'                        => 'do_expand__' . 'wpbc_general_settings_dashboard_metabox' . '_link',
			'onclick'                            => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . 'wpbc_general_settings_dashboard_metabox' . "' );",
			'folder_style'                       => 'order:10;',
		);
		// FixIn: 10.12.4.7.  $tabs[ 'settings' . ( ++$separator_i ) ] = array_merge( $subtab_default, array( 'type' => 'separator' ,'folder_style' => 'order:10;' ) );


		// =============================================================================================================
		// ==  C A L E N D A R   +   B O O K I N G _ P R O C E S S  ==
		// =============================================================================================================
		$tabs['calendar_booking_process'] = array(
			'page_title'       => __( 'Calendar and Form Settings', 'booking' ),                                        // Header - Title.  If false, than hidden.
			// translators: 1: Booking Calendar.
			'page_description' => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'title'            => __( 'Calendar', 'booking' ),                                                          // Left Vertical Menu - Title.   + Booking Process.
			// translators: 1: Booking Calendar.
			'hint'             => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'font_icon'        => 'wpbc-bi-calendar2-range',                                                            // Left Vertical Menu - Icon.
			'link'             => '',                                                                                   // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
			'css_classes'      => '',                                                                                   // CSS.
			'default'          => false,                                                                                // Is this tab activated by default or not: true / false.
			'folder_style'     => 'order:10;',
		);

		$section_id          = 'wpbc_general_settings_calendar_metabox';
		$subtabs['calendar'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Calendar Settings', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Customize various calendar settings, specify how many months can be scrolled, and defining the legend.', 'booking' ),
				'font_icon'       => 'wpbc-bi-calendar2-range',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link', // sub_bold', .
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id = 'wpbc_general_settings_calendar_metabox' . '#do_other_actions__' . 'blink_day_selections';
		$subtabs['days_selection'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Days Selection', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Choose how users can select days: single day, multiple days, or a date range with minimum and maximum limits.', 'booking' ),
				'font_icon'       => 'wpbc-bi-calendar3-week',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link', // sub_bold', .
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);


		if ( class_exists('wpdev_bk_biz_s') ) {
			$section_id = 'wpbc_general_settings_calendar_metabox' . '#do_other_actions__' . 'blink_change_over_days';
			$subtabs['change_over_days'] = array_merge(
				$subtab_default,
				array(
					'title'           => __( 'Changeover Days', 'booking' ),
					'page_title'      => __( 'General Settings', 'booking' ),
					'hint'            => __( 'Set up check-in and check-out days for multi-day bookings, with visual indicators like diagonal or vertical lines. Ideal for bookings that require split days.', 'booking' ),
					'font_icon'       => 'wpbc_icn_flip',
					'font_icon_right' => 'wpbc-bi-question-circle',
					'css_classes'     => 'do_expand__' . $section_id . '_link', // sub_bold', .
					'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
					'default'         => false,
				)
			);
		}

		$section_id               = 'wpbc_general_settings_days_tooltips_metabox';
		$subtabs['days_tooltips'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Tooltips in days', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Configure tooltips to display information like booked times, costs, details, and availability when hovering over calendar days.', 'booking' ),
				'font_icon'       => 'wpbc-bi-chat-square-dots',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id              = 'wpbc_general_settings_availability_metabox';
		$subtabs['availability'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'General Availability', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Define unavailable weekdays, add booking buffers, and customize unavailable day options.', 'booking' ),
				'font_icon'       => 'wpbc-bi-calendar2-day',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id          = 'wpbc_general_settings_capacity_metabox#wpbc_general_settings_capacity_upgrade_metabox';
		$subtabs['capacity'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Booking Capacity', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Manage the ability to accept multiple bookings for the same date or time slot.', 'booking' ),
				'font_icon'       => 'wpbc_icn_filter_none',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . 'wpbc_general_settings_capacity_metabox' . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);


		$tabs['calendar_booking_process']['subtabs'] = $subtabs;


		// =============================================================================================================
		// ==  C O N F I R M A T I ON  ==
		// =============================================================================================================
		$tabs['booking_confirmation'] = array(
			'is_show_top_path'                   => false,                                                              // true | false.  By default value is: false.
			'is_show_top_navigation'             => false,                                                              // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => '',                                                                 // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'General Settings', 'booking' ),                                // Header - Title.  If false, than hidden.
			// translators: 1: Booking Calendar.
			'page_description'                   => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'title'                              => __( 'Booking Confirmation', 'booking' ),                            // Left Vertical Menu - Title.
			'hint'                               => __( 'Customize how the booking summary is displayed after a booking is created', 'booking' ),
			'link'                               => '',                                                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
			'default'                            => false,                                                               // Is this tab activated by default or not: true / false.
			'font_icon'                          => 'wpbc-bi-card-checklist',
			'font_icon_right'                    => 'wpbc-bi-question-circle',
			'css_classes'                        => 'do_expand__' . 'wpbc_general_settings_booking_confirmation_metabox' . '_link',
			'onclick'                            => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" .
													'wpbc_general_settings_booking_confirmation_metabox#wpbc_general_settings_booking_confirmation_left_metabox#wpbc_general_settings_booking_confirmation_right_metabox#wpbc_general_settings_booking_confirmation_help_metabox' .
													"' );",
			'folder_style'     => 'order:20;',
		);

		// FixIn: 10.12.4.7.  $tabs[ 'settings' . ( ++$separator_i ) ] = array_merge( $subtab_default, array( 'type' => 'separator' ,'folder_style' => 'order:20;' ) );

		// =============================================================================================================
		// == Manage Bookings ==
		// =============================================================================================================
		$tabs['manage_bookings'] = array(
			'is_show_top_path'                   => false,                                                              // true | false.  By default value is: false.
			'is_show_top_navigation'             => false,                                                              // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => '',                                                                 // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'General Settings', 'booking' ),                                // Header - Title.  If false, than hidden.
			// translators: 1: Booking Calendar.
			'page_description'                   => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'title'                              => __( 'Manage Bookings', 'booking' ),                                         // Left Vertical Menu - Title.
			// translators: 1: Booking Calendar.
			'hint'                               => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'font_icon'                          => 'wpbc_icn_app_registration',                                        // Left Vertical Menu - Icon.
			'link'                               => '',                                                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
			'css_classes'                        => '',                                                                 // CSS.
			'icon'                               => '',                                                                 // Icon - link to the real PNG img.
			'default'                            => false,                                                              // Is this tab activated by default or not: true / false.
			'folder_style'                       => 'order:1000',
		);

		$subtabs = array();

		$section_id                       = 'wpbc_general_settings_booking_timeline_metabox';
		$subtabs['booking_timeline'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Timeline (front-end)', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Customize timeline options, including the display of booking details.', 'booking' ),
				'font_icon'       => 'wpbc_icn_align_vertical_bottom wpbc_icn_rotate_90',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link ',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		if ( class_exists('wpdev_bk_personal') ) {
			$section_id                  = 'wpbc_general_settings_bookings_options_metabox';
			$subtabs['bookings_options'] = array_merge(
				$subtab_default,
				array(
					'title'           => __( 'Manage Bookings', 'booking' ),
					'page_title'      => __( 'General Settings', 'booking' ),
					'hint'            => __( 'Allow customers to edit or cancel their own bookings.', 'booking' ),
					'font_icon'       => 'wpbc_icn_app_registration mode_edit_outline edit_calendar',
					'font_icon_right' => 'wpbc-bi-question-circle',
					'css_classes'     => 'do_expand__' . $section_id . '_link ',
					'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
					'default'         => false,
				)
			);
		}


		if ( class_exists('wpdev_bk_biz_s') ) {
			$section_id                           = 'wpbc_general_settings_auto_cancelation_approval_metabox';
			$subtabs['auto_cancelation_approval'] = array_merge(
				$subtab_default,
				array(
					'title'           => __( 'Auto Cancellation / Auto Approval', 'booking' ),
					'page_title'      => __( 'General Settings', 'booking' ),
					'hint'            => __( 'Enable automatic approval or cancellation of bookings.', 'booking' ),
					'font_icon'       => 'wpbc_icn_published_with_changes',
					'font_icon_right' => 'wpbc-bi-question-circle',
					'css_classes'     => 'do_expand__' . $section_id . '_link',
					'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
					'default'         => false,
				)
			);
		}

		$tabs['manage_bookings']['subtabs'] = $subtabs;


		// =============================================================================================================
		// == Admin Panel ==
		// =============================================================================================================
		$tabs['admin_panel'] = array(
			'is_show_top_path'                   => false,                                                              // true | false.  By default value is: false.
			'is_show_top_navigation'             => false,                                                              // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => '',                                                                 // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'General Settings', 'booking' ),                                // Header - Title.  If false, than hidden.
			'page_description'                   => __( 'Configure various options and settings in the admin panel.', 'booking' ), // Header - Title Description.  If false, than hidden.
			'title'                              => __( 'Admin Panel', 'booking' ),                                         // Left Vertical Menu - Title.
			// translators: 1: Booking Calendar.
			'hint'                               => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'font_icon'                          => 'wpbc_icn_widgets',                                        // Left Vertical Menu - Icon.
			'link'                               => '',                                                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
			'css_classes'                        => '',                                                                 // CSS.
			'icon'                               => '',                                                                 // Icon - link to the real PNG img.
			'default'                            => false,                                                              // Is this tab activated by default or not: true / false.
			'folder_style'                       => 'order:1000',
		);

		$subtabs = array();

		$section_id                  = 'wpbc_general_settings_booking_listing_metabox';
		$subtabs['booking_listing'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Booking Admin Panel', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Configure various options and settings in the admin panel.', 'booking' ),
				'font_icon'       => 'wpbc-bi-list',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id                  = 'wpbc_general_settings_booking_calendar_overview_metabox';
		$subtabs['booking_calendar_overview'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Timeline View (Back-End)', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Customize timeline options in the admin panel, including displaying booking details.', 'booking' ),
				'font_icon'       => 'wpbc-bi-bar-chart-steps _icn_timeline 0wpbc_icn_rotate_45 0wpbc_icn_align_vertical_bottom',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id            = 'wpbc_general_settings_datestimes_metabox';
		$subtabs['datestimes'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Date and Time Formats', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Customize the display format for dates and times.', 'booking' ),
				'font_icon'       => 'wpbc-bi-braces-asterisk _icn_password',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id             = 'wpbc_general_settings_permissions_metabox';
		$subtabs['permissions'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Plugin Menu / Permissions', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Set user permissions for accessing plugin menu pages and configure the menu position.', 'booking' ),
				'font_icon'       => 'wpbc-bi-key',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id             = 'wpbc_general_settings_translations_metabox';
		$subtabs['translations'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Translations', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Choose whether to use local translations or WordPress translations.', 'booking' ),
				'font_icon'       => 'wpbc_icn_translate 0wpbc-bi-translate',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$tabs['admin_panel']['subtabs'] = $subtabs;


		// =============================================================================================================
		// == Advanced ==
		// =============================================================================================================
		$tabs['advanced'] = array(
			'is_show_top_path'                   => false,                                                              // true | false.  By default value is: false.
			'is_show_top_navigation'             => false,                                                              // true | false.  By default value is: false.
			'left_navigation__default_view_mode' => '',                                                                 // '' | 'min' | 'compact' | 'max' | 'none'.  By default value is: ''.
			'page_title'                         => __( 'General Settings', 'booking' ),                                // Header - Title.  If false, than hidden.
			// translators: 1: Booking Calendar.
			'page_description'                   => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'title'                              => __( 'Advanced', 'booking' ),                                         // Left Vertical Menu - Title.
			// translators: 1: Booking Calendar.
			'hint'                               => sprintf( __( 'Configure various options and settings in the %s.', 'booking' ), 'Booking Calendar' ),
			'font_icon'                          => 'wpbc-bi-gear',                                                     // Left Vertical Menu - Icon.
			'link'                               => '',                                                                 // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
			'css_classes'                        => '',                                                                 // CSS.
			'icon'                               => '',                                                                 // Icon - link to the real PNG img.
			'default'                            => false,                                                              // Is this tab activated by default or not: true / false.
			'folder_style'                       => 'order:1000',
		);

		$subtabs = array();

		$section_id             = 'wpbc_general_settings_advanced_metabox';
		$subtabs['advanced_options'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Advanced Options', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Configure loading of CSS/JavaScript files, set the number of simultaneous requests, and other advanced options.', 'booking' ),
				'font_icon'       => 'wpbc_icn_adjust',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id             = 'wpbc_general_settings_uninstall_metabox';
		$subtabs['uninstall'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Uninstall / Deactivation', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Enable the option to fully erase booking data when the plugin is deactivated.', 'booking' ),
				'font_icon'       => 'wpbc-bi-trash',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id             = 'wpbc_general_settings_information_metabox';
		$subtabs['information'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Plugin Info & News', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => __( 'Stay informed with the latest news and details about the plugin.', 'booking' ),
				'font_icon'       => 'wpbc_icn_newspaper -bi-newspaper _icn_news 0wpbc-bi-translate',
				'font_icon_right' => 'wpbc-bi-question-circle',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);

		$section_id             = 'wpbc_general_settings_help_metabox';
		$subtabs['tools'] = array_merge(
			$subtab_default,
			array(
				'title'           => __( 'Tools', 'booking' ),
				'page_title'      => __( 'General Settings', 'booking' ),
				'hint'            => '', //__( 'Stay informed with the latest news and details about the plugin. ', 'booking' ),
				'font_icon'       => 'wpbc_icn_construction',
				'font_icon_right' => '',
				'css_classes'     => 'do_expand__' . $section_id . '_link',
				'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
				'default'         => false,
			)
		);
 		if ( class_exists('wpdev_bk_multiuser') ) {
			$section_id             = 'wpbc_general_settings_multiuser_metabox';
			$subtabs['multiuser'] = array_merge(
				$subtab_default,
				array(
					'title'           => __( 'Multiuser Options', 'booking' ),
					'page_title'      => __( 'General Settings', 'booking' ),
					'hint'            => __( 'Multiuser Options', 'booking' ),
					'font_icon'       => 'wpbc_icn_people_alt',
					'font_icon_right' => 'wpbc-bi-question-circle',
					'css_classes'     => 'do_expand__' . $section_id . '_link',
					'onclick'         => "wpbc_admin_ui__do__open_url__expand_section( '" . wpbc_get_settings_url() . "', '" . $section_id . "' );",
					'default'         => false,
				)
			);
		}

		$tabs['advanced']['subtabs'] = $subtabs;

		// FixIn: 10.12.4.7.  $tabs[ 'settings' . ( ++$separator_i ) ] = array_merge( $subtab_default, array( 'type' => 'separator' ,'folder_style' => 'order:900;' ) );
		// $tabs[ 'settings' . ( ++$separator_i ) ] = array_merge( $subtab_default, array( 'type' => 'separator' ,'folder_style' => 'order:1000;' ) );

		return $tabs;
	}


	public function content() {

		// Checking.

		do_action( 'wpbc_hook_settings_page_header', 'general_settings' );       // Define Notices Section and show some static messages, if needed.

		if ( ! wpbc_is_mu_user_can_be_here( 'activated_user' ) ) {
			return false;
		}    // Check if MU user activated, otherwise show Warning message.

		if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {
			return false;
		}  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.

		$is_can = apply_bk_filter( 'recheck_version', true );
		if ( ! $is_can ) {
			?>
			<script type="text/javascript"> jQuery(document).ready(function () {
					jQuery('.wpdvlp-sub-tabs').remove();
				});
			</script>
			<?php
			return;
		}


		// Init Settings API & Get Data from DB.
		$this->settings_api();                                                  // Define all fields and get values from DB.

		// Submit .

		$submit_form_name = 'wpbc_general_settings_form';                       // Define form name.

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ 'is_form_sbmitted_' . $submit_form_name ] ) ) {

			// Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }.
			$nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page.

			// Save Changes .
			$this->update();
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_GET['scroll_to_section'] ) ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function () {
					jQuery('#<?php echo esc_js( sanitize_text_field( wp_unslash( $_GET['scroll_to_section'] ) ) ); ?> a').trigger('click');
				});
			</script>
			<?php
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ( isset( $_GET['wpbc_setup_wizard'] ) ) && ( 'reset' === $_GET['wpbc_setup_wizard'] ) ) {

			wpbc_setup_wizard_page__force_in_get();

			?>
			<div class="wpdvlp-sub-tabs wpbc_redirection_message" style="margin: 20px 0;padding: 1em;font-size: 14px;">
			<a href="<?php echo esc_url( wpbc_get_setup_wizard_page_url() ); ?>">Redirect</a> after <span class="wpbc_countdown">1</span> second...</div>
			<?php

			wpbc_redirect( wpbc_get_setup_wizard_page_url() );
		}

		// JavaScript: Tooltips, Popover, Datepick (js & css) .
		echo '<span class="wpdevelop">';
		wpbc_js_for_bookings_page();
		echo '</span>';

		// TODO: 2025-05-05 update in real  top  path. wpbc_ui_settings__top_path();

		// Content .
		?>
		<div class="clear"></div>
		<div class="wpbc_settings_flex_container">

			<div class="wpbc_settings_flex_container_left" style="display:none;">

				<div class="wpbc_settings_navigation_column">

					<div id="wpbc_general_settings_dashboard_tab" class="wpbc_settings_navigation_item wpbc_settings_navigation_item_active">
						<?php
						$title = esc_attr__( 'Dashboard', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_dashboard_tab a' ,'#wpbc_general_settings_dashboard_metabox', '<?php echo esc_js( $title ); ?>' );"
							href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_calendar_tab" class="wpbc_settings_navigation_item wpbc_navigation_top_border">
						<?php
						$title = esc_attr__( 'Calendar', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_calendar_tab a' ,'#wpbc_general_settings_calendar_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_days_tooltips_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Tooltips in days', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_days_tooltips_tab a' ,'#wpbc_general_settings_days_tooltips_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_availability_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Availability', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_availability_tab a' ,'#wpbc_general_settings_availability_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_capacity_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Capacity', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_capacity_tab a' ,'#wpbc_general_settings_capacity_metabox,#wpbc_general_settings_capacity_upgrade_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_form_tab" class="wpbc_settings_navigation_item">
						<?php
						$title = esc_attr__( 'Form Options', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_form_tab a' ,'#wpbc_general_settings_form_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_time_slots_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Time Slots', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_time_slots_tab a' ,'#wpbc_general_settings_time_slots_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_booking_confirmation_tab" class="wpbc_settings_navigation_item">
						<?php
						$title = esc_attr__( 'Booking Confirmation', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_confirmation_tab a' ,'#wpbc_general_settings_booking_confirmation_metabox,#wpbc_general_settings_booking_confirmation_left_metabox,#wpbc_general_settings_booking_confirmation_right_metabox,#wpbc_general_settings_booking_confirmation_help_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>

					<div id="wpbc_general_settings_booking_timeline_tab" class="wpbc_settings_navigation_item wpbc_navigation_top_border">
						<?php
						$title = esc_attr__( 'Timeline (front-end)', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_timeline_tab a' ,'#wpbc_general_settings_booking_timeline_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<?php if ( class_exists('wpdev_bk_personal') ) { ?>
						<div id="wpbc_general_settings_bookings_options_tab" class="wpbc_settings_navigation_item wpbc_navigation_top_border">
							<?php
							$title = esc_attr__( 'Manage Bookings', 'booking' );
							?>
							<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_bookings_options_tab a' ,'#wpbc_general_settings_bookings_options_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
						</div>
					<?php } ?>

					<?php if ( class_exists('wpdev_bk_biz_s') ) { ?>
							<div id="wpbc_general_settings_auto_cancelation_approval_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
							<?php
							$title = esc_attr__( 'Auto Cancellation / Auto Approval', 'booking' );
							?>
							<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_auto_cancelation_approval_tab a' ,'#wpbc_general_settings_auto_cancelation_approval_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
						</div>
					<?php } ?>


					<?php if ( class_exists('wpdev_bk_multiuser') ) { ?>
						<div id="wpbc_general_settings_multiuser_tab" class="wpbc_settings_navigation_item">
							<?php
							$title = esc_attr__( 'Multiuser options', 'booking' );
							?>
							<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_multiuser_tab a' ,'#wpbc_general_settings_multiuser_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
						</div>
					<?php } ?>

					<div id="wpbc_general_settings_booking_listing_tab" class="wpbc_settings_navigation_item wpbc_navigation_top_border">
						<?php
						$title = esc_attr__( 'Admin Panel', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_listing_tab a' ,'#wpbc_general_settings_booking_listing_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_booking_calendar_overview_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Timeline View', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_calendar_overview_tab a' ,'#wpbc_general_settings_booking_calendar_overview_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_datestimes_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Date / Time Formats', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_datestimes_tab a' ,'#wpbc_general_settings_datestimes_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_permissions_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Plugin Menu / Permissions', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_permissions_tab a' ,'#wpbc_general_settings_permissions_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_translations_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Translations', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_translations_tab a' ,'#wpbc_general_settings_translations_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_advanced_tab" class="wpbc_settings_navigation_item">
						<?php
						$title = esc_attr__( 'Advanced', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_advanced_tab a' ,'#wpbc_general_settings_advanced_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_uninstall_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Uninstall / deactivation', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_uninstall_tab a' ,'#wpbc_general_settings_uninstall_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<div id="wpbc_general_settings_information_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
						<?php
						$title = esc_attr__( 'Info / News', 'booking' );
						?>
						<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_information_tab a' ,'#wpbc_general_settings_information_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>
					<?php if ( ( class_exists('wpdev_bk_personal') ) && ( ! wpbc_is_this_demo() ) ) { ?>
						<div id="wpbc_general_settings_help_tab" class="wpbc_settings_navigation_item wpbc_navigation_sub_item">
							<?php
							$title = esc_attr__( 'Tools', 'booking' );
							?>
							<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_help_tab a' ,'#wpbc_general_settings_help_metabox', '<?php echo esc_js( $title ); ?>' );" href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
						</div>
					<?php } ?>

					<div id="wpbc_general_settings_all_tab" class="wpbc_settings_navigation_item wpbc_navigation_top_border">
						<?php
						$title = esc_attr__( 'Show All Settings', 'booking' );
						?>
						<a 	onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_all_tab a' ,'.postbox', '<?php echo esc_js( $title ); ?>' );" 
							href="javascript:void(0);"><span><?php echo esc_html( $title ); ?></span></a>
					</div>

				</div>

			</div>
			<div class="wpbc_settings_flex_container_right">

					<span class="metabox-holder">
					<form name="<?php echo esc_attr( $submit_form_name ); ?>" id="<?php echo esc_attr( $submit_form_name ); ?>" action="" method="post">
						<?php
						// N o n c e   field, and key for checking   S u b m i t.
						wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
						?>
						<input type="hidden" name="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" id="is_form_sbmitted_<?php echo esc_attr( $submit_form_name ); ?>" value="1"/>
						<input type="hidden" name="form_visible_section" id="form_visible_section" value=""/>

					<?php
					if ( wpbc_is_show_general_setting_options() ) {
						// FixIn: 8.9.4.11.
						?>

						<div class="wpbc_settings_row wpbc_settings_row_full_width" >

							<div 	id="wpbc_general_settings_dashboard_metabox" class="postbox"
									style="background: transparent;border: 0;box-shadow: none;"><?php //wpbc_open_meta_box_section( 'wpbc_general_settings_dashboard', __('Dashboard'), array( 'is_section_visible_after_load' => true, 'is_show_minimize' => false ) ); ?>
								<?php

								wpbc_ui_settings__panel__statistic();

								wpbc_ui_settings__panel__all_settings_panels();

								wpbc_ui_settings__panel__plugin_version();

						    ?></div><?php //wpbc_close_meta_box_section(); ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_calendar', __('Calendar', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'calendar' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_days_tooltips', __('Calendar Dates Tooltips', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'days_tooltips' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_availability', __('Availability', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'availability' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php
								wpbc_open_meta_box_section( 'wpbc_general_settings_booking_confirmation',
																__( 'Booking Confirmation', 'booking' ),
																array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) );
								$this->settings_api()->show( 'booking_confirmation' );
								wpbc_close_meta_box_section();
							?>
							<div class="wpbc_settings_row wpbc_settings_row_left">
							<?php
								wpbc_open_meta_box_section( 'wpbc_general_settings_booking_confirmation_left',
																__( 'Section', 'booking' ) .  ': ' . __( 'Personal Information', 'booking' ),
																array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) );
								$this->settings_api()->show( 'booking_confirmation_left' );
								wpbc_close_meta_box_section();

								wpbc_open_meta_box_section( 'wpbc_general_settings_booking_confirmation_right',
																__( 'Section', 'booking' ) .  ': ' . __( 'Booking details', 'booking' ),
																array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) );
								$this->settings_api()->show( 'booking_confirmation_right' );
								wpbc_close_meta_box_section();
							?>
							</div>
							<div class="wpbc_settings_row wpbc_settings_row_right">
							<?php
								wpbc_open_meta_box_section( 'wpbc_general_settings_booking_confirmation_help',
																__( 'Shortcodes', 'booking' ),
																array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) );
								$this->settings_api()->show( 'booking_confirmation_help' );
								wpbc_close_meta_box_section();
							?>
							</div>
							<div class="clear"></div>

							<?php if ( class_exists('wpdev_bk_personal') ) { ?>
								<?php wpbc_open_meta_box_section( 'wpbc_general_settings_bookings_options', __('Bookings Options', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

								<?php $this->settings_api()->show( 'bookings_options' ); ?>

								<?php wpbc_close_meta_box_section(); ?>
							<?php } ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_booking_listing', __('Booking Admin Panel', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'booking_listing' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_booking_calendar_overview', __('Calendar Overview (admin panel)', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php // FixIn: 8.5.2.20.
								$this->settings_api()->show( 'booking_calendar_overview' );
							?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_booking_timeline', __('Timeline (front-end)', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php
								$this->settings_api()->show( 'booking_timeline' );
							?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php if ( class_exists('wpdev_bk_biz_s') ) { ?>


								<?php wpbc_open_meta_box_section( 'wpbc_general_settings_auto_cancelation_approval', __('Auto cancellation / auto approval of bookings', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

								<?php $this->settings_api()->show( 'auto_cancelation_approval' ); ?>

								<?php wpbc_close_meta_box_section(); ?>

							<?php } ?>



							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_capacity', __('Capacity', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'capacity' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php
							if ( ! class_exists( 'wpdev_bk_personal' ) ) {

								$wpbc_metabox_id = 'wpbc_general_settings_capacity_upgrade';

								ob_start();
								$is_panel_visible = wpbc_is_dismissed( $wpbc_metabox_id . '_metabox', array(
																		'title' => '<i class="menu_icon icon-1x wpbc_icn_close"></i> ',
																		'hint'  => __( 'Dismiss', 'booking' ),
																		'class' => 'wpbc_panel_get_started_dismiss',
																		'css'   => 'background: #fff;border-radius: 7px;'
																	));
								?>
								<script type="text/javascript"> jQuery('#<?php echo esc_js( $wpbc_metabox_id ); ?>_metabox').hide(); </script>
								<?php
								$dismiss_button_content = ob_get_clean();

								if ( $is_panel_visible ) {

									wpbc_open_meta_box_section( $wpbc_metabox_id,
																	  __('Booking Quantity Control - Set limits for the number of bookings per day or time slot.', 'booking'),
																	  array(  'is_section_visible_after_load' => false,
																			  'is_show_minimize'   => false,
																			  'dismiss_button'     => $dismiss_button_content
															  ) );

									$this->settings_api()->show( 'capacity_upgrade' );

									wpbc_close_meta_box_section();
								}
							}
							?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_advanced', __('Advanced', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'advanced' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php  wpbc_open_meta_box_section( 'wpbc_general_settings_information', __('Information', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php  $this->settings_api()->show( 'information' ); ?>

							<?php  wpbc_close_meta_box_section(); ?>



							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_datestimes', __('Date : Time', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'date_time' ); ?>

							<?php wpbc_close_meta_box_section(); ?>



							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_permissions', __('Plugin Menu', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'permissions' ); ?>

							<?php wpbc_close_meta_box_section(); ?>


							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_uninstall', __('Uninstall / deactivation', 'booking'), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'uninstall' ); ?>

							<?php wpbc_close_meta_box_section(); ?>

							<?php if ( ( class_exists( 'wpdev_bk_personal' ) ) && ( ! wpbc_is_this_demo() ) ) { ?>
								<?php wpbc_open_meta_box_section( 'wpbc_general_settings_help', __( 'Tools', 'booking' ), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

								<?php $this->settings_api()->show( 'help' ); ?>

								<?php wpbc_close_meta_box_section(); ?>
							<?php } ?>

							<?php wpbc_open_meta_box_section( 'wpbc_general_settings_translations', __( 'Translations', 'booking' ), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

							<?php $this->settings_api()->show( 'translations' ); ?>

							<?php wpbc_translation_buttons_settings_section(); ?>

							<?php wpbc_close_meta_box_section(); ?>

							<?php if ( class_exists( 'wpdev_bk_multiuser' ) ) {    // FixIn: 9.2.3.8. ?>

								<?php wpbc_open_meta_box_section( 'wpbc_general_settings_multiuser', __( 'Multiuser options', 'booking' ), array( 'is_section_visible_after_load' => false, 'is_show_minimize' => false ) ); ?>

								<?php $this->settings_api()->show( 'multiuser' ); ?>

								<?php wpbc_close_meta_box_section(); ?>

							<?php } ?>

						</div>
						<div class="clear"></div>
						<div class="container_for_save_buttons">
							<input type="submit" value="<?php esc_attr_e( 'Save Changes', 'booking' ); ?>" class="button button-primary wpbc_submit_button"/>
							<span class="sub_right">
								<a style="margin:0;" class="button button"
									onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_all_tab a' ,'.postbox', 'Show All Settings' );"
									href="javascript:void(0);">
									<?php
										esc_html_e( 'Show All Settings', 'booking' )
									?>
								</a>
								<?php
								if ( 'translations_updated_from_wpbc_and_wp' !== get_bk_option( 'booking_translation_update_status' ) ) {

									$current_locale = wpbc_get_maybe_reloaded_booking_locale();

									if ( ! in_array( $current_locale, array( 'en_US', 'en_CA', 'en_GB', 'en_AU' ), true ) ) {

										echo '<a class="button button" href="'
											. esc_url( wpbc_get_settings_url() . '&system_info=show&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' ) . '&update_translations=1#wpbc_general_settings_system_info_metabox' )
											. '">'
											. esc_html__( 'Update Translations', 'booking' )
											. '</a>';
									}
								}

								if ( ! wpbc_is_this_demo() ) {

									echo '<a style="margin:0 2em;" class="button button" href="'
										. esc_url( wpbc_get_settings_url() . '&system_info=show&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' ) . '&restore_dismissed=On#wpbc_general_settings_restore_dismissed_metabox' )
										. '">'
										. esc_html__( 'Restore all dismissed windows', 'booking' )
										. '</a>';
								}
								?>
							</span>
						</div>

					<?php } ?>
					</form>
					</span>

			</div>
		</div>


    	<?php

		do_action( 'wpbc_hook_settings_page_footer', 'general_settings' );
    }


	public function update() {

		$validated_fields = $this->settings_api()->validate_post();             // Get Validated Settings fields in $_POST request.

		$validated_fields = apply_filters( 'wpbc_settings_validate_fields_before_saving', $validated_fields );   // Hook for validated fields.

		/**
		 * Skip saving specific option, for example in Demo mode.
		 * // unset( $validated_fields['booking_start_day_weeek'] );
		 */

		// FixIn: 9.8.6.1.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['form_visible_section'] ) ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function () {
					jQuery('<?php
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						echo esc_js( $_POST['form_visible_section'] );
						?> a').trigger('click');
				});
			</script>
			<?php
		}

		$this->settings_api()->save_to_db( $validated_fields );                 // Save fields to DB.

		wpbc_show_changes_saved_message();

		/**
		 * // O L D   W A Y:   Saving Fields Data
		 * //      update_bk_option( 'booking_is_delete_if_deactive'
		 * //                       , WPBC_Settings_API::validate_checkbox_post('booking_is_delete_if_deactive') );
		 * //      ( (isset( $_POST['booking_is_delete_if_deactive'] ))?'On':'Off') );
		 */
	}
}


/**
 *

if ( ! wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {                    // If this User not "super admin",  then  do  not load this page at all

    if (  ( ! isset( $ _GET['tab'] ) ) || ( $_GET['tab'] == 'general' )  ) {     // If tab  was not selected or selected default,  then  redirect  it to the "form" tab.
        $_GET['tab'] = 'form';
    }
} else {
    add_action('wpbc_menu_created', array( new WPBC_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
}
*/

 add_action('wpbc_menu_created', array( new WPBC_Page_SettingsGeneral() , '__construct') );    // Executed after creation of Menu
 