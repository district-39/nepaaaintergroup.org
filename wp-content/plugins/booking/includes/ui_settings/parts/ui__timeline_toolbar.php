<?php
/**
 * Admin Panel UI - Timeline Toolbar
 *
 * @version  1.0
 * @package  Any
 * @category Page Structure in Admin Panel
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2025-03-22
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}


/**
 * Show "Send Emails" toggle at  the right side of Top Horizontal Navigation  Bar
 *
 * @param string $active_page_tag - such as 'wpbc'.
 * @param string $active_tab_tab  - such  as 'vm_calendar'.
 *
 * @return void
 */
function wpbc_timeline_toolbar_show_send_emails_btn( $active_page_tag, $active_tab_tab ) {

	if ( ( 'wpbc' === $active_page_tag ) && ( 'vm_calendar' === $active_tab_tab ) ) {

		?>
		<div class="wpbc_ajx_toolbar wpbc_no_borders wpbc_not_toolbar_is_send_emails" style="margin-left: auto;">
			<div class="ui_container ui_container_micro">
				<div class="ui_group">
					<div class="ui_element">
						<?php
						       wpbc_timeline__is_send_emails_toggle();
							   // wpbc_toolbar_is_send_emails_btn('');
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
add_action( 'hook__wpbc_ui__top_horisontal_nav__end', 'wpbc_timeline_toolbar_show_send_emails_btn', 10, 2 );


/**
 * Toggle "Emails Sending".
 *
 * @return void
 */
function wpbc_timeline__is_send_emails_toggle(){

 	$el_id = 'is_send_email_for_pending';

	$el_value = ( get_bk_option( 'booking_send_emails_off_addbooking' ) !== 'On' ) ? 'On' : 'Off';

	$params_checkbox = array(
							  'id'       => $el_id 		// HTML ID  of element
							, 'name'     => $el_id
							, 'label'    => array( 'title' => __( 'Emails sending', 'booking' ) , 'position' => 'right' )           // FixIn: 9.6.1.5.
							, 'style'    => '' 					// CSS of select element
							, 'class'    => '' 					// CSS Class of select element
							, 'disabled' => false
							, 'attr'     => array() 							// Any  additional attributes, if this radio | checkbox element
							, 'legend'   => ''									// aria-label parameter
							, 'value'    => $el_value 							// Some Value from optins array that selected by default
							, 'selected' => ( ( 'On' == $el_value ) ? true : false )		// Selected or not
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
							//, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( {'ui_usr__send_emails': (jQuery( this ).is(':checked') ? 'send' : 'not_send') } );"					// JavaScript code
							, 'hint' 	=> array( 'title' => __('Send email notification to customer about this operation' ,'booking') , 'position' => 'top' )
						);

	wpbc_flex_toggle( $params_checkbox );
}

/**
 * Show Resource selection choosen in Timeline View.
 *
 * @return void
 */
function wpbc_ui__timeline__resource_selection() {

	if ( ! class_exists( 'wpdev_bk_personal' ) ) {
		return;
	}

	wpbc_ui__choosen__resource_selection();

	$bk_admin_url  = wpbc_get_params_in_url( wpbc_get_bookings_url( false ), array( 'wh_booking_type' ) );
	$bk_admin_url .= '&wh_booking_type=';

	?>
	<script type="text/javascript">
		function wpbc_timeline_view__choozen__remove_all_options(selectbox_id) {
			jQuery( selectbox_id + ' option' ).removeAttr( 'selected' );    	// Disable selection in the real selectbox.
			jQuery( selectbox_id ).trigger( 'chosen:updated' );            		// Remove all fields from the Choozen field	// FixIn: 8.7.9.9.
		}
		function wpbc_timeline_view__reload_page__for_selected_resources() {
			var resource_value = jQuery( '#wh_booking_type' ).val();
			if ( resource_value == null ) {
				resource_value = jQuery( "#wh_booking_type option:first" ).val();
			}
			if ( resource_value == null ) {
				resource_value = '';
			}
			// FixIn: 7.2.1.18 - Fix "Request-URI Too Long" fatal error.
			if ( resource_value instanceof Array ) {
				jQuery.each( resource_value, function (r_index, r_value) {
					r_value = String( r_value );
					if ( r_value.length > 200 ) {
						resource_value[r_index] = '';
					}
				} );
			}
			window.location.assign( "<?php echo $bk_admin_url; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>" + resource_value );
		}
	</script>
	<?php
}


/**
 * Get data for Resource Selection elemnt - [C H O O S E N]
 *
 * @return void
 */
function wpbc_ui__choosen__resource_selection() {

	$resources_arr = wpbc_get_br_as_objects();

	$all_ids = array();
	foreach ( $resources_arr as $resource_obj ) {
		$all_ids[] = $resource_obj->id;
	}

	$selector_arr = array();
	if ( count( $all_ids ) > 1 ) {
		$selector_arr[ __( 'All resources', 'booking' ) ] = implode( ',', $all_ids );
	}

	foreach ( $resources_arr as $resource_obj ) {

		$resource_title = $resource_obj->title;

		if ( ( isset( $resource_obj->parent ) ) && ( intval( $resource_obj->parent ) !== 0 ) ) {
			$resource_title = '&nbsp;&nbsp;&nbsp;' . $resource_title;
		}

		$selector_arr[ $resource_title ] = $resource_obj->id;
	}


	$selector_id          = 'wh_booking_type';
	$text_empty_selection = __( 'Booking resources', 'booking' );
	wpbc_ui__choosen( $selector_id, $selector_arr, $text_empty_selection );
}


/**
 *  R e s o u r c e s    S e l e c t o r    [C H O O S E N].
 *
 * @param string $selector_id           - 'wh_booking_type' - HTML ID of element.
 * @param array  $selector_arr          - array( 'Title' => Value ).
 * @param string $text_empty_selection  - 'Booking resources'.
 */
function wpbc_ui__choosen( $selector_id, $selector_arr, $text_empty_selection ) {

	$wpdevbk_value       = '';
	$wpdevbk_value_array = array();

	if ( isset( $_REQUEST[ $selector_id ] ) ) {                                            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		$wpdevbk_value = sanitize_text_field( wp_unslash( $_REQUEST[ $selector_id ] ) );   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing

		if ( strpos( $wpdevbk_value, ',' ) !== false ) {
			$wpdevbk_value_array = explode( ',', $wpdevbk_value );
		}
	}

	$wpdevbk_selector_default = array_search( $wpdevbk_value, $selector_arr, true );
	if ( false === $wpdevbk_selector_default ) {
		$wpdevbk_selector_default = current( $selector_arr );
	}

	?>
	<div class="wpbc_ui_el wpbc_ui_el__choosen wpbc_ui_el__choosen_<?php echo esc_attr( $selector_id ); ?>">
		<select multiple="multiple" class="chzn-select wpbc_ui_el__choosen_select" style="display:none;"
				id="<?php echo esc_attr( $selector_id ); ?>"
				name="<?php echo esc_attr( $selector_id ); ?>[]"
				data-placeholder="<?php echo esc_attr( $text_empty_selection ); ?>">
			<?php
			$is_all_resources_selected = false;
			foreach ( $selector_arr as $key => $value ) {
				if ( 'divider' !== $value ) {
					$is_in_array = in_array( $value, $wpdevbk_value_array, true );
					?>
					<option <?php
					if ( ( ( $wpdevbk_value === $value ) || ( $is_in_array ) ) && ( ! $is_all_resources_selected ) ) {
						echo ' selected="SELECTED" ';
						if ( strpos( $value, ',' ) !== false ) {
							$is_all_resources_selected = true;
						}
					}
					if ( ( strpos( $key, '&nbsp;' ) === false ) || ( __( 'All resources', 'booking' ) === $key ) ) {
						echo ' style="font-weight:600;" ';
					}
					?>
					value="<?php echo esc_attr( $value ); ?>"
					title="<?php echo esc_attr( $key ); ?>"><?php
						echo esc_html( substr( $key, 0, 80 ) . ( ( strlen( $key ) > 80 ) ? '...' : '' ) );                    // FixIn: 9.1.2.3.
					?></option><?php
				}
			}
			?>
		</select>
		<div class="wpbc_ui_el__choosen_reset_buttons">
			<input type="hidden" name="blank_field__this_field_only_for_formatting_buttons" value="">
			<a data-original-title="<?php esc_attr_e( 'Clear booking resources selection', 'booking' ); ?>"
			   rel="tooltip"
			   class="tooltip_top my_class4"
			   onclick="javascript:wpbc_timeline_view__choozen__remove_all_options('#<?php echo esc_attr( $selector_id ); ?>');"
			><i class="wpbc_icn_close"></i></a>
			<a data-original-title="<?php esc_attr_e( 'Apply booking resources selection', 'booking' ); ?>"
			   rel="tooltip"
			   class="tooltip_top my_class5"
			   onclick="javascript:wpbc_timeline_view__reload_page__for_selected_resources();"
			><i class="wpbc_icn_refresh"></i></a>
		</div>
		</div>
		<script type="text/javascript">
			<?php
			echo wpbc_jq_ready_start();                                                                                 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			if ( 'function' === typeof( jQuery("#<?php echo esc_attr( $selector_id ); ?>").chosen ) ) {

				jQuery( "#<?php echo esc_attr( $selector_id ); ?>" ).chosen( { no_results_text: "No results matched" } );
				if ( jQuery( '#<?php echo esc_attr( $selector_id ); ?>_chosen' ).length ) {
					jQuery( '.wpbc_ui_el__choosen_<?php echo esc_attr( $selector_id ); ?> .wpbc_ui_el__choosen_reset_buttons').css('display','flex');
					jQuery( "#<?php echo esc_attr( $selector_id ); ?>_chosen" ).attr( "tabindex", "0" );
				}
				// Catch any selections in the Choozen.
				jQuery( "#<?php echo esc_attr( $selector_id ); ?>" ).chosen().on( 'change', function (va) {
					if ( jQuery( "#<?php echo esc_attr( $selector_id ); ?>" ).val() != null ) {
						// So we are having aready values.
						jQuery.each( jQuery( "#<?php echo esc_attr( $selector_id ); ?>" ).val(), function (index, value) {
							if ( value.indexOf( ',' ) > 0 ) {                                                                             // Ok we are have array with  all booking resources ID.
								jQuery( '#<?php echo esc_attr( $selector_id ); ?>' + ' option' ).removeAttr( 'selected' );                // Disable selection in the real selectbox.
								jQuery( '#<?php echo esc_attr( $selector_id ); ?>' + ' option:first-child' ).prop( "selected", true );    // Disable selection in the real selectbox.
								jQuery( '#<?php echo esc_attr( $selector_id ); ?>' ).trigger( 'liszt:updated' );                          // Update all fields from the Choozen field.
								var my_message = '<?php echo esc_js( __( 'Please note, its not possible to add new resources, if "All resources" option is selected. Please clear the selection, then add new resources.', 'booking' ) ); ?>';
								wpbc_admin_show_message( my_message, 'warning', 10000 );
							}
						} );
					}
				} );
			} else {
				alert( 'WPBC Error. JavaScript library "chosen" was not defined.' );
			}
			<?php
			echo wpbc_jq_ready_end();                                                                                   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
        </script>
        <?php
    }
