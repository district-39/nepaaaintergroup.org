<?php /**
 * @version 1.0
 * @description Action  for  Template Setup pages
 * @category    Setup Action
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-10-12
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Date | Time Formats,  Etc... ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - Date | Time Formats ... - Step 02
 *
 * 	Help Tips:
 *
 *		<script type="text/html" id="tmpl-template_name_a">
 * 			Escaped:  	 {{data.test_key}}
 * 			HTML:  		{{{data.test_key}}}
 * 			JS: 	  	<# if (true) { alert( 1 ); } #>
 * 		</script>
 *
 * 		var template__var = wp.template( 'template_name_a' );
 *
 * 		jQuery( '.content' ).html( template__var( { 'test_key' => '<strong>Data</strong>' } ) );
 *
 * @return void
 */


function wpbc_template__date_time_formats__action_validate_data( $post_data ){

	$escaped_data = array(
		'wpbc_swp_date_format'     => get_option( 'booking_date_format' ),
		'wpbc_swp_time_format'     => get_option( 'date_format' ),
		'wpbc_swp_start_day_weeek' => 0,
		'wpbc_swp_date_view_type'  => 'short'
	);

	$key = 'wpbc_swp_date_format';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_time_format';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_start_day_weeek';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = intval( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_date_view_type';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}

	return $escaped_data;
}



/**
 *  Update data
 *
 * @param $cleaned_data     array()
 *
 * @return void
 */
function wpbc_setup__update__date_time_formats( $cleaned_data ){

	if ( ! empty( $cleaned_data['wpbc_swp_date_format'] ) ) {
		update_bk_option( 'booking_date_format', $cleaned_data['wpbc_swp_date_format'] );
	}
	if ( ! empty( $cleaned_data['wpbc_swp_time_format'] ) ) {
		update_bk_option( 'booking_time_format', $cleaned_data['wpbc_swp_time_format'] );
	}
	if ( ! empty( $cleaned_data['wpbc_swp_start_day_weeek'] ) ) {
		update_bk_option( 'booking_start_day_weeek', $cleaned_data['wpbc_swp_start_day_weeek'] );
	}
	if ( ! empty( $cleaned_data['wpbc_swp_date_view_type'] ) ) {
		update_bk_option( 'booking_date_view_type', $cleaned_data['wpbc_swp_date_view_type'] );
	}
}