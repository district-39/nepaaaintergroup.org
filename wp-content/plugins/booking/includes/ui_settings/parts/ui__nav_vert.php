<?php
/**
 * Admin Panel UI - Parts
 *
 * @version  1.2
 * @package  Any
 * @category Page Structure in Admin Panel
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2025-02-15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

function wpbc_ui__get_root_menu_arr() {

	$icn = '<i style="margin: 0;margin-left: auto;" class="wpbc_ui_el__vert_nav_icon menu_icon icon-1x wpbc-bi-chevron-right arrow-right-short"></i>';

	$pages_arr = array(
		'wpbc'              => array(
			'type'      => 'menu',
			'title'     => __( 'Bookings', 'booking' ),
			'font_icon' => 'wpbc-bi-collection',
		),
		'wpbc-new'          => array(
			'type'      => 'menu',
			'title'     => __( 'Add Booking', 'booking' ),
			'font_icon' => 'wpbc-bi-plus',
			'url'       => wpbc_get_new_booking_url(),
		),
		'wpbc-divider-1'    => array(
			'type' => 'divider',
		),
		'wpbc-availability' => array(
			'type'      => 'menu',
			'title'     => __( 'Availability', 'booking' ),
			'font_icon' => 'wpbc-bi-calendar-week',
		),
	);

	if ( class_exists('wpdev_bk_biz_m') ) {
		$pages_arr['wpbc-prices'] = array(
			'type'      => 'menu',
			'title'     => __( 'Prices', 'booking' ),
			'font_icon' => 'wpbc-bi-cash-stack',
		);
	}

	if ( class_exists('wpdev_bk_personal') ) {
		$pages_arr['wpbc-resources'] = array(
			'type'      => 'menu',
			'title'     => __( 'Resources', 'booking' )  . ' / ' . __( 'unique calendars', 'booking' ),
			'font_icon' => 'wpbc-bi-list',
		);
	} else {
		$pages_arr['wpbc-resources'] = array(
			'type'      => 'menu',
			'title'     => __( 'Resource', 'booking' ) . ' / ' . __( 'Publish', 'booking' ),
			'font_icon' => 'wpbc-bi-list',
		);
	}
	$pages_arr['wpbc-divider-2'] = array(
		'type' => 'divider',
	);
	$pages_arr['wpbc-settings'] = array(
			'type'      => 'menu',
			'title'     => __( 'Settings', 'booking' ),
			'font_icon' => 'wpbc_icn_settings',
	);

	return $pages_arr;
}

/**
 * Show Left Vaertical Navigation Bar
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui__left_vertical_nav( $args =array() ) {

	$defaults = array(
		'attr' => array(),
	);
	$params   = wp_parse_args( $args, $defaults );

	// Ability to click on panel, only if there 'min' class - panel minimized!
	echo '<div class="wpbc_ui_el__vert_left_bar__wrapper" onclick0="javascript:if (( jQuery( this ).parent(\'.wpbc_settings_page_wrapper\').hasClass(\'min\') ) && ( ! wpbc_admin_ui__is_in_mobile_screen_size())) { wpbc_admin_ui__sidebar_left__do_max(); }" >';

	// FixIn: 10.12.1.7.
	wpbc_ui__vert_left_bar__side_button__do_compact();
	wpbc_ui__vert_left_bar__side_button__do_max();

	$active_page_arr = array(
		'active_page'   => $args['active_page'],          // wpbc-settings.
		'active_tab'    => $args ['active_tab'],          // calendar_appearance.
		'active_subtab' => $args['active_subtab'],        // calendar_appearance_skin.
	);

	// Available Main Menu - slug => titles.
	$root_menu_arr = wpbc_ui__get_root_menu_arr();

	echo '  <div class="wpbc_ui_el__vert_left_bar__content">';

	$is_show_all_menus = true;

	if ( ! $is_show_all_menus ) {
		wpbc_ui__vert_left_bar__section__root_menu( $root_menu_arr );
	} else {

		// wpbc_ui_el__divider_horizontal();
		echo '<div class="wpbc_ui_el__row100 wpbc_ui_el__expand_colapse_btns">';
		wpbc_ui__vert_left_bar__do_compact();
		wpbc_ui__vert_left_bar__do_max();
		echo '</div>';

		echo '  <div class="wpbc_ui_el__vert_left_bar__root_sections_container" role="tablist">';
	}

	// Loop to  show all  main  sections in vertical  menu.
	foreach ( $root_menu_arr as $main_page_slug => $root_menu_options_arr ) {

		if ( 'menu' !== $root_menu_options_arr['type'] ) {
			continue;
		}

		$page_title = $root_menu_options_arr['title'];
		$is_expanded = ( $main_page_slug === $active_page_arr['active_page'] );

		if ( $is_show_all_menus ) {
			echo '  <div class="wpbc_ui_el__vert_left_bar__root_section_element root_section_element_' . esc_attr( $main_page_slug ) . ' ' . ( ( $is_expanded ) ? 'section_expanded' : '' ) . '">';

			wpbc_ui__vert_menu__show_root_section_header( $main_page_slug, $page_title );
		}

		echo '  <div class="wpbc_ui_el__vert_left_bar__section wpbc_ui_el__vert_left_bar__section_' .
			 esc_attr( $main_page_slug ) .
			 ( ( ( $main_page_slug !== $active_page_arr['active_page'] ) && ( ! $is_show_all_menus ) ) ? ' wpbc_ui__hide ' : '' ) .
			 // ( ( ( ! $is_expanded ) && ( $is_show_all_menus ) ) ? ' wpbc_ui__hide ' : '' ) .
			 '">';

		// Show only active page settings list.
		if ( $main_page_slug !== $active_page_arr['active_page'] ) {
			//continue;
		}
		$page_item_arr = $args['page_nav_tabs'][ $main_page_slug ];

		if ( ! $is_show_all_menus ) {
			// Section Header.
			echo '<div class="wpbc_ui_el__row100 wpbc_ui_el__expand_colapse_btns">';

			wpbc_ui__vert_menu__show_section_header_go_back( $page_title );
			wpbc_ui__vert_left_bar__do_compact();
			wpbc_ui__vert_left_bar__do_max();
			echo '</div>';
			wpbc_ui_el__divider_horizontal();
		}

		foreach ( $page_item_arr as $main_menu_slug => $menu_item_arr ) {

			$folder_style = ( ! empty( $menu_item_arr['folder_style'] ) ) ? esc_attr( $menu_item_arr['folder_style'] ) : '';

			$folder_css = '';
			if ( ( ! empty( $menu_item_arr['subtabs'] ) ) && ( ! empty( $menu_item_arr['is_active'] ) ) ) {
				$folder_css .= ' expanded';
			}

			echo '<div class="wpbc_ui_el__level__folder ' . esc_attr( $folder_css ) . '" style="' . esc_attr( $folder_style ) . '">';


			if ( empty( $menu_item_arr['type'] ) ) {
				$menu_item_arr['type'] = 'main';
			}

			switch ( $menu_item_arr['type'] ) {

				case 'separator':
					wpbc_ui_el__divider_horizontal();
					break;

				case 'html':
					wpbc_ui__vert_menu__item_html( $main_menu_slug, $menu_item_arr );
					break;

				default:
					wpbc_ui__vert_menu__item_main( $main_menu_slug, $menu_item_arr );
			}


			foreach ( $menu_item_arr['subtabs'] as $main_submenu_slug => $submenu_item_arr ) {

				if ( empty( $submenu_item_arr['type'] ) ) {
					$submenu_item_arr['type'] = 'subtab';
				}

				switch ( $submenu_item_arr['type'] ) {

					case 'subtab':
						wpbc_ui__vert_menu__item_sub( $main_submenu_slug, $submenu_item_arr );
						break;

					case 'separator':
						wpbc_ui__vert_menu__item_separtor( $main_submenu_slug, $submenu_item_arr );
						break;

					case 'html':
						wpbc_ui__vert_menu__item_html( $main_submenu_slug, $submenu_item_arr );
						break;

					default:
				}
			}
			echo '</div><!-- wpbc_ui_el__level__folder -->';
		}

		if ( $is_show_all_menus ) {
			echo '   </div><!-- wpbc_ui_el__vert_left_bar__root_section_element -->';
		}
		echo '   </div><!-- wpbc_ui_el__vert_left_bar__section -->';
	}                                                                                                                   // Loop to  show all  main  sections in vertical  menu.


	if ( $is_show_all_menus ) {
		echo '   </div><!-- wpbc_ui_el__vert_left_bar__root_sections_container -->';
	}


	$is_show_up = get_bk_option( 'booking_wpdev_copyright_adminpanel' );
	$is_show_up = ( ( 'Off' !== $is_show_up ) && ( ! class_exists( 'wpdev_bk_multiuser' ) ) );

	$is_show_up = ( ( 'hide' !== get_bk_option( 'booking_menu_go_pro' ) ) && ( ! class_exists( 'wpdev_bk_multiuser' ) ) );

	if ( $is_show_up ) {
		?><div class="wpbc_ui_el__vert_left_bar__footer_compensator"></div><?php
	}

	echo '   </div><!-- wpbc_ui_el__vert_left_bar__content -->';
	if ( $is_show_up ) {
		$url = wpbc_up_link();
		?>
		<div class="wpbc_ui_el__vert_left_bar__footer_section">
			<a class="wpbc_ui_el_upgrade_button wpbc_button_light wpbc_button_green" href="<?php echo esc_url( $url ); ?>">
				<span class="hide_in_compact_mode"><?php esc_html_e( 'Upgrade to Pro', 'booking' ); ?></span>
				<span class="hide_in_max_mode"><?php esc_html_e( 'Pro', 'booking' ); ?></span>
			</a>
		</div>
		<?php
	}
	echo '</div><!-- wpbc_ui_el__vert_left_bar__wrapper -->';

	wpbc_start_element_scrollable__with_simplebar( '.wpbc_ui_el__vert_left_bar__content' );
}

/**
 * Set Scrolable Element with Simplebar js library.
 *
 * @param string $jq_element - e.g.  '.wpbc_ui_el__vert_left_bar__content'
 * @param string $options    - e.g. 'autoHide: false, scrollbarMinSize: 10'   -  options for the Simplebar. See  more: https://www.npmjs.com/package/simplebar#1-documentation
 *
 * @return void
 */
