<?php
/**
 * Uninstall.php for cleaning plugin database.
 *
 * Trigger the file when plugin is deleted.
 *
 * @since 1.0.0
 * @package   easy-accordion-free
 * @subpackage easy-accordion-free/includes
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Load LCP file.
require_once 'plugin-main.php';

$settings = get_option( 'sp_eap_settings' );
if ( true === ( $settings['eap_data_remove'] ) ) {
	// Delete Accordions and shortcodes.
	$accordions = get_posts(
		array(
			'post_type'      => array( 'sp_easy_accordion', 'sp_accordion_faqs' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $accordions ) ) {
		foreach ( $accordions as $accordion_id ) {
			wp_delete_post( $accordion_id, true );
		}
	}

	// Remove option.
	delete_option( 'sp_eap_flush_rewrite_rules' );
	delete_option( 'sp_eap_settings' );
	delete_option( 'sp_eafree_review_notice_dismiss' );
	delete_option( '_transient_timeout_sp-eap-framework-transient' );
	delete_option( '_transient_sp-eap-framework-transient' );
	delete_option( '_transient_timeout_eapro-metabox-transient' );
	delete_option( '_transient_eapro-metabox-transient' );
	delete_transient( 'spea_plugins' );
	delete_transient( 'spea_plugins_data' );

	// Remove options in Multisite.
	delete_site_option( 'sp_eap_settings' );
	delete_site_option( 'sp_eafree_review_notice_dismiss' );
	delete_site_option( '_transient_timeout_spf-eap-framework-transient' );
	delete_site_option( '_transient_spf-eap-framework-transient' );
	delete_site_option( '_transient_timeout_eapro-metabox-transient' );
	delete_site_option( '_transient_eapro-metabox-transient' );

	// Delete offer banner related option keys.
	delete_option( 'shapedplugin_offer_banner_dismissed_black_friday_2025' );
	delete_option( 'shapedplugin_offer_banner_dismissed_new_year_2026' );
} else {
	update_option( 'sp_eap_flush_rewrite_rules', false );
}
