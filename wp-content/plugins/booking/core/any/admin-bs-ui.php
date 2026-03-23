<?php /**
 * @version 1.1
 * @package Any
 * @category Toolbar. BS UI Elements for Admin Panel.
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-14
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


////////////////////////////////////////////////////////////////////////////////
//  Single  elements      //////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
	 * Show BS Button
 * 
 * @param array $item
                        array( 
                                'type' => 'button' 
                              , 'title' => ''                     // Title of the button
                              , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                              , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                              , 'action' => ''                    // Some JavaScript to execure, for example run  the function
                              , 'class' => 'button-secondary'     // button-secondary  | button-primary
                              , 'icon' => ''
                              , 'font_icon' => ''
                              , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                              , 'style' => ''                     // Any CSS class here
                              , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                              , 'attr' => array()
                        ); 
 */
function wpbc_bs_button( $item ) {
    
    $default_item_params = array( 
                                'type' => 'button' 
                              , 'title' => ''                     // Title of the button
                              , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                              , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                              , 'action' => ''                    // Some JavaScript to execure, for example run  the function
                              , 'class' => 'button-secondary'     // button-secondary  | button-primary
                              , 'icon' => ''
                              , 'font_icon' => ''
                              , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                              , 'style' => ''                     // Any CSS class here
                              , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                              , 'attr' => array()
                        ); 
    $item_params = wp_parse_args( $item, $default_item_params );

    ?><a  class="button <?php 
                              echo ( ! empty( $item_params['hint'] ) ) ? ' tooltip_' . esc_attr( $item_params['hint']['position'] ) . ' ' : '' ;
                              echo esc_attr( $item_params['class'] );
              ?>" style="<?php echo esc_attr( $item_params['style'] ); ?>"
              <?php if ( ! empty( $item_params['hint'] ) ) { ?>
                  title="<?php  echo esc_attr( $item_params['hint']['title'] ); ?>"
              <?php } ?>                 
              href="<?php echo esc_attr( $item_params['link'] ); ?>"
          <?php if ( ! empty( $item_params['action'] ) ) { ?>
              onclick="javascript:<?php
              // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              echo $item_params['action']; ?>"
          <?php } ?>  
          <?php
          // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          echo wpbc_get_custom_attr( $item_params );
		  ?>
        ><?php
              $btn_icon = '';
              if ( ! empty( $item_params['icon'] ) ) {                                

                  if ( substr( $item_params['icon'], 0, 4 ) != 'http') {$img_path = WPBC_PLUGIN_URL . '/assets/img/' . $item_params['icon'];}
                  else                                                 {$img_path = $item_params['icon'];}
				  // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                  $btn_icon = '<img class="menuicons" src="' . $img_path . '" />';    // Img  Icon.

              } elseif ( ! empty( $item_params['font_icon'] ) ) {                     

                  $btn_icon = '<i class="menu_icon icon-1x ' . $item_params['font_icon'] . '"></i>';                         // Font Icon.

              }  
              if (  ( ! empty( $btn_icon ) ) && ( $item_params['icon_position'] == 'left' )  ){
				  echo wp_kses_post( $btn_icon );
				  if (! empty($item_params['title'])) {
					  echo '&nbsp;';
				  }
              }
              // Text
              echo '<span' . ( (  ( ! empty( $btn_icon ) )  && ( ! $item_params['mobile_show_text'] ) )? ' class="in-button-text"' : '' ) . '>';
              echo wp_kses_post( $item_params['title'] );
              if (  ( ! empty( $btn_icon ) ) && ( $item_params['icon_position'] == 'right' )  )
                  echo '&nbsp;' ;
              echo '</span>';

              if (  ( ! empty( $btn_icon ) ) && ( $item_params['icon_position'] == 'right' )  )
                  echo   wp_kses_post( $btn_icon );
    ?></a><?php    
}


/**
	 * Show BS text
 * 
 * @param array $item
                        array( 
                            'id' => ''                  // HTML ID  of element                                                          
                          , 'value' => ''                     
                          , 'placeholder' => ''
                          , 'style' => ''             // CSS of select element
                          , 'class' => ''             // CSS Class of select element
                          , 'attr' => array()         // Any  additional attributes, if this radio | checkbox element 
                        ); 
 */
function wpbc_bs_text( $item ) {

    $default_item_params = array(
                                'type'          => 'text' 
                                , 'id'          => ''  
                                , 'name'        => ''  
                                , 'label'       => ''  
                                , 'disabled'    => false
                                , 'class'       => ''
                                , 'style'       => ''
                                , 'placeholder' => ''                                                                                                                                    
                                , 'attr'        => array()
                                , 'value' => ''
                                , 'onfocus' => ''
    );
    $item_params = wp_parse_args( $item, $default_item_params );

    ?><input  type="<?php echo esc_attr( $item_params['type'] ); ?>" 
              id="<?php echo esc_attr( $item_params['id'] ); ?>"
              name="<?php echo esc_attr( $item_params['id'] ); ?>"
              style="<?php echo esc_attr( $item_params['style'] ); ?>"
              class="form-control <?php echo esc_attr( $item_params['class'] ); ?>"
              placeholder="<?php echo esc_attr( $item_params['placeholder'] ); ?>"
              value="<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $item_params['value']; ?>"
              <?php disabled( $item_params['disabled'], true ); ?>               
              <?php
				  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				  echo wpbc_get_custom_attr( $item_params );
			  ?>
              <?php 
                if ( ! empty( $item_params['onfocus'] ) ) {
                    ?> onfocus="javascript:<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $item_params['onfocus']; ?>" <?php
                }
              ?>
          /><?php    
}


/**
	 * Show BS selectbox
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
                                , 'onfocus' => ''
                                , 'onchange' => ''
                        )
 */
function wpbc_bs_select( $item ) {
    
    $default_item_params = array(         
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
                                , 'onfocus' => ''
                                , 'onchange' => ''
                        ); 
    $item_params = wp_parse_args( $item, $default_item_params );

    
    ?><select  
            id="<?php echo esc_attr( $item_params['id'] ); ?>" 
            name="<?php echo esc_attr( $item_params['name'] ); echo ( $item_params['multiple'] ? '[]' : '' ); ?>" 
            class="form-control <?php echo esc_attr( $item_params['class'] ); ?>"                                 
            style="<?php echo esc_attr( $item_params['style'] ); ?>" 
            <?php disabled( $item_params['disabled'], true ); ?> 
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo wpbc_get_custom_attr( $item_params );
			?>
            <?php echo ( $item_params['multiple'] ? ' multiple="MULTIPLE"' : '' ); ?>                                                                                                                                      
            <?php 
                if ( ! empty( $item_params['onfocus'] ) ) {
                    ?> onfocus="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $item_params['onfocus']; ?>" <?php
                } 
                if ( ! empty( $item_params['onchange'] ) ) {
                    ?> onchange="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $item_params['onchange']; ?>" <?php
                } 
            ?>
            autocomplete="off"
        ><?php
        foreach ( $item_params['options'] as $option_value => $option_data ) {

            if ( ! is_array( $option_data ) ) {
                $option_data = array( 'title' => $option_data );
                $is_was_simple = true;
            } else $is_was_simple = false;
            
            $default_option_params = array(         
                                          'id' => ''                            // HTML ID  of element
                                        , 'name' => ''  
                                        , 'style' => ''                         // CSS of select element
                                        , 'class' => ''                         // CSS Class of select element
                                        , 'disabled' => false
                                        , 'selected' => false
                                        , 'attr' => array()                     // Any  additional attributes, if this radio | checkbox element                                                   
                                        , 'value' => ''                         // Some Value from optins array that selected by default                                              
                
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
                        ?>        
                    class="<?php echo esc_attr( $option_data['class'] ); ?>"
                    style="<?php echo esc_attr( $option_data['style'] ); ?>" 
                    <?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo wpbc_get_custom_attr( $option_data );
					?>
                    <?php selected(  $option_data['selected'], true ); ?>                                 
                    <?php if ( ! empty( $item_params['value'] ) ) selected(  $item_params['value'], esc_attr( $option_value ) ); //Recheck  global  selected parameter ?>
                    <?php disabled( $option_data['disabled'], true ); ?> 
                
                ><?php echo esc_attr( $option_data['title'] ); ?></option><?php
            }
        }
    ?></select><?php    
}


/**
	 * Show BS checkbox
 * 
 * @param array $item
                        array( 
                                'type' => 'radio'    
                                , 'id' => ''                        // HTML ID  of element
                                , 'name' => ''
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element                                                                                
                                , 'disabled' => false
                                , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                , 'legend' => ''                    // aria-label parameter 
                                , 'value' => ''                     // Some Value from optins array that selected by default                                      
                                , 'selected' => false               // Selected or not                                
                        ); 
 */
function wpbc_bs_radio( $item ) {
    $item['type'] = 'radio';
    wpbc_bs_checkbox( $item );
}


/**
	 * Show BS checkbox
 * 
 * @param array $item
                        array( 
                                'type' => 'checkbox'    
                                , 'id' => ''                        // HTML ID  of element
                                , 'name' => ''
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element                                                                                
                                , 'disabled' => false
                                , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                , 'legend' => ''                    // aria-label parameter 
                                , 'value' => ''                     // Some Value from optins array that selected by default                                      
                                , 'selected' => false               // Selected or not                                
                        ); 
 */
function wpbc_bs_checkbox( $item ) {

    $default_item_params = array( 
                                'type' => 'checkbox'    
                                , 'id' => ''                        // HTML ID  of element
                                , 'name' => ''
                                , 'style' => ''                     // CSS of select element
                                , 'class' => ''                     // CSS Class of select element                                                                                
                                , 'disabled' => false
                                , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                                , 'legend' => ''                    // aria-label parameter 
                                , 'value' => ''                     // Some Value from optins array that selected by default                                      
                                , 'selected' => false               // Selected or not                                
                      ); 
    $item_params = wp_parse_args( $item, $default_item_params );

    ?><input    type="<?php echo esc_attr( $item_params['type'] ); ?>"
                id="<?php echo esc_attr( $item_params['id'] ); ?>" 
                name="<?php echo esc_attr( $item_params['name'] ); ?>" 
                value="<?php echo esc_attr( $item_params['value'] ); ?>" 
                aria-label="<?php echo esc_attr( $item_params['legend'] ); ?>"
                class="<?php echo esc_attr( $item_params['class'] ); ?>"
                style="<?php echo esc_attr( $item_params['style'] ); ?>" 
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo wpbc_get_custom_attr( $item_params );
				?>
                <?php checked(  $item_params['selected'], true ); ?>                                 
                <?php disabled( $item_params['disabled'], true ); ?> 
        /><?php 
}


