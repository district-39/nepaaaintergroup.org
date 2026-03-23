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
 * @modified 2025-03-20
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

/**
 * Show Top Horisontal Navigation Bar
 *
 * @param array $args - parameters.
 *
 * @return void
 */
function wpbc_ui__top_horisontal_nav( $args =array() ) {

	$defaults = array(
		'attr' => array(),
	);
	$params   = wp_parse_args( $args, $defaults );

	$active_page_arr = array(
		'active_page'   => $args['active_page'],          // wpbc-settings.
		'active_tab'    => $args ['active_tab'],          // calendar_appearance.
		'active_subtab' => $args['active_subtab'],        // calendar_appearance_skin.
	);

	// Available Main Menu - slug => titles.
	$show_these_pages_arr = array(
		'wpbc'              => __( 'Bookings', 'booking' ),
		'wpbc-new'          => __( 'Add Booking', 'booking' ),
		'wpbc-availability' => __( 'Availability', 'booking' ),
		'wpbc-prices'       => __( 'Prices', 'booking' ),
		'wpbc-resources'    => __( 'Resources', 'booking' ),
		'wpbc-settings'     => __( 'Settings', 'booking' ),
		'wpbc-setup'        => __( 'Setup', 'booking' ),
	);


	// Ability to click on panel, only if there 'min' class - panel minimized!
	echo '<div class="wpbc_ui_el__horis_top_bar__wrapper">';

	echo '  <div class="wpbc_ui_el__horis_top_bar__content">';

	$main_page_slug = $active_page_arr['active_page'];
	$page_title     = $show_these_pages_arr[ $main_page_slug ];

	// Loop to  show all  main  sections in horisontal menu.
	foreach ( $show_these_pages_arr as $main_page_slug => $page_title ) {

		// Show only active page settings list.
		if ( $main_page_slug !== $active_page_arr['active_page'] ) {
			continue;
		}
		$page_item_arr = $args['page_nav_tabs'][ $main_page_slug ];

		do_action( 'hook__wpbc_ui__top_horisontal_nav__start', $active_page_arr['active_page'], $active_page_arr['active_tab'] );  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		foreach ( $page_item_arr as $main_menu_slug => $menu_item_arr ) {
			do_action( 'hook__wpbc_ui__top_horisontal_nav__item_start', $active_page_arr['active_page'], $active_page_arr['active_tab'], $main_menu_slug );   // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			wpbc_ui__horis_menu__item_main( $main_menu_slug, $menu_item_arr );

			do_action( 'hook__wpbc_ui__top_horisontal_nav__item_end', $active_page_arr['active_page'], $active_page_arr['active_tab'], $main_menu_slug );   // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		}
		do_action( 'hook__wpbc_ui__top_horisontal_nav__end', $active_page_arr['active_page'], $active_page_arr['active_tab'] );   // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		// wpbc_ui_el__divider_vertical();
	}                                                                                                                   // Loop to  show all  main  sections in horisontal menu.
	echo '   </div><!-- wpbc_ui_el__horis_top_bar__content -->';

	echo '</div><!-- wpbc_ui_el__horis_top_bar__wrapper -->';
}


/**
 * Show Horisontal - "Menu Item".
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
function wpbc_ui__horis_menu__item_main( $menu_slug, $menu_item_arr ) {

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

	?><div class="wpbc_ui_el__horis_nav_item wpbc_ui_el__horis_nav_item__<?php echo esc_attr( $menu_slug ); ?>  <?php echo ( ! empty( $menu_item_arr['is_active'] ) ) ? ' active ' : ''; ?>">
		<?php
		// Single Item.
		?>
		<a 	href="<?php echo esc_url( $menu_item_arr['url'] ); ?>"
			class="wpbc_ui_el__horis_nav_item__a wpbc_ui_el__horis_nav_item__single">
			<?php if ( ! empty( $menu_item_arr['font_icon'] ) ) { ?>
			<i 	class="wpbc_ui_el__horis_nav_icon tooltip_top menu_icon icon-1x <?php echo esc_attr( $menu_item_arr['font_icon'] ); ?>"
				data-original-title="<?php echo esc_attr( $menu_item_arr['title'] ); ?>"></i>
			<?php } ?>
			<span class="wpbc_ui_el__horis_nav_title"><?php echo wp_kses_post( $menu_item_arr['title'] ); ?></span>
		</a>
	</div>
	<?php
}
