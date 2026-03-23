<?php /**
 * @version 1.0
 * @description Action  for  Template Setup pages
 * @category    Setup Action
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-10-20
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// -------------------------------------------------------------------------------------------------------------
// == Action - Color Theme ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - Color Theme
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
function wpbc_template__color_theme__action_validate_data( $post_data ){

	$escaped_data = array(
		'booking_form_theme'           => get_bk_option( 'booking_form_theme' ),            // ''
		'booking_skin'                 => get_bk_option( 'booking_skin' ),                  // '/css/skins/25_5__round_1.css'
		'booking_timeslot_picker_skin' => get_bk_option( 'booking_timeslot_picker_skin' )   // '/css/time_picker_skins/light__24_8.css'
	);
	// -----------------------------------------------------------------------------------------------------------------
	$key = 'booking_form_theme';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'booking_skin';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	}
	$key = 'booking_timeslot_picker_skin';
	if ( ( isset( $post_data[ $key ] ) ) ) {
		$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
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
function wpbc_setup__update__color_theme( $cleaned_data ){

	if ( isset( $cleaned_data['booking_form_theme'] ) ) {
		update_bk_option( 'booking_form_theme', $cleaned_data['booking_form_theme'] );
	}
	if ( isset( $cleaned_data['booking_skin'] ) ) {

	    $upload_dir              = wp_upload_dir();
	    $custom_user_skin_folder = $upload_dir['basedir'];
	    $custom_user_skin_url    = $upload_dir['baseurl'];

	    $selected_calendar_skin = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL, $custom_user_skin_folder, $custom_user_skin_url ), '', $cleaned_data['booking_skin'] );

	    // Check if this skin exist in the plugin  folder
	    if (
				( file_exists( 			WPBC_PLUGIN_DIR . $selected_calendar_skin ) )
			 || ( file_exists( $custom_user_skin_folder . $selected_calendar_skin ) )
		){
		    update_bk_option( 'booking_skin', $selected_calendar_skin );
	    }

	}
	if ( isset( $cleaned_data['booking_timeslot_picker_skin'] ) ) {

	    $selected_calendar_skin = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL ), '', $cleaned_data['booking_timeslot_picker_skin'] );

		if ( file_exists( WPBC_PLUGIN_DIR . $selected_calendar_skin ) ) {                                               // Check if this skin exist in the plugin  folder
			update_bk_option( 'booking_timeslot_picker_skin', $selected_calendar_skin );
		}
	}

}