function wpbc_start_element_scrollable__with_simplebar( $jq_element, $options = 'autoHide: false' ) {

	// FixIn: 10.12.2.3.

	// Initial example:  new SimpleBar( jQuery( '.wpbc_ui_el__vert_left_bar__content' )[0], { autoHide: false } );.
	?>
	<script type="text/javascript">
		(function() { var a = setInterval( function() {  if ( ( 'undefined' === typeof jQuery ) || ! window.jQuery ) { return; } clearInterval( a ); jQuery( document ).ready( function () {
			jQuery( '.wpbc_ui_el__vert_left_bar__section' ).css( { "animation-duration": "1ms" } ); // Set animation of showing left siebar from left to right imediate to prevent flipping.
			new SimpleBar( jQuery( '<?php echo esc_attr( $jq_element ); ?>' )[0], { <?php echo esc_attr( $options ); ?> } );
			var wait_timer = setTimeout( function (){
				jQuery( '.wpbc_ui_el__vert_left_bar__section' ).css( { "animation-duration": "200ms" } ); // Set animation to normal value.
			}, 300 );
		} ); }, 400 ); })();
	</script>
	<?php
}

/* == Toggles in Left  menu ========================================================================================= */

/**
 * Show element - "Open / Hide Left Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__do_toggle() {

	$el_arr                    = array();
	$el_arr['font_icon']       = 'wpbc_icn_menu';
	$el_arr['container_style'] = 'padding:0 8px;';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_show_left_vertical_nav';
	$el_arr['onclick']         = "if ( jQuery( '.wpbc_ui_el__vert_left_bar__content' ).is( ':visible' ) ) {";
	$el_arr['onclick']         .= ' wpbc_admin_ui__sidebar_left__do_min(); ';
	$el_arr['onclick']         .= '} else {';
	$el_arr['onclick']         .= ' wpbc_admin_ui__sidebar_left__do_max(); ';
	$el_arr['onclick']         .= '}';
	$el_arr['hint']            = array(
		'title'    => __( 'Toggle side menu', 'booking' ),
		'position' => 'right',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "Max - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__do_max() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_open_left_vertical_nav wpbc_ui__hide hide_in_compact_mode0';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_left__do_max(); ';
	$el_arr['font_icon']       = 'wpbc-bi-box-arrow-right';
	$el_arr['hint']            = array(
		'title'    => __( 'Open side menu', 'booking' ),
		'position' => 'top',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "Compact - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__do_compact() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_hide_left_vertical_nav';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_left__do_compact(); ';
	$el_arr['font_icon']       = 'wpbc-bi-box-arrow-left';
	$el_arr['hint']            = array(
		'title'    => __( 'Set side menu compact', 'booking' ),
		'position' => 'top',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "X - Close (none)" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__do_none() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_hide_left_vertical_nav';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_left__do_hide(); ';
	$el_arr['font_icon']       = 'wpbc_icn_close';
	$el_arr['hint']            = array(
		'title'    => __( 'Close side menu', 'booking' ),
		'position' => 'bottom',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "_ - Min" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__do_min() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_hide_left_vertical_nav';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_left__do_min(); ';
	$el_arr['font_icon']       = 'wpbc_icn_minimize';
	$el_arr['hint']            = array(
		'title'    => __( 'Minimize side menu', 'booking' ),
		'position' => 'bottom',
	);
	wpbc_ui_el__a( $el_arr );
}

/* == Elements in Left  menu ========================================================================================= */

