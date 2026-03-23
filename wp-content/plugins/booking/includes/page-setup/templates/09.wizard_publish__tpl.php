<?php /**
 * @version 1.0
 * @description Template   for Setup pages
 * @category    Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-10-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// ---------------------------------------------------------------------------------------------------------------------
// == Main - Publish Form  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Template - Publish Form
 *
 * 	Help Tips:
 *
 *		<script type="text/html" id="tmpl-template_name_a">
 * 			Escaped:  	 {{data.test_key}}
 * 			HTML:  		{{{data.test_key}}}
 * 			JS: 	  	<# if (true) { alert( 1 ); } #>
 * 		</script>
 *
 * 		var template__var = wp.template( 'template_name_a' );
 *
 * 		jQuery( '.content' ).html( template__var( { 'test_key' => '<strong>Data</strong>' } ) );
 *
 * @return void
 */
function wpbc_stp_wiz__template__wizard_publish(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__wizard_publish">
		<?php
		// == Steps as Dots ==   with Header
		?>
		<div class="wpbc__container_place__header__steps_for_timeline"  style="flex:1 1 100%;margin-top:-20px;">
			<div class="wpbc__form__div wpbc_container_booking_form wpbc_swp_section wpbc_swp_section__wizard_publish">
				<div class="wpbc__row">
					<div class="wpbc__field" style="flex: 0 0 auto;flex-flow: column;margin:0 0 10px;">
						<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Publish Your Booking Form', 'booking' ); ?></h1>
						<p class="wpbc_swp_section_header_description"><?php esc_html_e('Start by using the pre-configured booking page or integrate booking form into a new page.','booking'); ?></p>
					</div>
					<div class="wpbc__field">
						<#
							var wpbc_template__timeline_steps = wp.template( 'wpbc_template__timeline_steps' );
						#>
						<div class="wpbc__container_place__steps_for_timeline">{{{  wpbc_template__timeline_steps( data.steps_is_done )  }}}</div>
					</div>
				</div>
			</div>
		</div><?php

		// == Main Section ==

		?><div class="wpbc_ajx_page__section_main"><?php

			wpbc_stp_wiz__panels__wizard_publish__embed_exist_new();

		?></div><?php

		// == CSS to Hide original 'Steps as Dots' ==
		?>
		<style type="text/css">
			.wpbc__container_place__steps_for_timeline {
				display: none;
			}
			.wpbc__container_place__header__steps_for_timeline .wpbc__container_place__steps_for_timeline {
				display: flex;
			}
			.wpbc_setup_wizard_page_container .wpbc__container_place__steps_for_timeline{
				margin: 0 auto 10px;
			}
			.wpbc_page_publish_notice_section > div.wpbc-settings-notice {
				margin: 0 0 25px !important;
			}
			/* Panels */
			.wpbc_ajx_page__section_main .wpbc_ui_settings__flex_container {
				justify-content: space-between;
				flex: 1 1 100%;
			}
			.wpbc_ajx_page__section_main .wpbc_ui_settings__flex_container__col_50 {
				flex: 1 1 48%;
				margin: 0 1%;
			}
			/* Shortcode Text Field*/
			.wpbc_ajx_page__section_main .wpbc_flextable_col .wpbc_ajx_toolbar .ui_container .ui_group .ui_element .shortcode_text_field {
				font-size: 14px !important;
				cursor: text;
				font-weight: 600;
				background: #75A249;
				width: 100%;
				color: #FFF;
				min-width: 15em;
			}
		</style>
	</script><?php
}


/**
 * Show 2 Panels - side by  side
 *
 * @param $params
 *
 * @return void
 */
function wpbc_stp_wiz__panels__wizard_publish__embed_exist_new( $params = array() ) {

	?><div class="wpbc_ui_settings__flex_container"><?php

		if ( ! empty( wpbc_stp_wiz__is_exist_published_page_with_booking_form() ) ) { ?>
			<div class="wpbc_ui_settings__flex_container__col_50" >
				<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__preview"><?php esc_html_e( 'Preview', 'booking' ); ?></div>
				<?php wpbc_ui_settings__panel__publish_into_exist( $params ); ?>
			</div><?php
		}
		?>
		<div class="wpbc_ui_settings__flex_container__col_50">
			<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__publish"><?php esc_html_e( 'Publish', 'booking' ); ?></div>
			<?php wpbc_ui_settings__panel__publish_into_new( $params ); ?>
		</div>
	</div><?php
}


