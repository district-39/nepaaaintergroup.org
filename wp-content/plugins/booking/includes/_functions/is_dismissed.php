<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage  Is Dismissed
 * @category    Functions
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-09-03
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


// =====================================================================================================================
// ==  Is Dismissed  ==
// =====================================================================================================================


/**
 * Only  check  if Dismised or visible this section
 *
 * @param $element_html_id
 *
 * @return bool
 */
function wpbc_is_dismissed_panel_visible( $element_html_id ) {
	return ( '1' != get_user_option( 'booking_win_' . $element_html_id ) );

}

/**
 * Show dismiss close button  for specific HTML section
 *
 * @param  string $element_html_id   - ID of HTML selection  to  dismiss
 * @param  array  $params			 - array( 'title' => 'Dismiss', 'is_apply_in_demo' => false ) )
 *
 * @return bool
 *
 *    Examples:
 *				$is_dismissed = wpbc_is_dismissed( 'wpbc_dashboard_section_video_f' );
 *
 *              $is_dismissed = wpbc_is_dismissed( 'html_id', array( 'is_apply_in_demo' => $is_apply_in_demo ) );
 *
 *          $is_panel_visible = wpbc_is_dismissed( 'wpbc-panel-get-started', array(
 *             									   'title' => '<i class="menu_icon icon-1x wpbc_icn_close"></i> ',
 *             									   'hint'  => __( 'Dismiss', 'booking' ),
 *             									   'class' => 'wpbc_panel_get_started_dismiss',
 *             									   'css'   => ''
 * 											));
 */
function wpbc_is_dismissed( $element_html_id, $params = array() ){														// FixIn: 8.1.3.10.

	$defaults = array(
						  'title' => '&times;'
						, 'hint' => __( 'Dismiss' ,'booking')
						, 'is_apply_in_demo' => ! wpbc_is_this_demo()
						, 'class' => 'wpbc_x_dismiss_btn'									// CSS class of  close X element
						, 'css' => ''									// Style class of  close X element
						, 'dismiss_css_class'  => '' 					//'.'.$id . '_' . 'weekdays_conditions'
				);
	$params = wp_parse_args( $params, $defaults );


	$params['css'] = 'text-decoration: none;font-weight: 600;float:right;' . $params['css'];							// Append CSS instead of replace it

	if ( ( class_exists( 'WPBC_Dismiss' )) && ( $params[ 'is_apply_in_demo' ] ) ) {

		global $wpbc_Dismiss;

		$is_panel_visible = $wpbc_Dismiss->render( 	array(
															'id'                => $element_html_id,
															'title'             => $params['title'],
															'hint'              => $params['hint'],
															'class'             => $params['class'],
															'css'               => $params['css'],
															'dismiss_css_class' => $params['dismiss_css_class']
												) );
	} else {
		$is_panel_visible = false;
	}

	return $is_panel_visible;
}


class WPBC_Dismiss {

    public  $element_id;
    public  $title;
    public  $hint;
    public  $html_class;
    public  $css;
	public  $dismiss_css_class;

    public function __construct( ) {

    }

    public function render( $params = array() ){
        if (isset($params['id']))
                $this->element_id = $params['id'];
        else    return  false;                                                  // Exit, because we do not have ID of element

        if (isset($params['title']))
                $this->title = $params['title'];
        else    $this->title = __( 'Dismiss'  ,'booking');

        if (isset($params['hint']))
                $this->hint = $params['hint'];
        else    $this->hint = __( 'Dismiss'  ,'booking');

        if (isset($params['class']))
                $this->html_class = $params['class'];
        else    $this->html_class = 'wpbc-panel-dismiss';

        if (isset($params['css']))
                $this->css = $params['css'];
        else    $this->css = 'text-decoration: none;font-weight: 600;';

        if (isset($params['dismiss_css_class']))
                $this->dismiss_css_class = $params['dismiss_css_class'];
        else    $this->dismiss_css_class = '';

        return $this->show();
    }

    public function show() {

	    // Check if this window is already Hided or not
		if ( '1' == get_user_option( 'booking_win_' . $this->element_id ) ){     // Panel Hided

			echo '<script type="text/javascript"> jQuery(document).ready(function(){ ';
			if ( ! empty( $this->element_id ) ) {
				echo ' jQuery( "#' . esc_attr( $this->element_id ) . '" ).hide(); ';
			}
			if ( ! empty( $this->dismiss_css_class ) ) {
				echo ' jQuery( "' . esc_attr( $this->dismiss_css_class ) . '" ).hide(); ';
			}
			echo ' }); </script>';

			return false;

		} else {                                                                  // Show Panel
			echo '<script type="text/javascript"> jQuery(document).ready(function(){ ';

			if ( ! empty( $this->element_id ) ) {
				echo ' jQuery( "#' . esc_attr( $this->element_id ) . '" ).show(); ';
			}
			if ( ! empty( $this->dismiss_css_class ) ) {
				echo ' jQuery( "' . esc_attr( $this->dismiss_css_class ) . '" ).show(); ';
			}
			echo ' }); </script>';
        }

        wp_nonce_field('wpbc_ajax_admin_nonce',  "wpbc_admin_panel_dismiss_window_nonce" ,  true , true );
        // Show Hide link
        ?><a class="<?php echo esc_attr( $this->html_class ); ?>"  style="<?php echo esc_attr( $this->css ); ?>"
			 title="<?php echo esc_attr( $this->hint ); ?>"
			 href="javascript:void(0)"
             onclick="javascript: if ( typeof( wpbc_hide_window ) == 'function' ) {
				 	wpbc_hide_window('<?php echo esc_attr( $this->element_id ); ?>');
				 	wpbc_dismiss_window(<?php echo esc_attr( wpbc_get_current_user_id() ); ?>, '<?php echo esc_attr( $this->element_id ); ?>');
				 	jQuery( this ).hide(); <?php
				 	if ( ! empty( $this->dismiss_css_class ) ) {
					    echo "jQuery('" . esc_attr($this->dismiss_css_class) . "').slideUp(500);";
				    }
			 		?>
				 } else {  <?php
             		echo "jQuery('#" . esc_attr( $this->element_id ) . "').slideUp(500);";
				 	if ( ! empty( $this->dismiss_css_class ) ) {
					    echo "jQuery('" . esc_attr($this->dismiss_css_class) . "').slideUp(500);";
				    }
			  ?> }"
          ><?php
		echo wp_kses_post( $this->title ); ?></a><?php

	    return true;
    }
}

if( is_admin() ) {
	global $wpbc_Dismiss;
	$wpbc_Dismiss = new WPBC_Dismiss();
}