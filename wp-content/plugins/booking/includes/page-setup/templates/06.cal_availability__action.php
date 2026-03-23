<?php /**
 * @version 1.0
 * @description Action  for  Template Setup pages
 * @category    Setup Action
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-10-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Action - Form Structure ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - Form Structure
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


/**
 * Validate
 *
 * @param $post_data
 *
 * @return array
 */
function wpbc_template__cal_availability__action_validate_data( $post_data ){

	$escaped_data = array(
		'wpbc_swp_cal_availability__weekdays'     => '',// '999',
		'booking_unavailable_days_num_from_today' => get_bk_option( 'booking_unavailable_days_num_from_today' ),
		'booking_available_days_num_from_today'   => intval( get_bk_option( 'booking_available_days_num_from_today' ) ),
		'booking_unavailable_extra_in_out'        => get_bk_option( 'booking_unavailable_extra_in_out' ),
		'booking_unavailable_extra_minutes_in'    => get_bk_option( 'booking_unavailable_extra_minutes_in' ),
		'booking_unavailable_extra_minutes_out'   => get_bk_option( 'booking_unavailable_extra_minutes_out' ),
		'booking_unavailable_extra_days_in'       => get_bk_option( 'booking_unavailable_extra_days_in' ),
		'booking_unavailable_extra_days_out'      => get_bk_option( 'booking_unavailable_extra_days_out' )
	);
	// -----------------------------------------------------------------------------------------------------------------
	$key = 'wpbc_swp_cal_availability__weekdays';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
		$post_data_arr = explode(',',$post_data[ $key ]);
		foreach ( $post_data_arr as $kk => $vl ) {
			$post_data_arr[ $kk ] = intval( $vl );
		}
		$escaped_data[ $key ] = implode( ',', $post_data_arr );
	}
	// -----------------------------------------------------------------------------------------------------------------
	// FixIn: 10.8.1.4.
	$key = 'booking_unavailable_days_num_from_today';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = intval( $post_data[ $key ] );
		if ( 'm' === substr( $post_data[ $key ], - 1 ) ) {
			$escaped_data[ $key ] .= 'm';
		}
	}
	// -----------------------------------------------------------------------------------------------------------------
	$key = 'booking_available_days_num_from_today';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = intval( $post_data[ $key ] );
		$escaped_data[ $key ] = ( empty( $escaped_data[ $key ] ) ) ? '' : $escaped_data[ $key ];
	}
	// -----------------------------------------------------------------------------------------------------------------
	$key = 'booking_unavailable_extra_in_out';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		if ( in_array( $post_data[ $key ], array( '', 'm', 'd' ) ) ) {
			$escaped_data[ $key ] = $post_data[ $key ];
		} else {
			$escaped_data[ $key ] = '';
		}
	}
	// -----------------------------------------------------------------------------------------------------------------
	$key = 'booking_unavailable_extra_minutes_in';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = sanitize_text_field( $post_data[ $key ] );
	}
	$key = 'booking_unavailable_extra_minutes_out';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = sanitize_text_field( $post_data[ $key ] );
	}
	$key = 'booking_unavailable_extra_days_in';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = sanitize_text_field( $post_data[ $key ] );
	}
	$key = 'booking_unavailable_extra_days_out';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = sanitize_text_field( $post_data[ $key ] );
	}

	return $escaped_data;
}



/**
 *  Update
 *
 * @param $cleaned_data     array
 *
 * @return void
 */
function wpbc_setup__update__cal_availability( $cleaned_data ){

	if ( ! empty( $cleaned_data['wpbc_swp_cal_availability__weekdays'] ) ) {

		$weekdays_arr = explode( ',', $cleaned_data['wpbc_swp_cal_availability__weekdays'] );

		for ( $i = 0; $i < 7; $i ++ ) {
			update_bk_option( 'booking_unavailable_day' . $i, ( in_array( $i, $weekdays_arr ) ) ? 'On' : 'Off' );
		}
	}

	if ( isset( $cleaned_data['booking_unavailable_days_num_from_today'] ) ) {
		update_bk_option( 'booking_unavailable_days_num_from_today', $cleaned_data['booking_unavailable_days_num_from_today'] );
	}

	if ( isset( $cleaned_data['booking_available_days_num_from_today'] ) ) {
		update_bk_option( 'booking_available_days_num_from_today', $cleaned_data['booking_available_days_num_from_today'] );
	}

	// -----------------------------------------------------------------------------------------------------------------
	if ( isset( $cleaned_data['booking_unavailable_extra_in_out'] ) ) {
		update_bk_option( 'booking_unavailable_extra_in_out', $cleaned_data['booking_unavailable_extra_in_out'] );
	}
	// -----------------------------------------------------------------------------------------------------------------
	if ( isset( $cleaned_data['booking_unavailable_extra_minutes_in'] ) ) {
		update_bk_option( 'booking_unavailable_extra_minutes_in', $cleaned_data['booking_unavailable_extra_minutes_in'] );
	}
	if ( isset( $cleaned_data['booking_unavailable_extra_minutes_out'] ) ) {
		update_bk_option( 'booking_unavailable_extra_minutes_out', $cleaned_data['booking_unavailable_extra_minutes_out'] );
	}
	if ( isset( $cleaned_data['booking_unavailable_extra_days_in'] ) ) {
		update_bk_option( 'booking_unavailable_extra_days_in', $cleaned_data['booking_unavailable_extra_days_in'] );
	}
	if ( isset( $cleaned_data['booking_unavailable_extra_days_out'] ) ) {
		update_bk_option( 'booking_unavailable_extra_days_out', $cleaned_data['booking_unavailable_extra_days_out'] );
	}

}