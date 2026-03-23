<?php
/**
 * The Accordion FAQs section preloader template.
 *
 * This template can be overridden by copying it to yourtheme/easy-accordion-free/templates/templates-parts/preloader.php
 *
 * @package easy_accordion_free
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( $eap_preloader ) { ?>
	<div id="eap-preloader-<?php echo esc_attr( $post_id ); ?>" class="accordion-preloader">
		<img src="<?php echo esc_url( SP_EA_URL . 'public/assets/ea_loader.svg' ); ?>" alt="<?php esc_attr_e( 'Loader image', 'easy-accordion-free' ); ?>"/>
	</div>
	<?php
}
