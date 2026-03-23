<?php

/**
 * Elementor WPBC_Elementor_Go_Button_Control control.
 *
 * A control for displaying Go Button.
 *
 * @since 1.0.0
 */
class WPBC_Elementor_Go_Button_Control extends \Elementor\Base_Data_Control {

	/**
	 * Get wpbc_go_button control type.
	 *
	 * Retrieve the control type, in this case `go_form_field`.
	 *
	 * @return string Control type.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_type(): string {
		return 'wpbc_go_button';
	}


	/**
	 * Render go_form_field control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function content_template(): void {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field elementor-control-button">
			<div class="elementor-button-wrapper">
				<button
						class="elementor-button <# if ( data.css_class ) { #>{{{data.css_class}}}<# } #>"
						type="button"
						<# if ( data.url ) { #>
						onclick="javascript:window.location.href ='{{{data.url}}}';"
						<# } #>
				><# if ( data.icon ) { #>
					<span>{{{ data.text }}}</span>
				<# }
				if ( data.icon ) { #>
					<i class="{{{ data.icon }}}" aria-hidden="true"></i>
				<# } #></button>
			</div>
		</div>

		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}

