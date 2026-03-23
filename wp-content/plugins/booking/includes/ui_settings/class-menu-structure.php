<?php
/**
 * Plugin Menu Structure in Admin Panel
 *
 * @version  1.0
 * @package  Any
 * @category Plugin Menu Structure - in Admin Panel
 * @author   wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified  2025-02-09
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// FixIn: 11.0.0.1.

/**
 * Define Settings Page Structure
 */
abstract class WPBC_Menu_Structure {

	/**
	 * Parameters for current page,  if this page selected,  otherwise its = empty array()
	 *
	 * @var array
	 */
	protected $current_page_params;

	/**
	 * Tabs array, same for all objects.
	 *
	 * @var array
	 */
	protected static $nav_tabs;

	/**
	 * Defining Name of parameter in GET request for Navigation TOP and BOTTOM tabs:        - $_GET[ 'tab' ]    == 'payment'        - $_GET[ 'subtab' ] == 'paypal'.
	 *
	 * @var array
	 */
	protected $tags;


	/**
	 * Constructor
	 */
	public function __construct() {

		self::$nav_tabs = array();

		$this->current_page_params = array();

		$this->tags = array();

		// Defining Name of parameter in GET request - $_GET[ 'tab' ]    == 'payment'.
		$this->tags['tab'] = 'tab';

		// Defining Name of parameter in GET request - $_GET[ 'subtab' ] == 'paypal'.
		$this->tags['subtab'] = 'subtab';

		// FixIn: 10.14.1.2.
		// This Hook fire after creation menu in class WPBC_Admin_Menus.
		add_action( 'wpbc_define_nav_tabs', array( $this, 'wpbc_create_plugin_menu_structure_arr' ), 10, 1 );

		// Such calls requires for ability to use methods, such  as  $this->is_use_option__in_subtabs_or_tabs('...'),  which  can be run  only  after_tabs_defined !
		// We set here priority 11, to load this function, after 'wpbc_create_plugin_menu_structure_arr', where defined all tabs.
		add_action( 'wpbc_define_nav_tabs', array( $this, 'after_tabs_defined' ), 11, 1 );

		// Right Vertical Sidebar.                                                                                      // FixIn: 10.14.1.3.
		add_action( 'wpbc_ui__right_vertical_sidebar_content', array( $this, 'maybe_show_right_sidebar_content' ) );

		// Right Vertical Sidebar - Compact.                                                                                      // FixIn: 10.14.1.3.
		add_action( 'wpbc_ui__right_vertical_sidebar_compact_content', array( $this, 'maybe_show_right_sidebar_compact_content' ) );
	}


	// -----------------------------------------------------------------------------------------------------------------
	// Abstract Methods
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Define slug in what menu to show this page.                             // Parameter relative: $_GET['page'].
	 *
	 * @return string
	 *
	 * Example:
	 *
	 * return 'wpbc-settings';
	 */
	abstract public function in_page();

