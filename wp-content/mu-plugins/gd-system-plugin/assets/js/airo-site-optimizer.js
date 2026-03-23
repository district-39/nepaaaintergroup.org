const dataLayer = window._expDataLayer || [];

jQuery( document ).ready( function( $ ) {
    $( '#optimize-my-site-airo-banner' ).on( 'click', function( event ) {
        event.preventDefault();

        const linkUrl = $(this).attr('href');

        dataLayer.push({
            schema: 'add_event',
            version: 'v1',
            data: {
                eid: `site-optimizer.admin-dashboard.banner.link.click`,
                type: 'click',
            }
        });

        window.open(linkUrl, '_blank', 'noopener');
    });
} );