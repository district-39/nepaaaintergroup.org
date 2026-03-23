<?php
/**
 * includes/elementor-booking-form/elementor-widget-booking.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Booking Calendar Widget.
 *
 * Elementor widget for Booking Calendar plugin.
 *
 * @since 1.0.0
 */
class WPBC_Elementor_WPBC_Booking_Form_1 extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve list widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name(): string {
		return 'wpbc_widget_booking_form_1';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve list widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title(): string {
		return 'Booking Calendar'; // . ' - ' . esc_html__( 'Booking Form', 'booking' ); //.
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve list widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon(): string {
		return 'wpbc_logo_in_elementor';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories(): array {
		return [ 'basic', 'wpbc' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the list widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords(): array {
		return [ 'Booking Calendar', 'booking', 'calendar', 'bookings', 'events', 'appointments', 'booking plugin', 'wpbc' ];
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url(): string {
		return 'https://wpbookingcalendar.com/contact/';
	}

	/**
	 * Get widget promotion data.
	 *
	 * Retrieve the widget promotion data.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Widget promotion data.
	 */
	protected function get_upsale_data(): array {
		return array(
			'image'        => esc_url( ELEMENTOR_ASSETS_URL . 'images/go-pro.svg' ),
			'image_alt'    => esc_attr__( 'Upgrade', 'booking' ),
			'title'        => esc_html__( 'Booking Calendar Pro', 'booking' ),
			'description'  => esc_html__( 'Get the premium version of the plugin with additional features.', 'booking' ),
			'upgrade_url'  => esc_url( 'https://wpbookingcalendar.com/prices/' ),
			'upgrade_text' => esc_html__( 'Upgrade Now', 'booking' ),
			'condition'    => ! class_exists('wpdev_bk_personal'),
		);
	}

	/**
	 * Whether the widget requires inner wrapper.
	 *
	 * Determine whether to optimize the DOM size.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return bool Whether to optimize the DOM size.
	 */
	public function has_widget_inner_wrapper(): bool {
		return true;
	}

	/**
	 * Whether the element returns dynamic content.
	 *
	 * Determine whether to cache the element output or not.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return bool Whether to cache the element output.
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Register list widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls(): void {

		$this->start_controls_section(
			'wpbc_booking_interface_section',
			[
				'label' => esc_html__( 'Booking Interface', 'booking' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wpbc_booking_shortcode',
			[
				'type'        => \Elementor\Controls_Manager::SELECT,
				'label'       => esc_html__( 'Display Mode', 'booking' ),
				'options'     => [
					'booking'         => esc_html__( 'Booking Form', 'booking' ) . ' + ' . esc_html__( 'Calendar', 'booking' ),
					'bookingcalendar' => esc_html__( 'Availability Calendar Only', 'booking' ),
				],
				'default'     => 'booking',
				'label_block' => true,
			]
		);

		if ( class_exists( 'wpdev_bk_personal' ) ) {
			$this->add_control(
				'wpbc_booking_resource_id',
				[
					'label' => esc_html__( 'Select Booking Resource', 'booking' ),
					'type'  => 'wpbc_resource_selection',
				]
			);
		}

		$this->add_control(
			'wpbc_booking_months_number',
			[
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => esc_html__( 'Number of Visible Months', 'booking' ),
				'options' => [
					'1'  => '1 ' . esc_html__( 'month', 'booking' ),
					'2'  => '2 ' . esc_html__( 'months', 'booking' ),
					'3'  => '3 ' . esc_html__( 'months', 'booking' ),
					'4'  => '4 ' . esc_html__( 'months', 'booking' ),
					'5'  => '5 ' . esc_html__( 'months', 'booking' ),
					'6'  => '6 ' . esc_html__( 'months', 'booking' ),
					'7'  => '7 ' . esc_html__( 'months', 'booking' ),
					'8'  => '8 ' . esc_html__( 'months', 'booking' ),
					'9'  => '9 ' . esc_html__( 'months', 'booking' ),
					'10' => '10 ' . esc_html__( 'months', 'booking' ),
					'11' => '11 ' . esc_html__( 'months', 'booking' ),
					'12' => '12 ' . esc_html__( 'months', 'booking' ),
				],
				'default' => '1',
			]
		);


		if ( wpbc_is_custom_forms_enabled() ) {
			$this->add_control(
				'wpbc_custom_form_id',
				[
					'label' => esc_html__( 'Use Custom Form', 'booking' ),
					'type'  => 'wpbc_custom_form_selection',
					'condition' => [
						'wpbc_booking_shortcode' => 'booking',
					],
				]
			);
		}

		$this->add_control(
			'wpbc_go_button__form_fields',
			[
				'type'      => 'wpbc_go_button',
				'text'      => esc_html__( 'Go to Form Fields Configuration', 'booking' ),
				'icon'      => 'eicon-share-arrow',
				'css_class' => 'e-btn-txt e-btn-txt-border',
				'url'       => esc_url( wpbc_get_settings_url() . '&tab=form' ),
				'condition' => [
					'wpbc_booking_shortcode' => 'booking',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wpbc_booking_advanced_section',
			[
				'label' => esc_html__( 'Advanced', 'booking' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wpbc_booking_calendar_dates_start',
			[
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'label'       => esc_html__( 'Earliest date visible/selectable in the calendar.', 'booking' ),
				'default'     => '',
				'label_block' => true,
				'description' => '<strong>' . esc_html__('Optional', 'booking') . '!</strong> '  . esc_html__( 'Format: YYYY-MM-DD (e.g. 2025-07-26)', 'booking' ),
				'picker_options' => [
					'enableTime' => false,
					'dateFormat' => 'Y-m-d',  // '2025-07-26' format
				],
			]
		);

		$this->add_control(
			'wpbc_booking_calendar_dates_end',
			[
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'label'       => esc_html__( 'Latest date visible/selectable in the calendar.', 'booking' ),
				'default'     => '',
				'label_block' => true,
				'description' => '<strong>' . esc_html__('Optional', 'booking') . '!</strong> ' . esc_html__( 'Format: YYYY-MM-DD (e.g. 2025-12-31)', 'booking' ),
				'picker_options' => [
					'enableTime' => false,
					'dateFormat' => 'Y-m-d',  // '2025-07-26' format
				],
			]
		);

		$this->add_control(
			'wpbc_booking_calendar_startmonth',
			[
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'label'       => esc_html__( 'Select start month of calendar after loading.', 'booking' ),
				'default'     => '',
				'label_block' => true,
				'description' => '<strong>' . esc_html__('Optional', 'booking') . '!</strong> ' . esc_html__( 'Format: YYYY-MM (e.g. 2025-3)', 'booking' ),
				'picker_options' => [
					'enableTime' => false,
					'dateFormat' => 'Y-m',  // '2025-07-26' format
				],
			]
		);
		$this->add_control(
			'wpbc_booking_calendar_dates_reset_button',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => '<button type="button" class="elementor-button wpbc-reset-dates-button" style="margin-top:10px;">'.esc_html__('Reset Dates','booking').'</button>',
				'content_classes' => 'wpbc-calendar-dates-reset-wrapper',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'wpbc_theme_section',
			[
				'label' => esc_html__( 'Appearance & Color Themes', 'booking' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'wpbc_calendar_skin_control',
			[
				'label'              => esc_html__( 'Calendar Theme (Skin)', 'booking' ),
				'type'               => 'wpbc_calendar_skin_selection',
				'frontend_available' => false,
				'render_type'        => 'none',
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Render list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render(): void {

		$shortcode_params = array();
		$settings         = $this->get_settings_for_display();

		// Shortcode type.
		$allowed_shortcodes = array( 'booking', 'bookingcalendar' );
		$shortcode_type = in_array( $settings['wpbc_booking_shortcode'], $allowed_shortcodes, true ) ? $settings['wpbc_booking_shortcode'] : 'booking';

		// Number of months.
		$months_number = isset( $settings['wpbc_booking_months_number'] ) ? intval( $settings['wpbc_booking_months_number'] ) : 1;
		if ( $months_number > 1 ) {
			$shortcode_params[] = "nummonths={$months_number}";
		}

		// Booking resource (if personal version is active).
		if ( class_exists( 'wpdev_bk_personal' ) && ! empty( $settings['wpbc_booking_resource_id'] ) && intval( $settings['wpbc_booking_resource_id'] ) > 1 ) {
			$resource_id        = intval( $settings['wpbc_booking_resource_id'] );
			$shortcode_params[] = "resource_id={$resource_id}";
		}

		// Is custom use form ?
		if ( ( class_exists( 'wpdev_bk_biz_m' ) ) && ! empty( $settings['wpbc_custom_form_id'] ) ) {
			$wpbc_custom_form_id = sanitize_text_field( $settings['wpbc_custom_form_id'] );
			if ( 'standard' !== $wpbc_custom_form_id ) {
				$shortcode_params[] = "form_type='{$wpbc_custom_form_id}'";
			}
		}

		// wpbc_booking_calendar_dates_start / calendar_dates_end / startmonth .
		if ( ! empty( $settings['wpbc_booking_calendar_dates_start'] ) ) {
			$shortcode_params[] = "calendar_dates_start='" . $settings['wpbc_booking_calendar_dates_start'] . "'";
		}
		if ( ! empty( $settings['wpbc_booking_calendar_dates_end'] ) ) {
			$shortcode_params[] = "calendar_dates_end='" . $settings['wpbc_booking_calendar_dates_end'] . "'";
		}
		if ( ! empty( $settings['wpbc_booking_calendar_startmonth'] ) ) {
			$shortcode_params[] = "startmonth='" . $settings['wpbc_booking_calendar_startmonth'] . "'";
		}



		// Final parameters string.
		$shortcode_params_str = implode( ' ', $shortcode_params );

		echo do_shortcode( '[' . $shortcode_type . ' ' . $shortcode_params_str . ']' );
	}

	public function get_script_depends(): array {
		return array( 'wpbc-elementor-frontend-bridge' );
	}

	public function get_style_depends(): array {
		return array( 'wpbc-elementor-frontend-bridge' );
	}

}