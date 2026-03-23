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
function wpbc_stp_wiz__template__get_started(){

	?><script type="text/html" id="tmpl-wpbc_stp_wiz__template__get_started">
		<?php
		// == Steps as Dots ==   with Header
		?>
		<div class="wpbc__container_place__header__steps_for_timeline"  style="flex:1 1 100%;margin-top:-20px;">
			<div class="wpbc__form__div wpbc_container_booking_form wpbc_swp_section wpbc_swp_section__get_started">
				<div class="wpbc__row">
					<div class="wpbc__field" style="flex: 0 1 550px;flex-flow: column;margin: 0 auto;">
						<h1 class="wpbc_swp_section_header" ><?php echo wp_kses_post( sprintf( __(  'Congratulations! You\'re all set!', 'booking' ), 'WP Booking Calendar' ) ); ?></h1>
						<p class="wpbc_swp_section_header_description" style="font-size: 14px;"><?php
							esc_html_e('Everything is ready to start accepting your first booking.','booking');
						?></p><?php

						if(0){
							?><p class="wpbc_swp_section_header_description" style="font-size: 13px;"><?php
								//_e('Check helpful links to different sections of plugin for managing bookings and calendar(s).','booking');
								?><strong><?php esc_html_e( 'Next Steps', 'booking' ); ?></strong>. <?php
								esc_html_e('Start receiving new bookings from visitors on your website, or create bookings manually in the Admin Panel.','booking');
								echo ' ';
								esc_html_e('Manage Bookings in Booking Listing and Timeline View pages.','booking');
							?></p><?php
						}

					?></div><?php

					/* ?>
					<div class="wpbc__field">
						<#
							var wpbc_template__timeline_steps = wp.template( 'wpbc_template__timeline_steps' );
						#>
						<div class="wpbc__container_place__steps_for_timeline">{{{  wpbc_template__timeline_steps( data.steps_is_done )  }}}</div>
					</div>
 					<?php */ ?>
				</div>
			</div>
		</div>
		<?php
		// == Main Section ==
		?>
		<div class="wpbc_ajx_page__section_main">
			<?php
				wpbc_stp_wiz__panels__get_started__structure();
			?>
		</div>
		<div class="wpbc_page_main_section    wpbc_container    wpbc_form    wpbc_container_booking_form">
			<div class="wpbc__form__div wpbc_swp_section wpbc_swp_section__welcome" style="flex: 1 1 auto;">

				<div class="wpbc__spacer" style="width:100%;clear:both;height:20px;margin-bottom:20px;border-bottom:1px solid #ccc;"></div>

				<div class="wpbc__row wpbc_setup_wizard_page__section_footer">
					<div class="wpbc__field wpbc_container wpbc_container_booking_form" style="justify-content: center">
						<a     class="wpbc_button_light"  style="padding-left: 15px;padding-right: 12px;"
							   tabindex="0"
							   id="btn__toolbar__buttons_prior"
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( {
																									'current_step': '{{data.steps[ data.current_step ].prior}}',
																									'do_action': 'none',
																									'ui_clicked_element_id': 'btn__toolbar__buttons_prior'
																								} );
										wpbc_button_enable_loading_icon( this );
										wpbc_admin_show_message_processing( '' );" ><i class="menu_icon icon-1x wpbc_icn_arrow_back_ios"></i><!--span>&nbsp;&nbsp;&nbsp;<?php esc_html_e('Go Back','booking'); ?></span--></a>
						<a	   style="margin: 0 0 0 1em;"
							   class="wpbc_button_light button-primary"
							   id="btn__toolbar__buttons_next"
							   onclick=" wpbc_ajx__setup_wizard_page__send_request_with_params( { 'do_action': 'skip_wizard' } ); " ><span><?php
								esc_html_e('Complete','booking'); ?></span><!--&nbsp;&nbsp;&nbsp;</span><i class="menu_icon icon-1x wpbc_icn_arrow_forward_ios"></i--></a>
					</div>
				</div>
			</div>
		</div>
		<?php

		//wpbc_stp_wiz__ui__get_started__bottom_buttons();

		// == CSS to Hide original 'Steps as Dots' ==
		?>
		<style type="text/css">
			.wpbc_ajx_page__container .wpbc_ajx_page__section_footer,
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
			.wpbc_ajx_page__section_main .wpbc_ui_settings__flex_container__col_33 {
				flex: 1 1 30%;
				margin: 0 1%;
			}
			.wpbc_ajx_page__section_main .wpbc_ui_settings__flex_container__col_50 {
				flex: 1 1 48%;
				margin: 0 1%;
			}
			.wpbc_ajx_page__section_main .wpbc_ui_settings__flex_container__col_100 {
				flex: 1 1 98%;
				margin: 0 1%;
			}
			.wpbc_dismiss_x__in_panel_card {
			  margin-top: -40px;
			}
			/* Hide published page notice from  previous section */
			.wpbc_page_publish_notice_section {
				display: none;
			}
		</style>
	</script><?php
}



