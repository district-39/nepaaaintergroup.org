<?php
/**
 * Class Change Resource of the Booking - Option for "Actions Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Class WPBC_Action_Change_Resource
 */
class WPBC_Action_Change_Resource {

	const ACTION = 'change_booking_resource';

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

		// Change booking resource.
		$html = "<a class='ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . esc_js( self::ACTION ) . "' 
					href=\"javascript:void(0)\" 
					onclick=\"wpbc_boo_listing__click__change_booking_resource( {{data['parsed_fields']['booking_id']}}, {{data['parsed_fields']['resource_id']}} );\"
				>" .
					esc_js( __( 'Change resource', 'booking' ) ) .
					"<i class='menu_icon icon-1x wpbc_icn_shuffle'></i>" .
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
			<div id="wpbc_modal__change_booking_resource__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
								<?php
									esc_html_e( 'Change resource', 'booking' );
								?>
								<sup class="wpbc_modal__change_booking_resource__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<div class="modal-body">
							<div id="section_in_in_modal__change_booking_resource"></div>
							<label class="help-block">
							<?php
								esc_html_e( 'Select the new booking resource for booking', 'booking' );
							?>
							</label>
							<input type="hidden" id="wpbc_modal__change_booking_resource__booking_id" value=""/>
						</div>
						<div class="modal-footer">
							<a  id="wpbc_modal__change_booking_resource__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: 	wpbc_ajx_booking_ajax_action_request( {
											'booking_action'       : '<?php echo esc_js( self::ACTION ); ?>',
											'booking_id'           : jQuery( '#wpbc_modal__change_booking_resource__booking_id' ).val(),
											'selected_resource_id' : jQuery( '#wpbc_modal__change_booking_resource__resource_id' ).val(),
											'ui_clicked_element_id': 'wpbc_modal__change_booking_resource__button_send'
									} );
									wpbc_button_enable_loading_icon( this );
									jQuery( '#wpbc_modal__change_booking_resource__section' ).wpbc_my_modal( 'hide' );" >
							<?php
								esc_html_e( 'Change resource', 'booking' );
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



	/**
	 * Get Template for Modal Window -  Booking Cost Edit - Layout - Modal Window structure
	 *
	 * @return false|void
	 */
	public static function hidden_template() {

		// Template.
		?>
		<script type="text/html" id="tmpl-wpbc_ajx__modal__change_booking_resource">
			<select   id="wpbc_modal__change_booking_resource__resource_id"
					name="wpbc_modal__change_booking_resource__resource_id"
					class="wpbc_ui_control wpbc_ui_select change_booking_resource_selectbox">
				<#
				_.each( data.ajx_booking_resources, function ( p_resource, p_resource_id, p_data ){
					#><option value="{{p_resource.booking_type_id}}" style="<#
										if( undefined != p_resource.parent ) {
											if( '0' == p_resource.parent ) {
												#>font-weight:600;<#
											} else {
												#>font-size:0.95em;padding-left:20px;<#
											}
										}
									#>"
					><#
						if( undefined != p_resource.parent ) {
							if( '0' != p_resource.parent ) {
								#>&nbsp;&nbsp;&nbsp;<#
							}
						}
					#>{{p_resource.title}}</option><#
				});
				#>
			</select>
		</script>
		<?php
	}
}

// Loads hidden modal template.
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Action_Change_Resource(), 'template_for_modal' ) );

// Loads hidden template.
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Action_Change_Resource(), 'hidden_template' ) );
