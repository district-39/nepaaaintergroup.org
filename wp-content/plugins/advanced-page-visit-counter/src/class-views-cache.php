<?php

class APVC_view_cache {

	public $term;
	public function __construct(){
		add_action( 'apvc_refresh_pages_widget', array( $this, 'apvc_refresh_pages_widget' ) );
		add_action( 'apvc_refresh_posts_widget', array( $this, 'apvc_refresh_posts_widget' ) );
		add_action( 'apvc_refresh_countries_widget', array( $this, 'apvc_refresh_countries_widget' ) );
		add_action( 'apvc_refresh_states_widget', array( $this, 'apvc_refresh_states_widget' ) );
		add_action( 'apvc_refresh_cities_widget', array( $this, 'apvc_refresh_cities_widget' ) );
		add_action( 'apvc_refresh_devices_widget', array( $this, 'apvc_refresh_devices_widget' ) );
		add_action( 'apvc_refresh_devices_os_widget', array( $this, 'apvc_refresh_devices_os_widget' ) );
		add_action( 'apvc_refresh_browsers_widget', array( $this, 'apvc_refresh_browsers_widget' ) );

		add_action( 'apvc_refresh_refer_trending_ref_widget', array( $this, 'apvc_refresh_refer_trending_ref_widget' ) );
		add_action( 'apvc_refresh_refer_trending_ref_list_widget', array( $this, 'apvc_refresh_refer_trending_ref_list_widget' ) );
	}

	public static function update_cache( $type, $resource ) {
		global $wpdb;
		$resources_table = APVC_Query::get_table_name( APVC_Query::VIEWS_DATA );
		$resource_key    = self::page_key( $type );
		$resource_value  = self::page_value( $type, $resource );

		$query = $wpdb->prepare(
			"UPDATE $resources_table SET stored_title = %s, stored_url = %s, stored_type = %s, stored_type_label = %s, stored_author_id = %s, stored_author = %s, stored_date = %s, stored_category = %s WHERE $resource_key = %s",
			self::get_title( $type, $resource_value ),
			self::get_url( $type, $resource_value ),
			self::get_type( $type, $resource_value ),
			self::get_type_label( $type, $resource_value ),
			self::get_author_id( $type, $resource_value ),
			self::get_author( $type, $resource_value ),
			self::get_date( $type, $resource_value ),
			self::get_category( $type, $resource_value ),
			$resource_value
		);
		$wpdb->query($query);
	}

	public static function page_key( $type ) {
		return $type;
	}