/**
	 * Show BS addon
 * 
 * @param array $item
                        array( 
                            'type' => 'addon' 
                          , 'element' => 'text'               // text | radio | checkbox
                          , 'text' => ''                      // Simple plain text showing
                          , 'id' => ''                        // ID, if this radio | checkbox element
                          , 'name' => ''                      // Name, if this radio | checkbox element
                          , 'value' => ''                     // value, if this radio | checkbox element
                          , 'selected' => false               // Selected, if this radio | checkbox element     
                          , 'legend' => ''                    // aria-label parameter , if this radio | checkbox element
                          , 'class' => ''                     // Any CSS class here
                          , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                          , 'text' => ''                      // Text  tp  show for Text  element
                        )
*/
function wpbc_bs_addon( $item ) {
    $milliseconds = round( microtime( true ) * 1000 );    // FixIn: 8.8.2.1.
    $default_item_params = array( 
                            'type' => 'addon' 
                          , 'element' => 'text'               // text | radio | checkbox
                          , 'text' => ''                      // Simple plain text showing
                          , 'id' => ''                        // ID, if this radio | checkbox element
                          , 'name' => ''                      // Name, if this radio | checkbox element
                          , 'value' => ''                     // value, if this radio | checkbox element
                          , 'selected' => false               // Selected, if this radio | checkbox element     
                          , 'legend' => ''                    // aria-label parameter , if this radio | checkbox element
                          , 'class' => ''                     // Any CSS class here
                          , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element 
                          , 'text' => ''                      // Text  tp  show for Text  element
                          , 'style' => ''
                        ); 
    $item_params = wp_parse_args( $item, $default_item_params );

    ?><span class="input-group-addon"><?php
    if ( $item_params['element'] == 'text' ) {
        ?><span class="<?php echo esc_attr( $item_params['class'] ); ?>" 
                style="<?php echo esc_attr( $item_params['style'] ); ?>"><?php
              echo esc_html( $item_params['text'] );
        ?></span><?php
    } elseif ( $item_params['element'] == 'checkbox' ) {

        if ( empty( $item_params['id'] ) )
          $item_params['id'] = $item_params['element'] . '_grp_' . $milliseconds ;

        if ( empty( $item_params['name'] ) )
          $item_params['name'] = $item_params['id'];

        ?><input type="checkbox" 
                 id="<?php echo esc_attr( $item_params['id'] ); ?>"
                 name="<?php echo esc_attr( $item_params['name'] ); ?>"
                 value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $item_params['value']; ?>"
                 aria-label="<?php echo esc_attr( $item_params['legend'] ); ?>"
                 class="<?php echo esc_attr( $item_params['class'] ); ?>"
                 <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
                 <?php if ( $item_params['selected'] ) { echo ' checked="CHECKED" '; } ?>
          /><?php    

    } elseif ( $item_params['element'] == 'radio' ) {

        if ( empty( $item_params['id'] ) )
          $item_params['id'] = $item_params['element'] . '_grp_' . $milliseconds ;

        if ( empty( $item_params['name'] ) )
          $item_params['name'] = $item_params['id'];

        ?><input type="radio" 
                 id="<?php echo esc_attr( $item_params['id'] ); ?>"
                 name="<?php echo esc_attr( $item_params['name'] ); ?>"
                 value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $item_params['value']; ?>"
                 aria-label="<?php echo esc_attr( $item_params['legend'] ); ?>"
                 class="<?php echo esc_attr( $item_params['class'] ); ?>"
                 <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $item_params ); ?>
                 <?php if ( $item_params['selected'] ) { echo ' checked="CHECKED" '; } ?>
          /><?php                                          
    }
    ?></span><?php
    
}


/**
	 * Show BS Dropdown
 * 
 * @param array $item
                        array( 
 
                        ); 
 */
function wpbc_bs_dropdown( $item ) {
        
    $default_item_params = array( 
                          'id' => ''                                            // HTML ID  of element
                        , 'default' => ''                                       // Some Value from optins array that selected by default
                        , 'hint' => ''                                          // Hint // array( 'title' => __('Delete booking' ,'booking') , 'position' => 'bottom' ) 
                        , 'class' => 'button-secondary'        
                        , 'style' => ''                                         // Any CSS style
                        , 'label' => ''                                         // Label of element "at Top of element"
                        , 'title' => ''                                         // Title of element "Inside of element"
                        , 'align' => 'left'                                     // Align: left | right                                               
                        , 'attr' => array()                              
                        , 'icon' => ''
                        , 'font_icon' => ''
                        , 'icon_position' => 'left'                             // Position  of icon relative to Text: left | right
                        , 'mobile_show_text' => false                           // Show  or hide text,  when viewing on Mobile devices (small window size).
                        , 'disabled' => array()                                 // If some options disbaled,  then its must list  here
                        , 'options' => array()                                  // Associated array  of titles and values         
                        ); 
    $params = wp_parse_args( $item, $default_item_params );

    
    $milliseconds = round(microtime(true) * 1000);
    
    if ( empty( $params['id'] ) ) $params['id'] = 'wpbc_bs_dropdown' . $milliseconds;
    
    // Check  if this list Simple or Complex (with  input elements) ////////////
    $is_this_simple_list =  true;		      					//FixIn: 9.0.1.1.1
    foreach ( $params['options'] as $key => $value ) {
        if ( is_array( $value ) ) {                                             
            $is_this_simple_list = false;
            break;
        }    
    }    
    
    ?><a href="javascript:void(0)" 
         <?php if (! $is_this_simple_list ) { ?>
         onclick="javascript:jQuery('#<?php echo esc_js( $params['id'] ); ?>_container').show();"
         <?php } ?>
         data-toggle="wpbc_dropdown" 
         id="<?php echo esc_attr( $params['id'] );?>_selector"
          <?php if ( ! empty( $params['hint'] ) ) { ?>
         title="<?php  echo esc_js( $params['hint']['title'] ); ?>"
          <?php } ?>                 
         class="button dropdown-toggle <?php 
                echo esc_attr( ( ! empty( $params['hint'] ) ) ? 'tooltip_' . $params['hint']['position'] . ' ' : '' );
                echo esc_attr( ( ! empty( $params['class'] ) ) ? $params['class'] . ' ' : '' );
                ?>"   
         style="<?php echo esc_attr( $params['style'] ); ?>"
         <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $params ); ?>
         ><?php 

            $btn_icon = '';
            if ( ! empty( $params['icon'] ) ) {                                

                if ( substr( $params['icon'], 0, 4 ) != 'http') $img_path = WPBC_PLUGIN_URL . '/assets/img/' . $params['icon'];
                else                                                 $img_path = $params['icon'];
				// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                $btn_icon = '<img class="menuicons" src="' . $img_path . '" />';    // Img  Icon

            } elseif ( ! empty( $params['font_icon'] ) ) {                     

                $btn_icon = '<i class="menu_icon icon-1x ' . $params['font_icon'] . '"></i>';                         // Font Icon

            }  
            if (  ( ! empty( $btn_icon ) ) && ( $params['icon_position'] == 'left' )  )
                {echo wp_kses_post( $btn_icon ) . '&nbsp;';}

            // Text
            echo '<span' . ( (  ( ! empty( $btn_icon ) )  && ( ! $params['mobile_show_text'] ) )? ' class="in-button-text"' : '' ) . '>';
            echo    wp_kses_post( $params['title'] );
            if (  ( ! empty( $btn_icon ) ) && ( $params['icon_position'] == 'right' )  )
                echo '&nbsp;' ;
            echo '</span>';

            if (  ( ! empty( $btn_icon ) ) && ( $params['icon_position'] == 'right' )  )
                echo   wp_kses_post( $btn_icon );
        ?> &nbsp;<span class="caret"></span></a><?php    
        
        ?><ul   
            id="<?php echo esc_attr( $params['id'] ); ?>_container"
            class="dropdown-menu dropdown-menu-<?php echo esc_attr( $params['align'] ); ?>"
             ><?php
             
            wpbc_bs_list_of_options_for_dropdown( $params , 'simple' , $is_this_simple_list);
            
        ?></ul><?php 
}


