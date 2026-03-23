<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Simple Booking Form  - Save Changes - on Settings > Booking Form page
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-08-17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/*
 * Sanitize and Save changes
 */
function wpbc_simple_form__page_save_submit(){


    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_POST['reset_to_default_form'] ) && ( $_POST['reset_to_default_form']  == 'standard' ) ) {

		// == Custom Forms ==  Start			// Get  Custom  booking form
		if ( class_exists( 'wpdev_bk_biz_m' ) ) {
			$form_name = wpbc_get_sanitized_custom_booking_form_name_from_url();
			if ( ( ! empty( $form_name ) ) && ( 'standard' !== $form_name ) ) {

				$simple_form_configuration = wpbc_simple_form__visual__get_default_form();              // We are importing old structure to  have default booking form.

				wpbc_save_custom_booking_form__for_simple_form_mode( $form_name, $simple_form_configuration );

				wpbc_show_changes_saved_message();

				return;
			}
		}

		update_bk_option( 'booking_form_structure_type',  'vertical'  );
		update_bk_option( 'booking_form_layout_max_cols',  1  );

		$visual_form_structure = wpbc_simple_form__visual__get_default_form();              // We are importing old structure to  have default booking form.
		update_bk_option( 'booking_form_visual',  $visual_form_structure  );

        wpbc_show_changes_saved_message();

        return;
    }

	// -------------------------------------------------------------------------------------------------------------
    // Update booking form structure
    update_bk_option( 'booking_form_structure_type',  	WPBC_Settings_API::validate_text_post_static( 'booking_form_structure_type' )  );
    update_bk_option( 'booking_form_layout_max_cols',  	max( intval(WPBC_Settings_API::validate_text_post_static( 'booking_form_layout_max_cols' )),1)  );
	// -------------------------------------------------------------------------------------------------------------
	// Update Color Theme and skins/picker not in MU versions
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST[ 'booking_form_theme' ] ) ) {
		$wpbc_selected_theme = WPBC_Settings_API::validate_text_post_static( 'booking_form_theme' );
		update_bk_option( 'booking_form_theme', $wpbc_selected_theme );
		//if (! class_exists( 'wpdev_bk_multiuser' ) ){
			if ( 'wpbc_theme_dark_1' === $wpbc_selected_theme ) {
				update_bk_option( 'booking_skin', '/css/skins/24_9__dark_1.css' );
				update_bk_option( 'booking_timeslot_picker_skin', '/css/time_picker_skins/black.css' );
			}
			if ( '' === $wpbc_selected_theme ) {
				//update_bk_option( 'booking_skin', '/css/skins/green-01.css' );
                update_bk_option( 'booking_skin', '/css/skins/25_5__square_1.css' );               // FixIn: 10.4.0.1.
				//update_bk_option( 'booking_timeslot_picker_skin', '/css/time_picker_skins/grey.css' );
				update_bk_option( 'booking_timeslot_picker_skin', '/css/time_picker_skins/light__24_8.css' );      	// FixIn: 10.4.0.1.
			}
		//}
	}

	// -------------------------------------------------------------------------------------------------------------
    // Calendar skin
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_POST['set_calendar_skin'] ) ) {

	    $selected_calendar_skin = WPBC_Settings_API::validate_text_post_static( 'set_calendar_skin' );

		// FixIn: 10.3.0.5.
	    $upload_dir              = wp_upload_dir();
	    $custom_user_skin_folder = $upload_dir['basedir'];
	    $custom_user_skin_url    = $upload_dir['baseurl'];

	    $selected_calendar_skin = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL, $custom_user_skin_folder, $custom_user_skin_url ), '', $selected_calendar_skin );

	    // Check if this skin exist in the plugin  folder
	    if (
				( file_exists( 			WPBC_PLUGIN_DIR . $selected_calendar_skin ) )
			 || ( file_exists( $custom_user_skin_folder . $selected_calendar_skin ) )
		){
		    update_bk_option( 'booking_skin', $selected_calendar_skin );
	    }

    }
    // Calendar skin
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_POST['set_time_picker_skin'] ) ) {

	    $selected_calendar_skin = WPBC_Settings_API::validate_text_post_static( 'set_time_picker_skin' );

	    $selected_calendar_skin = str_replace( array( WPBC_PLUGIN_DIR, WPBC_PLUGIN_URL ), '', $selected_calendar_skin );

	    // Check if this skin exist in the plugin  folder
	    if ( file_exists( WPBC_PLUGIN_DIR . $selected_calendar_skin ) ) {
		    update_bk_option( 'booking_timeslot_picker_skin', $selected_calendar_skin );
	    }
    }

	// -------------------------------------------------------------------------------------------------------------

	$booking_form_layout_width = WPBC_Settings_API::validate_text_post_static( 'booking_form_layout_width' );
    $booking_form_layout_width = ( intval( $booking_form_layout_width ) <= 0 ) ? 100 : intval( $booking_form_layout_width );
    update_bk_option( 'booking_form_layout_width',  $booking_form_layout_width );

	$booking_form_layout_width_px_pr = WPBC_Settings_API::validate_text_post_static( 'booking_form_layout_width_px_pr' );
    if ( ! in_array( $booking_form_layout_width_px_pr, array( 'px', '%' ) ) ) {
	    $booking_form_layout_width_px_pr = '%';
    }
    update_bk_option( 'booking_form_layout_width_px_pr',  $booking_form_layout_width_px_pr);

    if ( wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {
	    update_bk_option( 'booking_is_use_autofill_4_logged_user', WPBC_Settings_API::validate_checkbox_post_static( 'booking_is_use_autofill_4_logged_user' ) );
	    update_bk_option( 'booking_timeslot_picker', WPBC_Settings_API::validate_checkbox_post_static( 'booking_timeslot_picker' ) );
    }

	// -------------------------------------------------------------------------------------------------------------
	//TODO: 2024-08-11 18:28 customize it differently  for each  booking forms
    // update_bk_option( 'booking_send_button_title',  WPBC_Settings_API::validate_text_post_static( 'booking_send_button_title_name' )  );

	// -------------------------------------------------------------------------------------------------------------
    $skip_obligatory_field_types = array( 'calendar', 'submit', 'email' );

	$if_exist_required = array(
		'rangetime',
		'captcha',
		'durationtime',
		'starttime',
		'endtime',
	);                                                                        //FixIn:  TimeFreeGenerator

    $visual_form_structure = array();

//        $visual_form_structure[] = array(
//                                      'type'     => 'calendar'
//                                    , 'obligatory' => 'On'
//                                );

	// Loop  all form  filds for saving them.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['form_field_name'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( $_POST['form_field_name'] as $field_key => $field_name ) {

			$form_field_type_val = wpbc_clean_text_value( $_POST['form_field_type'][ $field_key ] );  // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			$form_field_type_val = ( 'select-one' === $form_field_type_val ) ? 'selectbox-one' : $form_field_type_val;
			$form_field_type_val = ( 'select-multiple' === $form_field_type_val ) ? 'selectbox-multiple' : $form_field_type_val;

			$visual_form_structure[] = array(
				'type'              => $form_field_type_val,
				'name'              => wpbc_clean_text_value( $field_name ),
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				'obligatory'        => ( ( in_array( wpbc_clean_text_value( $_POST['form_field_type'][ $field_key ] ), $skip_obligatory_field_types ) ) ? 'On' : 'Off' ),
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				'active' => ( ( in_array( wpbc_clean_text_value( $_POST['form_field_type'][ $field_key ] ), $skip_obligatory_field_types ) ) ? 'On' : ( isset( $_POST['form_field_active'][ $field_key ] ) ? 'On' : 'Off' ) ),
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				'required' => ( ( in_array( wpbc_clean_text_value( $_POST['form_field_type'][ $field_key ] ), $skip_obligatory_field_types ) )
					? 'On'
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
					: ( ( in_array( wpbc_clean_text_value( $field_name ), $if_exist_required ) ) ? 'On' : ( isset( $_POST['form_field_required'][ $field_key ] ) ? 'On' : 'Off' ) ) ),
				'if_exist_required' => ( ( in_array( wpbc_clean_text_value( $field_name ), $if_exist_required ) ) ? 'On' : 'Off' ),
				'label'             => WPBC_Settings_API::validate_text_post_static( 'form_field_label', $field_key ),
				'value'             => WPBC_Settings_API::validate_text_post_static( 'form_field_value', $field_key ),
			);

			if ( 'captcha' === $visual_form_structure[ ( count( $visual_form_structure ) - 1 ) ]['type'] ) {
				if ( wpbc_is_mu_user_can_be_here( 'only_super_admin' ) ) {
					update_bk_option( 'booking_is_use_captcha', ( 'On' === $visual_form_structure[ ( count( $visual_form_structure ) - 1 ) ]['active'] ) ? 'On' : 'Off' );
				} else {
					// For regular  users we set Captcha settings from  Super booking admin.
					$visual_form_structure[ ( count( $visual_form_structure ) - 1 ) ]['active'] = get_option( 'booking_is_use_captcha' );
				}
			}
		}
	}

