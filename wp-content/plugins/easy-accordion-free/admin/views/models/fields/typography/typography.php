<?php
/**
 * Framework typography field.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    easy-accordion-free
 * @subpackage easy-accordion-free/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'SP_EAP_Field_typography' ) ) {
	/**
	 *
	 * Field: typography
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SP_EAP_Field_typography extends SP_EAP_Fields {

		/**
		 * Chosen
		 *
		 * @var bool
		 */
		public $chosen = false;

		/**
		 * Value
		 *
		 * @var array
		 */
		public $value = array();

		/**
		 * Typography field constructor.
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

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->field_before();

			$args = wp_parse_args(
				$this->field,
				array(
					'font_family'        => true,
					'font_weight'        => true,
					'font_style'         => true,
					'font_size'          => true,
					'line_height'        => true,
					'letter_spacing'     => true,
					'text_align'         => true,
					'text_transform'     => true,
					'color'              => true,
					'chosen'             => true,
					'preview'            => true,
					'subset'             => true,
					'multi_subset'       => false,
					'extra_styles'       => false,
					'backup_font_family' => false,
					'font_variant'       => false,
					'word_spacing'       => false,
					'text_decoration'    => false,
					'custom_style'       => false,
					'margin_bottom'      => false,
					'exclude'            => '', // phpcs:ignore
					'unit'               => 'px',
					'line_height_unit'   => '',
					'preview_text'       => 'The quick brown fox jumps over the lazy dog',
				)
			);

			$default_value = array(
				'font-family'        => '',
				'font-weight'        => '',
				'font-style'         => '',
				'font-variant'       => '',
				'font-size'          => '',
				'line-height'        => '',
				'letter-spacing'     => '',
				'word-spacing'       => '',
				'text-align'         => '',
				'text-transform'     => '',
				'text-decoration'    => '',
				'backup-font-family' => '',
				'color'              => '',
				'custom-style'       => '',
				'type'               => '',
				'subset'             => '',
				'margin-bottom'      => '',
				'extra-styles'       => array(),
			);

			$default_value    = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;
			$this->value      = wp_parse_args( $this->value, $default_value );
			$this->chosen     = $args['chosen'];
			$chosen_class     = ( $this->chosen ) ? ' eapro--chosen' : '';
			$line_height_unit = ( ! empty( $args['line_height_unit'] ) ) ? $args['line_height_unit'] : $args['unit'];

			echo '<div class="eapro--typography' . esc_attr( $chosen_class ) . '" data-unit="' . esc_attr( $args['unit'] ) . '" data-line-height-unit="' . esc_attr( $line_height_unit ) . '" data-exclude="' . esc_attr( $args['exclude'] ) . '">';

			echo '<div class="eapro--blocks eapro--blocks-selects">';

			//
			// Font Family.
			if ( ! empty( $args['font_family'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Font Family', 'easy-accordion-free' ) . '</div>';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->create_select( array( $this->value['font-family'] => $this->value['font-family'] ), 'font-family', esc_html__( 'Select a font', 'easy-accordion-free' ) );
				echo '</div>';
			}

			//
			// Backup Font Family.
			if ( ! empty( $args['backup_font_family'] ) ) {
				echo '<div class="eapro--block eapro--block-backup-font-family hidden">';
				echo '<div class="eapro--title">' . esc_html__( 'Backup Font Family', 'easy-accordion-free' ) . '</div>';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $this->create_select(
					apply_filters(
						'eapro_field_typography_backup_font_family',
						array(
							'Arial, Helvetica, sans-serif',
							"'Arial Black', Gadget, sans-serif",
							"'Comic Sans MS', cursive, sans-serif",
							'Impact, Charcoal, sans-serif',
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							'Tahoma, Geneva, sans-serif',
							"'Trebuchet MS', Helvetica, sans-serif'",
							'Verdana, Geneva, sans-serif',
							"'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace",
							'Georgia, serif',
							'Palatino Linotype',
						)
					),
					'backup-font-family',
					esc_html__( 'Default', 'easy-accordion-free' )
				);
				echo '</div>';
			}

			//
			// Font Style and Extra Style Select.
			if ( ! empty( $args['font_weight'] ) || ! empty( $args['font_style'] ) ) {

				//
				// Font Style Select.
				echo '<div class="eapro--block eapro--block-font-style hidden">';
				echo '<div class="eapro--title">' . esc_html__( 'Font Style', 'easy-accordion-free' ) . '</div>';
				echo '<select class="eapro--font-style-select" data-placeholder="Default">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<option value="">' . ( ! $this->chosen ? esc_html__( 'Default', 'easy-accordion-free' ) : '' ) . '</option>';
				if ( ! empty( $this->value['font-weight'] ) || ! empty( $this->value['font-style'] ) ) {
					echo '<option value="' . esc_attr( strtolower( $this->value['font-weight'] . $this->value['font-style'] ) ) . '" selected></option>';
				}
				echo '</select>';
				echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[font-weight]' ) ) . '" class="eapro--font-weight" value="' . esc_attr( $this->value['font-weight'] ) . '" />';
				echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[font-style]' ) ) . '" class="eapro--font-style" value="' . esc_attr( $this->value['font-style'] ) . '" />';

				//
				// Extra Font Style Select.
				if ( ! empty( $args['extra_styles'] ) ) {
					echo '<div class="eapro--block-extra-styles hidden">';
					echo ( ! $this->chosen ) ? '<div class="eapro--title">' . esc_html__( 'Load Extra Styles', 'easy-accordion-free' ) . '</div>' : '';
					$placeholder = ( $this->chosen ) ? esc_html__( 'Load Extra Styles', 'easy-accordion-free' ) : esc_html__( 'Default', 'easy-accordion-free' );
					// phpcs:ignore
					echo $this->create_select( $this->value['extra-styles'], 'extra-styles', $placeholder, true );
					echo '</div>';
				}

				echo '</div>';

			}

			// Subset.
			if ( ! empty( $args['subset'] ) ) {
				echo '<div class="eapro--block eapro--block-subset hidden">';
				echo '<div class="eapro--title">' . esc_html__( 'Subset', 'easy-accordion-free' ) . '</div>';
				$subset = ( is_array( $this->value['subset'] ) ) ? $this->value['subset'] : array_filter( (array) $this->value['subset'] );
				// phpcs:ignore
				echo $this->create_select( $subset, 'subset', esc_html__( 'Default', 'easy-accordion-free' ), $args['multi_subset'] );
				echo '</div>';
			}

			//
			// Text Align.
			if ( ! empty( $args['text_align'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Text Align', 'easy-accordion-free' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'inherit' => esc_html__( 'Inherit', 'easy-accordion-free' ),
						'left'    => esc_html__( 'Left', 'easy-accordion-free' ),
						'center'  => esc_html__( 'Center', 'easy-accordion-free' ),
						'right'   => esc_html__( 'Right', 'easy-accordion-free' ),
						'justify' => esc_html__( 'Justify', 'easy-accordion-free' ),
						'initial' => esc_html__( 'Initial', 'easy-accordion-free' ),
					),
					'text-align',
					esc_html__( 'Default', 'easy-accordion-free' )
				);
				echo '</div>';
			}

			//
			// Font Variant.
			if ( ! empty( $args['font_variant'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Font Variant', 'easy-accordion-free' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'normal'         => esc_html__( 'Normal', 'easy-accordion-free' ),
						'small-caps'     => esc_html__( 'Small Caps', 'easy-accordion-free' ),
						'all-small-caps' => esc_html__( 'All Small Caps', 'easy-accordion-free' ),
					),
					'font-variant',
					esc_html__( 'Default', 'easy-accordion-free' )
				);
				echo '</div>';
			}

			//
			// Text Transform.
			if ( ! empty( $args['text_transform'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Text Transform', 'easy-accordion-free' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'none'       => esc_html__( 'None', 'easy-accordion-free' ),
						'capitalize' => esc_html__( 'Capitalize', 'easy-accordion-free' ),
						'uppercase'  => esc_html__( 'Uppercase', 'easy-accordion-free' ),
						'lowercase'  => esc_html__( 'Lowercase', 'easy-accordion-free' ),
					),
					'text-transform',
					esc_html__( 'Default', 'easy-accordion-free' )
				);
				echo '</div>';
			}

			//
			// Text Decoration.
			if ( ! empty( $args['text_decoration'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Text Decoration', 'easy-accordion-free' ) . '</div>';
				// phpcs:ignore
				echo $this->create_select(
					array(
						'none'               => esc_html__( 'None', 'easy-accordion-free' ),
						'underline'          => esc_html__( 'Solid', 'easy-accordion-free' ),
						'underline double'   => esc_html__( 'Double', 'easy-accordion-free' ),
						'underline dotted'   => esc_html__( 'Dotted', 'easy-accordion-free' ),
						'underline dashed'   => esc_html__( 'Dashed', 'easy-accordion-free' ),
						'underline wavy'     => esc_html__( 'Wavy', 'easy-accordion-free' ),
						'underline overline' => esc_html__( 'Overline', 'easy-accordion-free' ),
						'line-through'       => esc_html__( 'Line-through', 'easy-accordion-free' ),
					),
					'text-decoration',
					esc_html__( 'Default', 'easy-accordion-free' )
				);
				echo '</div>';
			}

			echo '</div>';

			echo '<div class="eapro--blocks eapro--blocks-inputs">';

			// Font Size.
			if ( ! empty( $args['font_size'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Font Size', 'easy-accordion-free' ) . '</div>';
				echo '<div class="eapro--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[font-size]' ) ) . '" class="eapro--font-size eapro--input eapro-input-number" value="' . esc_attr( $this->value['font-size'] ) . '" />';
				echo '<span class="eapro--unit">' . esc_attr( $args['unit'] ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			// Line Height.
			if ( ! empty( $args['line_height'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Line Height', 'easy-accordion-free' ) . '</div>';
				echo '<div class="eapro--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[line-height]' ) ) . '" class="eapro--line-height eapro--input eapro-input-number" value="' . esc_attr( $this->value['line-height'] ) . '" />';
				echo '<span class="eapro--unit">' . esc_attr( $line_height_unit ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Letter Spacing.
			if ( ! empty( $args['letter_spacing'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Letter Spacing', 'easy-accordion-free' ) . '</div>';
				echo '<div class="eapro--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[letter-spacing]' ) ) . '" class="eapro--letter-spacing eapro--input eapro-input-number" value="' . esc_attr( $this->value['letter-spacing'] ) . '" />';
				echo '<span class="eapro--unit">' . esc_attr( $args['unit'] ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			//
			// Word Spacing.
			if ( ! empty( $args['word_spacing'] ) ) {
				echo '<div class="eapro--block">';
				echo '<div class="eapro--title">' . esc_html__( 'Word Spacing', 'easy-accordion-free' ) . '</div>';
				echo '<div class="eapro--input-wrap">';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[word-spacing]' ) ) . '" class="eapro--word-spacing eapro--input eapro-input-number" value="' . esc_attr( $this->value['word-spacing'] ) . '" />';
				echo '<span class="eapro--unit">' . esc_attr( $args['unit'] ) . '</span>';
				echo '</div>';
				echo '</div>';
			}

			echo '</div>';

			//
			// Font Color.
			echo '<div class="eapro--blocks eapro--blocks-colors">';
			if ( ! empty( $args['color'] ) ) {
				$default_color_attr = ( ! empty( $default_value['color'] ) ) ? ' data-default-color="' . esc_attr( $default_value['color'] ) . '"' : '';
				echo '<div class="eapro--block eapro--block-font-color">';
				echo '<div class="eapro--title">' . esc_html__( 'Font Color', 'easy-accordion-free' ) . '</div>';
				echo '<div class="eapro-field-color">';
				// phpcs:ignore
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[color]' ) ) . '" class="eapro-color eapro--color" value="' . esc_attr( $this->value['color'] ) . '"' . $default_color_attr . ' />';
				echo '</div>';
				echo '</div>';
			}
			// Margin Bottom.
			if ( ! empty( $args['margin_bottom'] ) ) {
				echo '<div class="eapro--block eapro--block-margin">';
				echo '<div class="eapro--title">' . esc_html__( 'Margin Bottom', 'easy-accordion-free' ) . '</div>';
				echo '<div class="eapro--blocks lw-typo-margin">';
				echo '<div class="eapro--block eapro--unit icon"><i class="fa fa-long-arrow-down"></i></div>';
				echo '<div class="eapro--block"><input type="number" name="' . esc_attr( $this->field_name( '[margin-bottom]' ) ) . '" class="eapro--margin-bottom eapro--input eapro-number" value="' . esc_attr( $this->value['margin-bottom'] ) . '" /></div>';
				echo '<div class="eapro--block eapro--unit">' . esc_html( $args['unit'] ) . '</div>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';

			//
			// Custom style.
			if ( ! empty( $args['custom_style'] ) ) {
				echo '<div class="eapro--block eapro--block-custom-style">';
				echo '<div class="eapro--title">' . esc_html__( 'Custom Style', 'easy-accordion-free' ) . '</div>';
				echo '<textarea name="' . esc_attr( $this->field_name( '[custom-style]' ) ) . '" class="eapro--custom-style">' . esc_attr( $this->value['custom-style'] ) . '</textarea>';
				echo '</div>';
			}

			//
			// Preview.
			$always_preview = ( 'always' !== $args['preview'] ) ? ' hidden' : '';

			if ( ! empty( $args['preview'] ) ) {
				echo '<div class="eapro--block eapro--block-preview' . esc_attr( $always_preview ) . '">';
				echo '<div class="eapro--toggle fa fa-toggle-off"></div>';
				echo '<div class="eapro--preview">' . esc_attr( $args['preview_text'] ) . '</div>';
				echo '</div>';
			}

			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[type]' ) ) . '" class="eapro--type" value="' . esc_attr( $this->value['type'] ) . '" />';
			echo '<input type="hidden" name="' . esc_attr( $this->field_name( '[unit]' ) ) . '" class="eapro--unit-save" value="' . esc_attr( $args['unit'] ) . '" />';

			echo '</div>';
			// phpcs:ignore
			echo $this->field_after();
		}

		/**
		 * Create select
		 *
		 * @param  array  $options options.
		 * @param  string $name name.
		 * @param  mixed  $placeholder placeholder.
		 * @param  bool   $is_multiple is_multiple.
		 * @return statement
		 */
		public function create_select( $options, $name, $placeholder = '', $is_multiple = false ) {

			$multiple_name = ( $is_multiple ) ? '[]' : '';
			$multiple_attr = ( $is_multiple ) ? ' multiple data-multiple="true"' : '';
			$chosen_rtl    = ( $this->chosen && is_rtl() ) ? ' chosen-rtl' : '';

			$output  = '<select name="' . esc_attr( $this->field_name( '[' . $name . ']' . $multiple_name ) ) . '" class="eapro--' . esc_attr( $name ) . esc_attr( $chosen_rtl ) . '" data-placeholder="' . esc_attr( $placeholder ) . '"' . $multiple_attr . '>';
			$output .= ( ! empty( $placeholder ) ) ? '<option value="">' . esc_attr( ( ! $this->chosen ) ? $placeholder : '' ) . '</option>' : '';

			if ( ! empty( $options ) ) {
				foreach ( $options as $option_key => $option_value ) {
					if ( $is_multiple ) {
						$selected = ( in_array( $option_value, $this->value[ $name ], true ) ) ? ' selected' : '';
						$output  .= '<option value="' . esc_attr( $option_value ) . '"' . esc_attr( $selected ) . '>' . esc_attr( $option_value ) . '</option>';
					} else {
						$option_key = ( is_numeric( $option_key ) ) ? $option_value : $option_key;
						$selected   = ( $option_key === $this->value[ $name ] ) ? ' selected' : '';
						$output    .= '<option value="' . esc_attr( $option_key ) . '"' . esc_attr( $selected ) . '>' . esc_attr( $option_value ) . '</option>';
					}
				}
			}

			$output .= '</select>';

			return $output;
		}
	}
}
