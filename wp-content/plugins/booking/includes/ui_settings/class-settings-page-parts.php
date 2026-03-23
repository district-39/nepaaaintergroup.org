<?php
/**
 * Page Structure in Admin Panel
 *
 * @version  1.2
 * @package  Any
 * @category Page Structure in Admin Panel
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-12-23,    2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// UI Parts.
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/elements/ui_el__dropdown_menu.php';
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/elements/ui_el__a.php';
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/elements/ui_el__divider.php';
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/parts/ui__nav_top.php';
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/parts/ui__nav_vert.php';
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/parts/ui__nav_horis.php';
require_once WPBC_PLUGIN_DIR . '/includes/ui_settings/parts/ui__timeline_toolbar.php';


// FixIn: 11.0.0.1.

/**
 * Define Settings Page Structure
 */
class WPBC_Settings_Page_Parts {

	private $page_structure_obj;
	private $active_page;
	private $active_tab;
	private $active_subtab;

	/**
	 * Constructor.
	 *
	 * @param array $template_params_arr            - array(
	 *                                              'active_page'        => $page_tag,
	 *                                              'active_tab'         => $active_page_tab,
	 *                                              'active_subtab'      => $active_page_subtab,
	 *                                              'page_structure_obj' => $this,
	 *                                              ).
	 */
	public function __construct( $template_params_arr ) {

		$this->active_page        = $template_params_arr['active_page'];
		$this->active_tab         = $template_params_arr['active_tab'];
		$this->active_subtab      = $template_params_arr['active_subtab'];
		$this->page_structure_obj = $template_params_arr['page_structure_obj'];
	}


	/**
	 *  Show Template Header
	 *
	 * @return void
	 */
	public function template_header() {

		$tabs_arr                = $this->page_structure_obj->get_nav_tabs();
		$current_page_params_arr = $this->page_structure_obj->get_current_page_params();

		// -------------------------------------------------------------------------------------------------------------
		// 'Left Sidebar'  - By default it shoud be ''. Other options: '', 'min', 'compact', 'max'.
		// -------------------------------------------------------------------------------------------------------------
		$left_navigation__default_view_mode = strval( $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'left_navigation__default_view_mode' ) );

