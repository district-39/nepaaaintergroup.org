<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Woo;

use WP_Post;

/**
 * WooCommerce Pages Setup Handler
 *
 * Handles creation and management of required WooCommerce pages
 *
 * @package wp-site-designer-mu-plugins
 */
class Setup_Woo_Pages {

	/**
	 * WooCommerce page option names to check
	 *
	 * @var array<string>
	 */
	private const PAGE_OPTIONS = array(
		'woocommerce_shop_page_id',
		'woocommerce_cart_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_terms_page_id',
		'woocommerce_privacy_policy_page_id',
	);

	/**
	 * Page type constants for all WooCommerce pages
	 */
	private const PAGE_TYPE_SHOP           = 'shop';
	private const PAGE_TYPE_CART           = 'cart';
	private const PAGE_TYPE_CHECKOUT       = 'checkout';
	private const PAGE_TYPE_MY_ACCOUNT     = 'my-account';
	private const PAGE_TYPE_TERMS          = 'terms';
	private const PAGE_TYPE_PRIVACY_POLICY = 'privacy_policy';

	/**
	 * Default locale for page content
	 */
	private const DEFAULT_LOCALE = 'en-US';

	/**
	 * WooCommerce page configuration matching WC_Install::create_pages()
	 *
	 * @var array<string, array{title: string, slug: string, content: string, option: string, page_type: string}>
	 */
	private const PAGE_CONFIG = array(
		'shop'           => array(
			'title'     => 'Shop',
			'slug'      => 'shop',
			'content'   => '',
			'option'    => 'woocommerce_shop_page_id',
			'page_type' => self::PAGE_TYPE_SHOP,
		),
		'cart'           => array(
			'title'     => 'Cart',
			'slug'      => 'cart',
			'content'   => '',
			'option'    => 'woocommerce_cart_page_id',
			'page_type' => self::PAGE_TYPE_CART,
		),
		'checkout'       => array(
			'title'     => 'Checkout',
			'slug'      => 'checkout',
			'content'   => '',
			'option'    => 'woocommerce_checkout_page_id',
			'page_type' => self::PAGE_TYPE_CHECKOUT,
		),
		'my-account'     => array(
			'title'     => 'My Account',
			'slug'      => 'my-account',
			'content'   => '',
			'option'    => 'woocommerce_myaccount_page_id',
			'page_type' => self::PAGE_TYPE_MY_ACCOUNT,
		),
		'terms'          => array(
			'title'     => 'Terms and Conditions',
			'slug'      => 'terms',
			'content'   => '',
			'option'    => 'woocommerce_terms_page_id',
			'page_type' => self::PAGE_TYPE_TERMS,
		),
		'privacy_policy' => array(
			'title'     => 'Privacy Policy',
			'slug'      => 'privacy',
			'content'   => '',
			'option'    => 'woocommerce_privacy_policy_page_id',
			'page_type' => self::PAGE_TYPE_PRIVACY_POLICY,
		),
	);

