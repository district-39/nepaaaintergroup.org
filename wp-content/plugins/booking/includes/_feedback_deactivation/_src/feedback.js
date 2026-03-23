/**
 * global wpbc_plugins_params
 */
jQuery(
	function ($) {
		var wpbc_deactivation_feedback = {
			init: function () {
				this.event_init();
			},
			event_init: function () {
				var _that = this;

				$( document.body ).on(
					"click",
					'tr[data-plugin="booking/wpdev-booking.php"] span.deactivate a',
					function (e) {
						e.preventDefault();
						$( "#wpbc_deactivate-feedback-popup-wrapper" ).addClass( 'active' );
					}
				);

				$( document.body ).on(
					"click",
					'tr[data-plugin="booking-calendar-com/booking-calendar-com.php"] span.deactivate a',
					function (e) {
						e.preventDefault();
						$( '#wpbc_deactivate-feedback-popup-wrapper' ).addClass( 'active' );
					}
				);

				$( "#wpbc_deactivate-feedback-popup-wrapper" ).click(
					function (event) {
						var $target = $( event.target );
						if ( ! $target.closest( ".wpbc_deactivate-feedback-popup-inner" ).length ) {
							$( "#wpbc_deactivate-feedback-popup-wrapper" ).removeClass(
								"active"
							);
						}
					}
				);

				$( "form.wpbc_deactivate-feedback-form" ).on(
					'submit',
					function (e) {
						e.preventDefault();
						_that.send_data( $( this ) );
					}
				);

				$( '#wpbc_deactivate-feedback-popup-wrapper' ).on(
					'click',
					'.close-deactivate-feedback-popup',
					function () {
						$( '#wpbc_deactivate-feedback-popup-wrapper' ).removeClass( 'active' );
					}
				);

				$( 'input.wpbc_deactivate-feedback-input' ).on(
					'click',
					function () {
						if ( jQuery( "form.wpbc_deactivate-feedback-form" ).find( 'input[name="reason_slug"]:checked' ).length ) {
							jQuery( '.wpbc_deactivate-feedback-popup-form-more-details' ).show();
							jQuery( '.wpbc_deactivate-feedback-popup-form-footer .skip' ).hide();
						} else {
							jQuery( '.wpbc_deactivate-feedback-popup-form-more-details' ).hide();
							jQuery( '.wpbc_deactivate-feedback-popup-form-footer .skip' ).show();
						}
					}
				);
			},
			send_data: function (form) {
				var reason_slug = form.find( 'input[name="reason_slug"]:checked' ).map(
					function () {
						return jQuery( this ).val();
					}
				).get().join( ' | ' );

				if ( 0 === jQuery( "form.wpbc_deactivate-feedback-form" ).find( 'input[name="reason_slug"]:checked' ).length ) {
					alert( "Please select at least one option from the list" );
					return;
				}

				if ( form.find( "button.submit" ).hasClass( "button-disabled" ) ) {
					return;
				}

				var reason_text    = '';
				var reason_text_el = form.find(
					'input[name="reason_' + reason_slug + '"]'
				);

				if (reason_text_el.length > 0) {
					reason_text = reason_text_el.val();
				}

				var data = {
					reason_slug: "user_registration_deactivation_notice",
				};

				data["reason_" + reason_slug] = reason_text;

				var values_arr = {};
				jQuery.each(
					form.serializeArray(),
					function (i, field) {
						if ( 'undefined' === typeof (values_arr[field.name]) ) {
							values_arr[field.name] = field.value;
						} else {
							values_arr[field.name] += ' | ' + field.value;
						}
					}
				);

				$.ajax(
					{
						url       : wpbc_plugins_params.ajax_url,
						data      : values_arr,
						type      : "post",
						beforeSend: function () {
							form.find( "button.submit" ).addClass(
								"button-disabled button updating-message"
							);
						},
					}
				).done(
					function () {
						window.location = form.find( "a.skip" ).attr( "href" );
					}
				);
			},
		};

		wpbc_deactivation_feedback.init();
	}
);
