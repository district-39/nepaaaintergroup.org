<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Toolbar UI Elements. Data for UI Elements at Booking Calendar admin pages
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-08-13
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


// ---------------------------------------------------------------------------------------------------------------------
//  Simple  elements
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show FLEX Button
 *
 * @param array $item
                        array(
                                'type' => 'button'
                              , 'title' => ''                     // Title of the button
                              , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                              , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                              , 'action' => ''                    // Some JavaScript to execure, for example run  the function
                              , 'class' => ''     				  // wpbc_ui_button  | wpbc_ui_button_primary
                              , 'icon' => ''
                              , 'font_icon' => ''
                              , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                              , 'style' => ''                     // Any CSS class here
                              , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                              , 'attr' => array()
                        );
 */
function wpbc_flex_button( $item ) {

    $default_item_params = array(
                                'type' => 'button'
                              , 'title' => ''                     // Title of the button
                              , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                              , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                              , 'action' => ''                    // Some JavaScript to execure, for example run  the function
		                      , 'id' 	=> ''     				  // ''  | 'wpbc_ui_button_primary'
                              , 'class' => ''     				  // ''  | 'wpbc_ui_button_primary'
							  , 'icon' => false					  // array( 'icon_font' => 'wpbc_icn_check_circle_outline', 'position' => 'right', 'icon_img' => '' )
                              , 'style' => ''                     // Any CSS class here
                              , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                              , 'attr' => array()
							  , 'options' => array( 'link' => 'esc_attr' ) // array( 'link' => 'decode' )
                        );
    $item_params = wp_parse_args( $item, $default_item_params );

	$css_button_class = esc_attr( $item_params['class'] );

	if ( 'wpbc_button_as_icon' !== $css_button_class ) {
		$css_button_class = 'wpbc_ui_button ' . $css_button_class;
	}

    ?><a  class="wpbc_ui_control <?php echo esc_attr( $css_button_class );
													  echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
		  style="<?php 		  echo esc_attr( $item_params['style'] ); ?>"
		  href="<?php
					  if ( 'esc_attr' == $item_params['options']['link'] ) {
						  echo esc_attr( $item_params['link'] );
					  }
					  if ( 'decode' == $item_params['options']['link'] ) {
						  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						  echo str_replace( '"', '', htmlspecialchars_decode( esc_attr( $item_params['link'] ), ENT_QUOTES ) );
					  }
					  if ( 'no_decode' == $item_params['options']['link'] ) {
						  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						  echo str_replace( '"', '',  $item_params['link'] );
					  }
		  		?>"
          <?php if ( ! empty( $item_params['id'] ) ) { ?>
			id="<?php echo wp_kses_post( $item_params['id'] ); ?>"
          <?php } ?>

			<?php
			if ( ! empty( $item_params['action'] ) ) {
			?>
			onclick="javascript:<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpbc_esc_js( $item_params['action'] );
				?>" <?php
			}
			?>

		  <?php if ( ! empty( $item_params['hint'] ) ) { ?>
			  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
		  <?php } ?>
          <?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpbc_get_custom_attr( $item_params );
		  ?>
        ><?php
              $btn_icon = '';

			  // Icon
			  if ( ( ! empty( $item_params['icon'] ) ) && ( is_array( $item_params['icon'] ) ) ) {

				  // Icon IMG
				  if ( ! empty( $item_params['icon']['icon_img'] ) ) {

					  if ( substr( $item_params['icon']['icon_img'], 0, 4 ) != 'http' ) {
						  $img_path = WPBC_PLUGIN_URL . '/assets/img/' . $item_params['icon']['icon_img'];
					  } else {
						  $img_path = $item_params['icon']['icon_img'];
					  }
					  // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
					  $btn_icon = '<img class="menuicons" src="' . esc_url( $img_path ) . '" />';    // Img  Icon
				  }

				  // Icon Font
				  if ( ! empty( $item_params['icon']['icon_font'] ) ) {
					  $btn_icon = '<i class="menu_icon icon-1x ' . esc_attr( $item_params['icon']['icon_font'] ) . '"></i>';                         // Font Icon
				  }
			  }

			  if ( ( ! empty( $btn_icon ) ) && ( $item_params['icon']['position'] == 'left' ) ) {
				  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				  echo $btn_icon;

				  if ( ! empty( $item_params['title'] ) ) {
					  echo '&nbsp;';
				  }
			  }

              // Text
              echo '<span' . ( (  ( ! empty( $btn_icon ) )  && ( ! $item_params['mobile_show_text'] ) )? ' class="in-button-text"' : '' ) . '>';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo html_entity_decode(
											  wp_kses_post( $item_params['title'] )		// Sanitizes content for allowed HTML tags for post content
											, ENT_QUOTES								// Convert &quot;  to " and '
											, get_bloginfo( 'charset' ) 				// 'UTF-8'  or other
										);												// Convert &amp;dash;  to &dash;  etc...

			  	if ( ( ! empty( $btn_icon ) ) && ( $item_params['icon']['position'] == 'right' ) ) {
					echo '&nbsp;';
				}

              echo '</span>';

				if ( ( ! empty( $btn_icon ) ) && ( $item_params['icon']['position'] == 'right' ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $btn_icon;
				}
    ?></a><?php
}

/**
 * Show FLEX Label
 *
 * @param array $item
                        array(
                                  'id' => ''                        // HTML ID  of INPUT  element
                                , 'label' => __('Text..','booking') // Label  text  here
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element
                                , 'disabled' => false
                                , 'attr' => array()                 // Any  additional attributes
                        )
*/
function wpbc_flex_label( $item ) {

    $default_item_params = array(
                                  'id' => ''                        // HTML ID  of element
								, 'label' => ''						// Label
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element
                                , 'disabled' => false
								, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                , 'attr' => array()                 // Any additional attributes, if this radio | checkbox element
                        );
    $item_params = wp_parse_args( $item, $default_item_params );

	if ( ( ! empty( $item_params['label'] ) ) || ( ! empty( $btn_icon ) ) ) {

		?><label for="<?php echo esc_attr( $item_params['id'] ); ?>"
				 class="wpbc_ui_control_label <?php echo esc_attr( $item_params['class'] );
				 									echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
				 style="<?php echo esc_attr( $item_params['style'] ); ?>"
				  <?php if ( ! empty( $item_params['hint'] ) ) { ?>
					  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
				  <?php } ?>
				<?php disabled( $item_params['disabled'], true ); ?>
				<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpbc_get_custom_attr( $item_params ); ?>
		><?php

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo html_entity_decode(
									  wp_kses_post( $item_params['label'] )		// Sanitizes content for allowed HTML tags for post content
									, ENT_QUOTES								// Convert &quot;  to " and '
									, get_bloginfo( 'charset' ) 				// 'UTF-8'  or other
								);												// Convert &amp;dash;  to &dash;  etc...
		?></label><?php
	}

}

/**
 * Show FLEX text
 *
 * @param array $item
 *
 *  Example:
				$params_checkbox = array(
 										  'id'       => 'my_check_id' 		// HTML ID  of element
										, 'name'     => 'my_check_id'
										, 'label'    => __('Approve' ,'booking')
										, 'style'    => '' 					// CSS of select element
										, 'class'    => '' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'placeholder' => ''
										, 'value'    => 'CHECK_VAL_1' 		// Some Value from optins array that selected by default
										, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
										, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
                        );
 				?><div class="ui_element"><?php

				wpbc_flex_text( $params_select );

				?></div><?php
 *
 */
function wpbc_flex_text( $item ) {

    $default_item_params = array(
                                  'type'        => 'text'
                                , 'id'          => ''
                                , 'name'        => ''
                                , 'label'       => ''
                                , 'disabled'    => false
                                , 'class'       => ''
                                , 'style'       => ''
                                , 'placeholder' => ''
                                , 'attr'        => array()
                                , 'value' 		=> ''
								, 'is_escape_value' => true
                                , 'onfocus' => ''					// JavaScript code
                                , 'onchange' => ''					// JavaScript code
								, 'onkeydown' => ''					// JavaScript code

    );
    $item_params = wp_parse_args( $item, $default_item_params );

	if ( 		 ( empty( $item_params['name'] ) )
			&& ( ! empty( $item_params['id'] ) )
	) {
		$item_params['name'] = $item_params['id'];
	}

	// Show only, 		if 		$item_params['label']   	! EMPTY
	wpbc_flex_label(
						array(
							  'id' 	  => $item_params['id']
							, 'label' => $item_params['label']
						)
				   );

	if ( $item_params['is_escape_value'] ) {
		$escaped_value = esc_attr( $item_params['value'] );
	} else {
		$escaped_value = $item_params['value'];
	}

    ?><input  type="<?php 	echo esc_attr( $item_params['type'] ); ?>"
              id="<?php 	echo esc_attr( $item_params['id'] ); ?>"
              name="<?php 	echo esc_attr( $item_params['name'] ); ?>"
              style="<?php 	echo esc_attr( $item_params['style'] ); ?>"
              class="wpbc_ui_control wpbc_ui_text <?php echo esc_attr( $item_params['class'] ); ?>"
              placeholder="<?php 	echo esc_attr( $item_params['placeholder'] ); ?>"
              value="<?php
			  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			  echo $escaped_value; ?>"
	 		  autocomplete="off"
              <?php disabled( $item_params['disabled'], true ); ?>
              <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
              <?php
                if ( ! empty( $item_params['onfocus'] ) ) {
                    ?> onfocus="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onfocus'] ); ?>" <?php
                }
				if ( ! empty( $item_params['onchange'] ) ) {
					?> onchange="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onchange'] ); ?>" <?php
				}
				if ( ! empty( $item_params['onkeydown'] ) ) {
					?> onkeydown="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onkeydown'] ); ?>" <?php
				}
              ?>
          /><?php
}

/**
 * Show FLEX textarea
 *
 * @param array $item
 *
 *  Example:
				$params_textarea = array(
 										  'id'       => 'my_check_id' 		// HTML ID  of element
										, 'name'     => 'my_check_id'
										, 'label'    => __('Approve' ,'booking')
										, 'style'    => '' 					// CSS of select element
										, 'class'    => '' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'rows' 		=> '3'
										, 'cols' 		=> '50'
										, 'placeholder' => ''
										, 'value'    => 'Test VAL 1' 		// Some Value from optins array that selected by default
										, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
										, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
                        );
 				?><div class="ui_element"><?php

					wpbc_flex_textarea( $params_textarea );

				?></div><?php
 *
 */
function wpbc_flex_textarea( $item ) {

    $default_item_params = array(
                                  'id'          => ''
                                , 'name'        => ''
                                , 'label'       => ''
                                , 'disabled'    => false
                                , 'class'       => ''
                                , 'style'       => ''
                                , 'placeholder' => ''
                                , 'attr'        => array()
                        		, 'rows' 		=> '3'
                                , 'cols' 		=> '50'
                                , 'value' 		=> ''
								, 'is_escape_value' => true
                                , 'onfocus' => ''					// JavaScript code
                                , 'onchange' => ''					// JavaScript code
    );
    $item_params = wp_parse_args( $item, $default_item_params );

	if ( 		 ( empty( $item_params['name'] ) )
			&& ( ! empty( $item_params['id'] ) )
	) {
		$item_params['name'] = $item_params['id'];
	}

	// Show only, 		if 		$item_params['label']   	! EMPTY
	wpbc_flex_label(
						array(
							  'id' 	  => $item_params['id']
							, 'label' => $item_params['label']
						)
				   );

	if ( $item_params['is_escape_value'] ) {
		$escaped_value = esc_textarea( $item_params['value'] );
	} else {
		$escaped_value = $item_params['value'];
	}

    ?><textarea   id="<?php 	echo esc_attr( $item_params['id'] ); ?>"
				  name="<?php 	echo esc_attr( $item_params['name'] ); ?>"
				  style="<?php 	echo esc_attr( $item_params['style'] ); ?>"
				  class="wpbc_ui_control wpbc_ui_textarea <?php echo esc_attr( $item_params['class'] ); ?>"
				  placeholder="<?php 	echo esc_attr( $item_params['placeholder'] ); ?>"
				  autocomplete="off"
				  rows="<?php 	echo esc_attr( $item_params['rows'] ); ?>"
				  cols="<?php 	echo esc_attr( $item_params['cols'] ); ?>"
				  <?php disabled( $item_params['disabled'], true ); ?>
				  <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
				  <?php
					if ( ! empty( $item_params['onfocus'] ) ) {
						?> onfocus="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onfocus'] ); ?>" <?php
					}
					if ( ! empty( $item_params['onchange'] ) ) {
						?> onchange="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onchange'] ); ?>" <?php
					}
				  ?>
          ><?php echo esc_html( $escaped_value ); ?></textarea><?php
}

/**
	 * Show FLEX selectbox
 *
 * @param array $item
                        array(
                                  'id' => ''                        // HTML ID  of element
                                , 'name' => ''
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element
                                , 'multiple' => false
                                , 'disabled' => false
                                , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                , 'options' => array()              // Associated array  of titles and values
                                , 'disabled_options' => array()     // If some options disbaled,  then its must list  here
                                , 'value' => ''                     // Some Value from optins array that selected by default
								, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code
								, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code
                        )
 *
 *
 *  Simple Example :
 *
	 $params_select = array(
						  'id' => 'next_days'                        						// HTML ID  of element
						, 'name' => 'next_days'
		 				, 'label' => '' 	// __( 'Next Days', 'booking' )					// Label (optional)
						, 'style' => ''                     								// CSS of select element
						, 'class' => ''                     								// CSS Class of select element
						, 'multiple' => false
						, 'attr' => array()                 								// Any additional attributes, if this radio | checkbox element
						, 'disabled' => false
		 				, 'disabled_options' => array( 2, 30 )     							// If some options disabled, then it has to list here
						, 'options' => array(												// Associated array of titles and values
											  , 1 => '1' . ' ' . __('day' ,'booking')
											  , 2 => '2' . ' ' . __('days' ,'booking')
											  , 7 => '1' . ' ' . __('week' ,'booking')
											  , 30 => '1' . ' ' . __('month' ,'booking')
											  , 365 => '1' . ' ' . __('Year' ,'booking')
										)

						, 'value' => ( isset( $escaped_search_request_params[ 'next_days' ] ) ) ? $escaped_search_request_params[ 'next_days' ] : '183'	// Some Value from options array that selected by default
						, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code
						, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code

					  );

 	?><div class="ui_element"><?php

	wpbc_flex_select( $params_select );

	?></div><?php
 *
 *
 *
 * Example complex:
 *
 *
 *

	 $params_select = array(
						  'id' => 'next_days'                        // HTML ID  of element
						, 'name' => 'next_days'
		 				, 'label' => '<span class="" style="font-weight:600;">' . esc_html__( 'Cost', 'booking' ) . ' <em style="color:#888;">(' . __( 'min-max', 'booking' ) . '):</em></span>'
						, 'style' => ''                     // CSS of select element
						, 'class' => ''                     // CSS Class of select element
						, 'multiple' => false
						, 'attr' => array()                 // Any additional attributes, if this radio | checkbox element
						, 'disabled' => false
		 				, 'disabled_options' => array( 2, 5 )     // If some options disabled, then it has to list here
						, 'options' => array(				// Associated array of titles and values:   array( $option_value => $option_data, ... )
											    'group_days' => array( 'optgroup' => true, 'close' => false, 'title' => __( 'days', 'booking' ) )
											  , 1 => '1' . ' ' . __('day' ,'booking')
											  , 2 => '2' . ' ' . __('days' ,'booking')
											  , 3 => '3' . ' ' . __('days' ,'booking')
											  , 4 => '4' . ' ' . __('days' ,'booking')
											  , 5 => '5' . ' ' . __('days' ,'booking')
											  , 6 => '6' . ' ' . __('days' ,'booking')
			 								  	, 'group_days_end' => array( 'optgroup' => true, 'close' => true )

			 								  	, 'group_weeks' => array( 'optgroup' => true, 'close' => false, 'title' => __( 'weeks', 'booking' ) )
											  , 7 => '1' . ' ' . __('week' ,'booking')
											  , 14 => '2' . ' ' . __('weeks' ,'booking')
			 								  , 'group_weeks_end' => array( 'optgroup' => true, 'close' => true )

			 								  	, 'group_months' => array( 'optgroup' => true, 'close' => false, 'title' => __( 'months', 'booking' ) )
											  , 30 => '1' . ' ' . __('month' ,'booking')
											  , 60 => '2' . ' ' . __('months' ,'booking')
											  , 90 => '3' . ' ' . __('months' ,'booking')
											  , 183 => '6' . ' ' . __('months' ,'booking')
											  , 365 => '1' . ' ' . __('Year' ,'booking')
			 								  	, 'group_months_end' => array( 'optgroup' => true, 'close' => true )

											  	, 'complex_value' => array(
																		  'title' => 'Complex Option Here'		// Text  in selectbox option
																		, 'style' => ''                         // CSS of select element
																		, 'class' => ''                         // CSS Class of select element
																		, 'disabled' => false
																		, 'selected' => true
																		, 'attr' => array()                     // Any  additional attributes, if this radio | checkbox element
																		, 'optgroup' => false                   // Use only  if you need to show OPTGROUP - Also  need to  use 'title' of start, end 'close' for END
																		, 'close'  => false
																)
										)

						, 'value' => ( isset( $escaped_search_request_params[ 'next_days' ] ) ) ? $escaped_search_request_params[ 'next_days' ] : '183'	// Some Value from options array that selected by default
						, 'onfocus' =>  "console.log( 'ON FOCUS:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code
						, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val(), 'in element:' , jQuery( this ) );"					// JavaScript code

					  );

 	?><div class="ui_element"><?php

	wpbc_flex_select( $params_select );

	?></div><?php

 *
 */
function wpbc_flex_select( $item ) {

    $default_item_params = array(
                                  'id' => ''                        // HTML ID  of element
                                , 'name' => ''
								, 'label' => ''						// Label
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element
                                , 'multiple' => false
                                , 'disabled' => false
                                , 'attr' => array()                 // Any additional attributes, if this radio | checkbox element
                                , 'options' => array()              // Associated array of titles and values:   array( $option_value => $option_data, ... )
                                , 'disabled_options' => array()     // If some options disabled, then it has to list here
                                , 'value' => ''                     // Some Value from options array that selected by default
                                , 'onfocus' => ''					// JavaScript code
                                , 'onchange' => ''					// JavaScript code
                        );
    $item_params = wp_parse_args( $item, $default_item_params );

	// Show only, 		if 		$item_params['label']   	! EMPTY
	wpbc_flex_label(
						array(
							  'id' 	  => $item_params['id']
							, 'label' => $item_params['label']
						)
				   );

    ?><select
            id="<?php 	echo esc_attr( $item_params['id'] ); ?>"
            name="<?php echo esc_attr( $item_params['name'] ); echo ( $item_params['multiple'] ? '[]' : '' ); ?>"
            class="wpbc_ui_control wpbc_ui_select <?php echo esc_attr( $item_params['class'] ); ?>"
            style="<?php echo esc_attr( $item_params['style'] ); ?>"
            <?php disabled( $item_params['disabled'], true ); ?>
            <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
            <?php echo ( $item_params['multiple'] ? ' multiple="MULTIPLE"' : '' ); ?>
            <?php
                if ( ! empty( $item_params['onfocus'] ) ) {
                    ?> onfocus="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onfocus'] ); ?>" <?php
                }
                if ( ! empty( $item_params['onchange'] ) ) {
                    ?> onchange="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onchange'] ); ?>" <?php
                }
            ?>
            autocomplete="off"
        ><?php
        foreach ( $item_params['options'] as $option_value => $option_data ) {

	        if ( ! is_array( $option_data ) ) {
		        $option_data   = array( 'title' => $option_data );
		        $is_was_simple = true;
	        } else {
		        $is_was_simple = false;
	        }

            $default_option_params = array(
                                          'title' => ''							// Text  in selectbox option
                                        , 'style' => ''                         // CSS of select element
                                        , 'class' => ''                         // CSS Class of select element
                                        , 'disabled' => false
                                        , 'selected' => false
                                        , 'attr' => array()                     // Any  additional attributes, if this radio | checkbox element

                                        , 'optgroup' => false                   // Use only  if you need to show OPTGROUP - Also  need to  use 'title' of start, end 'close' for END
                                        , 'close'  => false

                                );
            $option_data = wp_parse_args( $option_data, $default_option_params );

            if ( $option_data['optgroup'] ) {                                   // OPTGROUP

                if ( ! $option_data['close'] ) {
                    ?><optgroup label="<?php  echo esc_attr( $option_data['title'] ); ?>"><?php
                } else {
                    ?></optgroup><?php
                }

            } else {                                                            // OPTION

                ?><option
                        value="<?php echo esc_attr( $option_value ); ?>"
                        <?php
                        if ( $is_was_simple ) {
                            selected( $option_value, $item_params['value'] );
                            disabled( in_array( $option_value, $item_params['disabled_options'] ), true );
                        }

						if ( ! empty( $option_data['class'] ) ){
							echo ' class="' . esc_attr( $option_data['class'] ) . '" ';
						}
						if ( ! empty( $option_data['style'] ) ){
							echo ' style="' . esc_attr( $option_data['style'] ) . '" ';
						}

						selected( $option_data['selected'], true );

						disabled( $option_data['disabled'], true );


	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $option_data );

						if ( ! empty( $item_params['value'] ) ) {

							if ( is_array( $item_params['value'] ) ) {
								selected( in_array( esc_attr( $option_value ), $item_params['value'] ), true );		// SELECT multiple,  have several items
							} else {
								selected( $item_params['value'], esc_attr( $option_value ) ); 						//Recheck  global  selected parameter
							}
						}
					?>
                ><?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo html_entity_decode(
												  wp_kses_post( $option_data['title'] )		// Sanitizes content for allowed HTML tags for post content
												, ENT_QUOTES								// Convert &quot;  to " and '
												, get_bloginfo( 'charset' ) 				// 'UTF-8'  or other
											);												// Convert &amp;dash;  to &dash;  etc...


				?></option><?php
            }
        }
    ?></select><?php
}

