<?php /**
 * @version 1.0
 * @description AJX Bookings Print
 * @category  AJX_Bookings Print Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2022-06-20
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_Print {

	/**
	 * Define HOOKs for loading CSS and  JavaScript files
	 */
	public function init_load_css_js_tpl() {
		// JS & CSS.

		// Load only  at  AJX_Bookings Settings Page.
		if ( 'vm_booking_listing' === wpbc_get_default_saved_view_mode_for_wpbc_page() ) {

			add_action( 'wpbc_enqueue_js_files', array( $this, 'js_load_files' ), 50 );
			add_action( 'wpbc_enqueue_css_files', array( $this, 'enqueue_css_files' ), 50 );

			add_action( 'wpbc_hook_settings_page_footer', array( $this, 'hook__page_footer_tmpl' ) );
		}
	}

	/**
	 * JSS
	 */
	public function js_load_files( $where_to_load ) {

		$in_footer = true;

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_script( 'wpbc-booking_ajx_print', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings_print.js'         /* wpbc_plugin_url( '/_out/js/code_mirror.js' ) */
												   , array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
			/**
			wp_enqueue_script( 'wpbc-booking_ajx_actions'
				, trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings__actions.js'         // wpbc_plugin_url( '/_out/js/code_mirror.js' )
				, array( 'wpbc_all' ), WP_BK_VERSION_NUM, $in_footer );
			/**
			wp_localize_script( 'wpbc_all', 'wpbc_live_request_obj'
								, array(
										'ajx_booking'  => '',
										'reminders' => ''
									)
			);
		    */
		}
	}

	/**
 * CSS
 */
	public function enqueue_css_files( $where_to_load ) {

		if ( ( is_admin() ) && ( in_array( $where_to_load, array( 'admin', 'both' ) ) ) ) {

			wp_enqueue_style( 'wpbc-booking_ajx_print_css', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings_print.css', array(), WP_BK_VERSION_NUM );
			//wp_enqueue_style( 'wpbc-listing_ajx_booking', trailingslashit( plugins_url( '', __FILE__ ) ) . '_out/bookings__listing.css' , array(), WP_BK_VERSION_NUM );

		}
	}

	/**
	 * Templates
	 *
	 * @param $page
	 *
	 * @return void
	 */
	public function hook__page_footer_tmpl( $page ){

		// page=wpbc&tab=vm_booking_listing
		if ( 'wpbc-ajx_booking'  === $page ) {		// it's from >>	do_action( 'wpbc_hook_settings_page_footer', 'wpbc-ajx_booking' );
			$this->wpbc_write_content_for_modal_print();
		}
	}

    /** Print Layout - Modal Window structure */
    private function wpbc_write_content_for_modal_print() {

		?><span class="wpdevelop"><?php

		  ?><div id="wpbc_ajx_print_modal" class="modal wpbc_popup_modal" tabindex="-1" role="dialog">
			  <div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?php esc_html_e('Print bookings' ,'booking'); ?>
						<div style="float:right;">
							<a  href="javascript:void(0);"
								onclick="javascript: wpbc_print_dialog__do_printing();"
								class="button button-primary" ><?php esc_html_e('Print' ,'booking'); ?></a>
							<a  href="javascript:void(0)" class="button" data-dismiss="modal"><?php esc_html_e('Close' ,'booking'); ?></a>
							<?php /* <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> */ ?>

						</div></h4>
					</div>
					<div class="modal-body">
						<div id="wpbc_content_for_js_print">
							<div id="wpbc__print_frame__inner"> * </div>
						</div>
					</div>
					<?php /* ?>
					<div class="modal-footer">
						<button type="button" class="button button-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="button button-primary">Save changes</button>
					</div>
					<?php */ ?>
				</div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<?php

		?></span><?php
    }
}


/**
 * Just for loading CSS and  JavaScript files
 */
if ( true ) {
	$wpbc_ajx_booking_print = new WPBC_Print;
	$wpbc_ajx_booking_print->init_load_css_js_tpl();
}