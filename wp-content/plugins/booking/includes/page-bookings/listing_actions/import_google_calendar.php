<?php
/**
 * Class to Import_Google_Calendar -- Option for "Bulk Actions - Dropdown Menu"
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript: in    ../includes/page-bookings/_src/bookings__actions.js  ->   function wpbc_ajx_booking__ui_click__import_google_calendar() { ... } .

/**
 * Class WPBC_Listing_Actions__Import_Google_Calendar
 */
class WPBC_Listing_Actions__Import_Google_Calendar{

	const ACTION = 'import_google_calendar';

	/**
	 * Get Action Option
	 *
	 * @return false|string
	 */
	public static function get_option() {

		if ( ! wpbc_is_user_can( self::ACTION, wpbc_get_current_user_id() ) ) {
			return false;
		}

		$booking_gcal_feed    = get_bk_option( 'booking_gcal_feed' );
		$is_this_btn_disabled = ( ( ! class_exists( 'wpdev_bk_personal' ) ) && ( empty( $booking_gcal_feed ) ) ) ? true : false;


		$css_class  = 'ul_dropdown_menu_li_action ';
		$css_class .= 'ul_dropdown_menu_li_action_' . self::ACTION;

		// Option Title.
		$html = "<a href=\"javascript:void(0)\"  class=\"" . esc_attr( $css_class ) . "\" 
					onclick=\"if ( 'function' === typeof( jQuery('#wpbc_modal__import_google_calendar__section').wpbc_my_modal ) ) {
								jQuery('#wpbc_modal__import_google_calendar__section').wpbc_my_modal('show');
							} else {
								alert( 'Warning! wpbc_my_modal module has not found. Please, recheck about any conflicts by deactivating other plugins.');
							}\"
					title=\"" . esc_attr( __( 'Import Google Calendar Events', 'booking' ) ) . "\"
				 >" .
					esc_js( __( 'Import Google Calendar', 'booking' ) ) .
					' ' .
					' <i class="menu_icon icon-1x wpbc-bi-google"></i>'.
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


		$booking_gcal_feed    = get_bk_option( 'booking_gcal_feed' );
		$is_this_btn_disabled = false;

		if ( ( ! class_exists( 'wpdev_bk_personal' ) ) && ( empty( $booking_gcal_feed ) ) ) {
			$is_this_btn_disabled = true;
			$settigns_link        = wpbc_get_settings_url() . '&tab=sync&subtab=gcal';
		} else {
			$booking_gcal_events_from             = get_bk_option( 'booking_gcal_events_from' );
			$booking_gcal_events_from_offset      = get_bk_option( 'booking_gcal_events_from_offset' );
			$booking_gcal_events_from_offset_type = get_bk_option( 'booking_gcal_events_from_offset_type' );

			$booking_gcal_events_until             = get_bk_option( 'booking_gcal_events_until' );
			$booking_gcal_events_until_offset      = get_bk_option( 'booking_gcal_events_until_offset' );
			$booking_gcal_events_until_offset_type = get_bk_option( 'booking_gcal_events_until_offset_type' );

			$booking_gcal_events_max = get_bk_option( 'booking_gcal_events_max' );
			// $booking_gcal_timezone = get_bk_option( 'booking_gcal_timezone'); .
		}

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__import_google_calendar__section" class="wpbc_modal__import_google_calendar__section modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
							<?php
							if ( $is_this_btn_disabled ) {
								esc_html_e( 'Warning!', 'booking' );
							} else {
								esc_html_e( 'Retrieve Google Calendar Events ', 'booking' );
							}
							?>
								<sup class="wpbc_modal__import_google_calendar__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<div class="modal-body">
							<?php
							if ( $is_this_btn_disabled ) {
								?>
								<label class="help-block" style="display:block;">
								<?php
									esc_html_e( 'Please configure settings for import Google Calendar events', 'booking' );
									echo '<a href="' . esc_url( $settigns_link ) . '">' . esc_html__( 'here', 'booking' ) . '</a>';
								?>
								</label>
								<?php
							} else {
								?>
								<table class="visibility_gcal_feeds_settings form-table0 settings-table0 table">
									<tbody>
									<?php
									if ( function_exists( 'wpbc_gcal_settings_content_field_selection_booking_resources' ) ) {
										wpbc_gcal_settings_content_field_selection_booking_resources();
									} else {
										?>
										<input type="hidden" name="wpbc_booking_resource" id="wpbc_booking_resource" value=""/>
										<?php
									}
									wpbc_gcal_settings_content_field_from( $booking_gcal_events_from, $booking_gcal_events_from_offset, $booking_gcal_events_from_offset_type );
									wpbc_gcal_settings_content_field_until( $booking_gcal_events_until, $booking_gcal_events_until_offset, $booking_gcal_events_until_offset_type );
									wpbc_gcal_settings_content_field_max_feeds( $booking_gcal_events_max );
									?>
									</tbody>
								</table>
								<?php
							}
							?>
						</div>
						<div class="modal-footer">
							<?php
							if ( $is_this_btn_disabled ) {
								echo '<a href="' . esc_url( $settigns_link ) . '" class="button button-primary">' . esc_html__( 'Configure', 'booking' ) . '</a>';
							} else {
								?>
								<a id="wpbc_modal__import_google_calendar__button_send" class="button button-primary"
									href="javascript:void(0);"
									onclick="javascript: 	wpbc_ajx_booking__ui_click__import_google_calendar();
															jQuery('#wpbc_modal__import_google_calendar__section').wpbc_my_modal('hide');
											"
								>
									<?php
										esc_html_e( 'Import Google Calendar Events', 'booking' );
									?>
								</a>
							<?php } ?>
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
add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Listing_Actions__Import_Google_Calendar(), 'template_for_modal' ) );