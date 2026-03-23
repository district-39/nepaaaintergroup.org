<?php
/**
 * Class to Payment Status Change - Option for "Actions Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Class WPBC_Action_Payment_Satatus
 */
class WPBC_Action_Payment_Satatus {

	const ACTION = 'set_payment_status';

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
					onclick=\"wpbc_boo_listing__click__set_payment_status( {{data['parsed_fields']['booking_id']}}, '{{data.parsed_fields.pay_status}}' );\"
				>" .
					esc_js( __( 'Edit payment status', 'booking' ) ) .
					" <# if ( ( '' === data.parsed_fields.pay_status ) || ( ! isNaN( parseFloat( data.parsed_fields.pay_status ) )) ) { #>" .
					"<i class='menu_icon icon-1x wpbc_icn_sell'></i>" .
					' <# } else { #>' .
					"<span class='hint_value_instead_icon'>{{data.parsed_fields.pay_status}}</span>" .
					' <# } #>' .
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

		$select_box_options = get_payment_status_titles();
		$select_box_options = array_flip( $select_box_options );
		$params_select      = array(
			'id'               => 'wpbc_modal__payment_status_edit__value',  // HTML ID  of element.
			'name'             => 'wpbc_modal__payment_status_edit__value',
			'label'            => '',                                        // Payment status // Label (optional).
			'style'            => '',                                        // CSS of select element.
			'class'            => '',                                        // CSS Class of select element.
			'multiple'         => false,
			'attr'             => array(
				'data-placeholder'   => __( 'Change status', 'booking' ),
				'ajx-selected-value' => '{{data.parsed_fields.pay_status}}',
			),
			'disabled'         => false,
			'disabled_options' => array(),                                   // If some options disabled, then it has to list here.
			'options'          => $select_box_options,

			/*
				//, 'value' => "{{data['parsed_fields']['pay_status']}}"//isset( $escaped_search_request_params[ $el_id ] ) ?  $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ] // Some Value from options array that selected by default
				//, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"       // JavaScript code
				//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"      // JavaScript code
			*/
		);
		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__payment_status_edit__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
							<?php
								esc_html_e( 'Update the booking payment status', 'booking' );
							?>
								<sup class="wpbc_modal__payment_status_edit__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<div class="modal-body">
							<?php
							wpbc_flex_select( $params_select );
							?>
							<label class="help-block">
							<?php
								esc_html_e( 'Select the payment status for booking', 'booking' );
							?>
							</label>
							<input type="hidden" id="wpbc_modal__payment_status_edit__booking_id" value=""/>
						</div>
						<div class="modal-footer">
							<a  id="wpbc_modal__payment_status_edit__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: 	wpbc_ajx_booking_ajax_action_request( {
											'booking_action'         : '<?php echo esc_js( self::ACTION ); ?>',
											'booking_id'             : jQuery( '#wpbc_modal__payment_status_edit__booking_id').val(),
											'selected_payment_status': jQuery( '#wpbc_modal__payment_status_edit__value' ).val(),
											'ui_clicked_element_id'  :  'wpbc_modal__payment_status_edit__button_send'
										} );
										wpbc_button_enable_loading_icon( this );
										jQuery( '#wpbc_modal__payment_status_edit__section' ).wpbc_my_modal( 'hide' );" >
							<?php
								esc_html_e( 'Save Changes', 'booking' );
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
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Action_Payment_Satatus(), 'template_for_modal' ) );
