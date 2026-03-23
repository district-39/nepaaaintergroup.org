<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Admin  Top  Bar
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-09-03
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// =====================================================================================================================
// ==  Admin  Top  Bar  ==
// =====================================================================================================================



function wpbc_add__booking_menu__in__admin_top_bar(){

	global $wp_admin_bar;

	$current_user = wpbc_get_current_user();

	$curr_user_role = get_bk_option( 'booking_user_role_booking' );
	$level = 10;
	if ($curr_user_role == 'administrator')       $level = 10;
	else if ($curr_user_role == 'editor')         $level = 7;
	else if ($curr_user_role == 'author')         $level = 2;
	else if ($curr_user_role == 'contributor')    $level = 1;
	else if ($curr_user_role == 'subscriber')     $level = 0;

	$is_super_admin = apply_bk_filter('multiuser_is_user_can_be_here', false, 'only_super_admin');
	if (   ( ($current_user->user_level < $level) && (! $is_super_admin)  ) || !is_admin_bar_showing() ) return;


	$update_title = wpbc_get_svg_logo_for_html() . '&nbsp;' . 'Booking Calendar';

	$is_user_activated = apply_bk_filter( 'multiuser_is_current_user_active', true ); // FixIn: 6.0.1.17.
	$update_count      = wpbc_db_get_number_new_bookings();
	if ( ( $update_count > 0 ) && ( $is_user_activated ) ) {
		$update_title .= "&nbsp;<span class='booking-count bk-update-count' style='background: #f0f0f1;color: #2c3338;display: inline;padding: 2px 5px;font-weight: 600;border-radius: 10px;'>" . number_format_i18n( $update_count ) . '</span>';
	}

	$link_bookings = wpbc_get_bookings_url();
	$link_res      = wpbc_get_resources_url();
	$link_settings = wpbc_get_settings_url();

	// FixIn: 9.8.15.9.
	$wp_admin_bar->add_menu(
			array(
				'id' => 'wpbc_bar',
				'title' => $update_title ,
				'href' => wpbc_get_bookings_url()
				)
			);

	$wp_admin_bar->add_menu(
			array(
				'id' => 'wpbc_bar_bookings',
				'title' => __( 'Bookings', 'booking' ),
				'href' => wpbc_get_bookings_url(),
				'parent' => 'wpbc_bar',
			)
	);
		$wp_admin_bar->add_menu(
				array(
					'id' => 'wpbc_bar_booking_listing',
					'title' => __( 'Booking Listing', 'booking' ),
					'href' => wpbc_get_bookings_url() . '&tab=vm_booking_listing',
					'parent' => 'wpbc_bar_bookings',
				)
		);
		$wp_admin_bar->add_menu(
				array(
					'id' => 'wpbc_bar_calendar_overview',
					'title' => __( 'Timeline View', 'booking' ),
					'href' => wpbc_get_bookings_url() . '&tab=vm_calendar',
					'parent' => 'wpbc_bar_bookings',
				)
		);


	 $curr_user_role_settings = get_bk_option( 'booking_user_role_settings' );
	 $level = 10;
	 if ($curr_user_role_settings == 'administrator')       $level = 10;
	 else if ($curr_user_role_settings == 'editor')         $level = 7;
	 else if ($curr_user_role_settings == 'author')         $level = 2;
	 else if ($curr_user_role_settings == 'contributor')    $level = 1;
	 else if ($curr_user_role_settings == 'subscriber')     $level = 0;

	 if (   ( ($current_user->user_level < $level) && (! $is_super_admin)  ) || !is_admin_bar_showing() ) return;

	// Booking > Add booking page
	$wp_admin_bar->add_menu(
			array(
				'id' => 'wpbc_bar_new',
				'title' => __( 'Add booking', 'booking' ),
				'href' => wpbc_get_new_booking_url(),
				'parent' => 'wpbc_bar',
			)
	);

	// Booking > Availability page
	$wp_admin_bar->add_menu(
			array(
				'id' => 'wpbc_bar_availability',
				'title' => __( 'Availability', 'booking' ),
				'href' => wpbc_get_availability_url(),
				'parent' => 'wpbc_bar',
			)
	);
			$wp_admin_bar->add_menu(
				array(
					'id' => 'wpbc_bar_days_availability',
					'title' => __( 'Days Availability', 'booking' ),
					'href' => wpbc_get_availability_url() ,
					'parent' => 'wpbc_bar_availability'
				)
			);
			if ( class_exists( 'wpdev_bk_biz_m' ) ){
				$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_seasons_availability',
						'title' => __( 'Season Availability', 'booking' ),
						'href' => wpbc_get_availability_url() . '&tab=season_availability',
						'parent' => 'wpbc_bar_availability'
					)
				);

				$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_seasons_filters',
						'title' => __( 'Seasons', 'booking' ),
						'href' => wpbc_get_availability_url() . '',
						'parent' => 'wpbc_bar_availability'
					)
				);
			}
			if ( wpbc_is_mu_user_can_be_here( 'only_super_admin' ) )
				$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_general_availability',
						'title' => __( 'General Availability', 'booking' ),
						'href' => wpbc_get_settings_url() . '&scroll_to_section=wpbc_general_settings_availability_tab',
						'parent' => 'wpbc_bar_availability'
					)
				);

	// Booking > Prices page
	if ( class_exists( 'wpdev_bk_biz_m' ) ){

		$wp_admin_bar->add_menu(
				array(
					'id' => 'wpbc_bar_prices',
					'title' => __( 'Prices', 'booking' ),
					'href' => wpbc_get_price_url(),
					'parent' => 'wpbc_bar',
				)
		);
			$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_daily_costs',
						'title' => __('Daily Costs','booking'),
						'href' => wpbc_get_price_url() . '&tab=cost',
						'parent' => 'wpbc_bar_prices',
					)
			);
			$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_cost_advanced',
						'title' => __('Form Options Costs','booking'),
						'href' => wpbc_get_price_url() . '&tab=cost_advanced',
						'parent' => 'wpbc_bar_prices',
					)
			);
			if ( class_exists( 'wpdev_bk_biz_l' ) ){
				$wp_admin_bar->add_menu(
						array(
							'id' => 'wpbc_bar_coupons',
							'title' => __('Discount Coupons','booking'),
							'href' => wpbc_get_price_url() . '&tab=coupons',
							'parent' => 'wpbc_bar_prices',
						)
				);
			}
			$wp_admin_bar->add_menu(
				array(
					'id' => 'wpbc_bar_seasons_costs',
					'title' => __( 'Seasons', 'booking' ),
					'href' =>  wpbc_get_price_url() . '',
					'parent' => 'wpbc_bar_prices'
				)
			);
			$wp_admin_bar->add_menu(
				array(
					'id' => 'wpbc_bar_costs_payment_gateways',
					'title' => __( 'Payment Setup', 'booking' ),
					'href' =>  wpbc_get_settings_url() . '&tab=payment',
					'parent' => 'wpbc_bar_prices'
				)
			);

	}

	//Booking > Resources page
	$wp_admin_bar->add_menu(
			array(
				'id' => 'wpbc_bar_resources',
				'title' => ( ( class_exists( 'wpdev_bk_personal' ) ) ? __( 'Resources', 'booking' ) : __( 'Resource', 'booking' ) ),
				'href' => $link_res,
				'parent' => 'wpbc_bar',
			)
		);
			$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_resources_general',
						'title' => ( ( class_exists( 'wpdev_bk_personal' ) ) ? __( 'Resources', 'booking' ) : __( 'Resource', 'booking' ) ),
						'href' => $link_res,
						'parent' => 'wpbc_bar_resources',
					)
				);

			if ( class_exists( 'wpdev_bk_biz_l' ) )
				$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_resources_searchable',
						'title' => __( 'Search Resources Setup', 'booking' ),//__( 'Searchable Resources', 'booking' ),//__( 'Search Availability', 'booking' ),
						'href' => $link_res . '&tab=searchable_resources',
						'parent' => 'wpbc_bar_resources'
					)
				);

