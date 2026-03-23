<?php
/**
 * The Accordion FAQs section title template.
 *
 * This template can be overridden by copying it to yourtheme/easy-accordion-free/templates/templates-parts/section-title.php
 *
 * @package easy_accordion_free
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( $acc_section_title ) { ?>
	<h2 class="eap_section_title eap_section_title_<?php echo esc_attr( $post_id ); ?>"><?php echo wp_kses_post( $main_section_title ); ?></h2>
<?php } ?>
