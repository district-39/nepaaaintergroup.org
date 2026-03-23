<?php
/**
 * Class to Set_As_Unread -- Option for "Bulk Actions - Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Class WPBC_Listing_Actions__Set_As_Unread
 */
class WPBC_Listing_Actions__Set_As_Unread{

	const ACTION = 'set_booking_as_unread';

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
		$css_class .= 'hide_button_if_no_selection ';
		$css_class .= 'ul_dropdown_menu_li_action_' . self::ACTION;

		$el_id = 'ul_dropdown_menu_li_action_' . self::ACTION;

		// Option Title.
		$html = "<a href=\"javascript:void(0)\"  class=\"" . esc_attr( $css_class ) . "\" 
					onclick=\"if ( ! wpbc_is_modal_accessible( '#wpbc_modal__set_booking_as_unread__section' ) ) {
								return false;
							}
							jQuery( '.wpbc_modal__title__reason__booking_id' ).html( 'ID: ' + wpbc_get_selected_row_id() );
							jQuery( '#wpbc_modal__set_booking_as_unread__section' ).wpbc_my_modal( 'show' );
							jQuery( '#wpbc_modal__set_booking_as_unread__value' ).trigger( 'focus' );
							\" 
					title=\"" . esc_attr( __( 'Mark as Unread selected bookings', 'booking' ) ) . "\"
				 >" .
					esc_js( __( 'Mark as unread', 'booking' ) ) .
					' <i class="menu_icon icon-1x wpbc-bi-circle-square"></i>' .
				'</a>';

		return $html;
	}


	/**
	 * Get Template for Modal Window -  Booking Cost Edit - Layout - Modal Window structure
	 *
	 * @return false|void
	 */
	public static function template_for_modal() {

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__set_booking_as_unread__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
								<span class="wpbc_modal__title__reason">
								<?php
									esc_html_e( 'Mark as Unread selected bookings', 'booking' );
								?>
								</span>
								<sup class="wpbc_modal__title__reason__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<!--div class="modal-body">
							<label for="wpbc_modal__set_booking_as_unread__value" style="font-size: 14px;margin: 5px 0 10px;">
							<?php
								echo '<strong>' . esc_attr__( 'Enter the reason for the operation.', 'booking' ) . '</strong> (' . esc_attr__( 'Optional', 'booking' ) . ')';
							?>
							</label>
							<textarea id="wpbc_modal__set_booking_as_unread__value"
									name="wpbc_modal__set_booking_as_unread__value" cols="87" rows="3"
									placeholder="<?php echo esc_attr__( 'Optional', 'booking' ) . ' '; ?>"
							></textarea>
							<input type="hidden" id="wpbc_modal__set_booking_as_unread__booking_id" value=""/>
							<p class="help-block">
								<?php
								/* translators: 1: ... */
								echo wp_kses_post( sprintf( __( 'In the %1$semail template%2$s, use the %3$s shortcode to display this text.', 'booking' ), '<a href="' . esc_url( wpbc_get_settings_url( true, false ) . '&tab=email&subtab=trash' ) . '">', '</a>', '<b>[reason]</b>' ) );
								?>
							</p>
						</div-->
						<div class="modal-footer">
							<a  id="wpbc_modal__set_booking_as_unread__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: wpbc_ajx_booking_ajax_action_request( {
														'booking_action'       : '<?php echo esc_js( self::ACTION ); ?>',
														'booking_id'           : wpbc_get_selected_row_id(),
														'reason_of_action'     : '', // jQuery( '#wpbc_modal__set_booking_as_unread__value' ).val(),
														'ui_clicked_element_id': 'wpbc_modal__set_booking_as_unread__button_send'
												} );
												wpbc_button_enable_loading_icon( this );
												jQuery( '.wpbc_modal__title__reason__booking_id' ).html('');
												jQuery( '#wpbc_modal__set_booking_as_unread__value' ).val(''),
												jQuery( '#wpbc_modal__set_booking_as_unread__section' ).wpbc_my_modal( 'hide' );
										" >
							<?php
								esc_html_e( 'Mark as unread', 'booking' );
							?>
							</a>
							<a href="javascript:void(0)" class="button button-secondary" data-dismiss="modal">
							<?php
								esc_html_e( 'Cancel', 'booking' );
							?>
							</a>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</span>
		<?php
	}
}

// Loads hidden modal template.
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Listing_Actions__Set_As_Unread(), 'template_for_modal' ) );