/**
 * Show Section Header - e.g. ' SETTINGS '
 *
 * @param string $page_title - title to show.
 *
 * @return void
 */
function wpbc_ui__vert_menu__show_section_header_go_back( $page_title ) {

	// FixIn: 10.12.1.7.
	?>
	<a onclick="javascript:wpbc_admin_ui__sidebar_left__show_section('main_menus');" href="javascript:void(0)"
	   class="wpbc_ui_el__go_back wpbc_ui_el  hide_in_compact_mode" >
		<i class="menu_icon icon-1x wpbc_icn_navigate_before"></i>
		<?php
			wpbc_ui__vert_menu__show_section_header( __( 'Go back', 'booking' ) );
		?>
	</a>
	<?php
}

/**
 * Left Sidebar.
 *
 * @param array $pages_arr
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__section__root_menu( $pages_arr ) {

	?>
	<div class="wpbc_ui_el__vert_left_bar__section wpbc_ui_el__vert_left_bar__section_main_menus wpbc_ui__hide">
		<?php

		// Section Header.
		echo '<div class="wpbc_ui_el__row100 wpbc_ui_el__expand_colapse_btns">';
		// wpbc_ui__vert_menu__show_section_header( __( 'Main menu', 'booking' ) );
		wpbc_ui__vert_left_bar__do_compact();
		wpbc_ui__vert_left_bar__do_max();
		echo '</div>';
		wpbc_ui_el__divider_horizontal();

		foreach ( $pages_arr as $page_slug => $main_page_options_arr ) {

			switch ( $main_page_options_arr['type'] ) {

				case 'divider':
					wpbc_ui_el__divider_horizontal();
					break;

				case 'menu':
				default:
					$page_title = $main_page_options_arr['title'];

					echo '<div class="wpbc_ui_el__vert_nav_item wpbc_ui_el__vert_nav_item_root wpbc_ui_el__vert_nav_item__' . esc_attr( $page_slug ) . '">';
					if ( ! empty( $main_page_options_arr['url'] ) ) {
						echo '  <a href="' . esc_url( $main_page_options_arr['url'] ) . '" class="wpbc_ui_el__vert_nav_item__a wpbc_ui_el__vert_nav_item__single">';
					} else {
						echo '  <a onclick="wpbc_admin_ui__sidebar_left__show_section(\'' . esc_attr( $page_slug ) . '\');" href="javascript:void(0)" class="wpbc_ui_el__vert_nav_item__a wpbc_ui_el__vert_nav_item__single">';
					}

					echo '   <i class="wpbc_ui_el__vert_nav_icon menu_icon icon-1x ' . esc_attr( $main_page_options_arr['font_icon'] ) . '"></i>';
					echo '   <span class="wpbc_ui_el__vert_nav_title hide_in_compact_mode">' . esc_html( $page_title ) . '</span>';
					if ( empty( $main_page_options_arr['url'] ) ) {
						echo '   <i class="wpbc_ui_el__vert_nav_icon wpbc_ui_el__vert_nav_icon_arrow menu_icon icon-1x wpbc-bi-chevron-right"></i>';
					}
					echo '  </a>';
					echo '</div>';
			}
		}
		?>
	</div>
	<?php
}


/**
 * Show Root Header
 *
 * @param string $main_page_slug - slug of all sub menu section.
 * @param string $page_title - title of header.
 * @param bool $is_expanded - Is expanded or not.
 *
 * @return void
 */
