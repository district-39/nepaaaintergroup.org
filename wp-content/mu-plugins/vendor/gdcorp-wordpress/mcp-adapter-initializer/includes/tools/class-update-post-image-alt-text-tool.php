<?php
/**
 * Update Post Image Alt Text Tool Class
 *
 * @package     mcp-adapter-initializer
 * @author      GoDaddy
 * @copyright   2025 GoDaddy
 * @license     GPL-2.0-or-later
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update Post Image Alt Text Tool
 *
 * Handles the registration and execution of the update post image alt text ability
 * for the MCP adapter. Updates the alt text attribute for a specific image block
 * within a post's content.
 */
class Update_Post_Image_Alt_Text_Tool extends Base_Tool {

	/**
	 * Tool identifier
	 *
	 * @var string
	 */
	const TOOL_ID = 'gd-mcp/update-post-image-alt-text';

	/**
	 * Tool instance
	 *
	 * @var Update_Post_Image_Alt_Text_Tool|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Update_Post_Image_Alt_Text_Tool
	 */
	public static function get_instance(): Update_Post_Image_Alt_Text_Tool {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {}

	/**
	 * Register the update post image alt text ability
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Post Image Alt Text', 'mcp-adapter-initializer' ),
				'description'         => __( 'Updates the alternative text for a specific image block within a WordPress post', 'mcp-adapter-initializer' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute_with_admin' ),
				'permission_callback' => '__return_true',
				'category'            => 'content-management',
			)
		);
	}

	/**
	 * Get the tool identifier
	 *
	 * @return string
	 */
	public function get_tool_id(): string {
		return self::TOOL_ID;
	}

	/**
	 * Get input schema for the tool
	 *
	 * @return array
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'post_id'   => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post containing the image', 'mcp-adapter-initializer' ),
					'minimum'     => 1,
				),
				'image_src' => array(
					'type'        => 'string',
					'description' => __( 'The source URL of the image to update', 'mcp-adapter-initializer' ),
				),
				'alt'       => array(
					'type'        => 'string',
					'description' => __( 'The new alternative text for the image', 'mcp-adapter-initializer' ),
				),
			),
			'required'   => array( 'post_id', 'image_src', 'alt' ),
		);
	}

	/**
	 * Get output schema for the tool
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Post image alt text update result', 'mcp-adapter-initializer' ),
			array(
				'post_id'   => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the updated post', 'mcp-adapter-initializer' ),
				),
				'image_src' => array(
					'type'        => 'string',
					'description' => __( 'The source URL of the image that was updated', 'mcp-adapter-initializer' ),
				),
			)
		);
	}

	/**
	 * Execute the update post image alt text tool
	 *
	 * @param array $input Input parameters
	 * @return array Update result or error
	 */
	public function execute( array $input ): array {
		// Validate required parameters
		if ( empty( $input['post_id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Post ID is required', 'mcp-adapter-initializer' ),
			);
		}

		if ( empty( $input['image_src'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Image source URL is required', 'mcp-adapter-initializer' ),
			);
		}

		if ( ! isset( $input['alt'] ) || '' === trim( $input['alt'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Alt text is required', 'mcp-adapter-initializer' ),
			);
		}

		$post_id   = (int) $input['post_id'];
		$image_src = esc_url_raw( trim( $input['image_src'] ) );
		$alt       = trim( $input['alt'] );

		// Check if post exists
		$post = get_post( $post_id );
		if ( ! $post ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Post with ID %d not found', 'mcp-adapter-initializer' ), $post_id ),
			);
		}

		// Parse the post content into blocks
		$blocks  = parse_blocks( $post->post_content );
		$updated = false;

		$normalized_image_src = $this->normalize_url( $image_src );

		// Process all blocks to find and update the image
		$updated = $this->find_and_update_image_block( $blocks, $image_src, $normalized_image_src, $alt );

		// Check if the image block was found
		if ( ! $updated ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Image with source URL "%1$s" not found in post %2$d', 'mcp-adapter-initializer' ), $image_src, $post_id ),
			);
		}

		// Update the post with modified content
		$result = wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => serialize_blocks( $blocks ),
			),
			true
		);

