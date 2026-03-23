<?php
/**
 * Update version.
 *
 * @package easy-accordion-free
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

update_option( 'easy_accordion_free_version', '3.0.0' );
update_option( 'easy_accordion_free_db_version', '3.0.0' );

$args          = new \WP_Query(
	array(
		'post_type'      => array( 'sp_easy_accordion' ),
		'post_status'    => 'publish',
		'posts_per_page' => '500',
	)
);
$shortcode_ids = wp_list_pluck( $args->posts, 'ID' );
if ( count( $shortcode_ids ) > 0 ) {
	foreach ( $shortcode_ids as $shortcode_key => $shortcode_id ) {
		$shortcode_data = get_post_meta( $shortcode_id, 'sp_eap_shortcode_options', true );
		if ( ! is_array( $shortcode_data ) ) {
			continue;
		}

		// Update title color .
		$old_eap_title_color                         = isset( $shortcode_data['eap_title_typography']['color'] ) ? $shortcode_data['eap_title_typography']['color'] : '#444';
		$shortcode_data['eap_title_color']['color1'] = $old_eap_title_color;
		// Update content color.
		$old_eap_content_color           = isset( $shortcode_data['eap_content_typography']['color'] ) ? $shortcode_data['eap_content_typography']['color'] : '#444';
		$shortcode_data['eap_dsc_color'] = $old_eap_content_color;

		update_post_meta( $shortcode_id, 'sp_eap_shortcode_options', $shortcode_data );
	}
}