function wpbc_ui__vert_menu__show_root_section_header( $main_page_slug, $page_title) {

	$css_section = '.root_section_element_' . $main_page_slug;

	// Section Header.
	?><a class="wpbc_ui_el__row100 wpbc_ui_el__root_section_header_a" href="javascript:void(0)"
		 onclick="javascript:var is_has_class = jQuery( '<?php echo esc_attr( $css_section ); ?>' ).hasClass('section_expanded'); jQuery( '.wpbc_ui_el__vert_left_bar__root_section_element' ).removeClass('section_expanded'); if (is_has_class) { jQuery( '<?php echo esc_attr( $css_section ); ?>' ).removeClass('section_expanded'); } else {jQuery( '<?php echo esc_attr( $css_section ); ?>' ).addClass('section_expanded');}"
	><?php

		?><i class="wpbc_ui_el__vert_menu_root_section_icon menu_icon icon-1x wpbc-bi-chevron-right"></i><?php

		wpbc_ui__vert_menu__show_section_header( $page_title );

	?></a><?php
}


/**
 * Show Section Header - e.g. ' SETTINGS '
 *
 * @param string $page_title - title to show.
 *
 * @return void
 */
function wpbc_ui__vert_menu__show_section_header( $page_title ) {

	echo '<h2 class="wpbc_ui_el__section_header hide_in_compact_mode">' . wp_kses_post( $page_title ) . '</h2>';
}

/**
 * Show main menu item.
 *
 * @param string $menu_slug     - 'calendar_appearance'.
 * @param array  $menu_item_arr - [    [title] => Skin
 *                                     [page_title] => Calendar General Settings 3
 *                                     [hint] => Calendar General Settings 2
 *                                     [link] =>
 *                                     [position] =>
 *                                     [css_classes] =>
 *                                     [icon] =>
 *                                     [font_icon] => wpbc-bi-calendar2-range
 *                                     [default] =>
 *                                     [disabled] =>
 *                                     [hided] =>
 *                                     [is_active] => 1
 *                                     [url] => http://beta/wp-admin/admin.php?page=wpbc-settings&#038;tab=calendar_appearance
 *                                     [subtabs] => [....]
 *                               ]
 *
 * @return void
 */
