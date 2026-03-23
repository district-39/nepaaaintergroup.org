<?php /**
 * @version 1.0
 * @description Template   for Setup pages
 * @category    Setup Templates
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-10
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Main - General Info ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - General Info - Step 01
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
function wpbc_stp_wiz__template__welcome(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__welcome">
	<div class="wpbc_page_main_section    wpbc_container    wpbc_form    wpbc_container_booking_form">
		<div class="wpbc__form__div wpbc_swp_section wpbc_swp_section__welcome">
			<div class="wpbc__row">
				<div class="wpbc__field">
					<h1 class="wpbc_swp_section_header"><?php
						/* translators: 1: ... */
						echo wp_kses_post( sprintf( __(  'Welcome to %s!', 'booking' ), 'WP Booking Calendar' ) ); ?></h1>
				</div>
			</div>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<p style="font-size:15px;line-height: 2.2;"><?php echo wp_kses_post( sprintf( __( 'We\'ll guide you through the steps to set up WP Booking Calendar on your site.','booking'), '<strong>WP Booking Calendar</strong>' ) ); ?></p>

					<div class="wpbc-settings-notice notice-warning notice-helpful-info0" style="max-width: Min(450px, 100%);margin: auto;padding: 4px 15px 7px 20px;font-size: 14px;line-height: 28px;margin-bottom: 25px; border-left-color: #e2892b;margin: 20px 20px 5px 2px;">
						<?php
						echo '<span style="margin: 0 5px 0 0;color: #e2892b;" ><i class="menu_icon icon-1x wpbc_icn_warning_amber"></i></span>';
						echo '<strong>' . esc_html__( 'Important!', 'booking' ) . '</strong> <br/>';
						// echo '<strong>' . esc_html__('Important' ,'booking') . ' ' . esc_html__('Note' ,'booking') . '!</strong><br> ';
						 	/* translators: 1: ... */
							echo  sprintf( __( 'If you have previously configured the Booking Calendar, this setup will override some of your settings. In this case, you can %1$sskip setup%2$s.', 'booking' ),  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								  '<a href="javascript:void(0)" onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { \'do_action\': \'skip_wizard\' } ); ">'
								, '</a>' );
							echo ' ';
						?>
					</div>
				</div>
			</div>
			<div class="wpbc__spacer" style="width:100%;clear:both;height:20px;margin-bottom:20px;border-bottom:1px solid #ccc;"></div>

			<div class="wpbc__row wpbc_setup_wizard_page__section_footer">
				<?php /** ?>
				<div class="wpbc__field wpbc_container wpbc_container_booking_form">
					<a    class="wpbc_button_light"  style=""
						   id="btn__toolbar__buttons_prior"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); "
						   title="<?php esc_attr_e('Exist and skip the Setup Wizard','booking'); ?>"
					 ><i class="menu_icon icon-1x wpbc_icn_close"></i><span>&nbsp;&nbsp;&nbsp;<?php esc_html_e('Skip the Setup Wizard','booking'); ?></span></a>
				</div>
				<?php /**/ ?>
				<div class="wpbc__field wpbc_container wpbc_container_booking_form">
					<a	  style="margin: 0 auto;"
						   class="wpbc_button_light button-primary"
						   id="btn__toolbar__buttons_next"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																								'current_step': '{{data.steps[ data.current_step ].next}}',
																								   'do_action': '{{data.steps[ data.current_step ].do_action}}',
																								'ui_clicked_element_id': 'btn__toolbar__buttons_next'
																							} );
									wpbc_button_enable_loading_icon( this );
									wpbc_admin_show_message_processing( '' );" ><span><?php esc_html_e('Let\'s Get Started','booking'); ?>&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i></a>
				</div>
			</div>
			<?php /**/ ?>
			<div class="wpbc__row">
				<div class="wpbc__field">
					<p class="wpbc_exit_link_small">
						<a href="javascript:void(0)"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); "
						   title="<?php esc_attr_e('Exit and skip the setup wizard','booking'); ?>"
						><?php
							esc_html_e('Exit and skip the setup wizard','booking');
						?></a>
						<?php /* ?>
						<a href="javascript:void(0)" class="wpbc_button_danger" style="margin: 25px 0 0;  font-size: 12px;"
						   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'make_reset' } ); "
						   title="<?php esc_attr_e('Reset the Setup Wizard and start from beginning','booking'); ?>"
						><?php
							esc_html_e('Reset Wizard','booking');
						?></a>
						<?php */ ?>
					</p>
				</div>
			</div>
			<?php /**/ ?>
		</div>
		<style type="text/css">
			.wpbc_ajx_page__container .wpbc_ajx_page__section_footer:not(.wpbc__row){ display: none;}
			.wpbc_setup_wizard_page_container .wpbc_steps_for_timeline_container {display: none;}
			.wpbc_setup_wizard_page_container .wpbc_swp_section__welcome {max-width: 420px}
		</style>
	</div>
	</script><?php
}

