jQuery( document ).ready( function ($) {

	/**
	 * Ajax save.
	 */
	$( document ).on( 'click', '.elementor-save-skin', function () {


		const $button         = $( this );
		const $controlWrapper = $button.closest( '.elementor-control-content' );
		const $select         = $controlWrapper.find( '.elementor-control-input-wrapper select' );
		const selectedSkin    = $select.val();

		const html_id = $button.attr( 'id' );
		const nonce   = WPBC_Ajax.nonce;

		// UI feedback: processing state.
		$button.attr( 'saved_title', $button.text() );
		$button.text( $button.attr( 'processing_title' ) + '...' );
		$button.prop( 'disabled', true );

		$.ajax(
			{
				url: WPBC_Ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'wpbc_save_calendar_skin',
					skin: selectedSkin,
					_ajax_nonce: nonce,
					html_id: html_id,
				},
				success: function (response) {

					// Restore button state.
					if ( html_id ) {
						const $btn = $( document.getElementById( html_id ) );
						$btn.prop( 'disabled', false ).text( $btn.attr( 'saved_title' ) ).removeAttr( 'saved_title' );
					}

					if ( ! response.success ) {
						console.warn( 'Skin save failed:', response.data || 'Unknown error' );
						return;
					}

					const savedSkinUrl = response.data?.url_full;
					console.log( 'Skin saved successfully:', savedSkinUrl );
				},
				error  : function () {

					console.error( 'AJAX error occurred during skin save.' );
					// Optional: restore button UI.
					if ( html_id ) {
						const $btn = $( '#' + html_id );
						$btn.prop( 'disabled', false ).text( $btn.attr( 'saved_title' ) ).removeAttr( 'saved_title' );
					}
				}
			}
		);
	} );

	$( document ).on( 'click', '.wpbc-reset-dates-button', function () {
		// Clear input values directly
		$( 'input[data-setting="wpbc_booking_calendar_dates_start"]' ).val( '' ).trigger( 'input' );
		$( 'input[data-setting="wpbc_booking_calendar_dates_end"]' ).val( '' ).trigger( 'input' );
		$( 'input[data-setting="wpbc_booking_calendar_startmonth"]' ).val( '' ).trigger( 'input' );
	} );
} );