/**
 * Show FLEX checkbox
 *
 * @param array $item
 *
 *  Example:
				$params_checkbox = array(
										  'id'       => 'my_check_id' 		// HTML ID  of element
										, 'name'     => 'my_check_id'
										, 'label'    => array( 'title' => __('Approve' ,'booking') , 'position' => 'right' )
										, 'style'    => '' 					// CSS of select element
										, 'class'    => '' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'legend'   => ''					// aria-label parameter
										, 'value'    => 'CHECK_VAL_1' 		// Some Value from optins array that selected by default
										, 'selected' => !false 				// Selected or not
										, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
										, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
                    					, 'is_use_toggle' => false          // Show checkbox  as toggle
                    					, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
									);
				?><div class="ui_element"><?php

				wpbc_flex_checkbox( $params_select );

				?></div><?php
 */
function wpbc_flex_checkbox( $item ) {

    $default_item_params = array(
                                  'type' => 'checkbox'
                                , 'id' => ''                        // HTML ID  of element
                                , 'name' => ''
								, 'label' => ''      				// Label	Example: 'label' => array( 'title' => __('Select status' ,'booking') , 'position' => 'left' )
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element
                                , 'disabled' => false
                                , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                , 'legend' => ''                    // aria-label parameter
                                , 'value' => ''                     // Some Value from optins array that selected by default
                                , 'selected' => false               // Selected or not
                                , 'onfocus' => ''					// JavaScript code
                                , 'onchange' => ''					// JavaScript code
								, 'is_use_toggle' => false          // Show checkbox  as toggle
								, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                      );
    $item_params = wp_parse_args( $item, $default_item_params );


	// Use toggle element,  instead of checkbox.
	if ( $item_params['is_use_toggle'] ) {

		wpbc_flex_toggle( $item );
		return;
	}

	if ( ( ! empty( $item_params['label'] ) ) && ( 'left' == $item_params['label']['position'] ) ) {
		$label_params = array( 'id' => $item_params['id'], 'label' => $item_params['label']['title'] );
		if ( ! empty( $item_params['label']['class'] ) ) {
			$label_params['class'] = $item_params['label']['class'];
		}
		wpbc_flex_label( $label_params );
	}

    ?><input    type="<?php echo esc_attr( $item_params['type'] ); ?>"
                id="<?php echo esc_attr( $item_params['id'] ); ?>"
                name="<?php echo esc_attr( $item_params['name'] ); ?>"
                value="<?php echo esc_attr( $item_params['value'] ); ?>"
                aria-label="<?php echo esc_attr( $item_params['legend'] ); ?>"
                class="wpbc_ui_<?php echo esc_attr( $item_params['type'] ); ?> <?php echo esc_attr( $item_params['class'] ); ?> <?php echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
                style="<?php echo esc_attr( $item_params['style'] ); ?>"
				<?php
					if ( ! empty( $item_params['hint'] ) ) { ?>
						title="<?php echo esc_attr( $item_params['hint']['title'] ); ?>" <?php
					}
				?>
                <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
                <?php checked(  $item_params['selected'], true ); ?>
                <?php disabled( $item_params['disabled'], true ); ?>
				<?php
					if ( ! empty( $item_params['onfocus'] ) ) {
						?> onfocus="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onfocus'] ); ?>" <?php
					}
					if ( ! empty( $item_params['onchange'] ) ) {
						?> onchange="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onchange'] ); ?>" <?php
					}
				?>
				autocomplete="off"
        /><?php

	if ( ( ! empty( $item_params['label'] ) ) && ( 'right' == $item_params['label']['position'] ) ) {
		$label_params = array( 'id' => $item_params['id'], 'label' => $item_params['label']['title'] );
		if ( ! empty( $item_params['label']['class'] ) ) {
			$label_params['class'] = $item_params['label']['class'];
		}
		wpbc_flex_label( $label_params );
	}
}

