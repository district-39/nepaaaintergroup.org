<?php
/**
 * The Metabox configuration.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    easy-accordion-free
 * @subpackage easy-accordion-free/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

//
// Metabox of the uppers section / Upload section.
// Set a unique slug-like ID.
//
$eap_accordion_content_source_settings = 'sp_eap_upload_options';

/**
 * Preview metabox.
 *
 * @param string $prefix The metabox main Key.
 * @return void
 */
SP_EAP::createMetabox(
	'sp_eap_live_preview',
	array(
		'title'        => __( 'Live Preview', 'easy-accordion-free' ),
		'post_type'    => 'sp_easy_accordion',
		'show_restore' => false,
		'context'      => 'normal',
	)
);
SP_EAP::createSection(
	'sp_eap_live_preview',
	array(
		'fields' => array(
			array(
				'type' => 'preview',
			),
		),
	)
);

//
// Metabox of the footer section / shortcode section.
// Set a unique slug-like ID.
//
$eap_display_shortcode = 'sp_eap_display_shortcode_sidebar';

//
// Create a metabox.
//
SP_EAP::createMetabox(
	$eap_display_shortcode,
	array(
		'title'     => __( 'How To Use', 'easy-accordion-free' ),
		'post_type' => 'sp_easy_accordion',
		'context'   => 'side',
	)
);


//
// Create a section.
//
SP_EAP::createSection(
	$eap_display_shortcode,
	array(
		'fields' => array(
			array(
				'type'  => 'shortcode',
				'class' => 'eap-admin-sidebar',
			),
		),
	)
);

SP_EAP::createMetabox(
	'eap_builder_option',
	array(
		'title'            => __( 'Page Builders', 'easy-accordion-free' ),
		'post_type'        => 'sp_easy_accordion',
		'context'          => 'side',
		'show_restore'     => false,
		'sp_lcp_shortcode' => false,
	)
);

SP_EAP::createSection(
	'eap_builder_option',
	array(
		'fields' => array(
			array(
				'type'      => 'shortcode',
				'shortcode' => 'builder_option',
				'class'     => 'eap-admin-sidebar',
			),
		),
	)
);


SP_EAP::createMetabox(
	'eap_pro_notice',
	array(
		'title'        => __( 'Unlock Pro Feature', 'easy-accordion-free' ),
		'post_type'    => 'sp_easy_accordion',
		'context'      => 'side',
		'show_restore' => false,
	)
);

SP_EAP::createSection(
	'eap_pro_notice',
	array(
		'fields' => array(
			array(
				'type'      => 'shortcode',
				'shortcode' => 'pro_notice',
				'class'     => 'eap-admin-sidebar',
			),
		),
	)
);

//
// Create a metabox.
//
SP_EAP::createMetabox(
	$eap_accordion_content_source_settings,
	array(
		'title'     => __( 'Easy Accordion', 'easy-accordion-free' ),
		'post_type' => 'sp_easy_accordion',
		'context'   => 'normal',
	)
);

//
// Create a section.
//
SP_EAP::createSection(
	$eap_accordion_content_source_settings,
	array(
		'fields' => array(
			array(
				'type'    => 'heading',
				'image'   => SP_EA_URL . 'admin/img/ea-logo.svg',
				'after'   => '<i class="fa fa-life-ring"></i> Support',
				'link'    => 'https://shapedplugin.com/support/?user=lite',
				'class'   => 'eap-admin-header',
				'version' => SP_EA_VERSION,
			),
			array(
				'id'       => 'eap_accordion_type',
				'type'     => 'button_set',
				'class'    => 'eap_accordion_type',
				'title'    => __( 'Content Type', 'easy-accordion-free' ),
				'sanitize' => 'sanitize_text_field',
				'options'  => array(
					'content-accordion' => array(
						'text' => '<img src="' . SP_EA_URL . 'admin/img/ea-content.svg"/>' . __( 'Custom', 'easy-accordion-free' ),
					),
					'post-accordion'    => array(
						'text' => '<img src="' . SP_EA_URL . 'admin/img/ea-post.svg"/>' . __( 'Post', 'easy-accordion-free' ),
					),
					'image-accordion'   => array(
						'text'     => '<img src="' . SP_EA_URL . 'admin/img/ea-image.svg"/>' . __( 'Image', 'easy-accordion-free' ),
						'pro_only' => true,
					),
				),
				'default'  => 'content-accordion',
			),
			// Content Accordion.
			array(
				'id'                     => 'accordion_content_source',
				'type'                   => 'group',
				'title'                  => __( 'Custom Content', 'easy-accordion-free' ),
				'button_title'           => '<i class="fa fa-plus-circle eap-add-content" aria-hidden="true"></i>' . __( 'Add New Item', 'easy-accordion-free' ),
				'class'                  => 'eap_accordion_content_wrapper',
				'accordion_title_prefix' => __( 'Item :', 'easy-accordion-free' ),
				'accordion_title_number' => true,
				'accordion_title_auto'   => true,
				'sanitize'               => 'eapro_sanitize_accordion_title_content',
				'fields'                 => array(
					array(
						'id'         => 'accordion_content_title',
						'class'      => 'accordion_content_title',
						'type'       => 'text',
						'wrap_class' => 'eap_accordion_content_source',
						'title'      => __( 'Title', 'easy-accordion-free' ),
					),
					array(
						'id'           => 'accordion_content_icon',
						'type'         => 'icon',
						'class'        => 'pro_only_field accordion_content_icon',
						'help'         => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea_custom_icon.svg" alt="' . __( 'custom item', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Custom Icon (Title Icon) (Pro)', 'easy-accordion-free' ) . '</div>',
						'wrap_class'   => 'eap_accordion_content_source',
						'button_title' => __( 'Custom Icon (Pro)', 'easy-accordion-free' ),
					),
					array(
						'id'         => 'accordion_content_description',
						'type'       => 'wp_editor',
						'wrap_class' => 'eap_accordion_content_source',
						'title'      => __( 'Description', 'easy-accordion-free' ),
						'height'     => '150px',
					),
				),
				'dependency'             => array( 'eap_accordion_type', '==', 'content-accordion' ),
			), // End of Content Accordion.
			array(
				'id'         => 'eap_post_type',
				'type'       => 'select',
				'title'      => __( 'Post Type', 'easy-accordion-free' ),
				'class'      => 'sp_eap_post_type only-select-for-pro',
				'attributes' => array(
					'placeholder' => __( 'Select Post Type', 'easy-accordion-free' ),
					'style'       => 'min-width: 150px;',
				),
				'options'    => array(
					'sp_accordion_faqs' => __( 'All FAQs', 'easy-accordion-free' ),
					'2'                 => __( 'Posts (Pro)', 'easy-accordion-free' ),
					'3'                 => __( 'Pages (Pro)', 'easy-accordion-free' ),
					'4'                 => __( 'Product (Pro)', 'easy-accordion-free' ),
					'5'                 => __( 'All Custom Posts (Pro)', 'easy-accordion-free' ),
				),
				'default'    => 'sp_accordion_faqs',
				'dependency' => array( 'eap_accordion_type', '==', 'post-accordion' ),
			),
			array(
				'id'         => 'eap_display_posts_from',
				'type'       => 'select',
				'class'      => 'sp_eap_post_type only-select-for-pro',
				'only_pro'   => true,
				'title'      => __( 'Filter Posts', 'easy-accordion-free' ),
				'options'    => array(
					'latest'        => __( 'Latest', 'easy-accordion-free' ),
					'taxonomy'      => __( 'Taxonomy (Pro)', 'easy-accordion-free' ),
					'specific_post' => __( 'Specific Posts (Pro)', 'easy-accordion-free' ),
				),
				'default'    => 'latest',
				'dependency' => array( 'eap_accordion_type', '==', 'post-accordion' ),
			),
			array(
				'id'         => 'post_order_by',
				'type'       => 'select',
				'class'      => 'sp_eap_post_type only-select-for-pro',
				'title'      => __( 'Order By', 'easy-accordion-free' ),
				'options'    => array(
					'ID'         => __( 'ID', 'easy-accordion-free' ),
					'date'       => __( 'Date', 'easy-accordion-free' ),
					'rand'       => __( 'Random (Pro)', 'easy-accordion-free' ),
					'title'      => __( 'Title (Pro)', 'easy-accordion-free' ),
					'modified'   => __( 'Modified (Pro)', 'easy-accordion-free' ),
					'menu_order' => __( 'Menu Order (Pro)', 'easy-accordion-free' ),
					'post__in'   => __( 'Drag & Drop (Pro)', 'easy-accordion-free' ),
				),
				'sanitize'   => 'sanitize_text_field',
				'default'    => 'date',
				'dependency' => array( 'eap_accordion_type', '==', 'post-accordion' ),
			),
			array(
				'id'         => 'post_order',
				'type'       => 'select',
				'title'      => __( 'Order', 'easy-accordion-free' ),
				'options'    => array(
					'ASC'  => __( 'Ascending', 'easy-accordion-free' ),
					'DESC' => __( 'Descending', 'easy-accordion-free' ),
				),
				'default'    => 'DESC',
				'sanitize'   => 'sanitize_text_field',
				'attributes' => array( 'data-depend-id' => 'post_order' ),
				'dependency' => array( 'eap_accordion_type', '==', 'post-accordion' ),
			),
			array(
				'type'    => 'notice',
				'class'   => 'only_pro_notice',
				'content' => sprintf(
				/* translators: 1: start link tag, 2: close tag. */
					__(
						'To show Accordion or FAQ from %3$sPosts%6$s, Pages, %4$sProducts%6$s, Custom Post Types, and %5$sImage Accordion%6$s, %1$sUpgrade to Pro!%2$s',
						'easy-accordion-free'
					),
					'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>',
					'</b></a>',
					'<a href="https://easyaccordion.io/faqs-from-posts-pages-custom-post-types-taxonomies/" target="_blank"><b>',
					'<a href="https://easyaccordion.io/woocommerce-product-accordion/" target="_blank"><b>',
					'<a href="https://easyaccordion.io/image-accordion/" target="_blank"><b>',
					'</a></b>'
				),
			),

		), // End of fields array.
	)
);

