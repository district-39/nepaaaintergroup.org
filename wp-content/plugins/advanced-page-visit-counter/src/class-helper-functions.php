<?php

class APVC_Helper_Functions
{
    public static  $chars = 'abcdfghjkmnpqrstvwxyz|ABCDFGHJKLMNPQRSTVWXYZ|0123456789' ;
    public static function apvc_is_pro()
    {
        return false;
    }
    
    public static function check_string( $string )
    {
        if ( !isset( $string ) ) {
            return null;
        }
        $safe_string = trim( urldecode( $string ) );
        $safe_string = str_replace( '+', ' ', $safe_string );
        $safe_string = sanitize_text_field( $safe_string );
        if ( strlen( $safe_string ) === 0 ) {
            return null;
        }
        return $safe_string;
    }
    
    public static function get_page_type()
    {
        $page_type = array();
        
        if ( is_singular() ) {
            $type = 'singular';
            $meta_key = 'singular_id';
            $meta_value = get_queried_object_id();
        } elseif ( is_author() ) {
            $type = 'author_archive';
            $meta_key = 'author_id';
            $meta_value = get_queried_object_id();
        } elseif ( is_date() ) {
            $type = 'date_archive';
            $meta_key = 'date_archive';
            $meta_value = self::get_date_archive_date();
        } elseif ( is_post_type_archive() ) {
            $type = 'post_type_archive';
            $meta_key = 'post_type';
            $meta_value = get_queried_object()->name;
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $type = 'term_archive';
            $meta_key = 'term_id';
            $meta_value = get_queried_object_id();
        } elseif ( is_search() ) {
            $type = 'search';
            $meta_key = 'search_query';
            $meta_value = get_search_query();
        } elseif ( is_home() ) {
            $type = 'home';
            $meta_key = null;
            $meta_value = null;
        } elseif ( is_404() ) {
            $url = null;
            $site_url = site_url();
            
            if ( $url == $site_url ) {
                $path = '/';
            } elseif ( substr( $url, 0, strlen( $site_url ) ) == $site_url ) {
                $path = substr( $url, strlen( $site_url ) );
            } else {
                $path = $url;
            }
            
            if ( is_null( $path ) ) {
                return null;
            }
            $type = '404';
            $meta_key = 'url_not_found';
            $meta_value = $path;
        } else {
            return null;
        }
        
        $page_type['page_type'] = $type;
        $page_type['page_meta_key'] = $meta_key;
        $page_type['page_meta_value'] = $meta_value;
        return $page_type;
    }
    
    public static function get_page_type_db( $rawData )
    {
        global  $wpdb ;
        $resources_table = APVC_Query::get_table_name( APVC_Query::VIEWS_DATA );
        $query = '';
        $payload = array_merge( $rawData );
        unset( $payload['page'] );
        switch ( $payload['type'] ) {
            case 'singular':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND singular_id = %d", $payload['type'], $payload['singular_id'] );
                break;
            case 'author_archive':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND author_id = %d", $payload['type'], $payload['author_id'] );
                break;
            case 'date_archive':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND date_archive = %s", $payload['type'], $payload['date_archive'] );
                break;
            case 'post_type_archive':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND post_type = %s", $payload['type'], $payload['post_type'] );
                break;
            case 'term_archive':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND term_id = %s", $payload['type'], $payload['term_id'] );
                break;
            case 'search':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND search_query = %s", $payload['type'], $payload['search_query'] );
                break;
            case 'home':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s ", $payload['type'] );
                break;
            case '404':
                $query = $wpdb->prepare( "SELECT * FROM {$resources_table} WHERE type = %s AND not_found_url = %s", $payload['type'], $payload['not_found_url'] );
                break;
        }
        $resource = $wpdb->get_row( $query );
        
        if ( is_null( $resource ) ) {
            $wpdb->insert( $resources_table, $payload );
            $resource = $wpdb->get_row( $query );
        }
        
        switch ( $resource->type ) {
            case 'singular':
                APVC_view_cache::update_cache( 'singular_id', $resource );
                break;
            case 'author_archive':
                APVC_view_cache::update_cache( 'author_id', $resource );
                break;
            case 'post_type_archive':
                APVC_view_cache::update_cache( 'post_type', $resource );
                break;
            case 'term_archive':
                APVC_view_cache::update_cache( 'term_id', $resource );
                break;
            case 'home':
                APVC_view_cache::update_cache( 'home', $resource );
                break;
            case 'date_archive':
                APVC_view_cache::update_cache( 'date_archive', $resource );
                break;
            case '404':
                APVC_view_cache::update_cache( '404', $resource );
                break;
            case 'search':
                APVC_view_cache::update_cache( 'search', $resource );
                break;
        }
        return $resource->id;
    }
    
