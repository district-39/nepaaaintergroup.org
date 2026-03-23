<?php
/**
 * Front-End Booking Form Source Resolver
 *
 * Decides which engine to use to build the booking form body:
 *  - bfb_db   : load exported shortcodes from booking_form_structures (only when BFB enabled)
 *  - legacy   : use legacy wpdev_bk_personal->get_booking_form()
 *  - simple   : fallback wpbc_simple_form__get_booking_form__as_html()
 *
 * Keeps legacy behavior by default:
 * - If BFB is disabled => never touches DB and returns legacy/simple only.
 *
 * @package Booking Calendar
 * @since   11.0.x
 * @file    ../includes/fontend/class-fe-form-source-resolver.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MultiUser ownership helper (core-safe; MU add-on hooks via filters).
 *
 * @since 11.0.x
 */
class WPBC_FE_MU {


	public static function get_owner_user_id_for_resource( $resource_id ) {

		$resource_id = (int) $resource_id;

		$user_id = apply_bk_filter( 'get_user_of_this_bk_resource', false, $resource_id );
		if ( $user_id ) {
			return $user_id;
		}

		return 0;
	}


	public static function is_user_super_booking_admin( $user_id ) {

		$user_id = intval( $user_id );

		if ( ( class_exists( 'wpdev_bk_multiuser' ) ) && ( $user_id > 0 ) ) {
			$is_user_super_admin = apply_bk_filter( 'is_user_super_admin', $user_id );
			if ( ! $is_user_super_admin ) {
				return false;
			}
		}

		return true;
	}
}


/**
 * Resolver: decides BFB vs legacy vs simple and returns normalized loader args.
 *
 * @since 11.0.x
 */
class WPBC_FE_Form_Source_Resolver {

	/**
	 * Resolve form source.
	 *
	 * Input keys:
	 *  - resource_id (int)
	 *  - form_slug (string)  // from shortcode form_type
	 *  - form_status (string) // published|preview
	 *  - custom_params (array) // parsed from options parser
	 *  - legacy_instance (wpdev_booking|null) // optional
	 *
	 * Output:
	 *  - engine: 'bfb_db'|'legacy'|'simple'
	 *  - apply_after_load_filter: bool
	 *  - bfb_loader_args: array
	 *  - fallback_chain: array
	 *
	 * @param array $req
	 *
	 * @return array
	 */
	public static function resolve( $req ) {

		$req = is_array( $req ) ? $req : array();

		$resource_id     = isset( $req['resource_id'] ) ? (int) $req['resource_id'] : 0;
		$form_slug_raw   = isset( $req['form_slug'] ) ? (string) $req['form_slug'] : '';
		$form_status_raw = isset( $req['form_status'] ) ? (string) $req['form_status'] : '';
		$custom_params   = ( isset( $req['custom_params'] ) && is_array( $req['custom_params'] ) ) ? $req['custom_params'] : array();

		$legacy_instance = isset( $req['legacy_instance'] ) ? $req['legacy_instance'] : null;

		// ---------------------------------------------------------------------
		// Step A: determine slug + status (contract).
		// ---------------------------------------------------------------------
		$form_slug = sanitize_text_field( $form_slug_raw );
		if ( '' === $form_slug ) {
			$form_slug = 'standard';
		}

		$status = sanitize_key( $form_status_raw );

		// Normalize synonyms.
		if ( in_array( $status, array( 'publish', 'published' ), true ) ) {
			$status = 'published';
		}
		if ( 'preview' !== $status ) {
			$status = 'published';
		}

		// ---------------------------------------------------------------------
		// If BFB is NOT enabled => legacy only (no DB touches).
		// ---------------------------------------------------------------------
		if ( ! class_exists( 'WPBC_Frontend_Settings' ) || ! WPBC_Frontend_Settings::is_bfb_enabled( null ) ) {
			return self::fallback_to_legacy_or_simple( $legacy_instance );
		}

		// If BFB runtime is not present => legacy only.
		if ( ! class_exists( 'WPBC_BFB_Form_Loader' ) ) {
			return self::fallback_to_legacy_or_simple( $legacy_instance );
		}

		// If storage is not available => legacy only.
		if ( ! class_exists( 'WPBC_BFB_Form_Storage' ) || ! method_exists( 'WPBC_BFB_Form_Storage', 'get_form_row_by_key' ) ) {
			return self::fallback_to_legacy_or_simple( $legacy_instance );
		}

		// Optional: if table does not exist, skip DB.
		if ( function_exists( 'wpbc_is_table_exists' ) && ! wpbc_is_table_exists( 'booking_form_structures' ) ) {
			return self::fallback_to_legacy_or_simple( $legacy_instance );
		}

		// ---------------------------------------------------------------------
		// Step B: determine owner_user_id (MultiUser, filterable).
		// ---------------------------------------------------------------------
		$owner_user_id = 0;

		// Caller may explicitly pass owner_user_id for future scopes.
		if ( isset( $req['owner_user_id'] ) ) {
			$owner_user_id = max( 0, (int) $req['owner_user_id'] );
		} else {
			$owner_user_id = WPBC_FE_MU::get_owner_user_id_for_resource( $resource_id );
			$is_super_user = WPBC_FE_MU::is_user_super_booking_admin( $owner_user_id );

			// If owner_user_id is is_super_user booking admin => treat as global (0).
			if ( $is_super_user ) {
				$owner_user_id = 0;
			} else {
				$owner_user_id = max( 0, (int) $owner_user_id );
			}
		}


		// ---------------------------------------------------------------------
		// Step C: query order (owner_user_id -> global -> standard fallback).
		// Resolver decides the final key to ask the loader for.
		// ---------------------------------------------------------------------
		$fallback_chain = array();

		$allow_standard_fallback = (bool) apply_filters(
			'wpbc_fe_resolver_allow_fallback_to_standard',
			true,
			$form_slug,
			$status,
			$owner_user_id,
			$req
		);

		// Try: (slug,status,owner_user_id) then (slug,status,global)   Return like this:     $found = [ "form_id":1,  "form_slug":"standard",  "status":"published",  "owner_user_id":0 ]
		$found = self::try_find_row( $form_slug, $status, $owner_user_id, $fallback_chain );
		if ( ! $found && ( $owner_user_id > 0 ) ) {
			$found = self::try_find_row( $form_slug, $status, 0, $fallback_chain );
		}

		// If missing and slug != standard => try standard (same status).
		if ( ! $found && $allow_standard_fallback && ( 'standard' !== $form_slug ) ) {

			$found = self::try_find_row( 'standard', $status, $owner_user_id, $fallback_chain );
			if ( ! $found && ( $owner_user_id > 0 ) ) {
				$found = self::try_find_row( 'standard', $status, 0, $fallback_chain );
			}
		}

		if ( $found ) {

			// Build args for BFB loader.
			$bfb_args = array(
				'form_slug'      => (string) $found['form_slug'],
				'status'         => (string) $found['status'],
				'owner_user_id'  => (int) $found['owner_user_id'],
				'resource_id'    => (int) $resource_id,
			);

			// Pass form_id if known (fast, deterministic).
			if ( ! empty( $found['form_id'] ) ) {
				$bfb_args['form_id'] = (int) $found['form_id'];
			}

			$result = array(
				'engine'                 => 'bfb_db',
				'apply_after_load_filter'=> true,
				'bfb_loader_args'        => $bfb_args,
				'fallback_chain'         => $fallback_chain,
			);

			/**
			 * Final override point (future scopes, preview rules, etc).
			 *
			 * @param array $result
			 * @param array $req
			 */
			return (array) apply_filters( 'wpbc_fe_form_source_resolution', $result, $req );
		}

		// Not found in DB => legacy fallback (existing behavior).
		return self::fallback_to_legacy_or_simple( $legacy_instance );
	}

