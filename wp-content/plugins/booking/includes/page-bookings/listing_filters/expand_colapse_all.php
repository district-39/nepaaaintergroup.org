<?php
/**
 * Class Expand / Colapse all  items in Listing.
 *
 * @package Support functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;                                                                                                               // Exit, if accessed directly.
}

// JavaScript Hook: in    ../includes/page-bookings/_src/boo_listing__actions.js  ->   function wpbc_boo_listing__init_hook__sort_by() { ... } .
// FixIn: 10.11.4.6.

/**
 * Class WPBC_Listing_Filters__Expand_Colapse_All
 *
 *   INFO: Get request parameter in template:    var p_value = wpbc_ajx_booking_listing.search_get_param( 'expand_colapse_all' );
 */
class WPBC_Listing_Filters__Expand_Colapse_All {

	const ID = 'expand_colapse_all';
	/**
	 * By  default we do not save the status of this option into  the DB.
	 */
	const IS_SAVE_STATUS = false;
	/**
	 * Get Action Button
	 *
	 * @return false|string
	 */
	public static function get_button() {

		if ( self::IS_SAVE_STATUS ) {
			// Expand or calpase all  rows on loading of the listing,  depends from saved parameter.
			?>
			<#
				jQuery(document).ready(function(){
					if ( 'wide' === wpbc_ajx_booking_listing.search_get_param('ui_usr__dates_short_wide') ) {
						wpbc_boo_listing__click__expand_all_rows();
					} else {
						wpbc_boo_listing__click__colapse_all_rows();
					}
				});
			#>
			<?php
		}
		?>
		<a class="wpbc_btn_expand_colapse_all"
		   style="
			<?php
			if ( self::IS_SAVE_STATUS ) { ?>
				<# if ( 'short' === wpbc_ajx_booking_listing.search_get_param('ui_usr__dates_short_wide') ) { #>display: none;<# } #>
			<?php
			}
			?>"
		   href="javascript:void(0)"
		   onclick="javascript:wpbc_boo_listing__click__expand_all_rows();<?php
			if ( self::IS_SAVE_STATUS ) {
				?> wpbc_ajx_booking_send_search_request_with_params( {'ui_usr__dates_short_wide': 'wide'} ); <?php
			}
		   ?>">
			<i class="menu_icon icon-1x wpbc-bi-plus-square-dotted tooltip_top" data-original-title="<?php echo esc_js( __( 'Expand All', 'booking' ) ); ?>"></i>
		</a>
		<a class="wpbc_btn_expand_colapse_all"
		   style="
			<?php
			if ( self::IS_SAVE_STATUS ) { ?>
				<# if ( 'wide' === wpbc_ajx_booking_listing.search_get_param('ui_usr__dates_short_wide') ) { #>display: none;<# } #>
			<?php
			} else {
				echo 'display: none;';
			}
			?>"
			href="javascript:void(0)"
			onclick="javascript:wpbc_boo_listing__click__colapse_all_rows();<?php
			if ( self::IS_SAVE_STATUS ) {
				?> wpbc_ajx_booking_send_search_request_with_params( {'ui_usr__dates_short_wide': 'short'} ); <?php
			}
			?>">
			<i class="menu_icon icon-1x wpbc-bi-dash-square-dotted tooltip_top" data-original-title="<?php echo esc_js( __( 'Colapse All', 'booking' ) ); ?>"></i>
		</a>
		<?php
	}
}