//
// Metabox for the Accordion Post Type.
// Set a unique slug-like ID.
//
$eap_accordion_shortcode_settings = 'sp_eap_shortcode_options';

//
// Create a metabox.
//
SP_EAP::createMetabox(
	$eap_accordion_shortcode_settings,
	array(
		'title'     => __( 'Shortcode Section', 'easy-accordion-free' ),
		'post_type' => 'sp_easy_accordion',
		'theme'     => 'light',
		'context'   => 'normal',
	)
);
//
// Create a section.
//
SP_EAP::createSection(
	$eap_accordion_shortcode_settings,
	array(
		'title'  => __( 'Accordion Settings', 'easy-accordion-free' ),
		'icon'   => 'fa fa-list-ul',
		'fields' => array(
			array(
				'id'         => 'eap_accordion_layout',
				'class'      => 'eap_accordion_layout',
				'type'       => 'image_select',
				'title'      => __( 'Layout Preset', 'easy-accordion-free' ),
				'subtitle'   => __( 'Choose an accordion layout preset.', 'easy-accordion-free' ),
				'sanitize'   => 'sanitize_text_field',
				'options'    => array(
					'vertical'     => array(
						'image'           => SP_EA_URL . 'admin/img/ea-vertical.svg',
						'option_name'     => __( 'Vertical', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/vertical-accordion/',
					),
					'multi-column' => array(
						'pro_only'        => true,
						'image'           => SP_EA_URL . 'admin/img/ea-multicolumn.svg',
						'option_name'     => __( 'Multicolumn', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/multicolumn-accordion/',
					),
					'horizontal'   => array(
						'pro_only'        => true,
						'image'           => SP_EA_URL . 'admin/img/ea-horizontal.svg',
						'option_name'     => __( 'Horizontal', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/horizontal-accordion/',
					),
				),
				'title_info' => sprintf(
					'<div class="ea-info-label">%s</div> <div class="ea-short-content">%s</div>',
					__( 'Accordion Layout', 'easy-accordion-free' ),
					__( 'Accordion Layout determines how accordion information is displayed, with choices like vertical, horizontal, or multi column layouts.', 'easy-accordion-free' )
				),
				'desc'       => sprintf(
					/* translators: 1: start link tag, 2: close tag,3: start link tag, 4: close tag. */
					__(
						'To create fantastic Accordion %3$sFAQ layouts%4$s and access to advanced customizations, %1$sUpgrade to Pro!%2$s',
						'easy-accordion-free'
					),
					'<a href="https://easyaccordion.io/pricing/" target="_blank"><strong>',
					'</strong></a>',
					'<a href="https://easyaccordion.io/vertical-accordion/" target="_blank"><strong>',
					'</strong></a>'
				),
				'default'    => 'vertical',
			),
			array(
				'id'              => 'accordion_margin_bottom',
				'type'            => 'spacing',
				'title'           => __( 'Space Between', 'easy-accordion-free' ),
				'subtitle'        => __( 'Set a margin to make space between accordion items.', 'easy-accordion-free' ),
				'all'             => true,
				'all_icon'        => '<i class="fa fa-arrows-v"></i>',
				'sanitize'        => 'eapro_sanitize_number_array_field',
				'all_placeholder' => 'margin',
				'default'         => array(
					'all' => '10',
				),
				'units'           => array(
					'px',
				),
				'attributes'      => array(
					'min' => 0,
				),
				'title_info'      => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-accordion-margin.svg" alt="' . __( 'Space Between Accordion FAQs', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Space Between Accordion FAQs', 'easy-accordion-free' ) . '</div>',
			),
			array(
				'id'         => 'eap_accordion_event',
				'type'       => 'button_set',
				'sanitize'   => 'sanitize_text_field',
				'title'      => __( 'Activator Event', 'easy-accordion-free' ),
				'subtitle'   => __( 'Set an activator event to switch between accordion items.', 'easy-accordion-free' ),
				'options'    => array(
					'ea-click' => array(
						'text' => __( 'Click', 'easy-accordion-free' ),
					),
					'ea-hover' => array(
						'text' => __( 'Mouse Over', 'easy-accordion-free' ),
					),
					'ea-auto'  => array(
						'text'     => __( 'AutoPlay', 'easy-accordion-free' ),
						'pro_only' => true,
					),
				),
				/* translators: 1: start strong tag, 2: close tag. */
				'title_info' => '<div class="ea-info-label">' . __( 'Activator Event', 'easy-accordion-free' ) . '</div> <div class="ea-short-content">' . sprintf( __( 'The %1$sActivator Event%2$s option allows you to define the user interaction that triggers accordion transitions, such as clicking, hovering, or autoplay.', 'easy-accordion-free' ), '<strong>', '</strong>' ) . '</div><div class="info-button"><a class="ea-open-live-demo" href="https://easyaccordion.io/activator-events/" target="_blank">' . __( 'Live Demo', 'easy-accordion-free' ) . '</a></div>',
				'default'    => 'ea-click',
			),
			array(
				'id'         => 'eap_accordion_mode',
				'class'      => 'eap_accordion_open_mode',
				'type'       => 'radio',
				'sanitize'   => 'sanitize_text_field',
				'title'      => __( 'Accordion Mode', 'easy-accordion-free' ),
				'subtitle'   => __( 'Expand or collapse accordion option on page load.', 'easy-accordion-free' ),
				'options'    => array(
					'ea-first-open' => __( 'First Open', 'easy-accordion-free' ),
					'ea-multi-open' => __( 'All Open', 'easy-accordion-free' ),
					'ea-all-close'  => __( 'All Folded', 'easy-accordion-free' ),
					'custom'        => __( 'Custom Open (Pro)', 'easy-accordion-free' ),
				),
				/* translators: 1: start strong tag, 2: close tag. */
				'title_info' => '<div class="ea-info-label">' . __( 'Accordion Mode', 'easy-accordion-free' ) . '</div> <div class="ea-short-content">' . sprintf( __( 'The %1$sAccordion Mode%2$s  option lets you choose whether the accordion items should be initially expanded or collapsed when the page loads.', 'easy-accordion-free' ), '<strong>', '</strong>' ) . '</div><div class="info-button"><a class="ea-open-live-demo" href="https://easyaccordion.io/accordion-modes/" target="_blank">' . __( 'Live Demo', 'easy-accordion-free' ) . '</a></div>',
				'default'    => 'ea-first-open',
			),
			array(
				'id'         => 'eap_mutliple_collapse',
				'type'       => 'switcher',
				'title'      => __( 'Multiple Active Together', 'easy-accordion-free' ),
				'subtitle'   => __( 'Don\'t collapse while expanding another item.', 'easy-accordion-free' ),
				'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
				'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
				'text_width' => 100,
				'default'    => false,
				'sanitize'   => 'rest_sanitize_boolean',
				'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-multiple-opening-together.svg" alt="' . __( 'Multiple Active or Opening Together', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Multiple Active or Opening Together', 'easy-accordion-free' ) . '</div>',
			),
			array(
				'id'         => 'eap_scroll_to_active_item',
				'type'       => 'switcher',
				'title'      => __( 'Scroll to Active Item', 'easy-accordion-free' ),
				'subtitle'   => __( 'Enable/Disable this option to scroll to  active accordion item.', 'easy-accordion-free' ),
				'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
				'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
				'text_width' => 100,
				'default'    => false,
				'sanitize'   => 'rest_sanitize_boolean',
				'title_info' => sprintf(
					'<div class="ea-info-label">%s</div> <div class="ea-short-content">%s</div><div class="info-button"><a class="ea-open-docs" href="https://docs.shapedplugin.com/docs/easy-accordion-free/configurations/how-to-enable-accordion-scrolling-to-active-item/" target="_blank">%s</a></div>',
					__( 'Scroll to Active Item', 'easy-accordion-free' ),
					__( 'This option allows automatic scrolling to the active accordion item. This provides a smoother and more user-friendly experience when navigating through accordion faqs section.', 'easy-accordion-free' ),
					__( 'Open Docs', 'easy-accordion-free' )
				),
			),
			array(
				'id'         => 'eap_schema_markup',
				'type'       => 'switcher',
				'title'      => __( 'Schema Markup', 'easy-accordion-free' ),
				'subtitle'   => __( 'Enable/Disable schema markup.', 'easy-accordion-free' ),
				'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
				'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
				'text_width' => 100,
				'default'    => false,
				'sanitize'   => 'rest_sanitize_boolean',
				'title_info' => sprintf(
					'<div class="ea-info-label">%s</div> <div class="ea-short-content"><strong>%s</strong> %s</div><div class="info-button"><a class="ea-open-docs" href="https://docs.shapedplugin.com/docs/easy-accordion-free/configurations/how-to-enable-schema-markup/" target="_blank">%s</a></div>',
					__( 'Schema Markup', 'easy-accordion-free' ),
					__( 'Schema Markup', 'easy-accordion-free' ),
					__( 'adds structured data to your Accordion FAQs, enhancing search engine visibility and improving the display of your Accordion FAQs in search results.', 'easy-accordion-free' ),
					__( 'Open Docs', 'easy-accordion-free' )
				),
			),
			array(
				'id'         => 'eap_preloader',
				'type'       => 'switcher',
				'sanitize'   => 'rest_sanitize_boolean',
				'title'      => __( 'Preloader', 'easy-accordion-free' ),
				'subtitle'   => __( 'Accordion will be hidden until page load completed.', 'easy-accordion-free' ),
				'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
				'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
				'text_width' => 100,
				'default'    => false,
			),
			array(
				'id'         => 'eap_faq_search',
				'type'       => 'switcher',
				'class'      => 'only-for-pro-switcher',
				'title'      => __( 'Display FAQ Search', 'easy-accordion-free' ),
				'subtitle'   => __( 'Show/hide accordion FAQ search field.', 'easy-accordion-free' ),
				'text_on'    => __( 'Show', 'easy-accordion-free' ),
				'text_off'   => __( 'Hide', 'easy-accordion-free' ),
				'text_width' => 80,
				'default'    => false,
				'sanitize'   => 'rest_sanitize_boolean',
				'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-accordion-faq-search.svg" alt="' . __( 'Accordion FAQ Search', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Accordion FAQ Search', 'easy-accordion-free' ) . '</div><div class="info-button img"><a class="ea-open-live-demo " href="https://easyaccordion.io/faqs-search-option/" target="_blank">' . __( 'Live Demo', 'easy-accordion-free' ) . '</a></div>',
			),
			array(
				'id'         => 'eap_faq_collapse_button',
				'type'       => 'switcher',
				'class'      => 'only-for-pro-switcher',
				'title'      => __( 'Expand/Collapse All Button', 'easy-accordion-free' ),
				'subtitle'   => __( 'Show/hide expand/collapse all button.', 'easy-accordion-free' ),
				'text_on'    => __( 'Show', 'easy-accordion-free' ),
				'text_off'   => __( 'Hide', 'easy-accordion-free' ),
				'text_width' => 80,
				'default'    => false,
				'sanitize'   => 'rest_sanitize_boolean',
				'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-expand-collapse-all-button.svg" alt="' . __( 'Expand/Collapse All Button', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Expand/Collapse All Button', 'easy-accordion-free' ) . '</div><div class="info-button img"><a class="ea-open-live-demo" href="https://easyaccordion.io/expand-collapse-all/" target="_blank">' . __( 'Live Demo', 'easy-accordion-free' ) . '</a></div>',
			),
			array(
				'type'    => 'notice',
				'class'   => 'only_pro_notice',
				'content' => sprintf(
					/* translators: 1: start link tag, 2: close tag,3: start link tag, 4: close tag,5: start link tag, 6: close tag; */
					__(
						'Want to make your %3$sFAQ Content Searchable%4$s and add an %5$sExpand/Collapse All%6$s button? %1$sUpgrade to Pro!%2$s',
						'easy-accordion-free'
					),
					'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>',
					'</b></a>',
					'<a href="https://easyaccordion.io/faqs-search-option/" target="_blank"><b>',
					'</b></a>',
					'<a href="https://easyaccordion.io/expand-collapse-all/" target="_blank"><b>',
					'</b></a>'
				),
			),
		), // Fields array end.
	)
); // End of Upload section.
//
// Carousel settings section begin.
//
SP_EAP::createSection(
	$eap_accordion_shortcode_settings,
	array(
		'title'  => '<span>' . __( 'Theme Settings', 'easy-accordion-free' ) . '</span>',
		'icon'   => 'eap-icon-theme-settings',
		'fields' => array(
			array(
				'id'         => 'eap_accordion_theme',
				'type'       => 'image_select',
				'title'      => __( 'Select Your Theme', 'easy-accordion-free' ),
				'class'      => 'sp_eap_accordion_theme',
				'subtitle'   => sprintf(
				/* translators: 1: start link tag, 2: close tag. */
					__( 'Select a theme style for your accordion FAQs. See %1$sAll the Themes%2$s at a glance.', 'easy-accordion-free' ),
					'<strong><a class="ea-link" href="https://easyaccordion.io/all-accordion-themes/" target="_blank">',
					'</a></strong>'
				),
				'options'    => array(
					'sp-ea-one'       => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-one.svg',
						'option_name'     => __( 'Default Theme', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-1',
					),
					'sp-ea-two'       => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-two.svg',
						'option_name'     => __( 'Theme Two', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-2',
					),
					'sp-ea-three'     => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-three.svg',
						'option_name'     => __( 'Theme Three', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-3',
					),
					'sp-ea-four'      => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-four.svg',
						'option_name'     => __( 'Theme Four', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-4',
					),
					'sp-ea-five'      => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-five.svg',
						'option_name'     => __( 'Theme Five', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-5',
					),
					'sp-ea-six'       => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-six.svg',
						'option_name'     => __( 'Theme Six', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-6',
					),
					'sp-ea-seven'     => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-seven.svg',
						'option_name'     => __( 'Theme Seven', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-7',
					),
					'sp-ea-eight'     => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-eight.svg',
						'option_name'     => __( 'Theme Eight', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-8',
					),
					'sp-ea-nine'      => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-nine.svg',
						'option_name'     => __( 'Theme Nine', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-9',
					),
					'sp-ea-ten'       => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-ten.svg',
						'option_name'     => __( 'Theme Ten', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-10',
					),
					'sp-ea-eleven'    => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-eleven.svg',
						'option_name'     => __( 'Theme Eleven', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-11',
					),
					'sp-ea-twelve'    => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-twelve.svg',
						'option_name'     => __( 'Theme Twelve', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-12',
					),
					'sp-ea-thirteen'  => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-thirteen.svg',
						'option_name'     => __( 'Theme Thirteen', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-13',
					),
					'sp-ea-fourteen'  => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-fourteen.svg',
						'option_name'     => __( 'Theme Fourteen', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-14',
					),
					'sp-ea-fifteen'   => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-fifteen.svg',
						'option_name'     => __( 'Theme Fifteen', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-15',
					),
					'sp-ea-sixteen'   => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-sixteen.svg',
						'option_name'     => __( 'Theme Sixteen', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-16',
					),
					'sp-ea-seventeen' => array(
						'image'           => SP_EA_URL . 'admin/img/theme-preview/sp-ea-seventeen.svg',
						'option_name'     => __( 'Theme Seventeen', 'easy-accordion-free' ),
						'option_demo_url' => 'https://easyaccordion.io/all-accordion-themes/#theme-17',
					),
				),
				'default'    => 'sp-ea-one',
				'dependency' => array( 'eap_accordion_layout', 'any', 'vertical,multi-column', true ),
			),
			array(
				'type'    => 'notice',
				'class'   => 'only_pro_notice',
				'content' => sprintf(
				/* translators: 1: start link tag, 2: close tag. */
					__(
						'To impress your visitors or users with professionally designed FAQ themes/templates, %1$sUpgrade to Pro!%2$s',
						'easy-accordion-free'
					),
					'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>',
					'</b></a>'
				),
			),
		),
	)
);
	//
	// Carousel settings section begin.
	//
	SP_EAP::createSection(
		$eap_accordion_shortcode_settings,
		array(
			'title'  => __( 'Display Settings', 'easy-accordion-free' ),
			'icon'   => 'fa fa-th-large',
			'fields' => array(
				array(
					'type'  => 'tabbed',
					'class' => 'eap-display-tabs',
					'tabs'  => array(
						array(
							'title'  => __( 'Title & Description', 'easy-accordion-free' ),
							'icon'   => '<span><i class="eap-icon-title"></i></span>',
							'fields' => array(
								array(
									'id'         => 'section_title',
									'type'       => 'switcher',
									'title'      => __( 'Accordion Section Title', 'easy-accordion-free' ),
									'subtitle'   => __( 'Show/hide the accordion section title.', 'easy-accordion-free' ),
									'text_on'    => __( 'Show', 'easy-accordion-free' ),
									'text_off'   => __( 'Hide', 'easy-accordion-free' ),
									'text_width' => 80,
									'default'    => false,
									'sanitize'   => 'rest_sanitize_boolean',
								),
								array(
									'id'         => 'eap_border_css',
									'type'       => 'border',
									'title'      => __( 'Item Border', 'easy-accordion-free' ),
									'subtitle'   => __( 'Set accordion item border.', 'easy-accordion-free' ),
									'all'        => true,
									'sanitize'   => 'eapro_sanitize_border_field',
									'default'    => array(
										'all'   => 1,
										'style' => 'solid',
										'color' => '#e2e2e2',
									),
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-accordion-border.svg" alt="' . __( 'Accordion Border', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Accordion Border', 'easy-accordion-free' ) . '</div>',
								),
								array(
									'type'    => 'subheading',
									'content' => __( 'Title', 'easy-accordion-free' ),
								),
								array(
									'id'       => 'ea_title_heading_tag',
									'type'     => 'select',
									'title'    => __( 'Title HTML Tag', 'easy-accordion-free' ),
									'subtitle' => __( 'Select Tag for accordion title.', 'easy-accordion-free' ),
									'options'  => array(
										'1' => __( 'H1', 'easy-accordion-free' ),
										'2' => __( 'H2', 'easy-accordion-free' ),
										'3' => __( 'H3', 'easy-accordion-free' ),
										'4' => __( 'H4', 'easy-accordion-free' ),
										'5' => __( 'H5', 'easy-accordion-free' ),
										'6' => __( 'H6', 'easy-accordion-free' ),
									),
									'sanitize' => 'sanitize_text_field',
									'default'  => '3',
									'radio'    => true,
								),
								array(
									'id'       => 'eap_title_color',
									'type'     => 'color_group',
									'title'    => __( 'Title Color', 'easy-accordion-free' ),
									'subtitle' => __( 'Set accordion title text color.', 'easy-accordion-free' ),
									'options'  => array(
										'color1' => __( 'Color', 'easy-accordion-free' ),
									),
									'default'  => array(
										'color1' => '#444',
									),
								),
								array(
									'id'       => 'eap_header_bg_color_type',
									'type'     => 'button_set',
									'title'    => __( 'Title Background Color Type ', 'easy-accordion-free' ),
									'subtitle' => __( 'Choose a color type for the title background.', 'easy-accordion-free' ),
									'options'  => array(
										'solid'    => array(
											'text' => __( 'Solid', 'easy-accordion-free' ),
										),
										'gradient' => array(
											'text'     => __( 'Gradient', 'easy-accordion-free' ),
											'pro_only' => true,
										),
									),
									'only_pro' => true,
									'default'  => array( 'solid' ),
									'sanitize' => 'sanitize_text_field',
								),
								array(
									'id'       => 'eap_header_bg_color',
									'type'     => 'color',
									'title'    => __( 'Title Background Color', 'easy-accordion-free' ),
									'subtitle' => __( 'Set accordion title background color.', 'easy-accordion-free' ),
									'default'  => '#eee',
									'sanitize' => 'sanitize_text_field',
								),
								array(
									'id'       => 'eap_nofollow_link',
									'type'     => 'checkbox',
									'title'    => __( 'Add rel="nofollow" to Link', 'easy-accordion-free' ),
									'default'  => false,
									'sanitize' => 'rest_sanitize_boolean',
								),
								array(
									'id'         => 'eap_title_padding',
									'type'       => 'spacing',
									'sanitize'   => 'eapro_sanitize_number_array_field',
									'class'      => 'only-for-pro',
									'title'      => __( 'Title Padding', 'easy-accordion-free' ),
									'subtitle'   => __( 'Set accordion title custom padding.', 'easy-accordion-free' ),
									'units'      => array( 'px' ),
									'default'    => array(
										'left'   => '15',
										'top'    => '15',
										'bottom' => '15',
										'right'  => '15',

									),
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-title-padding.svg" alt="' . __( 'Title Padding', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Title Padding', 'easy-accordion-free' ) . '</div>',
								),
								array(
									'type'    => 'subheading',
									'content' => __( 'Title Featured Icon', 'easy-accordion-free' ),
								),
								array(
									'id'         => 'eap_title_icon',
									'type'       => 'switcher',
									'class'      => 'only-for-pro-switcher',
									'title'      => __( 'Title Icon', 'easy-accordion-free' ),
									'subtitle'   => __( 'Show title featured icon or custom image before title.', 'easy-accordion-free' ),
									'text_on'    => __( 'Show', 'easy-accordion-free' ),
									'text_off'   => __( 'Hide', 'easy-accordion-free' ),
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea_custom_icon.svg" alt="' . __( 'Title Featured Icon', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Title Featured Icon', 'easy-accordion-free' ) . '</div>',
									'text_width' => 80,
									'default'    => false,
									'sanitize'   => 'rest_sanitize_boolean',
								),
								array(
									'id'              => 'eap_title_icon_size',
									'type'            => 'spacing',
									'sanitize'        => 'eapro_sanitize_number_array_field',
									'class'           => 'only-for-pro',
									'title'           => __( 'Title Icon Size ', 'easy-accordion-free' ),
									'subtitle'        => __( 'Set title icon size.', 'easy-accordion-free' ),
									'all'             => true,
									'all_icon'        => false,
									'all_placeholder' => '',
									'default'         => array(
										'all'   => 20,
										'units' => 'px',
									),
									'units'           => array(
										'px',
									),
								),
								array(
									'type'    => 'subheading',
									'content' => __( 'Description', 'easy-accordion-free' ),
								),
								array(
									'id'       => 'eap_dsc_color',
									'type'     => 'color',
									'title'    => __( 'Description Color', 'easy-accordion-free' ),
									'subtitle' => __( 'Set accordion description text color.', 'easy-accordion-free' ),
									'default'  => '#444',
									'sanitize' => 'sanitize_text_field',
								),
								array(
									'id'       => 'eap_description_bg_color',
									'type'     => 'color',
									'title'    => __( 'Description Background Color', 'easy-accordion-free' ),
									'subtitle' => __( 'Set accordion description background color.', 'easy-accordion-free' ),
									'default'  => '#fff',
									'sanitize' => 'sanitize_text_field',
								),
								array(
									'id'         => 'eap_description_padding',
									'type'       => 'spacing',
									'sanitize'   => 'eapro_sanitize_number_array_field',
									'class'      => 'only-for-pro',
									'title'      => __( 'Description Padding', 'easy-accordion-free' ),
									'subtitle'   => __( 'Set accordion description custom padding.', 'easy-accordion-free' ),
									'units'      => array( 'px' ),
									'default'    => array(
										'left'   => '15',
										'top'    => '15',
										'bottom' => '15',
										'right'  => '15',
									),
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-description-padding.svg" alt="' . __( 'Description Padding', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Description Padding', 'easy-accordion-free' ) . '</div>',
								),
								array(
									'id'         => 'eap_accordion_fillspace',
									'type'       => 'checkbox',
									'title'      => __( 'Fixed Content Height', 'easy-accordion-free' ),
									'subtitle'   => __( 'Check to display collapsible accordion content in a limited amount of space.', 'easy-accordion-free' ),
									'default'    => false,
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-fixed-content-height.svg" alt="' . __( 'Fixed Content Height', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Fixed Content Height', 'easy-accordion-free' ) . '</div>',
									'sanitize'   => 'rest_sanitize_boolean',
								),
								array(
									'id'              => 'eap_accordion_fillspace_height',
									'type'            => 'spacing',
									'title'           => __( 'Maximum Height', 'easy-accordion-free' ),
									'subtitle'        => __( 'Set fixed accordion content panel height. Default height 200px.', 'easy-accordion-free' ),
									'class'           => 'accordion-fillspace-height',
									'all'             => true,
									'all_icon'        => '<i class="fa fa-arrows-v"></i>',
									'all_placeholder' => __( 'Height', 'easy-accordion-free' ),
									'units'           => array(
										'px',
									),
									'default'         => array(
										'all' => '200',
									),
									'attributes'      => array(
										'min' => 50,
									),
									'sanitize'        => 'eapro_sanitize_number_array_field',
									'dependency'      => array( 'eap_accordion_fillspace', '==', 'true' ),
								),
								array(
									'id'         => 'eap_autop',
									'type'       => 'switcher',
									'title'      => __( 'Line Break', 'easy-accordion-free' ),
									'subtitle'   => __( 'wpautop/line break with paragraph in description.', 'easy-accordion-free' ),
									'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
									'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
									'text_width' => 100,
									'default'    => true,
									'sanitize'   => 'rest_sanitize_boolean',
								),
								array(
									'type'    => 'notice',
									'class'   => 'only_pro_notice',
									'content' => sprintf(
									/* translators: 1: start link tag, 2: close tag. */
										__(
											'Want to make your Accordion FAQs visually attractive with tons of flexible options? %1$sUpgrade to Pro!%2$s',
											'easy-accordion-free'
										),
										'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>',
										'</b></a>'
									),
								),
							),
						),
						array(
							'title'  => __( 'Expand & Collapse Icon', 'easy-accordion-free' ),
							'icon'   => '<span><i class="eap-icon-expand-collapse"></i></span>',
							'class'  => 'eap-not-image-accordion',
							'fields' => array(
								array(
									'id'         => 'eap_expand_close_icon',
									'type'       => 'switcher',
									'title'      => __( 'Expand & Collapse Icon', 'easy-accordion-free' ),
									'subtitle'   => __( 'Show/hide expand and collapse icon.', 'easy-accordion-free' ),
									'text_on'    => __( 'Show', 'easy-accordion-free' ),
									'text_off'   => __( 'Hide', 'easy-accordion-free' ),
									'text_width' => 80,
									'default'    => true,
									'sanitize'   => 'rest_sanitize_boolean',
								),
								array(
									'id'         => 'eap_expand_collapse_icon',
									'class'      => 'eap_expand_collapse_icon',
									'type'       => 'image_select',
									'title'      => __( 'Expand & Collapse Icon Style', 'easy-accordion-free' ),
									'subtitle'   => __( 'Choose a expand and collapse icon style.', 'easy-accordion-free' ),
									'title_info' => sprintf(
										'<div class="ea-short-content"><strong>%s</strong> %s</div>',
										__( 'Note:', 'easy-accordion-free' ),
										__( 'The first icon is free. Other blurry icons are pro.', 'easy-accordion-free' )
									),
									'sanitize'   => 'sanitize_text_field',
									'options'    => array(
										'1'  => array(
											'image' => SP_EA_URL . 'admin/img/collapse-expand-icon/plus-minus.svg',
										),
										'19' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/plus-times.svg',
											'pro_only' => true,
										),
										'5'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/check-times.svg',
											'pro_only' => true,
										),
										'6'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/chevron-down-right.svg',
											'pro_only' => true,
										),
										'13' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-down-up.svg',
											'pro_only' => true,
										),
										'9'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-up-down-9.svg',
											'pro_only' => true,
										),
										'2'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-down-right-7.svg',
											'pro_only' => true,
										),
										'18' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-down-up-18.svg',
											'pro_only' => true,
										),
										'3'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-double-down-right.svg',
											'pro_only' => true,
										),
										'15' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-double-down-up.svg',
											'pro_only' => true,
										),
										'10' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/angle-double-up-down.svg',
											'pro_only' => true,
										),
										'8'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/caret-down-right.svg',
											'pro_only' => true,
										),
										'17' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/caret-up-down-14.svg',
											'pro_only' => true,
										),
										'14' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/caret-down-up.svg',
											'pro_only' => true,
										),
										'4'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/arrow-down-right.svg',
											'pro_only' => true,
										),
										'16' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/arrow-down-up.svg',
											'pro_only' => true,
										),
										'11' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/arrow-up-down-18.svg',
											'pro_only' => true,
										),
										'7'  => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/hand-o-down-right.svg',
											'pro_only' => true,
										),
										'20' => array(
											'image'    => SP_EA_URL . 'admin/img/collapse-expand-icon/q-a-img.svg',
											'pro_only' => true,
										),
									),
									'default'    => '1',
									'dependency' => array(
										'eap_expand_close_icon',
										'==',
										'true',
									),
								),

								array(
									'id'              => 'eap_icon_size',
									'type'            => 'spacing',
									'title'           => __( 'Expand & Collapse Icon Size', 'easy-accordion-free' ),
									'subtitle'        => __( 'Set accordion collapse and expand icon size. Default value is 16px.', 'easy-accordion-free' ),
									'all'             => true,
									'all_icon'        => false,
									'all_placeholder' => 'speed',
									'sanitize'        => 'eapro_sanitize_number_array_field',
									'default'         => array(
										'all' => '16',
									),
									'units'           => array(
										'px',
									),
									'attributes'      => array(
										'min' => 0,
									),
									'dependency'      => array(
										'eap_expand_close_icon',
										'==',
										'true',
									),
								),
								array(
									'id'         => 'eap_icon_color_set',
									'type'       => 'color',
									'title'      => __( 'Icon Color', 'easy-accordion-free' ),
									'subtitle'   => __( 'Set icon color.', 'easy-accordion-free' ),
									'default'    => '#444',
									'sanitize'   => 'sanitize_text_field',
									'dependency' => array(
										'eap_expand_close_icon',
										'==',
										'true',
									),
								),
								array(
									'id'         => 'eap_icon_position',
									'type'       => 'button_set',
									'title'      => __( 'Expand & Collapse Icon Position', 'easy-accordion-free' ),
									'sanitize'   => 'sanitize_text_field',
									'subtitle'   => __( 'Set accordion expand and collapse icon position or alignment.', 'easy-accordion-free' ),
									'options'    => array(
										'left'  => array(
											'text' => __( 'Left', 'easy-accordion-free' ),
										),
										'right' => array(
											'text' => __( 'Right', 'easy-accordion-free' ),
										),
									),
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-expand-collapse-icon-position.svg" alt="' . __( 'Expand & Collapse Icon Position', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Expand & Collapse Icon Position', 'easy-accordion-free' ) . '</div>',
									'default'    => 'left',
									'dependency' => array(
										'eap_expand_close_icon',
										'==',
										'true',
									),
								),
								array(
									'type'    => 'notice',
									'class'   => 'only_pro_notice',
									'content' => sprintf(
									/* translators: 1: start link tag, 2: close tag. */
										__(
											'Want to make your Accordion FAQs visually attractive with tons of flexible options? %1$sUpgrade to Pro!%2$s',
											'easy-accordion-free'
										),
										'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>',
										'</b></a>'
									),
								),
							),
						),
						array(
							'title'  => __( 'Animation Effect', 'easy-accordion-free' ),
							'icon'   => '<span><i class="eap-icon-animation"></i></span>',
							'fields' => array(
								array(
									'id'         => 'eap_animation',
									'type'       => 'switcher',
									'class'      => 'only-for-pro-switcher',
									'title'      => __( 'Animation', 'easy-accordion-free' ),
									'subtitle'   => __( 'Enable/Disable accordion animation.', 'easy-accordion-free' ),
									'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
									'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
									'text_width' => 100,
									'default'    => false,
									'sanitize'   => 'rest_sanitize_boolean',
									'title_info' => sprintf(
										'<div class="ea-info-label">%s</div> <div class="ea-short-content">%s</div><div class="info-button"><a class="ea-open-live-demo" href="https://easyaccordion.io/accordion-animation/" target="_blank">%s</a></div>',
										__( 'Animation', 'easy-accordion-free' ),
										__( 'The Animation option allows you to control the accordion animation. Customize the visual experience of accordion transitions according to your preference.', 'easy-accordion-free' ),
										__( 'Live Demo', 'easy-accordion-free' )
									),
								),
								array(
									'id'       => 'eap_animation_style',
									'type'     => 'select',
									'class'    => 'only-select-for-pro',
									'title'    => __( 'Animation Style', 'easy-accordion-free' ),
									'subtitle' => __( 'Select an animation style for the description content.', 'easy-accordion-free' ),
									'sanitize' => 'sanitize_text_field',
									'options'  => array(
										'normal'        => __( 'Normal', 'easy-accordion-free' ),
										'fadeIn'        => __( 'fadeIn (Pro)', 'easy-accordion-free' ),
										'fadeInLeft'    => __( 'fadeInLeft (Pro)', 'easy-accordion-free' ),
										'fadeInUp'      => __( 'fadeInUp (Pro)', 'easy-accordion-free' ),
										'fadeInDownBig' => __( 'fadeInDownBig (Pro)', 'easy-accordion-free' ),
										'shake'         => __( 'shake (Pro)', 'easy-accordion-free' ),
										'swing'         => __( 'swing (Pro)', 'easy-accordion-free' ),
										'rollIn'        => __( 'rollIn (Pro)', 'easy-accordion-free' ),
										'bounce'        => __( 'bounce (Pro)', 'easy-accordion-free' ),
										'wobble'        => __( 'wobble (Pro)', 'easy-accordion-free' ),
										'slideInDown'   => __( 'slideInDown (Pro)', 'easy-accordion-free' ),
										'slideInLeft'   => __( 'slideInLeft (Pro)', 'easy-accordion-free' ),
										'slideInUp'     => __( 'slideInUp (Pro)', 'easy-accordion-free' ),
										'zoomIn'        => __( 'zoomIn (Pro)', 'easy-accordion-free' ),
										'zoomInDown'    => __( 'zoomInDown (Pro)', 'easy-accordion-free' ),
										'zoomInUp'      => __( 'zoomInUp (Pro)', 'easy-accordion-free' ),
										'zoomInLeft'    => __( 'zoomInLeft (Pro)', 'easy-accordion-free' ),
										'bounceIn'      => __( 'bounceIn (Pro)', 'easy-accordion-free' ),
										'bounceInDown'  => __( 'bounceInDown (Pro)', 'easy-accordion-free' ),
										'bounceInUp'    => __( 'bounceInUp (Pro)', 'easy-accordion-free' ),
										'jello'         => __( 'jello (Pro)', 'easy-accordion-free' ),
									),
									'default'  => 'normal',
								),
								array(
									'id'       => 'eap_animation_time',
									'type'     => 'spinner',
									'title'    => __( 'Transition Time', 'easy-accordion-free' ),
									'subtitle' => __( 'Set accordion expand and collapse transition time.', 'easy-accordion-free' ),
									'unit'     => 'ms',
									'min'      => 0,
									'max'      => 99999,
									'default'  => 300,
									'sanitize' => 'eapro_sanitize_number_field',
								),
								array(
									'id'       => 'eap_accordion_uniq_id',
									'class'    => 'eap_accordion_wrapper_uniq_attribute',
									'type'     => 'text',
									'sanitize' => 'sanitize_text_field',
									'title'    => '',
									'default'  => 'sp_easy_accordion-' . time() . '',
								),
							),
						),
						array(
							'title'  => __( 'Ajax Pagination', 'easy-accordion-free' ),
							'icon'   => '<span class="eap-tab-icon"><i class="eap-icon-ajax-pagination"></i></span>',
							'class'  => 'eap-not-image-accordion',
							'fields' => array(
								array(
									'type'    => 'notice',
									'style'   => 'normal',
									'content' => sprintf(
										/* translators: %1$s: strong and link tags start, %2$s: link and strong tags end, %3$s: strong tag starts %4$s: strong tag ends %5$s: another link tag start %6$s: link tag ends */
										__( 'To enhance your FAQs Page with %1$sLoad More Pagination%2$s, %5$sUpgrade to Pro!%6$s', 'easy-accordion-free' ),
										'<strong><a href="https://easyaccordion.io/ajax-paginations/" target="_blank">',
										'</a></strong>',
										'<strong class="eap-pro-text">',
										'</strong>',
										'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>',
										'</b></a>'
									),
								),
								array(
									'id'         => 'show_pagination',
									'type'       => 'switcher',
									'class'      => 'only-for-pro-switcher',
									'title'      => __( 'Ajax Pagination', 'easy-accordion-free' ),
									'subtitle'   => __( 'Enable/Disable accordion item pagination.', 'easy-accordion-free' ),
									'text_on'    => __( 'Enabled', 'easy-accordion-free' ),
									'text_off'   => __( 'Disabled', 'easy-accordion-free' ),
									'default'    => true,
									'only_pro'   => true,
									'sanitize'   => 'rest_sanitize_boolean',
									'text_width' => 100,
									'title_info' => '<div class="ea-img-tag"><img src="' . esc_url( SP_EA_URL ) . 'admin/img/ea-ajax-pagination.svg" alt="' . __( 'Ajax Pagination', 'easy-accordion-free' ) . '"></div><div class="ea-info-label img">' . __( 'Ajax Pagination', 'easy-accordion-free' ) . '</div><div class="info-button img"><a class="ea-open-live-demo" href="https://easyaccordion.io/ajax-paginations/" target="_blank">' . __( 'Live Demo', 'easy-accordion-free' ) . '</a></div>',
								),
								array(
									'id'         => 'pagination_type',
									'type'       => 'radio',
									'title'      => __( 'Ajax Pagination Type', 'easy-accordion-free' ),
									'subtitle'   => __( 'Choose an accordion item pagination type.', 'easy-accordion-free' ),
									'class'      => 'only-for-pro',
									'only_pro'   => true,
									'options'    => array(
										'ajax_load_more' => __( 'Ajax Load More', 'easy-accordion-free' ),
										'ajax_infinite_scrl' => __( 'Ajax Infinite Scroll', 'easy-accordion-free' ),
										'ajax_number'    => __( 'Ajax Number', 'easy-accordion-free' ),
									),
									'default'    => 'ajax_load_more',
									'dependency' => array( 'show_pagination', '==', 'true' ),
								),
								array(
									'id'         => 'load_more_label',
									'type'       => 'text',
									'title'      => __( 'Load More Label', 'easy-accordion-free' ),
									'default'    => __( 'Load More', 'easy-accordion-free' ),
									'subtitle'   => __( 'Change load more label text.', 'easy-accordion-free' ),
									'class'      => 'only-for-pro',
									'sanitize'   => 'sanitize_text_field',
									'only_pro'   => true,
									'dependency' => array( 'show_pagination|pagination_type', '==|==', 'true|ajax_load_more' ),
								),
								array(
									'id'         => 'pagination_show_per_page',
									'type'       => 'spinner',
									'title'      => __( 'Accordion Items Per Page', 'easy-accordion-free' ),
									'subtitle'   => __( 'Set number of accordion items to show per page/click.', 'easy-accordion-free' ),
									'class'      => 'only-for-pro',
									'sanitize'   => 'eapro_sanitize_number_field',
									'default'    => 8,
									'only_pro'   => true,
									'dependency' => array( 'show_pagination|pagination_type', '==|any', 'true|ajax_number,ajax_load_more,ajax_infinite_scrl' ),
								),
								array(
									'id'         => 'pagination_color',
									'class'      => 'pagination_color only-for-pro',
									'type'       => 'color_group',
									'title'      => __( 'Color', 'easy-accordion-free' ),
									'subtitle'   => __( 'Set Pagination color.', 'easy-accordion-free' ),
									'sanitize'   => 'eapro_sanitize_color_group_field',
									'options'    => array(
										'text_color'      => __( 'Text Color', 'easy-accordion-free' ),
										'text_active_clr' => __( 'Text Active Color', 'easy-accordion-free' ),
										'border_color'    => __( 'Border Color', 'easy-accordion-free' ),
										'border_active_clr' => __( 'Border Active Color', 'easy-accordion-free' ),
										'background'      => __( 'Background', 'easy-accordion-free' ),
										'active_background' => __( 'Active Background', 'easy-accordion-free' ),
									),
									'default'    => array(
										'text_color'      => '#5e5e5e',
										'text_active_clr' => '#ffffff',
										'border_color'    => '#bbbbbb',
										'border_active_clr' => '#FE7C4D',
										'background'      => '#ffffff',
										'active_background' => '#FE7C4D',
									),
									'dependency' => array( 'show_pagination', '==', 'true' ),
								),
							),
						),
					),
				),
			),
		)
	); // Accordion settings section end.

	//
	// Typography section begin.
	//
	SP_EAP::createSection(
		$eap_accordion_shortcode_settings,
		array(
			'title'           => __( 'Typography', 'easy-accordion-free' ),
			'icon'            => 'fa fa-font',
			'enqueue_webfont' => true,
			'fields'          => array(
				array(
					'type'    => 'notice',
					'content' => sprintf(
						/* translators: 1: start link tag, 2: close tag 3: bold tag start, 4: bold tag end; */
						__(
							'Want to customize everything (colors and typography) easily? %1$sUpgrade to Pro!%2$s P.S. Note: The section %3$scolor%4$s fields work in the lite version.',
							'easy-accordion-free'
						),
						'<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"> <b>',
						'</b></a>',
						'<b>',
						'</b>'
					),
				),
				array(
					'id'         => 'section_title_font_load',
					'type'       => 'switcher',
					'class'      => 'only-for-pro-switcher',
					'title'      => __( 'Load Accordion Section Title Font', 'easy-accordion-free' ),
					'subtitle'   => __( 'On/Off google font for the section title.', 'easy-accordion-free' ),
					'default'    => false,
					'sanitize'   => 'rest_sanitize_boolean',
					'dependency' => array(
						'section_title',
						'==',
						'true',
						true,
					),
				),
				array(
					'id'            => 'eap_section_title_typography',
					'type'          => 'typography',
					'title'         => __( 'Accordion Section Title Font', 'easy-accordion-free' ),
					'subtitle'      => __( 'Set Accordion section title font properties.', 'easy-accordion-free' ),
					'default'       => array(
						'font-family'    => 'Open Sans',
						'font-style'     => '600',
						'font-size'      => '28',
						'line-height'    => '32',
						'letter-spacing' => '0',
						'text-align'     => 'left',
						'text-transform' => 'none',
						'type'           => 'google',
						'unit'           => 'px',
						'color'          => '#444',
						'margin-bottom'  => '30',
					),
					'preview'       => 'always',
					'margin_bottom' => true,
					'dependency'    => array(
						'section_title',
						'==',
						'true',
						true,
					),
					'preview_text'  => 'Accordion Section Title',
				),
				array(
					'id'       => 'eap_title_font_load',
					'type'     => 'switcher',
					'class'    => 'only-for-pro-switcher',
					'title'    => __( 'Load Accordion Item Title Font', 'easy-accordion-free' ),
					'subtitle' => __( 'On/Off google font for the accordion item title.', 'easy-accordion-free' ),
					'default'  => false,
				),
				array(
					'id'           => 'eap_title_typography',
					'type'         => 'typography',
					'title'        => __( 'Item Title Font', 'easy-accordion-free' ),
					'subtitle'     => __( 'Set accordion item title font properties.', 'easy-accordion-free' ),
					'default'      => array(
						'font-family'    => 'Open Sans',
						'font-style'     => '600',
						'font-size'      => '20',
						'line-height'    => '30',
						'letter-spacing' => '0',
						'color'          => '#444',
						'active_color'   => '#444',
						'hover_color'    => '#444',
						'text-align'     => 'left',
						'text-transform' => 'none',
						'type'           => 'google',
					),
					'color'        => false,
					'preview_text' => 'Accordion Item Title',
					'preview'      => 'always',
				),
				array(
					'id'       => 'eap_desc_font_load',
					'type'     => 'switcher',
					'class'    => 'only-for-pro-switcher',
					'title'    => __( 'Load Accordion Item Description Font', 'easy-accordion-free' ),
					'subtitle' => __( 'On/Off google font for the accordion item description.', 'easy-accordion-free' ),
					'default'  => false,
				),
				array(
					'id'           => 'eap_content_typography',
					'type'         => 'typography',
					'title'        => __( 'Description Font', 'easy-accordion-free' ),
					'subtitle'     => __( 'Set accordion item description font properties.', 'easy-accordion-free' ),
					'default'      => array(
						'color'          => '#444',
						'font-family'    => 'Open Sans',
						'font-style'     => '400',
						'font-size'      => '16',
						'line-height'    => '26',
						'letter-spacing' => '0',
						'text-align'     => 'left',
						'text-transform' => 'none',
						'type'           => 'google',
					),
					'color'        => false,
					'preview'      => 'always',
					'preview_text' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Vel voluptatum, earum quibusdam quaerat cum quidem Culpa nam placeat iste laudantium illum, in aperiam deserunt ullam cumque libero. Vero, aut pariatur amet consectetur adipisicing elit. Facilis, tempora, quasi repellat reiciendis praesentium accusantium perspiciatis vero vitae numquam blanditiis nisi accusamus saepe eius.',
				),
			), // End of fields array.
		)
	); // Style settings section end.
