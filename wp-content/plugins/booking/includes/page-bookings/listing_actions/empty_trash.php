<?php
/**
 * Class to Empty_Trash -- Option for "Bulk Actions - Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript: in    ../includes/print/_src/bookings_print.js  ->   function wpbc_print_dialog__show() { ... } .

/**
 * Class WPBC_Listing_Actions__Empty_Trash
 */
class WPBC_Listing_Actions__Empty_Trash{

	const ACTION = 'empty_trash';

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
		$css_class .= 'ul_dropdown_menu_li_trash_color ';
		$css_class .= 'ul_dropdown_menu_li_action_' . self::ACTION;

		$el_id = 'ul_dropdown_menu_li_action_' . self::ACTION;

		// Option Title.
		$html = "<a href=\"javascript:void(0)\"  class=\"" . esc_attr( $css_class ) . "\" 
					onclick=\"if ( wpbc_are_you_sure('" . esc_attr( __( 'Do you really want to do this ?', 'booking' ) ) . "') ) {
								wpbc_ajx_booking_ajax_action_request( { 
																	'booking_action'       : '" . esc_js( self::ACTION ) . "', 
																	'ui_clicked_element_id': '{$el_id}'  
																} );
								wpbc_button_enable_loading_icon( this ); 
							} \"
					title=\"" . esc_attr( __( 'Empty Trash', 'booking' ) ) . "\"
				 >" .
					esc_js( __( 'Empty Trash', 'booking' ) ) .
					' <i class="menu_icon icon-1x wpbc_icn_delete_forever"></i>' .
				'</a>';

		return $html;
	}
}