	// ---------------------------------------------------------------------
	// Internals
	// ---------------------------------------------------------------------

	/**
	 * Try find DB row for (slug,status,owner) and record attempt in chain.
	 *
	 * @param string $slug
	 * @param string $status
	 * @param int    $owner_user_id
	 * @param array  $chain (by ref)
	 *
	 * @return array|false
	 */
	private static function try_find_row( $slug, $status, $owner_user_id, &$chain ) {

		$slug          = sanitize_text_field( (string) $slug );
		$status        = sanitize_key( (string) $status );
		$owner_user_id = max( 0, (int) $owner_user_id );

		$chain[] = array(
			'form_slug'     => $slug,
			'status'        => $status,
			'owner_user_id' => $owner_user_id,
		);

		$row = WPBC_BFB_Form_Storage::get_form_row_by_key( $slug, $status, $owner_user_id );

		if ( ! $row || empty( $row->booking_form_id ) ) {
			return false;
		}

		return array(
			'form_id'       => (int) $row->booking_form_id,
			'form_slug'     => $slug,
			'status'        => $status,
			'owner_user_id' => isset( $row->owner_user_id ) ? max( 0, (int) $row->owner_user_id ) : $owner_user_id,
		);
	}

	/**
	 * Legacy/simple fallback result.
	 *
	 * @param mixed $legacy_instance
	 *
	 * @return array
	 */
	private static function fallback_to_legacy_or_simple( $legacy_instance ) {

		if ( ( ! empty( $legacy_instance ) ) && ( false !== $legacy_instance->wpdev_bk_personal ) && ( 'On' != get_bk_option( 'booking_is_use_simple_booking_form' ) ) ) {
			return array(
				'engine'                  => 'legacy',
				'apply_after_load_filter' => false,
				'bfb_loader_args'         => array(),
				'fallback_chain'          => array(),
			);
		}

		return array(
			'engine'                  => 'simple',
			'apply_after_load_filter' => true,
			'bfb_loader_args'         => array(),
			'fallback_chain'          => array(),
		);
	}
}