/**
 * Show FLEX toggle    instead of checkbox
 *
 * @param array $item
 *
 *  Example:
				$params_checkbox = array(
										  'id'       => 'my_check_id' 		// HTML ID  of element
										, 'name'     => 'my_check_id'
										, 'label'    => array( 'title' => __('Approve' ,'booking') , 'position' => 'right' )
										, 'style'    => '' 					// CSS of select element
										, 'toggle_style' => ''              // Styles CSS of toggle container
										, 'toggle_class' => ''              // Class  CSS of toggle container
										, 'class' => ''                     // CSS Class of input element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'legend'   => ''					// aria-label parameter
										, 'value'    => 'CHECK_VAL_1' 		// Some Value from optins array that selected by default
										, 'selected' => !false 				// Selected or not
										, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
										, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
									);
				?><div class="ui_element"><?php

					wpbc_flex_toggle( $params_select );

				?></div><?php
 */
function wpbc_flex_toggle( $item ){

	$default_item_params = array(
							  'type' => 'checkbox'
							, 'id' => ''                        // HTML ID  of element
							, 'name' => ''
							, 'label' => ''      				// Label	Example: 'label' => array( 'title' => __('Select status' ,'booking') , 'position' => 'left' )
							, 'style' => ''                     // CSS of select element
							, 'toggle_style' => ''              // Styles CSS of toggle container
							, 'toggle_class' => ''              // Class  CSS of toggle container
							, 'class' => ''                     // CSS Class of input element
							, 'disabled' => false
							, 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
							, 'legend' => ''                    // aria-label parameter
							, 'value' => ''                     // Some Value from optins array that selected by default
							, 'selected' => false               // Selected or not
							, 'onfocus' => ''					// JavaScript code
							, 'onchange' => ''					// JavaScript code
							, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
				  );
    $item_params = wp_parse_args( $item, $default_item_params );
	/**
	 * Structure:
					?><span class="wpbc_ui__toggle">
						<input type="checkbox" name="field_name"  id="wpbc_field_id" value="On" />
						<label class="wpbc_ui__toggle_icon"  	 for="wpbc_field_id"></label>
						<label class="wpbc_ui__toggle_label" 	 for="wpbc_field_id">Some Text</label>
						<i class="wpbc_help_tooltip"></i>
					</span><?php
	*/

	?><span class="wpbc_ui__toggle <?php echo esc_attr( $item_params['toggle_class'] ); ?>" style="<?php echo esc_attr( $item_params['toggle_style'] ); ?>"><?php

		?><input    type="<?php 		 echo esc_attr( $item_params['type'] ); ?>"
					id="<?php 			 echo esc_attr( $item_params['id'] ); ?>"
					name="<?php 		 echo esc_attr( $item_params['name'] ); ?>"
					value="<?php 		 echo esc_attr( $item_params['value'] ); ?>"
					aria-label="<?php 	 echo esc_attr( $item_params['legend'] ); ?>"
					class="wpbc_ui_<?php echo esc_attr( $item_params['type'] ); ?> <?php echo esc_attr( $item_params['class'] ); ?>"
					<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
					<?php checked(  $item_params['selected'], true ); ?>
					<?php disabled( $item_params['disabled'], true ); ?>
					<?php
						if ( ! empty( $item_params['onfocus'] ) ) {
							?> onfocus="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onfocus'] ); ?>" <?php
						}
						if ( ! empty( $item_params['onchange'] ) ) {
							?> onchange="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_esc_js( $item_params['onchange'] ); ?>" <?php
						}
					?>
					autocomplete="off"
			/><?php

			?><label class="wpbc_ui__toggle_icon
					 		<?php echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>
						   "
					 for="<?php echo esc_attr( $item_params['id'] ); ?>"
					 <?php
						if ( ! empty( $item_params['hint'] ) ) { ?>
					 	    title="<?php echo esc_attr( $item_params['hint']['title'] ); ?>" <?php
						}
					 ?>
				></label><?php

			if ( ! empty( $item_params['label'] ) ) {
				wpbc_flex_label( array( 'id' => $item_params['id'], 'class' => 'wpbc_ui__toggle_label', 'label' => $item_params['label']['title'] ) );
			}

			?><i class="wpbc_help_tooltip"></i><?php

	?></span><?php

}

