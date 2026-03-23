<?php
/**
 * Custom import export.
 *
 * @link http://shapedplugin.com
 * @since 2.0.0
 *
 * @package Easy_Accordion_free
 * @subpackage Easy_Accordion_free/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom import export.
 */
class Easy_Accordion_Import_Export {

	/**
	 * Export
	 *
	 * @param  mixed $accordion_ids Export accordion ids.
	 * @return object
	 */
	public function export( $accordion_ids ) {
		$export = array();
		if ( ! empty( $accordion_ids ) ) {
			$post_type = 'all_faqs' === $accordion_ids ? 'sp_accordion_faqs' : 'sp_easy_accordion';
			$post_in   = 'all_faqs' === $accordion_ids || 'all_shortcodes' === $accordion_ids ? '' : $accordion_ids;

			$args       = array(
				'post_type'        => $post_type,
				'post_status'      => array( 'inherit', 'publish' ),
				'orderby'          => 'modified',
				'suppress_filters' => 1, // wpml, ignore language filter.
				'posts_per_page'   => -1,
				'post__in'         => $post_in,
			);
			$accordions = get_posts( $args );
			if ( ! empty( $accordions ) ) {
				foreach ( $accordions as $accordion ) {
					if ( 'all_faqs' !== $accordion_ids ) {
						$accordion_export = array(
							'title'       => sanitize_text_field( $accordion->post_title ),
							'original_id' => absint( $accordion->ID ),
							'meta'        => array(),
						);
					}
					if ( 'all_faqs' === $accordion_ids ) {
							$accordion_export = array(
								'title'       => sanitize_text_field( $accordion->post_title ),
								'original_id' => absint( $accordion->ID ),
								'content'     => $accordion->post_content,
								'image'       => esc_url_raw( get_the_post_thumbnail_url( $accordion->ID, 'single-post-thumbnail' ) ),
								'all_faqs'    => 'all_faqs',
								'meta'        => array(),
							);
					}
					foreach ( get_post_meta( $accordion->ID ) as $metakey => $value ) {
						$accordion_export['meta'][ $metakey ] = $value[0];
					}
					$export['accordion'][] = $accordion_export;

					unset( $accordion_export );
				}
				$export['metadata'] = array(
					'version' => SP_EA_VERSION,
					'date'    => gmdate( 'Y/m/d' ),
				);
			}
			return $export;
		}
	}

