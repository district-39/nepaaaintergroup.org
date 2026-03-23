<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Shortcode functions
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-05-14
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

// =====================================================================================================================
// ==  String Manipulation functions  ==
// =====================================================================================================================

/**
 * Insert New Line symbols after <br> tags. Usefull for the settings pages to  show in redable view
 *
 * @param string $param
 * @return string
 */
function wpbc_nl_after_br($param) {

	$value = preg_replace( "@(&lt;|<)br\s*/?(&gt;|>)(\r\n)?@", "<br/>", $param );

	return $value;
}


/**
 * Replace ** to <strong> and * to  <em>
 *
 * @param String $text
 * @return string
 */
function wpbc_replace_to_strong_symbols( $text ){

	$patterns =  '/(\*\*)(\s*[^\*\*]*)(\*\*)/';
	$replacement = '<strong>${2}</strong>';
	$value_return = preg_replace($patterns, $replacement, $text);

	$patterns =  '/(\*)(\s*[^\*]*)(\*)/';
	$replacement = '<em>${2}</em>';
	$value_return = preg_replace($patterns, $replacement, $value_return);

	return $value_return;
}


/**
 * Replace 'true' | 'false' to __('yes') | __('no'). E.g.:    '...Fee: true...' => '...Fee: yes...'
 *
 * Replace value 'true' to  localized __( 'yes', 'booking' ) in Content -- usually it's required before showing text to  user for saved data of selected checkboxes, that  was configured with  empty  value: [checkbox fee ""]
 *
 * @param $value
 *
 * @return array|string|string[]
 */
function wpbc_replace__true_false__to__yes_no( $value ) {                                                                    // FixIn: 9.8.9.1.

	$checkbox_true_value = apply_filters( 'wpbc_checkbox_true_value', __( 'Y_E_S', 'booking' ) );
	$value_replaced = str_replace( 'true', $checkbox_true_value, $value );

	$checkbox_true_value = apply_filters( 'wpbc_checkbox_false_value', __( 'N_O', 'booking' ) );
	$value_replaced = str_replace( 'false', $checkbox_true_value, $value_replaced );

	return $value_replaced;
}


/**
 * Convert Strings in array  to Lower case
 * @param $array
 *
 * @return mixed
 */
function wpbc_convert__strings_in_arr__to_lower( $array ){
	return unserialize( strtolower( serialize( $array ) ) );
}


/**
 * Prepare text to show as HTML,  replace double encoded \\n to <br>  and escape \\" and  \\' . 		Mainly used in Ajax.
 *
 * @param $text
 *
 * @return array|string|string[]|null
 */
function wpbc_prepare_text_for_html( $text ){

	/**
	 * Replace <p> | <br> to ' '
	 *
	 * $plain_form_data_show = preg_replace( '/<(br|p)[\t\s]*[\/]?>/i', ' ', $plain_form_data_show );
	 */
	$text = preg_replace( '/(\\\\n)/i', '<br>', $text );                                // New line in text  areas replace with <br>
	$text = preg_replace( '/(\\\\")/i', '&quot;', html_entity_decode( $text ) );        // escape quote symbols;
	$text = preg_replace( "/(\\\\')/i", '&apos;', html_entity_decode( $text ) );        // escape quote symbols;

	// Replace \r \n \t
	$text = preg_replace( '/(\\r|\\n|\\t)/i', ' ', $text );

	return $text;
}


// TODO: Need to  check if we really  need to make this.      2023-10-06 12:46
/**
 * Escaping text  for output.
 *
 * @param string $output
 *
 * @return string
 */
function wpbc_escaping_text_for_output( $output ){

	// Save empty  spaces
	$original_symbols  = array( '&nbsp;'  );
	$temporary_symbols = array( '^space^' );
	$output = str_replace( $original_symbols, $temporary_symbols, $output );					// &nbsp; 	-> 	^space^

	// Escaping ?
	$output = esc_js( $output );									// it adds 		'\n' symbols  	|		" into &quot		|  		<  ->  &lt;  		...
	$output = html_entity_decode( $output );						// Convert back  to HTML,  but now we have 	'\n' symbols
	$output = str_replace( "\\n", '', $output );					// Remove '\n' symbols

	// Back to empty spaces.
	$original_symbols  = array( '^space^'  );
	$temporary_symbols = array( '&nbsp;'   );
	$output = str_replace( $original_symbols, $temporary_symbols, $output);

	return $output;
}