/**
 * BFB resolver for "Booking Form Data" template (content_form).
 *
 * Purpose:
 * - When BFB DB engine is used for a form, the correct booking "data template"
 *   must come from wp_booking_form_structures.content_form (not booking_form_show option).
 * - This is used by wpbc_get__booking_form_data__show() to replace [field] shortcodes
 *   with values from booking form_data string.
 *
 * Design notes:
 * - Uses WPBC_FE_Form_Source_Resolver::resolve() to keep the same decision chain as front-end rendering.
 * - Reads the template via wpbc_bfb_get_booking_form_pair() (must expose content_form).
 * - Safe no-op when BFB / resolver is not available.
 *
 * @since 11.0.x
 */
class WPBC_BFB_Booking_Data_Content_Resolver {

	/**
	 * Try to override legacy $booking_form_show by BFB content_form (resolver-driven).
	 *
	 * @param string $booking_form_show Legacy resolved booking_form_show.
	 * @param int    $resource_id       Booking resource ID.
	 * @param string $form_slug         Form slug/name (legacy: 'standard' or custom form name).
	 * @param string $form_data         Booking form_data string from DB.
	 * @param array  $params            Optional. Context override (e.g. form_status/preview token).
	 *
	 * @return string Possibly overridden template.
	 */
	public static function maybe_override_booking_form_show_by_bfb( $booking_form_show, $resource_id, $form_slug, $form_data, $params = array() ) {

		$booking_form_show = (string) $booking_form_show;
		$resource_id       = (int) $resource_id;
		$form_slug         = (string) $form_slug;
		$form_data         = (string) $form_data;

		if ( '' === $form_slug ) {
			$form_slug = 'standard';
		}

		// Hard requirements for BFB resolver path.
		if ( ! class_exists( 'WPBC_FE_Form_Source_Resolver' ) ) {
			return $booking_form_show;
		}
		if ( ! class_exists( 'WPBC_BFB_Form_Loader' ) ) {
			return $booking_form_show;
		}

		// ------------------------------------------------------------
		// Preview-aware context (critical fix).
		// ------------------------------------------------------------
		$ctx = array();
		if ( function_exists( 'wpbc_get_request_form_context' ) ) {
			$ctx = wpbc_get_request_form_context();
		}
		$ctx = wp_parse_args( ( is_array( $params ) ? $params : array() ), ( is_array( $ctx ) ? $ctx : array() ) );

		$form_status = ( isset( $ctx['form_status'] ) ) ? sanitize_key( $ctx['form_status'] ) : 'published';
		if ( in_array( $form_status, array( 'publish', 'published' ), true ) ) {
			$form_status = 'published';
		}
		if ( 'preview' !== $form_status ) {
			$form_status = 'published';
		}

		// If preview session has exported content_form payload => use it (fast + correct).
		if ( ( 'preview' === $form_status ) && function_exists( 'wpbc_bfb_preview__maybe_get_payload_from_context' ) ) {

			$payload = wpbc_bfb_preview__maybe_get_payload_from_context( $ctx );

			if ( ! empty( $payload ) && isset( $payload['content_form'] ) && ( '' !== (string) $payload['content_form'] ) ) {

				$content_form = (string) $payload['content_form'];

				// Keep legacy behavior: custom html shortcodes + language.
				$content_form = wpbc_bf__replace_custom_html_shortcodes( $content_form );
				$content_form = wpbc_lang( $content_form );

				return $content_form;
			}
		}

		// ------------------------------------------------------------
		// Resolver-driven DB load (status must NOT be hard-coded).
		// ------------------------------------------------------------
		$custom_params = array();

		// Optional: allow future-proof explicit form_id embedded into booking form_data.
		$bfb_form_id = self::get_bfb_form_id_from_form_data( $form_data, $resource_id );
		if ( $bfb_form_id > 0 ) {
			$custom_params['bfb_form_id'] = (int) $bfb_form_id;
		}

		$req = array(
			'resource_id'     => $resource_id,
			'form_slug'       => $form_slug,
			'form_status'     => $form_status, // <-- critical: use preview/published
			'custom_params'   => $custom_params,
			'legacy_instance' => null,

			// Future-proof: let filters access the full preview context if they need it.
			'ctx'             => $ctx,
		);

		$resolved = WPBC_FE_Form_Source_Resolver::resolve( $req );

		// If preview status is requested but nothing found, try published as fallback (keeps BFB).
		if ( ( empty( $resolved['engine'] ) || ( 'bfb_db' !== $resolved['engine'] ) ) && ( 'preview' === $form_status ) ) {
			$req['form_status'] = 'published';
			$resolved = WPBC_FE_Form_Source_Resolver::resolve( $req );
		}

		if ( empty( $resolved['engine'] ) || ( 'bfb_db' !== $resolved['engine'] ) ) {
			return $booking_form_show;
		}

		$bfb_loader_args = array();
		if ( ! empty( $resolved['bfb_loader_args'] ) && is_array( $resolved['bfb_loader_args'] ) ) {
			$bfb_loader_args = $resolved['bfb_loader_args'];
		}

		// Keep old behavior: allow explicit override from booking data (if present).
		if ( ( $bfb_form_id > 0 ) && empty( $bfb_loader_args['form_id'] ) ) {
			$bfb_loader_args['form_id'] = (int) $bfb_form_id;
		}

		// Signal loader that we need the "content_form" (booking fields data template).
		$bfb_loader_args['return'] = 'content_form';

		$bfb_pair = wpbc_bfb_get_booking_form_pair( $bfb_loader_args );

		$content_form = self::extract_content_form_from_pair( $bfb_pair );

		if ( '' === trim( $content_form ) ) {
			return $booking_form_show;
		}

		// Keep legacy behavior: custom html shortcodes + language.
		$content_form = wpbc_bf__replace_custom_html_shortcodes( $content_form );
		$content_form = wpbc_lang( $content_form );

		return $content_form;
	}


