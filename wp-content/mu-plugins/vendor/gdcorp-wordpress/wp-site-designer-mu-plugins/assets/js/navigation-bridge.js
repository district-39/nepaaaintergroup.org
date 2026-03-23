/**
 * Navigation Bridge
 *
 * Bridges navigation events between WordPress and Site Designer iframe.
 *
 * @package wp-site-designer-mu-plugins
 */

(function() {
	'use strict';

	const PARENT_ORIGIN = window.siteDesignerNavigation?.parentOrigin || null;
	const ALLOWED_ORIGINS = window.siteDesignerNavigation?.allowedOrigins || [];

	let urlCheckInterval = null;
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
	 * Send current page info to parent window
	 */
	function sendNavigationInfo() {
		const urlParams = new URLSearchParams(window.location.search);
		const pageInfo = {
			type: 'wordpress-navigation',
			url: window.location.href,
			pathname: pathname,
			timestamp: Date.now()
		};

		// Block editors: get page info from wp.data
		if (context.hasWpData()) {
			const { select } = wp.data;

			// Site Editor: use core/edit-site store
			if (context.isSiteEditor) {
				try {
					const pageId = urlParams.get('postId');
					const pageType = urlParams.get('postType') || 'page';

					if (pageId) {
						pageInfo.pageId = parseInt(pageId, 10);

						const pageEntity = select('core').getEntityRecord('postType', pageType, pageInfo.pageId);

						if (pageEntity) {
							pageInfo.pageName = pageEntity.slug || pageEntity.title?.rendered || pageEntity.title?.raw || pageEntity.title;
						}
					}
				} catch (e) {
					// Site editor store error
				}
			}

			// Post Editor: use core/editor store
			if (context.isPostEditor) {
				try {
					const currentPost = select('core/editor').getCurrentPost();
					if (currentPost && currentPost.id) {
						pageInfo.pageId = currentPost.id;
						pageInfo.pageName = currentPost.slug || currentPost.title;
					}
				} catch (e) {
					// Post editor store not available
				}
			}
		}

		// Fallback: extract page ID from body classes (frontend)
		if (!pageInfo.pageId) {
			const bodyClasses = document.body.className.split(' ');
			for (const className of bodyClasses) {
				if (className.startsWith('page-id-')) {
					pageInfo.pageId = parseInt(className.replace('page-id-', ''), 10);
					break;
				} else if (className.startsWith('postid-')) {
					pageInfo.pageId = parseInt(className.replace('postid-', ''), 10);
					break;
				}
			}
		}

		// Fallback: extract page ID from admin URL
		if (!pageInfo.pageId && urlParams.has('post')) {
			pageInfo.pageId = parseInt(urlParams.get('post'), 10);
		}

		// Fallback: extract pageName from URL pathname (frontend)
		if (!pageInfo.pageName) {
			const cleanPath = pathname.replace(/^\/+|\/+$/g, '');
			const segments = cleanPath.split('/').filter(s => s);
			if (segments.length > 0) {
				pageInfo.pageName = segments[segments.length - 1];
			} else if (cleanPath === '') {
				pageInfo.pageName = 'home';
			}
		}

		if (window.parent && window.parent !== window) {
			if (PARENT_ORIGIN) {
				window.parent.postMessage(pageInfo, PARENT_ORIGIN);
			} else {
				ALLOWED_ORIGINS.forEach(function (origin) {
					window.parent.postMessage(pageInfo, origin);
				});
			}
		}
	}

	/**
	 * Setup subscribe for Site Editor (SPA navigation)
	 */
	function setupSiteEditorSubscribe() {
		let lastPageId = null;
		const urlParams = new URLSearchParams(window.location.search);

		// Trigger initial fetch
		const postId = urlParams.get('postId');
		if (postId) {
			const postIdNum = parseInt(postId, 10);
			const postType = urlParams.get('postType') || 'page';
			wp.data.select('core').getEntityRecord('postType', postType, postIdNum);
		}

		// Subscribe for entity changes
		unsubscribe = wp.data.subscribe(() => {
			try {
				const postId = urlParams.get('postId');
				if (postId) {
					const postIdNum = parseInt(postId, 10);
					const postType = urlParams.get('postType') || 'page';
					const entity = wp.data.select('core').getEntityRecord('postType', postType, postIdNum);

					if (entity && postIdNum !== lastPageId) {
						lastPageId = postIdNum;
						setTimeout(sendNavigationInfo, 100);
					}
				}
			} catch (e) {
				// Silently ignore
			}
		});
	}

	/**
	 * Setup subscribe for Post Editor (wait for post data)
	 */
	function setupPostEditorSubscribe() {
		let hasLoadedOnce = false;

		unsubscribe = wp.data.subscribe(() => {
			if (hasLoadedOnce) return;

			try {
				const currentPost = wp.data.select('core/editor').getCurrentPost();
				if (currentPost && currentPost.id) {
					hasLoadedOnce = true;
					setTimeout(sendNavigationInfo, 100);
				}
			} catch (e) {
				// Silently ignore
			}
		});
	}

	/**
	 * Setup subscribe based on editor context
	 */
	function setupSubscribe() {
		if (!context.hasWpData() || !context.isBlockEditor()) {
			return;
		}

		try {
			if (context.isSiteEditor) {
				setupSiteEditorSubscribe();
			} else if (context.isPostEditor) {
				setupPostEditorSubscribe();
			}
		} catch (e) {
			// Silently ignore subscribe errors
		}
	}

	/**
	 * Cleanup function
	 */
	function cleanup() {
		if (urlCheckInterval) {
			clearInterval(urlCheckInterval);
			urlCheckInterval = null;
		}
		if (unsubscribe) {
			unsubscribe();
			unsubscribe = null;
		}
	}

	/**
	 * Initialize
	 */
	function init() {
		// Send on initial load and setup subscribe
		sendNavigationInfo();
		setupSubscribe();

		// Send on history navigation
		window.addEventListener('popstate', sendNavigationInfo);

		// Intercept pushState and replaceState
		const originalPushState = history.pushState;
		const originalReplaceState = history.replaceState;

		history.pushState = function(...args) {
			originalPushState.apply(this, args);
			sendNavigationInfo();
		};

		history.replaceState = function(...args) {
			originalReplaceState.apply(this, args);
			sendNavigationInfo();
		};

		// Send on hash changes
		window.addEventListener('hashchange', sendNavigationInfo);

		// Cleanup on page unload
		window.addEventListener('beforeunload', cleanup);

		// Poll for URL changes (fallback)
		let lastUrl = window.location.href;
		urlCheckInterval = setInterval(() => {
			if (window.location.href !== lastUrl) {
				lastUrl = window.location.href;
				sendNavigationInfo();
			}
		}, 1000);
	}

	// Start when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();

