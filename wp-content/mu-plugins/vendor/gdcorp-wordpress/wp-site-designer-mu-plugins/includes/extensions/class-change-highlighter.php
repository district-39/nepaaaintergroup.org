<?php
/**
 * Change Highlighter Plugin
 *
 * @package wp-site-designer-mu-plugins
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Extensions;

/**
 * Class Change_Highlighter
 *
 * Handles highlighting of changed content blocks in the editor and frontend.
 */
class Change_Highlighter {

	/**
	 * Initialize the Change Highlighter extension
	 *
	 * @return void
	 */
	public static function init(): void {
		$instance = new self();

		// Enqueue for Gutenberg editor only.
		add_action( 'enqueue_block_editor_assets', array( $instance, 'enqueue_highlighter_admin' ) );

		// Enqueue for frontend.
		add_action( 'wp_enqueue_scripts', array( $instance, 'enqueue_highlighter_frontend' ) );
	}

	/**
	 * Enqueue the Change Highlighter JavaScript for the block editor
	 *
	 * @return void
	 */
	public function enqueue_highlighter_admin(): void {
		// Ensure we are in admin context.
		if ( ! is_admin() ) {
			return;
		}

		wp_add_inline_script( 'wp-blocks', $this->get_highlighter_script() );
	}

	/**
	 * Enqueue the Change Highlighter JavaScript for the frontend
	 *
	 * @return void
	 */
	public function enqueue_highlighter_frontend(): void {
		// Make it more clear for CoPilot to have better understanding.
		if ( wp_script_is( 'wp-dom-ready', 'enqueued' ) ) {
			// If 'wp-dom-ready' is available, use it; otherwise check for 'jquery'.
			$handle = 'wp-dom-ready';
		} elseif ( wp_script_is( 'jquery', 'enqueued' ) ) {
			// If 'jquery' is available, use it, and if not, register a minimal handle.
			$handle = 'jquery';
		} else {
			// Neither is available, register a minimal handle.
			wp_register_script( 'site-designer-change-highlighter', '', array(), GDMU_SITE_DESIGNER_VERSION, true );
			wp_enqueue_script( 'site-designer-change-highlighter' );
			$handle = 'site-designer-change-highlighter';
		}

		// Add the highlighter script inline to the determined handle.
		wp_add_inline_script( $handle, $this->get_highlighter_script() );
	}