	/**
	 * Define Tabs and Subtabs of this Admin Page
	 *
	 * @return array();
	 *
	 * Example:
	 *
	 *  $tabs = array();
	 *  $tabs[ 'form' ] = array(
	 *                        'title' => __('Form','booking')                   // Title of TAB
	 *                      , 'hint' => __('Customization of Form Fields', 'booking')            // Hint
	 *                      , 'page_title' =>ucwords( __('Form fields', 'booking') )     // Title of Page
	 *                      //, 'link' => ''                                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
	 *                      //, 'position' => ''                                // 'left'  ||  'right'  ||  ''
	 *                      //, 'css_classes' => ''                             // CSS class(es)
	 *                      //, 'icon' => ''                                    // Icon - link to the real PNG img
	 *                      , 'font_icon' => 'wpbc_icn_draw'         // CSS definition  of forn Icon
	 *                      , 'default' => false                                // Is this tab activated by default or not: true || false.
	 *                      , 'disabled' => false                               // Is this tab disbaled: true || false.
	 *                      , 'hided'   => false                                // Is this tab hided: true || false.
	 *                      , 'subtabs' => array()
	 *
	 *  );
	 *  $tabs[ 'upgrade' ] = array(
	 *                        'title' => __('Upgrade','booking')                // Title of TAB
	 *                      , 'hint' => __('Upgrade to higher version', 'booking')              // Hint
	 *                      //, 'page_title' => __('Upgrade', 'booking')        // Title of Page
	 *                      , 'link' => 'http://server.com/'                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
	 *                      , 'position' => 'right'                             // 'left'  ||  'right'  ||  ''
	 *                      //, 'css_classes' => ''                             // CSS class(es)
	 *                      //, 'icon' => ''                                    // Icon - link to the real PNG img
	 *                      , 'font_icon' => 'wpbc_icn_auto_graph'// CSS definition  of forn Icon
	 *                      //, 'default' => false                              // Is this tab activated by default or not: true || false.
	 *                      //, 'subtabs' => array()
	 *
	 *  );
	 *
	 *  $subtabs = array();
	 *
	 *  $subtabs['fields'] = array(
	 *                      'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
	 *                      , 'title' => __('Form','booking')                   // Title of TAB
	 *                      , 'page_title' => __('Form Settings', 'booking')                            // Title of Page
	 *                      , 'hint' => __('Customization of Form Settings', 'booking')                // Hint
	 *                      , 'link' => ''                                      // link
	 *                      , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
	 *                      , 'css_classes' => ''                               // CSS class(es)
	 *                      //, 'icon' => 'https://www.paypalobjects.com/webstatic/icon/pp258.png'      // Icon - link to the real PNG img
	 *                      //, 'font_icon' => 'wpbc_icn_payment'                        // CSS definition of Font Icon
	 *                      , 'default' =>  true                                // Is this sub tab activated by default or not: true || false.
	 *                      , 'disabled' => false                               // Is this sub tab deactivated: true || false.
	 *                      , 'checkbox'  =>  false                             // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )
	 *                      , 'content' => 'content'                            // Function to load as conten of this TAB
	 *                  );
	 *
	 *  $subtabs['form-separator'] = array(
	 *                      'type' => 'separator'                               // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
	 *                  );
	 *  $subtabs['form-goto'] = array(
	 *                      'type' => 'goto-link'                               // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
	 *                      , 'title' =>ucwords( __('Form fields', 'booking') )   // Title of TAB
	 *                      , 'hint' => ''                                      // Hint
	 *                      , 'show_section' => 'id_of_show_section'            // ID of HTML element, for scroll to.
	 *                  );
	 *
	 *  ob_start();
	 *  ...
	 *  $html_element_data = ob_get_clean();
	 *
	 *  $subtabs['form-selection'] = array(
	 *                      'type' => 'html'                                    // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
	 *                      , 'html' => $html_element_data
	 *                  );
	 *
	 *  $subtabs['form-save'] = array(
	 *                      'type' => 'button'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
	 *                      , 'title' => __('Save Changes','booking')           // Title of TAB
	 *                      , 'form' => 'wpbc_form'                             // Required for 'button'!  Name of Form  to submit
	 *                  );
	 *
	 *  $tabs[ 'form' ][ 'subtabs' ] = $subtabs;
	 *
	 *  return $tabs;
	 */
	abstract public function tabs();

	/**
	 * Show Content of this page - Main function.
	 *
	 *  In top  of this function  have to be checking ubout Update (saving POST request).
	 *
	 *   Exmaple:
	 *
	 *  //  S u b m i t  ///////////////////////////////////////////////////////
	 *
	 *  $this_submit_form  = 'wpbc_emails_toolbar';                             // Define form name
	 *
	 *  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	 *  if ( isset( $_POST['is_form_sbmitted_'. $this_submit_form ] ) ) {
	 *
	 *      // Check   N o n c e
	 *      check_admin_referer( 'wpbc_settings_page_' . $this_submit_form  );  // Its stop show anything on submiting, if its not refear to the original page
	 *
	 *      // Make Update of settings
	 *      $edit_field_data = $this->update_wpbc_emails_toolbar( $menu_slug );
	 *  }
	 */
	abstract public function content();


	// -----------------------------------------------------------------------------------------------------------------
	// Right Vertical Sidebar.                                                                                          // FixIn: 10.14.1.3.
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Show content of right sidebar.  Overide it in child classes !
	 *
	 * @return void
	 */
	abstract public function right_sidebar_content();


	/**
	 * Helper method to show 'right sidebar' only if it is enabled page!
	 *
	 * @return void
	 */
	public function maybe_show_right_sidebar_content(){

		if ( ! $this->is_page_activated() ) {
			return false;
		}

		$this->right_sidebar_content();
	}

	/**
	 * Show content of right sidebar - Compact.  Overide it in child classes !
	 *
	 * @return void
	 */
	abstract public function right_sidebar_compact_content();


	/**
	 * Helper method to show 'right sidebar' - Compact only if it is enabled page!
	 *
	 * @return void
	 */
	public function maybe_show_right_sidebar_compact_content(){

		if ( ! $this->is_page_activated() ) {
			return false;
		}

		$this->right_sidebar_compact_content();
	}

