<?php
/**
 * Class to Payment Request - Option for "Actions Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Class WPBC_Action_Payment_Satatus
 */
class WPBC_Action_Payment_Request {

	const ACTION = 'send_payment_request';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_button() {

		if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
			return false;
		}
		if ( ! wpbc_is_user_can( self::ACTION, wpbc_get_current_user_id() ) ) {
			return false;
		}

		// Send payment request to visitor.
		$html = "<a class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . esc_js( self::ACTION ) . "\" 
					href=\"javascript:void(0)\" 
					onclick=\"wpbc_boo_listing__click__send_payment_request( {{data['parsed_fields']['booking_id']}}, '{{data.parsed_fields.visitorbookingpayurl}}', '{{{data.parsed_fields.currency_symbol}}}{{data.parsed_fields.cost}}' );\"
				>" .
					esc_js( __( 'Charge with pay link', 'booking' ) ) .
					"<i class='menu_icon icon-1x wpbc_icn_forward_to_inbox'></i>" .
				'</a>';

		return $html;
	}


	/**
	 * Get Template for Modal Window -  Booking Cost Edit - Layout - Modal Window structure
	 *
	 * @return false|void
	 */
	public static function template_for_modal() {

		if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
			return false;
		}

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__send_payment_request__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
							<?php
							esc_html_e( 'Payment request', 'booking' );
							?>
							<sup class="wpbc_modal__send_payment_request__booking_id wpbc_modal__booking_id__in_title"></sup>
							<span class="wpbc_modal__send_payment_request__cost"></span>
							</h4>
						</div>
						<div class="modal-body">
							<div class="wpbc_modal__2_fields">
								<input type="text" id="wpbc_modal__send_payment_request__url" value="">
									<a 	href="javascript:void(0)"
										onclick="javascript: wpbc_copy_text_to_clipbrd_from_element('wpbc_modal__send_payment_request__url');"
										class="button button-secondary wpbc_modal__2_fields_button"
										title="<?php esc_attr_e( 'Copy', 'booking' ); ?>"
									><i class="menu_icon icon-1x wpbc_icn_content_copy"></i></a>
							</div>
							<label class="help-block">
								<?php
									esc_html_e( 'Payment link URL', 'booking' );
								?>
							</label>
							<textarea id="wpbc_modal__send_payment_request__value" name="wpbc_modal__send_payment_request__value" cols="87" rows="5"
									  placeholder="<?php
//									  esc_attr_e( 'Optional', 'booking' );
//									  echo '. ';
//									  /* translators: 1: ... */
//									  echo esc_attr( sprintf( __( 'Type your %1$sreason for payment%2$s request', 'booking' ),'','') );
//									  echo '.';
									?>"
							></textarea>
							<input type="hidden" id="wpbc_modal__send_payment_request__booking_id" value=""/>
							<!--p class="help-block">
							<?php
								/* translators: 1: ... */
								echo wp_kses_post( sprintf( __( 'To send a %1$spayment request%2$s, either click send email or copy the link and share it elsewhere', 'booking' ), '<a href="' . esc_url( wpbc_get_settings_url( true, false ) . '&tab=email&subtab=payment_request' ) . '">', '</a>', '<b>', '</b>' ) );
							?>
							</p-->
							<label class="help-block">
							<?php
								/* translators: 1: ... */
								echo wp_kses_post( sprintf( __( 'Type your %1$sreason for payment%2$s request', 'booking' ), '<b>', ',</b>' ) );
							?>
							</label>
						</div>
						<div class="modal-footer">
							<a  id="wpbc_modal__send_payment_request__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: 	wpbc_ajx_booking_ajax_action_request( {
											'booking_action'         : '<?php echo esc_js( self::ACTION ); ?>',
											'booking_id'             : jQuery( '#wpbc_modal__send_payment_request__booking_id').val(),
											'reason_of_action'       : jQuery( '#wpbc_modal__send_payment_request__value' ).val(),
											'ui_clicked_element_id'  :  'wpbc_modal__send_payment_request__button_send'
										} );
										wpbc_button_enable_loading_icon( this );
										jQuery( '#wpbc_modal__send_payment_request__section' ).wpbc_my_modal( 'hide' );
									" >
							<?php
								esc_html_e( 'Send Email', 'booking' );
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
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Action_Payment_Request(), 'template_for_modal' ) );