/**
 * Show  Help Panels - What's Next ?
 *
 *
 * @return void
 */

function wpbc_stp_wiz__panels__get_started__structure( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container">

		<div class="wpbc_ui_settings__flex_container__col_33">
			<?php wpbc_stp_wiz__ui__message__get_started(); ?>
		</div>

		<div class="wpbc_ui_settings__flex_container__col_50" >
			<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__bookings_manage"><?php esc_html_e( 'Check and Manage Bookings', 'booking' ); ?></div>
			<?php wpbc_ui_settings__panel__bookings_manage( $params ); ?>

			<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__add_new_booking"><?php esc_html_e( 'Add New Bookings', 'booking' ); ?></div>
			<?php wpbc_ui_settings__panel__add_new_booking( $params ); ?>
		</div>

		<div class="wpbc_ui_settings__flex_container__col_100" style="border-top: 1px solid #e6e6e6;box-shadow: 0 0px 1px #d1d1d1;flex: 0 1 98%;margin: 10px 1% 15px;"></div>

		<div class="wpbc_ui_settings__flex_container__col_33">
			<?php wpbc_stp_wiz__ui__message__next_steps(); ?>
		</div>

		<div class="wpbc_ui_settings__flex_container__col_50">
			<?php

			$dismiss_id = 'wpbc_is_dismissed__panel__add_new_resource';
			list( $is_panel_visible, $dismiss_x_button ) = wpbc_is_dissmissed__in_settings_panel_card( $dismiss_id, 'wpdev_bk_personal' );
			if ( $is_panel_visible ) {

				$params['dismiss_x_button'] = $dismiss_x_button;

				?><div id="<?php echo esc_attr( $dismiss_id ); ?>">

					<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__add_new_resource"><?php esc_html_e( 'Resources  ( Calendars )', 'booking' ); ?></div>
					<?php wpbc_ui_settings__panel__add_new_resource( $params ); ?>

				</div><?php
			} ?>


			<div>
				<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__days_availability"><?php esc_html_e( 'Set Days Availability', 'booking' ); ?></div>
				<?php wpbc_ui_settings__panel__days_availability( $params ); ?>
			</div>

			<?php

			$dismiss_id = 'wpbc_is_dismissed__panel__price';
			list( $is_panel_visible, $dismiss_x_button ) = wpbc_is_dissmissed__in_settings_panel_card( $dismiss_id, 'wpdev_bk_biz_m' );
			if ( $is_panel_visible ) {

				$params['dismiss_x_button'] = $dismiss_x_button;

				?><div id="<?php echo esc_attr( $dismiss_id ); ?>">
					<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__price"><?php esc_html_e( 'Set Prices', 'booking' ); ?></div>
					<?php wpbc_ui_settings__panel__price( $params ); ?>
				</div><?php
			}
			?>

		</div>

	</div><?php
}


	/**
	 * Show Notice Message - get_started
	 *
	 * @return void
	 */
	function wpbc_stp_wiz__ui__message__get_started(){

		?><div class="wpbc-settings-notice notice-warning notice-helpful-info"
		  	   style="padding: 8px 20px;font-size: 14px;max-width: Min(53em,100%);margin:34px 0 0;"><?php

				?><strong><?php esc_html_e( 'Get Started', 'booking' ); ?></strong>. <?php

				esc_html_e( 'Start receiving a new bookings from visitors of your website or create a new bookings in the Admin Panel.', 'booking' );
				echo ' ';
				esc_html_e( 'Manage Bookings in Booking Listing and Timeline View pages.', 'booking' );

		?></div><?php
	}

	/**
	 * Show Notice Message -
	 *
	 * @return void
	 */
	function wpbc_stp_wiz__ui__message__next_steps(){

		?><div class="wpbc-settings-notice notice-warning notice-helpful-info"
				 style="padding: 8px 20px;font-size: 14px;max-width: Min(53em,100%);margin:34px 0 0;"><?php

				?><strong><?php esc_html_e( 'Next Steps', 'booking' ); ?></strong>. <?php
				esc_html_e( 'Add new booking resources (properties or services) with unique calendars and availability.', 'booking' );
				echo ' ';
				esc_html_e( 'Configure availability and pricing for your bookings.', 'booking' );
		?></div><?php
	}


