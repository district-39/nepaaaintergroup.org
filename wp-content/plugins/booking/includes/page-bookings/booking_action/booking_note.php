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
class WPBC_Action_Booking_Note {

	const ACTION = 'set_booking_note';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_button() {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return false;
		}
		if ( ! wpbc_is_user_can( self::ACTION, wpbc_get_current_user_id() ) ) {
			return false;
		}

		// Send payment request to visitor.
		$html = "<a class='ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . esc_js( self::ACTION ) . "' 
					href=\"javascript:void(0)\" 
					onclick=\"wpbc_boo_listing__click__set_booking_note( {{data['parsed_fields']['booking_id']}}, jQuery( '#set_booking_note_text_{{data.parsed_fields.booking_id}}').val() );\"
				>" .
					'<# if ( \'\' == data.parsed_fields.remark.trim() ) { #>' .
						esc_js( __( 'Add notes', 'booking' ) ) .
					'<# } else { #>' .
						esc_js( __( 'Edit note', 'booking' ) ) .
					'<# } #>' .
					"<i class='menu_icon icon-1x wpbc_icn_mode_comment'></i>" .
				'</a>';

		return $html;
	}


	/**
	 * Get Template for Modal Window -  Booking Cost Edit - Layout - Modal Window structure
	 *
	 * @return false|void
	 */
	public static function template_for_modal() {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return false;
		}

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__set_booking_note__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title"><?php esc_html_e( 'Edit note', 'booking' ); ?><sup class="wpbc_modal__set_booking_note__booking_id wpbc_modal__booking_id__in_title"></sup></h4>
						</div>
						<div class="modal-body">
							<textarea id="wpbc_modal__set_booking_note__value" name="wpbc_modal__set_booking_note__value" cols="87" rows="5"></textarea>
							<input type="hidden" id="wpbc_modal__set_booking_note__booking_id" value=""/>
							<label class="help-block"><?php esc_html_e( 'Add notes for the booking', 'booking' ); ?></label>
						</div>
						<div class="modal-footer">
							<a  id="wpbc_modal__set_booking_note__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: wpbc_ajx_booking_ajax_action_request( {
											'booking_action'         : '<?php echo esc_js( self::ACTION ); ?>',
											'booking_id'             : jQuery( '#wpbc_modal__set_booking_note__booking_id').val(),
											'remark'                 : jQuery( '#wpbc_modal__set_booking_note__value' ).val(),
											'ui_clicked_element_id'  :  'wpbc_modal__set_booking_note__button_send'
										} );
										wpbc_button_enable_loading_icon( this );
										jQuery( '#wpbc_modal__set_booking_note__section' ).wpbc_my_modal( 'hide' );
										"
							><?php esc_html_e( 'Save Changes', 'booking' ); ?> </a>
							<a href="javascript:void(0)" class="button button-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', 'booking' ); ?> </a>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</span>
		<?php
	}
}

// Loads hidden modal template.
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Action_Booking_Note(), 'template_for_modal' ) );