function wpbc_ui__vert_menu__item_main( $menu_slug, $menu_item_arr ) {

	$defaults      = array(
		'title'       => '',
		'page_title'  => '',
		'hint'        => '',
		'link'        => '',
		'position'    => '',
		'css_classes' => '',
		'icon'        => '',
		'font_icon'   => '',
		'default'     => false,
		'disabled'    => false,
		'hided'       => false,
		'is_active'   => 0,
		'url'         => '',
		'subtabs'     => array(),
	);
	$menu_item_arr = wp_parse_args( $menu_item_arr, $defaults );

	// CSS Classes.
	$menu_css_arr   = array();
	$menu_css_arr[] = 'wpbc_ui_el__vert_nav_item';
	$menu_css_arr[] = 'wpbc_ui_el__vert_nav_item__' . esc_attr( $menu_slug );

	if ( ( empty( $menu_item_arr['subtabs'] ) ) && ( ! empty( $menu_item_arr['is_active'] ) ) ) {
		$menu_css_arr[] = 'active';
	}
	if ( isset( $menu_item_arr['css_classes'] ) ) {
		$menu_css_arr[] = $menu_item_arr['css_classes'];
	}
	$menu_css_str = implode( ' ', $menu_css_arr );

	// Styles.
	$menu_style_str = ( isset( $menu_item_arr['style'] ) ) ? $menu_item_arr['style'] : '';


	?><div class="<?php echo esc_attr( $menu_css_str ); ?>" style="<?php echo esc_attr( $menu_style_str ); ?>">
		<?php
		if ( ! empty( $menu_item_arr['subtabs'] ) ) {
			// Folder Item - expand / colapse.  Previously: here was : wpbc_admin_ui__sidebar_left__do_max();
			?>
			<a href="javascript:void(0)"
				onclick="javascript:  if( ! jQuery( this ).parents('.wpbc_ui_el__level__folder').hasClass('expanded') ) { jQuery( '.wpbc_ui_el__level__folder' ).removeClass('expanded');jQuery( this ).parents('.wpbc_ui_el__level__folder').addClass('expanded'); } else {jQuery( '.wpbc_ui_el__level__folder' ).removeClass('expanded');} "
				class="wpbc_ui_el__vert_nav_item__a wpbc_ui_el__vert_nav_item__folder">
				<?php if ( ! empty( $menu_item_arr['font_icon'] ) ) { ?>
					<?php // Show icon in max mode, without right tooltip, thanks to '.hide_in_compact_mode'. // FixIn: 10.11.5.8. ?>
					<i 	class="wpbc_ui_el__vert_nav_icon hide_in_compact_mode  menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
						title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"
					></i>
					<?php // Show icon in compact mode, with right tooltip, thanks to '.hide_in_max_mode'. // FixIn: 10.11.5.8. ?>
					<i 	class="wpbc_ui_el__vert_nav_icon hide_in_max_mode tooltip_right_offset menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
						data-original-title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ) . ' - ' . esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"
					></i>
				<?php } ?>
				<span class="wpbc_ui_el__vert_nav_title hide_in_compact_mode"><?php echo wp_kses_post( $menu_item_arr['title'] ); ?></span>
				<i class="wpbc_ui_el__vert_nav_icon_right tooltip_right menu_icon icon-1x wpbc_ui_el__vert_nav_icon_expanded  hide_in_compact_mode wpbc-bi-dash-square-dotted" data-original-title="<?php echo esc_attr_e( 'Collapse', 'booking' ); ?>"></i>
				<i class="wpbc_ui_el__vert_nav_icon_right tooltip_right menu_icon icon-1x wpbc_ui_el__vert_nav_icon_collapsed hide_in_compact_mode wpbc-bi-plus-square-dotted" data-original-title="<?php echo esc_attr_e( 'Expand', 'booking' ); ?>"></i>
			</a>
			<?php
		} else {
			// Single Item Link.
			?>
			<a 	<?php if ( ! empty( $menu_item_arr['onclick'] ) ) { ?>
					onclick="javascript:<?php echo $menu_item_arr['onclick']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>"
					href="javascript:void(0);"
				<?php } else { ?>
					href="<?php echo esc_url( $menu_item_arr['url'] ); ?>"
				<?php } ?>
				class="wpbc_ui_el__vert_nav_item__a wpbc_ui_el__vert_nav_item__single">
				<?php if ( ! empty( $menu_item_arr['font_icon'] ) ) { ?>
					<?php // Show icon in max mode, without right tooltip, thanks to '.hide_in_compact_mode'. // FixIn: 10.11.5.8. ?>
					<i 	class="wpbc_ui_el__vert_nav_icon hide_in_compact_mode menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
						title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"
					></i>
					<?php // Show icon in compact mode, with right tooltip, thanks to '.hide_in_max_mode'. // FixIn: 10.11.5.8. ?>
					<i 	class="wpbc_ui_el__vert_nav_icon hide_in_max_mode tooltip_right_offset  menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
						data-original-title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ) . ' - ' .  esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"
					></i>
				<?php } ?>
				<span class="wpbc_ui_el__vert_nav_title hide_in_compact_mode"><?php echo wp_kses_post( $menu_item_arr['title'] ); ?></span>
				<?php
				// Icon at Right side.  Usually: 'wpbc-bi-question-circle' | 'wpbc-bi-arrow-down-short' | 'wpbc-bi-arrow-up-right-square'.
				if ( ! empty( $menu_item_arr['font_icon_right'] ) ) { ?>
				<i class="hide_in_compact_mode tooltip_right  menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon_right'] ); ?>"
				   data-original-title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"></i>
				<?php
				}
				?>
			</a>
			<?php
		}
		?>
	</div>
	<?php
}

/**
 * Sub - Single Item.
 *
 * @param string $menu_slug           - 'calendar_appearance'.
 * @param array  $menu_item_arr       - [
 *                                    'type' => "subtab",
 *                                    title = "Skin Search Form Layout 1"
 *                                    page_title = "Skin Search Settings 2"
 *                                    hint = "Skin Search Settings 3"
 *                                    position = ""
 *                                    css_classes = ""
 *                                    default = true
 *                                    disabled = false
 *                                    checkbox = false
 *                                    content = "content"
 *                                    show_checked_icon = false
 *                                    is_use_new_settings_skin = true
 *                                    is_active = true
 *                                    url = "http://beta/wp-admin/admin.php?page=wpbc-settings&#038;tab=calendar_appearance&#038;subtab=calendar_appearance_skin"
 *                                    ].
 *
 * @return void
 */