/**
	 * Show FLEX radio button
 *
 * @param array $item
 *
 *  Example:
				$params_checkbox = array(
										  'id'       => 'my_check_id' 		// HTML ID  of element
										, 'name'     => 'my_check_id'
										, 'label'    => array( 'title' => __('Approve' ,'booking') , 'position' => 'right' )
										, 'style'    => '' 					// CSS of select element
										, 'class'    => '' 					// CSS Class of select element
										, 'disabled' => false
										, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
										, 'legend'   => ''					// aria-label parameter
										, 'value'    => 'CHECK_VAL_1' 		// Some Value from optins array that selected by default
										, 'selected' => !false 				// Selected or not
										, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
										, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"					// JavaScript code
									);
				?><div class="ui_element"><?php

				wpbc_flex_radio( $params_select );

				$params_checkbox['id'] = 'my_check_id2';
				$params_checkbox['value'] = 'CHECK_VAL_2';

				wpbc_flex_radio( $params_select );

				?></div><?php
 */
function wpbc_flex_radio( $item ) {
    $item['type'] = 'radio';
	$item['is_use_toggle'] = false;
    wpbc_flex_checkbox( $item );
}

/**
 * Show FLEX addon (image or text  can  be here)
 *
 * @param array $item
                        array(
                            'type' => 'span'          // HTML tag that  will  bound content
                          , 'html' => ''			  // Any  other HTML content
                    	  , 'icon' => false			  // array( 'icon_font' => 'wpbc_icn_check_circle_outline', 'position' => 'right', 'icon_img' => '' )
                          , 'style' => ''             // CSS of select element
                          , 'class' => ''             // CSS Class of select element		// default included class  is .wpbc_ui_addon
                          , 'attr' => array()         // Any  additional attributes
                        );
 * Example 1:
`				$params_span = array(
									  'type'        => 'span'
									, 'html'        => '<i class="menu_icon icon-1x wpbc_icn_event"></i> &nbsp; Approve '
                     				, 'icon'        => false		// array( 'icon_font' => 'wpbc_icn_check_circle_outline', 'position' => 'right', 'icon_img' => '' )
									, 'class'       => 'wpbc_ui_button inactive ui_nowrap'
									, 'style'       => ''
									, 'attr'        => array()
								);

				?><div class="ui_element"><?php

					wpbc_flex_addon( $params_span );

					wpbc_flex_text( $params_text );

					wpbc_flex_addon( $params_span );

				?></div><?php
 * Example 2:
 		$params_addon = array(
							  'type'        => 'span'
							, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
							, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_event', 'position' => 'right', 'icon_img' => '' )
							, 'class'       => 'wpbc_ui_button inactive'
							, 'style'       => ''
							, 'attr'        => array()
						);
		?><div class="ui_element ui_nowrap"><?php
			wpbc_flex_addon( $params_addon );
		?></div><?php

 */
