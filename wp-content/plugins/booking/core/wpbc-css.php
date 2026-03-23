<?php /**
 * @version 1.0
 * @package 
 * @category Core
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2013.10.16
 */

class WPBC_CSS extends WPBC_JS_CSS {

	public function define() {

		$this->setType( 'css' );

		/**
		 * // Exmaples of usage Font Avesome: http://fontawesome.io/icons/
		 *
		 * $this->add( array(
		 *                'handle' => 'font-awesome',
		 *                'src' => WPBC_PLUGIN_URL . 'assets/libs/font-awesome-4.3.0/css/font-awesome.css' ,
		 *                'deps' => false,
		 *                'version' => '4.3.0',
		 *                'where_to_load' => array( 'admin' ),
		 *                'condition' => false
		 *      ) );
		 *
		 * // Exmaples of usage Font Avesome 3.2.1 (benefits of this version - support IE7): http://fontawesome.io/3.2.1/examples/
		 * $this->add( array(
		 *                'handle' => 'font-awesome',
		 *                'src' => WPBC_PLUGIN_URL . '/assets/libs/font-awesome/css/font-awesome.css' ,
		 *                'deps' => false,
		 *                'version' => '3.2.1',
		 *                'where_to_load' => array( 'admin' ),
		 *                'condition' => false
		 *      ) );
		 * $this->add( array(
		 *                'handle' => 'font-awesome-ie7',
		 *                'src' => WPBC_PLUGIN_URL . '/assets/libs/font-awesome/css/font-awesome-ie7.css' ,
		 *                'deps' => array('font-awesome'),
		 *                'version' => '3.2.1',
		 *                'where_to_load' => array( 'admin' ),
		 *                'condition' => 'IE 7'                               // CSS condition. Exmaple: <!--[if IE 7]>
		 *      ) );
		 */
	}