function wpbc_bs_list_of_options_for_dropdown( $params, $dropdown_type = 'simple', $is_this_simple_list = true ) {

	$milliseconds = round( microtime( true ) * 1000 );    // FixIn: 8.8.2.1.

    foreach ( $params['options'] as $key => $value ) {

        if ( is_array( $value ) ) {                                 // Complex value constructions

            ?><li role="presentation">
                <div class="btn-toolbar" role="toolbar">
                    <?php 
                    $item_params = reset($value) ;   
                    
                    $default_item_params = array( 
                                          'id' => ''                                            // HTML ID  of element
                                        , 'default' => ''                                       // Some Value from optins array that selected by default
                                        , 'hint' => ''                                          // Hint // array( 'title' => __('Delete booking' ,'booking') , 'position' => 'bottom' ) 
                                        , 'class' => 'button-secondary'        
                                        , 'style' => ''                                         // Any CSS style
                                        , 'label' => ''                                         // Label of element "at Top of element"
                                        , 'title' => ''                                         // Title of element "Inside of element"
                                        , 'align' => 'left'                                     // Align: left | right                                               
                                        , 'attr' => array()                              
                                        , 'icon' => ''
                                        , 'font_icon' => ''
                                        , 'icon_position' => 'left'                             // Position  of icon relative to Text: left | right
                                        , 'mobile_show_text' => false                           // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        , 'disabled' => array()                                 // If some options disbaled,  then its must list  here
                                        , 'options' => array()                                  // Associated array  of titles and values         
                                        ); 
                    $item_params = wp_parse_args( $item_params, $default_item_params );
                    
                    ?>
                    <div class="<?php echo ( $item_params['type'] == 'group' ) ? esc_attr( $item_params['class'] ) : 'input-group'; ?>" 
                         style="<?php echo ( $item_params['type'] == 'group' ) ? esc_attr( $item_params['style'] ) : ''; ?>" 
                         role="group"><?php 

                         // Count number of text elements in this LI section
                        $number_of_text_elements = 0; 
                        foreach ( $value as $item_params ) { 
                            if ( $item_params['type'] == 'text' )
                                $number_of_text_elements++;
                        }
                        $focus_element_id = 'shjgkdfhgjhhsjgkhjkgdhghdjghjdkghjkdhgjkfghdfjhgjdkdgkhjf';
                        foreach ( $value as $item_params ) {

                            $item_type = $item_params['type'];

                            switch ( $item_type ) {

                                case 'checkbox':
                                case 'radio':
                                    ?><span class="input-group-addon"><?php 

                                        if ( empty( $item_params['id'] ) )      $item_params['id'] = $params['id'] . '_grp_' . $milliseconds ;

                                        if ( empty( $item_params['name'] ) )    $item_params['name'] = $item_params['id'];

                                        $focus_element_id = esc_attr( $item_params['id'] );

                                        wpbc_bs_checkbox( $item_params );

                                        if ( ! empty( $item_params['label'] ) ) {

                                            ?><label for="<?php echo esc_attr( $item_params['id'] ); ?>"><?php 

                                                echo wp_kses_post( $item_params['label'] );
                                            ?></label><?php
                                        }
                                    ?></span><?php                                    

                                    break;

                                case 'select':

                                    if ( empty( $item_params['id'] ) )      $item_params['id'] = $params['id'] . '_grp_' . $milliseconds ;

                                    if ( empty( $item_params['name'] ) )    $item_params['name'] = $item_params['id'];

                                    $item_params['onfocus'] = "jQuery('#" . $focus_element_id ."').prop('checked', true);";

                                    wpbc_bs_select( $item_params );

                                    break;

                                case 'text': 

                                    if ( empty( $item_params['id'] ) )      $item_params['id'] = $params['id'] . '_grp_' . $milliseconds ;

                                    if ( empty( $item_params['name'] ) )    $item_params['name'] = $item_params['id'];

                                    ?><div class="dropdown-menu-text-element <?php echo 'dropdown-menu-text-element-count-' . esc_attr( $number_of_text_elements ); ?>"><?php

                                        if ( ! empty( $item_params['label'] ) ) {

                                            ?><label for="<?php echo esc_attr( $item_params['id'] ); ?>"><?php 

                                                echo wp_kses_post( $item_params['label'] );

                                            ?></label><?php
                                        } 

                                        ?><legend class="screen-reader-text"><span><?php echo wp_kses_post( $item_params['label'] ); ?></span></legend><?php                                                     

                                        $item_params['onfocus'] = "jQuery('#" . $focus_element_id ."').prop('checked', true);";

                                        wpbc_bs_text( $item_params );

                                    ?></div><?php 

                                    break;

                                case 'button':

                                    $default_item_params = array( 
                                                        'action' => "wpbc_close_dropdown_selectbox( '" . $params['id'] . "' )" // Close list and uncheck  any checkboxes or radio boxes in dropdown list
                                                        ); 

                                    $item_params = wp_parse_args( $item_params, $default_item_params );

                                    wpbc_bs_button( $item_params );
                                    break;

                                default:
                                    break;
                          }
                      }                        
                    ?>
                    </div>
                </div>
            </li><?php 

        } else {

            if ( $value == 'divider' ) {

                ?><li role="presentation" class="divider"></li><?php

            } elseif ( $value == 'header' ) {

                ?><li role="presentation" class="dropdown-header"><?php echo esc_attr( $key ) ?></li><?php

            } else {                             
                    ?><li role="presentation" <?php if ( in_array( $value,  $params['disabled'] ) === true ) { echo ' class="disabled"';  } ?> >
                          <a  role="menuitem"
                              tabindex="-1"
                              href="javascript:void(0)" 
                              <?php 
                                if ( in_array( $value,  $params['disabled'] ) !== true ) { 
                                  
                                    if ( $dropdown_type == 'list' ) { ?>
                              
                                                onclick="javascript:
                                                                    wpbc_show_selected_in_dropdown('<?php echo esc_js($params['id']); ?>', jQuery(this).html(), '<?php echo esc_js($value); ?>');
                                                            <?php if (! $is_this_simple_list ) { ?>
                                                                    wpbc_close_dropdown_selectbox( '<?php echo esc_js($params['id']); ?>' );
                                                            <?php } ?>"                                  
                                    <?php } else { ?>
                                                
                                                onclick="javascript:<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $value; ?>"
                                    <?php } 
                                                
                                } ?>
                           ><?php echo esc_html( $key ); ?></a>
                      </li><?php
            }                  
        }

    }    
}

////////////////////////////////////////////////////////////////////////////////
//  Control elements      //////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
	 * Button Groups
 *
 * @param array $args
 *

    $params = array(
                      'label_for' => 'actions'                              // "For" parameter  of button group element
                    , 'label' => __('Actions:', 'booking')                  // Label above the button group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                              'type' => 'button'
                                            , 'title' => __('Delete', 'booking')    // Title of the button
                                            , 'hint' => array( 'title' => __('Delete booking' ,'booking') , 'position' => 'bottom' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => ''                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => ''
                                            , 'icon_position' => 'left'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                            , 'attr' => array()
                                        )
                                        , array(
                                            'type' => 'button'
                                            , 'title' => __('Delete', 'booking')
                                            , 'class' => 'button-primary'
                                            , 'font_icon' => 'wpbc_icn_delete_outline'
                                            , 'icon_position' => 'left'
                                            , 'action' => "jQuery('#booking_filters_formID').trigger( 'submit' );"
                                            , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                        )
                                    )
    );
    wpbc_bs_button_group( $params );

 */
function wpbc_bs_button_group( $args = array() ) {

    $milliseconds = round(microtime(true) * 1000);

    $defaults = array(
                      'label_for' => 'btn_bs_' . $milliseconds                  // "For" parameter  of label element
                    , 'label' => ''                                             // Label above element
                    , 'style' => ''                                             // CSS of entire div element
                    , 'items' => array()                                        // Array of elements
              );

    $params = wp_parse_args( $args, $defaults );

  ?><div class="control-group" style="<?php echo esc_attr( $params['style'] ); ?>" ><?php
        if (! empty( $params['label'] ) ) {
            ?><label for="<?php echo esc_attr( $params['label_for'] ); ?>" class="control-label"><?php echo esc_html( $params['label'] ); ?></label><?php
        }
      ?><div class="btn-toolbar" role="toolbar" aria-label="..." id="<?php echo esc_attr( $params['label_for'] ); ?>">
            <div class="btn-group" role="group" aria-label="..."><?php

                foreach ( $params['items'] as $item ) {

                    switch ( $item['type'] ) {

                        case 'button':
                                        wpbc_bs_button( $item );
                                        break;
                        case 'dropdown':
                                        wpbc_bs_dropdown( $item );
                                        break;
                        default:
                            break;
                    }

                } ?>
            </div>
        </div>
    </div><?php
}