    public static function get_parameter( $parameter )
    {
        if ( isset( $_GET[$parameter] ) ) {
            return sanitize_text_field( $_GET[$parameter] );
        }
    }
    
    private static function get_date_archive_date()
    {
        $str = get_query_var( 'year' );
        
        if ( is_month() || is_day() ) {
            $month = get_query_var( 'monthnum' );
            $str = $str . '-' . str_pad(
                $month,
                2,
                '0',
                STR_PAD_LEFT
            );
        }
        
        
        if ( is_day() ) {
            $day = get_query_var( 'day' );
            $str = $str . '-' . str_pad(
                $day,
                2,
                '0',
                STR_PAD_LEFT
            );
        }
        
        return $str;
    }
    
    public static function get_ip_address()
    {
        $headers = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_INCAP_CLIENT_IP'
        );
        foreach ( $headers as $header ) {
            if ( isset( $_SERVER[$header] ) ) {
                return explode( ',', $_SERVER[$header] )[0];
            }
        }
        return 'null';
    }
    
    public static function get_user_agent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = '';
        //First get the platform?
        
        if ( preg_match( '/linux/i', $u_agent ) ) {
            $platform = 'linux';
        } elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
            $platform = 'mac';
        } elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        
        if ( preg_match( '/MSIE/i', $u_agent ) && !preg_match( '/Opera/i', $u_agent ) ) {
            $bname = 'Internet Explorer';
            $ub = 'MSIE';
        } elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
            $bname = 'Mozilla Firefox';
            $ub = 'Firefox';
        } elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
            $bname = 'Google Chrome';
            $ub = 'Chrome';
        } elseif ( preg_match( '/Safari/i', $u_agent ) ) {
            $bname = 'Apple Safari';
            $ub = 'Safari';
        } elseif ( preg_match( '/Opera/i', $u_agent ) ) {
            $bname = 'Opera';
            $ub = 'Opera';
        } elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
            $bname = 'Netscape';
            $ub = 'Netscape';
        }
        
        // finally get the correct version number
        $known = array( 'Version', $ub, 'other' );
        $pattern = '#(?<browser>' . join( '|', $known ) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if ( !preg_match_all( $pattern, $u_agent, $matches ) ) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count( $matches['browser'] );
        
        if ( $i != 1 ) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            
            if ( strripos( $u_agent, 'Version' ) < strripos( $u_agent, $ub ) ) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        
        } else {
            $version = $matches['version'][0];
        }
        
        // check if we have a number
        if ( $version == null || $version == '' ) {
            $version = '?';
        }
        return array(
            'name'    => $bname,
            'version' => $version,
        );
    }
    
    public static function get_user_device()
    {
        $browser = self::getBrowser();
        $device = array();
        // Check if the "mobile" word exists in User-Agent
        $isMob = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'mobile' ) );
        // Check if the "tablet" word exists in User-Agent
        $isTab = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'tablet' ) );
        // Platform check
        $isWin = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'windows' ) );
        $isMac = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'macintosh' ) );
        $isAndroid = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'android' ) );
        $isIPhone = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'iphone' ) );
        $isIPad = is_numeric( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'ipad' ) );
        $isIOS = $isIPhone || $isIPad;
        
        if ( $browser ) {
            $device['device_browser'] = $browser['name'];
            $device['device_browser_ver'] = $browser['version'];
        }
        
        
        if ( $isMob ) {
            
            if ( $isTab ) {
                $device['device_type'] = 'Tablet';
            } else {
                $device['device_type'] = 'Mobile';
            }
        
        } else {
            $device['device_type'] = 'Desktop';
        }
        
        
        if ( $isIOS ) {
            $device['device_os'] = 'iOS';
        } elseif ( $isAndroid ) {
            $device['device_os'] = 'Android';
        } elseif ( $isWin ) {
            $device['device_os'] = 'Windows';
        } elseif ( $isMac ) {
            $device['device_os'] = 'Mac';
        }
        
        return $device;
    }
    
    public static function str_contains( $haystack, $needle )
    {
        return '' === $needle || false !== strpos( $haystack, $needle );
    }
    
    public static function str_starts_with( $haystack, $needle )
    {
        if ( '' === $needle ) {
            return true;
        }
        return 0 === strpos( $haystack, $needle );
    }
    
    public static function str_ends_with( $haystack, $needle )
    {
        if ( '' === $haystack && '' !== $needle ) {
            return false;
        }
        $len = strlen( $needle );
        return 0 === substr_compare(
            $haystack,
            $needle,
            -$len,
            $len
        );
    }
    
    public static function is_internal_referrer( $referrer_url )
    {
        return !empty($referrer_url) && self::str_starts_with( strtolower( $referrer_url ), strtolower( site_url() ) );
    }
    
    public static function get_user_session( $user_object )
    {
        $current_session = self::get_current_user_session( $user_object->visitor->id() );
        
        if ( empty($current_session) ) {
            return self::create_session( $user_object );
        } else {
            return $current_session;
        }
    
    }
    
    public static function get_current_user_session( $user_id )
    {
        $session = APVC_Query::get_current_user_session( $user_id );
        return ( isset( $session ) ? $session : '' );
    }
    
    public static function create_session( $user_object )
    {
        $created_at = ( !empty($user_object->viewed_at) ? $user_object->viewed_at : ( new \DateTime() )->format( 'Y-m-d H:i:s' ) );
        $ended_at = ( !empty($user_object->ended_at) ? $user_object->ended_at : ( new \DateTime() )->format( 'Y-m-d H:i:s' ) );
        $campaign_id = 0;
        return APVC_Query::create_user_session( array(
            'visitor_id'         => $user_object->visitor->id(),
            'referrer_id'        => self::get_referrer( $user_object->referrer_url ),
            'campaign_id'        => $campaign_id,
            'created_at'         => $created_at,
            'ended_at'           => $ended_at,
            'device_type'        => $user_object->user_device['device_type'],
            'device_os'          => $user_object->user_device['device_os'],
            'device_browser'     => $user_object->user_device['device_browser'],
            'device_browser_ver' => $user_object->user_device['device_browser_ver'],
            'city_id'            => $user_object->city_id,
            'state_id'           => $user_object->state_id,
            'country_id'         => $user_object->country_id,
            'site_id'            => get_current_blog_id(),
        ) );
    }
    
    public static function get_referrer( $referrer_url )
    {
        if ( !isset( $referrer_url ) || strlen( $referrer_url ) === 0 ) {
            return null;
        }
        $url = new APVC_URL( $referrer_url );
        if ( !$url->is_valid_url() ) {
            return null;
        }
        if ( self::is_internal_referrer( $referrer_url ) ) {
            return null;
        }
        $referrer = APVC_Query::get_referrer( $url->get_domain() );
        
        if ( isset( $referrer ) ) {
            return $referrer->id;
        } else {
            return APVC_Query::create_referrer( $url->get_domain() );
        }
    
    }
    
    public static function create_view( $object_data )
    {
        $created_at = ( !empty($object_data->viewed_at) ? $object_data->viewed_at : ( new \DateTime() )->format( 'Y-m-d H:i:s' ) );
        return APVC_Query::create_view( array(
            'page_id'         => $object_data->rawdata['singular_id'],
            'viewed_at'       => $created_at,
            'page'            => $object_data->rawdata['page'] ?? '',
            'user_session_id' => $object_data->session,
        ) );
    }
    
    public static function set_session_int_view( $session_id, $view_id )
    {
        global  $wpdb ;
        $sessions_table = APVC_Query::get_table_name( APVC_Query::SESSIONS );
        $wpdb->query( $wpdb->prepare( "UPDATE {$sessions_table} SET first_view_id = %d WHERE user_session_id = %d AND first_view_id IS NULL", $view_id, $session_id ) );
    }
    
    public static function set_session_last_view( $view_id, $ended_at, $session_id )
    {
        global  $wpdb ;
        $sessions_table = APVC_Query::get_table_name( APVC_Query::SESSIONS );
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$sessions_table} SET last_view_id = %d, session_ended_at = %s WHERE user_session_id = %d AND first_view_id IS NOT NULL",
            $view_id,
            $ended_at,
            $session_id
        ) );
    }
    
    public static function blocked_ip( $ip_address )
    {
        $saved_option = get_option( 'apvc_basic_settings', true );
        if ( empty($saved_option) ) {
            return false;
        }
        if ( isset( $saved_option['apvc_exclude_ips'] ) && !empty($saved_option['apvc_exclude_ips']) ) {
            if ( in_array( $ip_address, $saved_option['apvc_exclude_ips'] ) ) {
                return true;
            }
        }
    }
    
    public static function blocked_post_type( $singular_id )
    {
        $post_type = get_post_type( absint( $singular_id ) );
        $saved_option = get_option( 'apvc_basic_settings', true );
        if ( empty($saved_option) ) {
            return false;
        }
        if ( isset( $saved_option['apvc_post_types'] ) && !empty($saved_option['apvc_post_types']) ) {
            if ( !in_array( $post_type, $saved_option['apvc_post_types'] ) ) {
                return true;
            }
        }
        return false;
    }
    
    public static function blocked_users( $user_id )
    {
        $saved_option = get_option( 'apvc_basic_settings', true );
        if ( empty($saved_option) ) {
            return false;
        }
        if ( isset( $saved_option['apvc_exclude_users'] ) && !empty($saved_option['apvc_exclude_users']) ) {
            if ( in_array( absint( $user_id ), $saved_option['apvc_exclude_users'] ) ) {
                return true;
            }
        }
    }
    
    public static function blocked_posts( $singular_id )
    {
        $saved_option = get_option( 'apvc_basic_settings', true );
        if ( empty($saved_option) ) {
            return false;
        }
        if ( isset( $saved_option['apvc_exclude_posts'] ) && !empty($saved_option['apvc_exclude_posts']) ) {
            if ( in_array( absint( $singular_id ), $saved_option['apvc_exclude_posts'] ) ) {
                return true;
            }
        }
    }
    
    public static function track_loggedin_users_only( $user_id )
    {
        $saved_option = get_option( 'apvc_advanced_settings', true );
        if ( !isset( $saved_option ) ) {
            return false;
        }
        if ( isset( $saved_option['apvc_track_loggedin_users'] ) && !empty($saved_option['apvc_track_loggedin_users']) ) {
            if ( absint( $user_id ) === 0 ) {
                return true;
            }
        }
    }
    
    public static function getBetweenDates( $startDate, $endDate )
    {
        $rangArray = array();
        $startDate = strtotime( $startDate );
        $endDate = strtotime( $endDate );
        for ( $currentDate = $startDate ;  $currentDate <= $endDate ;  $currentDate += 86400 ) {
            $date = date( 'Y-m-d', $currentDate );
            $rangArray[] = $date;
        }
        return $rangArray;
    }
    
    public static function getTotalVisitorsForRef( $ref_id )
    {
        global  $wpdb ;
        $user_sessions = APVC_Query::get_table_name( APVC_Query::SESSIONS );
        return count( $wpdb->get_results( $wpdb->prepare( 'SELECT `visitor_id` FROM ' . $user_sessions . ' WHERE referrer_id = %d GROUP BY visitor_id', $ref_id ) ) );
    }
    
    public static function getTotalViewsForRef( $ref_id )
    {
        global  $wpdb ;
        $user_sessions = APVC_Query::get_table_name( APVC_Query::SESSIONS );
        $user_views = APVC_Query::get_table_name( APVC_Query::VIEWS );
        $allSession = $wpdb->get_results( $wpdb->prepare( 'SELECT `user_session_id` FROM ' . $user_sessions . ' WHERE `referrer_id` = %d GROUP BY `user_session_id`', $ref_id ) );
        $viewsCount = 0;
        if ( !empty($allSession) ) {
            foreach ( $allSession as $session ) {
                $viewsCount += $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) as count FROM ' . $user_views . ' WHERE `user_session_id` = %d', $session->user_session_id ) );
            }
        }
        return $viewsCount;
    }
    
    public static function most_recent_view_id()
    {
        global  $wpdb ;
        $views_table = APVC_Query::get_table_name( APVC_Query::VIEWS );
        $id = $wpdb->get_var( "SELECT `id` FROM {$views_table} order by `viewed_at` desc limit 1" );
        if ( \is_null( $id ) ) {
            return null;
        }
        return \intval( $id );
    }
    
    public static function get_geo_data_status()
    {
        $time = new DateTime( 'now' );
        $db_file = get_option( 'apvc_geo_database_date', true );
        $final = absint( strtotime( $db_file ) - strtotime( $time->format( 'Y-m-d' ) ) );
        return floor( $final / 86400 );
    }
    
    public static function generate_random_string( $length = 18 )
    {
        $sets = explode( '|', self::$chars );
        $all = '';
        $randString = '';
        foreach ( $sets as $set ) {
            $randString .= $set[array_rand( str_split( $set ) )];
            $all .= $set;
        }
        $all = str_split( $all );
        for ( $i = 0 ;  $i < $length - count( $sets ) ;  $i++ ) {
            $randString .= $all[array_rand( $all )];
        }
        $randString = str_shuffle( $randString );
        return $randString;
    }
    
    public static function get_state_id( $state_name )
    {
        global  $wpdb ;
        $states_table = APVC_Query::get_table_name( APVC_Query::STATES );
        $id = $wpdb->get_var( $wpdb->prepare( "SELECT `state_id` FROM {$states_table} WHERE `state_name` = %s", $state_name ) );
        if ( \is_null( $id ) ) {
            return null;
        }
        return \intval( $id );
    }
    
    public static function get_city_id( $city_name )
    {
        global  $wpdb ;
        $city_table = APVC_Query::get_table_name( APVC_Query::CITIES );
        $id = $wpdb->get_var( $wpdb->prepare( "SELECT `city_id` FROM {$city_table} WHERE `city` = %s", $city_name ) );
        if ( \is_null( $id ) ) {
            return null;
        }
        return \intval( $id );
    }
    
    public static function get_city_name( $city_id )
    {
        global  $wpdb ;
        $city_table = APVC_Query::get_table_name( APVC_Query::CITIES );
        $name = $wpdb->get_var( $wpdb->prepare( "SELECT `city` FROM {$city_table} WHERE `city_id` = %d", $city_id ) );
        if ( \is_null( $name ) ) {
            return null;
        }
        return $name;
    }
    
    public static function get_state_name( $state_id )
    {
        global  $wpdb ;
        $state_table = APVC_Query::get_table_name( APVC_Query::STATES );
        $name = $wpdb->get_var( $wpdb->prepare( "SELECT `state_name` FROM {$state_table} WHERE `state_id` = %d", $state_id ) );
        if ( \is_null( $name ) ) {
            return null;
        }
        return $name;
    }
    
    public static function get_country_data( $country_id )
    {
        global  $wpdb ;
        $country_table = APVC_Query::get_table_name( APVC_Query::COUNTRIES );
        $name = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$country_table} WHERE `country_id` = %d", $country_id ) );
        if ( \is_null( $name ) ) {
            return null;
        }
        return $name;
    }

}