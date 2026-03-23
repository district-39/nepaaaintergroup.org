<?php /**
 * @version 1.0
 * @package  Booking Calendar
 * @category Booking Calendar Widgets
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-10-08
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly           // FixIn: 10.6.2.1.

// BookingWidget Class
class BookingWidget extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = 'Booking Calendar' );
	}

    /** @see WP_Widget::widget */
	function widget( $args, $instance ) {

		extract( $args );

		// FixIn: 6.1.1.11.
		$booking_widget_title = ( isset( $instance['booking_widget_title'] ) ) ? apply_filters( 'widget_title', $instance['booking_widget_title'] ) : __( 'Booking Calendar', 'booking' );
		if ( function_exists( 'icl_translate' ) ) {
			$booking_widget_title = icl_translate( 'wpml_custom', 'wpbc_custom_widget_booking_title1', $booking_widget_title );
		}

		$booking_widget_show = ( isset( $instance['booking_widget_show'] ) ) ? $instance['booking_widget_show'] : 'booking_form';

		$resource_id = ( isset( $instance['booking_widget_type'] ) ) ? intval( $instance['booking_widget_type'] ) : 1;
		$resource_id = ( empty( $resource_id ) ) ? 1 : $resource_id;

		$booking_widget_calendar_count = ( isset( $instance['booking_widget_calendar_count'] ) ) ? intval( $instance['booking_widget_calendar_count'] ) : 1;
		$booking_widget_last_field     = ( isset( $instance['booking_widget_last_field'] ) ) ? $instance['booking_widget_last_field'] : '';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $before_widget ;

	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	    if ( isset( $_GET['booking_hash'] ) ) {
		    esc_html_e( 'You need to use special shortcode [bookingedit] for booking editing.', 'booking' );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		    echo  $after_widget ;
		    return;
	    }

	    if ( $booking_widget_title != '' ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		    echo  $before_title . wp_kses_post( $booking_widget_title )  . $after_title ;
	    }

        echo "<div class='widget_wpdev_booking wpdevelop months_num_in_row_1'>";                                        // FixIn: 8.4.2.3.

		if ( 'booking_form' === $booking_widget_show ) {

			$my_booking_form_name = apply_bk_filter( 'wpbc_get_default_custom_form', 'standard', $resource_id );

			// Parse, sanitize and normilize shortcode paramaters.
			$shortcode_params = array(
				'is_echo'             => 1,
				'resource_id'         => $resource_id,
				'cal_count'           => $booking_widget_calendar_count,
				'custom_booking_form' => $my_booking_form_name,
			);

			$shortcode_result = WPBC_FE_Render::render_booking_form( $shortcode_params );

		} else {
			echo "<div class='wpbc_only_calendar wpbc_container'>";                                                       // FixIn: 8.0.1.2.
			echo "<div id='calendar_booking_unselectable" . esc_attr( $resource_id ) . "'></div>";                        // FixIn: 6.1.1.13.

			WPBC_FE_Render::render_calendar_only(
				array(
					'resource_id' => $resource_id,
					'cal_count'   => $booking_widget_calendar_count,
					'is_echo'     => 1,
				)
			);

			echo '</div>';
		}

	    if ( $booking_widget_last_field !== '' ) {
		    echo '<br/>' . esc_js( $booking_widget_last_field );
	    }
	    echo "</div>";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	    echo $after_widget;
    }

    /** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['booking_widget_title']          = sanitize_textarea_field( $new_instance['booking_widget_title'] );
		$instance['booking_widget_show']           = sanitize_textarea_field( $new_instance['booking_widget_show'] );
		$instance['booking_widget_type']           = ( empty( $new_instance['booking_widget_type'] ) ? 1 : intval( $new_instance['booking_widget_type'] ) );
		$instance['booking_widget_calendar_count'] = intval( $new_instance['booking_widget_calendar_count'] );
		$instance['booking_widget_last_field']     = $new_instance['booking_widget_last_field'];

		return $instance;
	}

    /** @see WP_Widget::form */
    function form($instance) {

	    if ( isset( $instance['booking_widget_title'] ) ) {
		    $booking_widget_title = esc_attr( $instance['booking_widget_title'] );
	    } else {
		    $booking_widget_title = '';
	    }
	    if ( isset( $instance['booking_widget_show'] ) ) {
		    $booking_widget_show = esc_attr( $instance['booking_widget_show'] );
	    } else {
		    $booking_widget_show = '';
	    }
	    if ( ( class_exists( 'wpdev_bk_personal' ) ) && ( isset( $instance['booking_widget_type'] ) ) ) {
		    $resource_id = intval( $instance['booking_widget_type'] );
	    } else {
		    $resource_id = 1;
	    }
	    if ( isset( $instance['booking_widget_calendar_count'] ) ) {
		    $booking_widget_calendar_count = esc_attr( $instance['booking_widget_calendar_count'] );
	    } else {
		    $booking_widget_calendar_count = 1;
	    }
	    if ( isset( $instance['booking_widget_last_field'] ) ) {
		    $booking_widget_last_field = esc_attr( $instance['booking_widget_last_field'] );
	    } else {
		    $booking_widget_last_field = '';
	    }
	    ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('booking_widget_title') ); ?>"><?php esc_html_e('Title' ,'booking'); ?>:</label><br/>
            <input value="<?php echo esc_attr( $booking_widget_title ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name('booking_widget_title') ); ?>"
                   id="<?php echo esc_attr( $this->get_field_id('booking_widget_title') ); ?>"
                   type="text" class="widefat" style="width:100%;line-height: 1.5em;" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('booking_widget_show') ); ?>"><?php esc_html_e('Show' ,'booking'); ?>:</label><br/>
            <select
                   name="<?php echo esc_attr( $this->get_field_name('booking_widget_show') ); ?>"
                   id="<?php echo esc_attr( $this->get_field_id('booking_widget_show') ); ?>" style="width:100%;line-height: 1.5em;">
                <option <?php if($booking_widget_show == 'booking_form') echo "selected"; ?> value="booking_form"><?php esc_html_e('Booking form with calendar' ,'booking'); ?></option>
                <option <?php if($booking_widget_show == 'booking_calendar') echo "selected"; ?> value="booking_calendar"><?php esc_html_e('Only availability calendar' ,'booking'); ?></option>
            </select>
        </p>


        <?php
        if ( class_exists('wpdev_bk_personal')) {
            $types_list = wpbc_get_br_as_objects(); ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('booking_widget_type') ); ?>"><?php esc_html_e('Booking resource' ,'booking'); ?>:</label><br/>
                <!--input id="calendar_type"  name="calendar_type" class="input" type="text" -->
                <select
                       name="<?php echo esc_attr( $this->get_field_name('booking_widget_type') ); ?>"
                       id="<?php echo esc_attr( $this->get_field_id('booking_widget_type') ); ?>"
                       style="width:100%;line-height: 1.5em;">
                            <?php foreach ($types_list as $tl) { ?>
                    <option  <?php if($resource_id == $tl->id ) echo "selected"; ?>
                        style="<?php if  (isset($tl->parent)) if ($tl->parent == 0 ) { echo 'font-weight:600;'; } else { echo 'font-size:11px;padding-left:20px;'; } ?>"
                        value="<?php echo esc_attr( $tl->id ); ?>"><?php echo esc_html( $tl->title ); ?></option>
                                <?php } ?>
                </select>

            </p>
        <?php } ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('booking_widget_calendar_count') ); ?>"><?php esc_html_e('Visible months' ,'booking'); ?>:</label><br/>

            <select style="width:100%;line-height: 1.5em;"
                    name="<?php echo esc_attr( $this->get_field_name('booking_widget_calendar_count') ); ?>"
                    id="<?php echo esc_attr( $this->get_field_id('booking_widget_calendar_count') ); ?>"
            >
	            <?php foreach ( array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ) as $tl ) { ?>
					<option
						<?php if ( $booking_widget_calendar_count == $tl ) { echo "selected"; } ?>
						style="font-weight:600;"
						value="<?php echo esc_attr( $tl ); ?>"><?php echo esc_html( $tl ); ?></option>
	            <?php } ?>
			</select>
		</p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('booking_widget_last_field') ); ?>"><?php esc_html_e('Footer' ,'booking'); ?>:</label><br/>
            <input value="<?php echo esc_attr( $booking_widget_last_field ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name('booking_widget_last_field') ); ?>"
                   id="<?php echo esc_attr( $this->get_field_id('booking_widget_last_field') ); ?>"
                   type="text" style="width:100%;line-height: 1.5em;" /><br/>
			<em style="font-size:11px;"><?php
				/* translators: 1: ... */
				echo wp_kses_post( sprintf( __( 'Example: %1$sMake booking here%2$s', 'booking' ), "<code>&lt;a href='" . get_site_url() . "'&gt;", '&lt;/a&gt;</code>' ) ); ?></em>
		</p>

		<p style="font-size:10px;"><?php

		/* translators: 1: ... */
		echo wp_kses_post( sprintf( __( '%1$sImportant!!!%2$s Please note, if you show booking calendar (inserted into post/page) with widget at the same page, then the last will not be visible.', 'booking' ), '<strong>', '</strong>' ) );

			if ( ! class_exists( 'wpdev_bk_personal' ) ) {

				?><em><?php
				/* translators: 1: ... */
				echo wp_kses_post( sprintf( __( '%1$sSeveral widgets are supported at %2$spaid versions%3$s.', 'booking' ), '<span style="">', '<a href="https://wpbookingcalendar.com/" target="_blank" style="text-decoration:none;color:#3A5670;">', '</a>', '</span>' ) );
				?></em> <?php
	    	}
	    ?></p><?php
    }
}


function wpbc_register_wpbc_widget() {
	// FixIn: 8.1.3.18.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	register_widget( "BookingWidget" );
}
add_action( 'widgets_init', 'wpbc_register_wpbc_widget' );