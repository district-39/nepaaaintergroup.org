"use strict";

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
jQuery(document).ready(function () {
  //'use strict';

  jQuery('.wpbc_media_upload_button').on('click', function (event) {
    var j_btn = jQuery(this);
    var is_multi_selection = false;
    // var insert_field_separator = ',';

    // Stop the anchor's default behavior
    event.preventDefault();

    // If frame exist close it
    if (wpbc_media_file_frame) {
      wpbc_media_file_frame.close();
    }

    // -----------------------------------------------------------------------------------------------------
    // Create Media Frame
    // -----------------------------------------------------------------------------------------------------
    wpbc_media_file_frame = wp.media.frames.wpbc_media_upload_file_frame = wp.media({
      // Check  here ../wp-includes/js/media-views.js
      // Set the title of the modal.
      title: j_btn.data('modal_title'),
      library: {
        //	type: ''
        // type: [ 'video', 'image' ]
        type: ['image']
      },
      button: {
        text: j_btn.data('btn_title')
      },
      multiple: is_multi_selection
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
    });

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
    wpbc_media_file_frame.on('select', function () {
      if (!is_multi_selection) {
        // Single file

        var attachment = wpbc_media_file_frame.state().get('selection').first().toJSON();

        // Put URL of file to text field
        jQuery('#' + j_btn.data('url_field')).val(attachment.url);
        jQuery('#' + j_btn.data('url_field')).trigger("wpbc_media_upload_url_set");

        //j_btn.parents( '.wpbc_extra__excerpt_img' ).find( '.ui_group__thumbnail .wpbc_media_upload_button' ).html( '<img src="' + attachment.url + '" class="search_thumbnail_img" />' );
      } else {
        // Multiple files.

        var file_paths = '';
        // var csv_data_line = '';
        wpbc_media_file_frame.state().get('selection').map(function (attachment) {
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
        jQuery('#' + j_btn.data('url_field')).val(file_paths);
        jQuery('#' + j_btn.data('url_field')).trigger("wpbc_media_upload_url_set");
      }
    });

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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5jbHVkZXMvX21lZGlhX3VwbG9hZC9fb3V0L3dwYmNfdWlfX21lZGlhX3VwbG9hZC5qcyIsIm5hbWVzIjpbIndwYmNfbWVkaWFfZmlsZV9mcmFtZSIsImpRdWVyeSIsImRvY3VtZW50IiwicmVhZHkiLCJvbiIsImV2ZW50Iiwial9idG4iLCJpc19tdWx0aV9zZWxlY3Rpb24iLCJwcmV2ZW50RGVmYXVsdCIsImNsb3NlIiwid3AiLCJtZWRpYSIsImZyYW1lcyIsIndwYmNfbWVkaWFfdXBsb2FkX2ZpbGVfZnJhbWUiLCJ0aXRsZSIsImRhdGEiLCJsaWJyYXJ5IiwidHlwZSIsImJ1dHRvbiIsInRleHQiLCJtdWx0aXBsZSIsImF0dGFjaG1lbnQiLCJzdGF0ZSIsImdldCIsImZpcnN0IiwidG9KU09OIiwidmFsIiwidXJsIiwidHJpZ2dlciIsImZpbGVfcGF0aHMiLCJtYXAiLCJvcGVuIl0sInNvdXJjZXMiOlsiaW5jbHVkZXMvX21lZGlhX3VwbG9hZC9fc3JjL3dwYmNfdWlfX21lZGlhX3VwbG9hZC5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogPT0gSG93IHRvIFVzZSA/ICA9PVxyXG4gKlxyXG4gKiAxLiBMb2FkIEpTOlxyXG4gKlxyXG4gKiBmdW5jdGlvbiB3cGJjX3JlZ2lzdGVyX2pzX19tZWRpYV91cGxvYWQoICRob29rICkge1xyXG4gKiAgICAgIGlmICggd3BiY19jYW5faV9sb2FkX29uX3RoaXNfcGFnZV9fbWVkaWFfdXBsb2FkKCkgKSB7XHJcbiAqICAgICAgICAgIHdwYmNfbG9hZF9qc19fcmVxdWlyZWRfZm9yX21lZGlhX3VwbG9hZCgpO1xyXG4gKiAgICAgIH1cclxuICogfVxyXG4gKiBhZGRfYWN0aW9uKCAnYWRtaW5fZW5xdWV1ZV9zY3JpcHRzJywgJ3dwYmNfcmVnaXN0ZXJfanNfX21lZGlhX3VwbG9hZCcgICk7XHJcbiAqXHJcbiAqXHJcbiAqIDIuIEluc2lkZSB0aGUgcGFnZSB1c2UgaHRtbCBlbGVtZW50IHdpdGggIHRoaXMgY2xhc3M6ICBcIndwYmNfbWVkaWFfdXBsb2FkX2J1dHRvblwiXHJcbiAqXHJcbiAqIDxhICAgaHJlZj1cImphdmFzY3JpcHQ6dm9pZCgwKVwiXHJcbiAqICAgICAgY2xhc3M9XCJ3cGJjX21lZGlhX3VwbG9hZF9idXR0b25cIlxyXG4gKiAgICAgIGRhdGEtbW9kYWxfdGl0bGU9XCI8P3BocCBlY2hvIGVzY19hdHRyKCBfXyggJ1NlbGVjdCBJbWFnZScsICdib29raW5nJyApICk7ID8+XCJcclxuICogICAgICBkYXRhLWJ0bl90aXRsZT1cIjw/cGhwIGVjaG8gZXNjX2F0dHIoIF9fKCAnU2VsZWN0IEltYWdlJywgJ2Jvb2tpbmcnICkgKTsgPz5cIlxyXG4gKiAgICAgIGRhdGEtdXJsX2ZpZWxkPVwiTVlfVVJMX0ZJRUxEXCJcclxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgID48aSBjbGFzcz1cIm1lbnVfaWNvbiBpY29uLTF4IHdwYmNfaWNuX3R1bmVcIj48L2k+PC9hPlxyXG4gKlxyXG4gKiAgIDMuICdkYXRhLXVybF9maWVsZCcgYXR0cmlidXRlIGRlZmluZSBURVhUIGZpZWxkLCAgd2hlcmUgd2lsbCBiZSBpbnNlcnRlZCBVUkwgb2Ygc2VsZWN0ZWQgaW1hZ2VcclxuICogICAgICAnZGF0YS1tb2RhbF90aXRsZScgIC0gSGVhZGVyIHRpdGxlIGluIHBvcHVwXHJcbiAqICAgICAgJ2RhdGEtYnRuX3RpdGxlJyAgICAtIEJ1dHRvbiB0aXRsZSBpbiBwb3B1cFxyXG4gKlxyXG4gKiAgIDQuIElmIHlvdSBuZWVkIHRvICB1cGRhdGUgVVJMICBzb21ld2hlcmUgZWxzZSBhdCAgdGhlIHBhZ2UsICB1c2UgdGhpcyBqIEphdmFTY3JpcHQgaG9vazpcclxuICpcclxuICogPHNjcmlwdCB0eXBlPVwidGV4dC9qYXZhc2NyaXB0XCI+XHJcbiAqICBqUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCl7XHJcbiAqICAgIGpRdWVyeSggJyNNWV9VUkxfRklFTEQnKS5vbiggJ3dwYmNfbWVkaWFfdXBsb2FkX3VybF9zZXQnLCBmdW5jdGlvbigpe1xyXG4gKiAgICAgICAgalF1ZXJ5KCAnI01ZX1VSTF9GSUVMRCcpLnBhcmVudHMoICcud3BiY19leHRyYV9fZXhjZXJwdF9pbWcnICkuZmluZCggJy51aV9ncm91cF9fdGh1bWJuYWlsIC53cGJjX21lZGlhX3VwbG9hZF9idXR0b24nICkuaHRtbCggJzxpbWcgc3JjPVwiJyArIGpRdWVyeSggJyNNWV9VUkxfRklFTEQnKS52YWwoKSArICdcIiBjbGFzcz1cInNlYXJjaF90aHVtYm5haWxfaW1nXCIgLz4nICk7XHJcbiAqICAgIH0pO1xyXG4gKiAgfSk7XHJcbiAqIDwvc2NyaXB0PlxyXG4gKlxyXG4gKi9cclxuXHJcbnZhciB3cGJjX21lZGlhX2ZpbGVfZnJhbWU7XHJcblxyXG5qUXVlcnkoIGRvY3VtZW50ICkucmVhZHkoIGZ1bmN0aW9uICgpe1xyXG5cdC8vJ3VzZSBzdHJpY3QnO1xyXG5cclxuXHRqUXVlcnkoICcud3BiY19tZWRpYV91cGxvYWRfYnV0dG9uJyApLm9uKCAnY2xpY2snLCBmdW5jdGlvbiggZXZlbnQgKSB7XHJcblxyXG5cdFx0dmFyIGpfYnRuID0galF1ZXJ5KCB0aGlzICk7XHJcblx0XHR2YXIgaXNfbXVsdGlfc2VsZWN0aW9uID0gZmFsc2U7XHJcblx0XHQvLyB2YXIgaW5zZXJ0X2ZpZWxkX3NlcGFyYXRvciA9ICcsJztcclxuXHJcblx0XHQvLyBTdG9wIHRoZSBhbmNob3IncyBkZWZhdWx0IGJlaGF2aW9yXHJcblx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cclxuXHRcdC8vIElmIGZyYW1lIGV4aXN0IGNsb3NlIGl0XHJcblx0XHRpZiAoIHdwYmNfbWVkaWFfZmlsZV9mcmFtZSApIHtcclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLmNsb3NlKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIENyZWF0ZSBNZWRpYSBGcmFtZVxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZSA9IHdwLm1lZGlhLmZyYW1lcy53cGJjX21lZGlhX3VwbG9hZF9maWxlX2ZyYW1lID0gd3AubWVkaWEoIHtcdFx0XHRcdFx0XHRcdFx0Ly8gQ2hlY2sgIGhlcmUgLi4vd3AtaW5jbHVkZXMvanMvbWVkaWEtdmlld3MuanNcclxuXHRcdFx0Ly8gU2V0IHRoZSB0aXRsZSBvZiB0aGUgbW9kYWwuXHJcblx0XHRcdHRpdGxlOiBqX2J0bi5kYXRhKCAnbW9kYWxfdGl0bGUnICksXHJcblx0XHRcdGxpYnJhcnk6IHtcclxuXHRcdFx0XHQgLy9cdHR5cGU6ICcnXHJcblx0XHRcdFx0IC8vIHR5cGU6IFsgJ3ZpZGVvJywgJ2ltYWdlJyBdXHJcblx0XHRcdFx0IHR5cGU6IFsgJ2ltYWdlJyBdXHJcblx0XHRcdH0sXHJcblx0XHRcdGJ1dHRvbjoge1xyXG5cdFx0XHRcdHRleHQ6IGpfYnRuLmRhdGEoICdidG5fdGl0bGUnICksXHJcblx0XHRcdH0sXHJcblx0XHRcdG11bHRpcGxlOiBpc19tdWx0aV9zZWxlY3Rpb24sXHJcblx0XHRcdC8vIHN0YXRlczogW1xyXG5cdFx0XHQvLyBcdFx0XHRuZXcgd3AubWVkaWEuY29udHJvbGxlci5MaWJyYXJ5KCB7XHJcblx0XHRcdC8vIFx0XHRcdFx0LypcclxuXHRcdFx0Ly8gXHRcdFx0XHRcdEFkZCB0byAgdGhpcyBsaWJhcmF5IGN1c3RvbSBwb3N0ICBwYXJhbWV0ZXI6ICRfUE9TVFsncXVlcnknXVsgJG1lZGlhX3VwbG9hZGVyX3BhcmFtc1sna2V5J10gXSA9ICRtZWRpYV91cGxvYWRlcl9wYXJhbXNbJ3ZhbHVlJ11cclxuXHRcdFx0Ly8gXHRcdFx0XHRcdFdlIGFyZSBjaGVja2luZyBpbiBmdW5jdG9uIHdwYmNfbWVkaWFfZmlsdGVyX3Bvc3RzX3doZXJlIG1lZGlhIGZpbGVzIHRoYXQgIG9ubHkgIHJlbGF0aXZlIHRvICB0aGlzIG1lZGkgRnJhbWUgb3BlbmluZ1xyXG5cdFx0XHQvLyBcdFx0XHRcdFx0QW5kIGZpbHRlcmluZyBwb3N0cyAoaW4gV0hFUkUpIHJlbGF0aXZlIGN1c3RvbSBwYXRoIHRvICBvdXIgZmlsZXMuXHJcblx0XHRcdC8vIFx0XHRcdFx0XHRlY2hvICd7JyAuICRtZWRpYV91cGxvYWRlcl9wYXJhbXNbJ2tleSddIC4gXCI6ICdcIiAuICRtZWRpYV91cGxvYWRlcl9wYXJhbXNbJ3ZhbHVlJ10gLiBcIicgfVwiO1xyXG5cdFx0XHQvLyBcdFx0XHRcdCovXHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdGxpYnJhcnk6IHdwLm1lZGlhLnF1ZXJ5KCksXHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdG11bHRpcGxlOiBpc19tdWx0aV9zZWxlY3Rpb24sXHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdHRpdGxlOlx0al9idG4uZGF0YSggJ21vZGFsX3RpdGxlJyApLFxyXG5cdFx0XHQvLyBcdFx0XHRcdFx0XHRwcmlvcml0eTogMTUsXHJcblx0XHRcdC8vIFx0XHRcdFx0XHRcdGZpbHRlcmFibGU6ICd1cGxvYWRlZCcsXHJcblx0XHRcdC8vIFx0XHRcdFx0IFx0XHR0eXBlOiBbJ2ltYWdlJ11cclxuXHRcdFx0Ly8gXHRcdFx0XHRcdFx0Ly8gaWRlYWxDb2x1bW5XaWR0aDogMTI1XHJcblx0XHRcdC8vIFx0XHRcdFx0fSApXHJcblx0XHRcdC8vIFx0XHRdXHJcblx0XHR9ICk7XHJcblxyXG5cdFx0LypcclxuXHRcdC8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vXHJcblx0XHQvLyBTZXQgIGN1c3RvbSBwYXJhbWV0ZXJzIGZvciB1cGxvYWRlclx0LT4gICRfUE9TVFsnd3BiY19tZWRpYV90eXBlJ10gLSBjaGVja2luZyBpbiBcInVwbG9hZF9kaXJcIiwgIHdoZW4gZmlsdGVyX3VwbG9hZF9kaXJcclxuXHRcdC8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vXHJcblx0XHR3cGJjX21lZGlhX2ZpbGVfZnJhbWUub24oICdyZWFkeScsIGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLnVwbG9hZGVyLm9wdGlvbnMudXBsb2FkZXIucGFyYW1zID0ge1xyXG5cdFx0XHRcdHR5cGU6ICd3cGJjX21lZGlhX2Rvd25sb2FkJyxcclxuXHRcdFx0XHQ8P3BocFxyXG5cdFx0XHRcdGVjaG8gJG1lZGlhX3VwbG9hZGVyX3BhcmFtc1sna2V5J10gLiBcIjogJ1wiIC4gJG1lZGlhX3VwbG9hZGVyX3BhcmFtc1sndmFsdWUnXSAuIFwiJ1wiO1xyXG5cdFx0XHRcdD8+XHJcblx0XHRcdH07XHJcblx0XHR9ICk7XHJcblx0XHQqL1xyXG5cclxuXHRcdC8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vXHJcblx0XHQvLyBXaGVuIEZpbGUgaGF2ZSBzZWxlY3RlZCwgZG8gdGhpc1xyXG5cdFx0Ly8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy9cclxuXHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggJ3NlbGVjdCcsIGZ1bmN0aW9uICgpIHtcclxuXHJcblx0XHRcdGlmICggISBpc19tdWx0aV9zZWxlY3Rpb24gKSB7IC8vIFNpbmdsZSBmaWxlXHJcblxyXG5cdFx0XHRcdHZhciBhdHRhY2htZW50ID0gd3BiY19tZWRpYV9maWxlX2ZyYW1lLnN0YXRlKCkuZ2V0KCAnc2VsZWN0aW9uJyApLmZpcnN0KCkudG9KU09OKCk7XHJcblxyXG5cdFx0XHRcdC8vIFB1dCBVUkwgb2YgZmlsZSB0byB0ZXh0IGZpZWxkXHJcblx0XHRcdFx0alF1ZXJ5KCAnIycgKyBqX2J0bi5kYXRhKCAndXJsX2ZpZWxkJyApICkudmFsKCBhdHRhY2htZW50LnVybCApO1xyXG5cdFx0XHRcdGpRdWVyeSggJyMnICsgal9idG4uZGF0YSggJ3VybF9maWVsZCcgKSApLnRyaWdnZXIoIFwid3BiY19tZWRpYV91cGxvYWRfdXJsX3NldFwiICk7XHJcblxyXG5cdFx0XHRcdC8val9idG4ucGFyZW50cyggJy53cGJjX2V4dHJhX19leGNlcnB0X2ltZycgKS5maW5kKCAnLnVpX2dyb3VwX190aHVtYm5haWwgLndwYmNfbWVkaWFfdXBsb2FkX2J1dHRvbicgKS5odG1sKCAnPGltZyBzcmM9XCInICsgYXR0YWNobWVudC51cmwgKyAnXCIgY2xhc3M9XCJzZWFyY2hfdGh1bWJuYWlsX2ltZ1wiIC8+JyApO1xyXG5cclxuXHRcdFx0fSBlbHNlIHsgLy8gTXVsdGlwbGUgZmlsZXMuXHJcblxyXG5cdFx0XHRcdHZhciBmaWxlX3BhdGhzID0gJyc7XHJcblx0XHRcdFx0Ly8gdmFyIGNzdl9kYXRhX2xpbmUgPSAnJztcclxuXHRcdFx0XHR3cGJjX21lZGlhX2ZpbGVfZnJhbWUuc3RhdGUoKS5nZXQoICdzZWxlY3Rpb24nICkubWFwKCBmdW5jdGlvbiAoIGF0dGFjaG1lbnQgKXtcclxuXHJcblx0XHRcdFx0XHRcdC8qXHJcblx0XHRcdFx0XHRcdFx0Ly8gUmVxdWVzdCAgbmV3IGRhdGFcclxuXHRcdFx0XHRcdFx0XHRhdHRhY2htZW50LmZldGNoKCkudGhlbihmdW5jdGlvbiAoZGF0YSkge1xyXG5cdFx0XHRcdFx0XHRcdFx0Y29uc29sZS5sb2coZGF0YSk7XHJcblx0XHRcdFx0XHRcdFx0XHQvLyBwcmVsb2FkaW5nIGZpbmlzaGVkIGFmdGVyIHRoaXMgeW91IGNhbiB1c2UgeW91ciBhdHRhY2htZW50IG5vcm1hbGx5XHJcblx0XHRcdFx0XHRcdFx0XHQvLyB3cC5tZWRpYS5hdHRhY2htZW50KCBhdHRhY2htZW50LmlkICkuZ2V0KCd1cmwnKTtcclxuXHRcdFx0XHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcdFx0Ki9cclxuXHJcblx0XHRcdFx0XHRhdHRhY2htZW50ID0gYXR0YWNobWVudC50b0pTT04oKTtcclxuXHJcblx0XHRcdFx0XHRcdC8qXHJcblx0XHRcdFx0XHRcdFx0aWYgKCBhdHRhY2htZW50LnVybCApIHtcclxuXHRcdFx0XHRcdFx0XHRcdC8vIEluc2VydCBpbmZvIGZyb20gc2VsZWN0ZWQgZmlsZXNcclxuXHRcdFx0XHRcdFx0XHRcdGNzdl9kYXRhX2xpbmUgPSBhdHRhY2htZW50LmlkICsgaW5zZXJ0X2ZpZWxkX3NlcGFyYXRvciArIGF0dGFjaG1lbnQudGl0bGUgICsgaW5zZXJ0X2ZpZWxkX3NlcGFyYXRvciArIGF0dGFjaG1lbnQud3BiY19tZWRpYV92ZXJzaW9uX251bSAgKyBpbnNlcnRfZmllbGRfc2VwYXJhdG9yICsgYXR0YWNobWVudC5kZXNjcmlwdGlvbiArIGluc2VydF9maWVsZF9zZXBhcmF0b3IgKyBhdHRhY2htZW50LnVybFxyXG5cdFx0XHRcdFx0XHRcdFx0ZmlsZV9wYXRocyA9IGZpbGVfcGF0aHMgPyBmaWxlX3BhdGhzICsgXCJcXG5cIiArIGNzdl9kYXRhX2xpbmUgOiBjc3ZfZGF0YV9saW5lO1xyXG5cdFx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdFx0Ki9cclxuXHRcdFx0XHRcdCBmaWxlX3BhdGhzID0gZmlsZV9wYXRocyA/IGZpbGVfcGF0aHMgKyBcIlxcblwiICsgYXR0YWNobWVudC51cmwgOiBhdHRhY2htZW50LnVybDtcclxuXHRcdFx0XHR9KTtcclxuXHJcblx0XHRcdFx0alF1ZXJ5KCAnIycgKyBqX2J0bi5kYXRhKCAndXJsX2ZpZWxkJyApICkudmFsKCBmaWxlX3BhdGhzICk7XHJcblx0XHRcdFx0alF1ZXJ5KCAnIycgKyBqX2J0bi5kYXRhKCAndXJsX2ZpZWxkJyApICkudHJpZ2dlciggXCJ3cGJjX21lZGlhX3VwbG9hZF91cmxfc2V0XCIgKTtcclxuXHRcdFx0fVxyXG5cclxuXHRcdH0gKTtcclxuXHJcblx0XHQvKlxyXG5cdFx0XHQvLyBGaXJlcyB3aGVuIGEgc3RhdGUgYWN0aXZhdGVzLlxyXG5cdFx0XHR3cGJjX21lZGlhX2ZpbGVfZnJhbWUub24oICdhY3RpdmF0ZScsIGZ1bmN0aW9uKCkgeyBhbGVydCgnYWN0aXZhdGUnKTsgfSApO1xyXG5cclxuXHRcdFx0Ly8gRmlyZXMgYWZ0ZXIgdGhlIGZyYW1lIG1hcmt1cCBoYXMgYmVlbiBidWlsdCwgYnV0IG5vdCBhcHBlbmRlZCB0byB0aGUgRE9NLlxyXG5cdFx0XHQvLyBAc2VlIHdwLm1lZGlhLnZpZXcuTW9kYWwuYXR0YWNoKClcclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLm9uKCAncmVhZHknLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3JlYWR5Jyk7IH0gKTtcclxuXHJcblx0XHRcdC8vIEZpcmVzIHdoZW4gdGhlIGZyYW1lJ3MgJGVsIGlzIGFwcGVuZGVkIHRvIGl0cyBET00gY29udGFpbmVyLlxyXG5cdFx0XHQvLyBAc2VlIG1lZGlhLnZpZXcuTW9kYWwuYXR0YWNoKClcclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLm9uKCAnYXR0YWNoJywgZnVuY3Rpb24oKSB7IGFsZXJ0KCdhdHRhY2gnKTsgfSApO1xyXG5cclxuXHRcdFx0Ly8gRmlyZXMgd2hlbiB0aGUgbW9kYWwgb3BlbnMgKGJlY29tZXMgdmlzaWJsZSkuXHJcblx0XHRcdC8vIEBzZWUgbWVkaWEudmlldy5Nb2RhbC5vcGVuKClcclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLm9uKCAnb3BlbicsIGZ1bmN0aW9uKCkgeyBhbGVydCgnb3BlbicpOyB9ICk7XHJcblxyXG5cdFx0XHQvLyBGaXJlcyB3aGVuIHRoZSBtb2RhbCBjbG9zZXMgdmlhIHRoZSBlc2NhcGUga2V5LlxyXG5cdFx0XHQvLyBAc2VlIG1lZGlhLnZpZXcuTW9kYWwuY2xvc2UoKVxyXG5cdFx0XHR3cGJjX21lZGlhX2ZpbGVfZnJhbWUub24oICdlc2NhcGUnLCBmdW5jdGlvbigpIHsgYWxlcnQoJ2VzY2FwZScpOyB9ICk7XHJcblxyXG5cdFx0XHQvLyBGaXJlcyB3aGVuIHRoZSBtb2RhbCBjbG9zZXMuXHJcblx0XHRcdC8vIEBzZWUgbWVkaWEudmlldy5Nb2RhbC5jbG9zZSgpXHJcblx0XHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggJ2Nsb3NlJywgZnVuY3Rpb24oKSB7IGFsZXJ0KCdjbG9zZScpOyB9ICk7XHJcblxyXG5cdFx0XHQvLyBGaXJlcyB3aGVuIGEgdXNlciBoYXMgc2VsZWN0ZWQgYXR0YWNobWVudChzKSBhbmQgY2xpY2tlZCB0aGUgc2VsZWN0IGJ1dHRvbi5cclxuXHRcdFx0Ly8gQHNlZSBtZWRpYS52aWV3Lk1lZGlhRnJhbWUuUG9zdC5tYWluSW5zZXJ0VG9vbGJhcigpXHJcblx0XHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggJ3NlbGVjdCcsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdHZhciBzZWxlY3Rpb25Db2xsZWN0aW9uID0gd3BiY19tZWRpYV9maWxlX2ZyYW1lLnN0YXRlKCkuZ2V0KCdzZWxlY3QnKTtcclxuXHRcdFx0fSApO1xyXG5cclxuXHRcdFx0Ly8gRmlyZXMgd2hlbiBhIG1vZGUgaXMgZGVhY3RpdmF0ZWQgb24gYSByZWdpb24geyAnbWVudScgfCB0aXRsZSB8IGNvbnRlbnQgfCB0b29sYmFyIHwgcm91dGVyIH1cclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLm9uKCAnY29udGVudDpkZWFjdGl2YXRlJywgZnVuY3Rpb24oKSB7IGFsZXJ0KCd7cmVnaW9ufTpkZWFjdGl2YXRlJyk7IH0gKTtcclxuXHRcdFx0Ly8gYW5kIGEgbW9yZSBzcGVjaWZpYyBldmVudCBpbmNsdWRpbmcgdGhlIG1vZGUuXHJcblx0XHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggJ2NvbnRlbnQ6ZGVhY3RpdmF0ZTp7bW9kZX0nLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3tyZWdpb259OmRlYWN0aXZhdGV7bW9kZX0nKTsgfSApO1xyXG5cclxuXHRcdFx0Ly8gRmlyZXMgd2hlbiBhIHJlZ2lvbiBpcyByZWFkeSBmb3IgaXRzIHZpZXcgdG8gYmUgY3JlYXRlZC5cclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLm9uKCAnY29udGVudDpjcmVhdGUnLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3tyZWdpb259OmNyZWF0ZScpOyB9ICk7XHJcblx0XHRcdC8vIGFuZCBhIG1vcmUgc3BlY2lmaWMgZXZlbnQgaW5jbHVkaW5nIHRoZSBtb2RlLlxyXG5cdFx0XHR3cGJjX21lZGlhX2ZpbGVfZnJhbWUub24oICdjb250ZW50OmNyZWF0ZTp7bW9kZX0nLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3tyZWdpb259OmNyZWF0ZXttb2RlfScpOyB9ICk7XHJcblxyXG5cdFx0XHQvLyBGaXJlcyB3aGVuIGEgcmVnaW9uIGlzIHJlYWR5IGZvciBpdHMgdmlldyB0byBiZSByZW5kZXJlZC5cclxuXHRcdFx0d3BiY19tZWRpYV9maWxlX2ZyYW1lLm9uKCAnY29udGVudDpyZW5kZXInLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3tyZWdpb259OnJlbmRlcicpOyB9ICk7XHJcblx0XHRcdC8vIGFuZCBhIG1vcmUgc3BlY2lmaWMgZXZlbnQgaW5jbHVkaW5nIHRoZSBtb2RlLlxyXG5cdFx0XHR3cGJjX21lZGlhX2ZpbGVfZnJhbWUub24oICdjb250ZW50OnJlbmRlcjp7bW9kZX0nLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3tyZWdpb259OnJlbmRlcnttb2RlfScpOyB9ICk7XHJcblxyXG5cdFx0XHQvLyBGaXJlcyB3aGVuIGEgbmV3IG1vZGUgaXMgYWN0aXZhdGVkIChhZnRlciBpdCBoYXMgYmVlbiByZW5kZXJlZCkgb24gYSByZWdpb24uXHJcblx0XHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggJ2NvbnRlbnQ6YWN0aXZhdGUnLCBmdW5jdGlvbigpIHsgYWxlcnQoJ3tyZWdpb259OmFjdGl2YXRlJyk7IH0gKTtcclxuXHRcdFx0Ly8gYW5kIGEgbW9yZSBzcGVjaWZpYyBldmVudCBpbmNsdWRpbmcgdGhlIG1vZGUuXHJcblx0XHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggJ2NvbnRlbnQ6YWN0aXZhdGU6e21vZGV9JywgZnVuY3Rpb24oKSB7IGFsZXJ0KCd7cmVnaW9ufTphY3RpdmF0ZXttb2RlfScpOyB9ICk7XHJcblxyXG5cdFx0XHQvLyBHZXQgYW4gb2JqZWN0IHJlcHJlc2VudGluZyB0aGUgY3VycmVudCBzdGF0ZS5cclxuXHRcdFx0Ly93cGJjX21lZGlhX2ZpbGVfZnJhbWUuc3RhdGUoKTtcclxuXHJcblx0XHRcdC8vIEdldCBhbiBvYmplY3QgcmVwcmVzZW50aW5nIHRoZSBwcmV2aW91cyBzdGF0ZS5cclxuXHRcdFx0Ly93cGJjX21lZGlhX2ZpbGVfZnJhbWUubGFzdFN0YXRlKCk7XHJcblx0XHQqL1xyXG5cdFx0LypcclxuXHRcdFx0Ly8gRGVidWdlIGFsbCBldmVudHMgZnJvbSAgbWVkaWEgRnJhbWUhXHJcblx0XHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vbiggXCJhbGxcIiwgZnVuY3Rpb24gKCBldmVudE5hbWUgKXtcclxuXHRcdFx0XHRjb25zb2xlLmxvZyggJ0ZyYW1lIEV2ZW50OiAnICsgZXZlbnROYW1lICk7XHJcblx0XHRcdH0gKTtcclxuXHJcblx0XHRcdC8vIERlYnVnZSBhbGwgZXZlbnRzIGZyb20gIG1lZGlhIEZyYW1lIVxyXG5cdFx0XHR3cC5tZWRpYS5tb2RlbC5BdHRhY2htZW50LmdldCggXCJjb2xsZWN0aW9uXCIgKS5jb2xsZWN0aW9uLm9uKCBcImFsbFwiLCBmdW5jdGlvbiAoIGV2ZW50TmFtZSApe1xyXG5cdFx0XHRcdGNvbnNvbGUubG9nKCAnW0NvbGxlY3Rpb25dIEV2ZW50OiAnICsgZXZlbnROYW1lICk7XHJcblx0XHRcdH0gKTtcclxuXHRcdFx0d3AubWVkaWEubW9kZWwuQXR0YWNobWVudC5nZXQoIFwibW9kZWxzXCIgKS5jb2xsZWN0aW9uLm9uKCBcImFsbFwiLCBmdW5jdGlvbiAoIGV2ZW50TmFtZSApe1xyXG5cdFx0XHRcdGNvbnNvbGUubG9nKCAnW21vZGVsc10gRXZlbnQ6ICcgKyBldmVudE5hbWUgKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0XHR3cC5tZWRpYS5tb2RlbC5BdHRhY2htZW50LmdldCggXCJ2aWV3c1wiICkuY29sbGVjdGlvbi5vbiggXCJhbGxcIiwgZnVuY3Rpb24gKCBldmVudE5hbWUgKXtcclxuXHRcdFx0XHRjb25zb2xlLmxvZyggJ1t2aWV3c10gRXZlbnQ6ICcgKyBldmVudE5hbWUgKTtcclxuXHRcdFx0fSApO1xyXG5cdFx0Ki9cclxuXHJcblx0XHQvLyBPcGVuIHRoZSBtb2RhbC5cclxuXHRcdHdwYmNfbWVkaWFfZmlsZV9mcmFtZS5vcGVuKCk7XHJcblx0fSk7XHJcbn0pOyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQSxJQUFJQSxxQkFBcUI7QUFFekJDLE1BQU0sQ0FBRUMsUUFBUyxDQUFDLENBQUNDLEtBQUssQ0FBRSxZQUFXO0VBQ3BDOztFQUVBRixNQUFNLENBQUUsMkJBQTRCLENBQUMsQ0FBQ0csRUFBRSxDQUFFLE9BQU8sRUFBRSxVQUFVQyxLQUFLLEVBQUc7SUFFcEUsSUFBSUMsS0FBSyxHQUFHTCxNQUFNLENBQUUsSUFBSyxDQUFDO0lBQzFCLElBQUlNLGtCQUFrQixHQUFHLEtBQUs7SUFDOUI7O0lBRUE7SUFDQUYsS0FBSyxDQUFDRyxjQUFjLENBQUMsQ0FBQzs7SUFFdEI7SUFDQSxJQUFLUixxQkFBcUIsRUFBRztNQUM1QkEscUJBQXFCLENBQUNT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
