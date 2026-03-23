
function wpbc_flextimeline_nav( timeline_obj, nav_step ){

    jQuery( ".wpbc_timeline_front_end" ).trigger( "timeline_nav" , [ timeline_obj, nav_step ] );        // FixIn: 7.0.1.48.

    // jQuery( '#'+timeline_obj.html_client_id + ' .wpbc_tl_prev,#'+timeline_obj.html_client_id + ' .wpbc_tl_next').remove();
    // jQuery('#'+timeline_obj.html_client_id + ' .wpbc_tl_title').html( '<span class="wpbc_icn_rotate_right wpbc_spin"></span> &nbsp Loading...' );      // '<div style="height:20px;width:100%;text-align:center;margin:15px auto;">Loading ... <img style="vertical-align:middle;box-shadow:none;width:14px;" src="'+_wpbc.get_other_param( 'url_plugin' )+'/assets/img/ajax-loader.gif"><//div>'

    jQuery( '#'+timeline_obj.html_client_id + ' .flex_tl_prev,#'+timeline_obj.html_client_id + ' .flex_tl_next').remove();
    jQuery('#'+timeline_obj.html_client_id + ' .flex_tl_title').html( '<span class="wpbc_icn_rotate_right wpbc_spin"></span> &nbsp Loading...' );      // '<div style="height:20px;width:100%;text-align:center;margin:15px auto;">Loading ... <img style="vertical-align:middle;box-shadow:none;width:14px;" src="'+_wpbc.get_other_param( 'url_plugin' )+'/assets/img/ajax-loader.gif"><//div>'


//Deprecated: FixIn: 9.0.1.1.1
// if ( 'function' === typeof( jQuery(".popover_click.popover_bottom" ).popover )  )       //FixIn: 7.0.1.2  - 2016-12-10
//     jQuery('.popover_click.popover_bottom').popover( 'hide' );                      //Hide all opened popovers

    jQuery.ajax({
        url: wpbc_url_ajax,
        type:'POST',
        success: function ( data, textStatus ){                                 // Note,  here we direct show HTML to TimeLine frame
                    if( textStatus == 'success') {
                        jQuery('#' + timeline_obj.html_client_id + ' .wpbc_timeline_ajax_replace' ).html( data );
                        return true;
                    }
                },
        error:  function ( XMLHttpRequest, textStatus, errorThrown){
                    window.status = 'Ajax Error! Status: ' + textStatus;
                    alert( 'Ajax Error! Status: ' + XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText );
                },
        // beforeSend: someFunction,
        data:{
                action:              'WPBC_FLEXTIMELINE_NAV',
                timeline_obj:        timeline_obj,
                nav_step:            nav_step,
                wpdev_active_locale: _wpbc.get_other_param( 'locale_active' ),
                wpbc_nonce:          document.getElementById('wpbc_nonce_'+ timeline_obj.html_client_id).value
        }
    });
}

