<?php
/**
 * Gutenberg Support Plugin
 *
 * Enables communication between the Gutenberg editor (in iframe) and Site Designer
 * parent application. Handles commands like saving editor content on demand.
 *
 * Why this exists:
 * - Site Designer embeds WordPress in an iframe for live editing
 * - Parent app needs to trigger WordPress actions (e.g., save content)
 * - This listens for postMessage commands and executes corresponding actions
 *
 * Security note:
 * - This script only loads when request is validated as coming from allowed origin
 * - We verify message type to ensure it's a recognized command
 *
 * @package wp-site-designer-mu-plugins
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

/**
 * Handles Gutenberg editor integration with Site Designer parent window
 *
 * Listens for postMessage commands from parent and executes editor actions.
 */
class Gutenberg_Support {

	/**
	 * Initialize the class and register hooks
	 *
	 * Uses enqueue_block_editor_assets hook which only fires when
	 * Gutenberg editor is being loaded (not on all admin pages).
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();
		add_action( 'enqueue_block_editor_assets', array( $instance, 'cmd_listener' ) );
		add_action( 'enqueue_block_editor_assets', array( $instance, 'disable_welcome_guide' ) );
		add_action( 'enqueue_block_editor_assets', array( $instance, 'hide_footer' ) );
	}

	/**
	 * Enqueue JavaScript that handles postMessage commands from Site Designer
	 *
	 * @return void
	 */
	public function cmd_listener(): void {
		$script = <<<'JS'
/**
 * Gutenberg Support - Site Designer to WordPress Communication
 *
 * Listens for commands from parent window and executes Gutenberg editor actions.
 * This script only runs in validated iframe context (origin checked server-side).
 */
( function() {
	'use strict';

	/**
	 * Save current post content via Gutenberg editor API
	 *
	 * Only saves if:
	 * - Gutenberg editor is available
	 * - Post has unsaved changes (isDirty)
	 */
	async function saveContent() {
		// Verify Gutenberg editor is available.
		if ( typeof wp === 'undefined' || ! wp.data || ! wp.data.select( 'core/editor' ) ) {
			return;
		}

		try {
			const { dispatch, select } = wp.data;

			// Only save if there are unsaved changes.
			if ( select( 'core/editor' ).isEditedPostDirty() ) {
				await dispatch( 'core/editor' ).savePost();
			}
		} catch ( error ) {
			// Silent fail - parent doesn't need to know about save errors.
			// WordPress will show its own error notifications in the editor.
		}
	}

	/**
	 * Handle incoming postMessage commands from parent window
	 *
	 * Supported commands:
	 * - site-designer-save: Triggers content save
	 */
	window.addEventListener( 'message', function( event ) {
		// Verify message has the expected structure.
		if ( ! event.data || typeof event.data.type !== 'string' ) {
			return;
		}

		// Route commands to appropriate handlers.
		switch ( event.data.type ) {
			case 'site-designer-save':
				saveContent();
				break;
		}
	} );
} )();
JS;

		wp_add_inline_script( 'wp-edit-post', $script );
	}

