<?php

/**
 * Elementor WPBC_Elementor_Custom_Form_Selection_Control control.
 *
 * A control for displaying a select field with the ability to choose booking_custom_forms.
 *
 * @since 1.0.0
 */
class WPBC_Elementor_Custom_Form_Selection_Control extends \Elementor\Base_Data_Control {

	/**
	 * Get custom_form control type.
	 *
	 * Retrieve the control type, in this case `custom_form`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type(): string {
		return 'wpbc_custom_form_selection';
	}

	/**
	 * Get booking_custom_forms.
	 *
	 * Retrieve all the available booking_custom_forms.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return array Available booking_custom_forms.
	 */
	public static function get_booking_custom_forms(): array {

		$custom_forms_arr_sorted = wpbc_toolbar__get_custom_forms__options_for_selection();

		$sorted_options_list = array();

		foreach ( $custom_forms_arr_sorted as $slug => $form_item ) {
			if ( is_array( $form_item ) ) {
				if ( isset( $form_item['optgroup'] ) ) {
					$sorted_options_list[] = $form_item;
				} else {
					// Legacy skins with attributes: 'style=>'color:#ccc;' .
					$form_item['slug']     = $slug;
					$sorted_options_list[] = $form_item;
				}
			} else {
				$sorted_options_list[] = array(
					'slug'  => $slug,
					'title' => $form_item,
				);
			}
		}

		return $sorted_options_list;
	}

	/**
	 * Get custom_form control default settings.
	 *
	 * Retrieve the default settings of the custom_form control. Used to return
	 * the default settings while initializing the custom_form control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Currency control default settings.
	 */
	protected function get_default_settings(): array {
		return [
			'booking_custom_forms' => self::get_booking_custom_forms()
		];
	}

	/**
	 * Get custom_form control default value.
	 *
	 * Retrieve the default value of the custom_form control. Used to return the
	 * default value while initializing the control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Currency control default value.
	 */
	public function get_default_value(): string {

		$booking_custom_forms_arr = self::get_booking_custom_forms();

		foreach ( $booking_custom_forms_arr as $index => $custom_form_arr ) {
			return $custom_form_arr['slug'];
		}
		return 'standard';
	}


	/**
	 * Render custom_form control output in the editor.
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
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>

			<div class="elementor-control-input-wrapper">
				<select id="<?php echo esc_attr( $control_uid ); ?>" data-setting="{{ data.name }}">
					<# _.each( data.booking_custom_forms, function( custom_form_arr, custom_form_index ) {
						if ( custom_form_arr['optgroup'] ) {                                   // OPTGROUP

                			if ( ! custom_form_arr['close'] ) {
                    			#><optgroup label="{{{custom_form_arr['title']}}}"><#
                			} else {
                    			#></optgroup><#
                			}

            			} else {
							#><option value="{{ custom_form_arr['slug'] }}" >{{{ custom_form_arr['title'] }}}</option><#
						}
					} ); #>
				</select>
			</div>

		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}