	/**
	 * Check if required WooCommerce pages exist
	 *
	 * @return bool True if all required pages exist, false otherwise.
	 */
	public function check_required_pages_exist(): bool {
		foreach ( self::PAGE_OPTIONS as $option_name ) {
			$page_id = get_option( $option_name, 0 );
			if ( ! $page_id || ! get_post( $page_id ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get list of missing WooCommerce pages
	 *
	 * @return array<string> Array of option names for missing pages.
	 */
	private function get_missing_pages(): array {
		$missing_pages = array();

		foreach ( self::PAGE_OPTIONS as $option_name ) {
			$page_id = get_option( $option_name, 0 );
			if ( ! $page_id || ! get_post( $page_id ) ) {
				$missing_pages[] = $option_name;
			}
		}

		return $missing_pages;
	}

	/**
	 * Create required WooCommerce pages
	 *
	 * Replicates WooCommerce's page creation behavior but checks by option values
	 * instead of slugs. Creates pages with blocks or shortcodes based on WooCommerce version.
	 *
	 * @return void
	 */
	public function create_required_pages(): void {
		$missing_pages = $this->get_missing_pages();

		// If no pages are missing, nothing to do.
		if ( empty( $missing_pages ) ) {
			return;
		}

		// Create missing pages using WooCommerce's wc_create_page function if available.
		foreach ( $missing_pages as $option_name ) {
			$page_key = $this->get_page_key_from_option( $option_name );
			if ( ! isset( self::PAGE_CONFIG[ $page_key ] ) ) {
				continue;
			}

			$page_config = self::PAGE_CONFIG[ $page_key ];
			$this->create_woocommerce_page( $page_config );
		}
	}

	/**
	 * Create a WooCommerce page replicating WC_Install::create_pages() behavior
	 *
	 * Uses wc_create_page() if available, otherwise falls back to manual creation.
	 * Checks by option value and slug to prevent duplicates.
	 *
	 * @param array{title: string, slug: string, content: string, option: string} $page_config Page configuration.
	 *
	 * @return void
	 */
	private function create_woocommerce_page( array $page_config ): void {
		// Check if page already exists by option value.
		$existing_page_id = get_option( $page_config['option'], 0 );
		if ( $existing_page_id && get_post( $existing_page_id ) ) {
			return;
		}

		// Check if a page with this slug already exists.
		$existing_page_by_slug = $this->get_page_by_slug( $page_config['slug'] );
		if ( $existing_page_by_slug ) {
			// Update the existing page's content and set the option.
			$content = $this->get_page_content_for_config( $page_config );
			wp_update_post(
				array(
					'ID'           => $existing_page_by_slug->ID,
					'post_content' => $content,
					'post_title'   => $page_config['title'],
				)
			);

			// Set the option to use this existing page.
			update_option( $page_config['option'], $existing_page_by_slug->ID );

			// Handle privacy policy page - also set WordPress core option.
			if ( 'woocommerce_privacy_policy_page_id' === $page_config['option'] ) {
				update_option( 'wp_page_for_privacy_policy', $existing_page_by_slug->ID );
			}

			return;
		}

		// Use WooCommerce's wc_create_page() function if available (replicates WC_Install behavior).
		if ( function_exists( 'wc_create_page' ) ) {
			// Get the actual content (handle special markers for terms and privacy).
			$content = $this->get_page_content_for_config( $page_config );

			$page_id = wc_create_page(
				$page_config['slug'],
				$page_config['option'],
				$page_config['title'],
				$content
			);

			if ( $page_id ) {
				// Set the post author to the administrator with the lowest ID.
				$admin_user_id = $this->get_lowest_admin_user_id();
				if ( $admin_user_id > 0 ) {
					wp_update_post(
						array(
							'ID'          => $page_id,
							'post_author' => $admin_user_id,
						)
					);
				}

				// Handle privacy policy page - also set WordPress core option.
				if ( 'woocommerce_privacy_policy_page_id' === $page_config['option'] ) {
					update_option( 'wp_page_for_privacy_policy', $page_id );
				}
			}
			return;
		}

		// Fallback: Create page manually if wc_create_page() is not available.
		$this->create_page_manually( $page_config );
	}

	/**
	 * Create a page manually (fallback if wc_create_page() is not available)
	 *
	 * @param array{title: string, slug: string, content: string, option: string} $page_config Page configuration.
	 *
	 * @return void
	 */
	private function create_page_manually( array $page_config ): void {
		// Check if a page with this slug already exists.
		$existing_page_by_slug = $this->get_page_by_slug( $page_config['slug'] );
		if ( $existing_page_by_slug ) {
			// Update the existing page's content and set the option.
			$content = $this->get_page_content_for_config( $page_config );
			wp_update_post(
				array(
					'ID'           => $existing_page_by_slug->ID,
					'post_content' => $content,
					'post_title'   => $page_config['title'],
				)
			);

			// Set the option to use this existing page.
			update_option( $page_config['option'], $existing_page_by_slug->ID );

			// Handle privacy policy page - also set WordPress core option.
			if ( 'woocommerce_privacy_policy_page_id' === $page_config['option'] ) {
				update_option( 'wp_page_for_privacy_policy', $existing_page_by_slug->ID );
			}

			return;
		}

		// Get content with blocks or shortcodes based on WooCommerce version.
		// Handle special content markers for terms and privacy pages.
		$content = $this->get_page_content_for_config( $page_config );

		$page_data = array(
			'post_title'   => $page_config['title'],
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => $page_config['slug'],
			'post_author'  => $this->get_lowest_admin_user_id(),
		);

		$page_id = wp_insert_post( $page_data, true );

		if ( ! is_wp_error( $page_id ) && $page_id > 0 ) {
			// Store the page ID in WooCommerce options.
			update_option( $page_config['option'], $page_id );

			// Handle privacy policy page - also set WordPress core option.
			if ( 'woocommerce_privacy_policy_page_id' === $page_config['option'] ) {
				update_option( 'wp_page_for_privacy_policy', $page_id );
			}
		}
	}

	/**
	 * Get page content for a page configuration
	 *
	 * @param array{title: string, slug: string, content: string, option: string, page_type: string} $page_config Page configuration.
	 *
	 * @return string Page content.
	 */
	private function get_page_content_for_config( array $page_config ): string {
		// Check if this page type uses default content.
		if ( self::PAGE_TYPE_TERMS === $page_config['page_type'] ) {
			return $this->get_terms_content();
		}

		if ( self::PAGE_TYPE_PRIVACY_POLICY === $page_config['page_type'] ) {
			return $this->get_privacy_content();
		}

		// For other pages, use the standard content method.
		return $this->get_page_content( $page_config['page_type'] );
	}

	/**
	 * Get page content (blocks or shortcodes) based on WooCommerce version
	 *
	 * @param string $page_type The page type constant.
	 *
	 * @return string Page content with blocks or shortcodes.
	 */
	private function get_page_content( string $page_type ): string {
		// Check if WooCommerce blocks are available (newer versions use blocks).
		$use_blocks = $this->should_use_blocks();

		if ( $use_blocks ) {
			return $this->get_block_content( $page_type );
		}

		// Fallback to shortcodes for older WooCommerce versions.
		return $this->get_shortcode_content( $page_type );
	}

	/**
	 * Check if we should use blocks instead of shortcodes
	 *
	 * @return bool True if blocks should be used.
	 */
	private function should_use_blocks(): bool {
		// WooCommerce 8.0+ uses blocks by default.
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '8.0.0', '>=' ) ) {
			return true;
		}

		// Check if block editor is available and WooCommerce blocks are registered.
		if ( function_exists( 'register_block_type' ) && class_exists( 'Automattic\WooCommerce\Blocks\Package' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get block content for a page
	 *
	 * @param string $page_type The page type constant.
	 *
	 * @return string Block content.
	 */
	private function get_block_content( string $page_type ): string {
		$block_mapping = array(
			self::PAGE_TYPE_CART       => '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',
			self::PAGE_TYPE_CHECKOUT   => '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',
			self::PAGE_TYPE_MY_ACCOUNT => '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->',
		);

		return $block_mapping[ $page_type ] ?? '';
	}

	/**
	 * Get shortcode content for a page
	 *
	 * @param string $page_type The page type constant.
	 *
	 * @return string Shortcode content.
	 */
	private function get_shortcode_content( string $page_type ): string {
		$shortcode_mapping = array(
			self::PAGE_TYPE_CART       => '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',
			self::PAGE_TYPE_CHECKOUT   => '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',
			self::PAGE_TYPE_MY_ACCOUNT => '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->',
		);

		return $shortcode_mapping[ $page_type ] ?? '';
	}

	/**
	 * Get page key from WooCommerce option name
	 *
	 * @param string $option_name The WooCommerce option name.
	 *
	 * @return string The page key.
	 */
	private function get_page_key_from_option( string $option_name ): string {
		foreach ( self::PAGE_CONFIG as $key => $config ) {
			if ( $config['option'] === $option_name ) {
				return $key;
			}
		}

		return '';
	}

	/**
	 * Get the locale to use for page content
	 *
	 * @return string Locale code (e.g., 'en-US').
	 */
	private function get_locale(): string {
		// Get WordPress locale, fallback to default.
		$locale = get_locale();
		if ( empty( $locale ) ) {
			return self::DEFAULT_LOCALE;
		}

		// Normalize locale format (e.g., 'en_US' -> 'en-US').
		$locale = str_replace( '_', '-', $locale );

		// If locale file doesn't exist, fallback to default.
		// Check using terms page type as a representative check.
		if ( ! $this->content_file_exists( self::PAGE_TYPE_TERMS, $locale ) ) {
			return self::DEFAULT_LOCALE;
		}

		return $locale;
	}

	/**
	 * Get content file path for a page type and locale
	 *
	 * @param string $page_type The page type constant.
	 * @param string $locale    The locale code.
	 *
	 * @return string Content file path.
	 */
	private function get_content_file_path( string $page_type, string $locale ): string {
		$file_mapping = array(
			self::PAGE_TYPE_TERMS          => 'terms.php',
			self::PAGE_TYPE_PRIVACY_POLICY => 'privacy.php',
		);

		$filename = $file_mapping[ $page_type ] ?? '';
		if ( empty( $filename ) ) {
			return '';
		}

		$base_dir = __DIR__ . '/content/' . $locale . '/';

		return $base_dir . $filename;
	}

	/**
	 * Check if content file exists for a page type and locale
	 *
	 * @param string $page_type The page type constant.
	 * @param string $locale    The locale code.
	 *
	 * @return bool True if file exists.
	 */
	private function content_file_exists( string $page_type, string $locale ): bool {
		$file_path = $this->get_content_file_path( $page_type, $locale );

		return ! empty( $file_path ) && file_exists( $file_path );
	}

	/**
	 * Load content from file for a page type
	 *
	 * @param string $page_type The page type constant.
	 *
	 * @return string Page content, empty string if file not found.
	 */
	private function load_content_from_file( string $page_type ): string {
		$locale    = $this->get_locale();
		$file_path = $this->get_content_file_path( $page_type, $locale );

		if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
			return '';
		}

		// Load content from file (file should return a string).
		$content = include $file_path;

		return is_string( $content ) ? $content : '';
	}

	/**
	 * Get default content for Terms and Conditions page
	 *
	 * @return string Terms and Conditions page content.
	 */
	private function get_terms_content(): string {
		return $this->load_content_from_file( self::PAGE_TYPE_TERMS );
	}

	/**
	 * Get default content for Privacy Policy page
	 *
	 * @return string Privacy Policy page content.
	 */
	private function get_privacy_content(): string {
		return $this->load_content_from_file( self::PAGE_TYPE_PRIVACY_POLICY );
	}

	/**
	 * Get a page by its slug (post_name)
	 *
	 * @param string $slug The page slug.
	 *
	 * @return WP_Post|null The page post object if found, null otherwise.
	 */
	private function get_page_by_slug( string $slug ): ?WP_Post {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		return ( $page instanceof WP_Post ) ? $page : null;
	}

	/**
	 * Get the administrator user ID with the lowest ID
	 *
	 * @return int Administrator user ID, or 0 if no administrator found.
	 */
	private function get_lowest_admin_user_id(): int {
		$administrators = get_users(
			array(
				'role'    => 'administrator',
				'fields'  => 'ID',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'number'  => 1,
			)
		);

		if ( empty( $administrators ) ) {
			return 0;
		}

		return (int) $administrators[0];
	}
}
