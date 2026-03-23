<?php
/**
 * Class Resource_Choosen
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Class WPBC_Listing_Actions__Resource_Choosen
 */
class WPBC_Listing_Actions__Resource_Choosen{

	const ACTION = 'resource_choosen';

	/**
	 * Get element
	 *
	 * @return false|string
	 */
	public static function get_element_html( $escaped_search_request_params, $defaults ) {

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			return '';
		}

		ob_start();


		$select_box_options = self::get_booking_resources_arr();

		$el_id         = 'wh_booking_type';
		$params_select = array(
			'id'               => $el_id,                        // HTML ID  of element.
			'name'             => $el_id,
			'label'            => '',
			'style'            => 'display:none;',               // CSS of select element.
			'class'            => 'chzn-select wpbc_ui_el__choosen_select',                 // CSS Class of select element.
			'multiple'         => true,
			'attr'             => array( 'data-placeholder' => __( 'Select booking resources', 'booking' ) ),  // Any additional attributes, if this radio | checkbox element.
			'disabled'         => false,
			'disabled_options' => array(),                       // If some options disabled, then it has to list here.
			'options'          => $select_box_options,
			'value'            => isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ],  // Some Value from options array that selected by default.
			// 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );",       // JavaScript code.
			// 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val(), 'in element:' , jQuery( this ) );",      // JavaScript code.
		);

		?><div class="wpbc_ui_el wpbc_ui_el__choosen wpbc_ui_el__choosen_<?php echo esc_attr( $el_id ); ?>"><?php
			wpbc_flex_select( $params_select );
			?>
			<div class="wpbc_ui_el__choosen_reset_buttons">
				<input type="hidden" name="blank_field__this_field_only_for_formatting_buttons" value="">
				<a data-original-title="<?php esc_attr_e( 'Clear booking resources selection', 'booking' ); ?>"
				   rel="tooltip"
				   class="tooltip_top my_class4"
				   onclick="javascript:wpbc_bo_listing__choozen__remove_all_options('#<?php echo esc_attr( $el_id ); ?>');"
				><i class="wpbc_icn_close"></i></a>
			</div>
			<?php

		?></div><?php

		self::js_for_choosen( $el_id  );

		$html = ob_get_clean();

		return $html;
	}

	public static function js_for_choosen( $el_id ) {
		?>
		<script type="text/javascript">
            function wpbc_bo_listing__choozen__remove_all_options( selectbox_id ){
				jQuery( selectbox_id + ' option' ).prop( 'selected', false );    										// Disable selection in the real selectbox
				jQuery( selectbox_id ).trigger( 'chosen:updated' );            											// Remove all fields from the Choozen field	// FixIn: 8.7.9.9.
				jQuery( selectbox_id ).trigger( 'change' );
            }
			<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpbc_jq_ready_start();
			?>
			if ( 'function' === typeof( jQuery("#<?php echo esc_attr( $el_id ); ?>").chosen ) ) {

				// Init Choozen
				jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).chosen( { no_results_text: "No results matched" } );

				// Show remove button(s).
				if ( jQuery( '#<?php echo esc_attr( $el_id ); ?>_chosen' ).length ) {
					jQuery( '.wpbc_ui_el__choosen_<?php echo esc_attr( $el_id ); ?> .wpbc_ui_el__choosen_reset_buttons').css('display','flex');
					jQuery( "#<?php echo esc_attr( $el_id ); ?>_chosen" ).attr( "tabindex", "0" );
				}

				// Catch any selections in the Choozen.
				jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).chosen().on( 'change', function (va) {

					if ( jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).val() != null ) {
						//So we are having aready values
						jQuery.each( jQuery( "#<?php echo esc_attr( $el_id ); ?>" ).val(), function (index, value) {
							// Ok we are have array with  all booking resources ID.
							if ( (value.indexOf( ',' ) > 0) || ('0' === value) ) {

								// Disable selection in the real selectbox
								jQuery( '#<?php echo esc_attr( $el_id ); ?>' + ' option' ).removeAttr( 'selected' );

								// Select "All resources" option in real selectbox
								jQuery( '#<?php echo esc_attr( $el_id ); ?>' + ' option:first-child' ).prop( "selected", true );

								// Highlight options in chosen, before removing.
								jQuery('#<?php echo esc_attr( $el_id ); ?>_chosen li.search-choice:not(:contains(' + '<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo html_entity_decode( esc_js( __( 'All resources', 'booking' ) ) ); ?>' + '))')
									.fadeOut(350).fadeIn(300)
									.fadeOut(350).fadeIn(400)
									.fadeOut(350).fadeIn(300)
									.fadeOut(350).fadeIn(400)
									.animate({opacity: 1}, 4000);

								// Update chosen LI choices, relative selected options in selectbox
								var all_resources_timer = setTimeout( function (){
									jQuery( '#<?php echo esc_attr( $el_id ); ?>' ).trigger( 'chosen:updated' );            			// Remove all fields from the Choozen field
								}, 2000 );
								var my_message = '<?php echo esc_js( __( 'Please note, its not possible to add new resources, if "All resources" option is selected. Please clear the selection, then add new resources.', 'booking' ) ); ?>';
								wpbc_admin_show_message( my_message, 'warning', 10000 );
							}
						} );
					}
				});

			} else {
				alert( 'WPBC Error. JavaScript library "chosen" was not defined.' );
			}
			<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpbc_jq_ready_end();
			?>
		</script>
		<?php
	}

	public static function get_booking_resources_arr() {

		/**
		 * $resources_sql_arr:
		 * result = [
		 *            1 = [
		 *                  booking_type_id = "1"
		 *                  title = "Standard"
		 *                  users = "3"
		 *                  import = null
		 *                  export = null
		 *                  cost = "25"
		 *                  default_form = "owner-custom-form-1"
		 *                  prioritet = "2"
		 *                  parent = "0"
		 *                 ],
		 *             ...
		 */
		$resources_sql_arr = wpbc_ajx_get_all_booking_resources_arr();

		/**
		 * $resources_arr = array(             linear_resources = {array} [12]            single_or_parent = {array} [5]                child = {array} [2]  )
		 *
		 *    $resources_arr = {array} [3]
		 *                                     linear_resources = {array} [12]
		 *                                                                          1 = {array} [12]
		 *                                                                                           booking_type_id = "1"
		 *                                                                                           title = "Standard"
		 *                                                                                           users = "3"
		 *                                                                                           import = null
		 *                                                                                           export = null
		 *                                                                                           cost = "25"
		 *                                                                                           default_form = "owner-custom-form-1"
		 *                                                                                           prioritet = "2"
		 *                                                                                           parent = "0"
		 *                                                                                           visitors = "2"
		 *                                                                                           id = "1"
		 *                                                                                           count = {int} 5
		 *                                                                          5 = {array} [12]
		 *                                                                                           booking_type_id = "5"
		 *                                                                                           title = "Standard-1"
		 *                                                                                           users = "1"
		 *                                                                                           import = null
		 */

		$resources_arr      = wpbc_ajx_arrange_booking_resources_arr( $resources_sql_arr );
		$style              = '';
		$select_box_options = array();            // FixIn: 4.3.2.1.

		if ( ! empty( $resources_arr ) ) {

			$linear_resources_arr = $resources_arr['linear_resources'];

			if ( count( $linear_resources_arr ) > 1 ) {

				$resources_id_arr = array();
				foreach ( $linear_resources_arr as $bkr ) {
					$resources_id_arr[] = $bkr['id'];
				}

				/* implode( ',', $resources_id_arr ) */
				$select_box_options[0] = array(
					'title' => __( 'All resources', 'booking' ),
					'attr'  => array( 'title' => '<strong>' . esc_html__( 'All resources', 'booking' ) . '</strong>' ),
					'style' => 'font-weight:600;',
				);
			}

			foreach ( $linear_resources_arr as $bkr ) {

				$option_title = wpbc_lang( $bkr['title'] );

				if ( isset( $bkr['parent'] ) ) {
					if ( $bkr ['parent'] == 0 ) {
						$option_title = $option_title;
						$style        = 'font-weight:600;';
					} else {
						$option_title = '&nbsp;&nbsp;&nbsp;' . $option_title;
						$style        = 'font-weight:400;';
					}
				}
				$select_box_options[ $bkr ['id'] ] = array(
					'title' => $option_title,
					'attr'  => array( 'title' => $option_title ),
					'style' => $style,
				);
			}
		}

		return $select_box_options;
	}



	/**
	 * Get Template for Modal Window -  Booking Cost Edit - Layout - Modal Window structure
	 *
	 * @return false|void
	 */
	public static function template_for_modal__000() {

		?>
		<span class="wpdevelop">
			<div id="wpbc_modal__resource_choosen__section" class="modal wpbc_popup_modal wpbc_modal_in_listing" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
								<span class="wpbc_modal__title__reason">
								<?php
									esc_html_e( 'Delete selected bookings', 'booking' );
								?>
								</span>
								<sup class="wpbc_modal__title__reason__booking_id wpbc_modal__booking_id__in_title"></sup>
							</h4>
						</div>
						<div class="modal-body">
							<label for="wpbc_modal__resource_choosen__value" style="font-size: 14px;margin: 5px 0 10px;">
							<?php
								echo '<strong>' . esc_attr__( 'Enter the reason for the operation.', 'booking' ) . '</strong> (' . esc_attr__( 'Optional', 'booking' ) . ')';
							?>
							</label>
							<textarea id="wpbc_modal__resource_choosen__value"
									name="wpbc_modal__resource_choosen__value" cols="87" rows="3"
									placeholder="<?php echo esc_attr__( 'Optional', 'booking' ) . ' '; ?>"
							></textarea>
							<input type="hidden" id="wpbc_modal__resource_choosen__booking_id" value=""/>
							<p class="help-block">
								<?php
								/* translators: 1: ... */
								echo wp_kses_post( sprintf( __( 'In the %1$semail template%2$s, use the %3$s shortcode to display this text.', 'booking' ), '<a href="' . esc_url( wpbc_get_settings_url( true, false ) . '&tab=email&subtab=deleted' ) . '">', '</a>', '<b>[reason]</b>' ) );
								?>
							</p>
						</div>
						<div class="modal-footer">
							<a  id="wpbc_modal__resource_choosen__button_send" class="button button-primary"
								href="javascript:void(0);"
								onclick="javascript: wpbc_ajx_booking_ajax_action_request( {
														'booking_action'       : '<?php echo esc_js( self::ACTION ); ?>',
														'booking_id'           : wpbc_get_selected_row_id(),
														'reason_of_action'     : jQuery( '#wpbc_modal__resource_choosen__value' ).val(),
														'ui_clicked_element_id': 'wpbc_modal__resource_choosen__button_send'
												} );
												wpbc_button_enable_loading_icon( this );
												jQuery( '.wpbc_modal__title__reason__booking_id' ).html('');
												jQuery( '#wpbc_modal__resource_choosen__value' ).val(''),
												jQuery( '#wpbc_modal__resource_choosen__section' ).wpbc_my_modal( 'hide' );
										" >
							<?php
								esc_html_e( 'Completely Delete', 'booking' );
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
//add_action( 'wpbc_hook_booking_template__hidden_templates', array( new WPBC_Listing_Actions__Resource_Choosen(), 'template_for_modal' ) );