//        $visual_form_structure[] = array(
//                                      'type'     => 'captcha'
//                                    , 'name'     => 'captcha'
//                                    , 'obligatory' => 'On'
//                                    , 'active'   => get_bk_option( 'booking_is_use_captcha' )
//                                    , 'required' => 'On'
//                                    , 'label'    => ''
//                                );
//        $visual_form_structure[] = array(
//                                      'type'     => 'submit'
//                                    , 'name'     => 'submit'
//                                    , 'obligatory' => 'On'
//                                    , 'active'   => 'On'
//                                    , 'required' => 'On'
//                                    , 'label'    => WPBC_Settings_API::validate_text_post_static( 'booking_send_button_title_name' )//get_bk_option( 'booking_send_button_title' )  						//FixIn:  8.8.1.14		// __('Send', 'booking')
//                                );


	if(
		( function_exists( 'wpbc_is_custom_booking_form_submitted__for_simple_form_mode' ) ) &&
		( wpbc_is_custom_booking_form_submitted__for_simple_form_mode() ) &&
		( ! empty( wpbc_get_sanitized_custom_booking_form_name_from_url() ) ) &&
		( 'standard' !== wpbc_get_sanitized_custom_booking_form_name_from_url()  )
	){

		$_POST['booking_form']      = str_replace( '\\n\\', '', wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) );
		$_POST['booking_form_show'] = str_replace( '\\n\\', '', wpbc_simple_form__get_form_show__as_shortcodes( $visual_form_structure ) );

		make_bk_action( 'wpbc_make_save_custom_booking_form' );			// Check here: wpbc_make_save_custom_booking_form()

		$form_name = wpbc_get_sanitized_custom_booking_form_name_from_url();
		wpbc_save_custom_booking_form__for_simple_form_mode( $form_name, $visual_form_structure );

	} else {

		update_bk_option( 'booking_form_visual',  $visual_form_structure  );
		update_bk_option( 'booking_form',      str_replace( '\\n\\', '', wpbc_simple_form__get_booking_form__as_shortcodes( $visual_form_structure ) ) );
		update_bk_option( 'booking_form_show', str_replace( '\\n\\', '', wpbc_simple_form__get_form_show__as_shortcodes() ) );
	}



    // FixIn: 9.8.6.1.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( ! empty( $_POST['form_visible_section'] ) ) {
		?><script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery( '<?php
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					echo esc_js( $_POST['form_visible_section'] );
					?> a' ).trigger('click');
			});
		</script><?php
    }


    if ( class_exists( 'wpdev_bk_personal' ) ) {
	    $is_use_simgple_form = get_bk_option( 'booking_is_use_simple_booking_form' );
	    if ( 'Off' === $is_use_simgple_form ) {
		    ?><script type="text/javascript"> window.location.href = '<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo ( wpbc_get_settings_url() ) . '&tab=form'; ?>'; </script><?php
	    }
    }

    wpbc_show_changes_saved_message();

	// To  refresh  the Calendar skin we need to  reload the page
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
    if ( isset( $_POST['booking_form_theme'] ) ) {
	    $url = wpbc_get_settings_url() . '&tab=form';
	    if ( function_exists( 'wpbc_get_sanitized_custom_booking_form_name_from_url' ) ) {
		    $url .= '&booking_form=' . wpbc_get_sanitized_custom_booking_form_name_from_url();
	    }
		?>
		<script type="text/javascript"> window.location.href = '<?php
				// phpcs:ignore WordPress.Security.EscapeOutput
				echo $url;
				?>'; </script><?php
    }
}