function wpbc_flex_addon( $item ) {

    $default_item_params = array(
                                  'type'        => 'span'
                                , 'html'        => ''
								, 'icon'        => false
                                , 'class'       => ''
                                , 'style'       => ''
								, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                , 'attr'        => array()
    );
    $item_params = wp_parse_args( $item, $default_item_params );

	// Icon
	$btn_icon = '';
	if ( ( ! empty( $item_params['icon'] ) ) && ( is_array( $item_params['icon'] ) ) ) {

		// Icon IMG
		if ( ! empty( $item_params['icon']['icon_img'] ) ) {

			if ( substr( $item_params['icon']['icon_img'], 0, 4 ) != 'http' ) {
				$img_path = WPBC_PLUGIN_URL . '/assets/img/' . $item_params['icon']['icon_img'];
			} else {
				$img_path = $item_params['icon']['icon_img'];
			}
			// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
			$btn_icon = '<img class="menuicons" src="' . esc_url( $img_path ) . '" />';    // Img  Icon
		}

		// Icon Font
		if ( ! empty( $item_params['icon']['icon_font'] ) ) {
			$btn_icon = '<i class="menu_icon icon-1x ' . esc_attr( $item_params['icon']['icon_font'] ) . '"></i>';                         // Font Icon
		}
	}

	?><<?php echo esc_attr( $item_params['type'] ); ?>
			class="wpbc_ui_control wpbc_ui_addon <?php echo esc_attr( $item_params['class'] );
													   echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
			style="<?php echo esc_attr( $item_params['style'] ); ?>"
			<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
		  <?php if ( ! empty( $item_params['hint'] ) ) { ?>
			  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
		  <?php } ?>

	><?php

	if ( ( ! empty( $btn_icon ) ) && ( 'left' == $item_params['icon']['position'] ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $btn_icon;
	}
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo html_entity_decode( wp_kses_post( $item_params['html'] )        // Sanitizes content for allowed HTML tags for post content
		, ENT_QUOTES                                                     // Convert &quot;  to " and '
		, get_bloginfo( 'charset' )                // 'UTF-8'  or other
	);                                                // Convert &amp;dash;  to &dash;  etc...


	if ( ( ! empty( $btn_icon ) ) && ( 'right' == $item_params['icon']['position'] ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $btn_icon;
	}

	?></<?php echo esc_attr( $item_params['type'] ); ?>><?php
}

function wpbc_flex_divider( $item = array() ){

    $default_item_params = array(
                                  'type'        => 'span'
                                , 'html'        => ''
								, 'icon'        => false
                                , 'class'       => ''
                                , 'style'       => ''
								, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                , 'attr'        => array()
    );
    $item_params = wp_parse_args( $item, $default_item_params );

	?><div class="wpbc_ui_control ui_elements_divider <?php echo esc_attr( $item_params['class'] );
													   echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
			style="<?php echo esc_attr( $item_params['style'] ); ?>"
			<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
		  <?php if ( ! empty( $item_params['hint'] ) ) { ?>
			  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
		  <?php } ?>
	  ></div><?php
}

/**
 * Show FLEX Vertical line for "wpbc_ui_control" elements 	with vertical  border color
 *
 * @param $item array
 *
 * @return void
 *
 *             Example:
 *             ?><div class="ui_element ui_nowrap"><?php
						wpbc_flex_vertical_color( array(
															'vertical_line' => 'border-left: 4px solid #11be4c;'
												) );
 *             ?></div><?php
 */
function wpbc_flex_vertical_color( $item = array() ){

    $default_item_params = array(
								  'id' 			=> ''
                                , 'html'        => '<span></span>'
								, 'icon'        => false
                                , 'class'       => ''
                                , 'style'       => ''
								, 'hint' 		=> ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                , 'attr'        => array()
								, 'vertical_line' => 'border-left: 4px solid #11be4c;'
    );
    $item_params = wp_parse_args( $item, $default_item_params );

	?><div
	            id="<?php 	echo esc_attr( $item_params['id'] ); ?>"
				class="wpbc_ui_control wpbc_ui_button ui_elements_vertical_color <?php echo esc_attr( $item_params['class'] );
													   echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
				style="<?php echo esc_attr( $item_params['vertical_line'] ) . ';'; ?>padding: 0;<?php echo esc_attr( $item_params['style'] ); ?>"
			<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
			<?php if ( ! empty( $item_params['hint'] ) ) { ?>
			  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
			<?php } ?>
	  ><?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $item_params['html']; ?></div><?php

}

/**
 * Show FLEX Horizontal Text Bar line for "wpbc_ui_control" elements 	with vertical  border color
 *
 * @param $item array
 *
 * @return void
 *
 *             Example:
 *
						$params_text_bar_1 = array(	  'id' 			 => ''
													, 'html'         => '1. ' . __( 'Calendar Skin', 'booking' )
													, 'option_class' => 'wpbc_option_step wpbc_passed_step'	// 'wpbc_option_step', 'wpbc_option_separator', 'wpbc_option_step wpbc_selected_step', 'wpbc_option_separator wpbc_passed_step'
													, 'class'        => ''
													, 'style'        => 'height:auto;'
													, 'hint' 		 => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
													, 'attr'         => array()
													, 'tag' 		 => 'span'
											);
						$params_text_bar_hr = array('id' 			 => ''
													, 'html'         => '&gt;'
													, 'option_class' => 'wpbc_option_separator'	// 'wpbc_option_step', 'wpbc_option_separator', 'wpbc_option_step wpbc_selected_step', 'wpbc_option_separator wpbc_passed_step'
													, 'class'        => ''
													, 'style'        => 'height:auto;'
													, 'hint' 		 => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
													, 'attr'         => array()
													, 'tag' 		 => 'span'
											);

						$params_text_bar_2                 = $params_text_bar_1;
						$params_text_bar_2['html']         = '2. ' . __( 'Calendar size', 'booking' );
						$params_text_bar_2['option_class'] = 'wpbc_option_step wpbc_selected_step';

						$params_text_bar_3                 = $params_text_bar_1;
						$params_text_bar_3['html']         = '3. ' . __( 'Dates selection', 'booking' );
						$params_text_bar_3['option_class'] = 'wpbc_option_step';

 *             ?><div class="ui_element ui_nowrap"><?php
 *
					wpbc_flex_horizontal_text_bar( $params_text_bar_1 );

					wpbc_flex_horizontal_text_bar( $params_text_bar_hr );

					wpbc_flex_horizontal_text_bar( $params_text_bar_2 );

 *             ?></div><?php
 */
function wpbc_flex_horizontal_text_bar( $item = array() ){

    $default_item_params = array(
								  'id' 			=> ''
                                , 'html'        => '<span></span>'
                                , 'class'       => ''
                                , 'style'       => ''
								, 'hint' 		=> ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                , 'attr'        => array()
								, 'tag' => 'span'
								, 'option_class'       => 'wpbc_option_step'	// 'wpbc_option_step', 'wpbc_option_separator', 'wpbc_option_step wpbc_selected_step', 'wpbc_option_separator wpbc_passed_step'
    );
    $item_params = wp_parse_args( $item, $default_item_params );

	?><<?php echo esc_attr( $item_params['tag'] );  ?>
				<?php if ( ! empty( $item_params['id'] ) ) { echo ' id="' . esc_attr( $item_params['id'] ) . '" '; } ?>
				class="wpbc_ui_control wpbc_ui_addon wpbc_text_bar <?php echo esc_attr( $item_params['class'] );
													   echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ; ?>"
				style="<?php echo esc_attr( $item_params['style'] ); ?>"
			<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
			<?php if ( ! empty( $item_params['hint'] ) ) { ?>
			  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
			<?php } ?>
	><span class="<?php echo esc_attr( $item_params['option_class'] ); ?>"><?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $item_params['html'];
	?></span></<?php echo esc_attr( $item_params['tag'] );  ?>><?php

}



// ---------------------------------------------------------------------------------------------------------------------
//  Complex  elements
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show FLEX  Dropdown
 *
 * Dropdown always have value as array. For example: [ '0' ], [ '4', '10' ]  or [ '6', '', '2022-05-24' ]
 *
 * @param array $args
 *
 *  = Simple Example: ==================================================================================================

    $params = array(
					  'id'      => 'wh_approved'
					, 'default' => isset( $escaped_search_request_params['wh_approved'] ) ? $escaped_search_request_params['wh_approved'] : $defaults['wh_approved']
                    , 'label' 	=> ''//__('Status', 'booking') . ':'
                    , 'title' 	=> __('Status', 'booking')
					, 'hint' 	=> array( 'title' => __('Filter bookings by booking status' ,'booking') , 'position' => 'top' )
					, 'li_options' => array (
											'0' => __( 'Pending', 'booking' ),
											'1' => __( 'Approved', 'booking' ),
											'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),
											// 'header1' => array( 'type' => 'header', 'title' => __( 'Default', 'booking' ) ),
											'any' => array(
														'type'     => 'simple',
														'value'    => '',
														// 'disabled' => true,
														'title'    => __( 'Any', 'booking' )
											),
									 )
                );

	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_dropdown( $params );

	?></div><?php
 *
 *
 *  = Complex Example: =================================================================================================
 *

	$params_addon = array(
						  'type'        => 'span'
						, 'html'        => ''// '<i class="menu_icon icon-1x wpbc_icn_event"></i>' //'<strong>' . esc_html__( 'Dates', 'booking ' ) . '</strong>'
						, 'icon'        =>  array( 'icon_font' => 'wpbc_icn_event', 'position' => 'right', 'icon_img' => '' )
						, 'class'       => 'wpbc_ui_button inactive'
						, 'style'       => ''
						, 'attr'        => array()
					);

    $dates_interval = array(
                                1 => '1' . ' ' . __('day' ,'booking')
                              , 2 => '2' . ' ' . __('days' ,'booking')
                              , 3 => '3' . ' ' . __('days' ,'booking')
                              , 4 => '4' . ' ' . __('days' ,'booking')
                              , 5 => '5' . ' ' . __('days' ,'booking')
                              , 6 => '6' . ' ' . __('days' ,'booking')
                              , 7 => '1' . ' ' . __('week' ,'booking')
                              , 14 => '2' . ' ' . __('weeks' ,'booking')
                              , 30 => '1' . ' ' . __('month' ,'booking')
                              , 60 => '2' . ' ' . __('months' ,'booking')
                              , 90 => '3' . ' ' . __('months' ,'booking')
                              , 183 => '6' . ' ' . __('months' ,'booking')
                              , 365 => '1' . ' ' . __('Year' ,'booking')
                        );

	$request_input_el_default = array(
		'wh_booking_date'             => isset( $escaped_search_request_params['wh_booking_date'] ) ? $escaped_search_request_params['wh_booking_date'] : $defaults['wh_booking_date'],
		'ui_wh_booking_date_radio'    => isset( $escaped_search_request_params['ui_wh_booking_date_radio'] ) ? $escaped_search_request_params['ui_wh_booking_date_radio'] : $defaults['ui_wh_booking_date_radio'],
		'ui_wh_booking_date_next'     => isset( $escaped_search_request_params['ui_wh_booking_date_next'] ) ? $escaped_search_request_params['ui_wh_booking_date_next'] : $defaults['ui_wh_booking_date_next'],
		'ui_wh_booking_date_prior'    => isset( $escaped_search_request_params['ui_wh_booking_date_prior'] ) ? $escaped_search_request_params['ui_wh_booking_date_prior'] : $defaults['ui_wh_booking_date_prior'],
		'ui_wh_booking_date_checkin'  => isset( $escaped_search_request_params['ui_wh_booking_date_checkin'] ) ? $escaped_search_request_params['ui_wh_booking_date_checkin'] : $defaults['ui_wh_booking_date_checkin'],
		'ui_wh_booking_date_checkout' => isset( $escaped_search_request_params['ui_wh_booking_date_checkout'] ) ? $escaped_search_request_params['ui_wh_booking_date_checkout'] : $defaults['ui_wh_booking_date_checkout']
	);

	$options = array (
						// 'header2'   => array( 'type' => 'header', 'title' => __( 'Complex Days', 'booking' ) ),
						// 'disabled1' => array( 'type' => 'simple', 'value' => '19', 'title' => __( 'This is option was disabled', 'booking' ), 'disabled' => true ),

						'0' => __( 'Current dates', 'booking' ),
						'1' => __( 'Today', 'booking' ),
						'2' => __( 'Previous dates', 'booking' ),
						'3' => __( 'All dates', 'booking' ),

						'divider1' => array( 'type' => 'html', 'html' => '<hr/>' ),

						'9' => __( 'Today check in/out', 'booking' ),
						'7' => __( 'Check In - Tomorrow', 'booking' ),
						'8' => __( 'Check Out - Tomorrow', 'booking' ),

						'divider2' => array( 'type' => 'html', 'html' => '<hr/>' ),

						// Next  [ '4', '10' ]		- radio button (if selected)  value '4'    and select-box with  selected value   '10'
					    'next' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								// recheck if LI selected: 	 $options['next']['selected_options_value'] == $params['default],  e.g. ~  [ '4', '10' ]
								'selected_options_value' => array(
																	1 => array( 'value' ),					//  $options['next']['input_options'][ 1 ]['value']				'4'
																	4 => array( 'value' ) 					//  $options['next']['input_options'][ 4 ]['value']				'10'
																),
								// Get selected Title, for dropdown if $options['next'] selected
								'selected_options_title' => array(
																	1 => array( 'label', 'title' ),			// $options['next']['input_options'][ 1 ]['label'][ 'title' ]		'Next'
																	'text1' => ': ',						// if key 'text1' not exist in ['input_options'], then it text	': '
																	4 => array( 'options', $request_input_el_default['ui_wh_booking_date_next'] )					// 	'10 days'
																),
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element">' )
													, array(	 														// Default options from simple input element, like: wpbc_flex_radio()
														  'type' => 'radio'
														, 'id'       => 'ui_wh_booking_date_radio_1' 					// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Next' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 												// CSS of select element
														, 'class'    => '' 												// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 										// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''												// aria-label parameter
														, 'value'    => '4'
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '4' ) ? true : false 				// Selected or not
														, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"	// JavaScript code
														, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"	// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element">' )
													, array(
															'type' => 'select'
														  , 'attr' => array()
														  , 'name' => 'ui_wh_booking_date_next'
														  , 'id' => 'ui_wh_booking_date_next'
														  , 'options' => $dates_interval
														  , 'value' => $request_input_el_default[ 'ui_wh_booking_date_next']
														  , 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_1').prop('checked', true);"									// JavaScript code
														  , 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"	// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),

						// Prior  [ '5', '10' ]
						'prior' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'min-width: 244px;',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ) ),		//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array(    1 => array( 'label', 'title' )
																	, 'text1' => ': '
																	, 4 => array( 'options' , $request_input_el_default[ 'ui_wh_booking_date_prior'] )
																 ), 													//  1 => array( 'label', 'title' )  --> $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_booking_date_radio_2' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Prior' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '5' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '5' ) ? true : false 				// Selected or not
														, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element">' )
													, array(
															'type' => 'select'
														  , 'attr' => array()
														  , 'name' => 'ui_wh_booking_date_prior'
														  , 'id' => 'ui_wh_booking_date_prior'
														  , 'options' => $dates_interval
														  , 'value' => $request_input_el_default[ 'ui_wh_booking_date_prior']
														  , 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_2').prop('checked', true);"					// JavaScript code
														  , 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
							),

						// Fixed [ '6', '', '2022-05-21']
					    'fixed' => array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'selected_options_value' => array( 1 => array( 'value' ), 4 => array( 'value' ), 7 => array( 'value' ) ),												//  4 => array( 'value' )  			-->   $complex_option['input_options'][4]['value']
								'selected_options_title' => array( 1 => array( 'label', 'title' ), 'text1' => ': ', 4 => array( 'value' ), 'text2' => ' - ' ,7 => array( 'value' ) ),	//  1 => array( 'label', 'title' )  -->   $complex_option['input_options'][1]['label'][ 'title' ]
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex:1 1 100%;margin-top:5px;">' )
													, array(
															'type' => 'radio'

														, 'id'       => 'ui_wh_booking_date_radio_3' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_radio'
														, 'label'    => array( 'title' => __('Dates' ,'booking') , 'position' => 'right' )
														, 'style'    => '' 					// CSS of select element
														, 'class'    => '' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'legend'   => ''					// aria-label parameter
														, 'value'    => '6' 		// Some Value from optins array that selected by default
														, 'selected' => ( $request_input_el_default[ 'ui_wh_booking_date_radio' ] == '6' ) ? true : false 				// Selected or not
														, 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
														, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 4px 4px 0;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_booking_date_checkin' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_checkin'
														, 'label'    => ''//__('Check-in' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Check-in' ,'booking')
														, 'value'    => $request_input_el_default[ 'ui_wh_booking_date_checkin'] 	// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);"					// JavaScript code
														, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex-flow: row wrap;padding: 4px 0 4px 4px;">' )
													, array(
														  'type'     => 'text'
														, 'id'       => 'ui_wh_booking_date_checkout' 		// HTML ID  of element
														, 'name'     => 'ui_wh_booking_date_checkout'
														, 'label'    => ''//__('Check-out' ,'booking')
														, 'style'    => 'width:100%;' 					// CSS of select element
														, 'class'    => 'wpdevbk-filters-section-calendar' 					// CSS Class of select element
														, 'disabled' => false
														, 'attr'     => array() 			// Any  additional attributes, if this radio | checkbox element
														, 'placeholder' => __('Check-out' ,'booking')
														, 'value'    => $request_input_el_default[ 'ui_wh_booking_date_checkout']  		// Some Value from optins array that selected by default
														, 'onfocus' =>  "jQuery('#ui_wh_booking_date_radio_3').prop('checked', true);"					// JavaScript code
														, 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"					// JavaScript code
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),

						'divider3' => array( 'type' => 'html', 'html' => '<hr/>' ),

					 	// Buttons
					    'buttons1' =>  array(
								'type'  => 'complex',
								'class' => 'ui_complex_option_element',
								'style' => 'justify-content: flex-end;',
								'input_options' => array(
													  array( 'type' => 'html',  'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Apply', 'booking' )                     										// Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_apply_click( {
														  												'dropdown_id'		 : 'wh_booking_date',
														  												'dropdown_radio_name': 'ui_wh_booking_date_radio'
																									} );"				// JavaScript code
														  , 'class' => 'wpbc_ui_button_primary'     // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
													, array( 'type' => 'html', 'html' => '<div class="ui_element" style="flex: 0 1 auto;margin: 0 0 0 1em;">' )
													, array(
															'type' => 'button'
														  , 'title' => __( 'Close', 'booking' )                     // Title of the button
														  , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
														  , 'link' => 'javascript:void(0)'    // Direct link or skip  it
														  , 'action' => "wpbc_ui_dropdown_close_click( 'wh_booking_date' );"                    // JavaScript code
														  , 'class' => ''     				  // wpbc_ui_button  | wpbc_ui_button_primary
														  , 'icon' => ''
														  , 'font_icon' => ''
														  , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
														  , 'style' => ''                     // Any CSS class here
														  , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
														  , 'attr' => array()
													  )
													, array( 'type' => 'html', 'html' => '</div>' )
											)
						),
				);

    $params = array(
					  'id'      => 'wh_booking_date'
					, 'default' => $request_input_el_default[ 'wh_booking_date' ]
                    , 'label' 	=> ''//__('Approve 1', 'booking') . ':'
                    , 'title' 	=> ''//__('Approve 2', 'booking')
					, 'hint' 	=> array( 'title' => __('Filter bookings by booking dates' ,'booking') , 'position' => 'top' )
					, 'align' 	=> 'left'
					, 'li_options' => $options
                );

	?><div class="ui_element ui_nowrap"><?php

		wpbc_flex_addon( $params_addon );

		wpbc_flex_dropdown( $params );

	?></div><?php

 *
 * =====================================================================================================================
 */