	// -----------------------------------------------------------------------------------------------------------------
	// Methods After 'Tabs' defined.                                                                                    // FixIn: 10.14.1.2.
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * We already  prepeared all tabs and can use function such  as $this->is_page_activated();
	 * // FixIn: 10.14.1.2.
	 *
	 * @return void
	 */
	public function after_tabs_defined() {

		// This call -> Initiate definition  of all parameters  for current page.
		$is_page_activated = $this->is_page_activated();

		if ( $is_page_activated ) {
			add_filter( 'admin_body_class', array( $this, 'admin_body_class__add_loading_classes' ) );
		}
	}


	/**
	 * Check if this page have to load in default Full  Screen  mode,
	 * because of parameter 'is_default_full_screen' => false  in ->tabs(...) method.
	 * If yes,  then  we add css class  'wpbc_admin_full_screen'  to  the body  of the page.
	 * // FixIn: 10.14.1.2.
	 *
	 * @param string $classes - CSS classes.
	 *
	 * @return mixed|string
	 */
	public function admin_body_class__add_loading_classes( $classes ) {

		// Such method  ->is_use_option__in_subtabs_or_tabs(...)  can be load only  after_tabs_defined !
		if ( $this->is_use_option__in_subtabs_or_tabs( 'is_default_full_screen' ) ) {
			$classes .= ' wpbc_admin_full_screen';
		}

		return $classes;
	}


	// -----------------------------------------------------------------------------------------------------------------
	// Getters
	// -----------------------------------------------------------------------------------------------------------------

	public function get_current_page_params() {
		return $this->current_page_params;
	}

	public function get_nav_tabs() {
		return self::$nav_tabs;
	}

	// -----------------------------------------------------------------------------------------------------------------
	// Active Page Parameters
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Check if this page selected (active), depend from the GET parameter
	 *  If selected, then  define Current Page Parameters.
	 *
	 * @return boolean
	 */
	protected function is_page_activated() {

		$is_page_selected = false;

		$this_page     = $this->in_page();
		$this_page_arr = ( is_array( $this_page ) ) ? $this_page : array( $this_page );

		$request_arr           = array();
		$request_arr['page']   = isset( $_REQUEST['page'] )                                 // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) )                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			: null;
		$request_arr['tab']    = isset( $_REQUEST[ $this->tags['tab'] ] )                   // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			? sanitize_text_field( wp_unslash( $_REQUEST[ $this->tags['tab'] ] ) )          // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			: null;
		$request_arr['subtab'] = isset( $_REQUEST[ $this->tags['subtab'] ] )                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			? sanitize_text_field( wp_unslash( $_REQUEST[ $this->tags['subtab'] ] ) )       // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			: null;

		foreach ( $this_page_arr as $this_page ) {

			foreach ( $this->tabs() as $this_tab_tag => $this_tab ) {

				// FixIn: 10.11.3.5.1.
				$not_skip_type_arr = array( 'tab', 'subtab' );

				if ( ( empty( $this_tab['type'] ) ) || ( in_array( $this_tab['type'], $not_skip_type_arr, true ) ) ) {
					// Probably  it is not the separator,  and we have no content here !
					// Get First Tab Element,  which  MUST be subtab element, all other tabs, can  be links,  not really  showing content!!!
					break;
				}
			}

			if ( empty( $this_tab ) ) {
				return $this_page;      // this page empty - tabs is empty  array,  probabaly  its was redefined in child CLASS to $tabs = array(); for not ability to open this page.
			}

			$this_subtab_tag = 0;
			$this_subtab     = array( 'default' => false );

			if ( isset( $this_tab['subtabs'] ) ) {
				foreach ( $this_tab['subtabs'] as $temp_this_subtab_tag => $temp_this_subtab ) {

					// Get First Subtab element from  subtabs array.
					if ( 'subtab' === $temp_this_subtab['type'] ) {
						$this_subtab_tag = $temp_this_subtab_tag;
						$this_subtab     = $temp_this_subtab;
						break;
					}
				}
			}

			if ( ( isset( $request_arr['page'] ) ) && ( $this_page === $request_arr['page'] ) ) {                       // We are inside of this page. Menu item selected.

				if (
					( ! isset( $request_arr['tab'] ) )                                                                  // the TAB      NOT     selected    and  Default.
					// && ( ! isset( $request_arr['subtab'] ) )                                                         // the SubTab   NOT     selected.
					&& ( isset( $this_tab['default'] ) ) && ( $this_tab['default'] ) ) {
					$is_page_selected = true;
				}

				if (
					( isset( $request_arr['tab'] ) )                                                                    // TAB Selected.
					&& ( ! isset( $request_arr['subtab'] ) )                                                            // SubTab   NOT     selected    &&      ! exist     ||  Default.
					&& ( $request_arr['tab'] === $this_tab_tag ) && ( ( 0 === $this_subtab_tag ) || ( $this_subtab['default'] ) ) ) {
					$is_page_selected = true;
				}

				if (
					( isset( $request_arr['tab'] ) )                                                                    // TAB      Selected.
					&& ( isset( $request_arr['subtab'] ) )                                                              // SubTab   Selected.
					&& ( $request_arr['tab'] === $this_tab_tag )
					&& ( $request_arr['subtab'] === $this_subtab_tag ) ) {
					$is_page_selected = true;
				}
			}

			// If this page activated,  then define Current Page parameters.
			if ( $is_page_selected ) {
				$this->define_current_page_parameters( $this_tab_tag, $this_tab, $this_subtab_tag, $this_subtab );
			}

			if ( $is_page_selected ) {
				break;
			}
		}

		return $is_page_selected;
	}


