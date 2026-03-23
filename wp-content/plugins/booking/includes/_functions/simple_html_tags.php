<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category  Simple HTML Tags for Booking Form
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-08-18
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly



/**
 * HTML shortcode description:
 *
 *  R E P L A C I N G:
 *
 *  <r>     ->      <div class="wpbc__row">
 *  </r>    ->      </div>
 *
 *  <c>     ->      <div class="wpbc__field">
 *  </c>    ->      </div>
 *
 *  <f>     ->      <span class="fieldvalue">
 *  </f>    ->      </span>
 *
 *  <l>     ->      <label>
 *  </l>    ->      </label>
 */

/**
 * Replace Custom HTML shortcodes,  such  as   <r> -> <div class="wpbc__row"> | <c> -> <div class="wpbc__field">     in Booking Form configuration
 *
 * @param $html_original_content
 *
 * @return array|mixed|string|string[]
 */
function wpbc_bf__replace_custom_html_shortcodes( $html_original_content ){

	$full_text_content = $html_original_content;

	$replacement_shortcode_arr = array(
										array( 'shortcode'  => 'r',    'replace_to' => array( 'tag' => 'div',   'attr' => array( 'class' => "wpbc__row" ) )    ),          // Row.
										array( 'shortcode'  => 'c',    'replace_to' => array( 'tag' => 'div',   'attr' => array( 'class' => "wpbc__field" ) )    ),        // Column.
										array( 'shortcode'  => 'item', 'replace_to' => array( 'tag' => 'div',   'attr' => array( 'class' => "bfb_element" ) )    ),        // Field element for BFB.

										array( 'shortcode'  => 'f',    'replace_to' => array( 'tag' => 'span',  'attr' => array( 'class' => "fieldvalue" ) )    ),         // Field  for "Content of booking fields data" form.
										array( 'shortcode'  => 'l',    'replace_to' => array( 'tag' => 'label', 'attr' => array() )    )                                   // Label.

//										array( 'r', '<div class="wpbc__row">'   , '</div>' ),            // Row
//										array( 'c', '<div class="wpbc__field">' , '</div>' ),            // Column
//										array( 'f', '<span class="fieldvalue">' , '</span>' ),           // Field  for "Content of booking fields data" form
//										array( 'l', '<label>' , '</label>' )                             // Label
								);

	foreach ( $replacement_shortcode_arr as $shortcode_pair_arr ) {
		// Open tags
		if ( ! is_null( $full_text_content ) ) {
			$rx_shortcode = $shortcode_pair_arr['shortcode'];                                   // 'r';

			$replacement           = '<' . $shortcode_pair_arr['replace_to']['tag'] . '>' ;                             // 'div class="wpbc__row"';
			//$regex                 = '%<\s*' . $rx_shortcode . '\s*>%';                                               // ? $regex = '%<\s*(' . $rx_shortcode . ')\s*|\s+[^>]*>%';
			//$full_text_content = preg_replace( $regex, $replacement, $full_text_content );

			$found_shortcodes_count = preg_match_all( '%<\s*' . $rx_shortcode . '(\s+[^\<\>]+)*' . '\s*>%', $full_text_content, $matches_shortoces, PREG_SET_ORDER );   // FixIn: 10.1.0.1.
			foreach ( $matches_shortoces as $index => $found_shortcode ) {
				$found_shortcode_to_replace = $found_shortcode[0];                                                      // <r id="first_row" class="sr_booking_info" style="background:red;">
				$found_shortcode_attributes_to_replace = ( isset( $found_shortcode[1] ) ) ? $found_shortcode[1] : '';   //  id="first_row" class="sr_booking_info" style="background:red;"
				$found_shortcode_attributes_to_replace = trim( $found_shortcode_attributes_to_replace );
				$found_shortcode_attributes_to_replace = str_replace( '  ', ' ', $found_shortcode_attributes_to_replace );

				$attributes_count = preg_match_all( '%([^=]+)=\s*[\'\"]{1}([^\'"]*)[\'\"]{1}\s*%', $found_shortcode_attributes_to_replace, $attributes_arr, PREG_SET_ORDER );
				/**
				 * $attributes_arr = [
				 *                     0 = [
				 *                              0 = "style="margin-right: auto;" "
				 *                              1 = "style"
				 *                              2 = "margin-right: auto;"
				 *                         ]
				 *                     1 = ]
				 *                              0 = "class='wpbc_data and_cl'"
				 *                              1 = "class"
				 *                              2 = "wpbc_data and_cl"
				 *                         ]
				 *                  ]
				 */
				$attributes_named_arr = array();
				foreach ( $attributes_arr as $attribute ) {
					if ( ( ! empty( $attribute ) ) && ( count( $attribute ) > 2 ) ) {
						$a_name  = $attribute[1];
						$a_value = $attribute[2];;
						$attributes_named_arr[ $a_name ] = $a_value;
					}
				}

				foreach ( $shortcode_pair_arr['replace_to']['attr'] as $attr_name => $attr_value ) {
					if ( ! empty( $attributes_named_arr[ $attr_name ] ) ) {
						$attributes_named_arr[ $attr_name ] .= ' ' . $attr_value;
					} else {
						$attributes_named_arr[ $attr_name ] = $attr_value;
					}
				}

				$final_attributes = WPBC_Settings_API::get_custom_attr_static( array( 'attr' => $attributes_named_arr ) );

				if ( ! empty( $final_attributes ) ) {
					$replace_to = str_replace( '>', ' ' . $final_attributes . '>', $replacement );
				} else {
					$replace_to = $replacement;
				}

				$full_text_content = str_replace( $found_shortcode_to_replace, $replace_to, $full_text_content );
			}
		}


		// Close tags
		if ( ! is_null( $full_text_content ) ) {
			$regex                 = '%<\/\s*' . $rx_shortcode . '\s*>%';
			$replacement           = '</' . $shortcode_pair_arr['replace_to']['tag'] . '>';
			$full_text_content = preg_replace( $regex, $replacement, $full_text_content );
		}
	}

	if ( ! is_null( $full_text_content ) ){

		// Replace      <spacer></spacer>   ->   <div style="width:100%;clear:both;"></div>       |       <spacer>height:10px;</spacer>   ->  <div style="width:100%;clear:both;height:10px"></div>
		$full_text_content = wpbc_bf__replace_custom_html_shortcode__spacer( $full_text_content );

		return $full_text_content;
	}

	// Error
	return 'WPBC. Error during replacing custom HTML shortcodes! <br>' .
	       $html_original_content;
}



	/**
	 * Escape booking from with alloweed HTML  atgs + Simple HTML shortcodes
	 * @param $content
	 *
	 * @return string
	 */
	function wpbc_form_escape_in_demo( $content ) {

		// Replace my comments
		$content = str_replace(
							array(
									'<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section):',
									'Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> -->'
								), 	'',
								trim( stripslashes( $content ) )
						);

		$value = wp_kses(
							trim( stripslashes( $content ) ),
							array_merge( array(
												'r' => array( 'style'=>true , 'class'=>true , 'id'=>true ),
												'f' => array( 'style'=>true , 'class'=>true , 'id'=>true ),
												'c' => array( 'style'=>true , 'class'=>true , 'id'=>true ),
												'l' => array( 'style'=>true , 'class'=>true , 'id'=>true ),
												'item'   => array( 'style' => true, 'class' => true, 'id' => true ),
												'spacer' => array(),
												'style'  => array()
							             ),
										 wp_kses_allowed_html( 'post' )
									)
						);
		return $value;
	}


	/**
	 * Replace SPACER shortcode:   <spacer></spacer>   ->   <div style="width:100%;clear:both;"></div>       |       <spacer>height:10px;</spacer>   ->  <div style="width:100%;clear:both;height:10px"></div>
	 *
	 * @param $html_original_content
	 *
	 * @return mixed
	 */
	function wpbc_bf__replace_custom_html_shortcode__spacer( $html_original_content ) {

		$rx_shortcode = 'spacer';
		$regex        = '%<\s*' . $rx_shortcode . '\s*>([-0-9a-zA-Z:;_!\(\)|\s]*)?<\/\s*' . $rx_shortcode . '\s*>%';
		//			$fields_count = preg_match_all( $regex, $html_original_content, $fields_matches, PREG_PATTERN_ORDER );
		//			debuge($fields_count, $fields_matches);
		$html_content_replaced = preg_replace( $regex, '<div class="wpbc__spacer" style="width:100%;clear:both;${1}"></div>' ,$html_original_content );

		if ( ! is_null( $html_content_replaced ) ){
			return $html_content_replaced;
		}

		return $html_original_content;
	}