// ---------------------------------------------------------------------------------------------------------------------
//  Already Exist
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Booking Confirmation =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__publish_into_exist( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__publish_into_exist"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__publish_into_exist( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Booking Confirmation  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__publish_into_exist( $params = array() ){

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__publish_into_exist">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-arrow-right-short"></i>
				<h1><span><span class="0wpbc_ui_settings__text_color__black2"> <?php esc_html_e( 'Pre-configured booking page', 'booking' ); ?>
				</span></span></h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<span class="wpbc_ui_settings__text_color__black"> <?php esc_html_e( 'Start by using the pre-configured booking page we have set up.', 'booking' ); ?>
				</span>
			</div><?php

			$wp_post_booking_absolute = wpbc_stp_wiz__is_exist_published_page_with_booking_form();
			if ( ! empty( $wp_post_booking_absolute ) ) {
				?>
				<div class="wpbc_ajx_toolbar wpbc_no_borders" style="margin: 0 auto;margin-top: 15px;">
					<div class="ui_container ui_container_small">
						<div class="ui_group ui_group__publish_btn"  style="align-items: center;">
							<div class="ui_element" style="margin: 0 15px 0 0;">
								<a href="<?php echo esc_url( $wp_post_booking_absolute ); ?>"
								   id="ui_btn_publish_into_exist" class="wpbc_ui_control wpbc_ui_button wpbc_ui_button_danger0 tooltip_top "
								   title="<?php esc_attr_e( 'Go to page with booking form','booking'); ?>"  >
										<span><?php esc_html_e('Go to page with booking form','booking'); ?>&nbsp;&nbsp;&nbsp;</span>
										<i class="menu_icon icon-1x wpbc-bi-arrow-right-short" style="margin: 0;"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			}

		?></div><?php
	}


