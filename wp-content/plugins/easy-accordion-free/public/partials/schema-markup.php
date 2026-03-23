<?php
/**
 * The Accordion FAQs schema markup template.
 *
 * @package easy_accordion_free
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! function_exists( 'sp_clean_schema' ) ) {
	/**
	 * Sp_clean_schema clean schema function.
	 *
	 * @param  mixed $string string.
	 * @return statement
	 */
	function sp_clean_schema( $string ) {
		$allowed_tags = array(
			'h1'     => array(),
			'h2'     => array(),
			'h3'     => array(),
			'h4'     => array(),
			'h5'     => array(),
			'h6'     => array(),
			'br'     => array(),
			'ol'     => array(),
			'ul'     => array(),
			'li'     => array(),
			// 'a'      => array(
			// 'href'  => array(),
			// 'title' => array(),
			// ),
			'p'      => array(),
			'div'    => array(),
			'b'      => array(),
			'strong' => array(),
			'i'      => array(),
			'em'     => array(),
		);

		// Allow custom tags.
		$allowed_tags = apply_filters( 'sp_eap_schema_allowed_tags', $allowed_tags );

		$string = strip_shortcodes( $string );
		$string = wp_kses( $string, $allowed_tags );
		// Remove commented codes.
		$string = preg_replace( '/<!--.*?-->/', '', $string );
		$string = preg_replace( '/\s+/', ' ', $string );
		$string = trim( $string );
		$string = str_replace( '"', "'", $string );

		return $string;
	}
}

if ( ! function_exists( 'schema_markup' ) ) {
	/**
	 * Schema_markup
	 *
	 * @param  mixed $title title of the accordion.
	 * @param  mixed $content accordion content.
	 * @return statement
	 */
	function schema_markup( $title = null, $content = null ) {
		$title   = $title ? esc_html( $title ) : '-';
		$content = $content ? sp_clean_schema( $content ) : '';
		$content = $content ? $content : '-';
		$markup  = '{
			"@type": "Question",
			"name": "' . $title . '",
			"acceptedAnswer": {
				"@type": "Answer",
				"text": "' . $content . '"
			}
		}';
		return $markup;
	}
}

if ( ! function_exists( 'minify_markup' ) ) {
	/**
	 * Minify the provided HTML markup by removing unnecessary whitespace.
	 *
	 * @param string $markup The HTML markup to minify.
	 * @return string The minified HTML markup.
	 */
	function minify_markup( $markup ) {
		$markup = preg_replace( array( '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/\s*(<[^>]+>)\s*/' ), array( '>', '<', ' ', '$1' ), $markup );

		return trim( $markup );
	}
}

if ( $eap_schema_markup ) {
	$unique_id = 'sp-ea-schema-' . $post_id . '-' . uniqid();

	$markup = '<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
		"@id": "' . esc_attr( $unique_id ) . '",
        "mainEntity": [';

	if ( 'content-accordion' === $accordion_type && is_array( $content_sources ) ) {
		$content_count = count( $content_sources );
		foreach ( $content_sources as $keys => $content_source ) {
			$accordion_title     = $content_source['accordion_content_title'] ? $content_source['accordion_content_title'] : '';
			$content_description = $content_source['accordion_content_description'] ? $content_source['accordion_content_description'] : '';

			$markup .= schema_markup( $accordion_title, $content_description ); // Escaped inside the function.
			if ( $keys + 1 !== $content_count ) {
				$markup .= ',';
			}
		}
	} elseif ( 'post-accordion' === $accordion_type ) {
		if ( $post_query->have_posts() ) {
			$post_count = 0;
			while ( $post_query->have_posts() ) {
				$post_query->the_post();

				$accordion_title = get_the_title();
				$content_main    = get_the_content();

				$markup .= schema_markup( $accordion_title, $content_main ); // Escaped inside the function.

				++$post_count;
				if ( $post_count < $post_query->found_posts ) {
					$markup .= ',';
				}
			}
			wp_reset_postdata();
		}
	}

	$markup .= ']
    }
    </script>';
	// Output the minified markup, ensuring only one instance is printed.
	if ( ! isset( $GLOBALS['faq_schema_outputted'] ) ) {
		echo minify_markup( $markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is safe, as $markup is fully escaped in schema_markup().
		$GLOBALS['faq_schema_outputted'] = true;
	}
}