	/**
	 * Export Accordion by ajax.
	 *
	 * @return void
	 */
	public function export_accordions() {
		$nonce = ( ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		$capability      = apply_filters( 'sp_easy_accordion_ui_permission', 'manage_options' );
		$is_user_capable = current_user_can( $capability ) ? true : false;

		if ( ! $is_user_capable ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'You do not have permission to export.', 'easy-accordion-free' ),
				),
				403
			);
		}

		if ( ! wp_verify_nonce( $nonce, 'eapro_options_nonce' ) ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'Error: Nonce verification has failed. Please try again.', 'easy-accordion-free' ),
				),
				403
			);
		}

		$accordion_ids = isset( $_POST['eap_ids'] ) ? wp_unslash( $_POST['eap_ids'] ) : ''; // phpcs:ignore
		$export        = $this->export( $accordion_ids );

		if ( is_wp_error( $export ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html( $export->get_error_message() ),
				),
				400
			);
		}

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
            // @codingStandardsIgnoreLine
            echo wp_json_encode($export, JSON_PRETTY_PRINT);
			die;
		}

		wp_send_json( $export, 200 );
	}

	/**
	 * Insert an attachment from an URL address.
	 *
	 * @param  String $url remote url.
	 * @param  Int    $parent_post_id parent post id.
	 * @return Int    Attachment ID
	 */
	public function insert_attachment_from_url( $url, $parent_post_id = null ) {

		if ( ! class_exists( 'WP_Http' ) ) {
			include_once ABSPATH . WPINC . '/class-http.php';
		}
		$attachment_title = sanitize_file_name( pathinfo( $url, PATHINFO_FILENAME ) );
		// Does the attachment already exist ?
		$attachment_id = post_exists( $attachment_title, '', '', 'attachment' );
		if ( $attachment_id ) {
			return absint( $attachment_id );
		}

		$http     = new \WP_Http();
		$response = $http->request( $url );
		if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {
			return false;
		}
		$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
		if ( ! empty( $upload['error'] ) ) {
			return false;
		}

		$file_path = $upload['file'];
		$file_name = basename( $file_path );
		$file_type = wp_check_filetype( $file_name, null );

		// Double-check MIME type & restrict to safe file types (images only, here).
		if ( empty( $file_type['type'] ) || ! wp_match_mime_types( 'image', $file_type['type'] ) ) {
			// Delete the invalid file immediately.
			wp_delete_file( $file_path );
			return false;
		}

		$wp_upload_dir = wp_upload_dir();

		$post_info = array(
			'guid'           => esc_url_raw( $wp_upload_dir['url'] . '/' . $file_name ),
			'post_mime_type' => sanitize_mime_type( $file_type['type'] ),
			'post_title'     => esc_html( $attachment_title ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Create the attachment.
		$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

		// Include image.php.
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Define attachment metadata.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

		// Assign metadata to attachment.
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	/**
	 * Import
	 *
	 * @param  array $accordions Import accordion array.
	 * @throws \Exception Error message.
	 * @return object
	 */
	public function import( $accordions ) {
		$errors        = array();
		$eap_post_type = 'sp_accordion_faqs';
		foreach ( $accordions as $index => $accordion ) {
			$errors[ $index ] = array();
			$new_accordion_id = 0;
			$eap_post_type    = isset( $accordion['all_faqs'] ) ? 'sp_accordion_faqs' : 'sp_easy_accordion';
			try {
				$new_accordion_id = wp_insert_post(
					array(
						'post_title'   => isset( $accordion['title'] ) ? sanitize_text_field( $accordion['title'] ) : '',
						'post_content' => isset( $accordion['content'] ) ? $accordion['content'] : '',
						'post_status'  => 'publish',
						'post_type'    => $eap_post_type,
					),
					true
				);

				if ( isset( $accordion['all_faqs'] ) ) {
						$url = isset( $accordion['image'] ) && ! empty( $accordion['image'] ) ? $accordion['image'] : '';
						// Insert attachment id.
						$thumb_id = $this->insert_attachment_from_url( $url, $new_accordion_id );

					if ( $url && $thumb_id ) {
						$accordion['meta']['_thumbnail_id'] = $thumb_id;
					}
				}

				if ( is_wp_error( $new_accordion_id ) ) {
					throw new Exception( $new_accordion_id->get_error_message() );
				}

				if ( isset( $accordion['meta'] ) && is_array( $accordion['meta'] ) ) {
					foreach ( $accordion['meta'] as $key => $value ) {
						$data = str_replace( '{#ID#}', $new_accordion_id, $value );
						if ( is_serialized( $data ) ) {
							// If the value is serialized, we need to unserialize it.
							$data = unserialize( $data, array( 'allowed_classes' => false ) );
						}
						update_post_meta(
							$new_accordion_id,
							$key,
							wp_slash( $data )
						);
					}
				}
			} catch ( Exception $e ) {
				array_push( $errors[ $index ], $e->getMessage() );
				// If there was a failure somewhere, clean up.
				wp_trash_post( $new_accordion_id );
			}

			// If no errors, remove the index.
			if ( ! count( $errors[ $index ] ) ) {
				unset( $errors[ $index ] );
			}

			// External modules manipulate data here.
			do_action( 'sp_easy_accordion_accordion_imported', $new_accordion_id );
		}

		$errors = reset( $errors );
		return isset( $errors[0] ) ? new WP_Error( 'import_accordion_error', $errors[0] ) : $eap_post_type;
	}

	/**
	 * Import Accordions by ajax.
	 *
	 * @return void
	 */
	public function import_accordions() {
		$nonce           = ( ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$capability      = apply_filters( 'sp_easy_accordion_ui_permission', 'manage_options' );
		$is_user_capable = current_user_can( $capability ) ? true : false;

		if ( ! $is_user_capable ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'You do not have permission to import.', 'easy-accordion-free' ),
				),
				403
			);
		}

		if ( ! wp_verify_nonce( $nonce, 'eapro_options_nonce' ) ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'Error: Nonce verification has failed. Please try again.', 'easy-accordion-free' ),
				),
				403
			);
		}

		// This variable has been sanitize in the below.
		$data       = isset( $_POST['accordion'] ) ? wp_unslash( $_POST['accordion'] ) : ''; // phpcs:ignore -- Data is sanitized later (line 319).
		if ( ! $data ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Nothing to import.', 'easy-accordion-free' ),
				),
				400
			);
		}

		// Decode JSON with error checking.
		$decoded_data = json_decode( $data, true );
		if ( is_string( $decoded_data ) ) {
			$decoded_data = json_decode( $decoded_data, true );
		}
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid JSON data.', 'easy-accordion-free' ),
				),
				400
			);
		}

		$allow_special_tags = isset( $_POST['allowSpecialTags'] ) ? sanitize_text_field( wp_unslash( $_POST['allowSpecialTags'] ) ) : ''; // Use this option to allow special tags (e.g., iframe, video).
		$accordions         = $allow_special_tags ? $decoded_data['accordion'] : wp_kses_post_deep( $decoded_data['accordion'] );
		$status             = $this->import( $accordions );

		if ( is_wp_error( $status ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html( $status->get_error_message() ),
				),
				400
			);
		}

		wp_send_json_success( $status, 200 );
	}
}
