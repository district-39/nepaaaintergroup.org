<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category UI Elements for Settings
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-09-14
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly
/*
// =====================================================================================================================
//  == Data Structure ==
// =====================================================================================================================
function wpbc_ui_settings__panel__data_structure(){

	$data_sections = array();

	$data_sections['calendar_settings'] = array( 'title' => __( 'Calendar Settings', 'booking' ), 'cards' => array() );
	$cards = array();
	$cards['calendar_look'] = array(
		'title'       => __( 'Calendar Look', 'booking' ),
		'description' => __( 'Set calendar skin, max months to scroll, calendar legend', 'booking' ),
		'icon'        => 'wpbc-bi-calendar2-range',
		'css'         => 'wpbc_ui_settings__card_divider_right',
		'url'         => 'javascript:void(0);',
		'onclick'     => "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );",
		'pro_url'     => ( ! class_exists( 'wpdev_bk_biz_s' ) ) ? '' : '1'
	);
	$data_sections['calendar_settings']['cards'] = $cards;

	return $data_sections;
}
*/

// =====================================================================================================================
//  Settings Panel  Structure:
// =====================================================================================================================
/**
 *
 *	<div class="wpbc_ui_settings__flex_container">
 *
 * 		<div class="wpbc_ui_settings__row">
 *			<div class="wpbc_ui_settings__panel">
 *
 *			  	<div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
 *					<div class="wpbc_ui_settings__center ">
 *						<a ...> Start Setup Wizard </a>
 *					</div>
 *				</div>
 *
 *		    </div>
 *		</div>
 *
 * 		<div class="wpbc_ui_settings__col">
 *			<div class="wpbc_ui_settings__panel">
 *
 *			  	<div class="wpbc_ui_settings__card">
 *					<div class="wpbc_ui_settings__center ">
 *						<a ...> Start Setup Wizard </a>
 *					</div>
 *				</div>
 *
 *		    </div>
 *		</div>
 *
 *	</div>
 */

/**
 * Start == Settings Panel Section ==
 *
 * @param $direction	=	'row' | 'col'
 *
 * @return void
 */
function wpbc_ui_settings__panel_start( $direction = 'row' ) {
	?>
	<div class="<?php echo ( 'col' === $direction ) ? 'wpbc_ui_settings__col' : 'wpbc_ui_settings__row'; ?>">
		<div class="wpbc_ui_settings__panel">
	<?php
}


/**
 * End  == Settings Panel Section ==
 * @return void
 */
function wpbc_ui_settings__panel_end() {
	?>
		</div>
	</div>
 	<?php
}


// =====================================================================================================================
//  == Top Path ==
// =====================================================================================================================

/**
 * Top Path Line
 *
 * @return void
 */
function wpbc_ui_settings__top_path() {

	?>
	<div class="wpbc_settings_path">
		<?php /* ?><div class="wpbc_settings_path_el" style="border: 1px solid #d0d0d0;margin-right: 15px;border-radius: 50%;aspect-ratio: 1 / 1;text-align: center;display: none;"> <a><i class="menu_icon icon-1x wpbc_icn_navigate_before"></i></a></div><?php */ ?>
		<div class="wpbc_settings_path_el">
			<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_dashboard_tab a' ,'#wpbc_general_settings_dashboard_metabox', '<?php echo esc_attr__( 'Dashboard', 'booking' ); ?>' );" href="javascript:void(0);"><?php
				esc_html_e('Settings', 'booking');
			?></a>
		</div>
		<div class="wpbc_settings_path_el"><i class="menu_icon icon-1x wpbc_icn_navigate_next"></i></div>
		<div class="wpbc_settings_path_el wpbc_settings_path_el_active">
			<a onclick="javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_dashboard_tab a' ,'#wpbc_general_settings_dashboard_metabox', '<?php echo esc_attr__( 'Dashboard', 'booking' ); ?>' );" href="javascript:void(0);"><?php
				esc_html_e('Dashboard', 'booking');
			?></a>
		</div>
		<?php wpbc_ui_settings_panel__top_path__version(); ?>
	</div>
	<?php
}


// =====================================================================================================================
//  == All Settings Panels ==
// =====================================================================================================================

