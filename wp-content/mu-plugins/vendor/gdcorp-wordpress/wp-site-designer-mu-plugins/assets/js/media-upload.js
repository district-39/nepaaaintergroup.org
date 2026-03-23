/**
 * Site Designer Media Upload Component
 * Opens WordPress native media library modal with upload + library tabs
 *
 * @version 1.0.0
 */

(function(window, document) {
	'use strict';

	// Early exit if WordPress media not available
	if (!window.wp || !window.wp.media) {
		console.error('WordPress media library not loaded');
		return;
	}

	/**
	 * Media Upload Manager
	 * Handles WordPress media modal operations
	 */
	class MediaUploadManager {
		constructor(options = {}) {
			this.options = {
				title: options.title || 'Select or Upload Media',
				buttonText: options.buttonText || 'Use this media',
				multiple: options.multiple || false,
				library: options.library || { type: 'image' },
				...options
			};

			this.frame = null;
			this.isLoading = false;
			this.onSelect = options.onSelect || null;
			this.onClose = options.onClose || null;
			this.onError = options.onError || null;
		}

		/**
		 * Create WordPress media frame
		 * This creates the modal with Upload + Library tabs
		 */
		createFrame() {
			if (this.frame) {
				return this.frame;
			}

			try {
				// Create WordPress media frame
				this.frame = wp.media({
					title: this.options.title,
					button: {
						text: this.options.buttonText
					},
					multiple: this.options.multiple,
					library: this.options.library
				});

				// Handle selection
				this.frame.on('select', () => this.handleSelect());

				// Handle close
				this.frame.on('close', () => this.handleClose());

				// Handle errors
				this.frame.on('error', (error) => this.handleError(error));

			} catch (error) {
				console.error('Failed to create media frame:', error);
				this.handleError(error);
			}

			return this.frame;
		}

		/**
		 * Handle media selection
		 */
		handleSelect() {
			try {
				const selection = this.frame.state().get('selection');

				if (!selection) {
					throw new Error('No selection found');
				}

				const selected = selection.toJSON();

				if (!selected || (Array.isArray(selected) && selected.length === 0)) {
					console.warn('Empty selection');
					return;
				}

				if (this.onSelect) {
					const media = this.options.multiple ? selected : selected[0];
					const formatted = this.formatMediaData(media);

					if (formatted) {
						this.onSelect(formatted);
					}
				}
			} catch (error) {
				console.error('Error handling media selection:', error);
				this.handleError(error);
			}
		}

		/**
		 * Handle modal close
		 */
		handleClose() {
			this.isLoading = false;

			if (this.onClose) {
				try {
					this.onClose();
				} catch (error) {
					console.error('Error in close callback:', error);
				}
			}
		}

		/**
		 * Handle errors
		 */
		handleError(error) {
			this.isLoading = false;

			console.error('Media upload error:', error);

			if (this.onError) {
				this.onError(error);
			}
		}

		/**
		 * Format media data for frontend
		 */
		formatMediaData(media) {
			if (!media) {
				console.warn('No media data to format');
				return null;
			}

			if (Array.isArray(media)) {
				return media.map(m => this.formatSingleMedia(m)).filter(Boolean);
			}

			return this.formatSingleMedia(media);
		}

		/**
		 * Format single media item
		 */
		formatSingleMedia(media) {
			if (!media || !media.id) {
				console.warn('Invalid media data');
				return null;
			}

			return {
				id: media.id,
				url: media.url || '',
				alt: media.alt || '',
				title: media.title || '',
				caption: media.caption || '',
				description: media.description || '',
				filename: media.filename || '',
				mime: media.mime || media.type || '',
				type: media.type || '',
				subtype: media.subtype || '',
				width: media.width || 0,
				height: media.height || 0,
				filesize: media.filesizeInBytes || 0,
				filesizeHumanReadable: media.filesizeHumanReadable || '',
				sizes: media.sizes || {},
				// Convenience URLs
				largeUrl: media.sizes?.large?.url || media.sizes?.full?.url || media.url,
				mediumUrl: media.sizes?.medium?.url || media.url,
				thumbnailUrl: media.sizes?.thumbnail?.url || media.url,
				// Metadata
				uploadedTo: media.uploadedTo || 0,
				date: media.dateFormatted || media.date || '',
				author: media.authorName || media.author || ''
			};
		}

		/**
		 * Open the media modal
		 */
		open() {
			if (this.isLoading) {
				console.warn('Media upload already in progress');
				return this;
			}

			this.isLoading = true;

			try {
				const frame = this.createFrame();
				frame.open();
			} catch (error) {
				console.error('Failed to open media modal:', error);
				this.handleError(error);
			}

			return this;
		}
	}

	/**
	 * Global API
	 */
	window.SiteDesignerMedia = {
		/**
		 * Open media library for logo upload
		 */
		openLogoUpload: function(options = {}) {
			return new MediaUploadManager({
				title: 'Upload Site Logo',
				buttonText: 'Select',
				multiple: false,
				library: { type: 'image' },
				...options
			}).open();
		},

		/**
		 * Open media library for images
		 */
		openImageUpload: function(options = {}) {
			return new MediaUploadManager({
				title: 'Select or Upload Image',
				buttonText: 'Select',
				multiple: false,
				library: { type: 'image' },
				...options
			}).open();
		},

		/**
		 * Send media selection to parent window (iframe context)
		 */
		notifyParent: function(media, options = {}) {
			if (!window.parent || window.parent === window) {
				console.warn('No parent window found');
				return;
			}

			// Determine target origin with proper priority
			let targetOrigin = '*';

			// Priority 1: Explicit option passed to this function
			if (options.targetOrigin) {
				targetOrigin = options.targetOrigin;
			}
			// Priority 2: Config from MediaUpload.php (most reliable)
			else if (window.siteDesignerMedia?.config?.parentOrigin) {
				targetOrigin = window.siteDesignerMedia.config.parentOrigin;
			}
			// Priority 3: Allowed origins from config (use first one)
			else if (window.siteDesignerMedia?.config?.allowedOrigins?.length > 0) {
				const allowedOrigins = window.siteDesignerMedia.config.allowedOrigins;
				// Filter out current site origin (WordPress itself)
				const currentOrigin = window.location.origin;
				const externalOrigins = allowedOrigins.filter(origin => origin !== currentOrigin);
				if (externalOrigins.length > 0) {
					targetOrigin = externalOrigins[0];
				}
			}
			// Priority 4: Try referrer ONLY if it's not the current WordPress site
			else if (document.referrer) {
				try {
					const referrerUrl = new URL(document.referrer);
					const referrerOrigin = referrerUrl.origin;
					const currentOrigin = window.location.origin;
					
					// Only use referrer if it's external (not WordPress navigating to itself)
					if (referrerOrigin !== currentOrigin) {
						targetOrigin = referrerOrigin;
					}
				} catch (e) {
					console.warn('Could not parse referrer URL');
				}
			}

			// Security warning
			if (targetOrigin === '*' && !options.allowWildcard) {
				console.warn('Using wildcard origin - not recommended for production');
			}

			try {
				window.parent.postMessage({
					type: options.type || 'site-designer-media-selected',
					media: media,
					timestamp: Date.now(),
					source: options.source || 'site-designer-media-upload'
				}, targetOrigin);
				
				console.log('Sent media to parent:', targetOrigin);
			} catch (error) {
				console.error('Failed to send message to parent:', error);
			}
		},

		/**
		 * Version
		 */
		version: '1.0.0'
	};

	/**
	 * Listen for messages from parent window
	 */
	function initParentListener() {
		window.addEventListener('message', (event) => {
			// Validate origin in production
			if (window.siteDesignerMedia?.config?.validateOrigin) {
				const allowedOrigins = window.siteDesignerMedia.config.allowedOrigins || [];
				if (allowedOrigins.length > 0 && !allowedOrigins.includes(event.origin)) {
					console.warn('Message from unexpected origin:', event.origin);
					return;
				}
			}

			const data = event.data;

			// Handle parent requests
			if (data && typeof data === 'object') {
				switch (data.type) {
					case 'site-designer-request-media':
						SiteDesignerMedia.openImageUpload({
							onSelect: (media) => {
								SiteDesignerMedia.notifyParent(media, { type: 'site-designer-media-selected', ...data.options });
							}
						});
						break;

					case 'site-designer-request-logo':
						SiteDesignerMedia.openLogoUpload({
							onSelect: async (media) => {
								// Actually applies the logo to WordPress
								await wp.apiRequest({
									path: '/wp/v2/settings',
									method: 'POST',
									data: { site_logo: media.id }
								});
								// Reload to show it
								window.location.reload();
								SiteDesignerMedia.notifyParent(media, { type: 'site-designer-logo-selected', ...data.options });
							}
						});
						break;
				}
			}
		});
	}

	// Initialize
	initParentListener();

	// Log when ready
	if (window.siteDesignerMedia && window.siteDesignerMedia.ready) {
		console.log('Site Designer Media Upload v' + window.SiteDesignerMedia.version + ' ready');
	}

})(window, document);