<?php /**
 * @version 1.0
 * @description Steps Structure for Setup Wizard Page
 * @category    Setup Class
 * @author wpdevelop
 *
 * @web-site http://oplugins.com/
 * @email info@oplugins.com
 *
 * @modified 2024-09-06
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_SETUP_WIZARD_STEPS {

	private $steps_arr = array();

	public function __construct() {

		if ( WPBC()->is_wp_inited() ) {
			$this->init_steps_data();
		} else {
			add_action( 'init', array( $this, 'init_steps_data' ) );
		}

		add_action( 'wpbc_after_wpbc_page_top__header_tabs', array( $this, 'show_top_right_wizard_button' ), 10, 3 );

	}


	/**
	 * Define Steps Data Structure  -  Init
	 *
	 * @return void
	 */
	public function init_steps_data(){

		$step_default_params = array(
										'show_section_left'  => false,
										'show_section_right' => false,
										'is_done'            => false,
										'do_action'          => 'none',
										'prior'              => '',
										'next'               => '',
										'prior_title' 		 => __( 'Go Back', 'booking' ),
										'next_title' 		 => __( 'Save and Continue', 'booking' )
									);
		$steps_arr = array();


		// Step #1
		$step_name = 'welcome';
		$steps_arr[ $step_name ] = $step_default_params;
		$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__welcome';
		$steps_arr[ $step_name ]['next'] = ( wpbc_is_this_demo() ) ? 'date_time_formats' : 'general_info';
		//$steps_arr[ $step_name ]['next_title'] = __( 'Let\'s Get Started', 'booking' );

		if ( ! wpbc_is_this_demo() ) {
			// Step #2
			$step_name                            = 'general_info';
			$steps_arr[ $step_name ]              = $step_default_params;
			$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__general_info';
			//$steps_arr[ $step_name ]['prior']  = 'bookings_types';
			$steps_arr[ $step_name ]['next'] = 'date_time_formats';
		}

		// Step #3
		$step_name = 'date_time_formats';
		$steps_arr[ $step_name ] = $step_default_params;
		$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__date_time_formats';
		if ( ! wpbc_is_this_demo() ) {
			$steps_arr[ $step_name ]['prior'] = 'general_info';
		}
		$steps_arr[ $step_name ]['next']   = 'bookings_types';

		// Step #4
		$step_name = 'bookings_types';
		$steps_arr[ $step_name ] = $step_default_params;
		$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__bookings_types';
		$steps_arr[ $step_name ]['prior']  = 'date_time_formats';
		$steps_arr[ $step_name ]['next']   = 'form_structure';

		// Step #5
		$step_name = 'form_structure';
		$steps_arr[ $step_name ] = $step_default_params;
		$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__form_structure';
		$steps_arr[ $step_name ]['prior']  = 'bookings_types';
		$steps_arr[ $step_name ]['next']   = 'cal_availability';
		$steps_arr[ $step_name ]['next_title'] = __( 'Continue', 'booking' );

		// Step #6
		$step_name = 'cal_availability';
		$steps_arr[ $step_name ] = $step_default_params;
		$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__cal_availability';
		$steps_arr[ $step_name ]['prior']  = 'form_structure';
		$steps_arr[ $step_name ]['next']   = 'color_theme';
		$steps_arr[ $step_name ]['next_title'] = __( 'Continue', 'booking' );

		// Step #7
		$step_name = 'color_theme';
		$steps_arr[ $step_name ] = $step_default_params;
		$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__color_theme';
		$steps_arr[ $step_name ]['prior']  = 'cal_availability';
		$steps_arr[ $step_name ]['next']   = 'optional_other_settings';

		// Step #8
		$step_name = 'optional_other_settings';
		$steps_arr[ $step_name ] = $step_default_params;
		//$steps_arr[ $step_name ]['show_section_left']  = true;
		//$steps_arr[ $step_name ]['show_section_right'] = true;
		$steps_arr[ $step_name ]['do_action'] 	= 'save_and_continue__optional_other_settings';
		$steps_arr[ $step_name ]['prior'] 	= 'color_theme';
		$steps_arr[ $step_name ]['next'] 	= ( wpbc_is_this_demo() ) ? 'get_started' : 'wizard_publish';
		$steps_arr[ $step_name ]['next_title'] = __( 'Continue', 'booking' );

		if ( ! wpbc_is_this_demo() ) {
			// Step #9
			$step_name               = 'wizard_publish';
			$steps_arr[ $step_name ] = $step_default_params;
			//$steps_arr[ $step_name ]['show_section_left']  = true;
			//$steps_arr[ $step_name ]['show_section_right'] = true;
			$steps_arr[ $step_name ]['do_action'] = 'save_and_continue__wizard_publish';
			$steps_arr[ $step_name ]['prior']     = 'optional_other_settings';
			$steps_arr[ $step_name ]['next']      = 'get_started';
			$steps_arr[ $step_name ]['next_title'] = __( 'Continue', 'booking' );
		}

		// Step #10
		$step_name = 'get_started';
		$steps_arr[ $step_name ] = $step_default_params;
		//$steps_arr[ $step_name ]['show_section_left']  = true;
		//$steps_arr[ $step_name ]['show_section_right'] = true;
		$steps_arr[ $step_name ]['do_action'] 	= 'save_and_continue__get_started';
		$steps_arr[ $step_name ]['prior'] 	= ( wpbc_is_this_demo() ) ? 'optional_other_settings' : 'wizard_publish';
		$steps_arr[ $step_name ]['next'] 	= 'welcome';
		//$steps_arr[ $step_name ]['next_title'] = __( 'Complete', 'booking' );

		$this->steps_arr = $steps_arr;
	}

	/**
	 * Get Steps Data Structure
	 * @return array
	 */
	public function get_steps_arr(){
	    return $this->steps_arr;
	}


	// =================================================================================================================
	// ==  Steps STRUCTURE  ==
	// =================================================================================================================

	/**
	 * Actual Step Number  -> 'general_info'        or      'optional_other_settings'
	 *
	 * @return int
	 */
	public function get_active_step_name() {

		$steps_arr = $this->db__get_steps_is_done();

		$first_step_name = '';
		foreach ( $steps_arr as $step_name => $step ) {

			$first_step_name =  (empty($first_step_name)) ? $step_name : $first_step_name;

			if ( empty( $step ) ) {
				return $step_name;
			}
		}
		return $first_step_name;
	}


	/**
	 * Actual Step Number  -> 2
	 *
	 * @return int
	 */
	public function get_active_step_num() {

		$steps_arr = $this->db__get_steps_is_done();
		$active_step   = 0;
		foreach ( $steps_arr as $step ) {

			if ( ! empty( $step ) ) {
				$active_step++;
			}
		}
		return $active_step;
	}


	/**
	 * Get Steps Count  -> 9
	 *
	 * @return int
	 */
	public function get_total_steps_count(){

		$steps_arr = $this->db__get_steps_is_done();

		return count($steps_arr);
	}


	/**
	 * Get % Progress for Setup Steps   -> 30
	 * @return int
	 */
	public function get_progess_value() {

		$progess_value = ( ($this->get_active_step_num()-0) * 100 ) / $this->get_total_steps_count();
		$progess_value = intval( $progess_value );

		return $progess_value;
	}


	// =================================================================================================================
	// ==  DB :: "Set Wizard Steps as Done"  ==
	// =================================================================================================================

	/**
	 * Get  all Steps from DB and from  Structure,
	 *
	 *    If not saved yet to DB,  then  get  default structure
	 *
	 *  And if later Wizard structure    ->init_steps_data()        was extended,  then  system  get  such  steps as uncompleted.
	 *
	 * @return array|mixed
	 */
	public function db__get_steps_is_done() {

		$steps_is_done = get_bk_option( 'booking_setup_wizard_page_steps_is_done' );

		if ( empty( $steps_is_done ) ) {
			// Get Default Steps  from  the Steps structure

			$steps_names = $this->get_steps_arr();
			$steps_names = array_keys( $steps_names );

			$steps_values  = array_fill( 0, count( $steps_names ), false );
			$steps_is_done = array_combine( $steps_names, $steps_values );
		} else {

			// Check  if some new steps here ?
			$possible_new_steps_names = $this->get_steps_arr();
			$possible_new_steps_names = array_keys( $possible_new_steps_names );
			foreach ( $possible_new_steps_names as $possible_new_steps_name ) {
				if ( ! isset( $steps_is_done[ $possible_new_steps_name ] ) ) {
					$steps_is_done[ $possible_new_steps_name ] = false;
				}
			}

		}

		return $steps_is_done;
	}


	/**
	 * Save statuses to  all steps
	 *
	 * @param $steps_arr
	 *
	 * @return void
	 */
	public function db__save_steps_is_done( $steps_arr ) {
		update_bk_option( 'booking_setup_wizard_page_steps_is_done', $steps_arr );
	}

	/**
	 * Set specific step  as Completed
	 *
	 * @param $step_name
	 *
	 * @return void
	 */
	public function db__set_step_as_completed( $step_name ) {

		$steps_arr = $this->db__get_steps_is_done();

		$steps_arr[ $step_name ] = true;

		$this->db__save_steps_is_done( $steps_arr );
	}

	/**
	 * Set specific step  as Uncompleted
	 *
	 * @param $step_name
	 *
	 * @return void
	 */
	public function db__set_step_as_uncompleted( $step_name ) {

		$steps_arr = $this->db__get_steps_is_done();

		$steps_arr[ $step_name ] = false;

		$this->db__save_steps_is_done( $steps_arr );
	}

	/**
	 * Check if specific step 'Is completed' ?
	 *
	 * @param string $step_name
	 *
	 * @return bool
	 */
	public function db__is_step_completed( $step_name ) {

		$steps_arr = $this->db__get_steps_is_done();

		if ( empty( $steps_arr[ $step_name ] ) ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * Mark All Steps as Completed or Uncompleted
	 *
	 * @param $is_completed  bool  (default true)
	 *
	 * @return void
	 */
	public function db__set_all_steps_as( $is_completed = true ) {

		if ( false === $is_completed ) {
			delete_bk_option( 'booking_setup_wizard_page_steps_is_done' );
		}

		$steps_names = $this->db__get_steps_is_done();

		$steps_names   = array_keys( $steps_names );
		$steps_values  = array_fill( 0, count( $steps_names ), $is_completed );
		$steps_is_done = array_combine( $steps_names, $steps_values );

		// Set  all  steps as not completed
		$this->db__save_steps_is_done( $steps_is_done );
	}

	/**
	 * Check  Is  all steps Completed
	 * @return bool
	 */
	public function db__is_all_steps_completed() {

		$steps_arr = $this->db__get_steps_is_done();

		foreach ( $steps_arr as $step_name => $steps_val ) {
			if ( empty( $steps_val ) ) {
				return false;
			}
		}

		return true;
	}



	// =================================================================================================================
	// ==  C O N T E N T  ==
	// =================================================================================================================

	// ----------------------------------------------------------
	// ==  Left Plugin Menu  -->  "Setup" with Progress Bar  ==
	// ----------------------------------------------------------

	/**
	 * Main Left Menu Title - "Setup" with Progress Bar
	 *
	 * @return false|string
	 */
	public function get_plugin_menu_title__setup_progress(){


		ob_start();

		?><div class="setup_wizard_page_container" style="display: flex;flex-flow: row wrap;justify-content: flex-start;align-items: center;color: #fff;margin: 0 -5px 0 0;overflow: visible;">
			<div class="name_item" style="margin-top: 0;white-space: nowrap;padding: 0 0 0 0;"><?php esc_html_e( 'Setup', 'booking' ); ?></div>
			<div style="margin:3px 0px 0 0;margin-left: auto;font-size: 9px;background: #2271b1;height: 15px;" class="wpbc_badge_count name_item update-plugins">
				<span class="update-count" style="white-space: nowrap;word-wrap: normal;"><?php
					echo esc_html( $this->get_active_step_num() . ' / ' . $this->get_total_steps_count() );
				?></span>
			</div>
			<div class="progress_line_container" style="width: 100%;border: 0px solid #757575;height: 3px;border-radius: 6px;margin: 7px 0 -3px -3px;overflow: hidden;background: #555;">
				<div class="progress_line" style="font-size: 6px;font-weight: 600;word-wrap: normal;border-radius: 6px;white-space: nowrap;background: #8ECE01;width: <?php
					echo esc_html( $this->get_progess_value() ); ?>%;height: 3px;"></div>
			</div>
		</div><?php

		return ob_get_clean();
	}

	// ----------------------------------------------------------
	// ==  Content Top Wizard Button  ==
	// ----------------------------------------------------------

	/**
	 * Black Button at  Top Right Side in WPBC plugin menu  ( except Wizard page )
	 *
	 * Show Continue Setup Wizard Button
	 * @return void
	 */
	public function show_top_right_wizard_button() {

		if ( ! wpbc_is_setup_wizard_page() ){

			if (
				( ! wpbc_is_user_can_access_wizard_page() ) ||
				( $this->db__is_all_steps_completed() )
			){
				return false;
			}

			// FixIn: 10.12.1.1.
			?><style tye="text/css">
				@media screen and (max-width: 782px) {
					.ui_element.wpbc_page_top__wizard_button {
						top: 49px !important;
					}
				}
				.wp-admin.wpbc_admin_full_screen .wpbc_header_news {
					display: none !important;
				}
				.wpbc_admin_full_screen .wpbc_page_top__wizard_button {
					display: none;
				}
				.wpbc_page_top__wizard_button {
					width: auto;
					position: fixed;
					z-index: 90000;
					box-shadow: 0 0 10px #c1c1c1;
					border-radius: 9px;
					background: transparent;
					right: 69px;
					top: 40px;
				}

				.ui_element.wpbc_page_top__wizard_button .wpbc_page_top__wizard_button_content,
				.ui_element.wpbc_page_top__wizard_button .wpbc_page_top__wizard_button_content:hover {
					border-radius: 5px;
					border: none;
					background: #535353; /* #6c9e00 #0b9300;*/
					box-shadow: 0 0 10px #dbdbdb;
					text-shadow: none;
					color: #fff;
					font-weight: 600;
					padding: 8px 10px 8px 15px;
					display: flex;
					flex-flow: row nowrap;
					justify-content: flex-start;
					align-items: center;
				}
			</style>
			<div style="min-width: 240px;top: 35px;font-size: 15px;" class="ui_element wpbc_page_top__wizard_button">
				<div class="wpbc_ui_control wpbc_page_top__wizard_button_content">
					<div class="in-button-text"
						 style="width: 100%;margin: 0;display: flex;flex-flow: row nowrap;justify-content: flex-start;align-items: center;">
						<div class="setup_wizard_page_container"
							 style="display: flex;flex-flow: row wrap;justify-content: flex-start;align-items: center;color: #fff;overflow: visible;flex: 1 1 auto;">
							<div class="name_item" style="margin-top: 0;white-space: nowrap;padding: 0;margin-right: 20px;">
								<i style="margin-right: 4px;" class="menu_icon icon-1x wpbc_icn_donut_large wpbc_icn_adjust0"></i> <?php esc_html_e( 'Finish Setup', 'booking' ); ?>
							</div>
							<div
								style="margin:2px 0px 0 9px;font-size: 9px;background: #3e3e3e;height: auto;border-radius: 5px;padding: 0px 7px 0px;margin-left: auto;"
								class="wpbc_badge_count name_item update-plugins">
								<span class="update-count"
									  style="white-space: nowrap;word-wrap: normal;"><?php echo esc_html( $this->get_active_step_num() . ' / ' . $this->get_total_steps_count() ); ?></span>
							</div>

							<div class="progress_line_container"
								 style="width: 100%;border: 0px solid #757575;height: 3px;border-radius: 6px;margin: 7px 0 0 0;overflow: hidden;background: #202020;">
								<div class="progress_line"
									 style="font-size: 6px;font-weight: 600;border-radius: 6px;word-wrap: normal;white-space: nowrap;background: #8ECE01;width: <?php echo esc_attr( $this->get_progess_value() ); ?>%;height: 3px;"></div>
							</div>
						</div>
						<a <?php  // onclick="javascript:jQuery( '.wpbc_page_top__wizard_button').remove();" href="javascript:void(0);"  ?>
						   href="<?php echo esc_url( wpbc_get_setup_wizard_page_url() ); ?>"
						   class="button button-primary"
						   style="margin-left: auto;font-size: 11px;min-height: 10px;margin-left: 25px;">Continue</a></div>
				</div>
			</div><?php
		}
	}

}
function wpbc_init_setup_wizard(){
	$setup_steps = new WPBC_SETUP_WIZARD_STEPS();
}
// $setup_steps = new WPBC_SETUP_WIZARD_STEPS();
add_action( 'init',   'wpbc_init_setup_wizard'  );



/**
* On plugin  activation set all steps as completed in Live Demos
* @return void
*/
function wpbc_booking_activate_plugin__wizard() {
	if ( wpbc_is_this_demo() ) {
		$setup_steps  = new WPBC_SETUP_WIZARD_STEPS();
		$is_completed = true;
		$setup_steps->db__set_all_steps_as( $is_completed );
	}
}
add_bk_action( 'wpbc_other_versions_activation',  'wpbc_booking_activate_plugin__wizard'  );