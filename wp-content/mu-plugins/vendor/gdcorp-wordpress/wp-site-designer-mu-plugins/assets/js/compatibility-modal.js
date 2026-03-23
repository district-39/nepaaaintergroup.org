/**
 * Site Designer Compatibility Modal
 *
 * Intercepts plugin and theme activation BEFORE it happens.
 * Shows a warning modal and only proceeds if user confirms.
 *
 * @package wp-site-designer-mu-plugins
 */

(function($) {
	'use strict';

	const GDMUCompatibilityModal = {
		/**
		 * Modal element
		 */
		modal: null,

		/**
		 * Current interception context
		 */
		context: {
			type: null,        // 'plugin' or 'theme'
			identifier: null,  // plugin file or theme stylesheet
			activateUrl: null, // URL to proceed with activation
			activateElement: null // Element that was clicked (for themes)
		},

		/**
		 * Initialize modal and interception
		 */
		init: function() {
			this.modal = $('#gdmu-compatibility-modal');
			
			if (!this.modal.length) {
				return;
			}

			this.bindInterceptors();
			this.bindModalEvents();
		},

		/**
		 * Bind activation link/button interceptors
		 */
		bindInterceptors: function() {
			// Intercept plugin activation links on plugins.php
			if (typeof gdmuCompatibility.incompatiblePlugins !== 'undefined') {
				this.bindPluginInterceptors();
			}

			// Intercept plugin activation on Add Plugins page (plugin-install.php)
			if (typeof gdmuCompatibility.pluginBlocklist !== 'undefined') {
				this.bindAddPluginsInterceptors();
			}

			// Intercept theme activation on themes.php
			if (typeof gdmuCompatibility.themes !== 'undefined') {
				this.bindThemeInterceptors();
			}
		},

		/**
		 * Bind plugin activation interceptors
		 */
		bindPluginInterceptors: function() {
			const self = this;
			const incompatiblePlugins = gdmuCompatibility.incompatiblePlugins;

			// Intercept single plugin activation links
			$(document).on('click', 'a[href*="action=activate"]', function(e) {
				const href = $(this).attr('href');
				
				// Extract plugin file from URL
				const pluginMatch = href.match(/[?&]plugin=([^&]+)/);
				if (!pluginMatch) {
					return; // Let it proceed normally
				}

				const pluginFile = decodeURIComponent(pluginMatch[1]);

				// Check if this plugin is incompatible
				if (!incompatiblePlugins.hasOwnProperty(pluginFile)) {
					return; // Compatible plugin, proceed normally
				}

				// Prevent activation and show modal
				e.preventDefault();
				e.stopPropagation();

				self.showPluginModal(pluginFile, incompatiblePlugins[pluginFile], href);
			});

			// Intercept bulk activation form submission
			$('#bulk-action-form').on('submit', function(e) {
				const action = $('#bulk-action-selector-top').val() || $('#bulk-action-selector-bottom').val();
				
				if (action !== 'activate-selected') {
					return; // Not a bulk activation
				}

				// Get selected plugins
				const selectedPlugins = [];
				$('input[name="checked[]"]:checked').each(function() {
					selectedPlugins.push($(this).val());
				});

				// Check for incompatible plugins in selection
				const incompatibleSelected = selectedPlugins.filter(function(plugin) {
					return incompatiblePlugins.hasOwnProperty(plugin);
				});

				if (incompatibleSelected.length === 0) {
					return; // No incompatible plugins selected
				}

				// For bulk, just warn about the first incompatible one
				// User can proceed if they acknowledge
				e.preventDefault();
				
				const firstIncompatible = incompatibleSelected[0];
				self.showPluginModal(
					firstIncompatible, 
					incompatiblePlugins[firstIncompatible], 
					null, // No single URL for bulk
					$(this) // Pass form for submission
				);
			});
		},

		/**
		 * Bind plugin activation interceptors for Add Plugins page (plugin-install.php)
		 */
		bindAddPluginsInterceptors: function() {
			const self = this;
			const blocklist = gdmuCompatibility.pluginBlocklist;

			// Intercept "Activate" button clicks on plugin cards
			// These buttons appear after a plugin is installed
			$(document).on('click', '.plugin-card .activate-now, .plugin-card .button.activate', function(e) {
				const $btn = $(this);
				const href = $btn.attr('href');
				
				if (!href) {
					return; // No href, let it proceed
				}

				// Get plugin slug from the button or card
				let slug = $btn.data('slug');
				
				// Fallback: try to get from parent card
				if (!slug) {
					const $card = $btn.closest('.plugin-card');
					if ($card.length) {
						// Plugin cards have class like "plugin-card-elementor"
						const cardClass = $card.attr('class');
						const match = cardClass.match(/plugin-card-([^\s]+)/);
						if (match) {
							slug = match[1];
						}
					}
				}

				// Fallback: try to extract from href
				if (!slug) {
					const pluginMatch = href.match(/[?&]plugin=([^&/]+)/);
					if (pluginMatch) {
						// Plugin file is like "elementor/elementor.php", extract directory name
						const pluginFile = decodeURIComponent(pluginMatch[1]);
						slug = pluginFile.split('/')[0];
					}
				}

				if (!slug) {
					return; // Can't determine slug, proceed normally
				}

				// Check if this plugin is in the blocklist
				if (!blocklist.hasOwnProperty(slug)) {
					return; // Not blocked, proceed normally
				}

				// Prevent activation and show modal
				e.preventDefault();
				e.stopPropagation();

				const blockInfo = blocklist[slug];
				self.showPluginModal(
					blockInfo.pluginFile || slug, 
					{
						name: blockInfo.name,
						reason: blockInfo.reason,
						status: 'blocked'
					}, 
					href
				);
			});

			// Also handle the activation link that appears after AJAX install
			$(document).on('click', 'a.activate-now', function(e) {
				const $btn = $(this);
				const href = $btn.attr('href');
				
				if (!href || !href.includes('action=activate')) {
					return;
				}

				// Try to get slug from data attribute
				let slug = $btn.data('slug');

				// Fallback: extract from URL
				if (!slug) {
					const pluginMatch = href.match(/[?&]plugin=([^&]+)/);
					if (pluginMatch) {
						const pluginFile = decodeURIComponent(pluginMatch[1]);
						slug = pluginFile.split('/')[0];
					}
				}

				if (!slug || !blocklist.hasOwnProperty(slug)) {
					return; // Not blocked
				}

				e.preventDefault();
				e.stopPropagation();

				const blockInfo = blocklist[slug];
				self.showPluginModal(
					blockInfo.pluginFile || slug,
					{
						name: blockInfo.name,
						reason: blockInfo.reason,
						status: 'blocked'
					},
					href
				);
			});
		},

		/**
		 * Bind theme activation interceptors
		 */
		bindThemeInterceptors: function() {
			const self = this;
			const themes = gdmuCompatibility.themes;

			// Intercept theme activation button clicks
			// WordPress themes page uses Backbone, so we need to handle both static and dynamic elements
			// Multiple selectors to catch different WordPress versions and views
			const themeActivateSelectors = [
				'.theme-actions .activate',
				'.theme-actions .button.activate', 
				'.theme-overlay .activate',
				'.theme-overlay .button.activate',
				'a.activate[href*="action=activate"]',
				'a[href*="action=activate"][href*="stylesheet="]'
			].join(', ');

			$(document).on('click', themeActivateSelectors, function(e) {
				const $btn = $(this);
				const href = $btn.attr('href');

				// Must have href with activation action
				if (!href || !href.includes('action=activate')) {
					return;
				}
				
				// Get the theme stylesheet from the activate URL
				let stylesheet = null;
				
				const match = href.match(/[?&]stylesheet=([^&]+)/);
				if (match) {
					stylesheet = decodeURIComponent(match[1]);
				}

				// Fallback: try to get from parent theme element
				if (!stylesheet) {
					const $theme = $btn.closest('.theme');
					if ($theme.length) {
						stylesheet = $theme.attr('data-slug');
					}
				}

				// Try from overlay
				if (!stylesheet) {
					const $overlay = $btn.closest('.theme-overlay');
					if ($overlay.length) {
						const $activeTheme = $overlay.find('.theme-name');
						// Try to find the theme in our data by name
						const themeName = $activeTheme.text().trim();
						for (const slug in themes) {
							if (themes[slug].name === themeName) {
								stylesheet = slug;
								break;
							}
						}
					}
				}

				if (!stylesheet) {
					return;
				}

				// Check if theme is in our pre-loaded list
				if (themes.hasOwnProperty(stylesheet)) {
					const themeData = themes[stylesheet];

					// Only intercept classic themes (non-block themes)
					if (themeData.isBlockTheme) {
						return; // Block theme, proceed normally
					}

					// Prevent activation and show modal
					e.preventDefault();
					e.stopPropagation();

					self.showThemeModal(stylesheet, themeData.name, href);
					return;
				}

				// Theme not in pre-loaded list (newly installed) - check via AJAX
				
				// Prevent default while we check
				e.preventDefault();
				e.stopPropagation();

				// Get theme name from the button or nearby elements
				let themeName = $btn.attr('aria-label') || '';
				themeName = themeName.replace('Activate ', '').trim();
				if (!themeName) {
					const $card = $btn.closest('.theme, .plugin-card');
					themeName = $card.find('.theme-name, .name h3').first().text().trim() || stylesheet;
				}

				self.checkThemeAndShowModal(stylesheet, themeName, href);
			});
		},

		/**
		 * Check theme info via AJAX and show modal if classic theme
		 *
		 * @param {string} stylesheet Theme stylesheet/slug
		 * @param {string} themeName Theme display name
		 * @param {string} activateUrl URL to activate theme
		 */
		checkThemeAndShowModal: function(stylesheet, themeName, activateUrl) {
			const self = this;

			$.post(gdmuCompatibility.ajaxUrl, {
				action: 'gdmu_get_theme_info',
				nonce: gdmuCompatibility.nonce,
				theme: stylesheet
			})
			.done(function(response) {
				if (!response.success) {
					// Can't get theme info, allow activation
					window.location.href = activateUrl;
					return;
				}

				const themeData = response.data;

				// Add to our local cache for future clicks
				gdmuCompatibility.themes[stylesheet] = themeData;

				if (themeData.isBlockTheme) {
					// Block theme, proceed with activation
					window.location.href = activateUrl;
					return;
				}

				// Classic theme - show modal
				self.showThemeModal(stylesheet, themeData.name || themeName, activateUrl);
			})
			.fail(function() {
				// AJAX failed, allow activation
				window.location.href = activateUrl;
			});
		},

		/**
		 * Bind modal button events
		 */
		bindModalEvents: function() {
			const self = this;

			// Continue Anyway (proceed with activation)
			this.modal.on('click', '.gdmu-modal-link', function(e) {
				e.preventDefault();
				self.handleProceed();
			});

			// Cancel Activation
			this.modal.on('click', '.gdmu-modal-cancel-btn', function(e) {
				e.preventDefault();
				self.handleCancel();
			});

			// Close on backdrop click
			this.modal.on('click', '.gdmu-modal-backdrop', function(e) {
				self.handleCancel();
			});

			// Close on Escape key
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape' && self.modal.hasClass('is-visible')) {
					self.handleCancel();
				}
			});
		},

		/**
		 * Show plugin compatibility modal
		 *
		 * @param {string} pluginFile Plugin file path
		 * @param {object} pluginInfo Plugin info (name, reason, status)
		 * @param {string} activateUrl URL to activate plugin
		 * @param {jQuery} $form Optional form element for bulk activation
		 * @return {boolean} True if modal was shown, false if skipped
		 */
		showPluginModal: function(pluginFile, pluginInfo, activateUrl, $form) {
			const i18n = gdmuCompatibility.i18n;

			// Check if user has dismissed plugin warnings
			if (gdmuCompatibility.dismissedPluginWarnings) {
				// Skip modal, proceed directly
				if (activateUrl) {
					window.location.href = activateUrl;
				} else if ($form) {
					$form.off('submit').submit();
				}
				return false;
			}
			
			// Store context
			this.context = {
				type: 'plugin',
				identifier: pluginFile,
				activateUrl: activateUrl,
				activateElement: $form || null
			};

			// Populate modal
			this.modal.attr('data-type', 'plugin');
			this.modal.attr('data-plugin', pluginFile);
			
			// Dynamic title with plugin name
			this.modal.find('.gdmu-modal-title').text(i18n.pluginWarningTitle.replace('%s', pluginInfo.name));
			this.modal.find('.gdmu-modal-message').text(i18n.pluginMessage);
			
			// Show review note if applicable
			const $note = this.modal.find('.gdmu-modal-note');
			if (pluginInfo.status === 'review') {
				$note.text(i18n.reviewNote).show();
			} else {
				$note.hide();
			}

			// Set checkbox label and reset state
			this.modal.find('.gdmu-modal-dismiss-label').text(i18n.dontShowPluginWarnings);
			this.modal.find('.gdmu-modal-dismiss-checkbox').prop('checked', false);

			// Set button labels
			this.modal.find('.gdmu-modal-link .gdmu-btn-text').text(i18n.continueAnyway);
			this.modal.find('.gdmu-modal-cancel-btn .gdmu-btn-text').text(i18n.cancelActivation);

			this.showModal();
			return true;
		},

		/**
		 * Show theme compatibility modal
		 *
		 * @param {string} stylesheet Theme stylesheet
		 * @param {string} themeName Theme display name
		 * @param {string} activateUrl URL to activate theme
		 * @return {boolean} True if modal was shown, false if skipped
		 */
		showThemeModal: function(stylesheet, themeName, activateUrl) {
			const i18n = gdmuCompatibility.i18n;

			// Check if user has dismissed theme warnings
			if (gdmuCompatibility.dismissedThemeWarnings) {
				// Skip modal, proceed directly
				window.location.href = activateUrl;
				return false;
			}
			
			// Store context
			this.context = {
				type: 'theme',
				identifier: stylesheet,
				activateUrl: activateUrl,
				activateElement: null
			};

			// Populate modal
			this.modal.attr('data-type', 'theme');
			this.modal.attr('data-theme', stylesheet);
			
			// Dynamic title with theme name
			this.modal.find('.gdmu-modal-title').text(i18n.themeWarningTitle.replace('%s', themeName));
			this.modal.find('.gdmu-modal-message').text(i18n.themeMessage);
			
			this.modal.find('.gdmu-modal-note').hide();

			// Set checkbox label and reset state
			this.modal.find('.gdmu-modal-dismiss-label').text(i18n.dontShowThemeWarnings);
			this.modal.find('.gdmu-modal-dismiss-checkbox').prop('checked', false);

			// Set button labels
			this.modal.find('.gdmu-modal-link .gdmu-btn-text').text(i18n.continueAnyway);
			this.modal.find('.gdmu-modal-cancel-btn .gdmu-btn-text').text(i18n.cancelActivation);

			this.showModal();
			return true;
		},

		/**
		 * Show the modal
		 */
		showModal: function() {
			this.modal.addClass('is-visible');
			$('body').addClass('gdmu-modal-open');
			
			// Focus on cancel button - safer default
			this.modal.find('.gdmu-modal-cancel-btn').focus();
		},

		/**
		 * Hide the modal
		 */
		hideModal: function() {
			this.modal.removeClass('is-visible');
			$('body').removeClass('gdmu-modal-open');
			
			// Reset context
			this.context = {
				type: null,
				identifier: null,
				activateUrl: null,
				activateElement: null
			};
		},

		/**
		 * Handle proceed action (user chose to activate anyway)
		 */
		handleProceed: function() {
			const self = this;
			const btn = this.modal.find('.gdmu-modal-link');
			const dismissCheckbox = this.modal.find('.gdmu-modal-dismiss-checkbox');
			const shouldDismiss = dismissCheckbox.prop('checked');
			
			btn.prop('disabled', true);
			this.setButtonLoading(btn, true);

			// Function to proceed with activation
			const proceedWithActivation = function() {
				// If we have an activation URL, navigate to it
				if (self.context.activateUrl) {
					window.location.href = self.context.activateUrl;
					return;
				}

				// If we have a form (bulk activation), submit it
				if (self.context.activateElement && self.context.activateElement.is('form')) {
					// Unbind our interceptor and submit the form
					self.context.activateElement.off('submit').submit();
					return;
				}

				// Fallback: just hide the modal
				self.hideModal();
			};

			// Save dismiss preference if checkbox is checked
			if (shouldDismiss && this.context.type) {
				$.post(gdmuCompatibility.ajaxUrl, {
					action: 'gdmu_dismiss_modal_warnings',
					nonce: gdmuCompatibility.nonce,
					type: this.context.type
				}).always(function() {
					// Update local state
					if (self.context.type === 'plugin') {
						gdmuCompatibility.dismissedPluginWarnings = true;
					} else if (self.context.type === 'theme') {
						gdmuCompatibility.dismissedThemeWarnings = true;
					}
					proceedWithActivation();
				});
				return;
			}

			proceedWithActivation();
		},

		/**
		 * Handle cancel action (user chose not to activate)
		 */
		handleCancel: function() {
			this.hideModal();
		},

		/**
		 * Set button loading state
		 *
		 * @param {jQuery} btn     Button element
		 * @param {boolean} loading Loading state
		 */
		setButtonLoading: function(btn, loading) {
			if (loading) {
				btn.addClass('is-loading');
			} else {
				btn.removeClass('is-loading');
			}
		}
	};

	// Initialize on DOM ready
	$(document).ready(function() {
		GDMUCompatibilityModal.init();
	});

})(jQuery);