// ---------------------------------------------------------------------------------------------------------------------
//  Publish New
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Booking Confirmation =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__publish_into_new( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__publish_into_new"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__publish_into_new( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Booking Confirmation  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__publish_into_new( $params = array() ){

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__publish_into_new">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-box-arrow-in-down-left"></i>
				<h1>
					<span>
						<span class="0wpbc_ui_settings__text_color__black2"><?php esc_html_e( 'Insert shortcode in a page', 'booking' ); ?></span>
					</span>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<span class="wpbc_ui_settings__text_color__black"> <?php esc_html_e( 'Would you like to embed your booking form in an existing page, or create a new one?', 'booking' ); ?>
				</span>
			</div>
			<div style="align-self:center;margin: 10px 0 0;" class="wpbc_flextable_col">
				<?php
					wpbc_ui_settings_panel__ui__publish_buttons( $params );
				?>
			</div>

		</div><?php
	}

		/**
		 * Show Publish Buttons UI
		 *
		 * @return void
		 */
		function wpbc_ui_settings_panel__ui__publish_buttons( $params = array() ){

			$defaults = array(
								'id' => wpbc_get_default_resource()
						);
			$resource = wp_parse_args( $params, $defaults );

			if ( ! empty( $resource['id'] ) ) {

				?>
				<div class="wpbc_ajx_toolbar wpbc_no_borders">
					<div class="ui_container ui_container_small">
						<div class="ui_group ui_group__shortcode" style="align-items: center;">
							<div class="ui_element wpbc_popup_modal" style="flex: 1 1 auto;margin: 0;">
								<?php
								// Get property  of booking resource: "shortcode_default"  (such function use cache )

								$shortcode_default_value = ( class_exists( 'wpdev_bk_personal' ) )
															? wpbc_get_resource_property( intval( $resource['id'] ), 'shortcode_default' )
															: '';
								$shortcode_default_value = empty( $shortcode_default_value ) ? '' : $shortcode_default_value;        // Check  if value == false

								?>
								<div  id="div_booking_resource_shortcode_<?php echo intval( $resource['id' ] ); ?>"
										   name="div_booking_resource_shortcode_<?php echo intval( $resource['id' ] ); ?>"
										   class="shortcode_text_field large-text put-in"
									   ><?php echo ( ! empty( $shortcode_default_value ) )
														  ? esc_attr( $shortcode_default_value )
														  : '[booking resource_id=' . intval( $resource['id'] ) . ']';
								?></div>
								<input type="hidden"
									   value="<?php echo ( ! empty( $shortcode_default_value ) )
														  ? esc_attr( $shortcode_default_value )
														  : '[booking resource_id=' . intval( $resource['id'] ) . ']';
										   ?>"
									   id="booking_resource_shortcode_<?php echo intval( $resource['id' ] ); ?>"
									   name="booking_resource_shortcode_<?php echo intval( $resource['id' ] ); ?>"
									   />
							</div>
							<div class="ui_element" style="margin-left: 0px;margin-right: 0px;">
							   <a href="https://wpbookingcalendar.com/faq/#shortcodes" class="tooltip_top wpbc-bi-question-circle"	title="<?php esc_attr_e( 'Learn how to integrate the booking form into a page using the shortcode.','booking'); ?>"></a>
							</div>
						</div>
						<div class="ui_group ui_group__publish_btn"  style="align-items: center;">
							<div class="ui_element" style="margin: 0 15px 0 0;">
								<a href="javascript:void(0)" onclick="javascript:wpbc_resource_page_btn_click(<?php echo intval( $resource['id'] ); ?>);"
								   id="ui_btn_shortcode_customize" class="wpbc_ui_control wpbc_ui_button wpbc_ui_button_danger0 tooltip_top "
								   title="<?php esc_attr_e( 'Customize Booking Calendar shortcode','booking'); ?>"  ><span style="display: none;"><?php
										esc_html_e('Customize','booking'); ?>&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_tune" style="margin: 0;"></i></a>
							</div>
							<div class="ui_element" style="margin: 0 2px 0 0;">
								<a class="wpbc_ui_control wpbc_ui_button wpbc_ui_button_primary tooltip_top "
								   href="javascript:void(0);" onclick="javascript: wpbc_modal_dialog__show__resource_publish( <?php echo esc_attr( $resource['id'] ); ?> );"
									title="<?php esc_attr_e('Embed your booking form into the page', 'booking'); ?>">
									<span style=" "><?php esc_html_e( 'Publish', 'booking' ); ?></span>
									<i class="menu_icon icon-1x wpbc_icn_tune" style="display: none;"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			}

		}



// ---------------------------------------------------------------------------------------------------------------------
// == SUPPORT ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Check  if the page with  booking form  already published and existing
 *
 * @return false|string
 */
function wpbc_stp_wiz__is_exist_published_page_with_booking_form() {

	$is_wp_post_booking = false;

	// FixIn: 10.9.2.5.
	if ( empty( get_page_by_path( 'wpbc-booking' ) ) ) {        // Old page, NOT created before     - Use new url
		$post_name_slug = 'wp-booking-calendar';
	} else {                                                    // Old page already was Created     - Use old url
		$post_name_slug = 'wpbc-booking';
	}
	$wp_post_booking = get_page_by_path( $post_name_slug );


	if ( empty( ! $wp_post_booking ) ) {

		$wp_post_booking_absolute = get_permalink( $wp_post_booking->ID );

		if ( ! empty( $wp_post_booking_absolute ) ) {

			$wp_post_booking_relative = wpbc_make_link_relative( $wp_post_booking_absolute );
			if ( wpbc_is_shortcode_exist_in_page( $wp_post_booking_relative, '[booking' ) ) {
				$is_wp_post_booking = true;

				return $wp_post_booking_absolute;
			}
		}
	}

	// FixIn: 10.12.3.1.
	$front_page_id = get_option( 'page_on_front' );
	if ( ! empty( $front_page_id ) ) {
		$wp_post_booking_absolute = get_permalink( $front_page_id );
		if ( ! empty( $wp_post_booking_absolute ) ) {
			if ( wpbc_is_shortcode_exist_in_page_with_id( $front_page_id, '[booking' ) ) {
				$is_wp_post_booking = true;

				return $wp_post_booking_absolute;
			}
		}
	}

	return $is_wp_post_booking;
}

