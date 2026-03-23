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

/**
 * == All Page Parameters ==
 *
 * TODO Refactorig: Continue work with description of the:   List of all parameters, that possible to use in  public function tabs() { ... }

'is_default_full_screen'                    => false,                                                                   // true | false.     Default: false.
'right_vertical_sidebar__is_show'           => false,                                                                   // true | false.     Default: false.
'right_vertical_sidebar__default_view_mode' => 'max',                                                                   // '' | 'min' | 'compact' | 'max' | 'none'.     Default: ''.
'left_navigation__default_view_mode'        => 'max',                                                                   // '' | 'min' | 'compact' | 'max' | 'none'.     Default: ''.
'right_vertical_sidebar_compact__is_show'   => false,                                                                   // true | false.     Default: false.

// TODO: recheck functionality  and ability to use:

	'is_show_top_path'                          => true,                                                                    // true | false.  By default value is: false.
	'is_show_top_navigation'                    => false,                                                                   // true | false.  By default value is: false.
	'title'                                     => __( 'Booking Form Builder', 'booking' ),                                 // Title of TAB //FixIn: 9.8.15.2.2.
	'hint'                                      => __( 'Define available days', 'booking' ),                                // Hint.
	'page_title'                                => __( 'Booking Form Builder', 'booking' ),                                 // Title of Page.
	'link'                                      => '',                                                                      // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link.
	'position'                                  => '',                                                                      // 'left'  /  'right'  /  ''.
	'css_classes'                               => '',                                                                      // CSS c l a s s(es).
	'icon'                                      => '',                                                                      // Icon - link to the real PNG img.
	'font_icon'                                 => 'wpbc_icn_flip_x0 wpbc-bi-input-cursor-text',                            // 'wpbc_icn_free_cancellation' // CSS definition  of forn Icon.
	'font_icon_right'                           => 'wpbc-bi-asterisk',
	'default'                                   => false,                                                                   // Is this tab activated by default or not: true || false.
	'disabled'                                  => false,                                                                   // Is this tab disbaled: true || false.
	'hided'                                     => false,                                                                   // Is this tab hided: true || false.
	'subtabs'                                   => array(),
	'folder_style'                              => 'order:11;',

 */


/**
 * Define Settings Page Structure
 */
abstract class WPBC_Page_Structure extends WPBC_Menu_Structure {

