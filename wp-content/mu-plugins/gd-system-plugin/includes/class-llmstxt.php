<?php

namespace WPaaS;

class LLMSTXT {

    const CACHE_KEY = 'gd_llms_txt_cache';

    public function __construct() {
        add_action( 'init', [ $this, 'register_virtual_llms' ] );

        // Invalidate cache when content changes
        add_action( 'save_post', [ $this, 'invalidate_cache' ] );
        add_action( 'edited_term', [ $this, 'invalidate_cache' ] );
        add_action( 'deleted_post', [ $this, 'invalidate_cache' ] );
        add_action( 'delete_term', [ $this, 'invalidate_cache' ] );
        add_action( 'trashed_post', [ $this, 'invalidate_cache' ] );

        add_action(
            'updated_option',
            function ( $option ) {
                if ( in_array( $option, [ 'is_llms_enabled' ], true ) ) {
                    $this->invalidate_cache();
                }
            }, 10, 1 );

        // Allow advanced users to clear cache via custom action
        add_action( 'gd_llms_txt_content_changed', [ $this, 'invalidate_cache' ] );
    }

    /**
     * Register virtual endpoint for /llms.txt
     */
    public function register_virtual_llms() {
        add_rewrite_rule( '^llms\.txt/?$', 'index.php?gd_llms_txt=1', 'top' );

        add_filter('query_vars', [$this, 'register_query_var']);


        add_action( 'template_redirect', [ $this, 'handle_llms_request' ] );
    }

    public function register_query_var($vars) {
        $vars[] = 'gd_llms_txt';
        return $vars;
    }

    /**
     * Handle requests to /llms.txt
     */
    public function handle_llms_request() {
        $has_query_var = get_query_var('gd_llms_txt');
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $is_llms_url = (strpos($request_uri, '/llms.txt') !== false);

        if ( ! $has_query_var && ! $is_llms_url ) {
            return;
        }

        add_filter('redirect_canonical', function($redirect_url) {
            if ( get_query_var('gd_llms_txt') ) {
                return false;
            }
            return $redirect_url;
        });

        $is_enabled = get_option( 'is_llms_enabled', 'disabled' );
        if ( 'enabled' !== $is_enabled ) {
            status_header( 404 );
            exit;
        }

        if ( $this->yoast_llms_conflict() ) {
            status_header( 404 );
            exit;
        }

        $body = get_transient( self::CACHE_KEY );

        if ( false === $body ) {
            $body = $this->build_llms_body();
            set_transient( self::CACHE_KEY, $body, DAY_IN_SECONDS );
        }

        do_action( 'gd_llms_txt_rendered', $body );

        header( 'Content-Type: text/plain; charset=utf-8' );
        header('Cache-Control: max-age=86400, must-revalidate, public');

        echo $body;
        exit;
    }


    /**
     * Detect if Yoast's LLMS.txt feature is active
     */
    private function yoast_llms_conflict() {
        if ( defined( 'WPSEO_VERSION' ) ) {
            $path = ABSPATH . 'llms.txt';
            if ( file_exists( $path ) ) {
                return true;
            }

            // In case Yoast exposes custom options
            if ( get_option( 'wpseo_llmstxt_enabled', false ) || get_option( 'wpseo_llmstxt_include', false ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build the markdown content for LLMS.txt
     */
    private function build_llms_body() {
        $lines = [];

        // Site header
        $lines[] = '# ' . get_bloginfo( 'name' ) . "\n";
        $tagline = get_bloginfo( 'description' );
        if ( ! empty( $tagline ) ) {
            $lines[] = '> ' . $tagline;
            $lines[] = ''; // blank line
        }

        // --- Core Content ---
        $lines[] = '## Core Content' . "\n";
        foreach ( $this->get_recent_posts() as $post ) {
            $title = strip_tags( $post->post_title );
            $url   = get_permalink( $post->ID );
            $desc  = $this->get_post_description( $post );
            $lines[] = '- ' . sprintf( '[%s](%s): %s', $title, $url, $desc );
        }
        $lines[] = '';

        // --- Key Topics ---
        $lines[] = '## Key Topics' . "\n";
        foreach ( $this->get_top_terms() as $term ) {
            $lines[] = '- ' . sprintf( '[%s](%s)', $term->name, get_term_link( $term ) );
        }
        $lines[] = '';
        $context = [
            'site'         => get_bloginfo( 'name' ),
            'recent_posts' => $this->get_recent_posts(),
            'top_terms'    => $this->get_top_terms(),
        ];

        // Allow external modification
        $lines = apply_filters( 'gd_llms_txt_lines', $lines, $context );

        $lines[] = '';
        $lines[] = '# Last updated: ' . gmdate('c') . "\n";

        return implode( "\n", $lines ) . "\n";
    }

    /**
     * Fetch top 5 public posts/pages from last 12 months
     */
    private function get_recent_posts() {
        $args = [
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'date_query'     => [
                ['after' => '1 year ago'],
            ],
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => '_yoast_wpseo_meta-robots-noindex',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => '_yoast_wpseo_meta-robots-noindex',
                    'value'   => '1',
                    'compare' => '!=',
                ],
            ],
            'has_password'   => false,
        ];

        return get_posts( $args );
    }

    /**
     * Fetch top 5 categories/tags by object count
     */
    private function get_top_terms() {
        $args = [
            'taxonomy'   => [ 'category', 'post_tag' ],
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => 5,
            'hide_empty' => true,
        ];
        return get_terms( $args );
    }

    /**
     * Extract description from excerpt or meta description
     */
    private function get_post_description( $post ) {
        $meta_desc = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );
        $excerpt   = $meta_desc ?: wp_strip_all_tags( $post->post_excerpt );

        if ( empty( $excerpt ) ) {
            $excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 20, '' );
        }

        return trim( preg_replace( '/\s+/', ' ', $excerpt ) );
    }

    public function invalidate_cache() {
        delete_transient( self::CACHE_KEY );
    }
}
