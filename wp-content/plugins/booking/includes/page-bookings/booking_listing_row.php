<?php
/**
 * Booking Listing Template - Booking Row
 *
 * @version  1.0
 * @package  Any
 * @category Templates for Booking Listing
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2025-03-24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

function wpbc_template__booking_listing_header() {

	// Header
	?><script type="text/html" id="tmpl-wpbc_ajx_booking_list_header">
		<div class="wpbc__list__head wpbc_selectable_head">
			<div class="wpbc__list__row">
				<div class="wpbc__list__col  wpbc_col_id  check-column">
					<div class="content_text"><input type="checkbox" /></div>
				</div>
				<div class="wpbc__list__col">
					<div class="content_text"><?php WPBC_Listing_Actions__Bulk_Actions::get_button(); ?></div>
					<div class="wpbc_ui_el__divider"><div class="wpbc_ui_el__vertical_space"></div></div>
					<div class="content_text"><?php WPBC_Listing_Filters__Expand_Colapse_All::get_button(); ?></div>
				</div>
				<div class="wpbc__list__col col__right">
					<div class="content_text"><?php WPBC_Listing_Filters__Sort_By::get_button(); ?></div>
					<div class="wpbc_ui_el__divider"><div class="wpbc_ui_el__vertical_line"></div></div>
					<div class="wpbc_ajx_booking_pagination_header wpbc__list__col"></div>
				</div>
			</div>
		</div>
	</script><?php
}

/**
 * Footer Template for listing Table.
 *
 * @return void
 */
function wpbc_template__booking_listing_footer() {

	// Footer.
	?>
	<script type="text/html" id="tmpl-wpbc_ajx_booking_list_footer">
		<div class="wpbc__list__foot wpbc_selectable_foot">
			<div class="wpbc__list__row">
				<?php /* ?>
				<div class="wpbc__list__col  wpbc_col_id  check-column">
					<div class="content_text"><input type="checkbox"/></div>
				</div>
 				<?php */ ?>
				<div class="wpbc__list__col col__right">
					<div class="wpbc_ajx_booking_pagination_footer wpbc__list__col"></div>
				</div>
			</div>
		</div>
	</script>
	<?php
}


