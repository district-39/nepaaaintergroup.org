/**
 * Viewport Bridge
 *
 * Captures viewport context for Site Designer chat requests.
 * Reports what content is visible to help AI understand user's current view.
 *
 * @package wp-site-designer-mu-plugins
 */

(function() {
	'use strict';

	const PARENT_ORIGIN = window.siteDesignerViewport?.parentOrigin || null;
	const ALLOWED_ORIGINS = window.siteDesignerViewport?.allowedOrigins || [];
	let lastSentData = '';
	let pollingInterval = null;
	let unsubscribe = null;

	// Context detection (cached)
	const pathname = window.location.pathname;
	const context = {
		isSiteEditor: pathname.includes('site-editor.php'),
		isPostEditor: pathname.includes('post.php') || pathname.includes('post-new.php'),
		isBlockEditor: function() { return this.isSiteEditor || this.isPostEditor; },
		hasWpData: function() { return typeof wp !== 'undefined' && wp.data; }
	};

	/**
	 * Truncate text to max length
	 */
	function truncate(text, maxLength) {
		if (!text) return '';
		const cleaned = text.trim().replace(/\s+/g, ' ');
		return cleaned.length > maxLength
			? cleaned.substring(0, maxLength) + '...'
			: cleaned;
	}

	/**
	 * Get the content root (where actual page content lives)
	 */
	function getContentRoot() {
		// Site Editor: content is in nested iframe
		const editorCanvas = document.querySelector('iframe[name="editor-canvas"]');
		if (editorCanvas?.contentDocument) {
			return {
				root: editorCanvas.contentDocument.body,
				window: editorCanvas.contentWindow
			};
		}

		// Post Editor: content is in .editor-styles-wrapper
		const editorWrapper = document.querySelector('.editor-styles-wrapper');
		if (editorWrapper) {
			return { root: editorWrapper, window: window };
		}

		// Frontend/Preview: use body
		return { root: document.body, window: window };
	}

	/**
	 * Check if element is visible in viewport
	 */
	function isElementVisible(element, targetWindow) {
		if (!element) return false;
		const rect = element.getBoundingClientRect();
		const viewportHeight = targetWindow ? targetWindow.innerHeight : window.innerHeight;
		return (
			rect.top < viewportHeight &&
			rect.bottom > 0 &&
			rect.height > 0 &&
			rect.width > 0
		);
	}

	/**
	 * Check if element is in main content (not in header or footer)
	 */
	function isInMainContent(element) {
		return element.closest('header') === null && element.closest('footer') === null;
	}

	/**
	 * Get label text for an input element
	 */
	function getInputLabel(input) {
		// Try label by 'for' attribute
		if (input.id) {
			const label = document.querySelector(`label[for="${input.id}"]`);
			if (label) return label.innerText.trim();
		}

		// Try parent label
		const parentLabel = input.closest('label');
		if (parentLabel) return parentLabel.innerText.trim();

		// Try previous sibling label
		let prevSibling = input.previousElementSibling;
		while (prevSibling) {
			if (prevSibling.tagName.toLowerCase() === 'label') {
				return prevSibling.innerText.trim();
			}
			prevSibling = prevSibling.previousElementSibling;
		}

		return input.placeholder || input.name || '';
	}

	// ============================================
	// Element Info Extractors (split for clarity)
	// ============================================

	function getHeadingInfo(element) {
		return {
			type: 'heading',
			text: truncate(element.innerText, 50)
		};
	}

	function getParagraphInfo(element) {
		const text = element.innerText.trim();
		if (text.length > 0) {
			return {
				type: 'paragraph',
				text: truncate(text, 100)
			};
		}
		return null;
	}

	function getInputInfo(element) {
		const inputType = element.type || 'text';
		if (inputType === 'hidden' || inputType === 'submit' || inputType === 'button') {
			return null;
		}

		const label = getInputLabel(element);
		const value = element.value || '';

		let text = '';
		if (label && value) {
			text = `${truncate(label, 30)} ${truncate(value, 50)}`;
		} else if (label) {
			text = truncate(label, 50);
		} else if (value) {
			text = truncate(value, 50);
		}

		if (text) {
			return { type: 'input', text: text };
		}
		return null;
	}

	function getButtonInfo(element) {
		const tagName = element.tagName.toLowerCase();
		const isButton = tagName === 'button' ||
			(tagName === 'a' && element.classList.contains('wp-element-button')) ||
			(tagName === 'a' && element.closest('.wp-block-button')) ||
			(tagName === 'div' && element.classList.contains('wp-block-button__link'));

		if (isButton) {
			return {
				type: 'button',
				text: truncate(element.innerText, 30)
			};
		}
		return null;
	}

	function getImageInfo(element) {
		const altText = element?.alt ? truncate(element.alt, 50) : null;
		const className = element?.className || '';

		const wpImageMatch = className.match(/wp-image-(\d+)/);
		const imageId = wpImageMatch ? wpImageMatch[1] : undefined;

		if (altText || imageId) {
			return {
				type: 'image',
				text: altText || '',
				imageId: imageId ? parseInt(imageId, 10) : undefined
			};
		}
		return null;
	}

	function getListItemInfo(element) {
		const text = element.innerText.trim();
		if (text.length > 0) {
			return {
				type: 'list-item',
				text: truncate(text, 100)
			};
		}
		return null;
	}

	/**
	 * Get element info based on type
	 */
	function getElementInfo(element) {
		const tagName = element.tagName.toLowerCase();

		if (tagName.match(/^h[1-6]$/)) return getHeadingInfo(element);
		if (tagName === 'p') return getParagraphInfo(element);
		if (tagName === 'li') return getListItemInfo(element);
		if (tagName === 'input' || tagName === 'textarea' || tagName === 'select') return getInputInfo(element);
		if (tagName === 'img') return getImageInfo(element);

		// Button check (can be button, a, or div)
		const buttonInfo = getButtonInfo(element);
		if (buttonInfo) return buttonInfo;

		// Skip ul (we capture li items instead)
		if (tagName === 'ul') return null;

		return null;
	}

	/**
	 * Check if element should be skipped from viewport capture
	 */
	function shouldSkipElement(element, targetWindow) {
		if (!isElementVisible(element, targetWindow) || !isInMainContent(element)) {
			return true;
		}

		if (element.classList.contains('editor-post-title')) {
			return true;
		}

		const tagName = element.tagName.toLowerCase();

		if ((tagName === 'p' || tagName === 'li') && element.innerText.trim().length === 0) {
			return true;
		}

		if (tagName === 'img' && !element.alt) {
			return true;
		}

		return false;
	}

	/**
	 * Capture viewport context with visible elements
	 */
	function captureViewportContext() {
		const contentContext = getContentRoot();
		const contentRoot = contentContext.root;
		const targetWindow = contentContext.window;
		const elements = [];

		const selector = 'h1, h2, h3, h4, h5, h6, p, ul, li, input, textarea, select, button, a.wp-element-button, .wp-block-button a, .wp-block-button__link, img';
		contentRoot.querySelectorAll(selector).forEach(element => {
			if (shouldSkipElement(element, targetWindow)) {
				return;
			}

			const info = getElementInfo(element);

			if (info && info.type === 'button' && !info.text) {
				return;
			}

			if (info) {
				elements.push(info);
			}
		});

		return {
			type: 'wordpress-viewport',
			timestamp: Date.now(),
			scrollPercent: Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100) || 0,
			visibleElements: elements
		};
	}

	/**
	 * Send viewport context to parent window
	 */
	function sendViewportContext() {
		if (!window.parent || window.parent === window) {
			return;
		}

		try {
			const viewportContext = captureViewportContext();
			const currentData = JSON.stringify(viewportContext.visibleElements);

			if (currentData !== lastSentData) {
				lastSentData = currentData;

				if (PARENT_ORIGIN) {
					window.parent.postMessage(viewportContext, PARENT_ORIGIN);
				} else {
					ALLOWED_ORIGINS.forEach(function (origin) {
						window.parent.postMessage(viewportContext, origin);
					});
				}
			}
		} catch (e) {
			console.error('[ViewportBridge] Error capturing viewport context:', e);
		}
	}

	/**
	 * Setup polling for block editors
	 */
	function setupPolling() {
		if (context.isBlockEditor()) {
			pollingInterval = setInterval(sendViewportContext, 3000);
		}
	}

	/**
	 * Cleanup function
	 */
	function cleanup() {
		if (pollingInterval) {
			clearInterval(pollingInterval);
			pollingInterval = null;
		}
		if (unsubscribe) {
			unsubscribe();
			unsubscribe = null;
		}
	}

	/**
	 * Setup scroll listener for post editor
	 */
	function setupEditorScrollListener(scrollHandler) {
		if (!context.isPostEditor) return;

		const editorScroller = document.querySelector('.interface-interface-skeleton__content');
		if (editorScroller) {
			editorScroller.addEventListener('scroll', scrollHandler, { passive: true });
		}
	}

	/**
	 * Setup event listeners
	 */
	function setupEventListeners() {
		let scrollTimeout;

		const scrollHandler = function() {
			clearTimeout(scrollTimeout);
			scrollTimeout = setTimeout(sendViewportContext, 200);
		};

		window.addEventListener('scroll', scrollHandler, { passive: true });
		setupEditorScrollListener(scrollHandler);

		document.addEventListener('focusin', function() {
			setTimeout(sendViewportContext, 50);
		});

		document.addEventListener('click', function() {
			setTimeout(sendViewportContext, 50);
		});

		window.addEventListener('beforeunload', cleanup);
	}

	/**
	 * Setup subscribe for Gutenberg block changes
	 */
	function setupSubscribe() {
		if (!context.hasWpData()) {
			return;
		}

		try {
			let previousSelectedBlock = null;
			let hasLoadedOnce = false;

			unsubscribe = wp.data.subscribe(() => {
				try {
					const blocks = wp.data.select('core/block-editor')?.getBlocks();

					if (blocks && blocks.length > 0 && !hasLoadedOnce) {
						hasLoadedOnce = true;
						setTimeout(sendViewportContext, 100);
					}

					const selectedBlockClientId = wp.data.select('core/block-editor')?.getSelectedBlockClientId();
					if (selectedBlockClientId !== previousSelectedBlock) {
						previousSelectedBlock = selectedBlockClientId;
						setTimeout(sendViewportContext, 50);
					}
				} catch (e) {
					// Silently ignore
				}
			});
		} catch (e) {
			// Silently ignore
		}
	}

	/**
	 * Initialize
	 */
	function init() {
		setupEventListeners();
		setupPolling();
		sendViewportContext();
		setupSubscribe();
	}

	// Start when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			setTimeout(init, 200);
		});
	} else {
		setTimeout(init, 200);
	}
})();

