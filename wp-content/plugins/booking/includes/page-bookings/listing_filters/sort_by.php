<?php
/**
 * Class Filter Toolbar Options -- 'Sort By' Dropdown.
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript Hook: in    ../includes/page-bookings/_src/boo_listing__actions.js  ->   function wpbc_boo_listing__init_hook__sort_by() { ... } .

/**
 * Class WPBC_Listing_Filters__Sort_By
 *
 *   INFO: Get request parameter in template:    var p_value = wpbc_ajx_booking_listing.search_get_param( 'wh_sort' );
 */
class WPBC_Listing_Filters__Sort_By {

	const ID = 'wh_sort';

	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_button() {

		$el_arr = array(
			// Icon: 'font_icon' => 'wpbc_icn_swap_vert', //.
			'title'           => '<strong class="nav-tab-text hide_in_mobile">' . __( 'Sort by', 'booking' ) . ': </strong><span class="selected_value"></span>',
			'hint'            => array(
				'title'    => __( 'Select sorting order', 'booking' ),
				'position' => 'top',
			),
			'position'        => 'left',
			'has_down_arrow'  => true,
			'has_border'      => true,
			'container_class' => 'ul_dropdown_menu__' . self::ID,
			'items'           => array(
				array(
					'type' => 'html',
					'html' => self::sort_by__option_html( 'booking_id__desc', __( 'ID', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-down-alt"></i>' ),
				),
				array(
					'type' => 'html',
					'html' => self::sort_by__option_html( 'booking_id__asc', __( 'ID', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-numeric-up"></i>' ),
				),
				array( 'type' => 'divider' ),
				array(
					'type' => 'html',
					'html' => self::sort_by__option_html( 'dates__desc', __( 'Dates', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-down"></i>' ),
				),
				array(
					'type' => 'html',
					'html' => self::sort_by__option_html( 'dates__asc', __( 'Dates', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-up-alt"></i>' ),
				),
			),
		);

		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$el_arr['items'][] = array( 'type' => 'divider' );
			$el_arr['items'][] = array(
				'type' => 'html',
				'html' => self::sort_by__option_html( 'resource__desc', __( 'Resource', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-alpha-down"></i>' ),
			);
			$el_arr['items'][] = array(
				'type' => 'html',
				'html' => self::sort_by__option_html( 'resource__asc', __( 'Resource', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-alpha-up-alt"></i>' ),
			);
		}
		if ( class_exists( 'wpdev_bk_biz_s' ) ) {
			$el_arr['items'][] = array( 'type' => 'divider' );
			$el_arr['items'][] = array(
				'type' => 'html',
				'html' => self::sort_by__option_html( 'cost__desc', __( 'Cost', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-down"></i>' ),
			);
			$el_arr['items'][] = array(
				'type' => 'html',
				'html' => self::sort_by__option_html( 'cost__asc', __( 'Cost', 'booking' ) . ' <i class="menu_icon icon-1x wpbc-bi-sort-up-alt"></i>' ),
			);
		}

		wpbc_ui_el__dropdown_menu( $el_arr );
	}


	/**
	 * Show option link.
	 *
	 * @param string $option_value - selected value of the option, e.g.: 'dates__desc'.
	 * @param string $option_title - title of the option.
	 *
	 * @return string
	 */
	private static function sort_by__option_html( $option_value, $option_title ) {

		$css_class  = 'ul_dropdown_menu_li_action ';
		$css_class .= 'ul_dropdown_menu_li__' . self::ID . '__' . $option_value;    // Example: ul_dropdown_menu_li__wh_sort__dates__asc .

		$html_for_drop_down_option = '<a  href="javascript:void(0)" class="' . esc_attr( $css_class ) . '" ' .
										" onclick=\"wpbc_ajx_booking_send_search_request_with_params({ '" . esc_attr( self::ID ) . "': '{$option_value}' }); \"
										>" . $option_title . '</a>';

		return $html_for_drop_down_option;
	}
}
