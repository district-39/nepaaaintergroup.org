<?php
/**
 * The post accordion template.
 *
 * This template can be overridden by copying it to yourtheme/easy-accordion-free/templates/default-accordion.php
 *
 * @package easy_accordion_free
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( empty( $content_sources ) ) {
	return;
}
// Include section title template.
require self::eap_locate_template( 'templates-parts/section-title.php' );
?>
<div id="<?php echo esc_attr( $eap_accordion_uniq_id ); ?>">
<div id="sp-ea-<?php echo esc_attr( $post_id ); ?>" class="sp-ea-one sp-easy-accordion" data-ea-active="<?php echo esc_attr( $eap_active_event ); ?>" data-ea-mode="<?php echo esc_attr( $accordion_layout ); ?>" data-preloader="<?php echo esc_attr( $eap_preloader ); ?>" data-scroll-active-item="<?php echo esc_attr( $eap_scroll_to_active_item ); ?>" data-offset-to-scroll="<?php echo esc_attr( $eap_offset_to_scroll ); ?>">

<?php
// Include section preloader template.
require self::eap_locate_template( 'templates-parts/preloader.php' );

global $wp_embed;
$ea_key                         = 1;
$eapro_allowed_description_tags = eapro_allowed_description_tags();
foreach ( $content_sources as $key => $content_source ) {
	$content_title       = $content_source['accordion_content_title'];
	$content             = apply_filters( 'sp_easy_accordion_content', $content_source['accordion_content_description'] );
	$content             = str_replace( ']]>', ']]&gt;', $content );
	$accordion_mode      = self::accordion_mode( $eap_accordion_mode, $ea_key, $eap_expand_icon, $eap_collapse_icon );
	$eap_exp_icon_markup = ( $eap_icon ) ? '<i aria-hidden="true" role="presentation" class="ea-expand-icon eap-icon-ea-expand-' . $accordion_mode['expand_icon_first'] . '"></i> ' : '';
	$data_sptarget       = '#collapse' . $post_id . $key;
	$eap_icon_markup     = $eap_exp_icon_markup;
	$eap_single_collapse = ! $eap_mutliple_collapse ? 'data-parent="#sp-ea-' . esc_attr( $post_id ) . '"' : '';
	// Include single item template.
	require self::eap_locate_template( 'templates-parts/single-item.php' );
	++$ea_key;
}
// Include Schema markup template.
require SP_EA_PATH . 'public/partials/schema-markup.php';
?>
</div>
</div>
