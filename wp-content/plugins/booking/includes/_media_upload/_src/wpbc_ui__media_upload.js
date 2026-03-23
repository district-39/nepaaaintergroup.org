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

var wpbc_media_file_frame;

jQuery( document ).ready( function (){
	//'use strict';

	jQuery( '.wpbc_media_upload_button' ).on( 'click', function( event ) {

		var j_btn = jQuery( this );
		var is_multi_selection = false;
		// var insert_field_separator = ',';

		// Stop the anchor's default behavior
		event.preventDefault();

		// If frame exist close it
		if ( wpbc_media_file_frame ) {
			wpbc_media_file_frame.close();
		}

		// -----------------------------------------------------------------------------------------------------
		// Create Media Frame
		// -----------------------------------------------------------------------------------------------------
		wpbc_media_file_frame = wp.media.frames.wpbc_media_upload_file_frame = wp.media( {								// Check  here ../wp-includes/js/media-views.js
			// Set the title of the modal.
			title: j_btn.data( 'modal_title' ),
			library: {
				 //	type: ''
				 // type: [ 'video', 'image' ]
				 type: [ 'image' ]
			},
			button: {
				text: j_btn.data( 'btn_title' ),
			},
			multiple: is_multi_selection,
			// states: [
			// 			new wp.media.controller.Library( {
			// 				/*
			// 					Add to  this libaray custom post  parameter: $_POST['query'][ $media_uploader_params['key'] ] = $media_uploader_params['value']
			// 					We are checking in functon wpbc_media_filter_posts_where media files that  only  relative to  this medi Frame opening
			// 					And filtering posts (in WHERE) relative custom path to  our files.
			// 					echo '{' . $media_uploader_params['key'] . ": '" . $media_uploader_params['value'] . "' }";
			// 				*/
			// 						library: wp.media.query(),
			// 						multiple: is_multi_selection,
			// 						title:	j_btn.data( 'modal_title' ),
			// 						priority: 15,
			// 						filterable: 'uploaded',
			// 				 		type: ['image']
			// 						// idealColumnWidth: 125
			// 				} )
			// 		]
		} );

		/*
		///////////////////////////////////////////////////////////////////////
		// Set  custom parameters for uploader	->  $_POST['wpbc_media_type'] - checking in "upload_dir",  when filter_upload_dir
		///////////////////////////////////////////////////////////////////////
		wpbc_media_file_frame.on( 'ready', function () {
			wpbc_media_file_frame.uploader.options.uploader.params = {
				type: 'wpbc_media_download',
				<?php
				echo $media_uploader_params['key'] . ": '" . $media_uploader_params['value'] . "'";
				?>
			};
		} );
		*/

		///////////////////////////////////////////////////////////////////////
		// When File have selected, do this
		///////////////////////////////////////////////////////////////////////
		wpbc_media_file_frame.on( 'select', function () {

			if ( ! is_multi_selection ) { // Single file

				var attachment = wpbc_media_file_frame.state().get( 'selection' ).first().toJSON();

				// Put URL of file to text field
				jQuery( '#' + j_btn.data( 'url_field' ) ).val( attachment.url );
				jQuery( '#' + j_btn.data( 'url_field' ) ).trigger( "wpbc_media_upload_url_set" );

				//j_btn.parents( '.wpbc_extra__excerpt_img' ).find( '.ui_group__thumbnail .wpbc_media_upload_button' ).html( '<img src="' + attachment.url + '" class="search_thumbnail_img" />' );

			} else { // Multiple files.

				var file_paths = '';
				// var csv_data_line = '';
				wpbc_media_file_frame.state().get( 'selection' ).map( function ( attachment ){

						/*
							// Request  new data
							attachment.fetch().then(function (data) {
								console.log(data);
								// preloading finished after this you can use your attachment normally
								// wp.media.attachment( attachment.id ).get('url');
							});
						*/

					attachment = attachment.toJSON();

						/*
							if ( attachment.url ) {
								// Insert info from selected files
								csv_data_line = attachment.id + insert_field_separator + attachment.title  + insert_field_separator + attachment.wpbc_media_version_num  + insert_field_separator + attachment.description + insert_field_separator + attachment.url
								file_paths = file_paths ? file_paths + "\n" + csv_data_line : csv_data_line;
							}
						*/
					 file_paths = file_paths ? file_paths + "\n" + attachment.url : attachment.url;
				});

				jQuery( '#' + j_btn.data( 'url_field' ) ).val( file_paths );
				jQuery( '#' + j_btn.data( 'url_field' ) ).trigger( "wpbc_media_upload_url_set" );
			}

		} );

		/*
			// Fires when a state activates.
			wpbc_media_file_frame.on( 'activate', function() { alert('activate'); } );

			// Fires after the frame markup has been built, but not appended to the DOM.
			// @see wp.media.view.Modal.attach()
			wpbc_media_file_frame.on( 'ready', function() { alert('ready'); } );

			// Fires when the frame's $el is appended to its DOM container.
			// @see media.view.Modal.attach()
			wpbc_media_file_frame.on( 'attach', function() { alert('attach'); } );

			// Fires when the modal opens (becomes visible).
			// @see media.view.Modal.open()
			wpbc_media_file_frame.on( 'open', function() { alert('open'); } );

			// Fires when the modal closes via the escape key.
			// @see media.view.Modal.close()
			wpbc_media_file_frame.on( 'escape', function() { alert('escape'); } );

			// Fires when the modal closes.
			// @see media.view.Modal.close()
			wpbc_media_file_frame.on( 'close', function() { alert('close'); } );

			// Fires when a user has selected attachment(s) and clicked the select button.
			// @see media.view.MediaFrame.Post.mainInsertToolbar()
			wpbc_media_file_frame.on( 'select', function() {
				var selectionCollection = wpbc_media_file_frame.state().get('select');
			} );

			// Fires when a mode is deactivated on a region { 'menu' | title | content | toolbar | router }
			wpbc_media_file_frame.on( 'content:deactivate', function() { alert('{region}:deactivate'); } );
			// and a more specific event including the mode.
			wpbc_media_file_frame.on( 'content:deactivate:{mode}', function() { alert('{region}:deactivate{mode}'); } );

			// Fires when a region is ready for its view to be created.
			wpbc_media_file_frame.on( 'content:create', function() { alert('{region}:create'); } );
			// and a more specific event including the mode.
			wpbc_media_file_frame.on( 'content:create:{mode}', function() { alert('{region}:create{mode}'); } );

			// Fires when a region is ready for its view to be rendered.
			wpbc_media_file_frame.on( 'content:render', function() { alert('{region}:render'); } );
			// and a more specific event including the mode.
			wpbc_media_file_frame.on( 'content:render:{mode}', function() { alert('{region}:render{mode}'); } );

			// Fires when a new mode is activated (after it has been rendered) on a region.
			wpbc_media_file_frame.on( 'content:activate', function() { alert('{region}:activate'); } );
			// and a more specific event including the mode.
			wpbc_media_file_frame.on( 'content:activate:{mode}', function() { alert('{region}:activate{mode}'); } );

			// Get an object representing the current state.
			//wpbc_media_file_frame.state();

			// Get an object representing the previous state.
			//wpbc_media_file_frame.lastState();
		*/
		/*
			// Debuge all events from  media Frame!
			wpbc_media_file_frame.on( "all", function ( eventName ){
				console.log( 'Frame Event: ' + eventName );
			} );

			// Debuge all events from  media Frame!
			wp.media.model.Attachment.get( "collection" ).collection.on( "all", function ( eventName ){
				console.log( '[Collection] Event: ' + eventName );
			} );
			wp.media.model.Attachment.get( "models" ).collection.on( "all", function ( eventName ){
				console.log( '[models] Event: ' + eventName );
			} );
			wp.media.model.Attachment.get( "views" ).collection.on( "all", function ( eventName ){
				console.log( '[views] Event: ' + eventName );
			} );
		*/

		// Open the modal.
		wpbc_media_file_frame.open();
	});
});