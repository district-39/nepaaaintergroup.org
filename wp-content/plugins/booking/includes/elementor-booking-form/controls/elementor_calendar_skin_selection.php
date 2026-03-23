<?php

/**
 * Elementor WPBC_Elementor_Calendar_Skin_Selection_Control control.
 *
 * A control for displaying a select field with the ability to choose booking_calendar_skins.
 *
 * @since 1.0.0
 */
class WPBC_Elementor_Calendar_Skin_Selection_Control extends \Elementor\Base_Data_Control {

	/**
	 * Get calendar_skin control type.
	 *
	 * Retrieve the control type, in this case `calendar_skin`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type(): string {
		return 'wpbc_calendar_skin_selection';
	}

	/**
	 * Get booking_calendar_skins.
	 *
	 * Retrieve all the available booking_calendar_skins.
	 *
	 * @return array Available booking_calendar_skins.
	 * @since  1.0.0
	 * @access public
	 * @static
	 */
	public static function get_booking_calendar_skins(): array {

		$transformed_cal_arr = wpbc_get_calendar_skin_options_with_legacy_sections();

		$sorted_options_list = array();

		foreach ( $transformed_cal_arr as $cal_index => $cal_item ) {
			if ( is_array( $cal_item ) ) {
				if ( isset( $cal_item['optgroup'] ) ) {
					$sorted_options_list[] = $cal_item;
				} else {
					// Legacy skins with attributes: 'style=>'color:#ccc;' .
					$cal_item['url']       = $cal_index;
					$sorted_options_list[] = $cal_item;
				}
			} else {
				$sorted_options_list[] = array(
					'url'   => $cal_index,
					'title' => $cal_item,
				);
			}
		}

		return $sorted_options_list;
	}

	/**
	 * Get calendar_skin control default settings.
	 *
	 * Retrieve the default settings of the calendar_skin control. Used to return
	 * the default settings while initializing the calendar_skin control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Currency control default settings.
	 */
	protected function get_default_settings(): array {
		return [
			'booking_calendar_skins' => self::get_booking_calendar_skins(),
			'default'                => wpbc_get_calendar_skin_url(),
		];
	}


	public function get_value( $control, $settings ) {
			$current_value = wpbc_get_calendar_skin_url(); // or default skin string.
		return $current_value;
	}

	public function get_default_value() {
		return wpbc_get_calendar_skin_url(); // fallback if no user value exists.
	}

	/**
	 * Render calendar_skin control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template(): void {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field elementor-control-type-select">

			<# if ( data.label ) {#>
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>

			<div class="elementor-control-input-wrapper">
				<select id="<?php echo esc_attr($control_uid); ?>" data-setting0="{{ data.name }}"  onchange="javascript:document.getElementById('elementor-preview-iframe').contentWindow.wpbc__calendar__change_skin( jQuery( this ).val() );" >
					<#
					_.each( data.booking_calendar_skins, function( calendar_skin_arr, calendar_skin_index ) {
						if ( calendar_skin_arr['optgroup'] ) {                                   // OPTGROUP

                			if ( ! calendar_skin_arr['close'] ) {
                    			#><optgroup label="{{{calendar_skin_arr['title']}}}"><#
                			} else {
                    			#></optgroup><#
                			}

            			} else {
							#><option value="{{ calendar_skin_arr['url'] }}" <# if (calendar_skin_arr['url'] === data.controlValue) { #>selected="selected"<# } #>>{{{ calendar_skin_arr['title'] }}}</option><#
						}
					} ); #>
				</select>
			</div>

		</div>

		<div class="elementor-control-field elementor-control-type-save-button">
			<div class="elementor-button-wrapper">
				<button class="elementor-button elementor-save-skin" type="button" id="elementor-save-skin-<?php echo esc_attr(wp_rand( 10, 1000000 )); ?>" processing_title="<?php esc_attr_e('Processing', 'booking'); ?>">
					<!--i class="eicon-check" aria-hidden="true"></i-->
					<?php esc_html_e( 'Save Calendar Skin', 'booking' ); ?>
				</button>
			</div>
		</div>

		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}