/**
 * Show Settings   = Booking Manage =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__bookings_manage( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__bookings_manage"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__booking_listing( $params );

			wpbc_ui_settings_panel__card__timeline_view( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Booking Listing ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__booking_listing( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_bookings_url() . '&tab=vm_booking_listing' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__booking_listing">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-collection"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Booking Listing','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'View and manage all your bookings in a list format.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Timeline View ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__timeline_view( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_bookings_url() . '&tab=vm_calendar' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__timeline_view">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_rotate_90 wpbc_icn_align_vertical_bottom"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Timeline View','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'View and manage bookings in a timeline format.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}



/**
 * Show Settings   = Add New Booking =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__add_new_booking( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__add_new_booking"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__add_new_booking( $params );

			wpbc_ui_settings_panel__card__add_new_booking_in_front_end( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Timeline View ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__add_new_booking( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_new_booking_url() . '' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__add_new_booking">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_add _circle_outline"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Add New Booking','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Manually add new bookings from the Admin Panel.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


	/**
	 * Show   == Dashboard Card  -  Front-end side page  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__add_new_booking_in_front_end( $params = array() ){

		$wp_post_booking_absolute = wpbc_stp_wiz__is_exist_published_page_with_booking_form();
		if ( ! empty( $wp_post_booking_absolute ) ) {

			?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__add_new_booking_in_front_end">
				<div class="wpbc_ui_settings__text_row">
					<i class="menu_icon icon-1x wpbc-bi-arrow-right-short"></i>
					<h1><span><a href="<?php echo esc_url( $wp_post_booking_absolute ); ?>" class="0wpbc_ui_settings__text_color__black2"> <?php esc_html_e( 'Pre-configured booking page', 'booking' ); ?>
					<a/></span></h1>
				</div>
				<div class="wpbc_ui_settings__text_row">
					<a href="<?php echo esc_url( $wp_post_booking_absolute ); ?>" class="wpbc_ui_settings__text_color__black"> <?php esc_html_e( 'Start receiving bookings directly on the front-end page.', 'booking' ); ?>
					</a>
				</div>
			</div><?php
		}
	}




/**
 * Show Settings   = Booking Manage =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__days_availability( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__bookings_manage"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__days_availability( $params );

			//wpbc_ui_settings_panel__card__season_availability( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Days Availability  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__days_availability( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_availability_url() . '' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__booking_listing">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-calendar2-check"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Days Availability','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Define available and unavailable days for your calendar(s).', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Season Availability ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__season_availability( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_availability_url() . '' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__timeline_view">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x 0wpbc-bi-calendar2-check 0wpbc_icn_check_circle_outline wpbc-bi-calendar2-week"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Season Availability','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Configuration of availability for booking resources', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}



/**
 * Show Settings   = Add New Booking =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__price( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__price"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__daily_costs( $params );

			//wpbc_ui_settings_panel__card__form_options_costs( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Daily Costs ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__daily_costs( $params = array() ){

		if ( ! class_exists( 'wpdev_bk_biz_m' ) ) {
			$url = ' href="https://wpbookingcalendar.com/features/#rates" ';
		} else {
			$url = ' href="' . esc_url( wpbc_get_price_url() . '' ) . '" ';
		}


		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__daily_costs">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-cash-coin wallet2 wpbc_icn_insert_chart_outlined00"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Prices for Bookings','booking'); ?></span>
					</a>
				</h1>
				<?php
				if ( ! class_exists( 'wpdev_bk_biz_m' ) ) {

					?><div class="wpbc_ui_settings__text_right"><a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpbc_replace__js_scripts__to__tpl_scripts(  $params ['dismiss_x_button'] );
				}
				?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Setup pricing for your bookings.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Form Options Costs ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__form_options_costs( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_price_url() . '' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__form_options_costs">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_insert_chart_outlined"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Form Options Costs','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Set up automatic email notifications for different booking actions.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}



/**
 * Show Settings   = Add New Calendar =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__add_new_resource( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__add_new_resource"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__add_new_resource( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php
}

	/**
	 * Show   == Dashboard Card  -  Timeline View ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__add_new_resource( $params = array() ){

		if ( ! class_exists( 'wpdev_bk_personal' ) ) {
			$url = ' href="https://wpbookingcalendar.com/features/#booking-resources" ';
		} else {
			$url = ' href="' . esc_url( wpbc_get_resources_url() . '' ) . '" ';
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__add_new_resource">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-calendar-plus"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Add New Resource','booking'); ?></span>
					</a>
				</h1>
				<?php
				if ( ! class_exists( 'wpdev_bk_personal' ) ) {

					?><div class="wpbc_ui_settings__text_right"><a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpbc_replace__js_scripts__to__tpl_scripts(  $params ['dismiss_x_button'] );
				}
				?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Create unique calendars with individual availability.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

