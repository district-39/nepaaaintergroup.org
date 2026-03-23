<?php
/**
 * Minimal Inline Assets Printer (NO wp_head/wp_footer hooks)
 *
 * - Prints <style>/<script> immediately where you call it.
 * - Optional de-dupe by $key (so the same payload is printed only once per request).
 *
 * IMPORTANT:
 * This is NOT “Elementor-safe” by design. It outputs inline tags at the call site.
 *
 * @file: includes/fontend/class-fe-inline-css-js.php
 * @package Booking Calendar
 * @since   11.0.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPBC_FE_Assets {

	const CSS_TAG_ID_BASE = 'wpbc-inline-css';
	const JS_TAG_ID_BASE  = 'wpbc-inline-js';

	/** @var array */
	protected static $printed_css = array();

	/** @var array */
	protected static $printed_js  = array();

	// ------------------------------------------------------------------------------------------------
	// == CSS ==
	// ------------------------------------------------------------------------------------------------

	/**
	 * Print inline CSS immediately (deduped by key).
	 *
	 * @param string $css
	 * @param string $key  Unique key to prevent duplicate output. If empty, derived from content hash.
	 *
	 * @return void
	 */
	public static function add_inline_css( $css, $key = '' ) {

		$css = is_scalar( $css ) ? trim( (string) $css ) : '';
		if ( '' === $css ) {
			return;
		}

		$key = self::normalize_key( $key, $css, 'css' );
		if ( isset( self::$printed_css[ $key ] ) ) {
			return;
		}
		self::$printed_css[ $key ] = true;

		$id = self::safe_dom_id( self::CSS_TAG_ID_BASE . '-' . $key );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return "\n" . '<style id="' . esc_attr( $id ) . '">' . "\n" . $css . "\n" . '</style>' . "\n";

		// wp_add_inline_style( $id, $css );  ???
	}

	/**
	 * Add inline CSS via WP style handle (Elementor-safe).
	 * Avoids printing <style> tags inside content.
	 *
	 * @param string $handle Style handle (e.g. 'wpbc-ui-both').
	 * @param string $css    CSS text.
	 * @param string $key    Dedupe key (optional).
	 *
	 * @return bool True if added / already added, false if not possible.
	 */
	public static function add_inline_css_to_wp_style( $handle, $css, $key = '' ) {
		// FixIn: 10.14.14.1.
		$handle = is_scalar( $handle ) ? trim( (string) $handle ) : '';
		$css    = is_scalar( $css ) ? trim( (string) $css ) : '';

		if ( '' === $handle || '' === $css ) {
			return false;
		}

		if ( ! function_exists( 'wp_add_inline_style' ) ) {
			return false;
		}

		// Handle must exist in WP styles system.
		if ( ! wp_style_is( $handle, 'enqueued' ) && ! wp_style_is( $handle, 'done' ) && ! wp_style_is( $handle, 'registered' ) ) {
			return false;
		}

		$key = self::normalize_key( $key, $css, 'css' );
		if ( isset( self::$printed_css[ $key ] ) ) {
			return true;
		}
		self::$printed_css[ $key ] = true;

		wp_add_inline_style( $handle, $css );

		return true;
	}

	// ------------------------------------------------------------------------------------------------
	// == JS ==
	// ------------------------------------------------------------------------------------------------

	/**
	 * Print inline JS immediately (deduped by key).
	 *
	 * @param string $js
	 * @param string $key  Unique key to prevent duplicate output. If empty, derived from content hash.
	 *
	 * @return void
	 */
	public static function add_inline_js( $js, $key = '' ) {

		$js = is_scalar( $js ) ? trim( (string) $js ) : '';
		if ( '' === $js ) {
			return;
		}

		$key = self::normalize_key( $key, $js, 'js' );
		if ( isset( self::$printed_js[ $key ] ) ) {
			return;
		}
		self::$printed_js[ $key ] = true;

		$id = self::safe_dom_id( self::JS_TAG_ID_BASE . '-' . $key );


		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return "\n" . '<script id="' . esc_attr( $id ) . '" type="text/javascript">' . "\n" . $js . "\n" . '</script>' . "\n";
	}

	/**
	 * Deprecated (use this add_jq_ready_js_to_wp_script  instead): Print JS wrapped into wpbc_jq_ready_start()/wpbc_jq_ready_end(), immediately.
	 *
	 * @param string $js_body
	 * @param string $key
	 *
	 * @return void
	 */
	public static function add_jq_ready_js( $js_body, $key = '' ) {

		$js_body = is_scalar( $js_body ) ? trim( (string) $js_body ) : '';
		if ( '' === $js_body ) {
			return;
		}

		if ( ';' !== substr( $js_body, -1 ) ) {
			$js_body .= ';';
		}

		$js = wpbc_jq_ready_start() . "\n" . $js_body . "\n" . wpbc_jq_ready_end();

		return self::add_inline_js( $js, $key );
	}

	// FixIn: 10.14.13.2.
	/**
	 * Add JS (wrapped in jQuery ready) as WP inline script after a registered/enqueued handle.
	 * This avoids <script> tags in content (Elementor-safe).
	 *
	 * @param string $handle  Script handle (e.g. 'wpbc-main-client' or 'wpbc_all').
	 * @param string $js_body JS without <script> and without ready wrapper.
	 * @param string $key     Dedupe key (optional).
	 *
	 * @return bool True if added, false if not possible.
	 */
	public static function add_jq_ready_js_to_wp_script( $handle, $js_body, $key = '' ) {

		$handle  = is_scalar( $handle ) ? trim( (string) $handle ) : '';
		$js_body = is_scalar( $js_body ) ? trim( (string) $js_body ) : '';

		if ( '' === $handle || '' === $js_body ) {
			return false;
		}

		// Only if WP script API is available and the handle is in play.
		if ( ! function_exists( 'wp_add_inline_script' ) ) {
			return false;
		}
		if ( ! wp_script_is( $handle, 'enqueued' ) && ! wp_script_is( $handle, 'done' ) && ! wp_script_is( $handle, 'registered' ) ) {
			return false;
		}

		if ( ';' !== substr( $js_body, - 1 ) ) {
			$js_body .= ';';
		}

		$js = wpbc_jq_ready_start() . "\n" . $js_body . "\n" . wpbc_jq_ready_end();

		$key = self::normalize_key( $key, $js, 'js' );
		if ( isset( self::$printed_js[ $key ] ) ) {
			return true;
		}
		self::$printed_js[ $key ] = true;

		wp_add_inline_script( $handle, $js, 'after' );

		return true;
	}

	// ------------------------------------------------------------------------------------------------
	// Internals
	// ------------------------------------------------------------------------------------------------

	/**
	 * Normalize de-dupe key. If empty, use deterministic hash from content.
	 *
	 * @param string $key
	 * @param string $content
	 * @param string $type  'css'|'js'
	 *
	 * @return string
	 */
	protected static function normalize_key( $key, $content, $type ) {

		$key = is_scalar( $key ) ? trim( (string) $key ) : '';
		if ( '' !== $key ) {
			return $key;
		}

		return (string) $type . '-' . md5( (string) $content );
	}

	/**
	 * Make a safe DOM id (ASCII, simple).
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	protected static function safe_dom_id( $id ) {

		$id = strtolower( (string) $id );
		$id = preg_replace( '/[^a-z0-9\-\_]+/', '-', $id );
		$id = trim( $id, '-' );

		// Prevent extremely long ids.
		if ( strlen( $id ) > 80 ) {
			$id = substr( $id, 0, 80 );
		}

		if ( '' === $id ) {
			$id = 'wpbc-inline';
		}

		return $id;
	}
}