	public static function page_value( $type, $resource ) {
		switch ( $type ) {
			case 'singular_id':
				return $resource->singular_id;
			case 'author_id':
				return $resource->author_id;
			case 'post_type':
				return $resource->post_type;
			case 'term_id':
				return $resource->term_id;
			case 'home':
				return 'home';
			case 'date_archive':
				return $resource->date_archive;
			case '404':
				return $resource->not_found_url;
			case 'search':
				return $resource->search_querystring;
			default:
				return null;
		}
	}
	public static function get_title( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
				return get_the_title( $resource_value );
			case 'author_id':
				return get_the_author_meta( 'display_name', $resource_value ) . ' ' . esc_html__( 'Archive', 'apvc' );
			case 'post_type':
				return get_post_type_object( $resource_value )->labels->singular_name . ' ' . esc_html__( 'Archive', 'apvc' );
			case 'term_id':
				return self::term( $resource_value )->name;
			case 'home':
				$id = get_option( 'page_for_posts' );
				if ( $id == 0 ) {
					return esc_html__( 'Blog', 'apvc' );
				} else {
					return get_the_title( $id );
				}
			case '404':
				return '404';
			case 'search':
			case 'date_archive':
				return $resource_value;
			default:
				return null;
		}
	}
	public static function get_url( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
				return get_permalink( $resource_value );
			case 'author_id':
				return get_author_posts_url( $resource_value );
			case 'post_type':
				return get_post_type_archive_link( $resource_value );
			case 'term_id':
				return get_term_link( $resource_value );
			case 'home':
				$id = get_option( 'page_for_posts' );
				if ( $id == 0 ) {
					return get_home_url();
				} else {
					return get_permalink( $id );
				}
			case 'date_archive':
				list($type, $year, $month, $day) = $resource_value;

				if ( $type == 'year' ) {
					return get_year_link( $year );
				} elseif ( $type == 'month' ) {
					return get_month_link( $year, $month );
				} else {
					return get_day_link( $year, $month, $day );
				}
			case '404':
				return site_url( $resource_value );
			case 'search':
				return get_search_link( $resource_value );
			default:
				return null;
		}
	}
	public static function get_type( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
				return get_post_type( $resource_value );
			case 'author_id':
				return 'author-archive';
			case 'post_type':
				return $resource_value . '-archive';
			case 'term_id':
				return self::term( $resource_value )->taxonomy;
			case 'home':
				return 'blog-archive';
			case 'date_archive':
				return 'date-archive';
			case '404':
				return 'not-found';
			case 'search':
				return 'search-archive';
			default:
				return null;
		}
	}
	public static function get_type_label( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
				return get_post_type_object( get_post_type( $resource_value ) )->labels->singular_name;
			case 'author_id':
				return esc_html__( 'Author Archive', 'apvc' );
			case 'post_type':
				return get_post_type_object( $resource_value )->labels->singular_name;
			case 'term_id':
				return get_taxonomy_labels( get_taxonomy( self::term( $resource_value )->taxonomy ) )->singular_name;
			case 'home':
				return esc_html__( 'Blog', 'apvc' );
			case 'date_archive':
				list($type) = self::date_archive_type( $resource_value );

				if ( $type == 'year' ) {
					return esc_html__( 'Date Archive (Year)', 'apvc' );
				} elseif ( $type == 'month' ) {
					return esc_html__( 'Date Archive (Month)', 'apvc' );
				} else {
					return esc_html__( 'Date Archive (Day)', 'apvc' );
				}
			case '404':
				return '404';
			case 'search':
				return esc_html__( 'Search', 'apvc' );
			default:
				return null;
		}
	}
	public static function get_author_id( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
				return get_post_field( 'post_author', $resource_value );
			case 'author_id':
				return $resource_value;
			default:
				return '';
		}
	}
	public static function get_author( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
			case 'author_id':
				$author_id = get_post_field( 'post_author', absint($resource_value) );
				return get_the_author_meta( 'display_name', $author_id );
			case 'post_type':
			case 'term_id':
			case 'home':
			case 'date_archive':
			case '404':
			case 'search':
				return null;
		}
	}
	public static function get_date( $type, $resource_value ) {
		switch ( $type ) {
			case 'singular_id':
			case 'author_id':
			case 'post_type':
			case 'term_id':
			case 'home':
			case 'date_archive':
			case '404':
			case 'search':
				return strtotime(( new \DateTime() )->format( 'Y-m-d H:i:s' ));
		}
	}
	public static function get_category( $type, $resource_value ) {

		switch ( $type ) {
			case 'singular_id':
				return self::get_category_details( true, $resource_value );
			case 'author_id':
			case 'post_type':
			case 'term_id':
			case 'home':
			case 'date_archive':
			case '404':
			case 'search':
				return null;
		}
	}
	public static function term( $term_id ) {
		return get_term( $term_id );
	}
	public static function date_archive_type( $date_archive ) {
		list($year, $month, $day) = array_pad( explode( '-', $date_archive ), 3, null );

		if ( is_null( $day ) && is_null( $month ) ) {
			return array( 'year', $year, null, null );
		} elseif ( is_null( $day ) ) {
			return array( 'month', $year, $month, null );
		} else {
			return array( 'day', $year, $month, $day );
		}
	}
	public static function get_category_details( $formatted = true, $resource_value = '' ) {
		if ( in_array( self::category_type( $resource_value ), get_post_types() ) ) {
			$categories = array();
			foreach ( get_the_category( $resource_value ) as $category ) {
				if ( $formatted ) {
					$categories[] = get_the_category_by_ID( $category->term_id );
				} else {
					$categories[] = $category->term_id;
				}
			}

			return $formatted ? implode( ', ', $categories ) : $categories;
		} else {
			return null;
		}
	}
	public static function category_type( $singular_id ) {
		return get_post_type( $singular_id );
	}

	public function apvc_refresh_pages_widget() {
		$ajax_data = APVC_Query::get_top_articles( 'page' );
		delete_transient('apvc_trend_top_pages');
		return set_transient( 'apvc_trend_top_pages', $ajax_data, 3600 );
	}

	public function apvc_refresh_posts_widget() {
		$ajax_data = APVC_Query::get_top_articles( 'post' );
		delete_transient('apvc_trend_top_posts');
		return set_transient( 'apvc_trend_top_posts', $ajax_data, 3600 );
	}

	public function apvc_refresh_countries_widget() {
		$ajax_data = APVC_Query::get_top_country();
		delete_transient('apvc_trend_top_countries');
		return set_transient( 'apvc_trend_top_countries', $ajax_data, 3600 );
	}

	public function apvc_refresh_states_widget() {
		$ajax_data = APVC_Query::get_top_states();
		delete_transient('apvc_trend_top_states');
		return set_transient( 'apvc_trend_top_states', $ajax_data, 3600 );
	}

	public function apvc_refresh_cities_widget() {
		$ajax_data = APVC_Query::get_top_cities();
		delete_transient('apvc_trend_top_cities');
		return set_transient( 'apvc_trend_top_cities', $ajax_data, 3600 );
	}

	public function apvc_refresh_devices_widget() {
		$ajax_data = APVC_Query::get_top_devices();
		delete_transient('apvc_trend_top_devices');
		return set_transient( 'apvc_trend_top_devices', $ajax_data, 3600 );
	}

	public function apvc_refresh_devices_os_widget() {
		$ajax_data = APVC_Query::get_top_devices_os();
		delete_transient('apvc_trend_top_devices_os');
		return set_transient( 'apvc_trend_top_devices_os', $ajax_data, 3600 );
	}

	public function apvc_refresh_browsers_widget() {
		$ajax_data = APVC_Query::get_top_browsers();
		delete_transient('apvc_trend_top_browsers');
		set_transient( 'apvc_trend_top_browsers', $ajax_data, 3600 );
	}

	public function apvc_refresh_refer_trending_ref_widget() {
		$ajax_data = APVC_Query::get_trending_referrer_name();
		delete_transient( 'apvc_trend_refferer' );
		set_transient( 'apvc_trend_refferer', $ajax_data, 3600 );
	}

	public function apvc_refresh_refer_trending_ref_list_widget() {
		$ajax_data = APVC_Query::get_referrers_list();
		delete_transient( 'apvc_trend_refferer_list' );
		set_transient( 'apvc_trend_refferer_list', $ajax_data, 3600 );
	}



}