/**
	 * Show Input Group
 *
 * @param array $args
 *

    $params = array(
                      'label_for' => 'min_cost'                             // "For" parameter  of label element
                    , 'label' => __('Min / Max cost:', 'booking')           // Label above the input group
                    , 'style' => ''                                         // CSS Style of entire div element
                    , 'items' => array(
                                        array(
                                            'type' => 'addon'
                                            , 'element' => 'text'       // text | radio | checkbox
                                            , 'text' => __('Cost', 'booking') . ':'
                                            , 'class' => ''                 // Any CSS class here
                                            , 'style' => 'font-weight:600;'                 // CSS Style of entire div element
                                        )
                                        , array(
                                            'type' => 'addon'
                                            , 'element' => 'checkbox'       // text | radio | checkbox
                                            , 'id' => 'apply_elem'          // ID, if this radio | checkbox element
                                            , 'name' => 'apply_elem'        // Name, if this radio | checkbox element
                                            , 'value' => 'On'               // value, if this radio | checkbox element
                                            , 'selected' => false           // Selected, if this radio | checkbox element
                                            , 'legend' => ''                // aria-label parameter , if this radio | checkbox element
                                            , 'class' => ''                 // Any CSS class here
                                            , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element
                                        )
                                        , array(
                                            'type' => 'addon'
                                            , 'element' => 'radio'          // text | radio | checkbox
                                            , 'id' => 'min_elem'            // ID, if this radio | checkbox element
                                            , 'name' => 'cost_elem'         // Name, if this radio | checkbox element
                                            , 'value' => 'min'              // value, if this radio | checkbox element
                                            , 'selected' => false           // Selected, if this radio | checkbox element
                                            , 'legend' => ''                // aria-label parameter , if this radio | checkbox element
                                            , 'class' => ''                 // Any CSS class here
                                            , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element
                                        )
                            // Warning! Can be text or selectbox, not both OR you need to define width
                            , array(
                                'type' => 'text'
                                , 'id' => 'min_cost'            // HTML ID  of element
                                , 'value' => ''                 // Value of Text field
                                , 'placeholder' => __('Reason of Cancelation', 'booking')
                                , 'style' => 'width:100px;'                 // CSS of select element
                                , 'class' => ''                 // CSS Class of select element
                                , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element
                            )
                                        , array(
                                            'type' => 'addon'
                                            , 'element' => 'text'           // text | radio | checkbox
                                            , 'text' => ' - '               // Simple plain text showing
                                        )
                                        , array(
                                            'type' => 'text'
                                            , 'id' => 'max_cost'            // HTML ID  of element
                                            , 'value' => ''                 // Value of Text field
                                            , 'placeholder' => __('Max Cost', 'booking')
                                            , 'style' => ''                 // CSS of select element
                                            , 'class' => ''                 // CSS Class of select element
                                            , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element

                                        )
                                        , array(
                                            'type' => 'addon'
                                            , 'element' => 'radio'          // text | radio | checkbox
                                            , 'id' => 'max_elem'            // ID, if this radio | checkbox element
                                            , 'name' => 'cost_elem'         // Name, if this radio | checkbox element
                                            , 'value' => 'max'              // value, if this radio | checkbox element
                                            , 'selected' => false           // Selected, if this radio | checkbox element
                                            , 'legend' => ''                // aria-label parameter , if this radio | checkbox element
                                            , 'class' => ''                 // Any CSS class here
                                            , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element
                                        )
                            // Warning! Can be text or selectbox, not both  OR you need to define width
                            , array(
                                  'type' => 'select'
                                , 'id' => 'select_option'       // HTML ID  of element
                                , 'options' => array( 'delete' => __('Delete', 'booking'), 'any' => __('Any','booking'), 'specific' => __('Specific', 'booking')  )      // Associated array  of titles and values
                                , 'disabled_options' => array( 'any' )  // If some options disbaled,  then its must list  here
                                , 'default' => 'specific'       // Some Value from optins array that selected by default
                                , 'style' => 'width:200px;'                 // CSS of select element
                                , 'class' => ''                 // CSS Class of select element
                                , 'attr' => array()             // Any  additional attributes, if this radio | checkbox element
                            )

                                // Select Advanced option list
                                , array(
                                      'type' => 'select'
                                    , 'id' => 'select_form_help_shortcode'
                                    , 'name' => 'select_form_help_shortcode'
                                    , 'style' => ''
                                    , 'class' => ''
                                    , 'multiple' => false
                                    , 'disabled' => false
                                    , 'disabled_options' => array()             // If some options disbaled,  then its must list  here
                                    , 'attr' => array()                         // Any  additional attributes, if this radio | checkbox element
                                    , 'options' => array(                       // Associated array  of titles and values
                                                          'info' => array(
                                                                        'title' => __('General Info', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_sf_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Standard Fields' ,'booking')
                                                                    )
                                                        , 'text' => array(
                                                                        'title' => __('Text', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'select' => array(
                                                                        'title' => __('Select', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'textarea' => array(
                                                                        'title' => __('Textarea', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'checkbox' => array(
                                                                        'title' => __('Checkbox', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_sf_e' => array( 'optgroup' => true, 'close'  => true )


                                                        , 'optgroup_af_s' => array(
                                                                        'optgroup' => true
                                                                        , 'close'  => false
                                                                        , 'title'  => '&nbsp;' . __('Advanced Fields' ,'booking')
                                                                    )
                                                        , 'info_advanced' => array(
                                                                        'title' => __('Info', 'booking')
                                                                        , 'id' => ''
                                                                        , 'name' => ''
                                                                        , 'style' => ''
                                                                        , 'class' => ''
                                                                        , 'disabled' => false
                                                                        , 'selected' => false
                                                                        , 'attr' => array()
                                                                    )
                                                        , 'optgroup_af_e' => array( 'optgroup' => true, 'close'  => true )

                                                    )
                                    , 'value' => ''                             // Some Value from optins array that selected by default
                                    , 'onfocus' => ''
                                    , 'onchange' => "jQuery('.wpbc_field_generator').hide();jQuery('.wpbc_field_generator_' + this.options[this.selectedIndex].value ).show();"
                                )
                                        , array(
                                              'type' => 'button'
                                            , 'title' => __('Delete', 'booking')    // Title of the button
                                            , 'hint' => array( 'title' => __('Delete booking' ,'booking') , 'position' => 'bottom' ) // Hint
                                            , 'link' => 'javascript:void(0)'        // Direct link or skip  it
                                            , 'action' => ''                // Some JavaScript to execure, for example run  the function
                                            , 'class' => ''                 // button-secondary  | button-primary
                                            , 'icon' => ''
                                            , 'font_icon' => ''
                                            , 'icon_position' => 'left'     // Position  of icon relative to Text: left | right
                                            , 'style' => ''                 // Any CSS class here
                                            , 'attr' => array()
                                        )
                                        , array(
                                            'type' => 'button'
                                            , 'title' => __('Delete', 'booking')
                                            , 'class' => 'button-primary'
                                            , 'font_icon' => 'wpbc_icn_delete_outline'
                                            , 'icon_position' => 'left'
                                            , 'action' => "jQuery('#booking_filters_formID').trigger( 'submit' );"
                                        )
                    )
              );
    ?><div class="control-group wpbc-no-padding" style="max-width:730px;"><?php
            wpbc_bs_input_group( $params );
    ?></div><?php

 */
function wpbc_bs_input_group( $args = array() ) {

    $milliseconds = round(microtime(true) * 1000);

    $defaults = array(
                      'label_for' => 'btn_bs_' . $milliseconds                  // "For" parameter  of label element
                    , 'label' => ''                                             // Label above element
                    , 'style' => ''                                             // CSS of entire div element
                    , 'items' => array()                                        // Array of elements
              );

    $params = wp_parse_args( $args, $defaults );

    ?><div class="wpbc-no-padding" style="width:auto;<?php echo esc_attr( $params['style'] ); ?>" ><?php
        if (! empty( $params['label'] ) ) {
            ?><label for="<?php echo esc_attr( $params['label_for'] ); ?>" class="control-label"><?php echo esc_html( $params['label'] ); ?></label><?php
        }
        ?><div class="btn-toolbar" role="toolbar" aria-label="..." id="<?php echo esc_attr( $params['label_for'] ); ?>_toolbar">
            <div class="input-group" role="group" aria-label="..."><?php

                    foreach ( $params['items'] as $item ) {
						switch ( $item['type']){

                            case 'button':
                                        ?><div class="input-group-btn"><?php

                                        wpbc_bs_button( $item );

                                        ?></div><?php
                                        break;

                            case 'text':
                                        wpbc_bs_text( $item );
                                        break;

                            case 'select':
                                        wpbc_bs_select( $item );
                                        break;

                            case 'addon':   // FixIn: 9.5.4.9.
										$item['style'] = empty($item['style']) ? 'line-height: 21px;' : $item['style'] . 'line-height: 21px;margin-right: -10px;';  // FixIn: 9.5.4.9.
                                        wpbc_bs_addon( $item );
											?></div><!-- /input-group -->
										</div>
									</div>
								</div>
								<div class="control-group wpbc-no-padding">
									<div class="wpbc-no-padding" style="width:auto;<?php echo esc_attr( $params['style'] ); ?>" >
										<div class="btn-toolbar" role="toolbar" aria-label="..." id="<?php echo esc_attr( $params['label_for'] ); ?>_toolbar">
											<div class="input-group" role="group" aria-label="..."><?php

                                        break;
                            default:
                                break;
                        }
                    }

            ?></div><!-- /input-group -->
        </div>
    </div><?php
}