	/**
	 * Define parameters for current selected page
	 *
	 * @param string $this_tab_tag    = 'email'.
	 * @param array  $this_tab        = [ 'title' => 'Emails', 'font_icon' => 'wpbc_icn_mail_outline',  'subtabs' => [  'new-admin' => [ 'type' => 'subtab', 'title' => 'New Booking (admin)', ... ],  ].
	 * @param string $this_subtab_tag = 'new-admin'.
	 * @param array  $this_subtab     = [ 'type' => 'subtab', 'title' => 'New Booking (admin)', 'page_title' => 'Emails Settings',... ].
	 *
	 * @return void
	 */
	protected function define_current_page_parameters( $this_tab_tag, $this_tab, $this_subtab_tag, $this_subtab ) {

		$this->current_page_params = array(
			'tab'    => array_merge( $this_tab, array( 'tag' => $this_tab_tag ) ),
			'subtab' => array_merge( $this_subtab, array( 'tag' => $this_subtab_tag ) ),
		);
	}


	/**
	 * Get all SubTabs of current opened page Tab
	 *
	 * @param string $menu_in_page_tag - Optional. Menu Tag, the same as $this->in_page ();.
	 *
	 * @return array
	 */
	protected function get_all_sub_tabs_of_selected_tab( $menu_in_page_tag = false ) {

		if ( false === $menu_in_page_tag ) {

			$this_page     = $this->in_page();
			$this_page_arr = ( is_array( $this_page ) ) ? $this_page : array( $this_page );

			$menu_in_page_tag = $this_page_arr[0];
		}

		$all_sub_tabs_of_selected_tab = self::$nav_tabs[ $menu_in_page_tag ][ $this->current_page_params['tab']['tag'] ]['subtabs'];

		return $all_sub_tabs_of_selected_tab;
	}



