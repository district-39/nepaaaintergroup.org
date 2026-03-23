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

function wpbc_template__form_structure__action_validate_data( $post_data ){

	$escaped_data = array(
		'wpbc_swp_booking_form_template_pro' => ''
	);

	$key = 'wpbc_swp_booking_form_template_pro';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			if ( is_array( $post_data[ $key ] ) ) {
				$post_data[ $key ] = $post_data[ $key ][0];
			}
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	return $escaped_data;
}



/**
 *  Update "General Data" like "Email" and "Title"
 *
 * @param $cleaned_data     array(
 *		'wpbc_swp_business_name'     => '',
 *		'wpbc_swp_booking_who_setup' => '',
 *		'wpbc_swp_industry'          => '',
 *		'wpbc_swp_email'             => '',
 *		'wpbc_swp_accept_send'       => 'Off'
 *
 * )
 *
 * @return void
 */
function wpbc_setup__update__form_structure( $cleaned_data ){

	/**
	if ( ! empty( $cleaned_data['wpbc_swp_booking_form_template'] ) ) {

		//update_bk_option( 'booking_range_start_day_dynamic' , '-1' );

	    // Structure
	    $booking_form_structure = $cleaned_data['wpbc_swp_booking_form_template'];       // vertical  form_right  form_center
	    update_bk_option( 'booking_form_structure_type' , $booking_form_structure );

		// Default Form
	    //$visual_form_structure = wpbc_simple_form__visual__get_default_form__without_times();
		//update_bk_option( 'booking_form_visual', $visual_form_structure );

		$visual_form_structure = get_bk_option( 'booking_form_visual'  );
	    update_bk_option( 'booking_form',       wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) );
	    update_bk_option( 'booking_form_show',  wpbc_simple_form__get_form_show__as_shortcodes( $visual_form_structure ) );

	}
	*/

	if ( ! empty( $cleaned_data['wpbc_swp_booking_form_template_pro'] ) ) {

		$booking_wizard_data = get_bk_option( 'booking_wizard_data' );

		$wpbc_swp_booking_types = '';
		if (
			( ! empty( $booking_wizard_data ) ) &&
			( ! empty( $booking_wizard_data['save_and_continue__bookings_types'] ) ) &&
			( ! empty( $booking_wizard_data['save_and_continue__bookings_types']['wpbc_swp_booking_types'] ) )
		) {
			$wpbc_swp_booking_types = $booking_wizard_data['save_and_continue__bookings_types']['wpbc_swp_booking_types'];
		}

		list( $is_pro, $cleaned_data['wpbc_swp_booking_form_template_pro'] ) = explode( '|', $cleaned_data['wpbc_swp_booking_form_template_pro'] );

		if ( 'free' === $is_pro ) {

			$booking_form_structure = $cleaned_data['wpbc_swp_booking_form_template_pro'];                              // vertical  form_right  form_center.
			update_bk_option( 'booking_form_structure_type', $booking_form_structure );


			$visual_form_structure = ( 'time_slots_appointments' === $wpbc_swp_booking_types )
				? wpbc_simple_form__visual__get_default_form__times_30min()
				: wpbc_simple_form__visual__get_default_form__without_times();

			switch ( $booking_form_structure ) {

				case 'wizard_services_a':
					$visual_form_structure = wpbc_simple_form__visual__get_default_form__service_duration_a();
					update_bk_option( 'booking_form_layout_max_cols', 2 );
					update_bk_option( 'booking_form_layout_width', '100' );
					update_bk_option( 'booking_form_layout_width_px_pr', '%' );
					break;

				case 'wizard_2columns':
					update_bk_option( 'booking_form_layout_max_cols', 2 );
					update_bk_option( 'booking_form_layout_width', '100' );
					update_bk_option( 'booking_form_layout_width_px_pr', '%' );
					break;

				default:
					update_bk_option( 'booking_form_layout_max_cols', ( ( 'vertical' === $booking_form_structure ) ? 2 : 1 ) );
					update_bk_option( 'booking_form_layout_width', ( ( 'vertical' === $booking_form_structure ) ? '740' : '440' ) );
					update_bk_option( 'booking_form_layout_width_px_pr', 'px' );
			}

			update_bk_option( 'booking_form_visual', $visual_form_structure );
			update_bk_option( 'booking_form', wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) );
			update_bk_option( 'booking_form_show', wpbc_simple_form__get_form_show__as_shortcodes( $visual_form_structure ) );

		} else {

			update_bk_option( 'booking_form', str_replace( array( '\\n\\', '\\n', ), "\n", wpbc_get__predefined_booking_form__template( $cleaned_data['wpbc_swp_booking_form_template_pro'] ) ) );
			update_bk_option( 'booking_form_show', str_replace( array( '\\n\\', '\\n', ), "\n", wpbc_get__predefined_booking_data__template( $cleaned_data['wpbc_swp_booking_form_template_pro'] ) ) );

		}
	}
}