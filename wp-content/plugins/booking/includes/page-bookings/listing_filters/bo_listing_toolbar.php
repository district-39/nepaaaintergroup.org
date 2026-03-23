<?php
/**
 * Toolbar for the Booking Listing page.
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}


// ---------------------------------------------------------------------------------------------------------------------
// Sending Emails Toggle
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show "Send Emails" toggle at  the right side of Top Horizontal Navigation  Bar
 *
 * @param string $active_page_tag - such as 'wpbc'.
 * @param string $active_tab_tab  - such  as 'vm_calendar' | 'vm_booking_listing'.
 *
 * @return void
 */
function wpbc_bo_listing__show_send_emails_btn( $active_page_tag, $active_tab_tab ) {

	if ( ( 'wpbc' === $active_page_tag ) && ( 'vm_booking_listing' === $active_tab_tab ) ) {
		?>
		<div class="wpbc_ajx_toolbar wpbc_no_borders wpbc_not_toolbar_is_send_emails" style="margin-left: auto;">
			<div class="ui_container ui_container_micro">
				<div class="ui_group">
					<div class="ui_element">
						<?php
						// This JS var wpbc_ajx_booking_listing.search_get_param( 'ui_usr__send_emails' ) can be 'send' | 'not_send' .

						wpbc_bo_listing__is_send_emails_toggle( '', wpbc_ajx_get__request_params__names_default( 'default' ) );
						?>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			(function () {
				var a = setInterval( function () {
					if ( ('undefined' === typeof wpbc_ajx_booking_listing) || ('undefined' === typeof jQuery) || !window.jQuery ) {
						return;
					}
					clearInterval( a );
					jQuery( document ).ready( function () {
						var param_ui_usr__send_emails = wpbc_ajx_booking_listing.search_get_param( 'ui_usr__send_emails' );
						param_ui_usr__send_emails     = ('send' === param_ui_usr__send_emails);
						jQuery( "#ui_usr__send_emails" ).prop( "checked", param_ui_usr__send_emails );
					} );
				}, 500 );
			})();
		</script>
		<?php
	}
}

add_action( 'hook__wpbc_ui__top_horisontal_nav__end', 'wpbc_bo_listing__show_send_emails_btn', 10, 2 );


/**
 * Toggle "Emails Sending".
 *
 * @return void
 */
function wpbc_bo_listing__is_send_emails_toggle( $escaped_search_request_params, $defaults ){

	$el_id = 'ui_usr__send_emails';

	$el_value = isset( $escaped_search_request_params[ $el_id ] ) ? $escaped_search_request_params[ $el_id ] : $defaults[ $el_id ];

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
							, 'selected' => ( ( 'send' == $el_value ) ? true : false )		// Selected or not
							//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
							, 'onchange' => "wpbc_ajx_booking_send_search_request_with_params( {'ui_usr__send_emails': (jQuery( this ).is(':checked') ? 'send' : 'not_send') } );"					// JavaScript code
							//, 'hint' 	=> array( 'title' => __('Filter bookings by booking status' ,'booking') , 'position' => 'top' )
						);

	wpbc_flex_toggle( $params_checkbox );
}


// ---------------------------------------------------------------------------------------------------------------------
// TODO: Toolbar  contunue here 2025-05-02
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Toolbar for Booking Listing page.
 *
 * @param array $escaped_search_request_params - initital escaped parameters.
 *
 * @return void
 */