	// -----------------------------------------------------------------------------------------------------------------
	// T A B s   -  Define
	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Define TABS structure.  -   General structure of tabs for every plugin menu page.
	 *
	 * Function executed after creation menu in class WPBC_Admin_Menus.
	 */
	public function wpbc_create_plugin_menu_structure_arr() {

		/**
		 *  Array (
		 *    [wpbc-resources] => Array ()
		 *    [wpbc-settings] => Array
		 *        (
		 *            [general] => Array
		 *                (
		 *                    [title] => General
		 *                    [page_title] => General Settings
		 *                    ...
		 *                    [subtabs] => Array ()
		 *                )
		 *            [help] => Array
		 *                (
		 *                    [title] => Help
		 *                    [page_title] =>
		 *                    ...
		 *                    [subtabs] => Array ()
		 *                )
		 *            [form] => Array
		 *                (
		 *                    [title] => Form
		 *                    [hint] => Customization of Form Fields
		 *                    [page_title] => Form Fields
		 *                    ...
		 *                    [subtabs] => Array
		 *                        (
		 *                            [goto-form] => Array
		 *                                (
		 *                                    [type] => goto-link
		 *                                    [title] => Booking Form Fields
		 *                                    ...
		 *                                    [content] => content
		 *                                    [update] => update
		 *                                )
		 *
		 *                            [goto-content-data] => Array
		 *                                (
		 *                                    [type] => button
		 *                                    [title] => Save Changes
		 *                                    [form] => wpbc_form
		 *                                    ...
		 *                                )
		 *
		 *                        )
		 *
		 *                )
		 *            [payment] => Array
		 *                (
		 *                    [title] => Payments
		 *                    [hint] => Customization of Payment
		 *                    [page_title] => Payment Gateways
		 *                    ...
		 *                    [subtabs] => Array
		 *                        (
		 *                            [paypal] => Array
		 *                                (
		 *                                    [type] => subtab
		 *                                    [title] => PayPal
		 *                                    ...
		 *                                )
		 *
		 *                            [sage] => Array
		 *                                (
		 *                                    [type] => subtab
		 *                                    [title] => Sage
		 *                                    ...
		 *                                )
		 *
		 *                        )
		 *
		 *                )
		 *
		 *        )
		 *
		 *    )
		 */
		/**
		 * The self::$nav_tabs =
		 *
		 *     WP Left Menu            |    Top Horiz Menu        |        Vert Left Menu        or top submenu
		 * ? page =wpbc-settings    &    tab=email            &        subtab=new-admin
		 *
		 *  [
		 *    wpbc-settings = [
		 *                            general = [...]
		 *                            form    = [...]
		 *                        email   = [...,
		 *                                            subtabs = [
		 *                                                        new-admin = [
		 *                                                                    type = "subtab"
		 *                                                                        , ...
		 */

		$this_page     = $this->in_page();                                                            // e.g. 'wpbc-settings'.
		$this_page_arr = ( is_array( $this_page ) ) ? $this_page : array( $this_page );               // it can be like: [ 'wpbc-settings' ].

		// Menu Structure.
		foreach ( $this_page_arr as $this_page ) {

			// WP Left Menu.
			if ( ! isset( self::$nav_tabs[ $this_page ] ) ) {
				self::$nav_tabs[ $this_page ] = array();
			}

			// Top Horiz Menu - e.g. [  'calendar_appearance' ] =>  [ ... ] ].
			$local_top_main_menu_arr = $this->tabs();

			// Vert Left Menu - or top submenu.
			$local_vert_left_menu_arr = array();

			foreach ( $local_top_main_menu_arr as $tab_tag => $tab_array ) {

				if ( isset( $tab_array['subtabs'] ) ) {
					$local_vert_left_menu_arr[ $tab_tag ] = $tab_array['subtabs'];         // Create new Subtabs array.

					unset( $local_top_main_menu_arr[ $tab_tag ]['subtabs'] );              // Detach Subtabs array from Tab array. It's required for do not overwrite subtabs with  already  exist subtabs in previlosly defined tab.
				} else {
					$local_vert_left_menu_arr[ $tab_tag ] = array();
				}
			}

			foreach ( $local_top_main_menu_arr as $tab_tag => $tab_array ) {

				if ( ! isset( self::$nav_tabs[ $this_page ][ $tab_tag ] ) ) {                 // If this tab  ( for exmaple "payment") declared previously,  then  does not do  anything.
					self::$nav_tabs[ $this_page ][ $tab_tag ] = array();
				}

				foreach ( $local_top_main_menu_arr[ $tab_tag ] as $page_prop_name => $page_prop_value ) {
					if ( 'subtabs' !== $page_prop_name ) {
						self::$nav_tabs[ $this_page ][ $tab_tag ][ $page_prop_name ] = $page_prop_value;
					}
				}

				if ( ! isset( self::$nav_tabs[ $this_page ][ $tab_tag ]['subtabs'] ) ) {
					self::$nav_tabs[ $this_page ][ $tab_tag ]['subtabs'] = array();
				}
				// Merge subtabs (Ex: PayPal and Sage) and attach to current tab: (Ex: payment).
				self::$nav_tabs[ $this_page ][ $tab_tag ]['subtabs'] = array_merge( self::$nav_tabs[ $this_page ][ $tab_tag ]['subtabs'], $local_vert_left_menu_arr[ $tab_tag ] );

			}
		}

		self::$nav_tabs = apply_filters( 'wpbc_plugin_menu_structure_arr', self::$nav_tabs, $this_page );
	}


	/**
	 * Get array  of visible TABs
	 * Tabs that  do not hided or disbaled
	 *
	 * @param string $menu_in_page_tag - Menu Tag, the same as $this->in_page ();.
	 *
	 * @return type
	 */
	protected function get_visible_tabs( $menu_in_page_tag ) {

		$visible_tabs = array();

		foreach ( self::$nav_tabs[ $menu_in_page_tag ] as $tab_tag => $tab ) {

			if ( empty( $tab['disabled'] ) && empty( $tab['hided'] ) ) {
				$visible_tabs[ $tab_tag ] = $tab;
			}
		}

		return $visible_tabs;
	}
}