		// Check for errors
		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to update post: %s', 'mcp-adapter-initializer' ), $result->get_error_message() ),
			);
		}

		return array(
			'success'   => true,
			'post_id'   => $post_id,
			'image_src' => $image_src,
			'message'   => __( 'Image alt text updated successfully', 'mcp-adapter-initializer' ),
		);
	}

	/**
	 * Find and update image block with matching URL (recursive)
	 *
	 * Recursively searches through blocks to find an image block with a URL
	 * matching the provided image source, then updates its alt text.
	 *
	 * @param array  &$blocks The blocks array to search through (passed by reference)
	 * @param string $image_src The original image source URL to match
	 * @param string $normalized_image_src The normalized version of the image source URL
	 * @param string $alt The new alt text to set
	 * @return bool True if a matching image block was found and updated, false otherwise
	 */
	private function find_and_update_image_block( array &$blocks, string $image_src, string $normalized_image_src, string $alt ): bool {
		foreach ( $blocks as &$block ) {
			$block_name = $block['blockName'] ?? null;
			// Skip non-image blocks but process their inner blocks
			if ( 'core/image' !== $block_name ) {
				if ( ! empty( $block['innerBlocks'] ) ) {
					if ( $this->find_and_update_image_block( $block['innerBlocks'], $image_src, $normalized_image_src, $alt ) ) {
						return true;
					}
				}
				continue;
			}

			// Check block attrs URL (new block format)
			if ( $this->check_and_update_block_attrs_url( $block, $image_src, $normalized_image_src, $alt ) ) {
				return true;
			}

			// Check innerHTML src attribute (classic block format)
			if ( $this->check_and_update_html_src( $block, $image_src, $normalized_image_src, $alt ) ) {
				return true;
			}

			// Check srcset attribute (responsive images)
			if ( $this->check_and_update_srcset( $block, $image_src, $normalized_image_src, $alt ) ) {
				return true;
			}

			// Recursively process inner blocks
			if ( ! empty( $block['innerBlocks'] ) ) {
				if ( $this->find_and_update_image_block( $block['innerBlocks'], $image_src, $normalized_image_src, $alt ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check and update block if attrs URL matches
	 *
	 * Checks if the block's attrs contain a URL that matches the target image source.
	 * This handles the new block editor format where image URLs are stored in block attributes.
	 *
	 * @param array  &$block The block to check (passed by reference)
	 * @param string $image_src The original image source URL to match
	 * @param string $normalized_image_src The normalized version of the image source URL
	 * @param string $alt The new alt text to set
	 * @return bool True if the URL matched and block was updated, false otherwise
	 */
	private function check_and_update_block_attrs_url( array &$block, string $image_src, string $normalized_image_src, string $alt ): bool {
		if ( empty( $block['attrs']['url'] ) ) {
			return false;
		}

		$block_url            = $block['attrs']['url'];
		$normalized_block_url = $this->normalize_url( $block_url );

		if ( $block_url === $image_src || $normalized_block_url === $normalized_image_src ) {
			$this->update_block_alt_text( $block, $alt );
			return true;
		}

		return false;
	}

	/**
	 * Check and update block if innerHTML src attribute matches
	 *
	 * Extracts the src attribute from the block's innerHTML and checks if it matches
	 * the target image source. This handles the classic block format where the image
	 * is stored as raw HTML.
	 *
	 * @param array  &$block The block to check (passed by reference)
	 * @param string $image_src The original image source URL to match
	 * @param string $normalized_image_src The normalized version of the image source URL
	 * @param string $alt The new alt text to set
	 * @return bool True if the src matched and block was updated, false otherwise
	 */
	private function check_and_update_html_src( array &$block, string $image_src, string $normalized_image_src, string $alt ): bool {
		if ( empty( $block['innerHTML'] ) ) {
			return false;
		}

		// Extract src from img tag
		if ( ! preg_match( '/src="([^"]+)"/', $block['innerHTML'], $matches ) ) {
			return false;
		}

		$html_src            = $matches[1];
		$normalized_html_src = $this->normalize_url( $html_src );

		if ( $html_src === $image_src || $normalized_html_src === $normalized_image_src ) {
			$this->update_block_alt_text( $block, $alt );
			return true;
		}

		return false;
	}

	/**
	 * Check and update block if srcset attribute contains matching URL
	 *
	 * Checks if the block's innerHTML contains a srcset attribute that includes
	 * the target image source. The srcset attribute contains multiple image URLs
	 * for responsive images (format: "url1 size1, url2 size2, ...").
	 *
	 * @param array  &$block The block to check (passed by reference)
	 * @param string $image_src The original image source URL to match
	 * @param string $normalized_image_src The normalized version of the image source URL
	 * @param string $alt The new alt text to set
	 * @return bool True if the srcset contained the URL and block was updated, false otherwise
	 */
	private function check_and_update_srcset( array &$block, string $image_src, string $normalized_image_src, string $alt ): bool {
		if ( empty( $block['innerHTML'] ) ) {
			return false;
		}

		// Extract srcset from img tag
		if ( ! preg_match( '/srcset="([^"]+)"/', $block['innerHTML'], $matches ) ) {
			return false;
		}

		$srcset = $matches[1];

		// Parse srcset into individual candidates ("url size, url size, ...")
		$candidates = array_map( 'trim', explode( ',', $srcset ) );

		foreach ( $candidates as $candidate ) {
			if ( '' === $candidate ) {
				continue;
			}

			// Split on whitespace to separate URL from any size descriptor (e.g., "300w", "2x").
			$parts = preg_split( '/\s+/', $candidate );
			if ( empty( $parts ) || empty( $parts[0] ) ) {
				continue;
			}

			$candidate_url            = $parts[0];
			$normalized_candidate_url = $this->normalize_url( $candidate_url );

			if ( $candidate_url === $image_src || $normalized_candidate_url === $normalized_image_src ) {
				$this->update_block_alt_text( $block, $alt );
				return true;
			}
		}

		return false;
	}

	/**
	 * Normalize URL for comparison by removing size suffixes
	 *
	 * Removes size suffixes like -300x200, -150x150, etc., before any file extension.
	 * This allows matching images regardless of which size variant is being used.
	 *
	 * @param string $url The URL to normalize
	 * @return string The normalized URL
	 */
	private function normalize_url( string $url ): string {
		// Remove query string and fragment so the file extension is at the end of the string.
		$base_url = preg_split( '/[?#]/', $url, 2 )[0];

		return preg_replace( '/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', $base_url );
	}

	/**
	 * Update the alt text in a block
	 *
	 * @param array  &$block The block to update (passed by reference)
	 * @param string $alt The new alt text
	 */
	private function update_block_alt_text( array &$block, string $alt ): void {
		// Update attrs alt (for block editor)
		$block['attrs']['alt'] = $alt;

		// Update innerHTML alt attribute if present
		if ( ! empty( $block['innerHTML'] ) ) {
			// Check if alt attribute already exists
			if ( strpos( $block['innerHTML'], 'alt=' ) !== false ) {
				// Replace existing alt attribute
				$block['innerHTML'] = preg_replace(
					'/(<img[^>]+\s)alt="[^"]*"/',
					'${1}alt="' . esc_attr( $alt ) . '"',
					$block['innerHTML']
				);
			} else {
				// Add alt attribute if it doesn't exist (after opening <img tag)
				$block['innerHTML'] = preg_replace(
					'/(<img\b)/',
					'$1 alt="' . esc_attr( $alt ) . '"',
					$block['innerHTML']
				);
			}

			// Also update innerContent if it exists
			if ( ! empty( $block['innerContent'][0] ) ) {
				$block['innerContent'][0] = $block['innerHTML'];
			}
		}
	}

	/**
	 * Prevent cloning
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton' );
	}
}