function wpbc_template__booking_listing_row() {

	// Rows.
	?>
	<script type="text/html" id="tmpl-wpbc_ajx_booking_list_row">
		<div id="row_id_{{{data.parsed_fields.booking_id}}}"
				class="wpbc_listing_usual_row wpbc_list_row wpbc_row wpbc_ui_el__listing_row
						<# if ( '1' === data.parsed_fields.trash ) { #> wpbc_row_booking_trash <# } #>
						<# if ( '1' == data.approved ) { #>wpbc_row_booking_approved<# } else { #>wpbc_row_booking_pending<# } #>
					">
			<?php wpbc_for_booking_template__action_read(); ?>
			<span class="wpbc_row_wrap max_height_a">
				<div class="wpbc_a_row      wpbc_a_row__booking  wpbc_a_row_nowrap">
					<div class="wpbc_a_col  wpbc_a_col__check  check-column "><?php wpbc_template__booking_listing_row__section_col__checkrow(); ?></div>
					<div class="wpbc_a_col  wpbc_a_col__dates"><?php wpbc_template__booking_listing_row__section_col__dates(); ?></div>
					<div class="wpbc_a_col  wpbc_a_col__data  wpbc_a_overflow_hide">
						<div class="wpbc_a_row   wpbc_a_row_nowrap">
							<div class="wpbc_a_col  wpbc_a_col__details_lables  wpbc_a_overflow_hide">
								<div class="wpbc_a_row">
									<div class="wpbc_a_col"><?php wpbc_template__booking_listing_row__section_col__booking_data(); ?></div>
								</div>
								<div class="wpbc_a_row">
									<div class="wpbc_a_col"><?php wpbc_template__booking_listing_row__section_col__labels(); ?></div>
								</div>
							</div>
							<div class="wpbc_a_col  wpbc_a_col__cost"><?php wpbc_template__booking_listing_row__section_col__cost(); ?></div>
							<div class="wpbc_a_col  wpbc_a_col__action"><?php wpbc_template__booking_listing_row__section_col__action_button(); ?></div>
						</div>
						<div class="wpbc_a_row wpbc_a_row__notes">
							<div class="wpbc_a_col"><?php wpbc_template__booking_listing_row__section_col__note_readonly(); ?></div>
						</div>
						<div class="wpbc_a_row  wpbc_a_row__system">
							<div class="wpbc_a_col"><?php wpbc_template__booking_listing_row__section_col__sys_info(); ?></div>
						</div>
						<?php wpbc_template__booking_listing_row__section_col__hidden_fields(); ?>
					</div>
				</div>
			</span>
		</div>
	</script>
	<?php

	/**
	 *  data objet =  { "booking_db": {
											"booking_id": "3",
											"trash": "0",
											"sync_gid": "",
											"is_new": "0",
											"status": "",
											"sort_date": "2022-02-03 12:00:01",
											"modification_date": "2022-03-14 17:48:23",
											"form": "selectbox-one^rangetime2^12:00 - 14:00~text^name2^Anthony~text^secondname2^Gomez~text^email2^Gomez.example@wpbookingcalendar.com~text^address2^144 Hitchcock Rd~text^city2^Jacksonville~text^postcode2^38374~text^country2^US~text^phone2^988-48-45~selectbox-one^visitors2^3~checkbox^children2[]^2~textarea^details2^",
											"hash": "993086af7a382f3956e6a0010932c856",
											"booking_type": "2",
											"remark": null,
											"cost": "58.00",
											"pay_status": "",
											"pay_request": "0"
									  },
						"id": "3",
						"approved": "0",
						"dates": [ "2022-02-03 12:00:01", "2022-02-03 14:00:02" ],
						"child_id": [ "", "" ],
						"short_dates": [ "2022-02-03 12:00:01", "-", "2022-02-03 14:00:02" ],
						"short_dates_child_id": [ "", "", "" ],
						"parsed_fields": {
											"rangetime": "12:00 PM - 2:00 PM",
											"name": "Anthony",
											"secondname": "Gomez",
											"email": "Gomez.example@wpbookingcalendar.com",
											"address": "144 Hitchcock Rd",
											"city": "Jacksonville",
											"postcode": "38374",
											"country": "US",
											"phone": "988-48-45",
											"visitors": "3",
											"children": "2",
											"details": "",
											"booking_id": "3",
											"trash": "0",
											"sync_gid": "",
											"is_new": "0",
											"status": "",
											"sort_date": "2022-02-03 12:00:01",
											"modification_date": "March 14, 2022 5:48 PM",
											"hash": "993086af7a382f3956e6a0010932c856",
											"booking_type": "2",
											"cost": "58.00",
											"pay_status": "",
											"pay_request": "0",
											"id": "3",
											"approved": "0"
						},
						"templates": {
										"form_show": "<div class=\"standard-content-form\"> \r\n\t<strong>First Name</strong>:<span class=\"fieldvalue\">Anthony</span>&nbsp;&nbsp; \r\n\t<strong>Last Name</strong>:<span class=\"fieldvalue\">Gomez</span>&nbsp;&nbsp; \r\n\t<strong>Email</strong>:<span class=\"fieldvalue\">Gomez.example@wpbookingcalendar.com</span>&nbsp;&nbsp; \r\n\t<strong>Adults</strong>:<span class=\"fieldvalue\"> 3</span>&nbsp;&nbsp; \r\n\t<strong>Children</strong>:<span class=\"fieldvalue\"> 2</span>&nbsp;&nbsp; \r\n\t<strong>Details</strong>:&nbsp;&nbsp;<span class=\"fieldvalue\"> </span> \r\n</div>"
									},
						"__search_request_keyword__": ""
					  }
	 */
}

