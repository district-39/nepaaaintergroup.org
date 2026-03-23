; (function ($) {

	function isVideo(url) {
		var regex = /^(https?:\/\/)?((www\.)?youtube\.com|vimeo\.com|dailymotion\.com|wistia\.com|hubspot\.com|sproutvideo\.com|hippovideo\.io|brightcove\.com|spotlightr\.com|vidyard\.com)\/.+$/i;
		return regex.test(url);
	}

	function SP_EAP_AccordionInit() {
		$('body').find('.sp-easy-accordion').each(function () {
			var accordion_id = $(this).attr('id');
			var _this = $(this);
			var ea_active = _this.data('ea-active');
			var ea_mode = _this.data('ea-mode');
			var preloader = _this.data('preloader');
			var scroll_active_item = _this.data('scroll-active-item'),
				offset_to_scroll = _this.data('offset-to-scroll');
			if (ea_mode === 'vertical') {
				if (ea_active === 'ea-click') {
					$("#" + accordion_id).each(function () {
						$("#" + accordion_id + " > .ea-card > .ea-header").on("click", function () {
							$("#" + accordion_id + " > .ea-card > .sp-collapse").on("hide.bs.spcollapse", function (e) {
								$(this).parent(".ea-card").removeClass("ea-expand");
								$(this).siblings(".ea-header").find(".ea-expand-icon").addClass('eap-icon-ea-expand-plus').removeClass('eap-icon-ea-expand-minus');
								e.stopPropagation();
							})
							$("#" + accordion_id + " > .ea-card > .sp-collapse").on("show.bs.spcollapse", function (e) {
								$(this).parent(".ea-card").addClass("ea-expand");
								$(this).siblings(".ea-header").find(".ea-expand-icon").addClass('eap-icon-ea-expand-minus').removeClass('eap-icon-ea-expand-plus');
								e.stopPropagation();
							})
						});
					});
					$("#" + accordion_id + " > .ea-card .ea-header a").on('click', function (event) {
						event.preventDefault();
					});
				}
				if (ea_active === 'ea-hover') {
					$("#" + accordion_id + " > .ea-card").mouseover(function () {
						$(this).children(".sp-collapse").spcollapse("show");
						if ($('.sp-collapse.show').length > 1) {
							$(this).children(".sp-collapse").spcollapse("hide");
						}
					});
					$("#" + accordion_id + " > .ea-card > .sp-collapse").on("hide.bs.spcollapse", function (e) {
						$(this).parent(".ea-card").removeClass("ea-expand");
						$(this).siblings(".ea-header").find(".ea-expand-icon").addClass('eap-icon-ea-expand-plus').removeClass('eap-icon-ea-expand-minus');
						e.stopPropagation();
					})
					$("#" + accordion_id + " > .ea-card > .sp-collapse").on("show.bs.spcollapse", function (e) {
						$(this).parent(".ea-card").addClass("ea-expand");
						$(this).siblings(".ea-header").find(".ea-expand-icon").addClass('eap-icon-ea-expand-minus').removeClass('eap-icon-ea-expand-plus');
						e.stopPropagation();
					})
				};
			}
			var preloader_id = $('#' + accordion_id + ' .accordion-preloader').attr('id');
			if (preloader_id) {
				$(document).ready(function () {
					$('#' + preloader_id).animate({ opacity: 0, }, 500).remove();
					$('#' + accordion_id).find('.ea-card').animate({ opacity: 1 }, 500);
				});
			}

			// Scroll to active item scripts.
			if (scroll_active_item) {
				$("#" + accordion_id + ' .sp-collapse').on('show.bs.spcollapse', function (e) {
					var $panel = $(this).closest('.ea-card');
					setTimeout(function (e) {
						$('html,body').animate({
							scrollTop: $panel.offset().top - offset_to_scroll
						}, 500);
					}, 500)
				});
			}
		});

		$('.sp-easy-accordion iframe:not(.wp-ea-iframe,.skip)').each(function () {
			var url = $(this).attr('src');
			if (isVideo(url)) {
				$(this).addClass('wp-ea-iframe').wrap("<div class='wp-easy-accordion-iframe-container'></div>");
			}
		});
	}

	// Initialize the carousel when the document is ready
	$(document).ready(function () {
		SP_EAP_AccordionInit();
	});

	// Register the handler with Elementor's frontend event.
	$(window).on('elementor/frontend/init', function () {
		// For General Widget.
		elementorFrontend.hooks.addAction('frontend/element_ready/sp_easy_accordion_pro_shortcode.default', SP_EAP_AccordionInit);
	});

})(jQuery);