/**
	 * Selectbox - Dropdown List with comple UI elements
 *
 * @param array $args = array(
                                'id' => ''                                              // HTML ID  of element
                                , 'id2' => ''                                           // (optional) HTML ID  of 2nd element
                                , 'default' => ''                                       // Some Value from optins array that selected by default
                                , 'default2' => ''                                      // (optional) Some Value from optins array that selected by default
                                , 'hint' => ''                                          // (optional) Hint: array( 'title' => __('Delete booking' ,'booking') , 'position' => 'bottom' )
                                , 'css_classes' => ''                                   // (optional)
                                , 'label' => ''                                         // (optional) Label of element "at Top of element"
                                , 'title' => ''                                         // Title of element "Inside of element"
                                , 'align' => 'left'                                     // (optional) Align: left | right
                                , 'disabled' => array()                                 // (optional) If some options disbaled,  then its must list  here
                                , 'options' => array(                                   // Associated array  of titles and values
                                                        __('Bla bla bla', 'booking') => '1'
                                                        , 'divider0' => 'divider'
                                                        , __('Section Header', 'booking') => 'header'
                                                            ...

                                                        , '_radio_or_checkbox' => array(
                                                                                    array(  'type'          => 'radio'          // 'radio' or 'checkbox'
                                                                                            , 'id' => $item_params['id']          // HTML ID  of element
                                                                                            , 'name' => $item_params['name']
                                                                                            , 'style' => ''                     // CSS of select element
                                                                                            , 'class' => ''                     // CSS Class of select element
                                                                                            , 'disabled' => false
                                                                                            , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                                            , 'legend' => ''                    // aria-label parameter
                                                                                            , 'value' => ''                     // Some Value from optins array that selected by default
                                                                                            , 'selected' => false               // Selected or not
                                                                                            , 'label' => ''                     // Label - title
                                                                                        )
                                                                            )
                                                        , '_select' => array(
                                                                                    array(  'type'          => 'select'
                                                                                            , 'id' => ''                        // HTML ID  of element
                                                                                            , 'style' => ''                     // CSS of select element
                                                                                            , 'class' => ''                     // CSS Class of select element
                                                                                            , 'multiple' => false
                                                                                            , 'disabled' => false
                                                                                            , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                                            , 'options' => array()              // Associated array  of titles and values
                                                                                            , 'disabled_options' => array()     // If some options disbaled,  then its must list  here
                                                                                            , 'value' => ''                     // Some Value from optins array that selected by default
                                                                                        )
                                                                            )
                                                        , '_texts' => array(
                                                                                    array(  'type'          => 'text'
                                                                                            , 'id'          => ''
                                                                                            , 'name'        => ''
                                                                                            , 'label'       => ''
                                                                                            , 'disabled'    => false
                                                                                            , 'class'       => ''
                                                                                            , 'style'       => ''
                                                                                            , 'placeholder' => ''
                                                                                            , 'attr'        => array()
                                                                                            , 'value' => ''
                                                                                        )
                                                                            )
                                                        , '_buttons' => array(
                                                                                    array(  'type' => 'button'
                                                                                          , 'title' => ''                     // Title of the button
                                                                                          , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                          , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                          , 'action' => "wpbc_close_dropdown_selectbox( '" . $params['id'] . "' )" // Some JavaScript to execure, for example run  the function
                                                                                          , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                                          , 'icon' => ''
                                                                                          , 'font_icon' => ''
                                                                                          , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                          , 'style' => ''                     // Any CSS class here
                                                                                          , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                          , 'attr' => array()
                                                                                        )
                                                                            )
                                                     )
                            );
 *
 Exmaple #1:
            $params = array(                                                    // Pending, Active, Suspended, Terminated, Cancelled, Fraud
                            'id' => 'wh_approved'
                            , 'options' => array (
                                                    __('Pending', 'booking') => '0',
                                                    __('Approved', 'booking') => '1',
                                                    , 'divider0' => 'divider'
                                                    , __('Section Header', 'booking') => 'header'
                                                    , __('Any', 'booking') => ''
                                                 )
                            , 'disabled' => array( '' )                         // It will disable "Any" option
                            , 'default' => ( isset( $_REQUEST[ 'wh_approved' ] ) ) ? esc_attr( $_REQUEST[ 'wh_approved' ] ) : ''
                            , 'label' => ''//__('Status', 'booking') . ':'
                            , 'title' => __('Bookings', 'booking')
                        );

            wpbc_bs_dropdown_list( $params );

 Exmaple #2:
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
            $params = array(
                              'id'  => 'wh_booking_date'
                            , 'id2' => 'wh_booking_date2'
                            , 'default' =>  ( isset( $_REQUEST[ 'wh_booking_date' ] ) ) ? esc_attr( $_REQUEST[ 'wh_booking_date' ] ) : ''
                            , 'default2' => ( isset( $_REQUEST[ 'wh_booking_date2' ] ) ) ? esc_attr( $_REQUEST[ 'wh_booking_date2' ] ) : ''
                            , 'hint' => array( 'title' => __('Filter bookings by booking dates' ,'booking') , 'position' => 'top' )
                            , 'label' => ''//__('Booked Dates', 'booking') . ':'
                            , 'title' => __('Dates', 'booking')
                            , 'options' => array (
                                                      __('Current dates' ,'booking')    => '0'
                                                    , __('Today' ,'booking')            => '1'
                                                    , __('Previous dates' ,'booking')   => '2'
                                                    , __('All dates' ,'booking')        => '3'
                                                    , 'divider1' => 'divider'
                                                    , __('Today check in/out' ,'booking')   => '9'
                                                    , __('Check In - Tomorrow' ,'booking')  => '7'
                                                    , __('Check Out - Tomorrow' ,'booking') => '8'
                                                    , 'divider2' => 'divider'
                                                    , 'next' => array(
                                                                        array(
                                                                                'type' => 'radio'
                                                                              , 'label' => __('Next' ,'booking')
                                                                              , 'id' => 'wh_booking_datedays_interval1'
                                                                              , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                              , 'style' => ''                     // CSS of select element
                                                                              , 'class' => ''                     // CSS Class of select element
                                                                              , 'disabled' => false
                                                                              , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                              , 'legend' => ''                    // aria-label parameter
                                                                              , 'value' => '4'                     // Some Value from optins array that selected by default
                                                                              , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                                && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '4' ) ) ? true : false
                                                                              )
                                                                        , array(
                                                                                'type' => 'select'
                                                                              , 'attr' => array()
                                                                              , 'name' => 'wh_booking_datenext'
                                                                              , 'id' => 'wh_booking_datenext'
                                                                              , 'options' => $dates_interval
                                                                              , 'value' => isset( $_REQUEST[ 'wh_booking_datenext'] ) ? esc_attr( $_REQUEST[ 'wh_booking_datenext'] ) : ''
                                                                              )
                                                                     )
                                                    , 'prior' => array(
                                                                        array(
                                                                                'type' => 'radio'
                                                                              , 'label' => __('Prior' ,'booking')
                                                                              , 'id' => 'wh_booking_datedays_interval2'
                                                                              , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                              , 'style' => ''                     // CSS of select element
                                                                              , 'class' => ''                     // CSS Class of select element
                                                                              , 'disabled' => false
                                                                              , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                              , 'legend' => ''                    // aria-label parameter
                                                                              , 'value' => '5'                     // Some Value from optins array that selected by default
                                                                              , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                                && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '5' ) ) ? true : false
                                                                              )
                                                                        , array(
                                                                                'type' => 'select'
                                                                              , 'attr' => array()
                                                                              , 'name' => 'wh_booking_dateprior'
                                                                              , 'id' => 'wh_booking_dateprior'
                                                                              , 'options' => $dates_interval
                                                                              , 'value' => isset( $_REQUEST[ 'wh_booking_dateprior'] ) ? esc_attr( $_REQUEST[ 'wh_booking_dateprior'] ) : ''
                                                                              )
                                                                     )
                                                    , 'fixed' => array( array(  'type' => 'group', 'class' => 'input-group text-group'),
                                                                        array(
                                                                                'type' => 'radio'
                                                                              , 'label' => __('Dates' ,'booking')
                                                                              , 'id' => 'wh_booking_datedays_interval3'
                                                                              , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                              , 'style' => ''                     // CSS of select element
                                                                              , 'class' => ''                     // CSS Class of select element
                                                                              , 'disabled' => false
                                                                              , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                              , 'legend' => ''                    // aria-label parameter
                                                                              , 'value' => '6'                     // Some Value from optins array that selected by default
                                                                              , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                                && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '6' ) ) ? true : false
                                                                              )
                                                                        , array(
                                                                                'type'          => 'text'
                                                                                , 'id'          => 'wh_booking_datefixeddates'
                                                                                , 'name'        => 'wh_booking_datefixeddates'
                                                                                , 'label'       => __('Check-in' ,'booking') . ':'
                                                                                , 'disabled'    => false
                                                                                , 'class'       => 'wpdevbk-filters-section-calendar'           // This class add datepicker
                                                                                , 'style'       => ''
                                                                                , 'placeholder' => gmdate( 'Y-m-d' )
                                                                                , 'attr'        => array()
                                                                                , 'value' => isset( $_REQUEST[ 'wh_booking_datefixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_booking_datefixeddates'] ) : ''
                                                                              )
                                                                        , array(
                                                                                'type'          => 'text'
                                                                                , 'id'          => 'wh_booking_date2fixeddates'
                                                                                , 'name'        => 'wh_booking_date2fixeddates'
                                                                                , 'label'       => __('Check-out' ,'booking') . ':'
                                                                                , 'disabled'    => false
                                                                                , 'class'       => 'wpdevbk-filters-section-calendar'                  // This class add datepicker
                                                                                , 'style'       => ''
                                                                                , 'placeholder' => gmdate( 'Y-m-d' )
                                                                                , 'attr'        => array()
                                                                                , 'value' => isset( $_REQUEST[ 'wh_booking_date2fixeddates'] ) ? esc_attr( $_REQUEST[ 'wh_booking_date2fixeddates'] ) : ''
                                                                              )
                                                                     )
                                                    , 'divider3' => 'divider'
                                                    , 'custom' => array( array(  'type' => 'group', 'class' => 'input-group text-group')
                                                                        , array(
                                                                                'type' => 'radio'
                                                                              , 'label' => __('Tada' ,'booking')
                                                                              , 'id' => 'wh_booking_datedays_interval4'
                                                                              , 'name' => 'wh_booking_datedays_interval_Radios'
                                                                              , 'style' => ''                     // CSS of select element
                                                                              , 'class' => ''                     // CSS Class of select element
                                                                              , 'disabled' => false
                                                                              , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                              , 'legend' => ''                    // aria-label parameter
                                                                              , 'value' => '10'                     // Some Value from optins array that selected by default
                                                                              , 'selected' => ( isset($_REQUEST[ 'wh_booking_datedays_interval_Radios'] )
                                                                                                && ( $_REQUEST[ 'wh_booking_datedays_interval_Radios'] == '10' ) ) ? true : false
                                                                              )

                                                                        , array(
                                                                                'type'          => 'text'
                                                                                , 'id'          => 'wh_booking_datecustom'
                                                                                , 'name'        => 'wh_booking_datecustom'
                                                                                , 'label'       => __('Custom' ,'booking') . ':'
                                                                                , 'disabled'    => false
                                                                                , 'class'       => ''
                                                                                , 'style'       => ''
                                                                                , 'placeholder' => gmdate( 'Y' )
                                                                                , 'attr'        => array()
                                                                                , 'value' => isset( $_REQUEST[ 'wh_booking_datecustom'] ) ? esc_attr( $_REQUEST[ 'wh_booking_datecustom'] ) : ''
                                                                              )
                                                                        )
                                                    , 'divider4' => 'divider'
                                                    , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ),
                                                                        array(
                                                                                  'type' => 'button'
                                                                                , 'title' => __('Apply' ,'booking')                     // Title of the button
                                                                                , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                , 'action' => "wpbc_show_selected_in_dropdown__radio_select_option("
                                                                                                                                                 . "  'wh_booking_date'"
                                                                                                                                                 . ", 'wh_booking_date2'"
                                                                                                                                                 . ", 'wh_booking_datedays_interval_Radios' "
                                                                                                                                                 . ");"
                                                                                                                    // Some JavaScript to execure, for example run  the function
                                                                                , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                                , 'icon' => ''
                                                                                , 'font_icon' => ''
                                                                                , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                , 'style' => ''                     // Any CSS class here
                                                                                , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                , 'attr' => array()

                                                                              )
                                                                        , array(
                                                                                  'type' => 'button'
                                                                                , 'title' => __('Close' ,'booking')                     // Title of the button
                                                                                , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                                , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                                //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                                , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                                , 'icon' => ''
                                                                                , 'font_icon' => ''
                                                                                , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                                , 'style' => ''                     // Any CSS class here
                                                                                , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                                , 'attr' => array()
                                                                              )
                                                                        )
                                                )
                        );
            wpbc_bs_dropdown_list( $params );


Exmaple #3:
            $params = array(
                          'id'  => 'wh_pay_status'
                        // , 'id2' => 'wh_booking_date2'
                        , 'default' =>  ( isset( $_REQUEST[ 'wh_pay_status' ] ) ) ? esc_attr( $_REQUEST[ 'wh_pay_status' ] ) : 'all'
                        // , 'default2' => ( isset( $_REQUEST[ 'wh_booking_date2' ] ) ) ? esc_attr( $_REQUEST[ 'wh_booking_date2' ] ) : ''
                        , 'hint' => array( 'title' => __('Payment status' ,'booking') , 'position' => 'top' )
                        , 'label' => ''//__('Booked Dates', 'booking') . ':'
                        , 'title' => __('Payment', 'booking')
                        , 'options' => array (
                                                __('Any Status' ,'booking')       =>'all',
                                                'divider0' => 'divider',
                                                __('Paid OK' ,'booking') =>'group_ok',
                                                __('Unknown Status' ,'booking')    =>'group_unknown',
                                                __('Not Completed' ,'booking')     =>'group_pending',
                                                __('Failed' ,'booking')            =>'group_failed'
                                                , 'divider2' => 'divider'
                                                , 'custom' => array( array(  'type' => 'group', 'class' => 'input-group text-group')
                                                                    , array(
                                                                            'type' => 'radio'
                                                                          , 'label' => __('Custom' ,'booking')
                                                                          , 'id' => 'wh_pay_statuscustom_radios1'
                                                                          , 'name' => 'wh_pay_statuscustom_Radios'
                                                                          , 'style' => ''                     // CSS of select element
                                                                          , 'class' => ''                     // CSS Class of select element
                                                                          , 'disabled' => false
                                                                          , 'attr' => array()                 // Any  additional attributes, if this radio | checkbox element
                                                                          , 'legend' => ''                    // aria-label parameter
                                                                          , 'value' => '1'                     // Some Value from optins array that selected by default
                                                                          , 'selected' => ( isset($_REQUEST[ 'wh_pay_statuscustom_Radios'] )
                                                                                            && ( $_REQUEST[ 'wh_pay_statuscustom_Radios'] == '1' ) ) ? true : false
                                                                          )

                                                                    , array(
                                                                            'type'          => 'text'
                                                                            , 'id'          => 'wh_pay_statuscustom'
                                                                            , 'name'        => 'wh_pay_statuscustom'
                                                                            , 'label'       => __('Payment status' ,'booking') . ':'
                                                                            , 'disabled'    => false
                                                                            , 'class'       => ''
                                                                            , 'style'       => ''
                                                                            , 'placeholder' => ''
                                                                            , 'attr'        => array()
                                                                            , 'value' => isset( $_REQUEST[ 'wh_pay_statuscustom'] ) ? esc_attr( $_REQUEST[ 'wh_pay_statuscustom'] ) : ''
                                                                          )
                                                                    )
                                                , 'divider4' => 'divider'
                                                , 'buttons' => array( array(  'type' => 'group', 'class' => 'btn-group' ),
                                                                    array(
                                                                              'type' => 'button'
                                                                            , 'title' => __('Apply' ,'booking')                     // Title of the button
                                                                            , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                            , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                            , 'action' => "wpbc_show_selected_in_dropdown__radio_select_option("
                                                                                                                                             . "  'wh_pay_status'"
                                                                                                                                             . ", ''"
                                                                                                                                             . ", 'wh_pay_statuscustom_Radios' "
                                                                                                                                             . ");"
                                                                                                                // Some JavaScript to execure, for example run  the function
                                                                            , 'class' => 'button-primary'       // button-secondary  | button-primary
                                                                            , 'icon' => ''
                                                                            , 'font_icon' => ''
                                                                            , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                            , 'style' => ''                     // Any CSS class here
                                                                            , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                            , 'attr' => array()

                                                                          )
                                                                    , array(
                                                                              'type' => 'button'
                                                                            , 'title' => __('Close' ,'booking')                     // Title of the button
                                                                            , 'hint' => ''                      // , 'hint' => array( 'title' => __('Select status' ,'booking') , 'position' => 'bottom' )
                                                                            , 'link' => 'javascript:void(0)'    // Direct link or skip  it
                                                                            //, 'action' => ''                    // Some JavaScript to execure, for example run  the function
                                                                            , 'class' => 'button-secondary'     // button-secondary  | button-primary
                                                                            , 'icon' => ''
                                                                            , 'font_icon' => ''
                                                                            , 'icon_position' => 'left'         // Position  of icon relative to Text: left | right
                                                                            , 'style' => ''                     // Any CSS class here
                                                                            , 'mobile_show_text' => false       // Show  or hide text,  when viewing on Mobile devices (small window size).
                                                                            , 'attr' => array()
                                                                          )
                                                                    )
                                            )
                    );
            wpbc_bs_dropdown_list( $params );

 */
