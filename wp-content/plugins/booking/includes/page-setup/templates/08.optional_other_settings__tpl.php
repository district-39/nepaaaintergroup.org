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
// == Main - Optional Settings  ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Template - Optional Settings
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
function wpbc_stp_wiz__template__optional_other_settings(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__optional_other_settings">
		<?php
		// == Steps as Dots ==   with Header
		?>
		<div class="wpbc__container_place__header__steps_for_timeline"  style="flex:1 1 100%;margin-top:-20px;">
			<div class="wpbc__form__div wpbc_container_booking_form wpbc_swp_section wpbc_swp_section__optional_other_settings">
				<div class="wpbc__row">
					<div class="wpbc__field" style="flex: 0 0 auto;flex-flow: column;margin:0 0 10px;">
						<h1 class="wpbc_swp_section_header" ><?php esc_html_e( 'Optional Settings and Features', 'booking' ); ?></h1>
						<p class="wpbc_swp_section_header_description"><?php esc_html_e('Configure additional optional settings and features.','booking'); ?></p>
					</div>
					<div class="wpbc__field">
						<#
							var wpbc_template__timeline_steps = wp.template( 'wpbc_template__timeline_steps' );
						#>
						<div class="wpbc__container_place__steps_for_timeline">{{{  wpbc_template__timeline_steps( data.steps_is_done )  }}}</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		// == Main Section ==
		?>
		<div class="wpbc_ajx_page__section_main">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpbc_stp_wiz__widget__optional_other_settings__all_settings_panels__get();
			if (0){
			?>
			<div class="wpbc_widgets">
				<div class="wpbc_widget             wpbc_widget_preview_booking_form">
					<div class="wpbc_widget_header">
						<span class="wpbc_widget_header_text"><?php esc_html_e( 'Preview', 'booking' ); echo ' <i class="menu_icon icon-1x wpbc_icn_navigate_next"></i> '; esc_html_e( 'Booking Form', 'booking' ); ?></span>
						<?php wpbc_stp_wiz__ui__form_structure__mobile_buttons(); ?>
					</div>
					<div class="wpbc_widget_content wpbc_ajx_toolbar wpbc_no_borders">
						<div class="wpbc_center_preview" style="margin: 0 auto;">
							<# data.calendar_force_load = data.calendar_force_load.replace( /ajax_scrpt/gi, 'script' ); #>
							{{{data.calendar_force_load}}}
						</div>
					</div>
				</div>
			</div>
			<?php
			}
			?>
		</div>
		<?php
		// == Right Section ==
		?>
		<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_right 	wpbc_ajx_page__section_right">
			<div class="wpbc_widgets"><?php
				//wpbc_stp_wiz__widget__optional_other_settings__skins();
				?>
				<#
					var wpbc_template__stp_wiz__footer_buttons = wp.template( 'wpbc_template__stp_wiz__footer_buttons' );
				#>
				<div class="wpbc_ajx_page__section wpbc_setup_wizard_page__section_footer		wpbc_ajx_page__section_footer">
					<div class="wpbc__container_place__footer_buttons 		wpbc_container    wpbc_form    wpbc_container_booking_form"><?php /* ?>{{{
						wpbc_template__stp_wiz__footer_buttons( data )
					}}}<?php */ ?>
						<div class="wpbc__row wpbc__row__btn_prior_next">
							<div class="wpbc__field">
								<a	   class="wpbc_button_light button-primary" tabindex="0"
									   id="btn__toolbar__buttons_next"
									   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																											'current_step': '{{data.steps[ data.current_step ].next}}',
																											   'do_action': '{{data.steps[ data.current_step ].do_action}}',
																											'ui_clicked_element_id': 'btn__toolbar__buttons_next'
																										} );
												wpbc_button_enable_loading_icon( this );
												wpbc_admin_show_message_processing( '' );" ><span><?php esc_html_e('Skip for Now','booking'); ?>&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

		//wpbc_stp_wiz__ui__optional_other_settings__bottom_buttons();

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
			.wpbc_ui_settings_panel__card__calendar,
			.wpbc_ui_settings_panel__card__general_availability,
			.wpbc_ui_settings_panel__card__change_over_days,

			.wpbc_ui_settings_panel__card__time_slots_options,
			.wpbc_ui_settings_panel__card__form_options000,

			.wpbc_ui_settings_panel__card__sync_options,

			.wpbc_ui_settings_panel__card__date_time_format,

			.wpbc_ui_settings__panel__up_header__admin_panel,
			.wpbc_ui_settings_panel__card__admin_panel,
			.wpbc_ui_settings_panel__card__plugin_menu_permistions,
			.wpbc_ui_settings_panel__card__translations,
			.wpbc_ui_settings_panel__card__timeline_back_end,

			.wpbc_ui_settings__panel__up_header__advanced_settings,
			.wpbc_ui_settings_panel__card__advanced_settings,
			.wpbc_ui_settings_panel__card__uninstall,
			.wpbc_ui_settings_panel__card__info_news
			{
				display:none;
			}
			.wpbc_ajx_page__section_right hr,
			.wpbc_ajx_page__section_right #btn__toolbar__buttons_prior,
			.wpbc_ajx_page__section_right .wpbc__row__btn_skip_exist{
				display:none;
			}
			.wpbc_ajx_page__section_right .wpbc_widgets{
				align-items: stretch;
			}
			.wpbc_ajx_page__section_right #btn__toolbar__buttons_next{
				margin-left: auto;
			}
			@media screen and (max-width: 782px) {
				.wpbc_ajx_page__container .wpbc_setup_wizard_page__section_right {
					display:none;
				}
			}
		</style>
	</script><?php
}


function wpbc_stp_wiz__widget__optional_other_settings__all_settings_panels__get() {

	ob_start();
	wpbc_ui_settings__panel__all_settings_panels( array( 'is_use_permalink' => true ) );
	$result = ob_get_clean();

	$result = wpbc_replace__js_scripts__to__tpl_scripts( $result );

	return $result;

}