// ---------------------------------------------------------------------------------------------------------------------
// Booking Row Elements
// ---------------------------------------------------------------------------------------------------------------------
function wpbc_template__booking_listing_row__section_col__checkrow(){

	?>
	<div class="content_text"><input type="checkbox"/></div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__dates() {

	?>
	<div class="booking_dates_small booking_dates_expand_section" style="<# /* if ( 'short' !== wpbc_ajx_booking_listing.search_get_param('ui_usr__dates_short_wide') ) { #>display: none;<# } */ #>" >
		<div class="content_text">{{{data.templates.short_dates_content}}}</div>
	</div>
	<div class="booking_dates_full booking_dates_expand_section" style="<# /* if ( 'wide' !== wpbc_ajx_booking_listing.search_get_param('ui_usr__dates_short_wide') ) { #>display: none;<# } */ #>" >
		<div class="content_text">{{{data.templates.wide_dates_content}}}</div>
	</div>
	<div class="wpbc_btn_expand_down">
		<a class="wpbc_btn_expand_down_a"
		   href="javascript:void(0)"
		   onclick="javascript:jQuery(this).parents('.wpbc_row_wrap').toggleClass('max_height_a');jQuery( this ).find('.menu_icon').toggle();">
			<i class="menu_icon icon-1x wpbc_icn_expand_more" title="<?php echo esc_js( __( 'Expand', 'booking' ) ); ?>"></i>
			<i class="menu_icon icon-1x wpbc_icn_expand_less" title="<?php echo esc_js( __( 'Colapse', 'booking' ) ); ?>" style="display:none;"></i>
		</a>
	</div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__booking_data(){

    ?>
	<div class="wpbc_listing_col wpbc_col_data">
		<div class="content_text">
			<#
				var booking_details_simple = data.templates.form_show_simple;
				var booking_details = data.templates.form_show;
				var booking_keyword = data[ '__search_request_keyword__' ];
					booking_details = wpbc_get_highlighted_search_keyword( booking_details, booking_keyword );
					booking_details_simple = wpbc_get_highlighted_search_keyword( booking_details_simple, booking_keyword );
			#>
			<a title="<?php echo esc_js( __( 'Expand', 'booking' ) ); ?>"
			   href="javascript:void(0)"
			   onclick="javascript:jQuery(this).parents('.wpbc_row_wrap').toggleClass('max_height_a');jQuery(this).parents('.wpbc_row_wrap').find('.wpbc_btn_expand_down_a .menu_icon').toggle();"
			   class="booking_details_simple__expand"
			><div class="booking_details_simple">{{{booking_details_simple}}}</div></a>
			<span <?php /* ?>
			   title="<?php echo esc_js( __( 'Collapse', 'booking' ) ); ?>"
			   href="javascript:void(0)"
			   onclick="javascript:jQuery(this).parents('.wpbc_row_wrap').toggleClass('max_height_a');jQuery(this).parents('.wpbc_row_wrap').find('.wpbc_btn_expand_down_a .menu_icon').toggle();"
 				<?php  // FixIn: 10.11.3.2  */ ?>
			   class="booking_details_simple__expand"
			><div class="booking_details_full">{{{booking_details}}}</div></span>
		</div>
	</div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__labels(){

    ?>
	<div class="wpbc_listing_col wpbc_col_booking_labels wpbc_col_labels">
		<div class="content_text">
			<span class="wpbc_label wpbc_label_booking_id"><span class="label_sup">ID:</span>{{data['parsed_fields']['booking_id']}}</span><?php
			?>
			<#
			if ( '<?php echo esc_js(__( 'Resource not exist', 'booking' )); ?>' == data.parsed_fields.resource_title ) {
				#><span class="wpbc_label wpbc_label_resource wpbc_label_deleted_resource">{{{data.parsed_fields.resource_title}}}</span><#
			} else if ( '' != data.parsed_fields.resource_title ) {
				#><a  href="javascript:void(0)" class="wpbc_label_link " title='<?php echo esc_attr( __( 'Change resource', 'booking' ) ); ?>'
					  onclick="wpbc_boo_listing__click__change_booking_resource( {{data['parsed_fields']['booking_id']}}, {{data['parsed_fields']['resource_id']}} );">
					<span class="wpbc_label wpbc_label_resource">{{{data.parsed_fields.resource_title}}}</span></a><#
			}

			if ( '' != data.templates.payment_label_template ) {
				#><a href="javascript:void(0)" class="wpbc_label_link " title='<?php echo esc_attr( __( 'Update payment status', 'booking' ) ); ?>'
					 onclick="javascript:wpbc_boo_listing__click__set_payment_status( {{data.parsed_fields.booking_id}}, '{{data.parsed_fields.pay_status}}' );">
					{{{data.templates.payment_label_template}}}
				</a><#
			}

			if ( '' != data.parsed_fields.sync_gid ) {
				#><span class="wpbc_label wpbc_label_imported"><i class="menu_icon icon-1x wpbc_icn_cloud_download system_update_alt"></i><?php echo esc_js(__( 'Imported', 'booking' )); ?></span><#
			}

			#><span class="wpbc_label wpbc_label_trash<# if ( '1' != data.parsed_fields.trash ) { #> hidden_items <# } #>"><i style="color: #f6efe8;" class="menu_icon icon-1x wpbc_icn_delete_forever"></i><?php echo esc_js(__( 'In Trash', 'booking' )); ?></span><#

			#><span class="wpbc_label wpbc_label_approved<# if ( '0' == data.approved ) { #> hidden_items <# } #>"><i style="color: #def5d4;" class="menu_icon icon-1x wpbc_icn_done_all"></i><?php echo esc_js(__( 'Approved', 'booking' )); ?></span><#
			#><span class="wpbc_label wpbc_label_pending<#  if ( '1' == data.approved ) { #> hidden_items <# } #>"><i style="color: #f6efe8;" class="menu_icon icon-1x wpbc_icn_hourglass_empty"></i><?php echo esc_js(__( 'Pending', 'booking' )); ?></span><#

			#><?php


			$labels = get_bk_option( 'wpbc_ajx_booking_labels' );
			$labels = explode( "\n", $labels );
			$labels = array_map( 'trim', $labels );

			// Check for Labels and colors.
			foreach ( $labels as $label ) {

				if ( ( ! empty( $label ) ) && ( false !== strpos( ':', $label ) ) ) {
					list( $label, $color, $text_color ) = explode( ':', $label );
				} else {
					$color      = '';
					$text_color = '';
				}

				if ( ! empty( $label ) ) {
					?><span class="wpbc_label" style="color:<?php echo esc_attr( $text_color ); ?>;background-color:<?php echo esc_attr( $color ); ?>;">{{{data.parsed_fields<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $label;
					?>}}}</span><?php
				}
			}

			// Predefined internal Labels.
			?>
			<#
			if ( 	( undefined != data._paid )
				 && ( -1 != data._paid.toLowerCase().indexOf( 'refund' ) )
			) {
				#><span class="wpbc_label booking_label__refund"><?php echo esc_js(__('Refund','booking')); ?></span><#
			}
		console.log( 'row listing', data );	// LISTING_ROWS
		#>
		</div>
	</div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__note_readonly(){

	if ( ! class_exists( 'wpdev_bk_personal' ) ) {
		return false;
	}

		$booking_action = 'set_booking_note';

		$el_id = 'ui_btn_' . $booking_action . '__note_readonly';

		if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
			return false;
		}

	$params_textarea = array(
		'id'              => "{$booking_action}_text_{{data.parsed_fields.booking_id}}",
		'name'            => "{$booking_action}_text_{{data.parsed_fields.booking_id}}",
		'label'           => '',
		'style'           => '',
		'class'           => "{$booking_action}_text",
		'disabled'        => ! false,
		'attr'            => array(),
		'rows'            => '3',
		'cols'            => '50',
		'placeholder'     => '',
		'value'           => '{{{data.parsed_fields.remark}}}',
		'is_escape_value' => true,
		//, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code.
		//, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code.
	);

    ?>
	<div class="wpbc_listing_col wpbc_col_note_readonly">
		<div class="content_text">
			<div class="ui_remark_section ui_remark_section__note_readonly  is_expand_remarks_<?php //echo ($is_expand_remarks) ? 'on' : 'off'; ?>"
			   style="<# if ( 'Off' === wpbc_ajx_booking_listing.search_get_param('ui_usr__is_expand_remarks') ) { #>display0: none;<# } #><# if ( '' == data.parsed_fields.remark.trim() ) { #>display: none;<# } #>"
				>
					<label><?php esc_html_e( 'Notes', 'booking' ); ?></label>
					<a href="javascript:void(0)"
					   onclick="javascript:wpbc_boo_listing__click__set_booking_note( {{data.parsed_fields.booking_id}}, jQuery( '#set_booking_note_text_{{data.parsed_fields.booking_id}}').val() );"
					   style="text-de"
					>
						<span><?php esc_html_e('Edit note','booking'); ?>&nbsp;</span>
						<i class="menu_icon icon-1x wpbc_icn_edit"></i>
					</a>
					<?php
						wpbc_flex_textarea( $params_textarea );
			?>
			</div>
		</div>
	</div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__sys_info(){

	?>
	<div class="wpbc_listing_col wpbc_col_sys_info">
		<div class="content_text">
			<div class="wpbc_actions_sysinfo">
				<span class="sysinfo_id"><?php echo esc_js(__( 'Booking ID', 'booking' )); ?>: <strong>{{data['parsed_fields']['booking_id']}}</strong></span><div class="wpbc_ui_el__vetical_line sysinfo_id"></div>
				<span class="sysinfo_edited"><?php echo esc_js(__( 'Edited', 'booking' )); ?>: <strong>{{data['parsed_fields']['modification_date']}}</strong></span><div class="wpbc_ui_el__vetical_line sysinfo_edited"></div>
				<span class="sysinfo_created"><?php echo esc_js(__( 'Created', 'booking' )); ?>: <strong>{{data['parsed_fields']['creation_date']}}</strong></span><div class="wpbc_ui_el__vetical_line sysinfo_created"></div>
				<span class="sysinfo_hash"><?php echo esc_js(__( 'Hash', 'booking' )); ?>: <strong>{{data['parsed_fields']['hash']}}</strong></span><div class="wpbc_ui_el__vetical_line sysinfo_hash"></div>
				<span class="sysinfo_sync_id"><?php echo esc_js(__( 'Sync ID', 'booking' )); ?>: <strong>{{data['parsed_fields']['sync_id']}}</strong></span>
			</div>
		</div>
	</div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__hidden_fields(){
	?>
	<#
		var selected_locale_value = '';
		if (
			   ( 'undefined' !== typeof( data.parsed_fields.booking_options) )
			&& ( 'undefined' !== typeof( data.parsed_fields.booking_options.booking_meta_locale ) )
		){
			var selected_locale_value = data.parsed_fields.booking_options.booking_meta_locale;
		}
	#>
	<input type="hidden" id="locale_for_booking{{data['parsed_fields']['booking_id']}}" name="locale_for_booking{{data['parsed_fields']['booking_id']}}" value="{{selected_locale_value}}" />
	<?php
}

function wpbc_template__booking_listing_row__section_col__cost() {

	if ( ! class_exists( 'wpdev_bk_biz_s' ) ) {
		return false;
	}

	?>
	<div class="wpbc_listing_col wpbc_col_cost">
		<a  href="javascript:void(0)"
			onclick="javascript:wpbc_boo_listing__click__set_booking_cost( {{data.parsed_fields.booking_id}}, '{{data.parsed_fields.cost}}' );">
			{{{data.parsed_fields.currency_symbol}}}
			{{data.parsed_fields.cost}}
		</a>
	</div>
	<?php
}

function wpbc_template__booking_listing_row__section_col__action_button() {

	?>
	<div class="wpbc_listing_col wpbc_col_action">
		<?php
		wpbc_template__booking_listing__el__btn_action()
		?>
	</div>
	<?php
}

// ---------------------------------------------------------------------------------------------------------------------
// Action Button
// ---------------------------------------------------------------------------------------------------------------------

function wpbc_template__booking_listing__el__btn_action(){

	$el_arr = array(
		'title'           => '',
		'font_icon'       => 'wpbc_icn_more_vert',
		'position'        => 'right',
		'has_down_arrow'  => false,
		'container_style' => 'position:absolute;',
		'items'           => array(),
	);

	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_approved() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_pending() );
	$el_arr['items'][] = array( 'type' => 'divider' );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_edit_booking() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Booking_Note::get_button() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_print() );
	if ( class_exists( 'wpdev_bk_biz_s' ) ) {
		$el_arr['items'][] = array( 'type' => 'header', 'title' => __( 'Collect Payment', 'booking' ) );
		$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Payment_Request::get_button() );
		$el_arr['items'][] = array( 'type' => 'divider' );
		$el_arr['items'][] = array( 'type' => 'header', 'title' => __( 'Payment Price and Status', 'booking' ) );
		$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Booking_Cost::get_button() );
		$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Payment_Satatus::get_button() );
	}
	$el_arr['items'][] = array( 'type' => 'divider' );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_add_google_calendar() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Change_Resource::get_button() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Duplicate_Booking::get_button() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => WPBC_Action_Change_Locale::get_button() );
	$el_arr['items'][] = array( 'type' => 'divider' );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_trash() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_trash_restore() );
	$el_arr['items'][] = array( 'type' => 'html', 'html' => wpbc_template__booking_listing__action_delete() );

	wpbc_ui_el__dropdown_menu( $el_arr );
}