/**
 * Convert text  with  escaped symbols like '1. Soem text  here\n2. \&quot;Quoted text\&quot;\n3. \&#039;Single quoted text\&#039;\n'        to        HTML:
 *
 * @param $text
 *
 * @return array|string|string[]
 */
function wpbc_string__escape__then_convert__n_amp__html( $text ) {

	$is_escape_sql = false;	// Do not replace %

	$escaped_text = wpbc_escape_any_xss_in_string($text, $is_escape_sql );

	$text_html = str_replace( array( "\\r\\n", "\\r", "\\n", "\r\n", "\r","\n" ), "<br/>", $escaped_text );            // FixIn: 8.1.3.4.

	$text_html = str_replace( array( "\\&" ), '&', $text_html );

	return $text_html;
}


// =====================================================================================================================
// ==  Regex for Shortcodes  ==
// =====================================================================================================================

/**
 * Get parameters of shortcode in string   '..some text [visitorbookingpayurl url='https://url.com/a/'] ...'  ->   [ 'url'='https://url.com.com/a/',  'start'=10,  'end'=80  ]
 *
 * @param string $shortcode	- shortcode name						- 'visitorbookingcancelurl'
 * @param string $subject	- string where to  search  shortcode:	- '<p>1 PT. [visitorbookingpayurl url='https://wpbookingcalendar.com/faq/']</p>'
 * @param int $pos						default 0					- 0
 * @param string $pattern_to_search		default	'%\s*([^=]*)=\s*[\'"]([^\'"]*)[\'"]\s*%'
 *
 * @return array|false			[
 * 									'url'   = "https://wpbookingcalendar.com/faq/"
 * 									'start' = 10
 * 									'end'   = 80
 *                       		]
 *
 * Example:
 *                		wpbc_get_params_of_shortcode_in_string( 'visitorbookingpayurl', '<p>1 PT. [visitorbookingpayurl url = "https://wpbookingcalendar.com/faq/"]</p>...' );
 *
 * 			output ->	[
 * 							'url'   = "https://wpbookingcalendar.com/faq/"
 * 							'start' = 10
 * 							'end'   = 80
 *                      ]
 *
 */
function wpbc_get_params_of_shortcode_in_string( $shortcode, $subject, $pos = 0, $pattern_to_search = '%\s*([^=]*)=\s*[\'"]([^\'"]*)[\'"]\s*%' ) { //FixIn: 9.7.4.4  //FixIn: 7.0.1.8     7.0.1.52

	if ( strlen( $subject ) < intval( $pos ) ) {                                        //FixIn: (9.7.4.5)
		return false;
	}

	$pos = strpos( $subject, '[' . $shortcode, $pos );                                   // FixIn: 7.0.1.52.

	if ( $pos !== false ) {
		$pos2 = strpos( $subject, ']', ( $pos + 2 ) );

		$my_params = substr( $subject, $pos + strlen( '[' . $shortcode ), ( $pos2 - $pos - strlen( '[' . $shortcode ) ) );


		preg_match_all( $pattern_to_search, $my_params, $keywords, PREG_SET_ORDER );

		foreach ( $keywords as $value ) {
			if ( count( $value ) > 1 ) {
				$shortcode_params[ trim( $value[1] ) ] = trim( $value[2] );                                            // FixIn: 9.7.4.4.
			}
		}
		$shortcode_params['start'] = $pos + 1;
		$shortcode_params['end']   = $pos2;

		return $shortcode_params;
	} else {
		return false;
	}
}


/**
 * Get shortcodes with params and text for replacing these shortcodes as new uniue shortcodes
 *
 * @param string $content_text  Example: "<span class="wpbc_top_news_dismiss">[wpbc_dismiss id="wpbc_top_news__offer_2023_04_21"]</span>"
 * @param array $shortcode_arr  Example: array( 'wpbc_dismiss' )
 *
 * @return array
 *              Example: array(
								 content    => "<span class="wpbc_top_news_dismiss">[wpbc_dismiss6764]</span>"
								 shortcodes => array(
													  'wpbc_dismiss6764' => array(
																					shortcode => "[wpbc_dismiss6764]",
																					params => array( id => "wpbc_top_news__offer_2023_04_21" )
																					shortcode_original => "[wpbc_dismiss id="wpbc_top_news__offer_2023_04_21"]"
																				  )
													)
							)
 */