function wpbc_bs_dropdown_list( $args = array() ) {

    $milliseconds = round(microtime(true) * 1000);

    $defaults = array(
                        'id' => ''                                              // HTML ID  of element
                        , 'id2' => ''                                           // HTML ID  of 2nd element
                        , 'default' => ''                                       // Some Value from optins array that selected by default
                        , 'default2' => ''                                      // Some Value from optins array that selected by default
                        , 'hint' => ''                                          // Hint // array( 'title' => __('Delete booking' ,'booking') , 'position' => 'bottom' )
                        , 'css_classes' => ''
                        , 'label' => ''                                         // Label of element "at Top of element"
                        , 'title' => ''                                         // Title of element "Inside of element"
                        , 'align' => 'left'                                     // Align: left | right
                        , 'options' => array()                                  // Associated array  of titles and values
                        , 'disabled' => array()                                 // If some options disbaled,  then its must list  here
                    );
    $params = wp_parse_args( $args, $defaults );

    // Check  if this list Simple or Complex (with  input elements) ////////////
    $is_this_simple_list =  true;        					//FixIn: 9.0.1.1.1
    foreach ( $params['options'] as $key => $value ) {
        if ( is_array( $value ) ) {
            $is_this_simple_list = false;
            break;
        }
    }

    // Selected Value //////////////////////////////////////////////////////////
    $wpbc_value = $params[ 'default' ];

    $wpbc_selector_default = array_search( $wpbc_value, $params['options'] );
    if ( $wpbc_selector_default === false ) {
        $wpbc_selector_default = key( $params['options'] );
        $wpbc_selector_default_value = current( $params['options'] );
    } else
        $wpbc_selector_default_value = $wpbc_value;

    $wpbc_selector_default_value2 = $params[ 'default2' ];

    // Initial setting title,  if already  was request /////////////////////////
      // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
      if ( isset( $_REQUEST[ esc_attr( $params['id'] ) ] ) ) {
        ?><script type="text/javascript">
            jQuery(document).ready( function(){
                jQuery('#<?php echo esc_attr( $params['id'] ); ?>_container .button.button-primary').trigger( "click" );
            });
           </script><?php
      }
	// -----------------------------------------------------------------------------------------------------------------
    ?>
      <div class="control-group">
        <?php if ( ! empty( $params['label'] ) ) {
            ?><label for="<?php echo esc_attr( $params['id'] ); ?>" class="control-label"><?php echo esc_html( $params['label'] ); ?></label><?php
        }
        ?><div>
            <div class="btn-group">
              <a href="javascript:void(0)"
                 <?php if (! $is_this_simple_list ) { ?>
                 onclick="javascript:jQuery('#<?php echo esc_js( $params['id'] ); ?>_container').show();"
                 <?php } ?>
                 data-toggle="wpbc_dropdown"
                 id="<?php echo esc_attr( $params['id'] ); ?>_selector"
                  <?php if ( ! empty( $params['hint'] ) ) { ?>
                 title="<?php  echo esc_js( $params['hint']['title'] ); ?>"
                  <?php } ?>
                 class="button button-secondary dropdown-toggle <?php
                        echo esc_attr( ( ! empty( $params['hint'] ) ) ? 'tooltip_' . $params['hint']['position'] . ' ' : '' );
                        echo esc_attr( ( ! empty( $params['css_classes'] ) ) ? $params['css_classes'] . ' ' : '' );
                        ?>"
                 <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $params ); ?>
                 ><label class="label_in_filters"
                      ><?php echo wp_kses_post( $params['title'] ); ?>: </label> <span class="wpbc_selected_in_dropdown"><?php echo wp_kses_post( $wpbc_selector_default ); ?></span> &nbsp; <span class="caret"></span></a>
              <ul
                  id="<?php echo esc_attr( $params['id'] ); ?>_container"
                  class="dropdown-menu dropdown-menu-<?php echo esc_attr( $params['align'] ); ?>"
                   ><?php

                    wpbc_bs_list_of_options_for_dropdown( $params , 'list', $is_this_simple_list );

            ?></ul>
              <input type="hidden" autocomplete="off" value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $wpbc_selector_default_value; ?>" id="<?php echo esc_attr( $params['id'] ); ?>"  name="<?php echo esc_attr( $params['id'] ); ?>" />
              <?php if ( ! empty($params['id2'] ) ) { ?>
                    <input type="hidden" autocomplete="off" value="<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $wpbc_selector_default_value2; ?>" id="<?php echo esc_attr( $params['id2'] ); ?>"  name="<?php echo esc_attr( $params['id2'] ); ?>" />
              <?php } ?>
            </div>
        </div>
      </div>
    <?php
}



////////////////////////////////////////////////////////////////////////////////
//  Drop Down Menu
////////////////////////////////////////////////////////////////////////////////

/**
	 * Show Dropdown Menu
 * 
 * Usage: 

wpbc_dropdown_menu( array( 
                           'title' => __('Help', 'booking') 
                         , 'hint' => array( 'title' => __('Help info' ,'booking') , 'position' => 'bottom' )
                         , 'font_icon' => 'glyphicon glyphicon-question-sign'
                         , 'position' => 'right'
                         , 'items' => array( 
                                             array( 'type' => 'link', 'title' => __('About Plugin', 'booking'), 'url' => 'https://wpbookingcalendar.com /' )
                                           , array( 'type' => 'divider' )
                                           , array( 'type' => 'text', 'title' => __('Text', 'booking') )
                                           , array( 'type' => 'link', 'title' => __('Help', 'booking'), 'url' => 'https://wpbookingcalendar.com/help/' )
                                           , array( 'type' => 'link', 'title' => __('FAQ', 'booking'), 'url' => 'https://wpbookingcalendar.com/faq/' )
                                           , array( 'type' => 'link', 'title' => __('Technical Support', 'booking'), 'url' => 'https://wpbookingcalendar.com/support/' )
                                           , array( 'type' => 'divider' )
                                           , array( 'type' => 'link', 'title' => __('Upgrade Now', 'booking'), 'url' => 'https://wpbookingcalendar.com/', 'style' => 'font-weight: 600;' , 'attr' => array( 'target' => '_blank' ) )
                                      )
                  ) ); 
 * 
 * @param array $args
 */
