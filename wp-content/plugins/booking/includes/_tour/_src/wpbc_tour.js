(function ($) {
	$( function () {

		// if Shepherd is undefined, exit.
		if ( ! window.Shepherd ) {
			return;
		}

		var button_classes    = 'button button-primary';
		var plugins_page_tour = window.wpbc_plugins_page_tour = new Shepherd.Tour();
		var main_tour         = window.wpbc_main_tour = new Shepherd.Tour();

		// Set up the defaults for each step
		main_tour.options.defaults = plugins_page_tour.options.defaults = {
			classes: 'wpbc_tour_theme202408 wpbc_tour_main',
			showCancelLink: true,
			scrollTo: false,
			tetherOptions: {
				constraints: [
					{
						to: 'scrollParent',
						attachment: 'together',
						pin: false
					}
				]
			}
		};
		
		/*
			Plugins page
		*/

		main_tour.addStep( 'intro', {
			title: wpbc_tour_i18n.plugins_page.title,
			text: wpbc_tour_i18n.plugins_page.text,
			attachTo: '.wpbc_plugins_links__start_tour top',
			buttons: [
				{
					classes: button_classes,
					text: wpbc_tour_i18n.plugins_page.button.text,
					action: function() {
						window.location = wpbc_tour_i18n.plugins_page.button.url;
					}
				}
			],
			tetherOptions: {
				constraints: [
					{
						to: 'scrollParent',
						attachment: 'together',
						pin: false
					}
				],
				offset: '20px 0'
			},
			when: {
				show: function() {
					$('body').addClass('plugins_page_highlight_wpbc');
					var popup = $(this.el);
					var target = $(this.tether.target);
					$('body, html').animate({
						scrollTop: popup.offset().top - 50
					}, 500, function() {
						window.scrollTo(0, popup.offset().top - 50);
					});
				},
				hide: function() {
					$('body').removeClass('plugins_page_highlight_wpbc');
				}
			}
		});


		/*
			Main Tour steps
		*/

		// 1. Your first backup
		main_tour.addStep( 'main_tour_start', {
			title: wpbc_tour_i18n.setup_page.title,
			text:  wpbc_tour_i18n.setup_page.text,
			//attachTo: '.wpbc_page_top__wizard_button_content bottom',
			attachTo: { element: jQuery( '#toplevel_page_wpbc ul li:nth-last-child(2)').get(0), on: 'right'},
			buttons: [
				{
					classes: 'wpbc_tour_end',
					text: wpbc_tour_i18n.button_end_tour.text,
					action: main_tour.cancel
				},
				{
					classes: button_classes,
					text: wpbc_tour_i18n.button_next.text,
					action: function() {
						//jQuery('.wpbc_page_top__wizard_button_content .button').trigger('click');
						jQuery( '#toplevel_page_wpbc ul li:nth-last-child(2) a').get(0).click();
					}
				}
			],
			tetherOptions: {
				constraints: [
					{
						  to: 'window',
      					  attachment: 'together'
					}
				],
				offset: '0 0'
			}
		});

	});

})(jQuery);

jQuery(document).ready(function(){

	setTimeout(function(){
		if (jQuery('.wpbc_plugins_links__start_tour').length){
			wpbc_main_tour.start();
		}
		if (jQuery('.wpbc_page_top__wizard_button_content').length){
			wpbc_main_tour.show('main_tour_start');
		}
	},1000)
});
