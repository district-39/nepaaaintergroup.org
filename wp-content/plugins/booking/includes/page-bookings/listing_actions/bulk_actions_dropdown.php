<?php
/**
 * Class Actions Toolbar -- 'Bulk Actions' Dropdown.
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript Hook: in    ../includes/page-bookings/_src/boo_listing__actions.js  ->   function wpbc_boo_listing__init_hook__sort_by() { ... } .

/**
 * Class WPBC_Listing_Actions__Bulk_Actions
 *
 *   INFO: Get request parameter in template:    var p_value = wpbc_ajx_booking_listing.search_get_param( 'bulk_actions' );
 */
class WPBC_Listing_Actions__Bulk_Actions {

	const ID = 'bulk_actions';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_button() {

		$el_arr = array(
			'font_icon'       => 'wpbc_icn_adjust',
			'title'           => '<span class="nav-tab-text hide_in_mobile">' . __( 'Bulk Actions', 'booking' ) . ' </span><span class="selected_value"></span>',
			'hint'            => array(
				'title'    => __( 'Select sorting order', 'booking' ),
				'position' => 'top',
			),
			'position'        => 'left',
			'has_down_arrow'  => true,
			'has_border'      => true,
			'container_class' => 'ul_dropdown_menu__' . self::ID,
			'items'           => array(
				array( 'type' => 'header', 'title' => __( 'Action on selected bookings', 'booking' ), 'class' => 'hide_button_if_no_selection' ),
				array( 'html' => WPBC_Listing_Actions__Set_Approve::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Set_Pending::get_option() ),
				array( 'type' => 'divider', 'class' => 'hide_button_if_no_selection' ),
				array( 'html' => WPBC_Listing_Actions__Set_As_Unread::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Set_As_Read::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Set_Read_All::get_option() ),
				array( 'type' => 'divider', 'class' => 'hide_button_if_no_selection0' ),
				array( 'html' => WPBC_Listing_Actions__Bulk_Print::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Export_CSV::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Import_Google_Calendar::get_option() ),
				array( 'type' => 'divider', 'class' => 'hide_button_if_no_selection' ),
				array( 'html' => WPBC_Listing_Actions__To_Trash::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Restore_From_Trash::get_option() ),
				array( 'html' => WPBC_Listing_Actions__Completely_Delete::get_option() ),
				array( 'type' => 'divider' ),
				array( 'html' => WPBC_Listing_Actions__Empty_Trash::get_option() ),
			),
		);

		wpbc_ui_el__dropdown_menu( $el_arr );
	}
}
