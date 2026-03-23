<?php
/**
 * Compatibility Registry
 *
 * Central registry for plugin and theme compatibility rules.
 *
 * @package wp-site-designer-mu-plugins
 */

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Compat;

/**
 * Manages blocklist, allowlist, and review bucket for plugins/themes
 */
class Compatibility_Registry {

	/**
	 * Plugin is blocked (full-site builder that conflicts with Site Designer)
	 */
	public const STATUS_BLOCKED = 'blocked';

	/**
	 * Plugin is compatible (block libraries that enhance Site Designer)
	 */
	public const STATUS_COMPATIBLE = 'compatible';

	/**
	 * Plugin is under review (unknown builder, warn but don't block)
	 */
	public const STATUS_REVIEW = 'review';

	/**
	 * Recommended block theme
	 */
	public const RECOMMENDED_THEME = 'twentytwentyfive';

	/**
	 * Hard blocklist - Full-site builders that conflict with Site Designer
	 *
	 * Keyed by plugin slug (directory name) for resilience against main file changes.
	 *
	 * @var array<string, array{name: string, reason: string}>
	 */
	public const BLOCKED_PLUGINS = array(
		'elementor'                   => array(
			'name'   => 'Elementor',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'elementor-pro'               => array(
			'name'   => 'Elementor Pro',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'divi-builder'                => array(
			'name'   => 'Divi Builder',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'js_composer'                 => array(
			'name'   => 'WPBakery Page Builder',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'bb-plugin'                   => array(
			'name'   => 'Beaver Builder',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'beaver-builder-lite-version' => array(
			'name'   => 'Beaver Builder Lite',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'brizy'                       => array(
			'name'   => 'Brizy',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'brizy-pro'                   => array(
			'name'   => 'Brizy Pro',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'oxygen'                      => array(
			'name'   => 'Oxygen Builder',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'bricks'                      => array(
			'name'   => 'Bricks Builder',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'seedprod-coming-soon-pro-5'  => array(
			'name'   => 'SeedProd Pro',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
		'coming-soon'                 => array(
			'name'   => 'SeedProd',
			'reason' => 'Full-site page builder that conflicts with Site Designer',
		),
	);

	/**
	 * Compatible plugins - Block libraries that enhance Site Designer
	 *
	 * Uses plugin slugs (directory names) for consistent matching.
	 *
	 * @var array<string>
	 */
	public const COMPATIBLE_PLUGINS = array(
		'kadence-blocks',
		'stackable-ultimate-gutenberg-blocks',
		'starter-templates',
	);

	/**
	 * Patterns to detect unknown builders (for review bucket)
	 *
	 * @var array<string>
	 */
	private const REVIEW_PLUGIN_PATTERNS = array(
		'/page-builder/i',
		'/site-builder/i',
		'/visual-composer/i',
		'/visual-editor/i',
		'/website-builder/i',
	);

	/**
	 * Extract plugin slug (directory name) from plugin file path
	 *
	 * @param string $plugin_file Plugin file path (e.g., 'elementor/elementor.php').
	 *
	 * @return string Plugin slug (e.g., 'elementor')
	 */
	public static function extract_slug( string $plugin_file ): string {
		$parts = explode( '/', $plugin_file );
		return $parts[0] ?? $plugin_file;
	}

	/**
	 * Get the compatibility status of a plugin
	 *
	 * @param string $plugin_file Plugin file path (e.g., 'elementor/elementor.php').
	 *
	 * @return string One of STATUS_BLOCKED, STATUS_COMPATIBLE, or STATUS_REVIEW
	 */
	public static function get_plugin_status( string $plugin_file ): string {
		$slug = self::extract_slug( $plugin_file );

		// Check blocklist first.
		if ( isset( self::BLOCKED_PLUGINS[ $slug ] ) ) {
			return self::STATUS_BLOCKED;
		}

		// Check compatible list.
		if ( in_array( $slug, self::COMPATIBLE_PLUGINS, true ) ) {
			return self::STATUS_COMPATIBLE;
		}

		// Check if it matches review patterns.
		if ( self::matches_review_patterns( $plugin_file ) ) {
			return self::STATUS_REVIEW;
		}

		// Default to compatible for unknown plugins.
		return self::STATUS_COMPATIBLE;
	}

	/**
	 * Get plugin display info for any plugin (blocklist or unknown)
	 *
	 * @param string $plugin_file Plugin file path.
	 *
	 * @return array{name: string, reason: string}
	 */
	public static function get_plugin_display_info( string $plugin_file ): array {
		$slug = self::extract_slug( $plugin_file );

		// First check blocklist for known info.
		if ( isset( self::BLOCKED_PLUGINS[ $slug ] ) ) {
			return self::BLOCKED_PLUGINS[ $slug ];
		}

		// Fallback to get_plugin_data() for unknown plugins.
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
		$name        = $plugin_file;

		if ( file_exists( $plugin_path ) ) {
			$plugin_data = get_plugin_data( $plugin_path, false, false );
			$name        = $plugin_data['Name'] ?? $plugin_file;
		}

		return array(
			'name'   => $name,
			'reason' => 'This plugin may conflict with Airo for WordPress',
		);
	}

	/**
	 * Check if a plugin matches review bucket patterns
	 *
	 * @param string $plugin_file Plugin file path.
	 *
	 * @return bool
	 */
	private static function matches_review_patterns( string $plugin_file ): bool {
		// Get plugin data for name and description matching.
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

		if ( ! file_exists( $plugin_path ) ) {
			return false;
		}

		$plugin_data = get_plugin_data( $plugin_path, false, false );
		$search_text = strtolower(
			$plugin_file . ' ' .
			( $plugin_data['Name'] ?? '' ) . ' ' .
			( $plugin_data['Description'] ?? '' )
		);

		foreach ( self::REVIEW_PLUGIN_PATTERNS as $pattern ) {
			if ( preg_match( $pattern, $search_text ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the recommended theme slug
	 *
	 * @return string
	 */
	public static function get_recommended_theme(): string {
		return self::RECOMMENDED_THEME;
	}

	/**
	 * Get the recommended theme display name
	 *
	 * @return string
	 */
	public static function get_recommended_theme_name(): string {
		$theme = wp_get_theme( self::RECOMMENDED_THEME );
		return $theme->exists() ? $theme->get( 'Name' ) : 'Twenty Twenty-Five';
	}

	/**
	 * Get all blocked plugins that are currently active
	 *
	 * @return array<string, array{name: string, reason: string}>
	 */
	public static function get_active_blocked_plugins(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins    = get_plugins();
		$active_blocked = array();

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( ! is_plugin_active( $plugin_file ) ) {
				continue;
			}

			$slug = self::extract_slug( $plugin_file );

			if ( isset( self::BLOCKED_PLUGINS[ $slug ] ) ) {
				$active_blocked[ $plugin_file ] = self::BLOCKED_PLUGINS[ $slug ];
			}
		}

		return $active_blocked;
	}

	/**
	 * Get all review-bucket plugins that are currently active
	 *
	 * @return array<string, array{name: string}>
	 */
	public static function get_active_review_plugins(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins   = get_plugins();
		$active_review = array();

		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( ! is_plugin_active( $plugin_file ) ) {
				continue;
			}

			$slug = self::extract_slug( $plugin_file );

			// Skip if blocked or compatible.
			if ( isset( self::BLOCKED_PLUGINS[ $slug ] ) ) {
				continue;
			}

			if ( in_array( $slug, self::COMPATIBLE_PLUGINS, true ) ) {
				continue;
			}

			// Check if matches review patterns.
			if ( self::matches_review_patterns( $plugin_file ) ) {
				$active_review[ $plugin_file ] = array(
					'name' => $plugin_data['Name'] ?? $plugin_file,
				);
			}
		}

		return $active_review;
	}
}
