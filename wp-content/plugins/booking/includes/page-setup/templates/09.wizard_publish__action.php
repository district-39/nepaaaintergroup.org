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
// == Action - Publish Form ==
// -------------------------------------------------------------------------------------------------------------
/**
 * Template - Publish Form
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
function wpbc_template__wizard_publish__action_validate_data( $post_data ){

	$escaped_data = array(
	//	'booking_form_theme'           => get_bk_option( 'booking_form_theme' )            // ''
	);
	// -----------------------------------------------------------------------------------------------------------------
	//	$key = 'booking_form_theme';
	//	if ( ( isset( $post_data[ $key ] ) ) ) {
	//		$escaped_data[ $key ] = wpbc_clean_text_value( $post_data[ $key ] );
	//	}

	return $escaped_data;
}



/**
 *  Update
 *
 * @param $cleaned_data     array
 *
 * @return void
 */
function wpbc_setup__update__wizard_publish( $cleaned_data ){

//	if ( isset( $cleaned_data['booking_form_theme'] ) ) {
//		update_bk_option( 'booking_form_theme', $cleaned_data['booking_form_theme'] );
//	}

}