function wpbc_get_shortcodes_in_text__as_unique_replace( $content_text, $shortcode_arr = array( 'wpbc_dismiss' ) ) {                   // FixIn: 9.6.1.8.

	$replace = array();

	foreach ( $shortcode_arr as $single_shortcode ) {

		$pos = 0;           // Loop to find if we have several such shortcodes in $content_text
		do {
			$shortcode_params = wpbc_get_params_of_shortcode_in_string( $single_shortcode, $content_text, $pos );

			if (  ( ! empty( $shortcode_params ) ) && ( isset( $shortcode_params['end'] ) ) && ( $shortcode_params['end'] < strlen( $content_text ) )  ){

				$exist_replace = substr( $content_text, $shortcode_params['start'], ( $shortcode_params['end'] - $shortcode_params['start'] ) );

				$new_replace = $single_shortcode . wp_rand( 1000, 9000 );

				$pos = $shortcode_params['start'] + strlen( $new_replace );

				$content_text = substr_replace( $content_text, $new_replace, $shortcode_params['start'], ( $shortcode_params['end'] - $shortcode_params['start'] ) );

				$params_in_shortcode = $shortcode_params;
				unset( $params_in_shortcode['start'] );
				unset( $params_in_shortcode['end'] );
				$replace[ $new_replace ] = array(
												  'shortcode'          => '[' . $new_replace . ']',
												  'params'             => $params_in_shortcode,
												  'shortcode_original' => '[' . $exist_replace . ']',
											);
			} else {
				$shortcode_params = false;
			}

		} while ( ! empty( $shortcode_params ) );

	}

	return array(
		'content'    => $content_text,
		'shortcodes' => $replace
	);
}


// ---------------------------------------------------------------------------------------------------------------------

/**
 *  >=BS - for 'Billing fields' - Get fields from booking form at the settings page or return false if no fields
 *
 * @param string $booking_form
 * @return mixed  false | array( $fields_count, $fields_matches )
 */
function wpbc_get_fields_from_booking_form( $booking_form = '' ){

	if ( empty( $booking_form ) ) {
		$booking_form = get_bk_option( 'booking_form' );
	}

	$types         = 'text[*]?|email[*]?|time[*]?|textarea[*]?|select[*]?|selectbox[*]?|checkbox[*]?|radio|acceptance|captchac|captchar|file[*]?|quiz';
	$regex         = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
	$regex2        = '%\[\s*(country[*]?|starttime[*]?|endtime[*]?)(\s*[a-zA-Z]*[0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)*((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';
	$fields_count  = preg_match_all( $regex, $booking_form, $fields_matches );
	$fields_count2 = preg_match_all( $regex2, $booking_form, $fields_matches2 );

	//Gathering Together 2 arrays $fields_matches  and $fields_matches2
	foreach ( $fields_matches2 as $key => $value ) {
		if ( $key == 2 ) {
			$value = $fields_matches2[1];
		}
		foreach ( $value as $v ) {
			$fields_matches[ $key ][ count( $fields_matches[ $key ] ) ] = $v;
		}
	}
	$fields_count += $fields_count2;

	if ( $fields_count > 0 ) {
		return array( $fields_count, $fields_matches );
	} else {
		return false;
	}
}


/**
 * >= BM - for 'Advanced costs' -- Get only SELECT, CHECKBOX & RADIO fields from booking form at the settings page or return false if no fields
 *
 * @param string $booking_form
 * @return mixed  false | array( $fields_count, $fields_matches )
 */
function wpbc_get_select_checkbox_fields_from_booking_form( $booking_form = '' ){

	if ( empty( $booking_form )  )
		$booking_form  = get_bk_option( 'booking_form' );

	$types = 'select[*]?|selectbox[*]?|checkbox[*]?|radio[*]?';                                                                // FixIn: 8.1.3.7.
	$regex = '%\[\s*(' . $types . ')(\s+[a-zA-Z][0-9a-zA-Z:._-]*)([-0-9a-zA-Z:#_/|\s]*)?((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';

	$fields_count = preg_match_all($regex, $booking_form, $fields_matches) ;

	if ( $fields_count > 0 )
		 return array( $fields_count, $fields_matches );
	else return false;
}


