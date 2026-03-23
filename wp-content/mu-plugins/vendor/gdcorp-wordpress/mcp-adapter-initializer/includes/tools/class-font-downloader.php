<?php
/**
 * Font Downloader Helper Class
 *
 * @package mcp-adapter-initializer
 */

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Font Downloader Helper
 *
 * Handles downloading Google Fonts from CSS URLs and converting them to local file paths.
 */
class Font_Downloader {

	/**
	 * Maximum number of attempts for downloads
	 *
	 * @var int
	 */
	private const MAX_ATTEMPTS = 3;

	/**
	 * Initial retry delay in seconds
	 *
	 * @var int
	 */
	private const INITIAL_RETRY_DELAY = 1;

	/**
	 * Download fonts from Google Fonts URLs and update font families array
	 *
	 * @param array $font_families Font families array from global styles.
	 * @return array Modified font families array with local file paths.
	 */
	public function process_font_families( array $font_families ): array {
		// Ensure WordPress file functions are available
		if ( ! function_exists( 'download_url' ) ) {
			require_once 'wp-admin/includes/file.php';
		}

		if ( empty( $font_families['theme'] ) || ! is_array( $font_families['theme'] ) ) {
			return $font_families;
		}

		foreach ( $font_families['theme'] as &$font_family ) {
			if ( empty( $font_family['fontFace'] ) || ! is_array( $font_family['fontFace'] ) ) {
				continue;
			}

			$font_slug = $font_family['slug'] ?? 'unknown';
			$font_name = $font_family['name'] ?? 'Unknown';

			foreach ( $font_family['fontFace'] as &$font_face ) {
				if ( empty( $font_face['src'] ) || ! is_array( $font_face['src'] ) ) {
					continue;
				}

				$src_url = $font_face['src'][0] ?? '';

				// Skip if not a Google Fonts URL
				if ( ! $this->is_google_fonts_url( $src_url ) ) {
					continue;
				}

				// Download the font and get local path
				$local_path = $this->download_google_font(
					$src_url,
					$font_slug,
					$font_face['fontWeight'] ?? '400',
					$font_face['fontStyle'] ?? 'normal'
				);

				if ( $local_path ) {
					// Update src to use local file path
					$font_face['src'] = array( $local_path );
				} else {
					error_log(
						sprintf(
							'[Font Downloader] Failed to download %s (%s %s) from %s',
							$font_name,
							$font_face['fontWeight'] ?? '400',
							$font_face['fontStyle'] ?? 'normal',
							$src_url
						)
					);
				}
			}
		}

		return $font_families;
	}

	/**
	 * Check if URL is a Google Fonts URL
	 *
	 * @param string $url URL to check.
	 * @return bool True if Google Fonts URL.
	 */
	private function is_google_fonts_url( string $url ): bool {
		return strpos( $url, 'fonts.googleapis.com' ) !== false;
	}

	/**
	 * Download a Google Font and return local file path
	 *
	 * @param string $google_fonts_css_url Google Fonts CSS URL.
	 * @param string $font_slug            Font slug for filename.
	 * @param string $weight               Font weight.
	 * @param string $style                Font style.
	 * @return string|null Local file path or null on failure.
	 */
	private function download_google_font( string $google_fonts_css_url, string $font_slug, string $weight, string $style ): ?string {
		// Parse Google Fonts CSS to get actual font file URL
		$font_file_url = $this->parse_google_fonts_css( $google_fonts_css_url );

		if ( ! $font_file_url ) {
			error_log( '[Font Downloader] Failed to parse Google Fonts CSS: ' . $google_fonts_css_url );
			return null;
		}

		// Generate filename
		$filename = $this->generate_filename( $font_slug, $weight, $style, $font_file_url );

		// Get WordPress fonts directory
		$font_dir = wp_font_dir();
		if ( ! $font_dir || ! isset( $font_dir['path'] ) ) {
			error_log( '[Font Downloader] Could not get WordPress font directory' );
			return null;
		}

		$font_dir_path  = $font_dir['path'];
		$font_file_path = trailingslashit( $font_dir_path ) . $filename;

		// Check if font already exists
		if ( file_exists( $font_file_path ) ) {
			// Return relative path using file:./ prefix (WordPress standard for portability)
			// This path is relative to theme directory and resolved dynamically on page load
			return 'file:./../../uploads/fonts/' . $filename;
		}

		// Download the font file with retry logic
		$downloaded_file = $this->download_with_retry( $font_file_url );

		if ( ! $downloaded_file ) {
			return null;
		}

		// Ensure fonts directory exists
		if ( ! file_exists( $font_dir_path ) ) {
			wp_mkdir_p( $font_dir_path );
		}

		// Move downloaded file to fonts directory
		$moved = @rename( $downloaded_file, $font_file_path );

		if ( ! $moved ) {
			// Fallback: try copy + delete
			$moved = copy( $downloaded_file, $font_file_path );
			if ( $moved ) {
				@unlink( $downloaded_file );
			}
		}

		if ( ! $moved ) {
			error_log( '[Font Downloader] Failed to move font file to: ' . $font_file_path );
			@unlink( $downloaded_file );
			return null;
		}

		// Validate the downloaded file
		if ( ! $this->validate_font_file( $font_file_path ) ) {
			error_log( '[Font Downloader] Font file validation failed: ' . $filename );
			@unlink( $font_file_path );
			return null;
		}

		// Return relative path using file:./ prefix (WordPress standard for portability)
		// This path is relative to theme directory and resolved dynamically on page load
		return 'file:./../../uploads/fonts/' . $filename;
	}