	/**
	 * Disable the welcome guide by injecting JavaScript into the block editor
	 *
	 * @return void
	 */
	public function disable_welcome_guide(): void {
		$script = <<<'JS'
( function () {
	if ( typeof wp === 'undefined' || ! wp.data?.subscribe || ! wp.data?.dispatch || ! wp.data?.select ) {
		return;
	}

	// Preference keys used across different WordPress versions
	// - 'welcomeGuide': WordPress 5.9+ (most common)
	// - 'welcome': Some WP versions and third-party plugins
	// - 'welcomeGuideActive': Legacy WP versions (pre-5.9)
	const WELCOME_GUIDE_PREFERENCE_KEYS = ['welcomeGuide', 'welcome', 'welcomeGuideActive'];

	// Stores that may contain welcome guide preferences
	const PREFERENCE_STORES = ['core/edit-post', 'core/edit-site', 'core'];

	const { dispatch, select, subscribe } = wp.data;
	let hasRun = false;

	// Wait for stores to be ready using subscribe
	const unsubscribe = subscribe(() => {
		// Check if stores are now available
		const editPostStore = select('core/edit-post');
		const preferencesStore = select('core/preferences');

		if (hasRun || !editPostStore || !preferencesStore) {
			return;
		}

		hasRun = true;
		unsubscribe();

		// Set preferences for all potential keys across all stores
		const prefsDispatch = dispatch('core/preferences');
		if (prefsDispatch?.set) {
			WELCOME_GUIDE_PREFERENCE_KEYS.forEach(key => {
				PREFERENCE_STORES.forEach(store => {
					try {
						prefsDispatch.set(store, key, false);
					} catch(e) {
						// Silently ignore - key or store may not exist in this WP version
					}
				});
			});
		}

		// Toggle feature if active
		if (editPostStore?.isFeatureActive?.('welcomeGuide')) {
			dispatch('core/edit-post').toggleFeature('welcomeGuide');
		}

		const editSiteStore = select('core/edit-site');
		if (editSiteStore?.isFeatureActive?.('welcomeGuide')) {
			dispatch('core/edit-site').toggleFeature('welcomeGuide');
		}
		
		// Set localStorage preferences for all WP_PREFERENCES keys
		try {
			// Find all WP_PREFERENCES keys
			const allWPPrefKeys = Object.keys(localStorage).filter(k => k.startsWith('WP_PREFERENCES'));
			
			// Set welcomeGuide: false in ALL WP_PREFERENCES keys
			allWPPrefKeys.forEach(key => {
				try {
					const prefs = JSON.parse(localStorage.getItem(key) || '{}');
					if (!prefs['core/edit-post']) prefs['core/edit-post'] = {};
					prefs['core/edit-post'].welcomeGuide = false;
					localStorage.setItem(key, JSON.stringify(prefs));
				} catch(e2) {}
			});
			
			// Also set the generic one if it doesn't exist
			if (!allWPPrefKeys.includes('WP_PREFERENCES')) {
				localStorage.setItem('WP_PREFERENCES', JSON.stringify({
					'core/edit-post': { welcomeGuide: false }
				}));
			}
		} catch(e) {}
		
		// Use MutationObserver to catch and remove the welcome modal whenever it appears
		const removeWelcomeModal = () => {
			try {
				const modal = document.querySelector('.components-modal__screen-overlay');
				if (modal) {
					modal.remove();
					return true;
				}
				return false;
			} catch(e) {
				return false;
			}
		};
		
		// Check immediately and at intervals
		const REMOVAL_CHECK_INTERVALS = [50, 150, 300, 500, 1000];
		REMOVAL_CHECK_INTERVALS.forEach((delay) => {
			setTimeout(removeWelcomeModal, delay);
		});
		
		// Set up MutationObserver to catch it if it appears later
		const observer = new MutationObserver(() => {
			removeWelcomeModal();
		});
		
		// Observe the body for any changes
		observer.observe(document.body, {
			childList: true,
			subtree: true
		});
	});
} )();
JS;

		// Post/page editor.
		if ( wp_script_is( 'wp-edit-post', 'enqueued' ) ) {
			wp_add_inline_script( 'wp-edit-post', $script, 'before' );
		}

		// Site editor (FSE).
		if ( wp_script_is( 'wp-edit-site', 'enqueued' ) ) {
			wp_add_inline_script( 'wp-edit-site', $script, 'before' );
		}
	}

	/**
	 * Hide the Gutenberg footer by injecting CSS into the block editor
	 *
	 * @return void
	 */
	public function hide_footer(): void {
		$css = '
			/* Hide Site Editor breadcrumb footer */
			.interface-interface-skeleton__footer,
			.block-editor-block-breadcrumb {
				display: none !important;
			}
			/* Remove bottom padding/margin from editor skeleton */
			.interface-interface-skeleton__body,
			.interface-navigable-region.interface-interface-skeleton__content {
				padding-bottom: 0 !important;
				margin-bottom: 0 !important;
			}
		';

		wp_add_inline_style( 'wp-edit-blocks', $css );
	}
}
