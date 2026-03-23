<?php
/**
 * Framework shortcode field.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    easy-accordion-free
 * @subpackage easy-accordion-free/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'SP_EAP_Field_shortcode' ) ) {
	/**
	 *
	 * Field: shortcode
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SP_EAP_Field_shortcode extends SP_EAP_Fields {

		/**
		 * Shortcode field constructor.
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

			// Get the Post ID.
			$post_id = get_the_ID();

			if ( ! empty( $this->field['shortcode'] ) && 'pro_notice' === $this->field['shortcode'] ) {
				if ( ! empty( $post_id ) ) {
					echo '<div class="eap_shortcode-area eap-pro-notice-wrapper">';
					echo '<div class="eap-pro-notice-heading">' . sprintf(
						/* translators: 1: start span tag, 2: close tag. */
						esc_html__( 'Grow Faster with %1$sPRO%2$s', 'easy-accordion-free' ),
						'<span>',
						'</span>'
					) . '</div>';

					echo '<p class="eap-pro-notice-desc">' . sprintf(
						/* translators: 1: start bold tag, 2: close tag. */
						esc_html__( 'Boost Conversions with Professional FAQs and %1$sAccordions by Pro!%2$s', 'easy-accordion-free' ),
						'<b>',
						'</b>'
					) . '</p>';

					echo '<ul>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( '17+ FAQ & Accordion Themes', 'easy-accordion-free' ) . '</li>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( 'Stylish Horizontal Accordion', 'easy-accordion-free' ) . '</li>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( 'Image and Post Accordion', 'easy-accordion-free' ) . '</li>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( 'Multi-column & Nested FAQs', 'easy-accordion-free' ) . '</li>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( 'WooCommerce FAQ Tab', 'easy-accordion-free' ) . '</li>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( 'FAQ Forms and Analytics', 'easy-accordion-free' ) . '</li>';
					echo '<li><i class="eap-icon-check-icon"></i> ' . esc_html__( '140+ Advanced Customizations', 'easy-accordion-free' ) . '</li>';
					echo '</ul>';

					echo '<div class="eap-pro-notice-button">';
					echo '<a class="eap-open-live-demo" href="https://easyaccordion.io/pricing/?ref=1" target="_blank">';
					echo esc_html__( 'Upgrade to Pro Now', 'easy-accordion-free' ) . ' <i class="eap-icon-shuttle_2285485-1"></i>';
					echo '</a>';
					echo '</div>';
					echo '</div>';
				}
			} elseif ( ! empty( $this->field['shortcode'] ) && 'builder_option' === $this->field['shortcode'] ) {
				echo ( ! empty( $post_id ) ) ? '
				<div class="eap-scode-wrap">
					<p>
						' .
							sprintf(
								/* translators: 1: start strong tag, 2: close tag. */
								esc_html__( 'Easy Accordion integrates seamlessly with %1$sGutenberg%2$s, Classic Editor, %1$sElementor%2$s, Divi, Bricks, Beaver, Oxygen, WPBakery Builder, and more.', 'easy-accordion-free' ),
								'<strong>',
								'</strong>'
							)
						. '
					</p>
				</div>
				' : '';
			} else {
				echo ( ! empty( $post_id ) ) ? '<div class="eap-scode-wrap"><p>To display the Accordion FAQs group, copy and paste this shortcode into your post, page, custom post, block editor, or page builder. <a href="https://docs.shapedplugin.com/docs/easy-accordion-pro/configurations/how-to-use-easy-accordion-shortcode-to-your-theme-files-or-php-templates/" target="_blank">Learn how</a> to include it in your template file.</p><span class="eap-shortcode-selectable">[sp_easyaccordion id="' . esc_attr( $post_id ) . '"]</span></div><div class="sp_eap-after-copy-text"><i class="fa fa-check-circle"></i> Shortcode Copied to Clipboard! </div>' : '';
			}
		}
	}
}