	// FixIn: 11.0.0.1.

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		// This Hook fire in the class WPBC_Admin_Menus for showing page content of specific menu.
		add_action( 'wpbc_page_structure_show', array( $this, 'content_structure' ) );
	}

	// -----------------------------------------------------------------------------------------------------------------
	// Abstract Methods
	// -----------------------------------------------------------------------------------------------------------------
	// FixIn: 10.11.4.4.  Removed abstarct  classes that exists in the parent class.

	/**
	 * Child classes ovveride it for auto update / save options in main form
	 *
	 * @return void
	 */
	public function maybe_update() {}


	/**
	 * Child classes ovveride  --  show vertical "Right Sidebar Content".
	 *  // FixIn: 10.14.1.3.
	 *
	 * @return void
	 */
	public function right_sidebar_content(){}



	/**
	 * Child classes ovveride  --  show vertical "Right Sidebar Content - Compact".
	 *  // FixIn: 10.14.1.3.
	 *
	 * @return void
	 */
	public function right_sidebar_compact_content(){}


	// -----------------------------------------------------------------------------------------------------------------
	// C O N T E N T
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * General Page Structure
	 *
	 * @param string $page_tag - its the same that  return $this->in_page ().
	 */
	public function content_structure( $page_tag ) {

		// -------------------------------------------------------------------------------------------------------------
		// Checking if this page  -  A C T I V E
		// -------------------------------------------------------------------------------------------------------------
		if ( ! $this->is_page_activated() ) {
			return false;
		}

		$active_page_tab    = $this->get_active__tab__tag();
		$active_page_subtab = $this->get_active__subtab__tag();

		// Fires Before showing settings Content page. Used in MultiUser version for blocking access to some pages.
		$is_show_this_page = apply_filters( 'wpbc_before_showing_settings_page_is_show_page', true, $page_tag, $active_page_tab, $active_page_subtab );
		if ( false === $is_show_this_page ) {
			return false;
		}

		$this->update_nav_tabs_structure( $page_tag, $active_page_tab, $active_page_subtab );

		// -------------------------------------------------------------------------------------------------------------
		// --  U p d a t e  --
		// -------------------------------------------------------------------------------------------------------------
		$this->maybe_update();

		// -------------------------------------------------------------------------------------------------------------
		// --  T e m p l a t e  --
		// -------------------------------------------------------------------------------------------------------------

		// Hook: Fires Before showing settings Content page.
		do_action( 'wpbc_before_settings_content', $page_tag, $active_page_tab, $active_page_subtab );


		$template_params_arr = array(
			'active_page'        => $page_tag,
			'active_tab'         => $active_page_tab,
			'active_subtab'      => $active_page_subtab,
			'page_structure_obj' => $this,
		);
		$settings_templates  = new WPBC_Settings_Page_Parts( $template_params_arr );

		// ---------------------------------------------------------------------------------------------------------
		// Template Header
		// ---------------------------------------------------------------------------------------------------------
		$settings_templates->template_header();

		wp_nonce_field( 'wpbc_ajax_admin_nonce', 'wpbc_admin_panel_nonce', true, true );                                // Nonce.

		// ---------------------------------------------------------------------------------------------------------
		// Content.
		// ---------------------------------------------------------------------------------------------------------
		if ( ( isset( $this->current_page_params['subtab'] ) ) && ( isset( $this->current_page_params['subtab']['content'] ) ) ) {
			call_user_func( array( $this, $this->current_page_params['subtab']['content'] ) );
		} elseif ( ( isset( $this->current_page_params['tab'] ) ) && ( isset( $this->current_page_params['tab']['content'] ) ) ) {
			call_user_func( array( $this, $this->current_page_params['tab']['content'] ) );
		} else {
			$this->content();
		}

		do_action( 'wpbc_show_settings_content', $page_tag, $active_page_tab, $active_page_subtab );                    // Hook.

		// ---------------------------------------------------------------------------------------------------------
		// Template Footer
		// ---------------------------------------------------------------------------------------------------------
		$settings_templates->template_footer();

		do_action( 'wpbc_after_settings_content', $page_tag, $active_page_tab, $active_page_subtab );                   // Hook: Fires After showing settings Content page.
	}


	// -----------------------------------------------------------------------------------------------------------------
	// ==  Support  ==
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Update structure of self::$nav_tabs with  'is_active' and 'url' parameters.
	 * We can  do  this here,  because this function executed at  step  of showing content,  and we know about all  the structure of the menus, already.
	 *
	 * Based on  URL: /admin.php?page=wpbc-settings&tab=email&subtab=new-visitor
	 *
	 * @param string $page_tag           - 'wpbc-settings'.
	 * @param string $active_page_tab    - 'email'.
	 * @param string $active_page_subtab - 'new-visitor'.
	 *
	 * @return bool
	 */
	private function update_nav_tabs_structure( $page_tag, $active_page_tab, $active_page_subtab ) {

		if ( empty( self::$nav_tabs ) ) {
			return false;
		}

		// Menu Pages [Bookings, Resources, Prices, Settings  ].
		foreach ( self::$nav_tabs as $nav_page_tag => $nav_page_arr ) {

			// Tabs [ General, Emails, ... ].
			foreach ( self::$nav_tabs[ $nav_page_tag ] as $nav_tab_tag => $nav_tab_arr ) {

				self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['is_active'] = false;
				self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['url']       = ( ! empty( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['link'] ) )
																					? self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['link']
																					: $this->get_tab_url( $nav_page_tag, $nav_tab_tag );
				// Inside of this page 'wpbc-settings' ?
				if ( $nav_page_tag === $page_tag ) {

					// Tab not selected -> clicked on Left menu only,  but this is 'DEFAULT' TAB,  so true.
					if ( ( empty( $active_page_tab ) ) && ( ! empty( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['default'] ) ) ) {
						self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['is_active'] = true;
					}

					// Page and Tab clicked !
					if ( ( $nav_page_tag === $page_tag ) && ( $active_page_tab === $nav_tab_tag ) ) {
						self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['is_active'] = true;
					}
				}

				if ( ! empty( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'] ) ) {
					// SubTabs e.g.: [ "New booking", "Approved", ... ].
					foreach ( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'] as $nav_subtab_tag => $nav_subtab_arr ) {

						self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['is_active'] = false;

						self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['url'] = ( ! empty( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['link'] ) )
																										? self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['link']
																										: $this->get_tab_url( $nav_page_tag, $nav_tab_tag, $nav_subtab_tag );
						// We know that this TAB is active.
						if ( ! empty( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['is_active'] ) ) {

							// SubTab not selected -> clicked on Left menu and Tab only,  but not clicked on submenu (e.g. Stripe or "approved email")  and if we have this is 'DEFAULT' TAB,  so true.
							if ( ( empty( $active_page_subtab ) ) && ( ! empty( self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['default'] ) ) ) {
								self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['is_active'] = true;
							}

							// Page and SubTab clicked !
							if ( $active_page_subtab === $nav_subtab_tag ) {
								self::$nav_tabs[ $nav_page_tag ][ $nav_tab_tag ]['subtabs'][ $nav_subtab_tag ]['is_active'] = true;
							}
						}
					}
				}
			}
		}
		return true;
	}

	/**
	 * Get current active TAB 'Tag'
	 *
	 * @return mixed|string
	 */
	protected function get_active__tab__tag() {

		$active_page_tab = '';

		if ( ( isset( $this->current_page_params['tab'] ) ) && ( ! empty( $this->current_page_params['tab']['tag'] ) ) ) {
			$active_page_tab = $this->current_page_params['tab']['tag'];
		}

		return $active_page_tab;
	}

	/**
	 * Get current active SubTAB 'Tag'
	 *
	 * @return mixed|string
	 */
	protected function get_active__subtab__tag() {

		$active_page_subtab = '';

		if ( ( isset( $this->current_page_params['subtab'] ) ) && ( ! empty( $this->current_page_params['subtab']['tag'] ) ) ) {
			$active_page_subtab = $this->current_page_params['subtab']['tag'];
		}

		return $active_page_subtab;
	}

	/**
	 * Get Option. Check if this defined in Tabs or Subtabs array Otherwise return false.
	 *
	 * @param string $option_name  - name of the option,  like 'is_use_new_settings_skin'.
	 *
	 * @return false|mixed
	 *
	 * Example:             $is_use_new_settings_skin = $this->is_use_option__in_subtabs( 'is_use_new_settings_skin' );
	 */
	public function is_use_option__in_subtabs_or_tabs( $option_name ) {

		// Check if this otion defined in tabs or 'subtabs' ?
		$is_use_option = $this->is_use_option__in_subtabs( $option_name );

		// If this option  not defined in 'subtabs', then we check it in 'tab'.
		if ( false === $is_use_option ) {
			$is_use_option = ( $this->is_use_option__in_tabs( $option_name ) );
		}

		return $is_use_option;
	}

	/**
	 * Get Option. Check if this defined in subtabs array Otherwise return false.
	 *
	 * @param string $option_name  - name of the option,  like 'is_use_new_settings_skin'.
	 *
	 * @return false|mixed
	 *
	 * Example:             $is_use_new_settings_skin = $this->is_use_option__in_subtabs( 'is_use_new_settings_skin' );
	 */
	public function is_use_option__in_subtabs( $option_name ) {

		// Check if this otion defined in subtabs ?
		$is_use_option = ( ( isset( $this->current_page_params['subtab'] ) ) && ( ! empty( $this->current_page_params['subtab'][ $option_name ] ) ) )
								? $this->current_page_params['subtab'][ $option_name ]
								: false;

		return $is_use_option;
	}

	/**
	 * Get Option. Check if this defined in btabs array Otherwise return false.
	 *
	 * @param string $option_name  - name of the option,  like 'is_use_new_settings_skin'.
	 *
	 * @return false|mixed
	 *
	 * Example:             $is_use_new_settings_skin = $this->is_use_option__in_subtabs( 'is_use_new_settings_skin' );
	 */
	public function is_use_option__in_tabs( $option_name ) {

		// Check if this otion defined in subtabs ?
		$is_use_option = ( ( isset( $this->current_page_params['tab'] ) ) && ( ! empty( $this->current_page_params['tab'][ $option_name ] ) ) )
								? $this->current_page_params['tab'][ $option_name ]
								: false;

		return $is_use_option;
	}

	/**
	 * Get Navigation path for header, if applicable.
	 * Used in Setup Wizard page.
	 *
	 * @return false|array
	 */
	protected function is_use_navigation_path_arr() {

		// Use Top line as Path in Setup Wizard page  // FixIn: 10.4.0.2.
		$is_use_navigation_path_arr = $this->is_use_option__in_subtabs( 'is_use_navigation_path' );

		return $is_use_navigation_path_arr;
	}

	/**
	 * Get URL of settings page, based on Page Slug and Tab Slug    e.g. page=wpbc-settings&tab=email&subtab=approved
	 *
	 * @param string $page_tag   - e.g. 'wpbc-settings'.
	 * @param string $tab_name   - e.g. 'email'.
	 * @param string $subtab_name ( Optional )   - e.g. 'approved'.
	 *
	 * @return string - Escaped URL to plugin  page.
	 */
	private function get_tab_url( $page_tag, $tab_name, $subtab_name = false ) {
		if ( false === $subtab_name ) {
			return esc_url( admin_url( add_query_arg( array( 'page' => $page_tag, $this->tags['tab'] => $tab_name ), 'admin.php' ) ) );
		} else {
			return esc_url( admin_url( add_query_arg( array( 'page' => $page_tag, $this->tags['tab'] => $tab_name, $this->tags['subtab'] => $subtab_name ), 'admin.php' ) ) );
		}
	}
}