//					if ($is_super_admin)
//						if ( class_exists( 'wpdev_bk_biz_l' ) )
//							$wp_admin_bar->add_menu(
//								array(
//									'id' => 'wpbc_bar_resources_search',
//									'title' => __( 'Search Form / Results', 'booking' ),
//									'href' => $link_settings . '&tab=search',
//									'parent' => 'wpbc_bar_resources'
//								)
//							);


	//Booking > Settings General page

	$wp_admin_bar->add_menu(
			array(
				'id' => 'wpbc_bar_settings',
				'title' => __( 'Settings', 'booking' ),
				'href' => wpbc_get_settings_url(),
				'parent' => 'wpbc_bar',
			)
	);

			if ($is_super_admin)
					$wp_admin_bar->add_menu(
						array(
							'id' => 'wpbc_bar_settings_general',
							'title' => __( 'General', 'booking' ),
							'href' => $link_settings,
							'parent' => 'wpbc_bar_settings'
						)
					);
			$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_settings_form',
						'title' => __( 'Booking Form', 'booking' ),
						'href' => $link_settings . '&tab=form',
						'parent' => 'wpbc_bar_settings'
					)
			);
			$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_settings_email',
						'title' => __( 'Emails', 'booking' ),
						'href' => $link_settings . '&tab=email',
						'parent' => 'wpbc_bar_settings'
					)
			);
			$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_settings_sync',
						'title' => __( 'Sync', 'booking' ),															//FixIn: 8.0
						'href' => $link_settings . '&tab=sync',
						'parent' => 'wpbc_bar_settings'
					)
			);
			if ( class_exists( 'wpdev_bk_biz_s' ) )
				$wp_admin_bar->add_menu(
					array(
						'id' => 'wpbc_bar_settings_payment',
						'title' => __( 'Payment Setup', 'booking' ),
						'href' => $link_settings . '&tab=payment',
						'parent' => 'wpbc_bar_settings'
					)
				);

			if ( ( class_exists( 'wpdev_bk_biz_l' ) ) && ( wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) )
					$wp_admin_bar->add_menu(
						array(
							'id' => 'wpbc_bar_settings_search',
							'title' => __( 'Search Form / Results', 'booking' ),
							'href' => $link_settings . '&tab=search',
							'parent' => 'wpbc_bar_settings'
						)
					);
			if ( class_exists( 'wpdev_bk_multiuser' ) )
				if ($is_super_admin)
					$wp_admin_bar->add_menu(
						array(
							'id' => 'wpbc_bar_settings_users',
							'title' => __( 'Users', 'booking' ),
							'href' => $link_settings . '&tab=users',
							'parent' => 'wpbc_bar_settings'
						)
					);
}
add_action( 'admin_bar_menu', 'wpbc_add__booking_menu__in__admin_top_bar', 70 );									    // Add   -  TOP Bar   - in admin menu
