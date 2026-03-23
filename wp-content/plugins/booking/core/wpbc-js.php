<?php /**
 * @version 1.0
 * @package Booking Calendar 
 * @category JavaScript files and varibales
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 * 
 * @modified 19.10.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

class WPBC_JS extends WPBC_JS_CSS {
    
    public function define() {
        
        $this->setType('js');
        
        /*
        $this->add( array(
                            'handle' => 'wpbc-datepick',
                            'src' => wpbc_plugin_url( '/js/datepick/jquery.datepick.js'), 
                            'deps' => array( 'wpbc_all' ),
                            'version' => '1.1',
                            'where_to_load' => array( 'admin', 'client' ),                //Usage: array( 'admin', 'client' )
                            'condition' => false    
                  ) );        
        */
    }

    /**
	 * Enqueue Files and Varibales.
     *  Useful in case, if we use get_options and current user functions...
     * 
     * @param type $where_to_load
     */
    public function enqueue( $where_to_load ) {

		wpbc_remove_js_conflicts();                                                                                     // FixIn: 9.5.4.2.

        wpbc_js_load_vars(  $where_to_load );
        do_action( 'wpbc_define_js_vars', $where_to_load );                                                             // Define JavaScript variables in all other files

        wpbc_js_load_libs(  $where_to_load );
        wpbc_js_load_files( $where_to_load );

	    if ( wpbc_is_new_booking_page() || wpbc_is_settings_form_page() || wpbc_is_settings_color_themes_page() || wpbc_is_setup_wizard_page() ) {
		    $where_to_load = 'both';
	    }

        // Load JavaScript files in all other versions
        do_action( 'wpbc_enqueue_js_files', $where_to_load );

		/*
		 * Remove `async` and `defer`  ( check more here https://javascript.info/script-async-defer )
		 * for scripts registered or enqueued, that required for correct  working of plugin,  like
		 * jquery and all Booking Calendar scripts
		 * because inside content of the page can  be something like  jQuery( document ).ready( function(){ ...} which  will
		 * generate         Uncaught ReferenceError: jQuery is not defined
		 */
		add_filter( 'script_loader_tag', array( $this, 'filter_script_loader_tag' ), 9000000000 , 3 );
    }


	/**
	 * Remove `async` and `defer`  ( check more here https://javascript.info/script-async-defer )
	 * for scripts registered or enqueued, that required for correct  working of plugin,  like
	 * jquery and all Booking Calendar scripts
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 *
	 * @return string Script HTML string.
	 *
	 */
	public function filter_script_loader_tag( $tag, $handle, $src ) {

		$script_handles_prevent_defer = array(
			  'jquery-core'         // exact value
			, 'jquery-migrate'
			//, 'wpbc-'             //starting from  'wpbc-'  it's not the exact value
			//, 'wpdevelop-'
		);


		// Remove defer and async attribute from  the src.
		if (
			   ( 'jquery-core' === $handle )
			|| ( 'jquery-migrate' === $handle )
			|| ( false !== strpos( $handle, 'wpbc_' ) )                 // Booking Calendar script wpbc_all.js          // FixIn: 10.1.2.3.
			|| ( false !== strpos( $handle, 'wpbc-' ) )                 // Booking Calendar scripts
			|| ( false !== strpos( $handle, 'wpdevelop-' ) )
			|| ( false !== strpos( $handle, 'wpbm-' ) )
		) {

			foreach ( array( 'async', 'defer' ) as $attr ) {

				if ( preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
					$tag = str_replace($attr, '', $tag);
					$tag = str_replace('=""', '', $tag);
					$tag = str_replace("=''", '', $tag);

					/*
					 * Test  here https://regex101.com/
					 *
					 * Expression:   \s+defer(\s*=\s*["']defer["'])?\s?
					 * Test  string: <script type='text/javascript' defer = 'defer'  defer="defer" src='http://beta/wp-content/plugins/booking-manager/js/wpbm_vars.js?ver=1.1' id='wpbm-global-vars-js'></script>
					 *
					 */
					$pattern = ":\s+{$attr}(\s*=\s*[\"']{$attr}[\"'])?\s?:mi";
					$replacement = ' ';
					$tag = preg_replace($pattern, $replacement, $tag);
				}
			}
		}

		return $tag;
	}


    /**
	 * Deregister  some conflict  scripts from  other plugins.
     * 
     * @param type $where_to_load
     */
    public function remove_conflicts( $where_to_load ) {
        
        if ( wpbc_is_bookings_page() ) {
            if (function_exists('wp_dequeue_script')) {
                //wp_dequeue_script( 'jquery.cookie' );
                //wp_dequeue_script( 'jquery-interdependencies' );
                wp_dequeue_script( 'chosen' );
                wp_dequeue_script( 'cs-framework' );
                wp_dequeue_script( 'cgmp-jquery-tools-tooltip' );                               // Remove this script jquery.tools.tooltip.min.js, which is load by the "Comprehensive Google Map Plugin"
	            wp_dequeue_script( 'bootstrap-script' );
            }
        }
    }
}


