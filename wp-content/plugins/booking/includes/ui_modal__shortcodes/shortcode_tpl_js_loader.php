<?php
/**
 * @version 1.0
 * @package     Template Loader for Booking Calendar shortcodes config in Popup
 * @subpackage  Template Loader
 * @category    Templates
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-02-03
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly			// FixIn: 9.9.0.15.


// =====================================================================================================================
//  Load JS files at  specific pages only
// =====================================================================================================================

/**
 * Load JS files.
 *
 * @param $hook		'post.php' | 'wp-booking-calendar3_page_wpbc-resources'
 *
 * @return void
 */
function wpbc_register_js__shortcode_config( $hook ) {

	if ( wpbc_can_i_load_on_this_page__shortcode_config() ) {

		wp_enqueue_script( 'wpbc_all', wpbc_plugin_url( '/_dist/all/_out/wpbc_all.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );              // FixIn: 9.8.6.1.
		wp_enqueue_script( 'wpbc_shortcode_popup', wpbc_plugin_url( '/includes/ui_modal__shortcodes/_out/wpbc_shortcode_popup.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) ); // FixIn: 9.8.6.1.
		wp_enqueue_script( 'wpbc-admin-support', wpbc_plugin_url( '/core/any/js/admin-support.js' ), array( 'jquery' ), WP_BK_VERSION_NUM, array( 'in_footer' => WPBC_JS_IN_FOOTER ) );		//Needed for ability to send dismiss //2024-03-13 // FixIn: 9.9.0.42.
		if ( wpbc_can_i_load_on__edit_new_post_page() ) {
			do_action( 'wpbc_enqueue_js_files', 'admin' );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'wpbc_register_js__shortcode_config'  );




//TODO: delete these code:
		// =====================================================================================================================
		//  Load wp Templates
		// =====================================================================================================================

		/**
		 * On WordPress Init, define load of  "WP Util,  that  support wp.template"  and define Templates at Footer
		 * @return void
		 */
		function wpbc_templates__init_hook__shortcode_config(){

			if ( wpbc_can_i_load_on_this_page__shortcode_config() ) {

				// Load: WP Util,  that  support wp.template,  based on underscore _.template system
				wp_enqueue_script( 'wp-util' );

				// Load: Templates
				add_action( 'admin_footer', 'wpbc_templates__shortcode_config__write_templates', 10, 2 );
			}
		}
		//add_action( 'init', 'wpbc_templates__init_hook__shortcode_config' );   								// Define init hooks


			/**
			 * Write all needed templates for shortcode config at the footer.
			 */
			function wpbc_templates__shortcode_config__write_templates(){


			}


		// =====================================================================================================================
		//  Info:  E X A M P L E   of   Template Usage
		// =====================================================================================================================
		 // * Template(s), that loaded 		in Footer 		defined in 		wpbc_templates__init_hook__shortcode_config()
		 // *
		 // *	add_action( 'admin_footer', 'wpbc__template__shortcode_config_booking__EXAMPLE', 10, 2 );
		 // *
		 // *
		 // * Help doc for use templates:
		 // * 1.  JavaScript:				<# console.log( 'JS from  template' ); #>
		 // * 2.  Escaped data:			{{ data.shortcode_config_2 }}
		 // * 2.  Not escaped - HTML data:	{{{ data.shortcode_config_2 }}}
		 // *
		if ( 0 ) {
			function wpbc__template__shortcode_config_booking__EXAMPLE(){

				// Templates here
				?><script type="text/html" id="tmpl-wpbc_shortcode_config__page_content"><?php

					echo 'TADA :) Rally  GOOD !:){{{ data.shortcode_config_2 }}}';

				?></script><?php

				// JS to Load template into page DOM  - HTML
				?>
				<script type="text/javascript">
					jQuery( document ).ready( function (){
						// Toolbar ---------------------------------------------------------------------------------------------------------
						var template__wpbc_shortcode_config__page_content = wp.template( 'wpbc_shortcode_config__page_content' );
						jQuery( '#wpbc_shortcode_config__run_template' ).html( template__wpbc_shortcode_config__page_content( {
																					'shortcode_config_1': 1,
																					'shortcode_config_2': 'This is variable from <strong> JS</strong>',
																					'shortcode_config_3': 'no'
														} ) );
					});
				</script>
				<?php
			}
		}