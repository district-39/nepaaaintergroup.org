<?php
/**
 * Class to Export CSV -- Option for "Bulk Actions - Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript: in    ../includes/page-bookings/_src/bookings__actions.js  ->   function wpbc_ajx_booking__ui_click__export_csv() { ... } .

/**
 * Class WPBC_Listing_Actions__Export_CSV
 */
class WPBC_Listing_Actions__Export_CSV{

	const ACTION = 'export_csv';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_option() {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return false;
		}
		if ( ! wpbc_is_user_can( self::ACTION, wpbc_get_current_user_id() ) ) {
			return false;
		}

		$css_class  = 'ul_dropdown_menu_li_action ';
		$css_class .= 'ul_dropdown_menu_li_action_' . self::ACTION;

		// Option Title.
		$html = "<a href=\"javascript:void(0)\"  class=\"" . esc_attr( $css_class ) . "\" 
					onclick=\"if ( 'function' === typeof( jQuery('#wpbc_modal__export_csv__section').wpbc_my_modal ) ) {							
							jQuery('#wpbc_modal__export_csv__section').wpbc_my_modal('show');
						} else {
							alert( 'Warning! wpbc_my_modal module has not found. Please, recheck about any conflicts by deactivating other plugins.');
						}\"
					title=\"" . esc_attr( __( 'Export bookings to CSV format', 'booking' ) ) . "\"
				 >" .
					esc_js( __( 'Export to CSV', 'booking' ) ) .
					' ' .
					' <i class="menu_icon icon-1x wpbc-bi-filetype-csv"></i>'.
					// "<span class='hint_value_instead_icon'>{{{data['parsed_fields']['currency_symbol']}}} {{data['parsed_fields']['cost']}}</span>" .
				'</a>';

		return $html;

		// onclick=\"wpbc_ajx_booking_send_search_request_with_params({ '" . esc_attr( self::ID ) . "': '{$option_value}' }); \"  //.
	}


	/**
	 * Get Template for Modal Window
	 *
	 * @return false|void
	 */
	public static function template_for_modal() {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$user_id = ( isset( $_REQUEST['wpbc_ajx_user_id'] ) ) ? intval( $_REQUEST['wpbc_ajx_user_id'] ) : wpbc_get_current_user_id();

		$booking_csv_export_params = get_user_option( 'booking_csv_export_params', (int) $user_id );
		$booking_csv_export_params = ( ! empty( $booking_csv_export_params ) ) ? $booking_csv_export_params : array();
		$defaults                  = array(
			'export_type'            => 'csv_all',
			'selected_id'            => '',
			'csv_export_separator'   => 'semicolon',
			'csv_export_skip_fields' => '',
		);
		$export_params_arr         = wp_parse_args( $booking_csv_export_params, $defaults );

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__export_csv__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
							<?php
								esc_html_e( 'CSV Export', 'booking' );
							?>
								<sup class="wpbc_modal__export_csv__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<div class="modal-body">
							<table class="form-table"><tbody>
							<?php
							$default_options_values = wpbc_get_default_options();

							$field_options = array(
								'semicolon' => '; - ' . __( 'semicolon', 'booking' ),
								'comma'     => ', - ' . __( 'comma', 'booking' ),
							);
							$fields        = array(
								'type'             => 'select',
								'value'            => $export_params_arr['csv_export_separator'],
								'title'            => __( 'CSV data separator', 'booking' ),
								'description'      => '',
								'options'          => $field_options,
								'label'            => '',
								'disabled'         => false,
								'disabled_options' => array(),
								'multiple'         => false,
								'description_tag'  => 'p',
								'tr_class'         => '',
								'class'            => '',
								'css'              => '',
								'only_field'       => false,
								'attr'             => array(),
							);
							WPBC_Settings_API::field_select_row_static( 'wpbc_field_booking_csv_export_separator', $fields );


							$field_options = array(
								'csv_page' => __( 'Current page', 'booking' ),
								'csv_all'  => __( 'All pages', 'booking' ),
							);
							$fields        = array(
								'type'             => 'select',
								'value'            => $export_params_arr['export_type'],
								'title'            => __( 'Export pages', 'booking' ),
								'description'      => '',
								'options'          => $field_options,
								'label'            => '',
								'disabled'         => false,
								'disabled_options' => array(),
								'multiple'         => false,
								'description_tag'  => 'p',
								'tr_class'         => '',
								'class'            => '',
								'css'              => '',
								'only_field'       => false,
								'attr'             => array(),
							);
							WPBC_Settings_API::field_select_row_static( 'wpbc_field_booking_csv_export_type', $fields );

							?>
							</tbody></table>
							<textarea id="wpbc_field_booking_csv_export_skip_fields"
									name="wpbc_field_booking_csv_export_skip_fields"
									style="width:100%;" cols="87" rows="2"
									placeholder="<?php echo 'trash,is_new,secondname'; ?>"
							><?php
								echo esc_textarea( $export_params_arr['csv_export_skip_fields'] );
							?></textarea>
							<label class="help-block">
								<?php
								/* translators: 1: ... */
								echo wp_kses_post( sprintf( __( 'Enter field names separated by commas to %1$sskip the export%2$s', 'booking' ), '<b>', '</b>' ) );
								?>
							</label>
							<input type="hidden" id="wpbc_modal__export_csv__booking_id" value="" />
						</div>
						<div class="modal-footer">
							<a id="wpbc_modal__export_csv__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: wpbc_ajx_booking__ui_click__export_csv( {
																	'booking_action'       	: '<?php echo esc_js( self::ACTION ); ?>',
																	'ui_clicked_element_id'	: 'wpbc_modal__export_csv__button_send',
																	'export_type'			: jQuery('#wpbc_field_booking_csv_export_type option:selected').val(),
																	'csv_export_separator'	: jQuery('#wpbc_field_booking_csv_export_separator option:selected').val(),
																	'csv_export_skip_fields': jQuery('#wpbc_field_booking_csv_export_skip_fields').val()
															} );"
							>
								<?php
									esc_html_e( 'Export', 'booking' );
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

// Loads hidden template.
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Listing_Actions__Export_CSV(), 'template_for_modal' ) );