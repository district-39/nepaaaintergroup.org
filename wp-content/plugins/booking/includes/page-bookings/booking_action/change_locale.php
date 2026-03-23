<?php
/**
 * Class Change Locale of the Booking - Option for "Actions Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Class WPBC_Action_Change_Locale
 */
class WPBC_Action_Change_Locale {

	const ACTION = 'set_booking_locale';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_button() {

		if ( ! wpbc_is_user_can( self::ACTION, wpbc_get_current_user_id() ) ) {
			return false;
		}

		// Change booking resource.
		$html = "<#
				var selected_locale_value = '';
				if (
					   ( 'undefined' !== typeof( data.parsed_fields.booking_options) )
					&& ( 'undefined' !== typeof( data.parsed_fields.booking_options.booking_meta_locale ) )
				){
					var selected_locale_value = data.parsed_fields.booking_options.booking_meta_locale;
				}
			#><a class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . esc_js( self::ACTION ) . "\" 
					href=\"javascript:void(0)\" 
					onclick=\"wpbc_boo_listing__click__set_booking_locale( {{data['parsed_fields']['booking_id']}}, '{{selected_locale_value}}' );\"
				>" .
					esc_js( __( 'Change language', 'booking' ) ) .
					" <# if ( '' === selected_locale_value ) { #>" .
					"<i class='menu_icon icon-1x wpbc_icn_language'></i>" .
					' <# } else { #>' .
					"<span class='hint_value_instead_icon'>{{selected_locale_value}}</span>" .
					' <# } #>' .
				'</a>';

		return $html;
	}


	/**
	 * Get Template for Modal Window -  Booking Change Locale - Modal Window structure
	 *
	 * @return false|void
	 */
	public static function template_for_modal() {

		$el_id = 'wpbc_modal__set_booking_locale';

		$availbale_locales_in_system = get_available_languages();
		$select_box_options          = array();
		$select_box_options['']      = __( 'Default Locale', 'booking' );
		$select_box_options['en_US'] = 'English (United States)';

		foreach ( $availbale_locales_in_system as $locale ) {
			$select_box_options[ $locale ] = $locale;
		}
		$params_select = array(
			'id'               => $el_id,
			'name'             => $el_id,
			'label'            => '',
			'style'            => '',
			'class'            => 'set_booking_locale_selectbox',
			'disabled'         => false,
			'disabled_options' => array(),
			'options'          => $select_box_options,
		);

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__set_booking_locale__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
								<?php
									esc_html_e( 'Change locale', 'booking' );
								?>
								<sup class="wpbc_modal__set_booking_locale__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<div class="modal-body">
							<?php
							wpbc_flex_select( $params_select );
							?>
							<label class="help-block">
							<?php
								esc_html_e( 'Select locale for this booking', 'booking' );
							?>
							</label>
							<input type="hidden" id="wpbc_modal__set_booking_locale__booking_id" value=""/>
						</div>
						<div class="modal-footer">
							<a  id="wpbc_modal__set_booking_locale__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: 	wpbc_ajx_booking_ajax_action_request( {
											'booking_action'       : '<?php echo esc_js( self::ACTION ); ?>',
											'booking_id'           : jQuery( '#wpbc_modal__set_booking_locale__booking_id' ).val(),
											'booking_meta_locale'  : jQuery( '#wpbc_modal__set_booking_locale' ).val(),
											'ui_clicked_element_id': 'wpbc_modal__set_booking_locale__button_send'
									} );
									wpbc_button_enable_loading_icon( this );
									jQuery( '#wpbc_modal__set_booking_locale__section' ).wpbc_my_modal( 'hide' );" >
							<?php
								esc_html_e( 'Change locale', 'booking' );
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
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Action_Change_Locale(), 'template_for_modal' ) );
