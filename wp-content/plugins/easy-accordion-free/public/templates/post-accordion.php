<?php
/**
 * The post accordion template.
 *
 * This template can be overridden by copying it to yourtheme/easy-accordion-free/templates/post-accordion.php
 *
 * @package easy_accordion_free
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Accordion FAQs Query data.
$post_query_data  = self::accordion_post_query( $upload_data );
$count_total_post = $post_query_data['count_total_post'];
$eap_args         = $post_query_data['post_query'];
$post_query       = new WP_Query( $eap_args );
// Section Title template.
require self::eap_locate_template( 'templates-parts/section-title.php' );
?>
<div id="<?php echo esc_attr( $eap_accordion_uniq_id ); ?>">
<div id="sp-ea-<?php echo esc_attr( $post_id ); ?>" class="sp-ea-one sp-easy-accordion" data-ea-active="<?php echo esc_attr( $eap_active_event ); ?>" data-ea-mode="<?php echo esc_attr( $accordion_layout ); ?>" data-preloader="<?php echo esc_attr( $eap_preloader ); ?>" data-scroll-active-item="<?php echo esc_attr( $eap_scroll_to_active_item ); ?>" data-offset-to-scroll="<?php echo esc_attr( $eap_offset_to_scroll ); ?>">
<?php
// Accordion preloader template.
require self::eap_locate_template( 'templates-parts/preloader.php' );

if ( $post_query->have_posts() ) {
	global $wp_embed;
	$ea_key                         = 1;
	$eapro_allowed_description_tags = eapro_allowed_description_tags();
	while ( $post_query->have_posts() ) {
		$post_query->the_post();
		$key          = get_the_ID();
		$post_title   = get_the_title( $key );
		$post_content = get_the_content();

		// Generate accordion mode and icon markup.
		$accordion_mode      = self::accordion_mode( $eap_accordion_mode, $ea_key, $eap_expand_icon, $eap_collapse_icon );
		$eap_exp_icon_markup = $eap_icon ? '<i aria-hidden="true" role="presentation" class="ea-expand-icon eap-icon-ea-expand-' . $accordion_mode['expand_icon_first'] . '"></i> ' : '';
		$data_sptarget       = '#collapse' . $post_id . $key;
		$eap_icon_markup     = $eap_exp_icon_markup;
		$eap_single_collapse = ! $eap_mutliple_collapse ? 'data-parent="#sp-ea-' . esc_attr( $post_id ) . '"' : '';

		// Filter post title and content.
		$content_title = apply_filters( 'sp_easy_accordion_post_title', $post_title );
		$content       = apply_filters( 'sp_easy_accordion_post_content', $post_content );

		// Replace invalid characters and process block content if function exists.
		$content = str_replace( ']]>', ']]&gt;', $content );
		if ( function_exists( 'do_blocks' ) && has_blocks( $content ) ) {
			$content = do_blocks( $content );
		}
		// Process Elementor content.
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::instance()->documents->get( $key )->is_built_with_elementor() ) {
			$content = \Elementor\Plugin::$instance->frontend->get_builder_content( $key, true );
		}

		// Include single item template.
		require self::eap_locate_template( 'templates-parts/single-item.php' );
		++$ea_key;
	}
	wp_reset_postdata();
}
// Include Schema markup template.
require SP_EA_PATH . 'public/partials/schema-markup.php';
?>
</div>
</div>
