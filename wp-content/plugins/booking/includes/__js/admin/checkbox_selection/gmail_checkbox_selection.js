/**
 * Checkbox Selection functions for Listing.
 */

/**
 * Selections of several  checkboxes like in gMail with shift :)
 * Need to  have this structure:
 * .wpbc_selectable_table
 *      .wpbc_selectable_head
 *              .check-column
 *                  :checkbox
 *      .wpbc_selectable_body
 *          .wpbc_row
 *              .check-column
 *                  :checkbox
 *      .wpbc_selectable_foot
 *              .check-column
 *                  :checkbox
 */
function wpbc_define_gmail_checkbox_selection( $ ){

	var checks, first, last, checked, sliced, lastClicked = false;

	// Check all checkboxes.
	$( '.wpbc_selectable_body' ).find( '.check-column' ).find( ':checkbox' ).on(
		'click',
		function (e) {
			if ( 'undefined' == e.shiftKey ) {
				return true;
			}
			if ( e.shiftKey ) {
				if ( ! lastClicked ) {
					return true;
				}
				checks  = $( lastClicked ).closest( '.wpbc_selectable_body' ).find( ':checkbox' ).filter( ':visible:enabled' );
				first   = checks.index( lastClicked );
				last    = checks.index( this );
				checked = $( this ).prop( 'checked' );
				if ( 0 < first && 0 < last && first != last ) {
					sliced = (last > first) ? checks.slice( first, last ) : checks.slice( last, first );
					sliced.prop(
						'checked',
						function () {
							if ( $( this ).closest( '.wpbc_row' ).is( ':visible' ) ) {
								return checked;
							}
							return false;
						}
					).trigger( 'change' );
				}
			}
			lastClicked = this;

			// toggle "check all" checkboxes.
			var unchecked = $( this ).closest( '.wpbc_selectable_body' ).find( ':checkbox' ).filter( ':visible:enabled' ).not( ':checked' );
			$( this ).closest( '.wpbc_selectable_table' ).children( '.wpbc_selectable_head, .wpbc_selectable_foot' ).find( ':checkbox' ).prop(
				'checked',
				function () {
					return (0 === unchecked.length);
				}
			).trigger( 'change' );

			return true;
		}
	);

	// Head || Foot clicking to  select / deselect ALL.
	$( '.wpbc_selectable_head, .wpbc_selectable_foot' ).find( '.check-column :checkbox' ).on(
		'click',
		function (event) {
			var $this          = $( this ),
				$table         = $this.closest( '.wpbc_selectable_table' ),
				controlChecked = $this.prop( 'checked' ),
				toggle         = event.shiftKey || $this.data( 'wp-toggle' );

			$table.children( '.wpbc_selectable_body' ).filter( ':visible' )
				.find( '.check-column' ).find( ':checkbox' )
				.prop(
					'checked',
					function () {
						if ( $( this ).is( ':hidden,:disabled' ) ) {
							return false;
						}
						if ( toggle ) {
							return ! $( this ).prop( 'checked' );
						} else if ( controlChecked ) {
							return true;
						}
						return false;
					}
				).trigger( 'change' );

			$table.children( '.wpbc_selectable_head,  .wpbc_selectable_foot' ).filter( ':visible' )
				.find( '.check-column' ).find( ':checkbox' )
				.prop(
					'checked',
					function () {
						if ( toggle ) {
							return false;
						} else if ( controlChecked ) {
							return true;
						}
						return false;
					}
				);
		}
	);


	// Visually  show selected border.
	$( '.wpbc_selectable_body' ).find( '.check-column :checkbox' ).on(
		'change',
		function (event) {
			if ( jQuery( this ).is( ':checked' ) ) {
				jQuery( this ).closest( '.wpbc_list_row' ).addClass( 'row_selected_color' );
			} else {
				jQuery( this ).closest( '.wpbc_list_row' ).removeClass( 'row_selected_color' );
			}

			// Disable text selection while pressing 'shift'.
			document.getSelection().removeAllRanges();

			// Show or hide buttons on Actions toolbar  at  Booking Listing  page,  if we have some selected bookings.
			wpbc_show_hide_action_buttons_for_selected_bookings();
		}
	);

	wpbc_show_hide_action_buttons_for_selected_bookings();
}