	/**
	 * Parse Google Fonts CSS to extract actual font file URL
	 *
	 * @param string $css_url Google Fonts CSS URL.
	 * @return string|null Font file URL or null on failure.
	 */
	private function parse_google_fonts_css( string $css_url ): ?string {
		// Fetch the CSS content
		$response = wp_remote_get(
			$css_url,
			array(
				'timeout'    => 10,
				'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( '[Font Downloader] Failed to fetch Google Fonts CSS: ' . $response->get_error_message() );
			return null;
		}

		$css_content = wp_remote_retrieve_body( $response );

		if ( empty( $css_content ) ) {
			return null;
		}

		// Extract font file URL from CSS
		// Google Fonts returns multiple @font-face rules with unicode-range subsetting.
		// We want the Latin subset (U+0000-00FF) which covers English and basic Latin characters.
		// This is typically the last @font-face rule in the response.

		// Try to find the Latin subset specifically (look for comment or unicode-range)
		if ( preg_match( '/\/\*\s*latin\s*\*\/.*?url\((https:\/\/fonts\.gstatic\.com\/[^)]+\.woff2)\)/s', $css_content, $matches ) ) {
			return $matches[1];
		}

		// Fallback: Get all woff2 URLs and return the last one (usually Latin subset)
		if ( preg_match_all( '/url\((https:\/\/fonts\.gstatic\.com\/[^)]+\.woff2)\)/', $css_content, $matches ) ) {
			// Return the last match (Latin subset is typically last)
			return end( $matches[1] );
		}

		// Final fallback: try to find any woff2 URL
		if ( preg_match( '/url\((https:\/\/[^)]+\.woff2)\)/', $css_content, $matches ) ) {
			return $matches[1];
		}

		return null;
	}

	/**
	 * Download file with retry logic and exponential backoff
	 *
	 * @param string $url URL to download.
	 * @return string|null Path to downloaded file or null on failure.
	 */
	private function download_with_retry( string $url ): ?string {
		$attempt = 0;
		$delay   = self::INITIAL_RETRY_DELAY;

		while ( $attempt < self::MAX_ATTEMPTS ) {
			++$attempt;

			$temp_file = download_url( $url, 30 ); // 30 second timeout

			if ( ! is_wp_error( $temp_file ) ) {
				return $temp_file;
			}

			$error_message = $temp_file->get_error_message();

			// Don't retry if it's a 404 or similar permanent error
			if ( strpos( $error_message, '404' ) !== false || strpos( $error_message, '403' ) !== false ) {
				return null;
			}

			if ( $attempt < self::MAX_ATTEMPTS ) {
				sleep( $delay );
				$delay *= 2; // Exponential backoff
			}
		}

		return null;
	}

	/**
	 * Generate filename for font file
	 *
	 * @param string $font_slug Font slug.
	 * @param string $weight    Font weight.
	 * @param string $style     Font style.
	 * @param string $url       Font file URL (to extract extension).
	 * @return string Filename.
	 */
	private function generate_filename( string $font_slug, string $weight, string $style, string $url ): string {
		// Extract extension from URL
		$extension = 'woff2'; // Default
		if ( preg_match( '/\.([a-z0-9]+)(\?|$)/i', $url, $matches ) ) {
			$extension = $matches[1];
		}

		// Sanitize components
		$font_slug = sanitize_file_name( $font_slug );
		$weight    = sanitize_file_name( $weight );
		$style     = sanitize_file_name( $style );

		return sprintf( '%s-%s-%s.%s', $font_slug, $weight, $style, $extension );
	}

	/**
	 * Validate that the downloaded file is a valid font file
	 *
	 * @param string $file_path Path to font file.
	 * @return bool True if valid.
	 */
	private function validate_font_file( string $file_path ): bool {
		// Check file exists and is readable
		if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
			return false;
		}

		// Check file size (fonts should be at least 1KB, max 5MB)
		$file_size = filesize( $file_path );
		if ( $file_size < 1024 || $file_size > 5 * 1024 * 1024 ) {
			return false;
		}

		// Check file signature for WOFF2 format
		$handle = fopen( $file_path, 'rb' );
		if ( ! $handle ) {
			return false;
		}

		$signature = fread( $handle, 4 );
		fclose( $handle );

		// WOFF2 signature: "wOF2" (0x774F4632)
		if ( 'wOF2' === $signature ) {
			return true;
		}

		// WOFF signature: "wOFF" (0x774F4646)
		if ( 'wOFF' === $signature ) {
			return true;
		}

		// TTF/OTF signatures
		$first_bytes = unpack( 'N', $signature );
		if ( $first_bytes && in_array( $first_bytes[1], array( 0x00010000, 0x4F54544F ), true ) ) {
			return true;
		}

		return false;
	}
}