	/**
	 * Extract content template from loader return (supports multiple formats).
	 *
	 * Supported formats:
	 * 1) array( 'content' => '...' )                                  <- current WPBC_BFB_Form_Loader output
	 * 2) array( 'content_form' => '...' )                             <- optional future alias
	 * 3) array( 'form' => array( 'content' => '...' ) )               <- future-proof
	 * 4) array( 'form' => array( 'content_form' => '...' ) )          <- future-proof
	 *
	 * @param mixed $bfb_pair Loader result.
	 *
	 * @return string
	 */
	private static function extract_content_form_from_pair( $bfb_pair ) {

		if ( ! is_array( $bfb_pair ) ) {
			return '';
		}

		/**
		 * Safety: if loader fell back to legacy, do NOT override booking_form_show.
		 * WPBC_BFB_Form_Loader returns 'source' => 'builder'|'legacy'.
		 */
		if ( isset( $bfb_pair['source'] ) && ( 'builder' !== $bfb_pair['source'] ) ) {
			return '';
		}

		// Current loader output key.
		if ( isset( $bfb_pair['content'] ) && is_string( $bfb_pair['content'] ) ) {
			return (string) $bfb_pair['content'];
		}

		// Optional alias (if some implementations already return it).
		if ( isset( $bfb_pair['content_form'] ) && is_string( $bfb_pair['content_form'] ) ) {
			return (string) $bfb_pair['content_form'];
		}

		// Future-proof nested formats.
		if ( isset( $bfb_pair['form'] ) && is_array( $bfb_pair['form'] ) ) {

			if ( isset( $bfb_pair['form']['content'] ) && is_string( $bfb_pair['form']['content'] ) ) {
				return (string) $bfb_pair['form']['content'];
			}

			if ( isset( $bfb_pair['form']['content_form'] ) && is_string( $bfb_pair['form']['content_form'] ) ) {
				return (string) $bfb_pair['form']['content_form'];
			}
		}

		return '';
	}


	/**
	 * (Optional / future-proof) Parse BFB form_id stored inside booking form_data.
	 *
	 * Supported patterns:
	 * - "wpbc_bfb_form_id{resource_id}^{ID}"
	 * - "wpbc_bfb_form_id^{ID}"
	 *
	 * @param string $form_data
	 * @param int    $resource_id
	 *
	 * @return int
	 */
	private static function get_bfb_form_id_from_form_data( $form_data, $resource_id ) {

		$form_data   = (string) $form_data;
		$resource_id = (int) $resource_id;

		if ( '' === $form_data ) {
			return 0;
		}

		$prefixes = array(
			'wpbc_bfb_form_id' . $resource_id . '^',
			'wpbc_bfb_form_id^',
		);

		foreach ( $prefixes as $prefix ) {

			$pos = strpos( $form_data, $prefix );
			if ( false === $pos ) {
				continue;
			}

			$chunk = substr( $form_data, $pos + strlen( $prefix ) );

			// Ends at "~" if present.
			if ( false !== strpos( $chunk, '~' ) ) {
				$chunk = substr( $chunk, 0, strpos( $chunk, '~' ) );
			}

			$chunk = trim( $chunk );
			if ( '' === $chunk ) {
				continue;
			}

			$form_id = (int) $chunk;
			if ( $form_id > 0 ) {
				return $form_id;
			}
		}

		return 0;
	}
}