	/**
	 * Get the Change Highlighter JavaScript code
	 *
	 * @return string The JavaScript code.
	 */
	private function get_highlighter_script(): string {
		$script = <<<'JS'
(function () {
				// Store active highlights
				let activeHighlights = [];

				// Observers for efficient position tracking
				let resizeObserver = null;
				let intersectionObserver = null;

				// Color scheme for different change types
				const COLORS = {
					'modified': '#ff6b00', // Brighter orange
					'added': '#00e676', // Brighter green
					'removed': '#ff1744' // Brighter red
				};

				// Add CSS for animations and overlays
				function injectStyles() {
					if (document.getElementById('change-highlighter-styles')) {
						return;
					}

					const style = document.createElement('style');
					style.id = 'change-highlighter-styles';
					style.textContent = `
					@keyframes flash-highlight {
						0%, 100% { opacity: 0.8; }
						50% { opacity: 0.3; }
					}

					.change-highlight-overlay {
						position: absolute;
						pointer-events: none;
						z-index: 999999;
						box-sizing: border-box;
						animation: flash-highlight 2s ease-in-out;
						animation-fill-mode: forwards;
						transition: opacity 0.3s ease;
					}

					.change-highlight-overlay.flash-complete {
						opacity: 0.3 !important;
						animation: none;
					}

					.change-highlighter-dimmed {
						opacity: 0.75 !important;
						transition: opacity 0.3s ease;
					}
				`;
					document.head.appendChild(style);
				}

				// Find element by selector and optional blockIndex
				function findElementBySelector(selector, blockIndex) {
					try {
						// If blockIndex is provided, find the nth occurrence
						if (typeof blockIndex === 'number' && blockIndex >= 0) {
							const elements = document.querySelectorAll(selector);

							if (elements.length > blockIndex) {
								return elements[blockIndex];
							} else {
								console.warn(`Block not found at index ${ blockIndex } for selector "${ selector }". Found ${ elements.length } elements.`);
								return null;
							}
						}

						// Otherwise, return the first match
						return document.querySelector(selector);
					} catch (e) {
						console.error(`Error finding element with selector "${ selector }":`, e);
						return null;
					}
				}

				// Find element by content matching
				function findElementByContent(blockType, content, blockIndex) {
					if (!content || !blockType) {
						return null;
					}

					// Try to find blocks of the same type
					const possibleSelectors = [
						`[data-type="${ blockType }"]`,
						`.wp-block-${ blockType.replace('/', '-') }`,
						`[class*="${ blockType }"]`
					];

					for (const selector of possibleSelectors) {
						const elements = document.querySelectorAll(selector);

						// If blockIndex is provided, try that first
						if (typeof blockIndex === 'number' && elements[blockIndex]) {
							return elements[blockIndex];
						}

						// Otherwise, try content matching
						for (const element of elements) {
							const elementText = element.textContent.trim();
							const contentText = content.trim();

							if (elementText.includes(contentText) || contentText.includes(elementText)) {
								return element;
							}
						}
					}

					return null;
				}

				// Get element position and dimensions
				function getElementBounds(element) {
					const rect = element.getBoundingClientRect();
					const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
					const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

					return {
						top: rect.top + scrollTop,
						left: rect.left + scrollLeft,
						width: rect.width,
						height: rect.height
					};
				}

				// Create highlight overlay
				function createHighlight(element, changeType) {
					const bounds = getElementBounds(element);
					const color = COLORS[changeType] || COLORS['modified'];

					const overlay = document.createElement('div');
					overlay.className = 'change-highlight-overlay';
					overlay.style.top = bounds.top + 'px';
					overlay.style.left = bounds.left + 'px';
					overlay.style.width = bounds.width + 'px';
					overlay.style.height = bounds.height + 'px';
					overlay.style.border = `4px solid ${ color }`;
					overlay.style.boxShadow = `0 0 20px ${ color }, 0 0 40px ${ color }, inset 0 0 20px ${ color }`;

					document.body.appendChild(overlay);

					// After animation completes, fade to 30% opacity
					setTimeout(() => {
						overlay.classList.add('flash-complete');
					}, 2000);

					return {
						element: element,
						overlay: overlay,
						changeType: changeType
					};
				}

				// Dim all non-highlighted elements
				function dimNonHighlightedElements() {
					// Get all highlighted elements
					const highlightedElements = new Set(
						activeHighlights.map(h => h.element)
					);

					// Build set of elements that should NOT be dimmed:
					// 1. Highlighted elements themselves
					// 2. Direct ancestors of highlighted elements (to keep them visible)
					const excludeFromDimming = new Set();

					highlightedElements.forEach(highlighted => {
						// Don't dim the highlighted element
						excludeFromDimming.add(highlighted);

						// Don't dim direct ancestors (parent chain to body)
						let parent = highlighted.parentElement;
						while (parent && parent !== document.body) {
							excludeFromDimming.add(parent);
							parent = parent.parentElement;
						}
					});

					// Now dim everything else - use a broad selector to catch all visible elements
					const allElements = document.querySelectorAll('body *:not(script):not(style):not(.change-highlight-overlay)');

					allElements.forEach(element => {
						// Skip if this element should not be dimmed
						if (excludeFromDimming.has(element)) {
							return;
						}

						// Skip if this is a child of a highlighted element (keep content inside highlights visible)
						let shouldSkip = false;
						for (const highlighted of highlightedElements) {
							if (highlighted.contains(element)) {
								shouldSkip = true;
								break;
							}
						}

						if (!shouldSkip) {
							element.classList.add('change-highlighter-dimmed');
						}
					});
				}

				// Restore opacity of all dimmed elements
				function restoreDimmedElements() {
					const dimmedElements = document.querySelectorAll('.change-highlighter-dimmed');
					dimmedElements.forEach(element => {
						element.classList.remove('change-highlighter-dimmed');
					});
				}

				// Initialize observers
				function initObservers() {
					// ResizeObserver: Update overlay dimensions when elements resize
					resizeObserver = new ResizeObserver((entries) => {
						for (const entry of entries) {
							// Find the highlight for this element
							const highlight = activeHighlights.find(h => h.element === entry.target);
							if (highlight && highlight.overlay) {
								const bounds = getElementBounds(entry.target);
								highlight.overlay.style.width = bounds.width + 'px';
								highlight.overlay.style.height = bounds.height + 'px';
							}
						}
					});

					// IntersectionObserver: Update overlay positions when elements scroll
					intersectionObserver = new IntersectionObserver((entries) => {
						for (const entry of entries) {
							// Find the highlight for this element
							const highlight = activeHighlights.find(h => h.element === entry.target);
							if (highlight && highlight.overlay) {
								const bounds = getElementBounds(entry.target);
								highlight.overlay.style.top = bounds.top + 'px';
								highlight.overlay.style.left = bounds.left + 'px';
							}
						}
					}, {
						root: null, // Use viewport as root
						threshold: [ 0, 0.25, 0.5, 0.75, 1 ] // Fire at multiple visibility thresholds
					});
				}

				// Disconnect observers
				function disconnectObservers() {
					if (resizeObserver) {
						resizeObserver.disconnect();
					}
					if (intersectionObserver) {
						intersectionObserver.disconnect();
					}
				}

				// Clear all highlights
				function clearHighlights() {
					activeHighlights.forEach(highlight => {
						if (highlight.overlay && highlight.overlay.parentNode) {
							highlight.overlay.parentNode.removeChild(highlight.overlay);
						}
					});
					activeHighlights = [];

					// Disconnect observers
					disconnectObservers();

					// Restore dimmed elements
					restoreDimmedElements();
				}

				// Handle highlight-changes message
				function handleHighlightChanges(changes) {
					if (!Array.isArray(changes) || changes.length === 0) {
						return;
					}

					// Clear existing highlights
					clearHighlights();

					// Inject styles if not already present
					injectStyles();

					// Initialize observers
					initObservers();

					let firstElement = null;

					// Create highlights for each change
					changes.forEach((change, index) => {
						let element = null;

						// STEP 1: Try position-based lookup (discriminated union)
						if (change.position) {
							// Check if it's the new format (has type field)
							if (change.position.type === 'id') {
								// ID-based: Most reliable, direct lookup
								element = document.getElementById(change.position.id);

								if (!element) {
									console.warn('Element not found by ID:', change.position.id);
								}
							} else if (change.position.type === 'selector') {
								// Selector-based: Use existing helper function
								element = findElementBySelector(
									change.position.selector,
									change.position.blockIndex
								);
							} else if (change.position.selector) {
								// Old format (no type field) - treat as selector-based for backward compatibility
								element = findElementBySelector(
									change.position.selector,
									change.position.blockIndex
								);
							}
						}

						// STEP 2: Fallback to content matching
						if (!element && change.newContent) {
							element = findElementByContent(
								change.blockType,
								change.newContent,
								change.position?.blockIndex // May be undefined for ID-based
							);
						}

						// STEP 3: Fallback to blockId selector
						if (!element && change.blockId) {
							element = findElementBySelector(`[data-block="${ change.blockId }"]`) ||
								findElementBySelector(`#block-${ change.blockId }`) ||
								findElementBySelector(`[id*="${ change.blockId }"]`);
						}

						// STEP 4: Create highlight if element found
						if (element) {
							const highlight = createHighlight(element, change.changeType);
							activeHighlights.push(highlight);

							// Observe element for size and position changes
							if (resizeObserver) {
								resizeObserver.observe(element);
							}
							if (intersectionObserver) {
								intersectionObserver.observe(element);
							}

							// Store first element for scrolling
							if (index === 0) {
								firstElement = element;
							}
						} else {
							// Enhanced logging for debugging
							console.warn('Could not find element for change:', {
								blockId: change.blockId,
								blockType: change.blockType,
								positionType: change.position?.type,
								positionId: change.position?.id,
								positionSelector: change.position?.selector,
								positionBlockIndex: change.position?.blockIndex
							});
						}
					});

					// Dim non-highlighted elements
					if (activeHighlights.length > 0) {
						dimNonHighlightedElements();
					}

					// Scroll first changed block into view
					if (firstElement) {
						setTimeout(() => {
							firstElement.scrollIntoView({
								behavior: 'smooth',
								block: 'center',
								inline: 'center'
							});
						}, 100);
					}
				}

				// Listen for postMessage events
				window.addEventListener('message', function (event) {
					const data = event.data;

					// Handle different message types
					if (data && typeof data === 'object') {
						switch (data.type) {
							case 'highlight-changes':
								if (data.changes) {
									handleHighlightChanges(data.changes);
								}
								break;

							case 'clear-highlights':
								clearHighlights();
								break;
						}
					}
				});
			})();
JS;

		return $script;
	}
}