// ---------------------------------------------------------------------------------------------------------------------
// -- Action for Options in Dropdown Menu
// ---------------------------------------------------------------------------------------------------------------------

/**
 * -- Approve -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_approved() {

	$booking_action = 'set_booking_approved';
	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}
	$html_for_drop_down_option  = "<# if ( '0' == data['approved']) { #>";
	$html_for_drop_down_option .= "<a  	class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "\"
										href=\"javascript:void(0)\"  
							           	onclick=\"wpbc_ajx_booking_ajax_action_request( { 
							           				'booking_action' : '{$booking_action}', 
							           				'booking_id'     : {{data['parsed_fields']['booking_id']}} 
												} ); 
							 					wpbc_button_enable_loading_icon( this );\"
									>" .
										esc_js( __( 'Approve', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_done_all'></i>" .
									'</a>';
	$html_for_drop_down_option .= '<# } #>';

	return $html_for_drop_down_option;
}

/**
 * -- Pending -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_pending() {

	$booking_action = 'set_booking_pending';
	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$html_for_drop_down_option  = "<# if ( '1' == data['approved']) { #>";
	$html_for_drop_down_option .= "<a 	class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "\"
										href=\"javascript:void(0)\" 
							           	onclick=\"if ( ! wpbc_is_modal_accessible( '#wpbc_modal__set_booking_pending__section' ) ) {
													return false;
												}
												jQuery( '.wpbc_modal__title__reason__booking_id' ).html( 'ID: ' + {{data.parsed_fields.booking_id}} );
												jQuery( '#wpbc_modal__set_booking_pending__booking_id').val('{{data.parsed_fields.booking_id}}');
												jQuery( '#wpbc_modal__set_booking_pending__section' ).wpbc_my_modal( 'show' );
												jQuery( '#wpbc_modal__set_booking_pending__value' ).trigger( 'focus' );
								  			\"
									>" .
										esc_js( __( 'Set as Pending', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_hourglass_empty'></i>" .
									'</a>';
	$html_for_drop_down_option .= '<# } #>';

	return $html_for_drop_down_option;
}

/**
 * -- Edit Booking -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_edit_booking() {

	$booking_action = 'edit_booking';

	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$edit_booking_url  = 'admin.php?page=' . wpbc_get_new_booking_url( false, false );
	$edit_booking_url .= '&booking_type={{data.parsed_fields.resource_id}}&booking_hash={{data.parsed_fields.hash}}&parent_res=1';
	$edit_booking_url .= '&booking_form={{data.parsed_fields.wpbc_custom_booking_form}}';                            // FixIn: 9.4.3.12.
	$edit_booking_url .= '&is_show_payment_form=Off';                                                                // FixIn: 9.9.0.38.
	// FixIn: 10.10.1.2  $edit_booking_url .= ( 'Off' !== get_bk_option( 'booking_is_resource_no_update__during_editing' ) ) ? '&resource_no_update=1' : '';        // FixIn: 9.4.2.3.

	$html_for_drop_down_option = "<a  	href='" . $edit_booking_url . "'
										class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action .
											" <# if ( '' == data['parsed_fields']['hash']) { #>wpbc_field_disabled<# } #>\" >" .
										esc_js( __( 'Edit booking', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_draw'></i>" .
								'</a>';

	return $html_for_drop_down_option;
}

/**
 * -- Add to Google Calendar -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_add_google_calendar() {

	$booking_action = 'booking_add_google_calendar';

	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$edit_booking_url  = '{{{data.parsed_fields.google_calendar_link}}}';

	$html_for_drop_down_option = "<a href='" . $edit_booking_url . "' 
									 target='_blank'
									 class='ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "' >" .
										esc_js( __( 'Add to Google Calendar', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_event'></i>" .
								'</a>';

	return $html_for_drop_down_option;
}

/**
 * -- Trash -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_trash() {

	$booking_action = 'move_booking_to_trash';

	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$html_for_drop_down_option  = "<# if ( '0' == data['parsed_fields']['trash'] ) { #>";
	$html_for_drop_down_option .= "<a  	class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "\"
										href=\"javascript:void(0)\"
							           	onclick=\"if ( ! wpbc_is_modal_accessible( '#wpbc_modal__move_booking_to_trash__section' ) ) {
													return false;
												}
												jQuery( '#wpbc_modal__move_booking_to_trash__booking_id').val('{{data.parsed_fields.booking_id}}');
												jQuery( '.wpbc_modal__title__reason__booking_id' ).html( 'ID: ' + {{data['parsed_fields']['booking_id']}} );
												jQuery( '#wpbc_modal__move_booking_to_trash__section' ).wpbc_my_modal( 'show' );
												jQuery( '#wpbc_modal__move_booking_to_trash__value' ).trigger( 'focus' );							
							 					\"
									>" .
										esc_js( __( 'Reject - move to trash', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_delete_outline'></i>" .
									'</a>';
	$html_for_drop_down_option .= '<# } #>';

	return $html_for_drop_down_option;
}

/**
 * -- Restore -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_trash_restore() {

	$booking_action = 'restore_booking_from_trash';

	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$html_for_drop_down_option  = "<# if ( '1' == data['parsed_fields']['trash'] ) { #>";
	$html_for_drop_down_option .= "<a  	class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "\"
										href=\"javascript:void(0)\"  
							           	onclick=\"if ( ! wpbc_is_modal_accessible( '#wpbc_modal__restore_booking_from_trash__section' ) ) {
													return false;
												}
												jQuery( '#wpbc_modal__restore_booking_from_trash__booking_id').val('{{data.parsed_fields.booking_id}}');												          
												jQuery( '.wpbc_modal__title__reason__booking_id' ).html( 'ID: ' + {{data.parsed_fields.booking_id}} );
												jQuery( '#wpbc_modal__restore_booking_from_trash__section' ).wpbc_my_modal( 'show' );
												jQuery( '#wpbc_modal__restore_booking_from_trash__value' ).trigger( 'focus' );
											\"
									>" .
										esc_js( __( 'Restore', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_rotate_left'></i>" .
									'</a>';
	$html_for_drop_down_option .= '<# } #>';

	return $html_for_drop_down_option;
}

/**
 * -- Completely Delete -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_delete() {

	$booking_action = 'delete_booking_completely';

	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$html_for_drop_down_option  = "<# if ( '1' == data['parsed_fields']['trash'] ) { #>";
	$html_for_drop_down_option .= "<a  	class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "\"
										href=\"javascript:void(0)\"  
							           	onclick=\"if ( ! wpbc_is_modal_accessible( '#wpbc_modal__delete_booking_completely__section' ) ) {
													return false;
												}
												jQuery( '#wpbc_modal__delete_booking_completely__booking_id').val('{{data.parsed_fields.booking_id}}');
												jQuery( '.wpbc_modal__title__reason__booking_id' ).html( 'ID: ' + {{data['parsed_fields']['booking_id']}} );
												jQuery( '#wpbc_modal__delete_booking_completely__section' ).wpbc_my_modal( 'show' );
												jQuery( '#wpbc_modal__delete_booking_completely__value' ).trigger( 'focus' );
							 					\"
									>" .
										esc_js( __( 'Completely Delete', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_close'></i>" .
									'</a>';
	$html_for_drop_down_option .= '<# } #>';

	return $html_for_drop_down_option;
}

/**
 * -- Print one booking -- Option for "Actions Dropdown Menu"
 *
 * @return string
 */
function wpbc_template__booking_listing__action_print() {

	$booking_action = 'set_print';

	if ( ! wpbc_is_user_can( $booking_action, wpbc_get_current_user_id() ) ) {
		return false;
	}

	$html_for_drop_down_option = "<a  	class=\"ul_dropdown_menu_li_action ul_dropdown_menu_li_action_" . $booking_action . "\"
										href=\"javascript:void(0)\"  
							           	onclick=\"wpbc_print_dialog__show( {{data['parsed_fields']['booking_id']}} );\"
									>" .
										esc_js( __( 'Print', 'booking' ) ) .
										"<i class='menu_icon icon-1x wpbc_icn_print'></i>" .
									'</a>';

	return $html_for_drop_down_option;
}