// ---------------------------------------------------------------------------------------------------------------------
// Used in 'Search Availability' functionality
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Parse Option Values to get Title and Value,  if used @@           '10 AM - 2PM@@10:00 - 14:00'      ->      [ '10 AM - 2PM',    '10:00 - 14:00' ]
 *
 * @param $option       '10 AM - 2PM@@10:00 - 14:00'            OR      '10:00 - 14:00'
 *
 * @return array        [ '10 AM - 2PM', '10:00 - 14:00' ]		OR		[ '10:00 - 14:00',  '10:00 - 14:00' ]
 *
 *
 *   Example #1:           wpbc_field_option__get_tile_value( '10 AM - 2PM@@10:00 - 14:00' )    ->      [ '10 AM - 2PM',    '10:00 - 14:00' ]
 *   Example #2:           wpbc_field_option__get_tile_value( '10:00 - 14:00' )                 ->      [ '10:00 - 14:00',  '10:00 - 14:00' ]
 */
function wpbc_shortcode_option__get_tile_value( $option ) {
	$option_title_value_arr = explode( '@@', $option );

	$option_title = $option_title_value_arr[0];
	$option_value = ( count( $option_title_value_arr ) == 2 ) ? $option_title_value_arr[1] : $option_title_value_arr[0];

	return array( $option_title, $option_value );
}



/**
 * Find shortcodes with Options:        [search_quantity "---@@1" 'Title A@@value2' "3"]    or  [search_quantity]
 *
 * @param $content         string    ex.: '<a href="[search_result_url]" class="wpbc_book_now_link"> ... [search_result_button "Booking" 'other' "d'ata"] ... '
 * @param $shortcode_to_search        string    ex.: 'search_result_url'
 *
 * @return array [
 *                    [search_result_url] => [
 *                                                        [replace] => [search_result_url]
 *                                                        [options] => []
 *                                       ]
 *                    [search_result_button] => [
 *                                                    	  [replace] => [search_result_button "Booking" 'other' "d'ata"]
 *                                                        [options] => [
 *                                                                        [0] => Booking
 *                                                                        [1] => other
 *                                                                        [2] => d'ata
 *                                                                    ]
 *                                                  ]
 *                 ]
 *
 *  Example: to  search one shortcode:   	 wpbc_shortcode_options__get( $text, 'search_result_button' );
 *  Example: to  search several shortcode:   wpbc_shortcode_options__get( $text, 'search_result_button|search_result_url' );
 *
 */
function wpbc_shortcode_with_options__find( $content, $shortcode_to_search = 'search_result_button' . '|' . 'search_result_url' ) {

	$shortcodes_arr = array();

	$regex = '%\[\s*(' . $shortcode_to_search . ')\s*((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';

	$fields_count = preg_match_all($regex, $content, $fields_matches, PREG_SET_ORDER ) ;

	if ( $fields_count > 0 ) {
		foreach ( $fields_matches as $found_shortcodes_arr ) {
			list( $full_shortcode_to_replace, $shortcode_name, $shortcode_params ) = $found_shortcodes_arr;

			$shortcodes_arr[$shortcode_name] = array( 'replace' => $full_shortcode_to_replace, 'options' => array() );

			$pattern_to_search = '%\s*[\'"]([^\'"]*)[\'"]\s*%';
			$pattern_to_search = '%\s*[\']([^\']*)[\']|[\"]([^\"]*)[\"]\s*%';
			$fields_count = preg_match_all($pattern_to_search, $shortcode_params, $options_matches, PREG_SET_ORDER ) ;

			foreach ( $options_matches as $found_options_arr ) {
				$found_option_to_replace = $found_options_arr[0];
				$found_option_value = $found_options_arr[ ( count( $found_options_arr ) - 1 ) ];

				$shortcodes_arr[$shortcode_name]['options'][]=$found_option_value;
			}
		}
	}

	return $shortcodes_arr;
}



