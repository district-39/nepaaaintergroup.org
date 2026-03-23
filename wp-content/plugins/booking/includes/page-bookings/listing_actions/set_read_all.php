<?php
/**
 * Class to Read All -- Option for "Bulk Actions - Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript: in    ../includes/print/_src/bookings_print.js  ->   function wpbc_print_dialog__show() { ... } .

/**
 * Class WPBC_Listing_Actions__Set_Read_All
 */
class WPBC_Listing_Actions__Set_Read_All{

	const ACTION = 'set_booking_as_read';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_option() {

		// In some versions: if ( ! class_exists( 'wpdev_bk_personal' ) ) { return false; } .

		if ( ! wpbc_is_user_can( self::ACTION, wpbc_get_current_user_id() ) ) {
			return false;
		}

		$css_class  = 'ul_dropdown_menu_li_action ';
		$css_class .= 'ul_dropdown_menu_li_action_' . self::ACTION;

		$el_id = 'ul_dropdown_menu_li_action_' . self::ACTION;

		// Option Title.
		$html = "<a href=\"javascript:void(0)\"  class=\"" . esc_attr( $css_class ) . "\" 
					onclick=\"wpbc_ajx_booking_ajax_action_request( { 
																		'booking_action'         : '" . esc_js( self::ACTION ) . "', 
																		'booking_id'       : '-1',
																		'reason_of_action' : '',
																		'ui_clicked_element_id': '{$el_id}'  																		
																} );
							 wpbc_button_enable_loading_icon( this ); \"
					title=\"" . esc_attr( __( 'Mark as read all bookings', 'booking' ) ) . "\"
				 >" .
					esc_js( __( 'Mark all as read', 'booking' ) ) .
					' <i class="menu_icon icon-1x wpbc-bi-0-circle-fill"></i>' .
				'</a>';

		return $html;
	}
}