    public function enqueue( $where_to_load ) {

		wp_enqueue_style( 'wpdevelop-bts',              wpbc_plugin_url( '/vendors/_custom/bootstrap-css/css/bootstrap.css' ), array(), WP_BK_VERSION_NUM );                                // FixIn: 9.0.1.1.1.
		wp_enqueue_style( 'wpdevelop-bts-theme',        wpbc_plugin_url( '/vendors/_custom/bootstrap-css/css/bootstrap-theme.css' ), array(), WP_BK_VERSION_NUM );
		wp_enqueue_style( 'wpbc-tippy-popover',         wpbc_plugin_url( '/vendors/_custom/tippy.js/themes/wpbc-tippy-popover.css' ), array(), WP_BK_VERSION_NUM );
		wp_enqueue_style( 'wpbc-tippy-times',           wpbc_plugin_url( '/vendors/_custom/tippy.js/themes/wpbc-tippy-times.css' ), array(), WP_BK_VERSION_NUM );
		wp_enqueue_style( 'wpbc-material-design-icons', wpbc_plugin_url( '/vendors/_custom/material-design-icons/material-design-icons.css' ), array(), WP_BK_VERSION_NUM );   // FixIn: 9.2.1.4.
		wp_enqueue_style( 'wpbc-ui-both',               wpbc_plugin_url( '/css/wpbc_ui_both.css' ), array(), WP_BK_VERSION_NUM );                                                            // FixIn: 10.0.0.25.

		if ( 'admin' === $where_to_load ) {                                                                                                                           // Admin CSS files.
			wp_enqueue_style( 'wpbc-bootstrap-icons',       wpbc_plugin_url( '/vendors/_custom/bootstrap-icons/bootstrap-icons.css' ), array(), WP_BK_VERSION_NUM );  // FixIn: 9.0.1.1.
			wp_enqueue_style( 'wpbc-all-admin',             wpbc_plugin_url( '/_dist/all/_out/wpbc_all_admin.min.css' ), array(), WP_BK_VERSION_NUM );                // Several concataned css files.
            wp_enqueue_style( 'wpbc-admin-support',         wpbc_plugin_url( '/core/any/css/admin-support.css' ),       array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-admin-menu',            wpbc_plugin_url( '/core/any/css/admin-menu.css' ),          array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-admin-toolbar',         wpbc_plugin_url( '/core/any/css/admin-toolbar.css' ),       array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-settings-page',         wpbc_plugin_url( '/core/any/css/settings-page.css' ),       array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-admin-listing-table',   wpbc_plugin_url( '/core/any/css/admin-listing-table.css' ), array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-br-table',              wpbc_plugin_url( '/core/any/css/admin-br-table.css' ),      array(), WP_BK_VERSION_NUM);
			wp_enqueue_style( 'wpbc-flex-table',            wpbc_plugin_url( '/core/any/css/wpbc_flextable.css' ),      array(), WP_BK_VERSION_NUM);                   // Flex: Booking resources Table    // FixIn: 9.9.0.7.
            wp_enqueue_style( 'wpbc-admin-modal-popups',    wpbc_plugin_url( '/css/modal.css' ),                        array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-admin-pages',           wpbc_plugin_url( '/css/admin.css' ),                        array(), WP_BK_VERSION_NUM);
			wp_enqueue_style( 'wpbc-admin-skin',            wpbc_plugin_url( '/css/admin-skin.css' ),                   array( 'wpbc-admin-pages' ), WP_BK_VERSION_NUM );  // FixIn: 8.0.2.4.

			$wpbc_inline_css = '';
			if ( wpbc_mu__is_simulated_login_as_user() ) {
				// Mark last item in menu as a button.
				$wpbc_inline_css .= ' #adminmenu #toplevel_page_wpbc .wp-submenu li:nth-last-child(1) a { font-weight: 600;background-color: #075c00;color: #fff;margin: 10px 10px 0;display: block;text-align: center;border-radius: 3px;transition: all .3s;box-shadow: none; } ';
				$wpbc_inline_css .= ' #adminmenu #toplevel_page_wpbc .wp-submenu li:nth-last-child(1) { margin-top: 10px;border-top: 1px solid #535454; } ';
			} else {
				// Horizontal Lines in Menu. // FixIn: 10.1.3.1.
				$wpbc_inline_css .= ' #adminmenu #toplevel_page_wpbc .wp-submenu li:nth-child(4) a { border-top: 1px solid #535454;padding-top: 10px;margin-top: 6px; } ';
				$wpbc_inline_css .= ' #adminmenu #toplevel_page_wpbc .wp-submenu li:nth-last-child(1) a { border-top: 1px solid #535454;padding-top: 10px;margin-top: 6px; } ';
			}
			wp_add_inline_style( 'wpbc-admin-pages', $wpbc_inline_css );
		}

		$server_request_uri = ( ( isset( $_SERVER['REQUEST_URI'] ) ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash */ /* FixIn: sanitize_unslash */
		if (
			( 'admin' !== $where_to_load ) ||
			( wpbc_is_new_booking_page() ) ||
			( wpbc_is_setup_wizard_page() ) ||
			( wpbc_is_settings_form_page() ) ||
			( wpbc_is_settings_color_themes_page() ) ||
			( ( wpbc_is_settings_page() ) && ( strpos( $server_request_uri, 'tab=payment' ) !== false ) )
		) {
			wpbc_enqueue_styles__front_end();
		}

		wpbc_enqueue_styles__calendar();

		do_action( 'wpbc_enqueue_css_files', $where_to_load );
	}


	public function remove_conflicts( $where_to_load ) {

		// FixIn: 8.1.3.12.
		if (
			wpbc_is_bookings_page() ||
			wpbc_is_new_booking_page() ||
			wpbc_is_setup_wizard_page() ||
			wpbc_is_settings_form_page() ||
			wpbc_is_settings_color_themes_page() ||
			wpbc_is_resources_page() ||
			wpbc_is_settings_page()
		) {
			if ( function_exists( 'wp_dequeue_style' ) ) {
				/**
				// wp_dequeue_style( 'cs-alert' );
				// wp_dequeue_style( 'cs-framework' );
				// wp_dequeue_style( 'cs-font-awesome' );
				// wp_dequeue_style( 'icomoon' );
				*/
				wp_dequeue_style( 'chosen' );
				wp_dequeue_style( 'toolset-font-awesome-css' );                          // Remove this script sitepress-multilingual-cms/res/css/font-awesome.min.css?ver=3.1.6, which is load by the "sitepress-multilingual-cms".
				wp_dequeue_style( 'toolset-font-awesome' );                              // FixIn: 5.4.5.8.
				wp_dequeue_style( 'the7-fontello-css' );
				wp_dequeue_style( 'dt-awsome-fonts-back-css' );                          // FixIn: 8.2.1.10             fix conflict  with https://the7.io/.
				wp_dequeue_style( 'dt-awsome-fonts-css' );
				wp_dequeue_style( 'dt-fontello-css' );
				wp_dequeue_style( 'cs_icons_data_css_default' );                         // FixIn: 8.1.3.12.
				wp_dequeue_style( 'icons-style' );                                       // FixIn: 8.2.1.22.
				wp_dequeue_style( 'fontawesome-style' );                                 // FixIn: 8.2.1.22.
				wp_dequeue_style( 'bootstrap-style' );                                   // FixIn: 8.2.1.22.
				wp_dequeue_style( 'bootstrap-theme-style' );                             // FixIn: 8.2.1.22.
			}
		}
	}
}


/**
 * Integrate CSS files required for front-end side.
 *
 * @return void
 */
function wpbc_enqueue_styles__front_end() {

	// Client or Add New Booking page.
	if ( ( 'On' === get_bk_option( 'booking_timeslot_picker' ) ) || ( wpbc_is_setup_wizard_page() || wpbc_is_builder_booking_form_page() ) ) {                                                // FixIn: 8.7.11.10.
		wp_enqueue_style( 'wpbc-time_picker', wpbc_plugin_url( '/css/wpbc_time-selector.css' ), array(), WP_BK_VERSION_NUM );

		$time_picker_skin_path = wpbc_get_time_picker_skin_url();
		if ( ! empty( $time_picker_skin_path ) ) {
			wp_enqueue_style( 'wpbc-time_picker-skin', $time_picker_skin_path, array( 'wpbc-time_picker' ), WP_BK_VERSION_NUM );
		}
	}
	wp_enqueue_style( 'wpbc-client-pages', wpbc_plugin_url( '/css/client.css' ), array(), WP_BK_VERSION_NUM );
	wp_enqueue_style( 'wpbc-all-client', wpbc_plugin_url( '/_dist/all/_out/wpbc_all_client.css' ), array( 'wpbc-client-pages' ), WP_BK_VERSION_NUM );    // Several concataned css files.

}

function wpbc_enqueue_styles__calendar() {

	wp_enqueue_style( 'wpbc-calendar', wpbc_plugin_url( '/css/calendar.css' ), array(), WP_BK_VERSION_NUM );

	// Calendar Skins.
	$calendar_skin_path = wpbc_get_calendar_skin_url();
	if ( ! empty( $calendar_skin_path ) ) {
		wp_enqueue_style( 'wpbc-calendar-skin', $calendar_skin_path, array(), WP_BK_VERSION_NUM );
	}

}



/**
 * Get URL to  Calendar Skin ( CSS file )
 *
 * @return string - URL to  calendar skin
 */
function wpbc_get_calendar_skin_url() {

	// -- Old 'inc' path -> Re-update  // FixIn: 8.7.11.11.
	$check_skin_path = get_bk_option( 'booking_skin' );
	if ( false !== strpos( $check_skin_path, 'inc/skins/' ) ) {
		$check_skin_path = str_replace( 'inc/skins/', 'css/skins/', $check_skin_path );
		update_bk_option( 'booking_skin', $check_skin_path );
	}
	// -- Absolute Path - Remove                                                                                        // FixIn: 8.9.4.8.
	if ( false !== strpos( $check_skin_path, WPBC_PLUGIN_DIR ) ) {
		$check_skin_path = str_replace( WPBC_PLUGIN_DIR, '', $check_skin_path );
		update_bk_option( 'booking_skin', $check_skin_path );
	}


	$calendar_skin_path = false;
	// -----------------------------------------------------------------------------------------------------------------
	// Check if this skin exist in Usual folder.
	// -----------------------------------------------------------------------------------------------------------------
	if ( file_exists( WPBC_PLUGIN_DIR . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_skin') ) ) ) {
		$calendar_skin_path = WPBC_PLUGIN_URL . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_skin') );
	}

	// -----------------------------------------------------------------------------------------------------------------
	// Check if skin exist in the Custom User folder at  the        http://example.com/wp-content/uploads/wpbc_skins/
	// -----------------------------------------------------------------------------------------------------------------
	$upload_dir              = wp_upload_dir();
	$custom_user_skin_folder = $upload_dir['basedir'];
	$custom_user_skin_url    = $upload_dir['baseurl'];

	if ( file_exists( $custom_user_skin_folder . str_replace(  array( WPBC_PLUGIN_URL , $custom_user_skin_url ), '', get_bk_option( 'booking_skin') ) ) ) {
		$calendar_skin_path = $custom_user_skin_url . str_replace( array(WPBC_PLUGIN_URL, $custom_user_skin_url ), '', get_bk_option( 'booking_skin') );
	}

	// -----------------------------------------------------------------------------------------------------------------
	// If still no file, then Default
	// -----------------------------------------------------------------------------------------------------------------
	if ( empty( $calendar_skin_path ) ) {
		$calendar_skin_path = WPBC_PLUGIN_URL . '/css/skins/25_5__round_1.css';
	}

	return $calendar_skin_path;
}


/**
 * Get URL  of Time Picker Skin - checked by  default of this skin exists.
 *
 * @return false|string
 */
function wpbc_get_time_picker_skin_url() {

	// Time_picker Skin.
	$time_picker_skin_path = false;

	// Just  default value,  if previously  was not saved any options.
	if ( empty( get_bk_option( 'booking_timeslot_picker_skin' ) ) ) {
		update_bk_option( 'booking_timeslot_picker_skin', '/css/time_picker_skins/light__24_8.css' );                   // FixIn: 10.4.0.1.
	}

	// Check if this skin exist in the plugin  folder.
	if ( file_exists( WPBC_PLUGIN_DIR . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_timeslot_picker_skin' ) ) ) ) {
		$time_picker_skin_path = WPBC_PLUGIN_URL . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_timeslot_picker_skin' ) );
	}

	// Check if this skin exist  in the Custom User folder at  the http://example.com/wp-content/uploads/wpbc_skins/ .
	$upload_dir              = wp_upload_dir();
	$custom_user_skin_folder = $upload_dir['basedir'];
	$custom_user_skin_url    = $upload_dir['baseurl'];
	if ( file_exists( $custom_user_skin_folder . str_replace( array( WPBC_PLUGIN_URL, $custom_user_skin_url, ), '', get_bk_option( 'booking_timeslot_picker_skin' ) ) ) ) {
		$time_picker_skin_path = $custom_user_skin_url . str_replace( array( WPBC_PLUGIN_URL, $custom_user_skin_url, ), '', get_bk_option( 'booking_timeslot_picker_skin' ) );
	}

	return $time_picker_skin_path;
}
