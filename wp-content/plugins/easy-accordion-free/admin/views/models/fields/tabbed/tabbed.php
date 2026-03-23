<?php
/**
 * Framework Tabbed fields.
 *
 * @link https://shapedplugin.com
 *
 * @package Easy_Accordion_free
 * @subpackage Easy_Accordion_free/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

/**
 *
 * Field: tabbed
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_EAP_Field_tabbed' ) ) {

	class SP_EAP_Field_tabbed extends SP_EAP_Fields {

		/**
		 * Field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render field
		 *
		 * @return void
		 */
		public function render() {

			$unallows = array( 'tabbed' );

			echo wp_kses_post( $this->field_before() );

			echo '<div class="eapro-tabbed-nav">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_icon   = ( ! empty( $tab['icon'] ) ) ? $tab['icon'] : '';
				$tabbed_active = ( empty( $key ) ) ? ' eapro-tabbed-active' : '';
				$tabbed_class  = ( ! empty( $tab['class'] ) ) ? $tab['class'] : '';

				echo '<a href="#" class="' . esc_attr( $tabbed_class . $tabbed_active ) . '">' . wp_kses_post( $tabbed_icon . $tab['title'] ) . '</a>';
			}
			echo '</div>';

			echo '<div class="eapro-tabbed-sections">';
			foreach ( $this->field['tabs'] as $key => $tab ) {

				$tabbed_hidden = ( ! empty( $key ) ) ? ' hidden' : '';
				$tabbed_class  = ( ! empty( $tab['class'] ) ) ? ' ' . $tab['class'] : '';
				echo '<div class="eapro-tabbed-section' . esc_attr( $tabbed_hidden . $tabbed_class ) . '">';

				foreach ( $tab['fields'] as $field ) {
					if ( in_array( $field['type'], $unallows ) ) {
						$field['_notice'] = true; }
					$field_id      = ( isset( $field['id'] ) ) ? $field['id'] : '';
					$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
					$field_value   = ( isset( $this->value[ $field_id ] ) ) ? $this->value[ $field_id ] : $field_default;
					$unique_id     = ( ! empty( $this->unique ) ) ? $this->unique : '';

					SP_EAP::field( $field, $field_value, $unique_id, 'field/tabbed' );

				}

				echo '</div>';
			}
			echo '</div>';

			echo wp_kses_post( $this->field_after() );
		}
	}
}