function wpbc_bs_dropdown_menu( $args = array() ) {
        
        $defaults = array(
                'title' => '',
                'hint' => '',
                'icon' => '',
                'font_icon' => '',
                'position' => '',
				'style'    => '',
                'items' => array()
        );        
	$params = wp_parse_args( $args, $defaults );
        
    ?><span class="dropdown <?php echo ( ( $params['position'] == 'right' ) ? 'pull-right' : '' ); ?>"  style="<?php echo esc_attr( $params['style'] ); ?>">
        <a href="javascript:void(0)"
           data-toggle="wpbc_dropdown" 
           aria-expanded="true"
            <?php if ( ! empty( $params['hint'] ) ) { ?>
              title="<?php  echo esc_attr( $params['hint']['title'] ); ?>"
            <?php } ?>           
           class="dropdown-toggle nav-tab <?php 
              echo esc_attr( ( ! empty( $params['hint'] ) ) ? 'tooltip_' . $params['hint']['position'] . ' ' : '' );
               ?>"><?php
           
           if ( ! empty( $params['icon'] ) ) {                                
                
                if ( substr( $params['icon'], 0, 4 ) != 'http') $img_path = WPBC_PLUGIN_URL . '/assets/img/' . $params['icon'];
                else                                            $img_path = $params['icon'];

				// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                ?><img class="menuicons" src="<?php echo esc_attr( $img_path ); ?>"><?php      // Img  Icon
                         
            } elseif ( ! empty( $params['font_icon'] ) ) {                     
                
                 ?><i class="menu_icon icon-1x <?php echo wp_kses_post($params['font_icon']); ?>"></i>&nbsp;<?php                              // Font Icon
                 
            } ?><span 
                    class="nav-tab-text"><?php echo wp_kses_post( $params['title'] ); ?></span>&nbsp;<b
                    class="caret" style="border-top-color: #333333 !important;"></b></a>
        <ul class="dropdown-menu" role="menu" style="<?php echo ( ( $params['position'] == 'right' ) ? 'right:0px; left:auto;' : '' ); ?>">
            <?php
            foreach ( $params['items'] as $items ) {
                switch ( $items['type'] ) {
                    
                    case 'divider':
                            ?><li class="divider"></li><?php
                        break;

                    case 'text':
                            ?><li <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $items ); ?> ><?php echo wp_kses_post( $items['title'] ); ?></li><?php
                        break;

                    default:
                        ?><li><a <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $items ); ?> href="<?php echo esc_attr( $items['url'] ); ?>"><?php echo wp_kses_post( $items['title'] ); ?></a></li><?php
                        break;
                }
            }
            ?>
        </ul>
    </span><?php
}



////////////////////////////////////////////////////////////////////////////////
//  Vertical Buttons Group
////////////////////////////////////////////////////////////////////////////////

/**
	 * Devine Vertical Buttons Group
 * 
 * @param array $params
 * 
 *  Example:
    $params['btn_vm_calendar'] = array(
                                  'title' => ''
                                , 'hint' => array( 'title' => __('Timeline View' ,'booking') , 'position' => 'bottom' )
                                , 'selected' => ( $selected_view_mode == 'vm_calendar' ) ? true : false
                                , 'link' => $bk_admin_url . '&tab=vm_calendar'
                                , 'icon' => ''
                                , 'font_icon' => 'glyphicon glyphicon-calendar'
                            );

 */
function wpbc_bs_vertical_buttons_group( $params ) {        
          
    ?><div role="group" class="btn-group-vertical" aria-label="..." ><?php 

        foreach ( $params as $btn_id => $btn_params ) {

            ?><a    id="<?php echo esc_attr( $btn_id ); ?>"
                  <?php if ( ! empty( $btn_params['hint'] ) ) { ?>
                    title="<?php  echo esc_js( $btn_params['hint']['title'] ); ?>"
                  <?php 
                  /* data-original-title="<?php  echo esc_js( $btn_params['hint'] ); ?>" */                      
                  } ?>                        
                    class="button button-secondary <?php 
                            echo esc_attr( ( $btn_params['selected'] ) ? 'active ' : '' );
                            echo esc_attr( ( ! empty( $btn_params['hint'] ) ) ? 'tooltip_' . $btn_params['hint']['position'] . ' ' : '' );
                            echo esc_attr( ( ! empty( $btn_params['css_classes'] ) ) ? $btn_params['css_classes'] . ' ' : '' );
                        ?>" 
                    href="<?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $btn_params['link']; ?>"
                    onclick="javascript:void(0)" 
               ><?php 
               
                    // Icon
                    $is_icon_not_showed = false;                
                    if ( ! empty( $btn_params['icon'] ) ) {                                

                        if ( substr( $btn_params['icon'], 0, 4 ) != 'http')
                            $img_path = WPBC_PLUGIN_URL . '/assets/img/' . $btn_params['icon'];
                        else
                            $img_path = $btn_params['icon'];

						// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                        ?><img class="menuicons" src="<?php echo esc_attr( $img_path ); ?>" style="width:20px;" /><?php     // IMG  Icon

                    } elseif ( ! empty( $btn_params['font_icon'] ) ) {                     

                         ?><i class="menu_icon icon-1x <?php echo esc_attr( $btn_params['font_icon'] ); ?>"></i><?php              // Font Icon
                    } else {
                        $is_icon_not_showed = true;
                    }

                    // Text
                    if (  ( ! empty( $btn_params['title'] ) ) && ( ( $is_icon_not_showed ) )  ){
                        ?><span class="btn-group-vertical-text" >&nbsp;<?php echo wp_kses_post( $btn_params['title'] ); ?></span><?php
                    }

            ?></a><?php
        }
        
    ?></div><?php      
    
}



////////////////////////////////////////////////////////////////////////////////
//  Navigation Toolbar Tabs
////////////////////////////////////////////////////////////////////////////////

/**
	 * Display Tab in Navigation line
 * 
 * @param array $args
                        array(                       
                                  'title' => ''                                 // Title of TAB                           
                                , 'hint' => array( 'title' => '', 'position' => 'bottom' )          // Hint    
                                , 'link' => 'javascript:void(0)'                // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                                , 'onclick' => ''                               // JS action
                                , 'position' => ''                              // 'left'  ||  'right'  ||  ''
                                , 'css_classes' => ''                           // CSS class(es)
                                , 'icon' => ''                                  // Icon - link to the real PNG img
                                , 'font_icon' => ''                             // CSS definition  of forn Icon
                                , 'default' => false                            // Is it activated by default: true || false. 
                                , 'disabled' => false                           // Is this sub tab deactivated: true || false. 
                                , 'checkbox'  => false                          // false or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status', 'value' => '', 'onclick' => '' )
                                , 'top' => true                                 // Top or Bottom TAB: true || false. 
                        )
 */
function wpbc_bs_display_tab( $args = array() ) {

    $defaults = array(
            'top' => true, 
            'title' => '',
            'hint' => '',
            'link' => 'javascript:void(0)',
            'onclick' => '',
            'css_classes' => '',
            'text_css' 	  => '',
            'icon' => '',
            'font_icon' => '', 
            'checkbox' => false,
            'disabled' => false,
            'default' => false        
    );

    $tab = wp_parse_args( $args, $defaults );


    if (  ( ! empty( $tab['hint'] ) ) && ( is_string( $tab['hint'] ) )  ) {     // Compatibility with previous hint declaration

        if ( $tab['top'] === true ) $tab['hint'] = array( 'title' => $tab['hint'], 'position' => 'top' );
        else                        $tab['hint'] = array( 'title' => $tab['hint'], 'position' => 'bottom' );        
    }
    
    
    $html_tag = 'a';

    if ( $tab['disabled'] ) {
        $tab['link'] = 'javascript:void(0)';
        $html_tag = 'span';
    }
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '<' , $html_tag , ' ';                                                 // Start HTML Tag

    if ( $html_tag == 'a' ) {                                                   // Paramters for A tag

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo ' href="' , $tab['link'] ,  '" ';

        if ( ! empty( $tab['onclick'] ) ){
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ' onclick="javascript:' , $tab['onclick'] ,  '" ';
		}
    } 
    
    if ( ! empty( $tab['hint'] ) ) {                                            // Hint
        echo ' title="' . esc_attr( $tab['hint']['title'] ) . '" ';
    } 

    echo ' class="nav-tab';                                                     // CSS Classes

            if ( $tab['top'] !== true )         echo ' wpdevelop-submenu-tab'; 
            if ( $tab['default'] )              echo ' nav-tab-active';                     
            if ( $tab['disabled'] )             echo ' wpdevelop-tab-disabled';             
            if ( ! empty( $tab['hint'] ) )      echo ' tooltip_' . esc_attr( $tab['hint']['position'] );
            if ( ! empty( $tab['position'] ) )  echo ' nav-tab-position-' . esc_attr( $tab['position'] );
            if ( ! empty( $tab['hided'] ) )     echo ' hide'; 
                        
            echo ' ' . esc_attr( $tab['css_classes'] );

    echo '" ';
            

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_get_custom_attr( $tab );
    echo '>';                                                                   // Close  >  for A  or SPAN


        // Icon
        $is_icon_showed = true;                
        if ( ! empty( $tab['icon'] ) ) {                                

            if ( substr( $tab['icon'], 0, 4 ) != 'http')
                $img_path = WPBC_PLUGIN_URL . '/assets/img/' . $tab['icon'];
            else
                $img_path = $tab['icon'];

			// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
            ?><img class="menuicons" src="<?php echo esc_attr( $img_path ); ?>" style="width:20px;" /><?php     // IMG  Icon

        } elseif ( ! empty( $tab['font_icon'] ) ) {                     

             ?><i class="menu_icon icon-1x <?php echo esc_attr( $tab['font_icon'] ); ?>"></i><?php              // Font Icon
        } else {
            $is_icon_showed = false;
        }

        // Text

        ?><span class="<?php echo ( $is_icon_showed ) ? 'nav-tab-text' : '';  ?>" style="<?php echo esc_attr( $tab['text_css'] ); ?>" ><?php
			if ( ! empty( $tab['title'] ) ) {
				echo ( $is_icon_showed ) ? '&nbsp;&nbsp;' : '';
			}
            echo wp_kses_post( $tab['title'] );
        ?></span><?php                            

        // C h e c k b o x                                        
        if ( $tab['checkbox'] !== false ) {                                                                         
            ?><input type="checkbox" 
                    <?php if ( $tab['checkbox']['checked'] ) echo ' checked="CHECKED" '; ?>
                    name="<?php echo esc_attr( $tab['checkbox']['name'] ); ?>_dublicated"
                    id="<?php echo esc_attr( $tab['checkbox']['name'] ); ?>_dublicated"
                    value="<?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo (  isset( $tab['checkbox']['value'] ) ? $tab['checkbox']['value'] : ''  ); ?>"
                    onchange="javascript: <?php if (  isset( $tab['checkbox']['onclick'] )  ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $tab['checkbox']['onclick'];
						} else {
							?>if ( jQuery('#<?php echo esc_js( $tab['checkbox']['name'] ); ?>').length > 0 ) { document.getElementById('<?php echo esc_js( $tab['checkbox']['name'] ); ?>').checked = this.checked; }<?php
						}
						?>"
                    /><?php
        } 

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</' , $html_tag , '>';     


    if ( 
            ( isset( $tab['top'] ) ) && ( $tab['top'] ) 
            && ( isset( $tab['position'] ) ) && ( $tab['position'] !== 'right' )    // FixIn: 6.0.1.13.
        )
        echo '&nbsp;';
}