		if ( ! empty( $left_navigation__default_view_mode ) ) {
			if ( 'none' === $left_navigation__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_left__do_hide(); } ); </script>';
			}
			if ( 'min' === $left_navigation__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_left__do_min(); } ); </script>';
			}
			if ( 'compact' === $left_navigation__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_left__do_compact(); } ); </script>';
			}
			if ( 'max' === $left_navigation__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_left__do_max(); } ); </script>';
			}
		}

		// -------------------------------------------------------------------------------------------------------------
		// 'Right Sidebar' - By default it shoud be ''. Other options: '', 'min', 'compact', 'max'.
		// -------------------------------------------------------------------------------------------------------------
		$right_vertical_sidebar__is_show           = $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'right_vertical_sidebar__is_show' );
		$right_vertical_sidebar__default_view_mode = strval( $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'right_vertical_sidebar__default_view_mode' ) );
		$right_vertical_sidebar__default_view_mode = ( ! empty( $right_vertical_sidebar__default_view_mode ) ) ? $right_vertical_sidebar__default_view_mode . '_right' : $right_vertical_sidebar__default_view_mode;

		if ( ! empty( $right_vertical_sidebar__default_view_mode ) ) {
			if ( 'none_right' === $right_vertical_sidebar__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_right__do_hide(); } ); </script>';
			}
			if ( 'min_right' === $right_vertical_sidebar__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_right__do_min(); } ); </script>';
			}
			if ( 'compact_right' === $right_vertical_sidebar__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_right__do_compact(); } ); </script>';
			}
			if ( 'max_right' === $right_vertical_sidebar__default_view_mode ) {
				echo '<script type="text/javascript"> jQuery( document ).ready( function () { wpbc_admin_ui__sidebar_right__do_max(); } ); </script>';
			}
		}
		// -------------------------------------------------------------------------------------------------------------


		// Top  wrapper  for admin  pages.
		echo '<div class="wpbc_admin' .
						( ( $right_vertical_sidebar__is_show ) ? ' right_vertical_sidebar_displayed ' : '' ) .
			           	' wpbc_admin_page__tab__' . esc_attr( $this->active_tab ) .
			 			' wpbc_admin_page__subtab__' . esc_attr( $this->active_subtab ) .
					'">';

		wpbc_ui__top_nav(
			array(
				'page_tag'           => $this->active_page,
				'active_page_tab'    => $this->active_tab,
				'active_page_subtab' => $this->active_subtab,
			)
		);

		// Fires Before showing settings Content page ( for example in setup  Wizard for show_top_right_wizard_button ).
		do_action( 'wpbc_after_wpbc_page_top__header_tabs', $this->active_page, $this->active_tab, $this->active_subtab );

		echo '<div class="wpbc_settings_page_wrapper ' .
							esc_attr( $left_navigation__default_view_mode ) . ' ' .
							esc_attr( $right_vertical_sidebar__default_view_mode ) .
						'">';

		// Left  vertical  menu.
		wpbc_ui__left_vertical_nav(
			array(
				'active_page'   => $this->active_page,         // wpbc-settings.
				'active_tab'    => $this->active_tab,          // calendar_appearance.
				'active_subtab' => $this->active_subtab,       // calendar_appearance_skin.
				'page_nav_tabs' => $this->page_structure_obj->get_nav_tabs(),
			)
		);

		?>
		<div class="wpbc_settings_page_content">
			<div id="<?php echo esc_attr( $this->active_page ); ?>-admin-page" class="wrap wpbc_page wpbc_page_tab__<?php echo esc_attr( $this->active_tab ); ?> wpbc_page_subtab__<?php echo esc_attr( $this->active_subtab ); ?>">
				<div class="wpbc_admin_message"></div>
				<div class="wpbc_admin_page">
					<div id="ajax_working"></div>
					<div class="clear wpbc_header_margin" style="height:0px;"></div>
					<div id="ajax_respond" class="ajax_respond" style="display:none;"></div>
					<div class="clear"></div>
					<?php

					$is_show_top_path = $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'is_show_top_path' );
					if ( $is_show_top_path ) {
						wpbc_ui_settings__top_path();
					}

					$is_show_top_navigation = $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'is_show_top_navigation' );
					if ( $is_show_top_navigation ) {
						// Top horisonal menu.
						wpbc_ui__top_horisontal_nav(
							array(
								'active_page'   => $this->active_page,         // wpbc-settings.
								'active_tab'    => $this->active_tab,          // calendar_appearance.
								'active_subtab' => $this->active_subtab,       // calendar_appearance_skin.
								'page_nav_tabs' => $this->page_structure_obj->get_nav_tabs(),
							)
						);
					}

					$this->show_title_and_description();
	}


	/**
	 *  Show Template Footer
	 *
	 * @return void
	 */
	public function template_footer() {

		echo '</div><!-- wpbc_admin_page -->';
		echo '</div><!-- wpbc_page -->';
		echo '</div><!-- .wpbc_settings_page_content -->';

		// Right vertical sidebar.
		$right_vertical_sidebar__is_show = $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'right_vertical_sidebar__is_show' );
		if ( $right_vertical_sidebar__is_show ) {

			wpbc_ui__right_vertical_sidebar(
				array(
					'active_page'   => $this->active_page,         // wpbc-settings.
					'active_tab'    => $this->active_tab,          // calendar_appearance.
					'active_subtab' => $this->active_subtab,       // calendar_appearance_skin.
					'page_nav_tabs' => $this->page_structure_obj->get_nav_tabs(),
				)
			);

			$right_vertical_sidebar_compact__is_show = $this->page_structure_obj->is_use_option__in_subtabs_or_tabs( 'right_vertical_sidebar_compact__is_show' );
			if ( $right_vertical_sidebar_compact__is_show ) {
				wpbc_ui__right_vertical_sidebar_compact(
					array(
						'active_page'   => $this->active_page,         // wpbc-settings.
						'active_tab'    => $this->active_tab,          // calendar_appearance.
						'active_subtab' => $this->active_subtab,       // calendar_appearance_skin.
						'page_nav_tabs' => $this->page_structure_obj->get_nav_tabs(),
					)
				);
			}
		}

		echo '</div><!-- .wpbc_settings_page_wrapper -->';
		echo '</div><!-- .wpbc_admin -->';


		/**
		 * == Full Screen Mode Checking ==  Defined in -> public function add_loading_classes( $classes ) .
		 *
		 * If defined full screen ( checked by exists .wpbc_admin_full_screen in body),
		 * then this code, remove the 'wp-toolbar' class from html tag
		 * and set  correct 'full screen'  or 'normal'  buttons in the rop  right side!
		 */
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<script type="text/javascript">' . wpbc_jq_ready_start() . 'wpbc_check_full_screen_mode();' . wpbc_jq_ready_end() . '</script>';
	}


	/**
	 * Show Header Title and Description
	 *
	 * @return void
	 */
	private function show_title_and_description() {

		$current_page_params_arr = $this->page_structure_obj->get_current_page_params();

		if (
			( ! empty( $current_page_params_arr['subtab'] ) ) &&
			( ! empty( $current_page_params_arr['subtab']['tag'] ) ) &&
			( $current_page_params_arr['subtab']['tag'] === $this->active_subtab )
		) {                                                                                    // Active: Subtab.
			// Header - Title.
			if ( isset( $current_page_params_arr['subtab']['page_title'] ) ) {
				$this->show_title( $current_page_params_arr['subtab']['page_title'] );
			}
			// Header - Title Description.
			if ( isset( $current_page_params_arr['subtab']['page_description'] ) ) {
				$this->show_title_description( $current_page_params_arr['subtab']['page_description'] );
			} elseif ( isset( $current_page_params_arr['subtab']['hint'] ) ) {
				$this->show_title_description( $current_page_params_arr['subtab']['hint'] );
			}
		} elseif ( ! empty( $current_page_params_arr['tab'] ) ) {                              // Active: Main tab.
			// Header - Title.
			if ( isset( $current_page_params_arr['tab']['page_title'] ) ) {
				$this->show_title( $current_page_params_arr['tab']['page_title'] );
			}
			if ( isset( $current_page_params_arr['tab']['page_description'] ) ) {
				$this->show_title_description( $current_page_params_arr['tab']['page_description'] );
			} elseif ( isset( $current_page_params_arr['tab']['hint'] ) ) {
				$this->show_title_description( $current_page_params_arr['tab']['hint'] );
			}
		}
	}


	/**
	 * Show Title
	 *
	 * @param string $value - title of the page.
	 *
	 * @return void
	 */
	private function show_title( $value ) {
		if ( ! empty( $value ) ) {
			echo '<h1 class="wpbc_settings_page_header_title">' . wp_kses_post( $value ) . '</h1>';
		}
	}


	/**
	 * Show Title Description
	 *
	 * @param string|array $value - Title Description of the page.
	 *
	 * @return void
	 */
	private function show_title_description( $value ) {

		if ( ! empty( $value ) ) {
			if ( is_array( $value ) ) {

				if ( ! empty( $value['title'] ) ) {
					echo '<p class="wpbc_settings_page_header_title_description">' . wp_kses_post( $value['title'] ) . '</p>';
				}
			} else {
				echo '<p class="wpbc_settings_page_header_title_description">' . wp_kses_post( $value ) . '</p>';
			}
		}
	}
}
