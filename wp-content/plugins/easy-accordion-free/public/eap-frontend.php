<?php
/**
 * The file that defines the shortcode plugin class.
 *
 * A class definition that define easy accordion  shortcode of the plugin.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package   easy-accordion-free
 * @subpackage easy-accordion-free/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

/**
 * The Shortcode class.
 *
 * This is used to define shortcode, shortcode attributes.
 */
class SP_EAP_FRONTEND {

	/**
	 * Holds the class object.
	 *
	 * @since 2.0.0
	 * @var object
	 */
	public static $instance;

	/**
	 * Contain the base class object.
	 *
	 * @since 2.0.0
	 * @var object
	 */
	public $base;

	/**
	 * Holds the accordion data.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $data;


	/**
	 * Undocumented variable
	 *
	 * @var string $post_id The post id of the accordion shortcode.
	 */
	public $post_id;


	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 2.0.0
	 * @static
	 * @return SP_EAP_FRONTEND Shortcode instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_shortcode( 'sp_easyaccordion', array( $this, 'sp_easy_accordion_shortcode' ) );
		add_action( 'save_post', array( $this, 'delete_page_accordion_option_on_save' ) );
	}

	/**
	 * Accordion post query.
	 *
	 * @param array $upload_data get all layout options.
	 *
	 * @return Array
	 */
	public static function accordion_post_query( $upload_data ) {
		$ignore_sticky = apply_filters( 'sp_eap_ignore_sticky_post', 1 );
		$order_by      = isset( $upload_data['post_order_by'] ) ? $upload_data['post_order_by'] : 'ID';
		$order         = isset( $upload_data['post_order'] ) ? $upload_data['post_order'] : 'DESC';
		$args          = array(
			'post_type'           => 'sp_accordion_faqs',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => $ignore_sticky,
			'fields'              => 'ids',
			'posts_per_page'      => -1,
			'order'               => $order,
			'orderby'             => $order_by,
		);

		$all_post_ids     = get_posts( $args );
		$count_total_post = count( $all_post_ids );
		$post_query       = array(
			'post_query'       => $args,
			'count_total_post' => $count_total_post,
		);
		return $post_query;
	}

	/**
	 * Full html show.
	 *
	 * @param array $post_id Shortcode ID.
	 * @param array $upload_data get all layout options.
	 * @param array $shortcode_data get all meta options.
	 * @param array $main_section_title shows section title.
	 */
	public static function sp_eap_html_show( $post_id, $upload_data, $shortcode_data, $main_section_title ) {
		if ( empty( $upload_data ) ) {
			return;
		}
		$accordion_type  = isset( $upload_data['eap_accordion_type'] ) ? $upload_data['eap_accordion_type'] : '';
		$content_sources = isset( $upload_data['accordion_content_source'] ) ? $upload_data['accordion_content_source'] : '';

		// Shortcode Option.
		$accordion_layout      = isset( $shortcode_data['eap_accordion_layout'] ) ? $shortcode_data['eap_accordion_layout'] : 'vertical';
		$eap_accordion_uniq_id = isset( $shortcode_data['eap_accordion_uniq_id'] ) ? $shortcode_data['eap_accordion_uniq_id'] : 'sp_easy_accordion-' . time() . '';

		$accordion_item_class = 'sp-ea-single';
		$eap_schema_markup    = isset( $shortcode_data['eap_schema_markup'] ) ? $shortcode_data['eap_schema_markup'] : false;
		// Accordion settings.
		$eap_preloader             = isset( $shortcode_data['eap_preloader'] ) ? $shortcode_data['eap_preloader'] : false;
		$eap_active_event          = isset( $shortcode_data['eap_accordion_event'] ) ? $shortcode_data['eap_accordion_event'] : '';
		$eap_accordion_mode        = isset( $shortcode_data['eap_accordion_mode'] ) ? $shortcode_data['eap_accordion_mode'] : '';
		$eap_mutliple_collapse     = isset( $shortcode_data['eap_mutliple_collapse'] ) ? $shortcode_data['eap_mutliple_collapse'] : '';
		$eap_accordion_fillspace   = isset( $shortcode_data['eap_accordion_fillspace'] ) ? $shortcode_data['eap_accordion_fillspace'] : '';
		$eap_nofollow_link         = isset( $shortcode_data['eap_nofollow_link'] ) ? $shortcode_data['eap_nofollow_link'] : false;
		$nofollow_link_text        = $eap_nofollow_link ? 'rel=nofollow' : '';
		$eap_scroll_to_active_item = isset( $shortcode_data['eap_scroll_to_active_item'] ) ? $shortcode_data['eap_scroll_to_active_item'] : false;
		$eap_offset_to_scroll      = apply_filters( 'eap_offset_to_scroll', 0 );

		$eap_accordion_fillspace_height = isset( $shortcode_data['eap_accordion_fillspace_height']['all'] ) ? $shortcode_data['eap_accordion_fillspace_height']['all'] : '200';
		$eap_title_tag                  = isset( $shortcode_data['ea_title_heading_tag'] ) ? 'h' . $shortcode_data['ea_title_heading_tag'] : 'h3';
		$acc_section_title              = isset( $shortcode_data['section_title'] ) ? $shortcode_data['section_title'] : '';

		// Expand / Collapse Icon.
		$eap_icon             = isset( $shortcode_data['eap_expand_close_icon'] ) ? $shortcode_data['eap_expand_close_icon'] : '';
		$eap_ex_icon_position = isset( $shortcode_data['eap_icon_position'] ) ? $shortcode_data['eap_icon_position'] : '';
		$eap_icon_size        = isset( $shortcode_data['eap_icon_size']['all'] ) ? $shortcode_data['eap_icon_size']['all'] : '16';
		$eap_icon_color       = isset( $shortcode_data['eap_icon_color_set'] ) ? $shortcode_data['eap_icon_color_set'] : '';
		$eap_collapse_icon    = 'plus';
		$eap_expand_icon      = 'minus';
		// Description.
		$eap_autop = isset( $shortcode_data['eap_autop'] ) ? $shortcode_data['eap_autop'] : true;

		wp_enqueue_script( 'sp-ea-accordion-js' );
		wp_enqueue_script( 'sp-ea-accordion-config' );
		ob_start();
		switch ( $accordion_type ) {
			case 'content-accordion':
				require self::eap_locate_template( 'default-accordion.php' );
				break;
			case 'post-accordion':
				require self::eap_locate_template( 'post-accordion.php' );
				break;
		}
		$html = ob_get_clean();
		$html = self::minify_output( $html );
		echo apply_filters( 'sp_easy_accordion', $html, $post_id ); // phpcs:ignore
	}

	/**
	 * Minify output
	 *
	 * @param  string $html output.
	 * @return string
	 */
	public static function minify_output( $html ) {
		$html = preg_replace( '/<!--(?!s*(?:[if [^]]+]|!|>))(?:(?!-->).)*-->/s', '', $html );
		// Fix the tag break issue: normalize whitespace in HTML tags.
		$html = preg_replace_callback(
			'/<[^>]+>/',
			function ( $matches ) {
				return preg_replace( '/\s+/', ' ', trim( $matches[0] ) );
			},
			$html
		);
		$html = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $html );
		while ( stristr( $html, '  ' ) ) {
			$html = str_replace( '  ', ' ', $html );
		}
		return $html;
	}

	/**
	 * A shortcode for rendering the accordion.
	 *
	 * @param [string] $attributes Shortcode attributes.
	 * @param [string] $content Shortcode content.
	 * @return array
	 */
	public function sp_easy_accordion_shortcode( $attributes, $content = null ) {
		if ( empty( $attributes['id'] ) || ( get_post_status( $attributes['id'] ) === 'trash' ) || 'sp_easy_accordion' !== get_post_type( $attributes['id'] ) ) {
			return;
		}

		$post_id = esc_attr( intval( $attributes['id'] ) );

		// Content Accordion.
		$upload_data        = get_post_meta( $post_id, 'sp_eap_upload_options', true );
		$shortcode_data     = get_post_meta( $post_id, 'sp_eap_shortcode_options', true );
		$settings           = get_option( 'sp_eap_settings' );
		$main_section_title = get_the_title( $post_id );
		ob_start();
		// Stylesheet loading problem solving here. Shortcode id to push page id option for getting how many shortcode in the page.
		$get_page_data      = SP_EA_Front_Scripts::get_page_data();
		$found_generator_id = $get_page_data['generator_id'];
		if ( ! is_array( $found_generator_id ) || ! $found_generator_id || ! in_array( $post_id, $found_generator_id ) ) {
			wp_enqueue_style( 'sp-ea-fontello-icons' );
			wp_enqueue_style( 'sp-ea-style' );
			$ea_dynamic_css = SP_EA_Front_Scripts::load_dynamic_style( $post_id, $shortcode_data );
			echo '<style>' . wp_strip_all_tags( $ea_dynamic_css['dynamic_css'] ) . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		// Update options if the existing shortcode id option not found.
		SP_EA_Front_Scripts::easy_accordion_update_options( $post_id, $get_page_data );
		self::sp_eap_html_show( $post_id, $upload_data, $shortcode_data, $main_section_title );
		return ob_get_clean();
	}

	/**
	 * Accordion_mode
	 *
	 * @param  mixed $eap_accordion_mode mode type.
	 * @param  mixed $ea_key key.
	 * @param  mixed $eap_expand_icon expand icon.
	 * @param  mixed $eap_collapse_icon collapse icon.
	 * @return array
	 */
	public static function accordion_mode( $eap_accordion_mode = null, $ea_key = null, $eap_expand_icon = null, $eap_collapse_icon = null ) {
		$a_open_first      = '';
		$expand_icon_first = '';
		$expand_class      = '';
		$aria_expanded     = 'false';

		switch ( $eap_accordion_mode ) {
			case 'ea-first-open':
				$is_first_key      = ( 1 === $ea_key );
				$a_open_first      = $is_first_key ? 'collapsed show' : '';
				$expand_icon_first = $is_first_key ? $eap_expand_icon : $eap_collapse_icon;
				$expand_class      = $is_first_key ? 'ea-expand' : '';
				$aria_expanded     = $is_first_key ? 'true' : 'false';
				break;
			case 'ea-multi-open':
				$a_open_first      = 'collapsed show';
				$expand_icon_first = $eap_expand_icon;
				$expand_class      = 'ea-expand';
				$aria_expanded     = 'true';
				break;
			case 'ea-all-close':
				$a_open_first      = 'spcollapse';
				$expand_icon_first = $eap_collapse_icon;
				$expand_class      = '';
				$aria_expanded     = 'false';
				break;
		}

		return array(
			'open_first'        => $a_open_first,
			'expand_icon_first' => $expand_icon_first,
			'expand_class'      => $expand_class,
			'aria_expanded'     => $aria_expanded,
		);
	}

	/**
	 * Delete page shortcode ids array option on save
	 *
	 * @param  int $post_ID current post id.
	 * @return void
	 */
	public function delete_page_accordion_option_on_save( $post_ID ) {
		if ( is_multisite() ) {
			$option_key = 'easy_accordion_page_id' . get_current_blog_id() . $post_ID;
			if ( get_site_option( $option_key ) ) {
				delete_site_option( $option_key );
			}
		} elseif ( get_option( 'easy_accordion_page_id' . $post_ID ) ) {
				delete_option( 'easy_accordion_page_id' . $post_ID );
		}
	}

	/**
	 * Custom Template locator.
	 *
	 * @param  mixed $template_name template name.
	 * @param  mixed $template_path template path.
	 * @param  mixed $default_path default path.
	 * @return string
	 */
	public static function eap_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = 'easy-accordion-free/templates';
		}
		if ( ! $default_path ) {
			$default_path = SP_EA_PATH . '/public/templates/';
		}
		$template = locate_template( trailingslashit( $template_path ) . $template_name );
		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
		// Return what we found.
		return $template;
	}
}
new SP_EAP_FRONTEND();