function wpbc_ui_settings__panel__all_settings_panels( $params = array() ) {

	$defaults = array(
						'is_use_permalink' => false
					);
	$params = wp_parse_args( $params, $defaults );

	?><style type="text/css">
	@media (max-width: 782px) {
	  .wpbc_settings_flex_container .wpbc_settings_flex_container_left:not(.wpbc_container_collapsed) {
		display:none;
	  }
	}
	</style><?php

	?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__calendar"><?php esc_html_e( 'Calendar Settings', 'booking' ); ?></div><?php

	wpbc_ui_settings__panel__calendar( $params );

	?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__form"><?php esc_html_e( 'Booking Form', 'booking' ); ?></div><?php
	wpbc_ui_settings__panel__form( $params );


	?><div class="wpbc_ui_settings__flex_container" style="justify-content: space-between;">
		<div style="flex:1 1 49%;margin-right:2%;">
			<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__booking_confirmation"><?php esc_html_e( 'Booking Summary', 'booking' ); ?></div>
			<?php wpbc_ui_settings__panel__booking_confirmation( $params ); ?>
		</div>
		<div style="flex:1 1 49%;">
			<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__notifications"><?php esc_html_e( 'Notifications', 'booking' ); ?></div>
			<?php wpbc_ui_settings__panel__notifications( $params ); ?>
		</div>
	</div><?php

	?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__sync_setu"><?php esc_html_e( 'Sync Bookings', 'booking' ); ?></div><?php
	wpbc_ui_settings__panel__sync_setup( $params );



	// -- Dismiss ----------------------------------------------------------------------------------------------
	$dismiss_id       = 'wpbc_is_dismissed__card__payment_setup';
	$dismiss_x_button = '';
	$is_panel_visible = true;
	if ( ( ! class_exists( 'wpdev_bk_biz_s' ) ) && ( ! wpbc_is_this_demo() ) ) {
		$is_panel_visible = wpbc_is_dismissed_panel_visible( $dismiss_id );
		if ( $is_panel_visible ) {
			ob_start();
			?><div class="wpbc_dismiss_x__in_panel_card"><?php $is_panel_visible = wpbc_is_dismissed( $dismiss_id );  ?></div><?php		// Close 'x' Button
			$dismiss_x_button = ob_get_clean();
		}
	}
	?><div  id="<?php echo esc_attr( $dismiss_id ); ?>" style="flex:1 1 100%;<?php echo ( ! $is_panel_visible ) ? 'display:none;' : ''; ?>"><?php
		?><div class="wpbc_dismiss_x__in_panel"><?php
			?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__payment_setup"><?php esc_html_e( 'Payment Setup', 'booking' ); ?></div><?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $dismiss_x_button;
		?></div><?php

		wpbc_ui_settings__panel__payment_setup( $params );
	?></div><?php


	?><div class="wpbc_ui_settings__flex_container" style="justify-content: space-between;">
		<?php
			// -- Dismiss ----------------------------------------------------------------------------------------------
			$dismiss_id       = 'wpbc_is_dismissed__card__search_availability';
			$dismiss_x_button = '';
			$is_panel_visible = true;
			if ( ( ! class_exists( 'wpdev_bk_biz_l' ) ) && ( ! wpbc_is_this_demo() ) ) {
				$is_panel_visible = wpbc_is_dismissed_panel_visible( $dismiss_id );
				if ( $is_panel_visible ) {
					ob_start();
					?><div class="wpbc_dismiss_x__in_panel_card"><?php $is_panel_visible = wpbc_is_dismissed( $dismiss_id );  ?></div><?php		// Close 'x' Button
					$dismiss_x_button = ob_get_clean();
				}
			}
		?>
		<div  id="<?php echo esc_attr( $dismiss_id ); ?>" style="flex:1 1 49%;margin-right:2%;<?php echo ( ! $is_panel_visible ) ? 'display:none;' : ''; ?>">
			<div class="wpbc_dismiss_x__in_panel">
				<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__search_availability"><?php esc_html_e( 'Search Availability', 'booking' ); ?></div>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $dismiss_x_button; ?>
			</div>
			<?php wpbc_ui_settings__panel__search_availability( $params ); ?>
		</div>
		<?php
			// -- Dismiss ----------------------------------------------------------------------------------------------
			$dismiss_id       = 'wpbc_is_dismissed__card__mu';
			$dismiss_x_button = '';
			$is_panel_visible = true;
			if ( ( ! class_exists( 'wpdev_bk_multiuser' ) ) && ( ! wpbc_is_this_demo() ) ) {
				$is_panel_visible = wpbc_is_dismissed_panel_visible( $dismiss_id );
				if ( $is_panel_visible ) {
					ob_start();
					?><div class="wpbc_dismiss_x__in_panel_card"><?php $is_panel_visible = wpbc_is_dismissed( $dismiss_id );  ?></div><?php		// Close 'x' Button
					$dismiss_x_button = ob_get_clean();
				}
			}
		?>
		<div  id="<?php echo esc_attr( $dismiss_id ); ?>" style="flex:1 1 49%;<?php echo ( ! $is_panel_visible ) ? 'display:none;' : ''; ?>">
			<div class="wpbc_dismiss_x__in_panel">
				<div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__mu"><?php esc_html_e( 'Multiuser Options', 'booking' ); ?></div>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $dismiss_x_button; ?>
			</div>
			<?php wpbc_ui_settings__panel__mu( $params ); ?>
		</div>
	</div><?php



	?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__manage_bookings"><?php esc_html_e( 'Manage Bookings', 'booking' ); ?></div><?php
	wpbc_ui_settings__panel__manage_bookings( $params );


	?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__admin_panel"><?php esc_html_e( 'Admin Panel', 'booking' ); ?></div><?php
	wpbc_ui_settings__panel__admin_panel( $params );

	?><div class="wpbc_ui_settings__panel__up_header wpbc_ui_settings__panel__up_header__advanced_settings"><?php esc_html_e( 'Advanced', 'booking' ); ?></div><?php
	wpbc_ui_settings__panel__advanced_settings( $params );


}


// =====================================================================================================================
//  == Is Dismissed Panel ==
// =====================================================================================================================
function wpbc_is_dissmissed__in_settings_panel_card( $dismiss_id , $class_name ){

    $is_panel_visible = true;
	$dismiss_x_button = '';

	if ( ( ! class_exists( $class_name ) ) && ( ! wpbc_is_this_demo() ) ) {

		$is_panel_visible = wpbc_is_dismissed_panel_visible( $dismiss_id );

		if (  $is_panel_visible ) {
			ob_start();
			?><div class="wpbc_dismiss_x__in_panel_card"><?php $is_panel_visible = wpbc_is_dismissed( $dismiss_id );  ?></div><?php		// Close 'x' Button
			$dismiss_x_button = ob_get_clean();
		}
	}
	return array( $is_panel_visible, $dismiss_x_button );
}


