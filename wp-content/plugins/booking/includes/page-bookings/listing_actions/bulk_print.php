<?php
/**
 * Class to Print -- Option for "Bulk Actions - Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript: in    ../includes/print/_src/bookings_print.js  ->   function wpbc_print_dialog__show() { ... } .

/**
 * Class WPBC_Listing_Actions__Bulk_Print
 */
class WPBC_Listing_Actions__Bulk_Print{

	const ACTION = 'bulk_print';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_option() {

		$css_class  = 'ul_dropdown_menu_li_action ';
		$css_class .= 'ul_dropdown_menu_li_action_' . self::ACTION;

		// Option Title.
		$html = "<a href=\"javascript:void(0)\"  class=\"" . esc_attr( $css_class ) . "\" 
					onclick=\"wpbc_print_dialog__show();\"
					title=\"" . esc_attr( __( 'Print bookings', 'booking' ) ) . "\"
				 >" .
					esc_js( __( 'Print', 'booking' ) ) .
					' <i class="menu_icon icon-1x wpbc_icn_print"></i>'.
				'</a>';

		return $html;

	}
}