/**
 * Find shortcodes with Names and Options:         [selectbox location "Any@@" "United States@@USA" "France" "Spain"]
 *
 * @param $content              string    ex.:      '... text ... [selectbox search_time 'Any@@' "10 AM - 2PM@@10:00 - 14:00" '3 PM - 4PM@@15:00 - 16:00']  .... [selectbox othersearch_time] ... [selectbox location "Any@@" "Spain Country@@Spain" "France" "Singapur" "Tirana"]   .... '
 * @param $shortcode_to_search  string    ex.:      'select|selectbox'
 *
 * @return array [
 *                    [search_time] =>  [
 *                                        [replace]  => [selectbox search_time 'Any@@' "10 AM - 2PM@@10:00 - 14:00" '3 PM - 4PM@@15:00 - 16:00']
 *                                        [type]     => selectbox
 *                                        [name]     => search_time
 *                                        [options]  => [
 *                                                        [0] => Any@@
 *                                                        [1] => 10 AM - 2PM@@10:00 - 14:00
 *                                                        [2] => 3 PM - 4PM@@15:00 - 16:00
 *                                                      ]
 *                                      ],
 *                    [othersearch_time] => [
 *                                            [replace] => [selectbox othersearch_time]
 *                                            [type]    => select
 *                                            [name]    => othersearch_time
 *                                            [options] => []
 *                                        ]
 *                    [location] => [
 *                                    [replace] => [selectbox location "Any@@" "Spain Country@@Spain" "France" "Singapur" "Tirana"]
 *                                    [type]    => select
 *                                    [name]    => location
 *                                    [options] => [
 *                                                    [0] => Any@@
 *                                                    [1] => Spain Country@@Spain
 *                                                    [2] => France
 *                                                    [3] => Singapur
 *                                                    [4] => Tirana
 *                                                ]
 *                                ]
 *                ]
 *
 *  Example: to  search several shortcode types:        wpbc_shortcode_with_name_and_options__find( $text, 'select|selectbox' );
 *  Example: to  search one shortcode type:             wpbc_shortcode_with_name_and_options__find( $text, 'checkbox' );
 *
 */
function wpbc_shortcode_with_name_and_options__find( $content, $shortcode_to_search = 'select' . '|' . 'selectbox' ) {

	$shortcodes_arr = array();

	$regex = '%\[\s*(' . $shortcode_to_search . ')\s+([^\s]+)\s*((?:\s*(?:"[^"]*"|\'[^\']*\'))*)?\s*\]%';

	$fields_count = preg_match_all($regex, $content, $fields_matches, PREG_SET_ORDER ) ;

	if ( $fields_count > 0 ) {
		foreach ( $fields_matches as $found_shortcodes_arr ) {
			list( $full_shortcode_to_replace, $shortcode_type, $shortcode_name, $shortcode_params ) = $found_shortcodes_arr;

			$shortcodes_arr[$shortcode_name] = array(
														'replace' => $full_shortcode_to_replace,
														'type'    => $shortcode_type,
														'name'    => $shortcode_name,
														'options' => array()
													);

			$pattern_to_search = '%\s*[\'"]([^\'"]*)[\'"]\s*%';
			$pattern_to_search = '%\s*[\']([^\']*)[\']|[\"]([^\"]*)[\"]\s*%';
			$fields_count = preg_match_all($pattern_to_search, $shortcode_params, $options_matches, PREG_SET_ORDER ) ;

			foreach ( $options_matches as $found_options_arr ) {
				$found_option_to_replace = $found_options_arr[0];
				$found_option_value = $found_options_arr[ ( count( $found_options_arr ) - 1 ) ];

				$shortcodes_arr[$shortcode_name]['options'][]=$found_option_value;
			}
		}
	}

	return $shortcodes_arr;
}



// ---------------------------------------------------------------------------------------------------------------------
// Replace <script> tags to <#
// ---------------------------------------------------------------------------------------------------------------------


/**
 * Replace <script> tags to <#
 *
 * @param $result
 *
 * @return array|string|string[]|null
 */
function wpbc_replace__js_scripts__to__tpl_scripts( $result ){

	// Replace <script> tags to <#
	$pattern = '/<script\s*(type=[\'"]+text\/javascript[\'"]+)?\s*>/i';
	$result  = preg_replace( $pattern, '<#', $result );

	// Replace </script> tags to #>
	$pattern = '/<\/script>/i';
	$result  = preg_replace( $pattern, '#>', $result );

	return $result;
}