function wpbc_ui__vert_menu__item_sub( $menu_slug, $menu_item_arr ) {

	// CSS Classes.
	$menu_css_arr   = array();
	$menu_css_arr[] = 'wpbc_ui_el__vert_nav_item';
	$menu_css_arr[] = 'wpbc_ui_el__vert_nav_item_sub';
	// $menu_css_arr[] = 'hide_in_compact_mode';
	$menu_css_arr[] = 'wpbc_ui_el__vert_nav_item__' . esc_attr( $menu_slug );
	if ( ! empty( $menu_item_arr['is_active'] ) ) {
		$menu_css_arr[] = 'active';
	}
	if ( isset( $menu_item_arr['css_classes'] ) ) {
		$menu_css_arr[] = $menu_item_arr['css_classes'];
	}
	$menu_css_str = implode( ' ', $menu_css_arr );

	// Styles.
	$menu_style_str = ( isset( $menu_item_arr['style'] ) ) ? $menu_item_arr['style'] : '';


	?>
	<div class="<?php echo esc_attr( $menu_css_str ); ?>" style="<?php echo esc_attr( $menu_style_str ); ?>">
		<a class="wpbc_ui_el__vert_nav_item__a wpbc_ui_el__vert_nav_item__single"
			<?php if ( ! empty( $menu_item_arr['onclick'] ) ) { ?>
				onclick="javascript:<?php echo $menu_item_arr['onclick']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>"
				href="javascript:void(0);"
			<?php } else { ?>
				href="<?php echo esc_url( $menu_item_arr['url'] ); ?>"
			<?php } ?>
		>
			<?php if ( ! empty( $menu_item_arr['font_icon'] ) ) { ?>
				<?php // Show icon in max mode, without right tooltip, thanks to '.hide_in_compact_mode'. // FixIn: 10.11.5.8.2. ?>
				<i class="wpbc_ui_el__vert_nav_icon hide_in_compact_mode menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
				   title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"
				></i>
				<?php // Show icon in compact mode, with right tooltip, thanks to '.hide_in_max_mode'. // FixIn: 10.11.5.8.2. ?>
				<i 	class="wpbc_ui_el__vert_nav_icon hide_in_max_mode tooltip_right_offset  menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
					data-original-title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ) . ' - ' .  esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"
				></i>
			<?php } ?>
			<span class="wpbc_ui_el__vert_nav_title hide_in_compact_mode"><?php echo wp_kses_post( $menu_item_arr['title'] ); ?></span>
			<?php


			// Show Togles for "Emails" and "Payment gateways",  maybe for some other pages.
			if (
				( ! empty( $menu_item_arr['show_checked_icon'] ) ) &&
				( ! empty( $menu_item_arr['checked_data'] ) )
			) {
				$is_checked_data = get_bk_option( $menu_item_arr['checked_data'] );
				if (
					( ( ! empty( $is_checked_data ) ) && ( isset( $is_checked_data['enabled'] ) ) && ( 'On' === $is_checked_data['enabled'] ) ) ||
					( ( ! empty( $is_checked_data ) ) && ( 'On' === $is_checked_data ) )
				) {
					echo '<i class="wpbc_ui_el__vert_nav_icon_right menu_icon icon-1x wpbc-bi-toggle2-on"  style="margin-left: auto;margin-top: 3px;color: #036aab;"></i>';
				} else {
					echo '<i class="wpbc_ui_el__vert_nav_icon_right menu_icon icon-1x wpbc-bi-toggle2-off" style="margin-left: auto;margin-top: 3px;"></i>';
				}
			}

			// Icon at Right side.  Usually: 'wpbc-bi-question-circle' | 'wpbc-bi-arrow-down-short' | 'wpbc-bi-arrow-up-right-square'.
			if ( ! empty( $menu_item_arr['font_icon_right'] ) ) { ?>
			<i class="hide_in_compact_mode tooltip_right  menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon_right'] ); ?>"
			   data-original-title="<?php echo ( ! empty( $menu_item_arr['hint'] ) ) ? esc_attr( wp_strip_all_tags( $menu_item_arr['hint'] ) ) : esc_attr( wp_strip_all_tags( $menu_item_arr['title'] ) ); ?>"></i>
			<?php
			}
			?>
		</a>
	</div>
	<?php
}

/**
 * Show horizontal  divider.
 *
 * @param string $menu_slug     - 'calendar_appearance'.
 * @param array  $menu_item_arr - [
 *                                  'type' => "separator",
 *                                  'is_active' => false,
 *                                  'url' => "http://beta/wp-admin/admin.php?page=wpbc-settings&#038;tab=calendar_appearance&#038;subtab=calendar_appearance_skin_sep"
 *                                ].
 *
 * @return void
 */
function wpbc_ui__vert_menu__item_separtor( $menu_slug, $menu_item_arr ) {

	$args = array( 'container_class' => 'wpbc_ui_el__vert_nav_item_sub' );
	wpbc_ui_el__divider_horizontal( $args );
}

/**
 * HTML
 *
 * @param string $menu_slug               - 'calendar_appearance'.
 * @param array  $menu_item_arr           - [
 *                                        'type' = "htnl"
 *                                        'html' = "Form Fields"
 *                                        ].
 *
 * @return void
 */
function wpbc_ui__vert_menu__item_html( $menu_slug, $menu_item_arr ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $menu_item_arr['html'];
}

