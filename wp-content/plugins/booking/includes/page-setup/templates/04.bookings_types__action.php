<?php /**
 * @version 1.0
 * @description Action  for  Template Setup pages
 * @category    Setup Action
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-30
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Action - Bookings Types ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - General Info - Step 01
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


function wpbc_template__bookings_types__action_validate_data( $post_data ){

	$escaped_data = array(
		'wpbc_swp_booking_types'            => '',          // Can be: 'full_days_bookings' | 'time_slots_appointments' | 'changeover_multi_dates_bookings'
		'wpbc_swp_booking_timeslot_picker'  => get_bk_option('booking_timeslot_picker'),
		'wpbc_swp_booking_appointments_type'  => 'rangetime',
		'wpbc_swp_booking_change_over_days_triangles'  => get_bk_option('booking_change_over_days_triangles')
	);

	$key = 'wpbc_swp_booking_types';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_booking_timeslot_picker';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_booking_appointments_type';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
			$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'wpbc_swp_booking_change_over_days_triangles';
	if ( ( isset( $post_data[ $key ] ) ) && ( ! empty( ( $post_data[ $key ] ) ) ) ) {
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
function wpbc_setup__update__bookings_types( $cleaned_data ){

	if ( ! empty( $cleaned_data['wpbc_swp_booking_types'] ) ) {

		// Show calendar legend by  default
		update_bk_option( 'booking_is_show_legend', 'On' );

		switch ( $cleaned_data['wpbc_swp_booking_types'] ) {

		    case 'full_days_bookings':

			    if ( class_exists( 'wpdev_bk_personal' ) ) {
				    update_bk_option( 'booking_form',       str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_form__template( 'hints-dev' ) ) );
				    update_bk_option( 'booking_form_show',  str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_data__template( 'hints-dev' ) ) );

					//    update_bk_option( 'booking_form',       str_replace( '\\n\\', '', wpbc_get__booking_form__template( '2_columns' ) ) );
					//    update_bk_option( 'booking_form_show',  str_replace( '\\n\\', '', wpbc_get__booking_data__template( '2_columns' ) ) );
			    } else {
				    // Free

				    // Structure
				    $booking_form_structure = 'form_right';                                                             // vertical  form_right  form_center
				    // $booking_form_structure = 'wizard_services_a'; // FixIn: 10.11.4.3.
				    update_bk_option( 'booking_form_structure_type' , $booking_form_structure );
				    // update_bk_option( 'booking_form_layout_max_cols' , 1 );

					// FixIn: 10.11.4.3.
				    update_bk_option( 'booking_form_layout_width', '440' );
				    update_bk_option( 'booking_form_layout_width_px_pr', 'px' );
				    update_bk_option( 'booking_form_layout_max_cols', 1 );


					// Default Form
				    $visual_form_structure = wpbc_simple_form__visual__get_default_form__without_times();

					update_bk_option( 'booking_form_visual', $visual_form_structure );
				    update_bk_option( 'booking_form',       wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) );
				    update_bk_option( 'booking_form_show',  wpbc_simple_form__get_form_show__as_shortcodes( $visual_form_structure ) );
			    }

				if ( class_exists('wpdev_bk_biz_s')) {
				    update_bk_option( 'booking_type_of_day_selections' ,    'range' );
			        update_bk_option( 'booking_range_selection_type',       'dynamic');
			        update_bk_option( 'booking_range_selection_days_count', '1');
			        update_bk_option( 'booking_range_selection_days_max_count_dynamic',30);
			        update_bk_option( 'booking_range_selection_days_specific_num_dynamic','');
			        update_bk_option( 'booking_range_start_day' ,           '-1' );
			        update_bk_option( 'booking_range_selection_days_count_dynamic','1');
			        update_bk_option( 'booking_range_start_day_dynamic' ,   '-1' );
				} else {
					update_bk_option( 'booking_type_of_day_selections',     'multiple' );
				}
		        update_bk_option( 'booking_range_selection_time_is_active', 'Off');              // Changeover
				update_bk_option( 'booking_recurrent_time' , 'On');                             // Use selected times for each booking date

		        update_bk_option( 'booking_legend_is_show_item_partially', 'Off');              // Legend Item
				update_bk_option( 'booking_skin', '/css/skins/25_5__square_1.css' ); // FixIn: 10.11.4.2.
				update_bk_option( 'booking_form_theme', '' );
		        break;

		    case 'time_slots_appointments':

			    update_bk_option( 'booking_timeslot_picker', ( 'On' === $cleaned_data['wpbc_swp_booking_timeslot_picker'] ) ? 'On' : 'Off' );

			    if ( class_exists( 'wpdev_bk_personal' ) ) {

					// FixIn: 10.7.1.4.
					if ( 'rangetime' === $cleaned_data['wpbc_swp_booking_appointments_type'] ){
						update_bk_option( 'booking_form',       str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_form__template( 'appointments30' ) ) );
						update_bk_option( 'booking_form_show',  str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_data__template( 'appointments30' ) ) );
					} else{
						 // FixIn: 10.12.4.8.
						update_bk_option( 'booking_form',       str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_form__template( 'appointments_service_c' ) ) );
						update_bk_option( 'booking_form_show',  str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_data__template( 'appointments_service_c' ) ) );
					}

			    } else {
				    // Free.

				    // Structure.
					$booking_form_structure = ( 'rangetime' === $cleaned_data['wpbc_swp_booking_appointments_type'] ) ? 'wizard_2columns' : 'wizard_services_a';  // vertical  form_right  form_center.
					$booking_form_structure = 'wizard_services_a'; // FixIn: 10.11.4.3.

				    update_bk_option( 'booking_form_structure_type', $booking_form_structure );
				    update_bk_option( 'booking_form_layout_width', '100' );
				    update_bk_option( 'booking_form_layout_width_px_pr', '%' );
				    update_bk_option( 'booking_form_layout_max_cols', 2 );

					// Default Form
					if ( 'rangetime' !== $cleaned_data['wpbc_swp_booking_appointments_type'] ) {
						$visual_form_structure = wpbc_simple_form__visual__get_default_form__service_duration_a();
					} else {
						$visual_form_structure = wpbc_simple_form__visual__get_default_form__times_30min();
					}

					update_bk_option( 'booking_form_visual', $visual_form_structure );
				    update_bk_option( 'booking_form',       wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) );
				    update_bk_option( 'booking_form_show',  wpbc_simple_form__get_form_show__as_shortcodes( $visual_form_structure ) );
			    }

				update_bk_option( 'booking_type_of_day_selections' , 'single' );
				update_bk_option( 'booking_range_selection_time_is_active', 'Off');              // Changeover
				update_bk_option( 'booking_recurrent_time' , 'Off');                              // Use selected times for each booking date

				update_bk_option( 'booking_legend_text_for_item_partially', __( 'Partially booked', 'booking' ) );
				update_bk_option( 'booking_legend_is_show_item_partially', 'On');              // Legend Item

				update_bk_option( 'booking_skin', '/css/skins/25_5__round_1.css' ); // FixIn: 10.11.4.2.
				update_bk_option( 'booking_form_theme', '' );
				// booking_timeslot_picker_skin

		        break;

		    case 'changeover_multi_dates_bookings':

				update_bk_option( 'booking_change_over_days_triangles', ( 'On' === $cleaned_data['wpbc_swp_booking_change_over_days_triangles'] ) ? 'On' : 'Off' );

			    if ( class_exists( 'wpdev_bk_personal' ) ) {
				    update_bk_option( 'booking_form',       str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_form__template( 'wizard' ) ) );
				    update_bk_option( 'booking_form_show',  str_replace( array('\\n\\','\\n'), "\n", wpbc_get__predefined_booking_data__template( 'wizard' ) ) );
			    } else {
				    // Free

				    // Structure
				    $booking_form_structure = 'form_center';                                                            // vertical  form_right  form_center
				    update_bk_option( 'booking_form_structure_type' , $booking_form_structure );
				    update_bk_option( 'booking_form_layout_max_cols' , 1 );

					// Default Form
				    $visual_form_structure = wpbc_simple_form__visual__get_default_form__without_times();

					update_bk_option( 'booking_form_visual', $visual_form_structure );
				    update_bk_option( 'booking_form',       wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) );
				    update_bk_option( 'booking_form_show',  wpbc_simple_form__get_form_show__as_shortcodes( $visual_form_structure ) );
			    }


			    update_bk_option( 'booking_type_of_day_selections' , 'range' );
		        update_bk_option( 'booking_range_selection_type', 'dynamic');
		        update_bk_option( 'booking_range_selection_days_count','2');
		        update_bk_option( 'booking_range_selection_days_max_count_dynamic',30);
		        update_bk_option( 'booking_range_selection_days_specific_num_dynamic','');
		        update_bk_option( 'booking_range_start_day' , '-1' );
		        update_bk_option( 'booking_range_selection_days_count_dynamic','2');
		        update_bk_option( 'booking_range_start_day_dynamic' , '-1' );

		        update_bk_option( 'booking_range_selection_time_is_active', 'On');              // Changeover
		        update_bk_option( 'booking_range_selection_start_time',     '14:00');
		        update_bk_option( 'booking_range_selection_end_time',       '12:00');

				update_bk_option( 'booking_recurrent_time' , 'Off');                             // Use selected times for each booking date

				update_bk_option( 'booking_legend_text_for_item_partially', __( 'Changeover', 'booking' ) );
				update_bk_option( 'booking_legend_is_show_item_partially', 'On');              // Legend Item

			    update_bk_option( 'booking_skin', '/css/skins/25_5__square_1.css' );
				update_bk_option( 'booking_form_theme', '' );

		        break;
		    default:
		       // Default
		}

        // Update Email Data
        //update_bk_option( $email_option_name, $email_data );
	}
}