function wpbc_ui__toolbar__bo_listing( $escaped_search_request_params ){

	$defaults = wpbc_ajx_get__request_params__names_default( 'default' );

	echo '<div class="wpbc_ui_toolbar  wpbc_ui_toolbar__bo_listing">';
	// 1st Row.
	echo '  <div class="wpbc_ui_group   wpbc_ui__height_lg   wpbc_ui__bg_transparent wpbc_ui__width_100  wpbc_ui_group_filter_row_1" >';

	wpbc_temp_wpbc_ui__filter__booking_dates();
	wpbc_temp_wpbc_ui__filter__booking_status();

	wpbc_ui_el__space_horizontal();

	wpbc_bo_listing__resource_choosen( $escaped_search_request_params, $defaults );

	wpbc_ui_el__space_horizontal();

	wpbc_temp_wpbc_ui__filter__booking_keyword();
	wpbc_temp_wpbc_ui__filter__reload_listing();
	wpbc_temp_wpbc_ui__filter__other_filters();

	echo '  </div>';

	// 2nd Row.
	echo '  <div class="wpbc_ui_group wpbc_ui__height_small  wpbc_ui__bg_white  wpbc_ui__hidden wpbc_ui__width_100  wpbc_ui_group_filter_row_2" >';

	wpbc_temp_wpbc_ui__filter__booking_status();
	wpbc_temp_wpbc_ui__filter__booking_status();

	wpbc_ui_el__space_horizontal();

	wpbc_temp_wpbc_ui__filter__booking_status();
	wpbc_temp_wpbc_ui__filter__booking_status();

	echo '  </div>';
	echo '</div>';

}

function wpbc_temp_wpbc_ui__filter__booking_keyword() {
	?>
	<div class="wpbc_ui_el " style="flex:1 1 auto;">
		<input style="flex:1 1 auto;" type="text" id="wpbc_search_field"
			   name="wpbc_search_field"
			   placeholder="Enter keyword to search..." value=""
			   autocomplete="off"
			   class="wpbc_ui_control wpbc_ui_text wpbc_ui0_el">
	</div><?php
}

function wpbc_temp_wpbc_ui__filter__booking_status() {

	$el_arr = array(
		// Icon: 'font_icon' => 'wpbc_icn_swap_vert', //.
		'title'           => '<strong class="nav-tab-text hide_in_mobile">' . __( 'Status', 'booking' ) . ': </strong><span class="selected_value"></span>',
		'hint'            => array( 'title' => __( 'Select sorting order', 'booking' ), 'position' => 'top', ),
		'font_icon'       => 'wpbc_icn_rule0 wpbc-bi-check-all0 wpbc-bi-toggles0 wpbc-bi-ui-checks-grid',
		'position'        => 'left',
		'has_down_arrow'  => true,
		'has_border'      => true,
		'container_class' => 'ul_dropdown_menu__' . 'status' . ' wpbc_ui__bg_white',
		'items'           => array(
			array(
				'type' => 'html',
				'html' => '<a  class="ul_dropdown_menu_li_action ul_dropdown_menu_li__status" 
								   href="javascript:void(0)" ' . " onclick=\"console.log({ '" . esc_attr( 'status' ) . "': 'pending' }); \"
								>" . __( 'Pending', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i></a>',
			),
			array(
				'type' => 'html',
				'html' => '<a  class="ul_dropdown_menu_li_action ul_dropdown_menu_li__status" 
								   href="javascript:void(0)" ' . " onclick=\"console.log({ '" . esc_attr( 'status' ) . "': 'approved' }); \"
								>" . __( 'Approved', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i></a>',
			),
			array( 'type' => 'divider' ),
			array(
				'type' => 'html',
				'html' => '<a  class="ul_dropdown_menu_li_action ul_dropdown_menu_li__status" 
								   href="javascript:void(0)" ' . " onclick=\"console.log({ '" . esc_attr( 'status' ) . "': 'all' }); \"
								>" . __( 'All', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i></a>',
			),
		),
	);

	wpbc_ui_el__dropdown_menu( $el_arr );
}