function wpbc_flex_dropdown( $args = array() ) {

	// $milliseconds = round( microtime( true ) * 1000 );

    $defaults = array(
                          'id' => ''                        // HTML ID  of element					Example: 'wh_booking_date'
                        , 'default' => array()              // Selected by default value(s)			Example: 'default' 	=> array( $defaults['wh_booking_date'] , $defaults['wh_booking_date2'] )
						, 'hint'  => ''    					// Mouse over tooltip					Example: 'hint' 	=> array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
						, 'style' => '' 					// CSS style of dropdown element (optional)
						, 'class' => '' 					// CSS class of dropdown element (optional)
						, 'label' => ''                     // Label of element "at Top of element"
						, 'title' => ''                     // Title of element "Inside of element"
						, 'align' => 'left'                 // Align: left | right
                        , 'li_options'  => array()                                 // Options
                        , 'disabled' => array()                                 // If some options disabled,  then option values list here
						, 'onfocus'  => ''										// JavaScript code
						, 'onchange' => ''										// JavaScript code
						, 'is_use_for_template' => false						// In case, if we are using it for template, then we skip  JavaScript code for initial value. Need to define it manually.		// FixIn: 9.4.3.5.
                    );
    $params = wp_parse_args( $args, $defaults );

	// If default value not array,  then  define it as single value in arr.
	if ( ! is_array( $params['default'] ) ) {
		$params['default'] = array( $params['default'] );
	}

	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * Recomposition of  simple options configuration  from
	 *   	'any' => __( 'Any', 'booking' )
	 *  to
	 * 		'any' => array( 'type' => 'simple', 'value' => 'any', 'title' => __( 'Any', 'booking' ) );
	 */
    $is_this_simple_list =  true;
    foreach ( $params['li_options'] as $key_value => $option_data ) {

		if ( ! is_array( $option_data ) ) {

            $params['li_options'][ $key_value ] = array( 'type' => 'simple', 'value' => (string) $key_value, 'title' => $option_data );

        } else {
			if ( ( isset( $option_data['type'] ) ) && ( 'complex' == $option_data['type'] ) ) {
				$is_this_simple_list = false;
			}
		}
    }
	// -----------------------------------------------------------------------------------------------------------------


	// -----------------------------------------------------------------------------------------------------------------
    // Rechecking about selected LI option,  based on $params['default'] like  ['4','10']  and getting title of such  option
	// -----------------------------------------------------------------------------------------------------------------
	$default_selected_title = array();
	foreach ( $params['li_options'] as $li_option ) {

		if ( 'simple' == $li_option['type'] ) {
			if ( $li_option['value'] === $params['default'][0] ) {
				$default_selected_title = $li_option['title'] ;
			}
		}

		if ( 'complex' == $li_option['type'] ) {

			// $option[ 'selected_options_value' ] => array( 1 => array( 'value' ), 4 => array( 'value' ) ),
			// $option[ 'selected_options_title' ] => array( 1 => array( 'label', 'title' ), 'text1' => ': ', 4 => array( 'value' ) ),

			// Get value of this LI
			$li_this_value = array();
			if ( isset( $li_option['selected_options_value'] ) )
				foreach ( $li_option['selected_options_value'] as $li_key => $input_keys ) {

					if ( isset( $li_option['input_options'][ $li_key ] ) ) {									// ['input_options'][4]

						$li_input_deep_value = $li_option['input_options'][ $li_key ];

						foreach ( $input_keys as $input_key_value ) {

							if ( isset( $li_input_deep_value[$input_key_value] ) ) {				// ['input_options'][4]['value']
								$li_input_deep_value = $li_input_deep_value[ $input_key_value ];
							}
						}
						$li_this_value[] = $li_input_deep_value;
					}
				}

			// Is this LI selected ?
			$is_same_value = array_diff( $params['default'], $li_this_value ) == array();

			if ( $is_same_value ) {

				// Get value of this LI
				$li_this_value = array();
				foreach ( $li_option['selected_options_title'] as $li_key => $input_keys ) {

					if ( isset( $li_option['input_options'][ $li_key ] ) ) {									// ['input_options'][4]

						$li_input_deep_value = $li_option['input_options'][ $li_key ];

						foreach ( $input_keys as $input_key_value ) {

							if ( isset( $li_input_deep_value[$input_key_value] ) ) {						// ['input_options'][4]['value']
								$li_input_deep_value = $li_input_deep_value[ $input_key_value ];
							}
						}
						$li_this_value[] = $li_input_deep_value;
					} else {
						$li_this_value[] = $input_keys; //some text
					}
				}

				$default_selected_title = implode( '', $li_this_value );
			}
		}

		if ( ! empty( $default_selected_title ) ) {
			break;
		}
	}

	if ( is_array( $default_selected_title ) ) {
		$default_selected_title = implode( '', $default_selected_title );		// Error::   ? no values ?
	}


    // ---------------------------------------------------------------------------------------------------------------------////////////////////////////////////
	// Show only, 		if 		$item_params['label']   	! EMPTY
	wpbc_flex_label(
						array(
							  'id' 	  => $params['id']
							, 'label' => $params['label']
							, 'class' => 'wpbc_ui_dropdown__outside_label'
						)
				   );

    ?><div    class="wpbc_ui_dropdown <?php echo esc_attr( $params['class'] ); ?>"
              style="<?php echo esc_attr( $params['style'] ); ?>"
		><?php

			?><a 	 href="javascript:void(0)"
					 id="<?php echo esc_attr( $params['id'] ); ?>_selector"
					 data-toggle="wpbc_dropdown"
					 class="wpbc_ui_control wpbc_ui_button dropdown-toggle <?php
							echo ( ! empty( $params['hint'] ) ) ? 'tooltip_' . esc_attr( $params['hint']['position'] ) . ' ' : '' ;
							?>"
					 <?php if (! $is_this_simple_list ) { ?>
					 	onclick="javascript:jQuery('#<?php echo esc_attr( $params['id'] ); ?>_container').show();"
					 <?php } ?>
					  <?php if ( ! empty( $params['hint'] ) ) { ?>
					 	title="<?php  echo esc_attr( $params['hint']['title'] ); ?>"
					  <?php } ?>
					 <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $params ); ?>
			><?php

				  	?><label class="wpbc_ui_dropdown__inside_label" <?php if ( empty( $params['title'] ) )  { echo ' style="display:none;" '; } ?> ><?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo html_entity_decode(
											  wp_kses_post( $params['title'] )			// Sanitizes content for allowed HTML tags for post content
											, ENT_QUOTES								// Convert &quot;  to " and '
											, get_bloginfo( 'charset' ) 				// 'UTF-8'  or other
										);												// Convert &amp;dash;  to &dash;  etc...
						  ?>: <?php
				    ?></label> <?php

				  	?><span class="wpbc_selected_in_dropdown" ><?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo html_entity_decode(
											  wp_kses_post( $default_selected_title )	// Sanitizes content for allowed HTML tags for post content
											, ENT_QUOTES								// Convert &quot;  to " and '
											, get_bloginfo( 'charset' ) 				// 'UTF-8'  or other
										);												// Convert &amp;dash;  to &dash;  etc...
				    ?></span> &nbsp; <?php

				    ?><span class="wpbc_ui_dropdown__inside_caret"></span><?php

			?></a><?php

			?><ul id="<?php echo esc_attr( $params['id'] ); ?>_container" 	class="ui_dropdown_menu ui_dropdown_menu-<?php echo esc_attr( $params['align'] ); ?>" ><?php

						wpbc_flex_dropdown__options( $params, array( 'is_this_simple_list' => $is_this_simple_list ) );

			?></ul><?php

			?><input type="hidden"
					 autocomplete="off"
					 value=""
					 id="<?php 		echo esc_attr( $params['id'] ); ?>"
					 name="<?php 	echo esc_attr( $params['id'] ); ?>"
				/><?php
		if( ! $params['is_use_for_template'] ){                                                                         // FixIn: 9.4.3.5.
			?>
			<script type="text/javascript">
				<?php /* document.getElementById("<?php echo esc_attr( $params['id'] ); ?>").value = "<?php echo wp_slash( wp_json_encode($params['default']  ) ); ?>";  */ ?>
				jQuery(document).ready(function(){

					jQuery( '#<?php echo esc_attr( $params['id'] ); ?>').val( "<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wp_slash( wp_json_encode( $params['default'] ) ); ?>" );

					<?php if (! empty( $params['onchange'] )) { ?>

						jQuery( '#<?php echo esc_attr( $params['id'] ); ?>' ).on( 'change', function( event ){

							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $params['onchange']; ?>
						});

					<?php } ?>

					<?php if (! empty( $params['onfocus'] )) { ?>

						jQuery( '#<?php echo esc_attr( $params['id'] ); ?>_selector' ).on( 'focus', function( event ){

							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $params['onfocus']; ?>
						});

					<?php } ?>
				})
			</script>
			<?php
		}
	?></div><?php
}

