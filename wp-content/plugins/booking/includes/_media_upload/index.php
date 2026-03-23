<?php
// Silence is golden.

/**
 * == How to Use ?  ==
 *
 * 1. Load JS:
 *
 * function wpbc_register_js__media_upload( $hook ) {
 *      if ( wpbc_can_i_load_on_this_page__media_upload() ) {
 *          wpbc_load_js__required_for_media_upload();
 *      }
 * }
 * add_action( 'admin_enqueue_scripts', 'wpbc_register_js__media_upload'  );
 *
 *
 * 2. Inside the page use html element with  this class:  "wpbc_media_upload_button"
 *
 * <a   href="javascript:void(0)"
 *      class="wpbc_media_upload_button"
 *      data-modal_title="<?php echo esc_attr( __( 'Select Image', 'booking' ) ); ?>"
 *      data-btn_title="<?php echo esc_attr( __( 'Select Image', 'booking' ) ); ?>"
 *      data-url_field="MY_URL_FIELD"
 *                                      ><i class="menu_icon icon-1x wpbc_icn_tune"></i></a>
 *
 *   3. 'data-url_field' attribute define TEXT field,  where will be inserted URL of selected image
 *      'data-modal_title'  - Header title in popup
 *      'data-btn_title'    - Button title in popup
 *
 *   4. If you need to  update URL  somewhere else at  the page,  use this j JavaScript hook:
 *
 * <script type="text/javascript">
 *  jQuery(document).ready(function(){
 *    jQuery( '#MY_URL_FIELD').on( 'wpbc_media_upload_url_set', function(){
 *        jQuery( '#MY_URL_FIELD').parents( '.wpbc_extra__excerpt_img' ).find( '.ui_group__thumbnail .wpbc_media_upload_button' ).html( '<img src="' + jQuery( '#MY_URL_FIELD').val() + '" class="search_thumbnail_img" />' );
 *    });
 *  });
 * </script>
 *
 */