function wpbc_bs_toolbar_tabs_html_container_start() {
    
    ?>
    <div class="wpdvlp-top-tabs">
        <div class="wpdvlp-tabs-wrapper">
            <div class="nav-tabs" ><?php                                        // T O P    T A B S    
}

function wpbc_bs_toolbar_tabs_html_container_end() {
    
          ?></div><!-- nav-tabs -->            
        </div><!-- wpdvlp-tabs-wrapper -->
    </div><!-- wpdvlp-top-tabs --><?php    
}

function wpbc_bs_toolbar_sub_html_container_start() {
                                                                                // S U B    T A B S     or      U I  elements
    ?><div class="wpdvlp-sub-tabs">
        <div class="wpdvlp-tabs-wrapper">
            <div class="nav-tabs"><?php                                     
}

function wpbc_bs_toolbar_sub_html_container_end() {
    
          ?><div class="clear"></div>
            </div><!-- nav-tabs -->            
        </div><!-- wpdvlp-tabs-wrapper -->
    </div><!-- wpdvlp-sub-tabs --><?php    
}

// FlexTable :: Tabs in Flex Table  for switching view of Table content
function wpbc_flextable_header_tabs_html_container_start() {

	?><span class="wpbc_flextable_header_tabs">
		<div class="wpdvlp-top-tabs"><?php

}

function wpbc_flextable_header_tabs_html_container_end() {

          ?></div><!-- wpdvlp-top-tabs -->
    </span><!-- wpdevelop wpbc_flextable_header_tabs --><?php
}


/**
 * Get HTML  for Open / Close  [+/-] Links for help  sections
 *
 * @param $link_title  string
 * @param $is_open	bool
 * @param $params	array
 *
 * @return false|string
 *
 *                 Example 1:
 *								wpbc_get_open_close_link__container_start_html( array(
 *																			'title'   => __('Standard Shortcodes' ,'booking'),
 *                     														'is_open' => true,
 *																			'params'  => array(
 *																								'container_attr' => array( 'style' => 'font-weight:600;' ),
 *																								'content_attr'   => array( 'style' => 'font-weight:400;display:block;padding: 0px 5px;margin: 0;background: #fff;' )
 *																		)));
 *
 *                 Example 2:
 *								wpbc_get_open_close_link__container_start_html( array(
 *																			'title'   => __('Cost Hints' ,'booking'),
 *																			'params'  => array( 'container_class' => 'wpbc_deprected_help_container' )
 *																	  ));
 */
function wpbc_get_open_close_link__container_start_html( $params = array()  ) {

	$defaults = array(
	                  'title'   => ''
					, 'is_open' => false
					, 'params'  => array()
				);
	$params   = wp_parse_args( $params, $defaults );

	$defaults = array(
						'container_class' => '',
						'container_attr'  => array(),
						'link_class' 	  => '',
						'link_attr'  	  => array(),
						'content_class'   => '',
						'content_attr'    => array()
				);
	$params['params'] = wp_parse_args( $params['params'], $defaults );

	ob_start();

	$params['params']['container_attr'] = WPBC_Settings_API::get_custom_attr_static( array( 'attr' => $params['params']['container_attr'] ) );
	$params['params']['link_attr'] 	    = WPBC_Settings_API::get_custom_attr_static( array( 'attr' => $params['params']['link_attr'] ) );
	$params['params']['content_attr']   = WPBC_Settings_API::get_custom_attr_static( array( 'attr' => $params['params']['content_attr'] ) );

	?><div class="wpbc_container_open_or_closed <?php echo esc_attr( ( $params['is_open'] ?  '':' wpbc_container_closed ' ) );
													  echo esc_attr( $params['params']['container_class'] ); ?>" <?php
													  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													  echo $params['params']['container_attr']; ?>><?php

	 	?><a  href="javascript:void(0)" class="wpbc_container_open_or_closed__link <?php echo esc_attr( $params['params']['link_class'] ); ?>" <?php
	 	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	 	echo $params['params']['link_attr']; ?>><?php
	 		echo wp_kses_post( $params['title'] );
		?></a><?php
	  	?><div class="wpbc_container_open_or_closed__content <?php echo esc_attr( $params['params']['content_class'] ); ?>"
	  		   <?php
	  		   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	  		   echo ( $params['is_open'] ?  '':' style="display:none;" ' ); ?>
	  		   <?php
	  		   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	  		   echo $params['params']['content_attr']; ?>><?php

	return ob_get_clean();
}

/**
 * Get HTML  for Open / Close  [+/-] Links for help  sections
 *
 * @return false|string
 */
function wpbc_get_open_close_link__container_end_html() {

	ob_start();

          ?></div><!-- wpbc_container_open_or_closed__content -->
    </div><!-- wpbc_container_open_or_closed --><?php

    return ob_get_clean();
}


////////////////////////////////////////////////////////////////////////////////
//  HTML sections and groups
////////////////////////////////////////////////////////////////////////////////

/** Clear Div */
function wpbc_clear_div() {
    ?><div class="clear"></div><?php 
}



////////////////////////////////////////////////////////////////////////////////
//  JS & CSS 
////////////////////////////////////////////////////////////////////////////////
/** Tooltips JavaScript functions */
function wpbc_bs_javascript_tooltips() {
    
    ?><span id="wpbc_tooltips_container"></span><?php
    // FixIn: 7.0.1.10.
    ?><script type="text/javascript">

		function wpbc_define_tippy_tooltips( parent_class ){

			if ( 'function' !== typeof( wpbc_tippy ) ){
				console.log( 'WPBC Error. JavaScript library "wpbc_tippy" was not defined.' );
				return  false;
			}

			// FixIn: 9.0.1.1.
			/**
			 * Get Title to  show in tooltip.
			 * By  default getting text  from  'data-original-title' attribute,  if not exist,
			 * then  checking 'title' attribute and replacing it by 'data-original-title'
			 *
			 * @param reference  - Dom element
			 * @returns string
			 */
			function wpbc_get_tippy_tooltip_title_text( reference ){
				var text2show = reference.getAttribute( 'data-original-title' );
				if ( null == text2show ){
					text2show = reference.getAttribute( 'title' );
					jQuery( reference ).attr( "data-original-title", jQuery( reference ).attr( "title" ) );
					jQuery( reference ).removeAttr( "title" );
				}
				return text2show;
			}

			if ( undefined == parent_class ){
				parent_class = '';
			}

			wpbc_tippy( parent_class + '.tooltip_top', {
				content( reference ){
					return wpbc_get_tippy_tooltip_title_text( reference );
				},
				placement: 'top-start',
			} );
			wpbc_tippy( parent_class + '.tooltip_bottom', {
				content( reference ){
					return wpbc_get_tippy_tooltip_title_text( reference );
				},
				placement: 'bottom-start',
			} );
			wpbc_tippy( parent_class + '.tooltip_bottom', {
				content( reference ){
					return wpbc_get_tippy_tooltip_title_text( reference );
				},
				placement: 'bottom-start',
			} );
			wpbc_tippy( parent_class + '.tooltip_right', {
				content( reference ){
					return wpbc_get_tippy_tooltip_title_text( reference );
				},
				placement: 'right',
			} );
			// FixIn: 10.11.5.8.
			wpbc_tippy( parent_class + '.tooltip_right_offset', {
				content( reference ){
					return wpbc_get_tippy_tooltip_title_text( reference );
				},
				offset: [0, 25],
				placement: 'right',
			} );
			wpbc_tippy( parent_class + '.tooltip_left', {
				content( reference ){
					return wpbc_get_tippy_tooltip_title_text( reference );
				},
				placement: 'left-start',
			} );

			return true;
		}

		//jQuery( document ).ready( function (){
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpbc_jq_ready_start();
		?>
			wpbc_define_tippy_tooltips( '' );
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpbc_jq_ready_end();
		?>
		//} );
    </script><?php 
}
    
    
/** Popover JavaScript functions */
function wpbc_bs_javascript_popover() {    
	//FixIn: 10.2.0.4 //FixIn: 10.1.3.7 /* //FixIn: 9.0.1.1 */

	ob_start();

	//INFO: Important! Please be carefully with  comments such  as this //   it can  corrupt everything. Please use this commenting: /**/
	?> <script type="text/javascript"> <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_jq_ready_start();

		?> wpbc_define_tippy_popover(); <?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpbc_jq_ready_end();
	?> </script> <?php

    $content_popover = ob_get_clean();

	$content_popover = str_replace( array( "\n" , "\r" ), '', $content_popover);										// FixIn: 10.1.4.1.

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $content_popover;
}
    


////////////////////////////////////////////////////////////////////////////////
// S U P P O R T 
////////////////////////////////////////////////////////////////////////////////

/**
	 * Get custom atrributes for HTML elements
 * 
 * @param array $field
 * @return type
 */
function wpbc_get_custom_attr( $field ) {

        $attributes = array();

        if ( ! empty( $field['attr'] ) && is_array( $field['attr'] ) ) {

            foreach ( $field['attr'] as $attr => $attr_v ) {
                $attributes[] = esc_attr( $attr ) . '="' . esc_attr( $attr_v ) . '"';
            }
        }

        return implode( ' ', $attributes );
}