/**
 * Options list  for Dropdown
 *
 * @param       $params
 * @param array $args
 */
function wpbc_flex_dropdown__options( $params, $args = array() ) {

	$defaults = array(
		// 'milliseconds'        => round( microtime( true ) * 1000 ),
		'is_this_simple_list' => true
	);
	$args   = wp_parse_args( $args, $defaults );


	foreach ( $params['li_options'] as $option_name => $li_option ) {

		$default_option = array(
								  'type'  => ''
								, 'class' => ''
								, 'style' => ''
								, 'title' => ''
								, 'disabled' => false
								, 'attr'     => array()
								, 'value'    => ''
								, 'html'     => ''
							);
		$li_option = wp_parse_args( $li_option, $default_option );


		// Is disabled ?
		if ( true === in_array( $li_option['value'], $params['disabled'] ) ) {
			$li_option['disabled'] = true;
		}
		if ( $li_option['disabled'] ) {
			$li_option['class'] .= ' disabled';
		}
		// Is header ?
		if ( 'header' == $li_option['type'] ) {
			$li_option['class'] .= ' dropdown-header';
		}


		?><li role="presentation"
			  class="<?php echo esc_attr( $li_option['class'] ); ?>"
			  style="<?php echo esc_attr( $li_option['style'] ); ?>"
			  <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $li_option ); ?>
		><?php

			switch ( $li_option['type'] ) {

				case 'simple':

					?><a  role="menuitem"
						  tabindex="-1"
		  			<?php if ( ! $li_option['disabled'] ) {

								if( false !== filter_var( $li_option['value'], FILTER_VALIDATE_URL ) ){ ?>

											href="<?php
											// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											echo $li_option['value']; ?>"

								<?php } else { ?>

											href="javascript:void(0)"
											onclick="javascript: wpbc_ui_dropdown_simple_click( {
																								  'dropdown_id'        : '<?php echo esc_attr( $params['id'] ); ?>'
																								, 'is_this_simple_list':  <?php echo esc_attr( ( $args['is_this_simple_list'] ) ? 'true' : 'false' ); ?>
																								, 'value'              : '<?php
											// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											echo $li_option['value']; ?>'
																								, '_this'              : this
																							} );"
								<?php }

					} ?>
					   ><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $li_option['title'];

					?></a><?php

					break;

				case 'html':
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $li_option['html'];
					break;

				case 'header':
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $li_option['title'];
					break;

				case 'complex' :

					foreach ( $li_option['input_options'] as $input_option ) {

						switch ( $input_option['type'] ) {

							case 'html':
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo $input_option['html'];
								break;

							case 'button':
								wpbc_flex_button( $input_option );
								break;

							case 'label':
								wpbc_flex_label( $input_option );
								break;

							case 'text':
								wpbc_flex_text( $input_option );
								break;

							case 'select':
								wpbc_flex_select( $input_option );
								break;

							case 'checkbox':
								wpbc_flex_checkbox( $input_option );
								break;

							case 'radio':
								wpbc_flex_radio( $input_option );
								break;

							case 'addon':
								wpbc_flex_addon( $input_option );
								break;

							default: // Default
						}
					}
					break;

				default: // Default
			}

		?></li><?php
	}
}


// ---------------------------------------------------------------------------------------------------------------------
//   < >  Buttons
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Button - Select Prior Skin in select-box
 * @return void
 */
function wpbc_smpl_form__ui__selectbox_prior_btn( $dropdown_id, $is_apply_rotating_icon = true ){

	$params_button = array(
			  'type' => 'button'
			, 'title' => ''	                 																			// Title of the button
			// , 'hint'  => array( 'title' => __('Previous' ,'booking') , 'position' => 'top' )
			, 'link' => 'javascript:void(0)'    																		// Direct link or skip  it
			, 'action' => // "console.log( 'ON CLICK:', jQuery( '[name=\"set_days_customize_plugin\"]:checked' ).val() , jQuery( 'textarea[id^=\"date_booking\"]' ).val() );"                    // Some JavaScript to execure, for example run  the function
						  " var is_selected = jQuery( '#" . $dropdown_id . " option:selected' ).prev(); "
//						  . " if ( is_selected.length == 0 ){ "															//FixIn: 10.7.1.5.1
//						  . "    if (  ( 'optgroup' == jQuery( '#" . $dropdown_id . " option:selected' ).parent().prop('nodeName').toLowerCase() ) "
//						  . "       && ( jQuery( '#" . $dropdown_id . " option:selected' ).parent().prev().length )  "
//						  . "       && ( 'optgroup' == jQuery( '#" . $dropdown_id . " option:selected' ).parent().prev().prop('nodeName').toLowerCase() )   ){ "
//						  . "         is_selected = jQuery( '#" . $dropdown_id . " option:selected' ).parent().prev().find('option').last(); "
//						  . "    } "
//						  . " } "
						  . " jQuery( '#" . $dropdown_id . " option:selected' ).prop('selected', false); "
						  . " if ( is_selected.length == 0 ){ "
//						  . "    is_selected = jQuery( '#" . $dropdown_id . " option' ).last(); "													//FixIn: 10.7.1.5.1
						  . "    is_selected = jQuery( '#" . $dropdown_id . " option:selected' ).parent().find('option').last(); "					//FixIn: 10.7.1.5.1
						  . " } "
						  . " if ( is_selected.length > 0 ){ "
						  .	"    is_selected.prop('selected', true).trigger('change'); "
						  . 	 ( ( $is_apply_rotating_icon ) ? "		wpbc_button_enable_loading_icon( this ); " : "" )
						  . " } else { "
						  . "    jQuery( this ).addClass( 'disabled' ); "
						  . " } "
			, 'class' => 'wpbc_ui_button'     				  															// wpbc_ui_button  | wpbc_ui_button_primary
			//, 'icon_position' => 'left'         																		// Position  of icon relative to Text: left | right
			, 'icon' 			   => array(
										'icon_font' => 'wpbc_icn_arrow_back_ios', 										// 'wpbc_icn_check_circle_outline',
										'position'  => 'left',
										'icon_img'  => ''
									)
			, 'style' => ''                     																		// Any CSS class here
			, 'mobile_show_text' => false       																		// Show  or hide text,  when viewing on Mobile devices (small window size).
			, 'attr' => array()
	);

	wpbc_flex_button( $params_button );
}