// FixIn: 10.12.1.7.
/**
 * Show element - "Compact - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__side_button__do_compact() {

	?>
	<button class="wpbc_ui__left_sidebar__side_button wpbc_ui__top_nav__btn_hide_left_vertical_nav"
			onclick="javascript:wpbc_admin_ui__sidebar_left__do_min();" title="<?php esc_attr_e( 'Set side menu compact', 'booking' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
			<path fill="#6B6B6B" d="M16.5 22a1.003 1.003 0 0 1-.71-.29l-9-9a1 1 0 0 1 0-1.42l9-9a1.004 1.004 0 1 1 1.42 1.42L8.91 12l8.3 8.29A.999.999 0 0 1 16.5 22Z"></path>
		</svg>
	</button>
	<?php
}

/**
 * Show element - "Max - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_left_bar__side_button__do_max() {
	?>
	<button class="wpbc_ui__left_sidebar__side_button wpbc_ui__hide wpbc_ui__top_nav__btn_open_left_vertical_nav"
			onclick="javascript:wpbc_admin_ui__sidebar_left__do_max();" title="<?php esc_attr_e( 'Open side menu', 'booking' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
			<path fill="#6B6B6B" d="M16.5 22a1.003 1.003 0 0 1-.71-.29l-9-9a1 1 0 0 1 0-1.42l9-9a1.004 1.004 0 1 1 1.42 1.42L8.91 12l8.3 8.29A.999.999 0 0 1 16.5 22Z"></path>
		</svg>
	</button>
	<script type="text/javascript">
		<?php echo wpbc_jq_ready_start();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		if (wpbc_admin_ui__is_in_this_screen_size(789)){
			jQuery( '.wpbc_ui__left_sidebar__side_button' ).toggleClass( 'wpbc_ui__hide' );
		}
		<?php echo wpbc_jq_ready_end();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</script>
	<?php
}


/* == Right Sidebar ================================================================================================= */
/**
 * Show Compact Right Vertical SideBar
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui__right_vertical_sidebar_compact( $args =array() ) {

	$defaults = array(
		'attr' => array(),
	);
	$params   = wp_parse_args( $args, $defaults );

	$active_page_arr = array(
		'active_page'   => $args['active_page'],          // wpbc-settings.
		'active_tab'    => $args ['active_tab'],          // calendar_appearance.
		'active_subtab' => $args['active_subtab'],        // calendar_appearance_skin.
	);

	// Ability to click on panel, only if there 'min' class - panel minimized!
	echo '<div class="wpbc_ui_el__vert_right_bar__wrapper wpbc_ui_el__vert_right_bar__wrapper wpbc_ui_el__vert_right_bar_compact__wrapper" >';

	echo '  <div class="wpbc_ui_el__vert_right_bar__content">';

	echo '  <div class="wpbc_ui_el__vert_right_bar__root_sections_container" role="tablist">';

	do_action( 'wpbc_ui__right_vertical_sidebar_compact_content', $active_page_arr );

	echo '   </div><!-- wpbc_ui_el__vert_right_bar__root_sections_container -->';
	echo '   </div><!-- wpbc_ui_el__vert_right_bar__content -->';
	echo '</div><!-- wpbc_ui_el__vert_right_bar_compact__wrapper -->';


	wpbc_start_element_scrollable__with_simplebar( '.wpbc_ui_el__vert_right_bar_compact__wrapper .wpbc_ui_el__vert_right_bar__content' );
}


/**
 * Show Right Vertical SideBar
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui__right_vertical_sidebar( $args =array() ) {

	$defaults = array(
		'attr' => array(),
	);
	$params   = wp_parse_args( $args, $defaults );

	// Ability to click on panel, only if there 'min' class - panel minimized!
	echo '<div class="wpbc_ui_el__vert_right_bar__wrapper wpbc_ui_el__vert_right_bar__wrapper" onclick0="javascript:if (( jQuery( this ).parent(\'.wpbc_settings_page_wrapper\').hasClass(\'min\') ) && ( ! wpbc_admin_ui__is_in_mobile_screen_size())) { wpbc_admin_ui__sidebar_right__do_max(); }" >';

	// FixIn: 10.12.1.7.
	wpbc_ui__vert_right_bar__side_button__do_compact();
	wpbc_ui__vert_right_bar__side_button__do_max();

	$active_page_arr = array(
		'active_page'   => $args['active_page'],          // wpbc-settings.
		'active_tab'    => $args ['active_tab'],          // calendar_appearance.
		'active_subtab' => $args['active_subtab'],        // calendar_appearance_skin.
	);

	echo '  <div class="wpbc_ui_el__vert_right_bar__content">';

	// wpbc_ui_el__divider_horizontal();
	echo '<div class="wpbc_ui_el__row100 wpbc_ui_el__expand_colapse_btns">';
	wpbc_ui__vert_right_bar__do_compact();
	wpbc_ui__vert_right_bar__do_max();
	echo '</div>';

	echo '  <div class="wpbc_ui_el__vert_right_bar__root_sections_container" role="tablist">';

	do_action( 'wpbc_ui__right_vertical_sidebar_content', $active_page_arr );

	echo '   </div><!-- wpbc_ui_el__vert_right_bar__root_sections_container -->';
	echo '   </div><!-- wpbc_ui_el__vert_right_bar__content -->';
	echo '</div><!-- wpbc_ui_el__vert_right_bar__wrapper -->';

	wpbc_start_element_scrollable__with_simplebar( '.wpbc_ui_el__vert_right_bar__wrapper .wpbc_ui_el__vert_right_bar__content' );
}

/**
 * Show element - "Compact - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__side_button__do_compact() {

	?>
	<button class="wpbc_ui__right_sidebar__side_button wpbc_ui__top_nav__btn_hide_right_vertical_nav"
			onclick="javascript:wpbc_admin_ui__sidebar_right__do_min();" title="<?php esc_attr_e( 'Set side menu compact', 'booking' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
			<path fill="#6B6B6B" d="M16.5 22a1.003 1.003 0 0 1-.71-.29l-9-9a1 1 0 0 1 0-1.42l9-9a1.004 1.004 0 1 1 1.42 1.42L8.91 12l8.3 8.29A.999.999 0 0 1 16.5 22Z"></path>
		</svg>
	</button>
	<?php
}

/**
 * Show element - "Max - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__side_button__do_max() {
	?>
	<button class="wpbc_ui__right_sidebar__side_button wpbc_ui__hide wpbc_ui__top_nav__btn_open_right_vertical_nav"
			onclick="javascript:wpbc_admin_ui__sidebar_right__do_max();" title="<?php esc_attr_e( 'Open side menu', 'booking' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
			<path fill="#6B6B6B" d="M16.5 22a1.003 1.003 0 0 1-.71-.29l-9-9a1 1 0 0 1 0-1.42l9-9a1.004 1.004 0 1 1 1.42 1.42L8.91 12l8.3 8.29A.999.999 0 0 1 16.5 22Z"></path>
		</svg>
	</button>
	<script type="text/javascript">
		<?php echo wpbc_jq_ready_start();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		if (wpbc_admin_ui__is_in_this_screen_size(789)){
			jQuery( '.wpbc_ui__right_sidebar__side_button' ).toggleClass( 'wpbc_ui__hide' );
		}
		<?php echo wpbc_jq_ready_end();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</script>
	<?php
}

/* == Toggles in Right  menu ========================================================================================= */