/** Define JavaScript Varibales */
function wpbc_js_load_vars( $where_to_load ) {
    

}


/** Default JavaScripts Libraries */
function wpbc_js_load_libs( $where_to_load ) {

	wp_enqueue_script( 'jquery' );                                                                                      // jQuery.

	//	$src  = '';
	//	$deps = '';
	//	$ver  = false;
	//	wp_enqueue_script( 'jquery', $src, $deps, $ver, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );

    // Default Admin Libs 
	if ( 'admin' == $where_to_load ) {
		if ( wpbc_is_settings_page() ) {
			wp_enqueue_style( 'thickbox' );                                        // CSS Thickbox
			wp_enqueue_script( 'thickbox' );                                       // JS Thickbox
			wp_enqueue_media();
		}
		wp_enqueue_style(  'wp-color-picker' );                                    // Color Picker
        wp_enqueue_script( 'wp-color-picker' ); 
        wp_enqueue_script( 'jquery-ui-sortable' );                                 // UI Sortable
    }

	// WP Util,  that  support wp.template,  based on underscore _.template system
    wp_enqueue_script( 'wp-util' );                                                                                     //FixIn: TimeFreeGenerator      // FixIn: 9.4.4.11.
}


/** Load JavaScript Files */
function wpbc_js_load_files( $where_to_load ) {

	wp_enqueue_script( 'wpbc_all',          wpbc_plugin_url( '/_dist/all/_out/wpbc_all.js' ),                    array( 'jquery'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );              // FixIn: 9.8.6.1.

    /**
     * Popover functionality.
     * Popover based on Tippy.js ( https://atomiks.github.io/tippyjs/v6/all-props/#allowhtml ), which  is based on  popper.js ( https://popper.js.org/docs/v2/tutorial/ )
     * Install Alt + F12 run
     * npm i @popperjs/core
     * npm i tippy.js
     * Copy ..\node_modules\@popperjs\core\dist\umd     to  ..\wp-content/plugins/booking/vendors/_custom/popper
     * Copy ..\node_modules\tippy.js\{dist && themes}   to  ..\wp-content/plugins/booking/vendors/_custom/tippy.js
     * In popper.js  edit global.Popper  to global.wpbc_Popper
     * In tippy-bundle.umd.js  edit global.Popper  to global.wpbc_Popper   and      global.tippy   to  global.wpbc_tippy
     */
	wp_enqueue_script( 'wpbc-popper', wpbc_plugin_url( '/vendors/_custom/popper/popper.js' ),                   array( 'wpbc_all'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );            // FixIn: 9.0.1.1.
	wp_enqueue_script( 'wpbc-tipcy',  wpbc_plugin_url( '/vendors/_custom/tippy.js/dist/tippy-bundle.umd.js' ),  array( 'wpbc-popper'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );            //FixIn: 9.8.1

	if ( $where_to_load != 'client' ) {
		wp_enqueue_script( 'wpbc-modal', wpbc_plugin_url( '/vendors/_custom/dropdown_modal/_out/dropdown_modal.js' ),       array( 'jquery'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );               //FixIn: 9.8.1
	}

    wp_enqueue_script( 'wpbc-datepick', wpbc_plugin_url( '/js/datepick/jquery.datepick.wpbc.9.0.js'),       array( 'wpbc_all'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );               //FixIn: 9.8.1
    $calendar_localization_url = wpbc_get_calendar_localization_url();                                                  // Localization
	if ( ! empty( $calendar_localization_url ) ) {
		wp_enqueue_script( 'wpbc-datepick-localize', $calendar_localization_url,                            array( 'wpbc-datepick'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );                 //FixIn: 9.8.1
	}

    if (  ( $where_to_load == 'client' ) || ( wpbc_is_new_booking_page()  ) || ( wpbc_is_settings_form_page() || wpbc_is_settings_color_themes_page() || wpbc_is_setup_wizard_page() || wpbc_is_builder_booking_form_page()  )   ) {

	    wp_enqueue_script( 'wpbc-main-client',  wpbc_plugin_url( '/js/client.js' ),                                  array( 'wpbc-datepick'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );             // Client
	    wp_enqueue_script( 'wpbc_capacity',     wpbc_plugin_url( '/includes/_capacity/_out/create_booking.js' ),     array( 'wpbc-main-client'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );          // Add new bookings   // FixIn: 9.8.0.3.
	    wp_enqueue_script( 'wpbc-times',        wpbc_plugin_url( '/js/wpbc_times.js' ),                              array( 'wpbc-main-client'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );          // FixIn: TimeFree 2 //UnComment it for Booking Calendar Free version
	    // if ( ( 'On' === get_bk_option( 'booking_timeslot_picker' ) ) || ( wpbc_is_setup_wizard_page() ) ) {
			wp_enqueue_script( 'wpbc-time-selector',    wpbc_plugin_url( '/js/wpbc_time-selector.js'),               array( 'wpbc-times'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );             // FixIn: 8.7.11.10.
	    // }
		wp_enqueue_script( 'wpbc-imask',    wpbc_plugin_url( '/vendors/imask/dist/imask.js'), array( 'wpbc-main-client'  ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) ); // FixIn: 10.12.4.10.
		$booking_is_use_phone_validation = get_bk_option( 'booking_is_use_phone_validation' );
		if ( 'On' === $booking_is_use_phone_validation ) {
			wp_enqueue_script( 'wpbc-iphone-validator', wpbc_plugin_url( '/js/wpbc_phone_validator.js' ), array( 'wpbc-imask' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );                                // FixIn: 10.12.4.10.
		}
    }

	if ( 'admin' === $where_to_load ) {
		wp_enqueue_script( 'wpbc-js-print', wpbc_plugin_url( '/vendors/_custom/wpbc_js_print/wpbc_js_print.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );           // FixIn: 9.8.1            //FixIn: 9.2.1.6   // FixIn: 9.1.2.13.
		wp_enqueue_script( 'wpbc-admin-main', wpbc_plugin_url( '/js/admin.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );                                      // Admin.
		if ( wpbc_can_i_load_on_this_page__shortcode_config() ) {
			wp_enqueue_script( 'wpbc_shortcode_popup', wpbc_plugin_url( '/includes/ui_modal__shortcodes/_out/wpbc_shortcode_popup.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) ); // FixIn: 9.8.6.1.
		}
		wp_enqueue_script( 'wpbc-admin-support', wpbc_plugin_url( '/core/any/js/admin-support.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
		wp_enqueue_script( 'wpbc-chosen', wpbc_plugin_url( '/vendors/chosen/chosen.jquery.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );           // Chosen Library.

		// FixIn: 10.12.2.3.  // docs: https://www.npmjs.com/package/simplebar#5-caveats .
		wp_enqueue_script( 'wpbc-simplebar', wpbc_plugin_url( '/vendors/simplebar/dist/simplebar.min.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );           // Chosen Library.
		wp_enqueue_style( 'wpbc-simplebar', wpbc_plugin_url(  '/vendors/simplebar/dist/simplebar.min.css' ), array(), WP_BK_VERSION_NUM );
	}
	if ( in_array( $where_to_load, array( 'admin', 'both' ), true ) ) {
		wp_enqueue_script( 'wpbc_all_admin', wpbc_plugin_url( '/_dist/all/_out/wpbc_all_admin.js' ), array( 'wpbc_all' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
	}
}


/**
 * Remove JS libraries that conflict with  Booking Calendar at the pages with booking forms.
 *
 * You can disable loading of all Booking Calendar  JavaScript  files at  the Booking > Settings General page in "Advanced" section.
 * To do so, you can expand the "Show advanced settings of JavaScript loading" option and select the "Load JS and CSS files only on specific pages" checkbox.
 * You can then specify a list of pages in a textarea where you want the Booking Calendar to appear.
 *
 * @return void
 */
function wpbc_remove_js_conflicts() {
	//if ( wp_script_is( 'script-ID', 'registered' ) ) { }
	wp_deregister_script( 'mphb-kbwood-datepick' );
}

////////////////////////////////////////////////////////////////////////////////
//  Support JavaScript functions
////////////////////////////////////////////////////////////////////////////////

/**
	 * Get URL Datepicker Localization JS File
 * 
 * @return string - URL to  calendar skin
 */
function wpbc_get_calendar_localization_url() {
    // Datepicker Localization - translation for calendar.                      Example:    $locale = 'fr_FR';   
    $locale = wpbc_get_maybe_reloaded_booking_locale();
    
    $calendar_localization_url = false;
    
    if ( ! empty( $locale ) ) {

        $locale_lang    = strtolower( substr( $locale, 0, 2 ) );                // 7.0.1.51
        $locale_country = strtolower( substr( $locale, 3 ) );

        if (   ( $locale_lang !== 'en') && ( wpbc_is_file_exist( '/js/datepick/jquery.datepick-' . $locale_lang . '.js' ) )   ) { 
            
                $calendar_localization_url = wpbc_plugin_url( '/js/datepick/jquery.datepick-'. $locale_lang . '.js' );

        } else if (   ( ! in_array( $locale, array( 'en_US', 'en_CA', 'en_GB', 'en_AU' ) )   )                                      // English Exceptions
                   && ( wpbc_is_file_exist( '/js/datepick/jquery.datepick-'. $locale_country . '.js' ) ) 
        ) { 

                $calendar_localization_url = wpbc_plugin_url( '/js/datepick/jquery.datepick-'. $locale_country . '.js' );                
        }          
    } 

    return $calendar_localization_url;
}


/**
	 * Check if we activated loading of JS/CSS only on specific pages and then load or no it
 * 
 * @param boolean $is_load_scripts  - Default: true
 * @return boolean                  - true | false
 */
function wpbc_is_load_css_js_on_client_page( $is_load_scripts ) {
    
    if ( ! is_admin() ) {           // Check  on Client side only
        
        $booking_is_load_js_css_on_specific_pages = get_bk_option( 'booking_is_load_js_css_on_specific_pages'  );
        if ( $booking_is_load_js_css_on_specific_pages == 'On' ) {
            
            $booking_pages_for_load_js_css = get_bk_option( 'booking_pages_for_load_js_css' );

            $booking_pages_for_load_js_css = preg_split('/[\r\n]+/', $booking_pages_for_load_js_css, -1, PREG_SPLIT_NO_EMPTY);

			$server_request_uri = ( ( isset( $_SERVER['REQUEST_URI'] ) ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash */ /* FixIn: sanitize_unslash */
            $request_uri = $server_request_uri;                                 //FixIn:5.4.1
	        // FixIn: 8.4.5.8.
            if (
            	   ( strpos( $request_uri, 'booking_hash=') !== false )
                || ( strpos( $request_uri, 'check_in=') !== false )
            ) {
                $request_uri = wp_parse_url($request_uri);
                if (  ( ! empty($request_uri ) ) && ( isset($request_uri['path'] ) )  ){
                    $request_uri = $request_uri['path'];
                } else {
					$server_request_uri = ( ( isset( $_SERVER['REQUEST_URI'] ) ) ? sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '' );  /* phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash */ /* FixIn: sanitize_unslash */
                    $request_uri = $server_request_uri;
                }
            }

	        if ( ( ! empty( $booking_pages_for_load_js_css ) ) && ( ! in_array( $request_uri, $booking_pages_for_load_js_css ) ) ) {

		        wp_add_inline_script( 'jquery', "console.log( '== WPBC :: Loading of JS/CSS files disabled for this page at WP Booking Calendar > Settings General page in Advanced section  ==' );" );
		        return false;
	        }
        }
    }
    return true;
}
add_filter( 'wpbc_is_load_script_on_this_page', 'wpbc_is_load_css_js_on_client_page' );

/**
 *  Load JS and CSS files for loading modals correctly
 *
 * @return void
 */
function wpbc_load_js__required_for_modals() {

	// JS for opening Modals.
	wp_enqueue_script( 'wpbc-modal', wpbc_plugin_url( '/vendors/_custom/dropdown_modal/_out/dropdown_modal.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );                        // FixIn: 9.8.1.

	// CSS.
	wp_enqueue_style( 'wpdevelop-bts', wpbc_plugin_url( '/vendors/_custom/bootstrap-css/css/bootstrap.css' ), array(), WP_BK_VERSION_NUM );                                                                     // FixIn: 9.8.1.
	wp_enqueue_style( 'wpdevelop-bts-theme', wpbc_plugin_url( '/vendors/_custom/bootstrap-css/css/bootstrap-theme.css' ), array(), WP_BK_VERSION_NUM );                                                         // FixIn: 9.8.1.

	wp_enqueue_style( 'wpbc-admin-support', wpbc_plugin_url( '/core/any/css/admin-support.css' ), array(), WP_BK_VERSION_NUM );
	wp_enqueue_style( 'wpbc-admin-menu', wpbc_plugin_url( '/core/any/css/admin-menu.css' ), array(), WP_BK_VERSION_NUM );
	// wp_enqueue_style( 'wpbc-admin-toolbar', wpbc_plugin_url( '/core/any/css/admin-toolbar.css' ),     array(), WP_BK_VERSION_NUM );  // .
	wp_enqueue_style( 'wpbc-flex-toolbar', wpbc_plugin_url( '/includes/_toolbar_ui/_src/toolbar_ui.css' ), array(), WP_BK_VERSION_NUM );
	wp_enqueue_style( 'wpbc-admin-modal-popups', wpbc_plugin_url( '/css/modal.css' ), array(), WP_BK_VERSION_NUM );
	wp_enqueue_style( 'wpbc-admin-pages', wpbc_plugin_url( '/css/admin.css' ), array(), WP_BK_VERSION_NUM );
	wp_enqueue_style( 'wpbc-admin-skin', wpbc_plugin_url( '/css/admin-skin.css' ), array( 'wpbc-admin-pages' ), WP_BK_VERSION_NUM );                        // FixIn: 8.0.2.4.
}


/**
 *  Load JS and CSS files for opening Media Upload PopUp in Admin Panel
 *
 * @return void
 */
function wpbc_load_js__required_for_media_upload(){

	/**
	 * Internal WordPress depending on:
	 *   'wp-util'                                -> array( 'underscore', 'jquery' )
	 *   'wp-backbone'                            -> array( 'backbone', 'wp-util' )
	 *    wp_enqueue_script( 'media-editor' )     -> 'wp-backbone'
	 */
	if ( ! wpbc_is_this_demo() ) {
		wp_enqueue_media();
		wp_enqueue_script( 'wpbc_ui__media_upload', wpbc_plugin_url( '/includes/_media_upload/_out/wpbc_ui__media_upload.js' ), array(
			'jquery',
			'media-editor'
		 ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );
	} else {
		$script  = "jQuery(document).ready(function() { ";
		$script .= " jQuery( '.wpbc_media_upload_button' ).on( 'click', function( event ) { ";
		$script .= "   alert('Warning! This feature is restricted in the public live demo.' ); ";
		$script .= " }); ";
		$script .= "}); ";
		wp_add_inline_script( 'jquery', $script );
	}
}


/**
 * On jQuery(...).ready function Start
 *
 * @return string
 */
function wpbc_jq_ready_start() {

	return "(function() { var a = setInterval( function() {  if ( ( 'undefined' === typeof _wpbc ) || ( 'undefined' === typeof jQuery ) || ! window.jQuery ) { return; } clearInterval( a ); jQuery( document ).ready( function (){";

	// Help description:
	?><script type="text/javascript">
		(function (){
			var a = setInterval( function (){
				if ( ('undefined' === typeof jQuery) || !window.jQuery ){
					return;
				}
				clearInterval( a );
				// Here can  be executed jQuery functions
				jQuery( document ).ready( function (){
					// Here is my code start
				} );
			}, 500 );
		})();
	</script><?php
}


/**
 * On jQuery(...).ready function end
 * @return string
 */
function wpbc_jq_ready_end() {

	return "} ); }, 500 ); })();";
}
