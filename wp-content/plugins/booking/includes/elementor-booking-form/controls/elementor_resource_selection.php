<?php

/**
 * Elementor WPBC_Elementor_Booking_Resource_Selection_Control control.
 *
 * A control for displaying a select field with the ability to choose booking_resources.
 *
 * @since 1.0.0
 */
class WPBC_Elementor_Booking_Resource_Selection_Control extends \Elementor\Base_Data_Control {

	/**
	 * Get resource control type.
	 *
	 * Retrieve the control type, in this case `resource`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type(): string {
		return 'wpbc_resource_selection';
	}

	/**
	 * Get booking_resources.
	 *
	 * Retrieve all the available booking_resources.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return array Available booking_resources.
	 */
	public static function get_booking_resources(): array {

		$resources_arr        = wpbc_ajx_get_all_booking_resources_arr();
		$resources_arr_sorted = wpbc_ajx_get_sorted_booking_resources_arr( $resources_arr );

		foreach ( $resources_arr_sorted as $resource_index => $br ) {

			$br_option_title = $br['title'];

			if ( ( isset( $br['parent'] ) ) && ( $br['parent'] == 0 ) && ( isset( $br['count'] ) ) && ( $br['count'] > 1 ) ) {
				$br_option_title .= ' [' . __( 'parent resource', 'booking' ) . ']';
			}

			// == CSS ==
			$resources_arr_sorted[ $resource_index ]['css_class'] = 'wpbc_single_resource';
			if ( isset( $br['parent'] ) ) {
				if ( $br['parent'] == 0 ) {
					if ( ( isset( $br['count'] ) ) && ( $br['count'] > 1 ) ) {
						$resources_arr_sorted[ $resource_index ]['css_class'] = 'wpbc_parent_resource';
					}
				} else {
					$resources_arr_sorted[ $resource_index ]['css_class'] = 'wpbc_child_resource';
				}
			}

			if ( 'wpbc_child_resource' === $resources_arr_sorted[ $resource_index ]['css_class'] ) {
				$br_option_title = ' &nbsp;&nbsp;&nbsp; ' . $br_option_title;
			}

			$resources_arr_sorted[ $resource_index ]['title'] = $br_option_title;
		}
		return $resources_arr_sorted;
	}

	/**
	 * Get resource control default settings.
	 *
	 * Retrieve the default settings of the resource control. Used to return
	 * the default settings while initializing the resource control.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return array Currency control default settings.
	 */
	protected function get_default_settings(): array {
		return [
			'booking_resources' => self::get_booking_resources()
		];
	}

	/**
	 * Get resource control default value.
	 *
	 * Retrieve the default value of the resource control. Used to return the
	 * default value while initializing the control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Currency control default value.
	 */
	public function get_default_value(): string {

		$booking_resources_arr = self::get_booking_resources();

		foreach ( $booking_resources_arr as $index => $resource_arr ) {
			return $resource_arr['booking_type_id'];
		}
		return '1';
	}


	/**
	 * Render resource control output in the editor.
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
					<# _.each( data.booking_resources, function( resource_arr, resource_index ) { #>
					<option value="{{ resource_arr.booking_type_id }}" class="{{ resource_arr.css_class }}">{{{ resource_arr.title }}}</option>
					<# } ); #>
				</select>
			</div>

		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}