function wpbc_temp_wpbc_ui__filter__booking_dates() {

	$el_arr = array(
		// Icon: 'font_icon' => 'wpbc_icn_swap_vert', //.
		'title'           => '<strong class="nav-tab-text hide_in_mobile">' . __( 'Dates', 'booking' ) . ': </strong><span class="selected_value"></span>',
		'hint'            => array( 'title' => __( 'Select sorting order', 'booking' ), 'position' => 'top', ),
		'font_icon'       => 'wpbc-bi-calendar3-range',
		'position'        => 'left',
		'has_down_arrow'  => true,
		'has_border'      => true,
		'container_class' => 'ul_dropdown_menu__' . 'status' . ' wpbc_ui__bg_white',
		'items'           => array(
			array(
				'type' => 'html',
				'html' => '<a  class="ul_dropdown_menu_li_action ul_dropdown_menu_li__status" 
								   href="javascript:void(0)" ' . " onclick=\"console.log({ '" . esc_attr( 'status' ) . "': 'pending' }); \"
								>" . __( 'Pending', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i></a>',
			),
			array(
				'type' => 'html',
				'html' => '<a  class="ul_dropdown_menu_li_action ul_dropdown_menu_li__status" 
								   href="javascript:void(0)" ' . " onclick=\"console.log({ '" . esc_attr( 'status' ) . "': 'approved' }); \"
								>" . __( 'Approved', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i></a>',
			),
			array( 'type' => 'divider' ),
			array(
				'type' => 'html',
				'html' => '<a  class="ul_dropdown_menu_li_action ul_dropdown_menu_li__status" 
								   href="javascript:void(0)" ' . " onclick=\"console.log({ '" . esc_attr( 'status' ) . "': 'all' }); \"
								>" . __( 'All', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i></a>',
			),
		),
	);

	wpbc_ui_el__dropdown_menu( $el_arr );
}

function wpbc_temp_wpbc_ui__filter__reload_listing() {

	$el_arr = array();

	$el_arr['onclick'] = "jQuery( '#toolbar_booking_listing,.wpbc_ajx_under_toolbar_row' ).hide();";

	$el_arr['title']           = '';
	$el_arr['font_icon']       = 'wpbc_icn_refresh wpbc_spin wpbc_animation_pause';
	$el_arr['hint']            = array(
		'title'    => __( 'Refresh listing', 'booking' ),
		'position' => 'top',
	);
	$el_arr['container_class'] = 'ul_dropdown_menu__reload_listing wpbc_ui_el ';

	wpbc_ui_el__a( $el_arr );
}

function wpbc_temp_wpbc_ui__filter__other_filters() {

	$el_arr = array();

	$el_arr['onclick'] = "jQuery( '#toolbar_booking_listing,.wpbc_ajx_under_toolbar_row' ).hide();";

	$el_arr['title']           = '';
	$el_arr['font_icon']       = 'wpbc_icn_tune';
	$el_arr['hint']            = array(
		'title'    => __( 'Advanced Filters', 'booking' ),
		'position' => 'top',
	);
	$el_arr['container_class'] = 'ul_dropdown_menu__other_filters wpbc_ui_el ';

	wpbc_ui_el__a( $el_arr );
}




/**
 * Get data for Resource Selection elemnt - [C H O O S E N]
 *
 * @return void
 */
function wpbc_bo_listing__resource_choosen( $escaped_search_request_params, $defaults ) {

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo WPBC_Listing_Actions__Resource_Choosen::get_element_html( $escaped_search_request_params, $defaults );

	return;

	?><style type="text/css">
		.wpbc_ui_toolbar .wpbc_ui_group .wpbc_ui_el.wpbc_ui_el__choosen { flex: 1 1 auto; }
	</style>
	<?php

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
	wpbc_ui__choosen2( $selector_id, $selector_arr, $text_empty_selection );
}





/**
 *  In Booking Listing - R e s o u r c e s    S e l e c t o r    [C H O O S E N].
 *
 * @param string $selector_id           - 'wh_booking_type' - HTML ID of element.
 * @param array  $selector_arr          - array( 'Title' => Value ).
 * @param string $text_empty_selection  - 'Booking resources'.
 */
function wpbc_ui__choosen2( $selector_id, $selector_arr, $text_empty_selection ) {

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
