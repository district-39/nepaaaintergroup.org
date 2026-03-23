; (function ($, window, document, undefined) {
	'use strict';

	//
	// Constants
	//
	var SP_EAP = SP_EAP || {};

	SP_EAP.funcs = {};

	SP_EAP.vars = {
		onloaded: false,
		$body: $('body'),
		$window: $(window),
		$document: $(document),
		$form_warning: null,
		is_confirm: false,
		form_modified: false,
		code_themes: [],
		is_rtl: $('body').hasClass('rtl'),
	};

	//
	// Helper Functions
	//
	SP_EAP.helper = {

		//
		// Generate UID
		//
		uid: function (prefix) {
			return (prefix || '') + Math.random().toString(36).substr(2, 9);
		},

		// Quote regular expression characters
		//
		preg_quote: function (str) {
			return (str + '').replace(/(\[|\-|\])/g, "\\$1");
		},

		//
		// Reneme input names
		//
		name_nested_replace: function ($selector, field_id) {

			var checks = [];
			var regex = new RegExp('(' + SP_EAP.helper.preg_quote(field_id) + ')\\[(\\d+)\\]', 'g');

			$selector.find(':radio').each(function () {
				if (this.checked || this.orginal_checked) {
					this.orginal_checked = true;
				}
			});

			$selector.each(function (index) {
				$(this).find(':input').each(function () {
					this.name = this.name.replace(regex, field_id + '[' + index + ']');
					if (this.orginal_checked) {
						this.checked = true;
					}
				});
			});

		},

		//
		// Debounce
		//
		debounce: function (callback, threshold, immediate) {
			var timeout;
			return function () {
				var context = this, args = arguments;
				var later = function () {
					timeout = null;
					if (!immediate) {
						callback.apply(context, args);
					}
				};
				var callNow = (immediate && !timeout);
				clearTimeout(timeout);
				timeout = setTimeout(later, threshold);
				if (callNow) {
					callback.apply(context, args);
				}
			};
		},

		//
		// Get a cookie
		//
		get_cookie: function (name) {

			var e, b, cookie = document.cookie, p = name + '=';

			if (!cookie) {
				return;
			}

			b = cookie.indexOf('; ' + p);

			if (b === -1) {
				b = cookie.indexOf(p);

				if (b !== 0) {
					return null;
				}
			} else {
				b += 2;
			}

			e = cookie.indexOf(';', b);

			if (e === -1) {
				e = cookie.length;
			}

			return decodeURIComponent(cookie.substring(b + p.length, e));

		},

		//
		// Set a cookie
		//
		set_cookie: function (name, value, expires, path, domain, secure) {

			var d = new Date();

			if (typeof (expires) === 'object' && expires.toGMTString) {
				expires = expires.toGMTString();
			} else if (parseInt(expires, 10)) {
				d.setTime(d.getTime() + (parseInt(expires, 10) * 1000));
				expires = d.toGMTString();
			} else {
				expires = '';
			}

			document.cookie = name + '=' + encodeURIComponent(value) +
				(expires ? '; expires=' + expires : '') +
				(path ? '; path=' + path : '') +
				(domain ? '; domain=' + domain : '') +
				(secure ? '; secure' : '');

		},

		//
		// Remove a cookie
		//
		remove_cookie: function (name, path, domain, secure) {
			SP_EAP.helper.set_cookie(name, '', -1000, path, domain, secure);
		},

	};

	//
	// Custom clone for textarea and select clone() bug
	//
	$.fn.eapro_clone = function () {

		var base = $.fn.clone.apply(this, arguments),
			clone = this.find('select').add(this.filter('select')),
			cloned = base.find('select').add(base.filter('select'));

		for (var i = 0; i < clone.length; ++i) {
			for (var j = 0; j < clone[i].options.length; ++j) {

				if (clone[i].options[j].selected === true) {
					cloned[i].options[j].selected = true;
				}

			}
		}

		this.find(':radio').each(function () {
			this.orginal_checked = this.checked;
		});

		return base;

	};

	//
	// Expand All Options
	//
	$.fn.eapro_expand_all = function () {
		return this.each(function () {
			$(this).on('click', function (e) {

				e.preventDefault();
				$('.eapro-wrapper').toggleClass('eapro-show-all');
				$('.eapro-section').eapro_reload_script();
				$(this).find('.fa').toggleClass('fa-indent').toggleClass('fa-outdent');

			});
		});
	};

	//
	// Options Navigation
	//
	$.fn.eapro_nav_options = function () {
		return this.each(function () {

			var $nav = $(this),
				$links = $nav.find('a'),
				$hidden = $nav.closest('.eapro').find('.eapro-section-id'),
				$last_section;

			$(window).on('hashchange eapro.hashchange', function () {

				var hash = window.location.hash.match(new RegExp('tab=([^&]*)'));
				var slug = hash ? hash[1] : $links.first().attr('href').replace('#tab=', '');
				var $link = $('#eapro-tab-link-' + slug);

				if ($link.length > 0) {

					$link.closest('.eapro-tab-depth-0').addClass('eapro-tab-active').siblings().removeClass('eapro-tab-active');
					$links.removeClass('eapro-section-active');
					$link.addClass('eapro-section-active');

					if ($last_section !== undefined) {
						$last_section.hide();
					}

					var $section = $('#eapro-section-' + slug);
					$section.show();
					$section.eapro_reload_script();

					$hidden.val(slug);

					$last_section = $section;

				}

			}).trigger('eapro.hashchange');

		});
	};

	//
	// Metabox Tabs
	//
	$.fn.eapro_nav_metabox = function () {
		return this.each(function () {

			var $nav = $(this),
				$links = $nav.find('a'),
				unique_id = $nav.data('unique'),
				post_id = $('#post_ID').val() || 'global',
				$last_section,
				$last_link;

			$links.on('click', function (e) {

				e.preventDefault();

				var $link = $(this),
					section_id = $link.data('section');

				if ($last_link !== undefined) {
					$last_link.removeClass('eapro-section-active');
				}

				if ($last_section !== undefined) {
					$last_section.hide();
				}

				$link.addClass('eapro-section-active');

				var $section = $('#eapro-section-' + section_id);
				$section.show();
				$section.eapro_reload_script();

				SP_EAP.helper.set_cookie('eapro-last-metabox-tab-' + post_id + '-' + unique_id, section_id);

				$last_section = $section;
				$last_link = $link;

			});

			var get_cookie = SP_EAP.helper.get_cookie('eapro-last-metabox-tab-' + post_id + '-' + unique_id);

			if (get_cookie) {
				$nav.find('a[data-section="' + get_cookie + '"]').trigger('click');
			} else {
				$links.first('a').trigger('click');
			}

		});
	};

	//
	// Metabox Page Templates Listener
	//
	$.fn.eapro_page_templates = function () {
		if (this.length) {

			$(document).on('change', '.editor-page-attributes__template select, #page_template', function () {

				var maybe_value = $(this).val() || 'default';

				$('.eapro-page-templates').removeClass('eapro-show').addClass('eapro-hide');
				$('.eapro-page-' + maybe_value.toLowerCase().replace(/[^a-zA-Z0-9]+/g, '-')).removeClass('eapro-hide').addClass('eapro-show');

			});

		}
	};

	//
	// Metabox Post Formats Listener
	//
	$.fn.eapro_post_formats = function () {
		if (this.length) {

			$(document).on('change', '.editor-post-format select, #formatdiv input[name="post_format"]', function () {

				var maybe_value = $(this).val() || 'default';

				// Fallback for classic editor version
				maybe_value = (maybe_value === '0') ? 'default' : maybe_value;

				$('.eapro-post-formats').removeClass('eapro-show').addClass('eapro-hide');
				$('.eapro-post-format-' + maybe_value).removeClass('eapro-hide').addClass('eapro-show');

			});

		}
	};

	//
	// Search
	//
	$.fn.eapro_search = function () {
		return this.each(function () {

			var $this = $(this),
				$input = $this.find('input');

			$input.on('change keyup', function () {

				var value = $(this).val(),
					$wrapper = $('.eapro-wrapper'),
					$section = $wrapper.find('.eapro-section'),
					$fields = $section.find('> .eapro-field:not(.hidden)'),
					$titles = $fields.find('> .eapro-title, .eapro-search-tags');

				if (value.length > 2) {

					$fields.addClass('eapro-hidden');
					$wrapper.addClass('eapro-search-all');

					$titles.each(function () {

						var $title = $(this);

						if ($title.text().match(new RegExp('.*?' + value + '.*?', 'i'))) {

							var $field = $title.closest('.eapro-field');

							$field.removeClass('eapro-hidden');
							$field.parent().eapro_reload_script();

						}

					});

				} else {

					$fields.removeClass('eapro-hidden');
					$wrapper.removeClass('eapro-search-all');

				}

			});

		});
	};

	//
	// Sticky Header
	//
	$.fn.eapro_sticky = function () {
		return this.each(function () {

			var $this = $(this),
				$window = $(window),
				$inner = $this.find('.eapro-header-inner'),
				padding = parseInt($inner.css('padding-left')) + parseInt($inner.css('padding-right')),
				offset = 32,
				scrollTop = 0,
				lastTop = 0,
				ticking = false,
				stickyUpdate = function () {

					var offsetTop = $this.offset().top,
						stickyTop = Math.max(offset, offsetTop - scrollTop),
						winWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

					if (stickyTop <= offset && winWidth > 782) {
						$inner.css({ width: $this.outerWidth() - padding });
						$this.css({ height: $this.outerHeight() }).addClass('eapro-sticky');
					} else {
						$inner.removeAttr('style');
						$this.removeAttr('style').removeClass('eapro-sticky');
					}

				},
				requestTick = function () {

					if (!ticking) {
						requestAnimationFrame(function () {
							stickyUpdate();
							ticking = false;
						});
					}

					ticking = true;

				},
				onSticky = function () {

					scrollTop = $window.scrollTop();
					requestTick();

				};

			$window.on('scroll resize', onSticky);

			onSticky();

		});
	};

	//
	// Dependency System
	//
	$.fn.eapro_dependency = function () {
		return this.each(function () {

			var $this = $(this),
				ruleset = $.eapro_deps.createRuleset(),
				depends = [],
				is_global = false;

			$this.children('[data-controller]').each(function () {

				var $field = $(this),
					controllers = $field.data('controller').split('|'),
					conditions = $field.data('condition').split('|'),
					values = $field.data('value').toString().split('|'),
					rules = ruleset;

				if ($field.data('depend-global')) {
					is_global = true;
				}

				$.each(controllers, function (index, depend_id) {

					var value = values[index] || '',
						condition = conditions[index] || conditions[0];

					rules = rules.createRule('[data-depend-id="' + depend_id + '"]', condition, value);

					rules.include($field);

					depends.push(depend_id);

				});

			});

			if (depends.length) {

				if (is_global) {
					$.eapro_deps.enable(SP_EAP.vars.$body, ruleset, depends);
				} else {
					$.eapro_deps.enable($this, ruleset, depends);
				}

			}

		});
	};


	//
	// Field: code_editor
	//
	$.fn.eapro_field_code_editor = function () {
		return this.each(function () {
			if (typeof wp === 'undefined' || typeof wp.codeEditor === 'undefined') {
				return;
			}

			var $this = $(this),
				$textarea = $this.find('textarea'),
				settings = $textarea.data('editor') || {};

			// Merge with WP defaults
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				settings
			);

			// Initialize editor
			var editor = wp.codeEditor.initialize($textarea[0], editorSettings);
			//editor.codemirror.setOption('theme', 'monokai');
			// Sync changes back to textarea
			editor.codemirror.on('change', function () {
				$textarea.val(editor.codemirror.getValue()).trigger('change');
			});
		});
	};
	//
	// Field: group
	//
	$.fn.eapro_field_group = function () {
		return this.each(function () {

			var $this = $(this),
				$fieldset = $this.children('.eapro-fieldset'),
				$group = $fieldset.length ? $fieldset : $this,
				$wrapper = $group.children('.eapro-cloneable-wrapper'),
				$hidden = $group.children('.eapro-cloneable-hidden'),
				$max = $group.children('.eapro-cloneable-max'),
				$min = $group.children('.eapro-cloneable-min'),
				field_id = $wrapper.data('field-id'),
				unique_id = $wrapper.data('unique-id'),
				is_number = Boolean(Number($wrapper.data('title-number'))),
				max = parseInt($wrapper.data('max')),
				min = parseInt($wrapper.data('min'));

			// clear accordion arrows if multi-instance
			if ($wrapper.hasClass('ui-accordion')) {
				$wrapper.find('.ui-accordion-header-icon').remove();
			}

			var update_title_numbers = function ($selector) {
				$selector.find('.eapro-cloneable-title-number').each(function (index) {
					$(this).html(($(this).closest('.eapro-cloneable-item').index() + 1) + '.');
				});
			};

			$wrapper.accordion({
				header: '> .eapro-cloneable-item > .eapro-cloneable-title',
				collapsible: true,
				active: false,
				animate: false,
				heightStyle: 'content',
				icons: {
					'header': 'eapro-cloneable-header-icon fa fa-angle-right',
					'activeHeader': 'eapro-cloneable-header-icon fa fa-angle-down'
				},
				activate: function (event, ui) {

					var $panel = ui.newPanel;
					var $header = ui.newHeader;

					if ($panel.length && !$panel.data('opened')) {

						var $fields = $panel.children();
						var $first = $fields.first().find(':input').first();
						var $title = $header.find('.eapro-cloneable-value');

						$first.on('change keyup', function (event) {
							$title.text($first.val());
						});

						$panel.eapro_reload_script();
						$panel.data('opened', true);
						$panel.data('retry', false);

					} else if ($panel.data('retry')) {

						$panel.eapro_reload_script_retry();
						$panel.data('retry', false);

					}

				}
			});

			$wrapper.sortable({
				axis: 'y',
				handle: '.eapro-cloneable-title,.eapro-cloneable-sort',
				helper: 'original',
				cursor: 'move',
				placeholder: 'widget-placeholder',
				start: function (event, ui) {

					$wrapper.accordion({ active: false });
					$wrapper.sortable('refreshPositions');
					ui.item.children('.eapro-cloneable-content').data('retry', true);

				},
				update: function (event, ui) {

					SP_EAP.helper.name_nested_replace($wrapper.children('.eapro-cloneable-item'), field_id);
					$wrapper.eapro_customizer_refresh();

					if (is_number) {
						update_title_numbers($wrapper);
					}

				},
			});

			$group.children('.eapro-cloneable-add').on('click', function (e) {

				e.preventDefault();

				var count = $wrapper.children('.eapro-cloneable-item').length;

				$min.hide();

				if (max && (count + 1) > max) {
					$max.show();
					return;
				}

				var new_field_id = unique_id + field_id + '[' + count + ']';

				var $cloned_item = $hidden.eapro_clone(true);

				$cloned_item.removeClass('eapro-cloneable-hidden');

				$cloned_item.find(':input[name!="_pseudo"]').each(function () {
					this.name = new_field_id + this.name.replace((this.name.startsWith('_nonce') ? '_nonce' : unique_id), '');
				});

				$cloned_item.find('.eapro-data-wrapper').each(function () {
					$(this).attr('data-unique-id', new_field_id);
				});

				$wrapper.append($cloned_item);
				$wrapper.accordion('refresh');
				$wrapper.accordion({ active: count });
				$wrapper.eapro_customizer_refresh();
				$wrapper.eapro_customizer_listen({ closest: true });

				if (is_number) {
					update_title_numbers($wrapper);
				}

			});

			var event_clone = function (e) {

				e.preventDefault();

				var count = $wrapper.children('.eapro-cloneable-item').length;

				$min.hide();

				if (max && (count + 1) > max) {
					$max.show();
					return;
				}

				var $this = $(this),
					$parent = $this.parent().parent(),
					$cloned_helper = $parent.children('.eapro-cloneable-helper').eapro_clone(true),
					$cloned_title = $parent.children('.eapro-cloneable-title').eapro_clone(),
					$cloned_content = $parent.children('.eapro-cloneable-content').eapro_clone(),
					cloned_regex = new RegExp('(' + SP_EAP.helper.preg_quote(field_id) + ')\\[(\\d+)\\]', 'g');

				$cloned_content.find('.eapro-data-wrapper').each(function () {
					var $this = $(this);
					$this.attr('data-unique-id', $this.attr('data-unique-id').replace(cloned_regex, field_id + '[' + ($parent.index() + 1) + ']'));
				});

				var $cloned = $('<div class="eapro-cloneable-item" />');

				$cloned.append($cloned_helper);
				$cloned.append($cloned_title);
				$cloned.append($cloned_content);

				$wrapper.children().eq($parent.index()).after($cloned);

				SP_EAP.helper.name_nested_replace($wrapper.children('.eapro-cloneable-item'), field_id);

				$wrapper.accordion('refresh');
				$wrapper.eapro_customizer_refresh();
				$wrapper.eapro_customizer_listen({ closest: true });

				if (is_number) {
					update_title_numbers($wrapper);
				}

			};

			$wrapper.children('.eapro-cloneable-item').children('.eapro-cloneable-helper').on('click', '.eapro-cloneable-clone', event_clone);
			$group.children('.eapro-cloneable-hidden').children('.eapro-cloneable-helper').on('click', '.eapro-cloneable-clone', event_clone);

			var event_remove = function (e) {

				e.preventDefault();

				var count = $wrapper.children('.eapro-cloneable-item').length;

				$max.hide();
				$min.hide();

				if (min && (count - 1) < min) {
					$min.show();
					return;
				}

				$(this).closest('.eapro-cloneable-item').remove();

				SP_EAP.helper.name_nested_replace($wrapper.children('.eapro-cloneable-item'), field_id);

				$wrapper.eapro_customizer_refresh();

				if (is_number) {
					update_title_numbers($wrapper);
				}

			};

			$wrapper.children('.eapro-cloneable-item').children('.eapro-cloneable-helper').on('click', '.eapro-cloneable-remove', event_remove);
			$group.children('.eapro-cloneable-hidden').children('.eapro-cloneable-helper').on('click', '.eapro-cloneable-remove', event_remove);

		});
	};

	//
	// Field: spinner
	//
	$.fn.eapro_field_spinner = function () {
		return this.each(function () {

			var $this = $(this),
				$input = $this.find('input'),
				$inited = $this.find('.ui-spinner-button'),
				$unit = $input.data('unit');

			if ($inited.length) {
				$inited.remove();
			}

			$input.spinner({
				max: $input.data('max') || 100,
				min: $input.data('min') || 0,
				step: $input.data('step') || 1,
				create: function (event, ui) {
					if ($unit.length) {
						$this.find('.ui-spinner-up').after('<span class="ui-button-text-only eapro--unit">' + $unit + '</span>');
					}
				},
				spin: function (event, ui) {
					$input.val(ui.value).trigger('change');
				}
			});

		});
	};

	//
	// Field: switcher
	//
	$.fn.eapro_field_switcher = function () {
		return this.each(function () {

			var $switcher = $(this).find('.eapro--switcher');

			$switcher.on('click', function () {

				var value = 0;
				var $input = $switcher.find('input');

				if ($switcher.hasClass('eapro--active')) {
					$switcher.removeClass('eapro--active');
				} else {
					value = 1;
					$switcher.addClass('eapro--active');
				}

				$input.val(value).trigger('change');

			});

		});
	};

	//
	// Field: wp_editor
	//
	$.fn.eapro_field_wp_editor = function () {
		return this.each(function () {

			if (typeof window.wp.editor === 'undefined' || typeof window.tinyMCEPreInit === 'undefined' || typeof window.tinyMCEPreInit.mceInit.eapro_wp_editor === 'undefined') {
				return;
			}

			var $this = $(this),
				$editor = $this.find('.eapro-wp-editor'),
				$textarea = $this.find('textarea');

			// If there is wp-editor remove it for avoid dupliated wp-editor conflicts.
			var $has_wp_editor = $this.find('.wp-editor-wrap').length || $this.find('.mce-container').length;

			if ($has_wp_editor) {
				$editor.empty();
				$editor.append($textarea);
				$textarea.css('display', '');
			}

			// Generate a unique id
			var uid = SP_EAP.helper.uid('eapro-editor-');

			$textarea.attr('id', uid);

			// Get default editor settings
			var default_editor_settings = {
				tinymce: window.tinyMCEPreInit.mceInit.eapro_wp_editor,
				quicktags: window.tinyMCEPreInit.qtInit.eapro_wp_editor
			};

			// Get default editor settings
			var field_editor_settings = $editor.data('editor-settings');

			// Add on change event handle
			var editor_on_change = function (editor) {
				editor.on('change', SP_EAP.helper.debounce(function () {
					editor.save();
					$textarea.trigger('change');
				}, 250));
			};

			// Callback for old wp editor
			var wpEditor = wp.oldEditor ? wp.oldEditor : wp.editor;

			if (wpEditor && wpEditor.hasOwnProperty('autop')) {
				wp.editor.autop = wpEditor.autop;
				wp.editor.removep = wpEditor.removep;
				wp.editor.initialize = wpEditor.initialize;
			}

			// Extend editor selector and on change event handler
			default_editor_settings.tinymce = $.extend({}, default_editor_settings.tinymce, { selector: '#' + uid, setup: editor_on_change });

			// Override editor tinymce settings
			if (field_editor_settings.tinymce === false) {
				default_editor_settings.tinymce = false;
				$editor.addClass('eapro-no-tinymce');
			}

			// Override editor quicktags settings
			if (field_editor_settings.quicktags === false) {
				default_editor_settings.quicktags = false;
				$editor.addClass('eapro-no-quicktags');
			}

			// Wait until :visible
			var interval = setInterval(function () {
				if ($this.is(':visible')) {
					window.wp.editor.initialize(uid, default_editor_settings);
					clearInterval(interval);
				}
			});

			// Add Media buttons
			if (field_editor_settings.media_buttons && window.eapro_media_buttons) {

				var $editor_buttons = $editor.find('.wp-media-buttons');

				if ($editor_buttons.length) {

					$editor_buttons.find('.eapro-shortcode-button').data('editor-id', uid);

				} else {

					var $media_buttons = $(window.eapro_media_buttons);

					$media_buttons.find('.eapro-shortcode-button').data('editor-id', uid);

					$editor.prepend($media_buttons);

				}

			}

		});

	};

	//
	// Confirm
	//
	$.fn.eapro_confirm = function () {
		return this.each(function () {
			$(this).on('click', function (e) {

				var confirm_text = $(this).data('confirm') || window.eapro_vars.i18n.confirm;
				var confirm_answer = confirm(confirm_text);

				if (confirm_answer) {
					SP_EAP.vars.is_confirm = true;
				} else {
					e.preventDefault();
					return false;
				}

			});
		});
	};

	$.fn.serializeObject = function () {

		var obj = {};

		$.each(this.serializeArray(), function (i, o) {
			var n = o.name,
				v = o.value;

			obj[n] = obj[n] === undefined ? v
				: $.isArray(obj[n]) ? obj[n].concat(v)
					: [obj[n], v];
		});

		return obj;

	};

	//
	// Options Save
	//
	$.fn.eapro_save = function () {
		return this.each(function () {

			var $this = $(this),
				$buttons = $('.eapro-save'),
				$panel = $('.eapro-options'),
				flooding = false,
				timeout;

			$this.on('click', function (e) {

				if (!flooding) {

					var $text = $this.data('save'),
						$value = $this.val();

					$buttons.attr('value', $text);

					if ($this.hasClass('eapro-save-ajax')) {

						e.preventDefault();

						$panel.addClass('eapro-saving');
						$buttons.prop('disabled', true);

						window.wp.ajax.post('eapro_' + $panel.data('unique') + '_ajax_save', {
							data: $('#eapro-form').serializeJSONSP_EAP(),
							nonce: $('#eapro_options_nonce' + $panel.data('unique')).val(),
						})
							.done(function (response) {

								// clear errors
								$('.eapro-error').remove();

								if (Object.keys(response.errors).length) {

									var error_icon = '<i class="eapro-label-error eapro-error">!</i>';

									$.each(response.errors, function (key, error_message) {

										var $field = $('[data-depend-id="' + key + '"]'),
											$link = $('#eapro-tab-link-' + ($field.closest('.eapro-section').index() + 1)),
											$tab = $link.closest('.eapro-tab-depth-0');

										$field.closest('.eapro-fieldset').append('<p class="eapro-text-error eapro-error">' + error_message + '</p>');

										if (!$link.find('.eapro-error').length) {
											$link.append(error_icon);
										}

										if (!$tab.find('.eapro-arrow .eapro-error').length) {
											$tab.find('.eapro-arrow').append(error_icon);
										}

									});

								}

								$panel.removeClass('eapro-saving');
								$buttons.prop('disabled', false).attr('value', $value);
								flooding = false;

								SP_EAP.vars.form_modified = false;
								SP_EAP.vars.$form_warning.hide();

								clearTimeout(timeout);

								var $result_success = $('.eapro-form-success');
								$result_success.empty().append(response.notice).fadeIn('fast', function () {
									timeout = setTimeout(function () {
										$result_success.fadeOut('fast');
									}, 1000);
								});

							})
							.fail(function (response) {
								alert(response.error);
							});

					} else {

						SP_EAP.vars.form_modified = false;

					}

				}

				flooding = true;

			});

		});
	};

	//
	// Option Framework
	//
	$.fn.eapro_options = function () {
		return this.each(function () {

			var $this = $(this),
				$content = $this.find('.eapro-content'),
				$form_success = $this.find('.eapro-form-success'),
				$form_warning = $this.find('.eapro-form-warning'),
				$save_button = $this.find('.eapro-header .eapro-save');

			SP_EAP.vars.$form_warning = $form_warning;

			// Shows a message white leaving theme options without saving
			if ($form_warning.length) {

				window.onbeforeunload = function () {
					return (SP_EAP.vars.form_modified && SP_EAP.vars.is_confirm === false) ? true : undefined;
				};

				$content.on('change keypress', ':input', function () {
					if (!SP_EAP.vars.form_modified) {
						$form_success.hide();
						$form_warning.fadeIn('fast');
						SP_EAP.vars.form_modified = true;
					}
				});

			}

			if ($form_success.hasClass('eapro-form-show')) {
				setTimeout(function () {
					$form_success.fadeOut('fast');
				}, 1000);
			}

			$(document).on('keydown', function (event) {
				if ((event.ctrlKey || event.metaKey) && event.which === 83) {
					$save_button.trigger('click');
					event.preventDefault();
					return false;
				}
			});

		});
	};

	//
	// Shortcode Framework
	//
	$.fn.eapro_shortcode = function () {

		var base = this;

		base.shortcode_parse = function (serialize, key) {

			var shortcode = '';

			$.each(serialize, function (shortcode_key, shortcode_values) {

				key = (key) ? key : shortcode_key;

				shortcode += '[' + key;

				$.each(shortcode_values, function (shortcode_tag, shortcode_value) {

					if (shortcode_tag === 'content') {

						shortcode += ']';
						shortcode += shortcode_value;
						shortcode += '[/' + key + '';

					} else {

						shortcode += base.shortcode_tags(shortcode_tag, shortcode_value);

					}

				});

				shortcode += ']';

			});

			return shortcode;

		};

		base.shortcode_tags = function (shortcode_tag, shortcode_value) {

			var shortcode = '';

			if (shortcode_value !== '') {

				if (typeof shortcode_value === 'object' && !$.isArray(shortcode_value)) {

					$.each(shortcode_value, function (sub_shortcode_tag, sub_shortcode_value) {

						// sanitize spesific key/value
						switch (sub_shortcode_tag) {

							case 'background-image':
								sub_shortcode_value = (sub_shortcode_value.url) ? sub_shortcode_value.url : '';
								break;

						}

						if (sub_shortcode_value !== '') {
							shortcode += ' ' + sub_shortcode_tag.replace('-', '_') + '="' + sub_shortcode_value.toString() + '"';
						}

					});

				} else {

					shortcode += ' ' + shortcode_tag.replace('-', '_') + '="' + shortcode_value.toString() + '"';

				}

			}

			return shortcode;

		};

		base.insertAtChars = function (_this, currentValue) {

			var obj = (typeof _this[0].name !== 'undefined') ? _this[0] : _this;

			if (obj.value.length && typeof obj.selectionStart !== 'undefined') {
				obj.focus();
				return obj.value.substring(0, obj.selectionStart) + currentValue + obj.value.substring(obj.selectionEnd, obj.value.length);
			} else {
				obj.focus();
				return currentValue;
			}

		};

		base.send_to_editor = function (html, editor_id) {

			var tinymce_editor;

			if (typeof tinymce !== 'undefined') {
				tinymce_editor = tinymce.get(editor_id);
			}

			if (tinymce_editor && !tinymce_editor.isHidden()) {
				tinymce_editor.execCommand('mceInsertContent', false, html);
			} else {
				var $editor = $('#' + editor_id);
				$editor.val(base.insertAtChars($editor, html)).trigger('change');
			}

		};

		return this.each(function () {

			var $modal = $(this),
				$load = $modal.find('.eapro-modal-load'),
				$content = $modal.find('.eapro-modal-content'),
				$insert = $modal.find('.eapro-modal-insert'),
				$loading = $modal.find('.eapro-modal-loading'),
				$select = $modal.find('select'),
				modal_id = $modal.data('modal-id'),
				nonce = $modal.data('nonce'),
				editor_id,
				target_id,
				sc_key,
				sc_name,
				sc_view,
				sc_group,
				$cloned,
				$button;

			$(document).on('click', '.eapro-shortcode-button[data-modal-id="' + modal_id + '"]', function (e) {

				e.preventDefault();

				$button = $(this);
				editor_id = $button.data('editor-id') || false;
				target_id = $button.data('target-id') || false;

				$modal.show();

				// single usage trigger first shortcode
				if ($modal.hasClass('eapro-shortcode-single') && sc_name === undefined) {
					$select.trigger('change');
				}

			});

			$select.on('change', function () {

				var $option = $(this);
				var $selected = $option.find(':selected');

				sc_key = $option.val();
				sc_name = $selected.data('shortcode');
				sc_view = $selected.data('view') || 'normal';
				sc_group = $selected.data('group') || sc_name;

				$load.empty();

				if (sc_key) {

					$loading.show();

					window.wp.ajax.post('eapro-get-shortcode-' + modal_id, {
						shortcode_key: sc_key,
						nonce: nonce
					})
						.done(function (response) {

							$loading.hide();

							var $appended = $(response.content).appendTo($load);

							$insert.parent().removeClass('hidden');

							$cloned = $appended.find('.eapro--repeat-shortcode').eapro_clone();

							$appended.eapro_reload_script();
							$appended.find('.eapro-fields').eapro_reload_script();

						});

				} else {

					$insert.parent().addClass('hidden');

				}

			});

			$insert.on('click', function (e) {

				e.preventDefault();

				if ($insert.prop('disabled') || $insert.attr('disabled')) { return; }

				var shortcode = '';
				var serialize = $modal.find('.eapro-field:not(.hidden)').find(':input:not(.ignore)').serializeObjectSP_EAP();

				switch (sc_view) {

					case 'contents':
						var contentsObj = (sc_name) ? serialize[sc_name] : serialize;
						$.each(contentsObj, function (sc_key, sc_value) {
							var sc_tag = (sc_name) ? sc_name : sc_key;
							shortcode += '[' + sc_tag + ']' + sc_value + '[/' + sc_tag + ']';
						});
						break;

					case 'group':

						shortcode += '[' + sc_name;
						$.each(serialize[sc_name], function (sc_key, sc_value) {
							shortcode += base.shortcode_tags(sc_key, sc_value);
						});
						shortcode += ']';
						shortcode += base.shortcode_parse(serialize[sc_group], sc_group);
						shortcode += '[/' + sc_name + ']';

						break;

					case 'repeater':
						shortcode += base.shortcode_parse(serialize[sc_group], sc_group);
						break;

					default:
						shortcode += base.shortcode_parse(serialize);
						break;

				}

				shortcode = (shortcode === '') ? '[' + sc_name + ']' : shortcode;

				if (editor_id) {

					base.send_to_editor(shortcode, editor_id);

				} else {

					var $textarea = (target_id) ? $(target_id) : $button.parent().find('textarea');
					$textarea.val(base.insertAtChars($textarea, shortcode)).trigger('change');

				}

				$modal.hide();

			});

			$modal.on('click', '.eapro--repeat-button', function (e) {

				e.preventDefault();

				var $repeatable = $modal.find('.eapro--repeatable');
				var $new_clone = $cloned.eapro_clone();
				var $remove_btn = $new_clone.find('.eapro-repeat-remove');

				var $appended = $new_clone.appendTo($repeatable);

				$new_clone.find('.eapro-fields').eapro_reload_script();

				SP_EAP.helper.name_nested_replace($modal.find('.eapro--repeat-shortcode'), sc_group);

				$remove_btn.on('click', function () {

					$new_clone.remove();

					SP_EAP.helper.name_nested_replace($modal.find('.eapro--repeat-shortcode'), sc_group);

				});

			});

			$modal.on('click', '.eapro-modal-close, .eapro-modal-overlay', function () {
				$modal.hide();
			});

		});
	};

	//
	// WP Color Picker
	//
	if (typeof Color === 'function') {

		Color.prototype.toString = function () {

			if (this._alpha < 1) {
				return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
			}

			var hex = parseInt(this._color, 10).toString(16);

			if (this.error) { return ''; }

			if (hex.length < 6) {
				for (var i = 6 - hex.length - 1; i >= 0; i--) {
					hex = '0' + hex;
				}
			}

			return '#' + hex;

		};

	}

	SP_EAP.funcs.parse_color = function (color) {

		var value = color.replace(/\s+/g, ''),
			trans = (value.indexOf('rgba') !== -1) ? parseFloat(value.replace(/^.*,(.+)\)/, '$1') * 100) : 100,
			rgba = (trans < 100) ? true : false;

		return { value: value, transparent: trans, rgba: rgba };

	};

	$.fn.eapro_color = function () {
		return this.each(function () {

			var $input = $(this),
				picker_color = SP_EAP.funcs.parse_color($input.val()),
				palette_color = window.eapro_vars.color_palette.length ? window.eapro_vars.color_palette : true,
				$container;

			// Destroy and Reinit
			if ($input.hasClass('wp-color-picker')) {
				$input.closest('.wp-picker-container').after($input).remove();
			}

			$input.wpColorPicker({
				palettes: palette_color,
				change: function (event, ui) {

					var ui_color_value = ui.color.toString();

					$container.removeClass('eapro--transparent-active');
					$container.find('.eapro--transparent-offset').css('background-color', ui_color_value);
					$input.val(ui_color_value).trigger('change');

				},
				create: function () {

					$container = $input.closest('.wp-picker-container');

					var a8cIris = $input.data('a8cIris'),
						$transparent_wrap = $('<div class="eapro--transparent-wrap">' +
							'<div class="eapro--transparent-slider"></div>' +
							'<div class="eapro--transparent-offset"></div>' +
							'<div class="eapro--transparent-text"></div>' +
							'<div class="eapro--transparent-button">transparent <i class="fa fa-toggle-off"></i></div>' +
							'</div>').appendTo($container.find('.wp-picker-holder')),
						$transparent_slider = $transparent_wrap.find('.eapro--transparent-slider'),
						$transparent_text = $transparent_wrap.find('.eapro--transparent-text'),
						$transparent_offset = $transparent_wrap.find('.eapro--transparent-offset'),
						$transparent_button = $transparent_wrap.find('.eapro--transparent-button');

					if ($input.val() === 'transparent') {
						$container.addClass('eapro--transparent-active');
					}

					$transparent_button.on('click', function () {
						if ($input.val() !== 'transparent') {
							$input.val('transparent').trigger('change').removeClass('iris-error');
							$container.addClass('eapro--transparent-active');
						} else {
							$input.val(a8cIris._color.toString()).trigger('change');
							$container.removeClass('eapro--transparent-active');
						}
					});

					$transparent_slider.slider({
						value: picker_color.transparent,
						step: 1,
						min: 0,
						max: 100,
						slide: function (event, ui) {

							var slide_value = parseFloat(ui.value / 100);
							a8cIris._color._alpha = slide_value;
							$input.wpColorPicker('color', a8cIris._color.toString());
							$transparent_text.text((slide_value === 1 || slide_value === 0 ? '' : slide_value));

						},
						create: function () {

							var slide_value = parseFloat(picker_color.transparent / 100),
								text_value = slide_value < 1 ? slide_value : '';

							$transparent_text.text(text_value);
							$transparent_offset.css('background-color', picker_color.value);

							$container.on('click', '.wp-picker-clear', function () {

								a8cIris._color._alpha = 1;
								$transparent_text.text('');
								$transparent_slider.slider('option', 'value', 100);
								$container.removeClass('eapro--transparent-active');
								$input.trigger('change');

							});

							$container.on('click', '.wp-picker-default', function () {

								var default_color = SP_EAP.funcs.parse_color($input.data('default-color')),
									default_value = parseFloat(default_color.transparent / 100),
									default_text = default_value < 1 ? default_value : '';

								a8cIris._color._alpha = default_value;
								$transparent_text.text(default_text);
								$transparent_slider.slider('option', 'value', default_color.transparent);

							});

						}
					});
				}
			});

		});
	};

	//
	// ChosenJS
	//
	$.fn.eapro_chosen = function () {
		return this.each(function () {

			var $this = $(this),
				$inited = $this.parent().find('.chosen-container'),
				is_sortable = $this.hasClass('eapro-chosen-sortable') || false,
				is_ajax = $this.hasClass('eapro-chosen-ajax') || false,
				is_multiple = $this.attr('multiple') || false,
				set_width = is_multiple ? '100%' : 'auto',
				set_options = $.extend({
					allow_single_deselect: true,
					disable_search_threshold: 10,
					width: set_width,
					no_results_text: window.eapro_vars.i18n.no_results_text,
				}, $this.data('chosen-settings'));

			if ($inited.length) {
				$inited.remove();
			}

			// Chosen ajax
			if (is_ajax) {

				var set_ajax_options = $.extend({
					data: {
						type: 'post',
						nonce: '',
					},
					allow_single_deselect: true,
					disable_search_threshold: -1,
					width: '100%',
					min_length: 2,
					type_delay: 500,
					typing_text: window.eapro_vars.i18n.typing_text,
					searching_text: window.eapro_vars.i18n.searching_text,
					no_results_text: window.eapro_vars.i18n.no_results_text,
				}, $this.data('chosen-settings'));

				$this.SP_EAPAjaxChosen(set_ajax_options);

			} else {

				$this.chosen(set_options);

			}

			// Chosen keep options order
			if (is_multiple) {

				var $hidden_select = $this.parent().find('.eapro-hidden-select');
				var $hidden_value = $hidden_select.val() || [];

				$this.on('change', function (obj, result) {

					if (result && result.selected) {
						$hidden_select.append('<option value="' + result.selected + '" selected="selected">' + result.selected + '</option>');
					} else if (result && result.deselected) {
						$hidden_select.find('option[value="' + result.deselected + '"]').remove();
					}

					// Force customize refresh
					if ($hidden_select.children().length === 0 && window.wp.customize !== undefined) {
						window.wp.customize.control($hidden_select.data('customize-setting-link')).setting.set('');
					}

					$hidden_select.trigger('change');

				});

				// Chosen order abstract
				$this.SP_EAPChosenOrder($hidden_value, true);

			}

			// Chosen sortable
			if (is_sortable) {

				var $chosen_container = $this.parent().find('.chosen-container');
				var $chosen_choices = $chosen_container.find('.chosen-choices');

				$chosen_choices.bind('mousedown', function (event) {
					if ($(event.target).is('span')) {
						event.stopPropagation();
					}
				});

				$chosen_choices.sortable({
					items: 'li:not(.search-field)',
					helper: 'orginal',
					cursor: 'move',
					placeholder: 'search-choice-placeholder',
					start: function (e, ui) {
						ui.placeholder.width(ui.item.innerWidth());
						ui.placeholder.height(ui.item.innerHeight());
					},
					update: function (e, ui) {

						var select_options = '';
						var chosen_object = $this.data('chosen');
						var $prev_select = $this.parent().find('.eapro-hidden-select');

						$chosen_choices.find('.search-choice-close').each(function () {
							var option_array_index = $(this).data('option-array-index');
							$.each(chosen_object.results_data, function (index, data) {
								if (data.array_index === option_array_index) {
									select_options += '<option value="' + data.value + '" selected>' + data.value + '</option>';
								}
							});
						});

						$prev_select.children().remove();
						$prev_select.append(select_options);
						$prev_select.trigger('change');

					}
				});

			}

		});
	};
	//
	// Field: tabbed
	//
	$.fn.eapro_field_tabbed = function () {
		return this.each(function () {
			var $this = $(this),
				$links = $this.find('.eapro-tabbed-nav a'),
				$sections = $this.find('.eapro-tabbed-section');

			$links.on('click', function (e) {
				e.preventDefault();

				var $link = $(this),
					index = $link.index(),
					$section = $sections.eq(index);

				// Store the active tab index in a cookie
				SP_EAP.helper.set_cookie('activeTabIndex', index);

				$link.addClass('eapro-tabbed-active').siblings().removeClass('eapro-tabbed-active');
				$section.eapro_reload_script();
				$section.removeClass('hidden').siblings().addClass('hidden');
			});
			// Check if there's a stored active tab index in the cookie
			var activeTabIndex = SP_EAP.helper.get_cookie('activeTabIndex');
			// Check if the cookie exists
			if (activeTabIndex !== null) {
				$links.eq(activeTabIndex).trigger('click');
			} else {
				$links.first().trigger('click');
			}

		});
	};

	//
	// Helper Checkbox Checker
	//
	$.fn.eapro_checkbox = function () {
		return this.each(function () {

			var $this = $(this),
				$input = $this.find('.eapro--input'),
				$checkbox = $this.find('.eapro--checkbox');

			$checkbox.on('click', function () {
				$input.val(Number($checkbox.prop('checked'))).trigger('change');
			});

		});
	};

	//
	// Siblings
	//
	$.fn.eapro_siblings = function () {
		return this.each(function () {

			var $this = $(this),
				$siblings = $this.find('.eapro--sibling'),
				multiple = $this.data('multiple') || false;

			$siblings.on('click', function () {

				var $sibling = $(this);

				if (multiple) {

					if ($sibling.hasClass('eapro--active')) {
						$sibling.removeClass('eapro--active');
						$sibling.find('input').prop('checked', false).trigger('change');
					} else {
						$sibling.addClass('eapro--active');
						$sibling.find('input').prop('checked', true).trigger('change');
					}

				} else {

					$this.find('input').prop('checked', false);
					$sibling.find('input').prop('checked', true).trigger('change');
					$sibling.addClass('eapro--active').siblings().removeClass('eapro--active');

				}

			});

		});
	};

	//
	// Help Tooltip
	//
	$.fn.eapro_help = function () {
		return this.each(function () {

			var $this = $(this),
				$tooltip,
				$class = '';
			$this.on({
				mouseenter: function (e) {
					e.stopPropagation();
					$('.eapro-tooltip').remove();
					// this class add with the support tooltip.
					if ($this.find('.ea-support').length > 0) {
						$class = 'support-tooltip';
					}
					$tooltip = $('<div class="eapro-tooltip ' + $class + '"></div>').html($this.find('.eapro-help-text').html()).appendTo('body');
					var offset_left = SP_EAP.vars.is_rtl
						? $this.offset().left - $tooltip.outerWidth()
						: $this.offset().left + 24;
					var $top = $this.offset().top - ($tooltip.outerHeight() / 2 - 14);

					// this block used for support tooltip.
					if ($this.find('.ea-support').length > 0) {
						$top = $this.offset().top + 46;
						offset_left = $this.offset().left - 235;
					}
					$tooltip.css({
						top: $top,
						left: offset_left,
					});
				},
				mouseleave: function () {
					if ($tooltip !== undefined) {
						// Check if the cursor is still over the tooltip
						if (!$tooltip.is(':hover')) {
							$tooltip.remove();
						}
					}
				}
			});
			// Event delegation to handle tooltip removal when the cursor leaves the tooltip itself.
			$('body').on('mouseleave', '.eapro-tooltip', function () {
				if ($tooltip !== undefined) {
					$tooltip.remove();
				}
			});
		});
	};

	//
	// Customize Refresh
	//
	$.fn.eapro_customizer_refresh = function () {
		return this.each(function () {

			var $this = $(this),
				$complex = $this.closest('.eapro-customize-complex');

			if ($complex.length) {

				var $input = $complex.find(':input'),
					$unique = $complex.data('unique-id'),
					$option = $complex.data('option-id'),
					obj = $input.serializeObjectSP_EAP(),
					data = (!$.isEmptyObject(obj)) ? obj[$unique][$option] : '',
					control = window.wp.customize.control($unique + '[' + $option + ']');

				// clear the value to force refresh.
				control.setting._value = null;

				control.setting.set(data);

			} else {

				$this.find(':input').first().trigger('change');

			}

			$(document).trigger('eapro-customizer-refresh', $this);

		});
	};

	//
	// Customize Listen Form Elements
	//
	$.fn.eapro_customizer_listen = function (options) {

		var settings = $.extend({
			closest: false,
		}, options);

		return this.each(function () {

			if (window.wp.customize === undefined) { return; }

			var $this = (settings.closest) ? $(this).closest('.eapro-customize-complex') : $(this),
				$input = $this.find(':input'),
				unique_id = $this.data('unique-id'),
				option_id = $this.data('option-id');

			if (unique_id === undefined) { return; }

			$input.on('change keyup', SP_EAP.helper.debounce(function () {

				var obj = $this.find(':input').serializeObjectSP_EAP();
				var val = (!$.isEmptyObject(obj) && obj[unique_id] && obj[unique_id][option_id]) ? obj[unique_id][option_id] : '';

				window.wp.customize.control(unique_id + '[' + option_id + ']').setting.set(val);

			}, 250));

		});
	};

	//
	// Customizer Listener for Reload JS
	//
	$(document).on('expanded', '.control-section', function () {

		var $this = $(this);

		if ($this.hasClass('open') && !$this.data('inited')) {

			var $fields = $this.find('.eapro-customize-field');
			var $complex = $this.find('.eapro-customize-complex');

			if ($fields.length) {
				$this.eapro_dependency();
				$fields.eapro_reload_script({ dependency: false });
				$complex.eapro_customizer_listen();
			}

			$this.data('inited', true);

		}

	});

	//
	// Window on resize
	//
	SP_EAP.vars.$window.on('resize eapro.resize', SP_EAP.helper.debounce(function (event) {

		var window_width = navigator.userAgent.indexOf('AppleWebKit/') > -1 ? SP_EAP.vars.$window.width() : window.innerWidth;

		if (window_width <= 782 && !SP_EAP.vars.onloaded) {
			$('.eapro-section').eapro_reload_script();
			SP_EAP.vars.onloaded = true;
		}

	}, 200)).trigger('eapro.resize');

	//
	// Retry Plugins
	//
	$.fn.eapro_reload_script_retry = function () {
		return this.each(function () {

			var $this = $(this);

			if ($this.data('inited')) {
				$this.children('.eapro-field-wp_editor').eapro_field_wp_editor();
			}

		});
	};

	//
	// Reload Plugins
	//
	$.fn.eapro_reload_script = function (options) {

		var settings = $.extend({
			dependency: true,
		}, options);

		return this.each(function () {

			var $this = $(this);

			// Avoid for conflicts
			if (!$this.data('inited')) {

				// Field plugins
				$this.children('.eapro-field-code_editor').eapro_field_code_editor();
				$this.children('.eapro-field-group').eapro_field_group();

				$this.children('.eapro-field-spinner').eapro_field_spinner();
				$this.children('.eapro-field-switcher').eapro_field_switcher();
				$this.children('.eapro-field-wp_editor').eapro_field_wp_editor();
				$this.children('.eapro-field-tabbed').eapro_field_tabbed();

				// Field colors
				$this.children('.eapro-field-border').find('.eapro-color').eapro_color();
				$this.children('.eapro-field-color').find('.eapro-color').eapro_color();
				$this.children('.eapro-field-color_group').find('.eapro-color').eapro_color();
				$this.children('.eapro-field-typography').find('.eapro-color').eapro_color();

				// Field chosenjs
				$this.children('.eapro-field-select').find('.eapro-chosen').eapro_chosen();

				// Field Checkbox
				$this.children('.eapro-field-checkbox').find('.eapro-checkbox').eapro_checkbox();

				// Field Siblings
				$this.children('.eapro-field-button_set').find('.eapro-siblings').eapro_siblings();
				$this.children('.eapro-field-image_select').find('.eapro-siblings').eapro_siblings();

				// Help Tooptip
				$this.children('.eapro-field').find('.eapro-help').eapro_help();
				$('.eap-admin-header').find('.ea-support-area').eapro_help();

				if (settings.dependency) {
					$this.eapro_dependency();
				}

				$this.data('inited', true);

				$(document).trigger('eapro-reload-script', $this);

			}

		});
	};

	//
	// Document ready and run scripts
	//
	$(document).ready(function () {

		$('.eapro-save').eapro_save();
		$('.eapro-options').eapro_options();
		$('.eapro-sticky-header').eapro_sticky();
		$('.eapro-nav-options').eapro_nav_options();
		$('.eapro-nav-metabox').eapro_nav_metabox();
		$('.eapro-page-templates').eapro_page_templates();
		$('.eapro-post-formats').eapro_post_formats();
		$('.eapro-shortcode').eapro_shortcode();
		$('.eapro-search').eapro_search();
		$('.eapro-confirm').eapro_confirm();
		$('.eapro-expand-all').eapro_expand_all();
		$('.eapro-onload').eapro_reload_script();

	});

	$('.post-type-sp_easy_accordion .column-shortcode input').on('click', function (e) {
		e.preventDefault();
		/* Get the text field */
		var copyText = $(this);
		/* Select the text field */
		copyText.select();
		document.execCommand("copy");
		jQuery(".sp_eap-after-copy-text").animate({
			opacity: 1,
			bottom: 25
		}, 300);
		setTimeout(function () {
			jQuery(".sp_eap-after-copy-text").animate({
				opacity: 0,
			}, 200);
			jQuery(".sp_eap-after-copy-text").animate({
				bottom: 0
			}, 0);
		}, 2000);
	});
	$('.eap-shortcode-selectable').on('click', function (e) {
		e.preventDefault();
		sp_eap_copyToClipboard($(this));
		sp_eap_SelectText($(this));
		$(this).focus().select();
		jQuery(".sp_eap-after-copy-text").animate({
			opacity: 1,
			bottom: 25
		}, 300);
		setTimeout(function () {
			jQuery(".sp_eap-after-copy-text").animate({
				opacity: 0,
			}, 200);
			jQuery(".sp_eap-after-copy-text").animate({
				bottom: 0
			}, 0);
		}, 2000);
	});

	function sp_eap_copyToClipboard(element) {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($(element).text()).select();
		document.execCommand("copy");
		$temp.remove();
	}
	function sp_eap_SelectText(element) {
		var r = document.createRange();
		var w = element.get(0);
		r.selectNodeContents(w);
		var sel = window.getSelection();
		sel.removeAllRanges();
		sel.addRange(r);
	}

	function isValidJSONString(str) {
		try {
			JSON.parse(str);
		} catch (e) {
			return false;
		}
		return true;
	}
	// Accordion export.
	var $export_type = $('.eap_what_export').find('input:checked').val();
	$('.eap_what_export').on('change', function () {
		$export_type = $(this).find('input:checked').val();
	});
	$('.eap_export .eapro--button').on('click', function (event) {
		event.preventDefault();
		var $accordion_ids = $('.eap_post_ids select').val();
		var $ex_nonce = $('#eapro_options_noncesp_eap_tools').val();
		var data = {
			action: 'eap_export_accordions',
			nonce: $ex_nonce,
		};

		// Function to show success/error messages
		function showMessage(message, type = 'success', duration = 3000) {
			var $result = $('.spf-form-result.spf-form-' + type);
			$result.text(message).show();
			setTimeout(function () {
				$result.hide().text('');
			}, duration);
		}

		// Switch between export types
		switch ($export_type) {
			case 'selected_shortcodes':
				if (!$accordion_ids || $accordion_ids.length === 0) {
					showMessage('No shortcodes selected.', 'error');
					break;
				}
				data.eap_ids = $accordion_ids;
				break;

			case 'all_shortcodes':
				data.eap_ids = 'all_shortcodes';
				break;

			case 'all_faqs':
				data.eap_ids = 'all_faqs';
				break;

			default:
				showMessage('No group selected.', 'error');
				return; // Exit early since no valid selection was made
		}
		$.post(ajaxurl, data, function (resp) {
			if (resp) {
				// Convert JSON Array to string.
				if (isValidJSONString(resp)) {
					var json = JSON.stringify(JSON.parse(resp));
				} else {
					var json = JSON.stringify(resp);
				}
				// Convert JSON string to BLOB.
				var blob = new Blob([json], { type: 'application/json' });
				var link = document.createElement('a');
				var eap_time = $.now();
				link.href = window.URL.createObjectURL(blob);
				link.download = "easy-accordion-export-" + eap_time + ".json";
				link.click();
				// Show success message and reset form
				showMessage('Exported successfully!', 'success');
				setTimeout(function () {
					$('.eap_post_ids select').val('').trigger('chosen:updated');
				}, 3000);
			}
		});
	});
	// Accordion import.
	$('.eap_import button.import').on('click', function (event) {
		var $this = $(this),
			button_text = $this.text();
		event.preventDefault();
		var eap_accordions = $('#import').prop('files')[0];

		if ($('#import').val() != '') {
			$this.append('<span class="eapro-page-loading-spinner"><i class="fa fa-spinner" aria-hidden="true"></i></span>');
			$this.css('opacity', '0.7');

			var $im_nonce = $('#eapro_options_noncesp_eap_tools').val();
			var reader = new FileReader();
			reader.readAsText(eap_accordions);
			reader.onload = function (event) {
				var jsonObj = JSON.stringify(event.target.result);
				var allowSpecialTags = $('.eapro-field-checkbox input[name="sp_eap_tools[import_unSanitize]"]').val();
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						accordion: jsonObj,
						action: 'eap_import_accordions',
						nonce: $im_nonce,
						allowSpecialTags,
					},
					success: function (resp) {
						$this.html(button_text).css('opacity', '1');

						$('.eapro-form-result.eapro-form-success').text('Imported successfully!').show();
						setTimeout(function () {
							$('.eapro-form-result.eapro-form-success').hide().text('');
							$('#import').val('');
							if (resp.data === 'sp_accordion_faqs') {
								window.location.replace($('#eap_faqs_link_redirect').attr('href'));
							} else {
								window.location.replace($('#eap_link_redirect').attr('href'));
							}
						}, 2000);
					},
					error: function (error) {
						$('#import').val('');
						$this.html(button_text).css('opacity', '1');
						$('.eapro-form-result.eapro-form-success').addClass('error')
							.text('Something went wrong, please try again!').show();
						setTimeout(function () {
							$('.eapro-form-result.eapro-form-success').hide().text('').removeClass('error');
						}, 2000);
					}
				});
			}
		} else {
			$('.eapro-form-result.eapro-form-success').text('No exported json file chosen.').show();
			setTimeout(function () {
				$('.eapro-form-result.eapro-form-success').hide().text('');
			}, 3000);
		}
	});

	// Live Preview script.
	var preview_box = $('#sp_eap-preview-box');
	var preview_display = $('#sp_eap_live_preview').hide();
	$(document).on('click', '#sp__eap-show-preview:contains(Hide)', function (e) {
		e.preventDefault();
		var _this = $(this);
		_this.html('<i class="fa fa-eye" aria-hidden="true"></i> Show Preview');
		preview_box.html('');
		preview_display.hide();
	});

	$(document).on('click', '#sp__eap-show-preview:not(:contains(Hide))', function (e) {
		e.preventDefault();
		const { collapse, script } = window.eapro_vars.previewJS;
		const data = {
			action: 'sp_eap_preview_meta_box',
			data: $('form#post').serialize(),
			ajax_nonce: $('#eapro_metabox_noncesp_eap_live_preview').val(),
		};
		const $this = $(this);
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			error: (response) => {
				console.error('Error:', response);
				alert('An error occurred while loading the preview.');
			},
			success: (response) => {
				preview_display.show();
				preview_box.html(response);
				// Load the preview scripts in sequence
				$.getScript(collapse)
					.done(() => {
						return $.getScript(script);
					})
					.done(() => {
						$this.html('<i class="fa fa-eye-slash" aria-hidden="true"></i> Hide Preview');
						$(document).on('keyup change', () => {
							$this.html('<i class="fa fa-refresh" aria-hidden="true"></i> Update Preview');
						});
						// Scroll smoothly to the preview display
						$("html, body").animate({ scrollTop: preview_display.offset().top - 50 }, "slow");
					})
			}
		});
	});

	$(document).on('keyup change', '.sp-eap-options #eapro-form', function (e) {
		e.preventDefault();
		var $button = $(this).find('.eapro-save');
		$button.css({ "background-color": "#00C263", "pointer-events": "initial" }).val('Save Settings');
	});
	$('.sp-eap-options .eapro-save').on('click', function (e) {
		e.preventDefault();
		$(this).css({ "background-color": "#C5C5C6", "pointer-events": "none" }).val('Changes Saved');
	})

	$(document).on('click', '#sp_eap_shortcode_options .eapro-nav li a', function (event) {
		event.stopPropagation();
		$('.eap-display-tabs .eapro-tabbed-nav a:nth-child(1)').trigger('click');
	})

	// Get the select element
	var selectElement = $('.only-select-for-pro');
	// Add 'disabled' attribute to options containing '(Pro)' and set opacity to 0.9
	selectElement.find('option:contains("(Pro)")').prop({
		'disabled': true
	});

	$('.eapro-live-demo-icon').on('click', function (e) {
		e.stopPropagation();
	});
	$('#publishing-action').on('click', '#publish', function (e) {
		$('input[name="sp_eap_shortcode_options[eap_accordion_theme]"][value="sp-ea-one"]').prop('checked', true);
		$('input[name="sp_eap_shortcode_options[eap_accordion_layout]"][value="vertical"]').prop('checked', true);
	});
})(jQuery, window, document);