/**
 * Button - Select Next Skin in select-box
 * @return void
 */
function wpbc_smpl_form__ui__selectbox_next_btn( $dropdown_id, $is_apply_rotating_icon = true ){

	$params_button = array(
			  'type' => 'button'
			, 'title' => ''	                 // Title of the button
			// , 'hint'  => array( 'title' => __('Next' ,'booking') , 'position' => 'top' )
			, 'link' => 'javascript:void(0)'    // Direct link or skip  it
			, 'action' => //"console.log( 'ON CLICK:', jQuery( '[name=\"set_days_customize_plugin\"]:checked' ).val() , jQuery( 'textarea[id^=\"date_booking\"]' ).val() );"                    // Some JavaScript to execure, for example run  the function
						  " var is_selected = jQuery( '#" . $dropdown_id . " option:selected' ).next(); "
//						  . " if ( is_selected.length == 0 ){ "															//FixIn: 10.7.1.5.1
//						  . "    if (  ( 'optgroup' == jQuery( '#" . $dropdown_id . " option:selected' ).parent().prop('nodeName').toLowerCase() ) "
//						  . "       && ( jQuery( '#" . $dropdown_id . " option:selected' ).parent().next().length )  "
//						  . "       && ( 'optgroup' == jQuery( '#" . $dropdown_id . " option:selected' ).parent().next().prop('nodeName').toLowerCase() )   ){ "
//						  . "         is_selected = jQuery( '#" . $dropdown_id . " option:selected' ).parent().next().find('option').first(); "
//						  . "    } "
//						  . " } "
						  . " jQuery( '#" . $dropdown_id . " option:selected' ).prop('selected', false); "
						  . " if ( is_selected.length == 0 ){ "
						  . "    is_selected = jQuery( '#" . $dropdown_id . " option' ).first(); "
						  . " } "
						  . " if ( is_selected.length > 0 ){ "
						  .	"    is_selected.prop('selected', true).trigger('change'); "
						  . 	 ( ( $is_apply_rotating_icon ) ? "		wpbc_button_enable_loading_icon( this ); " : "" )
						  . " } else { "
						  . "    jQuery( this ).addClass( 'disabled' ); "
						  . " } "
			, 'class' => 'wpbc_ui_button'     				  // wpbc_ui_button  | wpbc_ui_button_primary
			//, 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
			, 'icon' 			   => array(
										'icon_font' => 'wpbc_icn_arrow_forward_ios', // 'wpbc_icn_check_circle_outline',
										'position'  => 'right',
										'icon_img'  => ''
									)
			, 'style' => ''                     // Any CSS class here
			, 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
			, 'attr' => array()
	);

	wpbc_flex_button( $params_button );
}

// ---------------------------------------------------------------------------------------------------------------------
//  Radio Containers
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Show FLEX    == Radio Container    ==
 *
 * @param array $item
 *
 *  Example:
 *                $params_radio = array(
 *                                          'id'       => 'wpbc_swp_booking_types__changeover_multi_dates_bookings'                                // HTML ID  of element
 *                                        , 'name'     => 'wpbc_swp_booking_types'
 *                                        , 'value'    => 'changeover_multi_dates_bookings'                                                        // Some Value from optins array that selected by default
 *                                        , 'label'    => array( 'title' => __('Changeover multi dates bookings','booking') , 'position' => 'right', 'class' => 'wpbc_ui_radio_choice_title' )
 *                                        , 'text_description'  => __('Receive and manage bookings for chosen times on selected date(s). Time-slots selection in booking form.','booking')
 *                                        , 'label_after_right' => '<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank"><strong class="wpbc_ui_radio_text_right">Pro</strong></a>'
 *                                        , 'footer_text'      => sprintf( __( '...', 'booking' ), '<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank">','</a>')
 *                                        , 'style'    => ''                                                                                // CSS of select element
 *                                        , 'class'    => ''                                                                                // CSS Class of select element
 *                                        , 'disabled' => !false
 *                                        , 'attr'     => array()                                                                        // Any  additional attributes, if this radio | checkbox element
 *                                        , 'legend'   => ''                                                                                // aria-label parameter
 *                                        , 'selected' => false                                                                            // Selected or not
 *                                        , 'onfocus' =>  "console.log( 'ON FOCUS:',  jQuery( this ).is(':checked') , 'in element:' , jQuery( this ) );"                    // JavaScript code
 *                                        , 'onchange' => "console.log( 'ON CHANGE:', jQuery( this ).val() , 'in element:' , jQuery( this ) );"                            // JavaScript code
 *                                    );
 *				  ?><div class="wpbc_ui_radio_section wpbc_ui_radio_section_as_row"><?php
 *                    wpbc_flex_radio_container( $params_radio );
 *                ?></div><?php
 *
 *
 *  It generate this HTML Content:
 *						<div class="wpbc_ui_radio_container"   >
 *							<div class="wpbc_ui_radio_choice">
 *								<input 	class="wpbc_ui_radio_choice_input"
 *									   type="radio"
 *									   disabled="disabled"
 *									   name="wpbc_swp_booking_types"
 *									     id="wpbc_swp_booking_types__changeover_multi_dates_bookings"
 *									   						  value="changeover_multi_dates_bookings"
 *								/>
 *								<label for="wpbc_swp_booking_types__changeover_multi_dates_bookings" class="wpbc_ui_radio_choice_title"><?php esc_html_e('Changeover multi dates bookings','booking'); ?></label>
 *								<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank"><strong class="wpbc_ui_radio_text_right">Pro</strong></a>
 *								<p class="wpbc_ui_radio_choice_description"><?php esc_html_e('Manage multidays bookings with changeover days for check in/out dates, marked with diagonal or vertical lines. Split days bookings.','booking'); ?></p>
 *							</div>
 *							<div class="wpbc_ui_radio_choice wpbc_ui_radio_footer">
 *								<p class="wpbc_ui_radio_choice_description"><?php echo wp_kses_post( sprintf( __( ',,,', 'booking' ),
 *									'<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank">','</a>')  ); ?></p>
 *							</div>
 *						</div>
 *
 *  CSS located in: ../includes/__css/admin/ui_el__radio_container.css
 */
function wpbc_flex_radio_container( $args = array() ) {

	// $milliseconds = round( microtime( true ) * 1000 );

    $defaults = array(
						  'type' => 'checkbox'
						, 'id' => ''                        // HTML ID  of element
						, 'name' => ''
						, 'label' => ''      				// Label	Example: 'label' => array( 'title' => __('Select status' ,'booking') , 'position' => 'left' )
						, 'style' => ''                     // CSS of select element
						, 'class' => ''                     // CSS Class of select element
						, 'disabled' => false
						, 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
						, 'legend' => ''                    // aria-label parameter
						, 'value' => ''                     // Some Value from options array that selected by default
						, 'selected' => false               // Selected or not
						, 'onfocus' => ''					// JavaScript code
						, 'onchange' => ''					// JavaScript code
						, 'is_use_toggle' => false          // Show checkbox  as toggle
						, 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )

						// Text at Right side of Label, e.g.	'Pro'
						, 'label_after_right' => ''			// '<a tabindex="-1" href="https://wpbookingcalendar.com/features/#change-over-days" target="_blank"><strong class="wpbc_ui_radio_text_right">Pro</strong></a>'

						// Text at Right side of Label, e.g.	'Pro'
						, 'text_description' => ''			// __('Receive and manage bookings for chosen times on selected date(s). Time-slots selection in booking form.','booking')

						// Footer Text  separated by line
						, 'footer_text' => ''
						, 'bottom_html' => ''
                    );
    $params = wp_parse_args( $args, $defaults );


	$params['type']          = 'radio';
	$params['is_use_toggle'] = false;
	if ( ( ! empty( $params['label'] ) ) && ( empty( $params['label']['position'] ) ) ) {
		$params['label']['position'] = 'right';
		$params['label']['class'] = 'wpbc_ui_radio_choice_title';
	}

	$params['class'] .= ' wpbc_ui_radio_choice_input';


	// Should start  with <div class="wpbc_ui_radio_section wpbc_ui_radio_section_as_row">  | <div class="wpbc_ui_radio_section">

	?>
	<div class="wpbc_ui_radio_container">
		<div class="wpbc_ui_radio_choice">
			<?php

				wpbc_flex_checkbox( $params );

				if ( ! empty( $params['label_after_right'] ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $params['label_after_right'];
				}

				if ( ! empty( $params['text_description'] ) ) {
					?><p class="wpbc_ui_radio_choice_description"><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $params['text_description'];
					?></p><?php
				}

			?>
		</div><?php

		if ( ! empty( $params['footer_text'] ) ) {
			?><div class="wpbc_ui_radio_choice wpbc_ui_radio_footer">
				<p class="wpbc_ui_radio_choice_description"><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $params['footer_text'];
				?></p>
			</div><?php
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $params['bottom_html'];
		?>
	</div>
	<?php

	// Should end with </div> .
}