/**
 * Show element - "Open / Hide Left Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__do_toggle() {

	$el_arr                    = array();
	$el_arr['font_icon']       = 'wpbc-bi-layout-sidebar-inset-reverse';
	$el_arr['container_style'] = 'padding:0 8px;';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_show_right_vertical_nav';
	$el_arr['onclick']         = "if ( jQuery( '.wpbc_ui_el__vert_right_bar__content' ).is( ':visible' ) ) {";
	$el_arr['onclick']         .= ' wpbc_admin_ui__sidebar_right__do_min(); ';
	$el_arr['onclick']         .= " jQuery( '.wpbc_ui__top_nav__btn_show_right_vertical_nav i' ).removeClass( 'wpbc-bi-layout-sidebar-reverse wpbc-bi-layout-sidebar-inset-reverse' ).addClass( 'wpbc-bi-layout-sidebar-reverse' ); ";
	$el_arr['onclick']         .= '} else {';
	$el_arr['onclick']         .= ' wpbc_admin_ui__sidebar_right__do_max(); ';
	$el_arr['onclick']         .= " jQuery( '.wpbc_ui__top_nav__btn_show_right_vertical_nav i' ).removeClass( 'wpbc-bi-layout-sidebar-reverse wpbc-bi-layout-sidebar-inset-reverse' ).addClass( 'wpbc-bi-layout-sidebar-inset-reverse' ); ";
	$el_arr['onclick']         .= '}';
	$el_arr['hint']            = array(
		'title'    => __( 'Toggle side menu', 'booking' ),
		'position' => 'left',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "Max - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__do_max() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_open_right_vertical_nav wpbc_ui__hide hide_in_compact_mode0';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_right__do_max(); ';
	$el_arr['font_icon']       = 'wpbc-bi-box-arrow-left';
	$el_arr['hint']            = array(
		'title'    => __( 'Open side menu', 'booking' ),
		'position' => 'top',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "Compact - Vertical Navigation" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__do_compact() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_hide_right_vertical_nav';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_right__do_compact(); ';
	$el_arr['font_icon']       = 'wpbc-bi-box-arrow-right';
	$el_arr['hint']            = array(
		'title'    => __( 'Set side menu compact', 'booking' ),
		'position' => 'top',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "X - Close (none)" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__do_none() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_hide_right_vertical_nav';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_right__do_hide(); ';
	$el_arr['font_icon']       = 'wpbc_icn_close';
	$el_arr['hint']            = array(
		'title'    => __( 'Close side menu', 'booking' ),
		'position' => 'bottom',
	);
	wpbc_ui_el__a( $el_arr );
}

/**
 * Show element - "_ - Min" panel
 *
 * @return void
 */
function wpbc_ui__vert_right_bar__do_min() {

	$el_arr                    = array();
	$el_arr['container_style'] = '';
	$el_arr['container_class'] = 'wpbc_ui__top_nav__btn_hide_right_vertical_nav';
	$el_arr['onclick']         = ' wpbc_admin_ui__sidebar_right__do_min(); ';
	$el_arr['font_icon']       = 'wpbc_icn_minimize';
	$el_arr['hint']            = array(
		'title'    => __( 'Minimize side menu', 'booking' ),
		'position' => 'bottom',
	);
	wpbc_ui_el__a( $el_arr );
}