<?php
/**
 * @version 1.0
 * @package     Auto Load Time Slots -- Editing or Adding form
 * @category    WP Booking Calendar > Settings > Booking Form page
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-09-24
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/**
 * Auto Load Time Slots -- Editing or Adding form
 *
 * @param $page string
 */
function wpbc_hook_settings_page_footer__auto_show_timeslots( $page ){

	if ( 'form_field_free_settings'  === $page ) {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		if ( ( isset( $_GET['field_type'] ) ) && ( 'timeslots' === $_GET['field_type'] ) ) {
			?>
			<script type="text/javascript">
				 jQuery(document).ready(function() {
					 if ( jQuery( '#select_form_help_shortcode option[value="rangetime"]' ).length ) {
						jQuery( '#select_form_help_shortcode' ).val( 'rangetime' ).trigger( 'change' );
					 }
					 if ( jQuery( '#select_form_help_shortcode option[value="edit_rangetime"]' ).length ) {
						jQuery( '#select_form_help_shortcode' ).val( 'edit_rangetime' ).trigger( 'change' );
					 }
				 });
			</script>
			<?php
		}
	}

}
add_action('wpbc_hook_settings_page_footer', 'wpbc_hook_settings_page_footer__auto_show_timeslots');