// ---------------------------------------------------------------------------------------------------------------------
//  Panel  == Calendar ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings Statistic | Agenda Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__calendar( $params = array() ) {

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__calendar"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__calendar( $params );
			wpbc_ui_settings_panel__card__days_selection( $params );
			wpbc_ui_settings_panel__card__change_over_days( $params );

			wpbc_ui_settings_panel__card__tooltips_in_days( $params );
			wpbc_ui_settings_panel__card__general_availability( $params );
			wpbc_ui_settings_panel__card__capacity( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Calendar  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__calendar( $params = array() ){

		$title   = esc_attr__( 'Calendar Settings', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_calendar_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick   = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_calendar_tab a' ,'#wpbc_general_settings_calendar_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__calendar">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-calendar2-range"></i>
				<h1>
					<a  onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $permalink; ?>">
					<?php esc_html_e( 'Customize various calendar settings, specify how many months can be scrolled, and defining the legend.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Days Selection  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__days_selection( $params = array() ){

		$title   = esc_attr__( 'Days Selection', 'booking' );
		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_calendar_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_calendar_tab a' ,'#wpbc_general_settings_calendar_metabox', '" . $title . "' );";
			$onclick .= "wpbc_blink_element('.wpbc_tr_set_gen_booking_type_of_day_selections', 4, 350); ";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__days_selection">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-calendar3-week"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $permalink; ?>"> <?php esc_html_e( 'Choose how users can select days: single day, multiple days, or a date range with minimum and maximum limits.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Change Over Days  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__change_over_days( $params = array() ){

		$dismiss_id = 'wpbc_is_dismissed__card__change_over_days';
		list( $is_panel_visible, $dismiss_x_button ) = wpbc_is_dissmissed__in_settings_panel_card( $dismiss_id, 'wpdev_bk_biz_s' );
		if ( ! $is_panel_visible ) { return; }

		$title = esc_attr__( 'Changeover Days', 'booking' );

		if (  class_exists( 'wpdev_bk_biz_s' ) ) {
			$onclick = ' onclick="';
		    $onclick .= "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_calendar_tab a' ,'#wpbc_general_settings_calendar_metabox', '" . $title . "' );";
			//$onclick .= " jQuery( '#type_of_day_selections_range').prop('checked',true).trigger('change'); ";			// Enable Range Days selection
			//$onclick .= " jQuery( '#range_selection_type_dynamic').prop('checked',true).trigger('change'); ";			// Enable Range Days selection
			$onclick .= " wpbc_blink_element('.wpbc_tr_set_gen_booking_range_selection_time_is_active', 4, 350); ";
			$onclick .= " wpbc_scroll_to('.wpbc_tr_set_gen_booking_range_selection_time_is_active' ); ";
			$onclick .= '" ';

			$url     = ' href="javascript:void(0);" ';

			if ( ! empty( $params['is_use_permalink'] ) ) {
				$url     = ' href="'. esc_url( wpbc_get_settings_url() ) . '&scroll_to_section=wpbc_general_settings_calendar_tab" ';			// Direct link
				$onclick = "";
			}

		} else {
			$onclick = '';
			$url = '  href="https://wpbookingcalendar.com/features/#change-over-days"  ';
		}

		?><div id="<?php echo esc_attr( $dismiss_id ); ?>" class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__change_over_days">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_flip"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $onclick; ?> >
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_biz_s' ) ) { ?>
				<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#change-over-days" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
				<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $dismiss_x_button; ?>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black"  <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?> <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $onclick; ?> > <?php esc_html_e( 'Set up check-in and check-out days for multi-day bookings, with visual indicators like diagonal or vertical lines. Ideal for bookings that require split days.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}

	/**
	 * Show   == Dashboard Card  -  Tooltips in Days  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__tooltips_in_days( $params = array() ){

		$title   = esc_attr__( 'Day Tooltips', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_days_tooltips_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_days_tooltips_tab a' ,'#wpbc_general_settings_days_tooltips_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__tooltips_in_days">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-chat-square-dots"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $permalink; ?>"> <?php esc_html_e( 'Configure tooltips to display information like booked times, costs, details, and availability when hovering over calendar days.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  General Availability  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__general_availability( $params = array() ){

		$title   = esc_attr__( 'General Availability', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_availability_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_availability_tab a' ,'#wpbc_general_settings_availability_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__general_availability">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-calendar-check bi-calendar2-day"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Define unavailable weekdays, add booking buffers, and customize unavailable day options.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  General Availability  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__capacity( $params = array() ){

		$title   = esc_attr__( 'Booking Capacity', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_capacity_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_capacity_tab a' ,'#wpbc_general_settings_capacity_metabox,#wpbc_general_settings_capacity_upgrade_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__capacity">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_filter_none"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Manage the ability to accept multiple bookings for the same date or time slot.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Form =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__form( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__form"><?php

		wpbc_ui_settings__panel_start();

		if ( WPBC_Frontend_Settings::is_bfb_enabled( null ) ) {
			wpbc_ui_settings_panel__card__bfb( $params );
		} else {
			wpbc_ui_settings_panel__card__form_fields( $params );
			wpbc_ui_settings_panel__card__time_fields( $params );
			wpbc_ui_settings_panel__card__time_slots_options( $params );
			wpbc_ui_settings_panel__card__form_options( $params );
		}

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Booking Form Fields ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__form_fields( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=form' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__form_fields">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_dashboard"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Booking Form Fields','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Add, remove, or customize fields in your booking form.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


	/**
	 * Show   == Dashboard Card  -  Booking Form Fields ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__bfb( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=builder_booking_form' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__form_fields">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_dashboard"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Drag & Drop Booking Form Builder','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Add, remove, or customize fields in your booking form.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Time Fields  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__time_fields( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=form&field_type=timeslots' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__time_fields">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_schedule"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Time Slot Configuration','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Enable and configure time selection fields in your booking form.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


	/**
	 * Show   == Dashboard Card  -  Form Options  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__form_options( $params = array() ){

		$title   = esc_attr__( 'Booking Form Options', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_form_tab';
			$onclick   = '';
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_form_tab a' ,'#wpbc_general_settings_form_metabox', '" . $title . "' );";
		}
		$onclick   = '';
		$permalink = wpbc_get_settings_url() . '&tab=form&subtab=form_options';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__form_options">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-toggle2-off"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
					<?php esc_html_e( 'Enable CAPTCHA, auto-fill fields, and customize the form\'s color theme.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}

	/**
	 * Show   == Dashboard Card  -  Time Slots Options  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__time_slots_options( $params = array() ){

		$title   = esc_attr__( 'Time Slot Appearance', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_time_slots_tab';
			$onclick   = '';
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_time_slots_tab a' ,'#wpbc_general_settings_time_slots_metabox', '" . $title . "' );";
		}
		$permalink = wpbc_get_settings_url() . '&tab=color_themes';
		$onclick   = '';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__time_slots_options">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_more_time"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Choose how time slots are shown in the form: as a dropdown list or a time picker, and set a color theme.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Booking Confirmation =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__booking_confirmation( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__booking_confirmation"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__booking_confirmation( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Booking Confirmation  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__booking_confirmation( $params = array() ){

		$title   = esc_attr__( 'Booking Confirmation', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_booking_confirmation_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_confirmation_tab a' ,'#wpbc_general_settings_booking_confirmation_metabox,#wpbc_general_settings_booking_confirmation_left_metabox,#wpbc_general_settings_booking_confirmation_right_metabox,#wpbc_general_settings_booking_confirmation_help_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__booking_confirmation">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-card-checklist 0_icn_grading 0receipt"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Customize how the booking summary is displayed after a booking is created.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Emails =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__notifications( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__notifications"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__emails( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Emails ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__emails( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=email' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__emails">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-envelope-at 0paper _icn_mail_outline"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Emails','booking'); ?></span>
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

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Sync =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__sync_setup( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__sync_setup"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__sync_options( $params );
			wpbc_ui_settings_panel__card__import_ics( $params );
			wpbc_ui_settings_panel__card__export_ics( $params );
			wpbc_ui_settings_panel__card__import_google_cal( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Sync Options ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__sync_options( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=sync&subtab=general' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__sync_options">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_sync_alt"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Sync Options','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Configure import/export rules, timezones, and which fields to include in sync operations.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


	/**
	 * Show   == Dashboard Card  -  Import - .ics ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__import_ics( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=sync&subtab=import' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__import_ics">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-box-arrow-in-down-right"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Import Bookings via .ics','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Set up and configure the import of events using .ics feeds.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


	/**
	 * Show   == Dashboard Card  -  Export - .ics ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__export_ics( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=sync&subtab=export' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__export_ics">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-box-arrow-up-right"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Export Bookings via .ics','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Set up and configure the export of bookings into .ics feeds.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


	/**
	 * Show   == Dashboard Card  -  Import - .ics ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__import_google_cal( $params = array() ){

		$onclick = '';//  ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
		$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=sync&subtab=gcal' ) . '" ';

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__import_google_cal">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_event"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Import Google Calendar Events','booking'); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Set up and configure the import of Google Calendar events using the API.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Payment Setup =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__payment_setup( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__payment_setup"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__payment_setup( $params );
			wpbc_ui_settings_panel__card__currency( $params );
			wpbc_ui_settings_panel__card__payment_options( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Payment Gateways  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__payment_setup( $params = array() ){

		if (  class_exists( 'wpdev_bk_biz_s' ) ) {
			$onclick = '';// ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
			$url     = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=payment' ) . '" ';
		} else {
			$onclick = '';
			$url = '  href="https://wpbookingcalendar.com/features/#payments"  ';
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__payment_setup">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-credit-card-2-back"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $onclick; ?> >
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Payment Gateways','booking'); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_biz_s' ) ) { ?>
				<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#payments" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?> <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $onclick; ?> >
					<?php
					/* translators: 1: ... */
					echo wp_kses_post( sprintf( __(  'Set up payment methods such as %s','booking') , 'Stripe, PayPal, iDeal, Authorize.Net, Opayo - Elavon, Bank Transfer, Pay in Cash, iPay88, ...') ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Currency  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__currency( $params = array() ){

		if (  class_exists( 'wpdev_bk_biz_s' ) ) {
			$onclick = '';// ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
			$url     = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=payment#wpbc_settings_payment_currency_metabox' ) . '" ';
		} else {
			$onclick = '';
			$url = '  href="https://wpbookingcalendar.com/features/#payments"  ';
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__currency">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-currency-exchange _icn_attach_money"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $onclick; ?> >
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Set Currency','booking'); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_biz_s' ) ) { ?>
				<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#payments" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?> <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $onclick; ?> > <?php esc_html_e( 'Configure the currency used for bookings and payments.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Payment Options  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__payment_options( $params = array() ){

		if (  class_exists( 'wpdev_bk_biz_s' ) ) {
			$onclick = '';// ' onclick="' . "javascript:wpbc_navigation_click_show_section(this,'#wpbc_general_settings_calendar_metabox' );" . '" ';
			$url     = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=payment#wpbc_settings_payment_options_metabox' ) . '" ';
		} else {
			$onclick = '';
			$url = '  href="https://wpbookingcalendar.com/features/#payments"  ';
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__payment_options">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-cash-coin wallet2"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $onclick; ?> >
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Payment Options','booking'); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_biz_s' ) ) { ?>
				<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#payments" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black"  <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?> <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $onclick; ?> >
					<?php esc_html_e('Configure cost per day or night, and set up different payment methods and pricing options.' , 'booking'); ?>
				</a>
			</div>
		</div><?php

	}



/**
 * Show Settings   = Search Availability =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__search_availability( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__search_availability"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__search_availability( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  Search Availability  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__search_availability( $params = array() ){

		if (  class_exists( 'wpdev_bk_biz_l' ) ) {
			$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=search' ) . '" ';
		} else {
			$url = ' href="https://wpbookingcalendar.com/features/#search" ';
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__search_availability">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_youtube_searched_for"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Search Availability','booking'); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_biz_l' ) ) { ?>
					<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#search" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black"  <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Configure the layout and functionality of both the search form and search results', 'booking' ); ?>.
				</a>
			</div>
		</div><?php
	}

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   =  MultiUser  =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__mu( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__mu"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__mu( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  MultiUser ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__mu( $params = array() ){

		if (  class_exists( 'wpdev_bk_multiuser' ) ) {
			$url = ' href="' . esc_url( wpbc_get_settings_url() . '&tab=users' ) . '" ';
		} else {
			$url = ' href="https://wpbookingcalendar.com/features/#multiuser" ';
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__mu">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_people_alt"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?>>
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html__('Multiuser Admin Panels','booking'); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_multiuser' ) ) { ?>
				<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#multiuser" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?>> <?php esc_html_e( 'Enable separate booking admin panels for different WordPress users.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}


// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Manage Bookings =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__manage_bookings( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__manage_bookings"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__timeline_front_end( $params );
			wpbc_ui_settings_panel__card__manage_bookings( $params );
			wpbc_ui_settings_panel__card__auto_cancel_approve( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}

	/**
	 * Show   == Dashboard Card  -  TimeLine Front-End  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__timeline_front_end( $params = array() ){

		$title   = esc_attr__( 'Timeline (Front-End)', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_booking_timeline_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_timeline_tab a' ,'#wpbc_general_settings_booking_timeline_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__timeline_front_end">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_rotate_90 wpbc_icn_align_vertical_bottom"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Customize timeline options, including the display of booking details.', 'booking' ); ?>
					<?php if ( ! class_exists( 'wpdev_bk_personal' ) ) { ?>
					<div class="wpbc_ui_settings__text_right" style="font-size: 10px;"><a href="https://wpbookingcalendar.com/features/#show-booking-details" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
					<?php } ?>

				</a>
			</div>
		</div><?php
	}

	/**
	 * Show   == Dashboard Card  -  Manage Bookings  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__manage_bookings( $params = array() ){

		$dismiss_id = 'wpbc_is_dismissed__card__manage_bookings';
		list( $is_panel_visible, $dismiss_x_button ) = wpbc_is_dissmissed__in_settings_panel_card( $dismiss_id, 'wpdev_bk_personal' );
		if ( ! $is_panel_visible ) { return; }

		$title   = esc_attr__( 'Manage Bookings', 'booking' );

		if (  class_exists( 'wpdev_bk_personal' ) ) {
			$onclick = ' onclick="';
		    $onclick .= "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_bookings_options_tab a' ,'#wpbc_general_settings_bookings_options_metabox', '" . $title . "' );";
			//$onclick .= " wpbc_blink_element('.wpbc_tr_set_gen_booking_range_selection_time_is_active', 4, 350); ";
			$onclick .= '" ';

			$url     = ' href="javascript:void(0);" ';

			if ( ! empty( $params['is_use_permalink'] ) ) {
				$url     = ' href="'. esc_url( wpbc_get_settings_url() ) . '&scroll_to_section=wpbc_general_settings_bookings_options_tab" ';
				$onclick   = "";
			}

		} else {
			$onclick = '';
			$url = '  href="https://wpbookingcalendar.com/features/#manage-bookings"  ';
		}

		?><div id="<?php echo esc_attr( $dismiss_id ); ?>" class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__manage_bookings">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_app_registration mode_edit_outline edit_calendar"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $onclick; ?> >
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_personal' ) ) { ?>
					<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/faq/configure-editing-cancel-payment-bookings-for-visitors/" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $dismiss_x_button; ?>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black"  <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?> <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $onclick; ?> > <?php esc_html_e( 'Allow customers to edit or cancel their own bookings.', 'booking' ); ?>
				</a>
			</div>
		</div><?php

	}

	/**
	 * Show   == Dashboard Card  -  Auto Cancellation / Auto Approval  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__auto_cancel_approve( $params = array() ){

		$dismiss_id = 'wpbc_is_dismissed__card__auto_cancel_approve';
		list( $is_panel_visible, $dismiss_x_button ) = wpbc_is_dissmissed__in_settings_panel_card( $dismiss_id, 'wpdev_bk_biz_s' );
		if ( ! $is_panel_visible ) { return; }

		$title = esc_attr__( 'Auto Cancellation / Auto Approval', 'booking' );

		if (  class_exists( 'wpdev_bk_biz_s' ) ) {
			$onclick = ' onclick="';
		    $onclick .= "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_auto_cancelation_approval_tab a' ,'#wpbc_general_settings_auto_cancelation_approval_metabox', '" . $title . "' );";
			//$onclick .= " wpbc_blink_element('.wpbc_tr_set_gen_booking_range_selection_time_is_active', 4, 350); ";
			$onclick .= '" ';
			$url     = ' href="javascript:void(0);" ';

			if ( ! empty( $params['is_use_permalink'] ) ) {
				$url     = ' href="'. esc_url( wpbc_get_settings_url() ) . '&scroll_to_section=wpbc_general_settings_auto_cancelation_approval_tab" ';
				$onclick   = "";
			}

		} else {
			$onclick = '';
			$url = '  href="https://wpbookingcalendar.com/features/#auto-cancellation"  ';
		}

		?><div id="<?php echo esc_attr( $dismiss_id ); ?>" class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__auto_cancel_approve">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_published_with_changes"></i>
				<h1>
					<a <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $url; ?> <?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $onclick; ?> >
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
				<?php if ( ! class_exists( 'wpdev_bk_biz_s' ) ) { ?>
					<div class="wpbc_ui_settings__text_right"><a href="https://wpbookingcalendar.com/features/#auto-cancellation" class="wpbc_ui_settings__text_pro" target="_blank"><?php esc_html_e('Pro','booking'); ?></a></div>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $dismiss_x_button; ?>
				<?php } ?>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black"  <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $url; ?> <?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $onclick; ?> > <?php esc_html_e( 'Enable automatic approval or cancellation of bookings.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Admin Panel =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__admin_panel( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__admin_panel"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__admin_panel( $params );
			wpbc_ui_settings_panel__card__plugin_menu_permistions( $params );
			wpbc_ui_settings_panel__card__translations( $params );
			wpbc_ui_settings_panel__card__timeline_back_end( $params );
			wpbc_ui_settings_panel__card__date_time_format( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}


	/**
	 * Show   == Dashboard Card  -  Admin Panel  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__admin_panel( $params = array() ){

		$title   = esc_attr__( 'Admin Panel', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_booking_listing_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_listing_tab a' ,'#wpbc_general_settings_booking_listing_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__admin_panel">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-speedometer2"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Configure various options and settings in the admin panel.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}

	/**
	 * Show   == Dashboard Card  -  TimeLine Back-End  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__timeline_back_end( $params = array() ){

		$title   = esc_attr__('Timeline View','booking') . ' (' . esc_attr__('Back-End','booking') . ')';

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_booking_calendar_overview_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_booking_calendar_overview_tab a' ,'#wpbc_general_settings_booking_calendar_overview_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__timeline_back_end">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-bar-chart-steps _icn_timeline 0wpbc_icn_rotate_45 0wpbc_icn_align_vertical_bottom"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Customize timeline options in the admin panel, including displaying booking details.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


	/**
	 * Show   == Dashboard Card  -  TimeLine Back-End  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__date_time_format( $params = array() ){

		$title   = esc_attr__( 'Date and Time Formats', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_datestimes_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_datestimes_tab a' ,'#wpbc_general_settings_datestimes_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__date_time_format">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-braces-asterisk _icn_password"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Customize the display format for dates and times.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


	/**
	 * Show   == Dashboard Card  -  Plugin Menu / Permissions  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__plugin_menu_permistions( $params = array() ){

		$title   = esc_attr__( 'Plugin Menu / Permissions', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_permissions_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_permissions_tab a' ,'#wpbc_general_settings_permissions_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__plugin_menu_permistions">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-key"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Set user permissions for accessing plugin menu pages and configure the menu position.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


	/**
	 * Show   == Dashboard Card  -  Translations  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__translations( $params = array() ){

		$title   = esc_attr__( 'Translations', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_translations_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_translations_tab a' ,'#wpbc_general_settings_translations_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__translations">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_translate 0wpbc-bi-translate"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Choose whether to use local translations or WordPress translations.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings   = Advanced Settings =  Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__advanced_settings( $params = array() ){

	?><div class="wpbc_ui_settings__flex_container wpbc_ui_settings__panel__advanced_settings"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__advanced_settings( $params );
			wpbc_ui_settings_panel__card__uninstall( $params );
			wpbc_ui_settings_panel__card__info_news( $params );
			//wpbc_ui_settings_panel__card__tools( $params );

		wpbc_ui_settings__panel_end();

	?></div><?php

}


	/**
	 * Show   == Dashboard Card  -  Advanced  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__advanced_settings( $params = array() ){

		$title   = esc_attr__( 'Advanced Options', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_advanced_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_advanced_tab a' ,'#wpbc_general_settings_advanced_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__advanced_settings">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-gear _icn_adjust 0wpbc-bi-translate"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Configure loading of CSS/JavaScript files, set the number of simultaneous requests, and other advanced options.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}

	/**
	 * Show   == Dashboard Card  -  Advanced  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__uninstall( $params = array() ){

		$title   = esc_attr__( 'Uninstall / Deactivation', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_uninstall_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_uninstall_tab a' ,'#wpbc_general_settings_uninstall_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__uninstall">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-trash  _icn_adjust 0wpbc-bi-translate"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Enable the option to fully erase booking data when the plugin is deactivated.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


	/**
	 * Show   == Dashboard Card  -  Advanced  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__info_news( $params = array() ){

		$title   = esc_attr__( 'Plugin Info & News', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_information_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_information_tab a' ,'#wpbc_general_settings_information_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__info_news">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc_icn_newspaper -bi-newspaper _icn_news 0wpbc-bi-translate"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'Stay informed with the latest news and details about the plugin.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}


	/**
	 * Show   == Dashboard Card  -  Advanced  ==
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__tools( $params = array() ){

		$title   = esc_attr__( 'Tools', 'booking' );

		if ( ! empty( $params['is_use_permalink'] ) ) {
			$permalink = wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_help_tab';
			$onclick   = "";
		} else {
			$permalink = 'javascript:void(0);';
			$onclick = "javascript:wpbc_ui_settings__panel__click( '#wpbc_general_settings_help_tab a' ,'#wpbc_general_settings_help_metabox', '" . $title . "' );";
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_text_small wpbc_ui_settings__card_divider_right wpbc_ui_settings_panel__card__tools">
			<div class="wpbc_ui_settings__text_row">
				<i class="menu_icon icon-1x wpbc-bi-wrench-adjustable-circle 0wpbc-bi-translate"></i>
				<h1>
					<a onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>">
						<span class="0wpbc_ui_settings__text_color__black2"><?php echo esc_html( $title ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row">
				<a class="wpbc_ui_settings__text_color__black" onclick="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $onclick; ?>" href="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $permalink; ?>"> <?php esc_html_e( 'View system information and recover lost bookings or booking resources.', 'booking' ); ?>
				</a>
			</div>
		</div><?php
	}





// =====================================================================================================================
//  == Other Panels ==
// =====================================================================================================================

// ---------------------------------------------------------------------------------------------------------------------
//  Panel  == Plugin Version | & Buttons ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings Statistic | Agenda Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__plugin_version(){

	?><div class="wpbc_ui_settings__flex_container"><?php

		wpbc_ui_settings__panel_start();

			wpbc_ui_settings_panel__card__setup_wizard();

			wpbc_ui_settings_panel__card__transaltion_download();

			wpbc_ui_settings_panel__card__transaltion_status();

			wpbc_ui_settings_panel__card__version();

		wpbc_ui_settings__panel_end();

	?></div><?php
}

// ---------------------------------------------------------------------------------------------------------------------
//  Panel  == Statistic | Agenda ==
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show Settings Statistic | Agenda Panel
 *
 * @return void
 */
function wpbc_ui_settings__panel__statistic(){

	$is_panel_visible = wpbc_is_dismissed_panel_visible( 'wpbc_dashboard_section_statistic' );        					// FixIn: 9.9.0.8.

	if (! $is_panel_visible) {
		return;
	}

	$statistic_cards_arr = ( $is_panel_visible ) ? wpbc_db_dashboard_get_bookings_count_arr() : array();


	?><div id="wpbc_dashboard_section_statistic" class="wpbc_ui_settings__flex_container"><?php

		wpbc_ui_settings__panel_start();

		?><div style="padding: 5px 8px 0 0;flex: 1 1 100%;margin-bottom: -100%;"><?php
			$is_panel_visible = wpbc_is_dismissed( 'wpbc_dashboard_section_statistic' );        						// Close Panel
		?></div><?php

				//foreach ( array("all","new","pending","approved","booking_today","was_made_today","check_in_today","check_out_today","check_in_tomorrow","check_out_tomorrow") as $card_name ) {
				foreach ( array( "all", "new", "pending", "approved" ) as $card_name ) {			// FixIn: 10.6.2.2.
					wpbc_ui_settings_panel__card__statistic( $card_name , $statistic_cards_arr );
				}
		 wpbc_ui_settings__panel_end();

		 /* ?>
		<div class="wpbc_ui_settings__row">
			<div class="wpbc_ui_settings__panel"><?php
				foreach ( array("check_in_today","check_out_today","check_in_tomorrow","check_out_tomorrow") as $card_name ) {
					wpbc_ui_settings_panel__card__statistic( $card_name , $statistic_cards_arr );
				}
		    ?></div>
		</div>
		<?php */ ?>
	</div>
	<?php
}

	function wpbc_ui_settings_panel__card__setup_wizard() {
		?>
		<div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
			<div class="wpbc_ui_settings__center  wpbc_container wpbc_container_booking_form">
				<a class="wpbc_button_light button-primary wpbc_button_green tooltip_top" style="padding: 12px 24px;"
				   title="<?php echo esc_attr( sprintf( __('We\'ll guide you through the steps to set up WP Booking Calendar on your site.','booking'), '<strong>WP Booking Calendar</strong>' ) ); ?>"
				   href="<?php echo esc_url( wpbc_get_settings_url() . '&wpbc_setup_wizard=reset&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' ) ); ?>"><?php
					esc_html_e( 'Start Setup Wizard', 'booking' )
				?></a>
			</div>
		</div>
		<?php
	}

	function wpbc_ui_settings_panel__card__transaltion_download() {
		?>
		<div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
			<div class="wpbc_ui_settings__center">
				<a class="button tooltip_top"
				   title="<?php
				   /* translators: 1: ... */
				   echo esc_attr( sprintf( __( 'Download translation PO files from %s and update the current translation version.', 'booking' ), 'wp.org / wpbookingcalendar.com' ) );?>"
				   href="<?php echo  esc_url( wpbc_get_settings_url() . '&system_info=show&_wpnonce='. wp_create_nonce( 'wpbc_settings_url_nonce' ) )
									.'&update_translations=1#wpbc_general_settings_system_info_metabox'; ?>"><?php
					esc_html_e( 'Update Translations', 'booking' )
				?></a>
			</div>
		</div>
		<?php
	}

	function wpbc_ui_settings_panel__card__transaltion_status() {
		?>
		<div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
			<div class="wpbc_ui_settings__center">
				<a class="button tooltip_top"
				   title="<?php echo esc_attr( __( 'Show exist translations status', 'booking' ) );?>"
				   href="<?php echo  esc_url( wpbc_get_settings_url() . '&system_info=show&_wpnonce='. wp_create_nonce( 'wpbc_settings_url_nonce' ) )
									 .'&show_translation_status=1#wpbc_general_settings_system_info_metabox'; ?>"><?php
					esc_html_e( 'Show translations status', 'booking' )
				?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Show Version card in top  panel
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__version(){
		// Version ---------------------------------------------------------------------------------------------------------

		$version_title = '';

		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$version_title .= wpbc_dashboard_info_get_version_type_readable();
		}
		if ( wpbc_is_this_demo() ) {
			$version_title .= ' (Demo)';
		} else {
			if ( 'free' != wpbc_dashboard_info_get_version_type() ) {
				$version_title .= ' (' . __( 'for', 'booking' ) . ' ' . trim( wpbc_dashboard_info_get_version_type_sites() ) . ')';
			}
		}

		?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right" style="align-items: center;">
			<div class="wpbc_ui_settings__text_row" style="justify-content: center;margin-left:-1em;">
				<i class="menu_icon icon-1x wpbc_icn_numbers -bi-balloon-heart bi-activity"></i>
				<h1>
					<a class="" href="https://wpbookingcalendar.com/changelog/#<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpbc_get_slug_format( trim( wpbc_dashboard_info_get_version_type_readable() . '_' . wpbc_dashboard_info_get_version_type_sites() . '_' . esc_attr( WP_BK_VERSION_NUM ) ) ); ?>">
					<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_attr( WP_BK_VERSION_NUM ); ?></span>
					</a>
				</h1>
			</div>
			<div class="wpbc_ui_settings__text_row" style="justify-content: center;">
				<a  class="wpbc_ui_settings__text_color__purple2"
					href="https://wpbookingcalendar.com/faq/difference-single-developer-multi-site/#<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wpbc_get_slug_format( trim( wpbc_dashboard_info_get_version_type_readable() . '_' . wpbc_dashboard_info_get_version_type_sites() . '_' . esc_attr( WP_BK_VERSION_NUM ) ) ); ?>"
				><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $version_title
				?></a>
			</div>
		</div><?php
	}

	/**
	 * Show Version card in top  panel
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__top_path__version(){
		// Version ---------------------------------------------------------------------------------------------------------

		$version_title = '';

		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$version_title .= wpbc_dashboard_info_get_version_type_readable();
		}
		if ( wpbc_is_this_demo() ) {
			$version_title .= ' (Demo)';
		} else {
			if ( 'free' != wpbc_dashboard_info_get_version_type() ) {
				$version_title .= ' (' . __( 'for', 'booking' ) . ' ' . trim( wpbc_dashboard_info_get_version_type_sites() ) . ')';
			}
		}

		?><style type="text/css">
			.wpbc_settings_path_el .wpbc_icn_numbers::before{
				font-size: 12px;
  				vertical-align: -.19em;
			}
		</style>
		<div class="wpbc_settings_path_el wpbc_ui_settings__flex_container" style="margin-left:auto;flex-flow: row nowrap;">
				<a class="" href="https://wpbookingcalendar.com/changelog/#<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo  wpbc_get_slug_format( trim( wpbc_dashboard_info_get_version_type_readable() . '_' . wpbc_dashboard_info_get_version_type_sites() .  '_' . esc_attr( WP_BK_VERSION_NUM ) ) ); ?>">
					<span class="wpbc_ui_settings__text_color__green"><i class="menu_icon icon-1x wpbc_icn_numbers -bi-balloon-heart bi-activity"></i> <?php echo esc_attr( WP_BK_VERSION_NUM ); ?></span>
				</a>
				<a  class="wpbc_ui_settings__text_color__purple2" style="margin-left:1em;"
					href="https://wpbookingcalendar.com/faq/difference-single-developer-multi-site/#<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo  wpbc_get_slug_format( trim( wpbc_dashboard_info_get_version_type_readable() . '_' . wpbc_dashboard_info_get_version_type_sites() .  '_' . esc_attr( WP_BK_VERSION_NUM ) ) ); ?>"
				><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $version_title
				?></a>
		</div><?php
	}

	/**
	 * Show statistic card in top  panel
	 *
	 * @param $card_name
	 * @param $statistic_cards_arr
	 *
	 * @return void
	 */
	function wpbc_ui_settings_panel__card__statistic( $card_name , $statistic_cards_arr ){

		if ( ( empty( $statistic_cards_arr ) ) || ( ! isset( $statistic_cards_arr[ $card_name ] ) ) ) {
			return;
		}

		$bk_admin_url = wpbc_get_bookings_url();
		$count_name   = $card_name;
		$count_num    = $statistic_cards_arr[ $card_name ];


		switch ( $count_name ) {
			case 'all':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_trending_up"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[0]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a class="wpbc_ui_settings__text_color__black2" style="font-weight: 600;"
						   href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[0]=3&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'All Bookings', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'new':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc-bi-eye"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_what_bookings=new&wh_trash=0&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a class="wpbc_ui_settings__text_color__blue" href="<?php echo esc_url( $bk_admin_url .'&wh_what_bookings=new&wh_trash=0&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'New (unverified) booking(s)', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'pending':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc-bi-slash-circle 0wpbc_icn_block"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_approved=0&wh_trash=0&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__orange" href="<?php echo esc_url( $bk_admin_url .'&wh_approved=0&wh_trash=0&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
							<?php esc_html_e( 'Pending booking(s)' ,'booking'); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'approved':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc-bi-check2-circle 0check-circle 0wpbc_icn_check_circle_outline"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_approved=1&wh_trash=0&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__green" href="<?php echo esc_url( $bk_admin_url .'&wh_approved=1&wh_trash=0&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
							<?php esc_html_e( 'Approved booking(s)' ,'booking'); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'booking_today':
				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_tune"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=1&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__green" href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=1&tab=vm_booking_listing&overwrite=1' ); ?>">
							<?php esc_html_e( 'Booking(s) for today' ,'booking');?>
						</a>
					</div>
				</div><?php

				break;

			case 'was_made_today':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_tune"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_modification_date[]=1&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__red" href="<?php echo esc_url( $bk_admin_url . '&wh_trash=0&wh_modification_date[]=1&wh_booking_date[]=3&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'New booking(s) made today', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'check_in_today':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_tune"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=10&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__red2" href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=10&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'Check in - Today', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'check_out_today':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_tune"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=11&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__blue2" href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=11&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'Check out - Today', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'check_in_tomorrow':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_tune"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=7&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__orange2" href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=7&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'Check in - Tomorrow', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;

			case 'check_out_tomorrow':

				?><div class="wpbc_ui_settings__card wpbc_ui_settings__card_divider_right">
					<div class="wpbc_ui_settings__text_row">
						<i class="menu_icon icon-1x wpbc_icn_tune"></i>
						<h1>
							<a href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=8&tab=vm_booking_listing&overwrite=1' ); ?>">
								<span class="wpbc_ui_settings__text_color__black2"><?php echo esc_html( $statistic_cards_arr[$count_name] ); ?></span>
							</a>
						</h1>
					</div>
					<div class="wpbc_ui_settings__text_row">
						<a  class="wpbc_ui_settings__text_color__green2" href="<?php echo esc_url( $bk_admin_url .'&wh_trash=0&wh_booking_date[]=8&tab=vm_booking_listing&overwrite=1' ); ?>"> <?php esc_html_e( 'Check out - Tomorrow', 'booking' ); ?>
						</a>
					</div>
				</div><?php

				break;
		}

	}