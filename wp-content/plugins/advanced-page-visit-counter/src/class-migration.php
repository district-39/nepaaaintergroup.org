<?php

class APVC_Migration extends APVC_Query
{
    private static  $referrers = array(
        array(
        'name'    => 'Google',
        'type'    => 'Search',
        'domains' => array(
        'www.google.com',
        'mail.google.com',
        'www.google.ad',
        'www.google.ae',
        'www.google.com.af',
        'www.google.com.ag',
        'www.google.com.ai',
        'www.google.al',
        'www.google.am',
        'www.google.co.ao',
        'www.google.com.ar',
        'www.google.as',
        'www.google.at',
        'www.google.com.au',
        'www.google.az',
        'www.google.ba',
        'www.google.com.bd',
        'www.google.be',
        'www.google.bf',
        'www.google.bg',
        'www.google.com.bh',
        'www.google.bi',
        'www.google.bj',
        'www.google.com.bn',
        'www.google.com.bo',
        'www.google.com.br',
        'www.google.bs',
        'www.google.bt',
        'www.google.co.bw',
        'www.google.by',
        'www.google.com.bz',
        'www.google.ca',
        'www.google.cd',
        'www.google.cf',
        'www.google.cg',
        'www.google.ch',
        'www.google.ci',
        'www.google.co.ck',
        'www.google.cl',
        'www.google.cm',
        'www.google.cn',
        'www.google.com.co',
        'www.google.co.cr',
        'www.google.com.cu',
        'www.google.cv',
        'www.google.com.cy',
        'www.google.cz',
        'www.google.de',
        'www.google.dj',
        'www.google.dk',
        'www.google.dm',
        'www.google.com.do',
        'www.google.dz',
        'www.google.com.ec',
        'www.google.ee',
        'www.google.com.eg',
        'www.google.es',
        'www.google.com.et',
        'www.google.fi',
        'www.google.com.fj',
        'www.google.fm',
        'www.google.fr',
        'www.google.ga',
        'www.google.ge',
        'www.google.gg',
        'www.google.com.gh',
        'www.google.com.gi',
        'www.google.gl',
        'www.google.gm',
        'www.google.gr',
        'www.google.com.gt',
        'www.google.gy',
        'www.google.com.hk',
        'www.google.hn',
        'www.google.hr',
        'www.google.ht',
        'www.google.hu',
        'www.google.co.id',
        'www.google.ie',
        'www.google.co.il',
        'www.google.im',
        'www.google.co.in',
        'www.google.iq',
        'www.google.is',
        'www.google.it',
        'www.google.je',
        'www.google.com.jm',
        'www.google.jo',
        'www.google.co.jp',
        'www.google.co.ke',
        'www.google.com.kh',
        'www.google.ki',
        'www.google.kg',
        'www.google.co.kr',
        'www.google.com.kw',
        'www.google.kz',
        'www.google.la',
        'www.google.com.lb',
        'www.google.li',
        'www.google.lk',
        'www.google.co.ls',
        'www.google.lt',
        'www.google.lu',
        'www.google.lv',
        'www.google.com.ly',
        'www.google.co.ma',
        'www.google.md',
        'www.google.me',
        'www.google.mg',
        'www.google.mk',
        'www.google.ml',
        'www.google.com.mm',
        'www.google.mn',
        'www.google.ms',
        'www.google.com.mt',
        'www.google.mu',
        'www.google.mv',
        'www.google.mw',
        'www.google.com.mx',
        'www.google.com.my',
        'www.google.co.mz',
        'www.google.com.na',
        'www.google.com.ng',
        'www.google.com.ni',
        'www.google.ne',
        'www.google.nl',
        'www.google.no',
        'www.google.com.np',
        'www.google.nr',
        'www.google.nu',
        'www.google.co.nz',
        'www.google.com.om',
        'www.google.com.pa',
        'www.google.com.pe',
        'www.google.com.pg',
        'www.google.com.ph',
        'www.google.com.pk',
        'www.google.pl',
        'www.google.pn',
        'www.google.com.pr',
        'www.google.ps',
        'www.google.pt',
        'www.google.com.py',
        'www.google.com.qa',
        'www.google.ro',
        'www.google.ru',
        'www.google.rw',
        'www.google.com.sa',
        'www.google.com.sb',
        'www.google.sc',
        'www.google.se',
        'www.google.com.sg',
        'www.google.sh',
        'www.google.si',
        'www.google.sk',
        'www.google.com.sl',
        'www.google.sn',
        'www.google.so',
        'www.google.sm',
        'www.google.sr',
        'www.google.st',
        'www.google.com.sv',
        'www.google.td',
        'www.google.tg',
        'www.google.co.th',
        'www.google.com.tj',
        'www.google.tl',
        'www.google.tm',
        'www.google.tn',
        'www.google.to',
        'www.google.com.tr',
        'www.google.tt',
        'www.google.com.tw',
        'www.google.co.tz',
        'www.google.com.ua',
        'www.google.co.ug',
        'www.google.co.uk',
        'www.google.com.uy',
        'www.google.co.uz',
        'www.google.com.vc',
        'www.google.co.ve',
        'www.google.vg',
        'www.google.co.vi',
        'www.google.com.vn',
        'www.google.vu',
        'www.google.ws',
        'www.google.rs',
        'www.google.co.za',
        'www.google.co.zm',
        'www.google.co.zw',
        'www.google.cat',
        'com.google.android.gm'
    ),
    ),
        array(
        'name'    => 'Google Docs',
        'type'    => 'Referrer',
        'domains' => array( 'docs.google.com' ),
    ),
        array(
        'name'    => 'Yahoo',
        'type'    => 'Search',
        'domains' => array(
        'search.yahoo.com',
        'at.search.yahoo.com',
        'be.search.yahoo.com',
        'br.search.yahoo.com',
        'ca.search.yahoo.com',
        'ch.search.yahoo.com',
        'de.search.yahoo.com',
        'es.search.yahoo.com',
        'espanol.search.yahoo.com',
        'fi.search.yahoo.com',
        'fr.search.yahoo.com',
        'gr.search.yahoo.com',
        'hk.search.yahoo.com',
        'id.search.yahoo.com',
        'ie.search.yahoo.com',
        'il.search.yahoo.com',
        'in.search.yahoo.com',
        'it.search.yahoo.com',
        'malaysia.search.yahoo.com',
        'nl.search.yahoo.com',
        'no.search.yahoo.com',
        'ph.search.yahoo.com',
        'pl.search.yahoo.com',
        'qc.search.yahoo.com',
        'r.search.yahoo.com',
        'ro.search.yahoo.com',
        'ru.search.yahoo.com',
        'se.search.yahoo.com',
        'sg.search.yahoo.com',
        'tr.search.yahoo.com',
        'tw.search.yahoo.com',
        'ua.search.yahoo.com',
        'uk.search.yahoo.com',
        'vn.search.yahoo.com',
        'za.search.yahoo.com',
        'uk.search.yahoo.com',
        'search.yahoo.co.jp',
        'co.search.yahoo.com'
    ),
    ),
        array(
        'name'    => 'Bing',
        'type'    => 'Search',
        'domains' => array( 'www.bing.com', 'cn.bing.com' ),
    ),
        array(
        'name'    => 'Yandex',
        'type'    => 'Search',
        'domains' => array(
        'yandex.com',
        'yandex.ru',
        'yandex.ua',
        'yandex.by',
        'yandex.kz',
        'yandex.uz',
        'yandex.com.tr',
        'yandex.fr',
        'yandex.az',
        'yandex.com.ge',
        'yandex.com.am',
        'yandex.co.il',
        'yandex.lv',
        'yandex.lt',
        'yandex.ee',
        'yandex.md',
        'yandex.tm',
        'yandex.tj'
    ),
    ),
        array(
        'name'    => 'DuckDuckGo',
        'type'    => 'Search',
        'domains' => array( 'duckduckgo.com' ),
    ),
        array(
        'name'    => 'Ecosia',
        'type'    => 'Search',
        'domains' => array( 'www.ecosia.org' ),
    ),
        array(
        'name'    => 'Qwant',
        'type'    => 'Search',
        'domains' => array( 'www.qwant.com' ),
    ),
        array(
        'name'    => 'AlohaFind',
        'type'    => 'Search',
        'domains' => array( 'alohafind.com' ),
    ),
        array(
        'name'    => 'Brave',
        'type'    => 'Search',
        'domains' => array( 'search.brave.com' ),
    ),
        array(
        'name'    => 'Presearch',
        'type'    => 'Search',
        'domains' => array( 'presearch.com' ),
    ),
        array(
        'name'    => 'Twitter',
        'type'    => 'Social',
        'domains' => array( 'twitter.com', 't.co' ),
    ),
        array(
        'name'    => 'Facebook',
        'type'    => 'Social',
        'domains' => array(
        'www.facebook.com',
        'm.facebook.com',
        'lm.facebook.com',
        'l.facebook.com'
    ),
    ),
        array(
        'name'    => 'Instagram',
        'type'    => 'Social',
        'domains' => array( 'www.instagram.com', 'l.instagram.com', 'lm.instagram.com' ),
    ),
        array(
        'name'    => 'TikTok',
        'type'    => 'Social',
        'domains' => array( 'www.tiktok.com' ),
    ),
        array(
        'name'    => 'LinkedIn',
        'type'    => 'Social',
        'domains' => array( 'www.linkedin.com', 'com.linkedin.android' ),
    ),
        array(
        'name'    => 'Pinterest',
        'type'    => 'Social',
        'domains' => array( 'www.pinterest.com' ),
    ),
        array(
        'name'    => 'YouTube',
        'type'    => 'Social',
        'domains' => array( 'www.youtube.com' ),
    ),
        array(
        'name'    => 'ArtStation',
        'type'    => 'Social',
        'domains' => array( 'www.artstation.com' ),
    ),
        array(
        'name'    => 'Bandcamp',
        'type'    => 'Social',
        'domains' => array( 'bandcamp.com' ),
    ),
        array(
        'name'    => 'Behance',
        'type'    => 'Social',
        'domains' => array( 'www.behance.net' ),
    ),
        array(
        'name'    => 'Bitbucket',
        'type'    => 'Social',
        'domains' => array( 'bitbucket.org' ),
    ),
        array(
        'name'    => 'CodePen',
        'type'    => 'Social',
        'domains' => array( 'codepen.io' ),
    ),
        array(
        'name'    => 'DeviantArt',
        'type'    => 'Social',
        'domains' => array( 'www.deviantart.com' ),
    ),
        array(
        'name'    => 'Discord',
        'type'    => 'Social',
        'domains' => array( 'discord.com' ),
    ),
        array(
        'name'    => 'Dribble',
        'type'    => 'Social',
        'domains' => array( 'dribbble.com' ),
    ),
        array(
        'name'    => 'Flickr',
        'type'    => 'Social',
        'domains' => array( 'flickr.com' ),
    ),
        array(
        'name'    => 'GitHub',
        'type'    => 'Social',
        'domains' => array( 'gist.github.com', 'github.com' ),
    ),
        array(
        'name'    => 'Goodreads',
        'type'    => 'Social',
        'domains' => array( 'www.goodreads.com' ),
    ),
        array(
        'name'    => 'Hacker News',
        'type'    => 'Social',
        'domains' => array( 'news.ycombinator.com' ),
    ),
        array(
        'name'    => 'Meetup',
        'type'    => 'Social',
        'domains' => array( 'www.meetup.com' ),
    ),
        array(
        'name'    => 'Mixcloud',
        'type'    => 'Social',
        'domains' => array( 'www.mixcloud.com' ),
    ),
        array(
        'name'    => 'Patreon',
        'type'    => 'Social',
        'domains' => array( 'www.patreon.com' ),
    ),
        array(
        'name'    => 'Quora',
        'type'    => 'Social',
        'domains' => array( 'bn.quora.com', 'www.quora.com' ),
    ),
        array(
        'name'    => 'Tencent QQ',
        'type'    => 'Social',
        'domains' => array( 'www.qq.com' ),
    ),
        array(
        'name'    => 'Ravelry',
        'type'    => 'Social',
        'domains' => array( 'www.ravelry.com' ),
    ),
        array(
        'name'    => 'Reddit',
        'type'    => 'Social',
        'domains' => array( 'out.reddit.com', 'www.reddit.com' ),
    ),
        array(
        'name'    => 'Slack',
        'type'    => 'Social',
        'domains' => array( 'slack.com' ),
    ),
        array(
        'name'    => 'SlideShare',
        'type'    => 'Social',
        'domains' => array( 'www.slideshare.net' ),
    ),
        array(
        'name'    => 'Snapchat',
        'type'    => 'Social',
        'domains' => array( 'www.snapchat.com' ),
    ),
        array(
        'name'    => 'SoundCloud',
        'type'    => 'Social',
        'domains' => array( 'soundcloud.com' ),
    ),
        array(
        'name'    => 'Stack Overflow',
        'type'    => 'Social',
        'domains' => array( 'stackoverflow.com' ),
    ),
        array(
        'name'    => 'StumbleUpon',
        'type'    => 'Social',
        'domains' => array( 'www.stumbleupon.com' ),
    ),
        array(
        'name'    => 'Telegram',
        'type'    => 'Social',
        'domains' => array( 'telegram.org' ),
    ),
        array(
        'name'    => 'Weibo',
        'type'    => 'Social',
        'domains' => array( 'weibo.com' ),
    ),
        array(
        'name'    => 'Tumblr',
        'type'    => 'Social',
        'domains' => array( 'www.tumblr.com' ),
    ),
        array(
        'name'    => 'Twitch',
        'type'    => 'Social',
        'domains' => array( 'www.twitch.tv' ),
    ),
        array(
        'name'    => 'Vimeo',
        'type'    => 'Social',
        'domains' => array( 'vimeo.com' ),
    ),
        array(
        'name'    => 'WeChat',
        'type'    => 'Social',
        'domains' => array( 'www.wechat.com' ),
    ),
        array(
        'name'    => 'WhatsApp',
        'type'    => 'Social',
        'domains' => array( 'www.whatsapp.com' ),
    ),
        array(
        'name'    => 'Udemy',
        'type'    => 'Social',
        'domains' => array( 'www.udemy.com' ),
    ),
        array(
        'name'    => '500px',
        'type'    => 'Social',
        'domains' => array( '500px.com' ),
    ),
        array(
        'name'    => 'WordPress.org',
        'type'    => 'Social',
        'domains' => array(
        'wordpress.org',
        'ru.wordpress.org',
        'ca.wordpress.org',
        'fr.wordpress.org',
        'pl.wordpress.org',
        'id.wordpress.org',
        'ur.wordpress.org',
        'bn.wordpress.org',
        'ja.wordpress.org',
        'pt.wordpress.org',
        'it.wordpress.org',
        'hy.wordpress.org',
        'hi.wordpress.org',
        'de.wordpress.org',
        'ar.wordpress.org',
        'es.wordpress.org',
        'cn.wordpress.org'
    ),
    )
    ) ;
    public function __construct()
    {
        add_action( 'apvc_start_migration', array( $this, 'start_migration' ) );
    }
    
    public function is_migrating()
    {
        $migration_status = get_option( 'apvc_migration_process_bulk_90x', true );
        if ( 'inprogress' === $migration_status ) {
            return true;
        }
        return false;
    }
    
    public function start_migration()
    {
        global  $wpdb ;
        $table = $wpdb->prefix . 'postmeta';
        $wpdb->delete( $table, array(
            'meta_key' => 'post_starting_count',
        ) );
        update_option( 'apvc_migration_process_bulk_90x', 'completed' );
        update_option( 'apvc_db_version', '1' );
    }
    
    public function migrating_data( $chunkIndex, $chunkSize )
    {
        global  $wpdb ;
        $apvc_query = new APVC_Query();
        $old_visits_table = $wpdb->prefix . $apvc_query::OLD_STATS_TABLE;
        update_option( 'apvc_migration_process_bulk_90x', 'in_progress' );
        //		$total_records = $wpdb->get_var( $wpdb->prepare( 'SELECT * FROM ' . $old_visits_table . ' LIMIT %d, %d', $chunkIndex, $chunkSize ) );
        //		$progress      = ( $chunkSize / $total_records ) * 100; // 20
        if ( 0 == $chunkIndex ) {
            self::create_tables_and_settings();
        }
        $status = self::create_records_to_starting_number();
        
        if ( is_null( $status ) ) {
            return wp_send_json_success( array(
                'status' => 'completed',
            ) );
        } else {
            return wp_send_json_success( array(
                'status' => 'migrating',
            ) );
        }
        
        return;
        $status = self::create_records( $chunkIndex, $chunkSize );
        $migrated = get_option( 'apvc_migrated_records', true );
        
        if ( is_null( $status ) ) {
            return wp_send_json_success( array(
                'total_records' => $total_records,
                'status'        => 'completed',
                'per_iteration' => $progress,
                'migrated_rec'  => $migrated,
            ) );
        } else {
            return wp_send_json_success( array(
                'total_records' => $total_records,
                'progress'      => $progress,
                'status'        => 'migrating',
                'per_iteration' => $progress,
                'migrated_rec'  => $migrated,
            ) );
        }
    
    }
    
    public static function create_records_to_starting_number()
    {
        global  $wpdb ;
        $apvc_query = new APVC_Query();
        $history_table = $wpdb->prefix . $apvc_query::OLD_STATS_TABLE;
        $old_records = $wpdb->get_results( 'SELECT COUNT(*) AS views, article_id as id FROM ' . $history_table . ' WHERE article_id != 0 GROUP BY article_id' );
        foreach ( $old_records as $record ) {
            update_post_meta( $record->id, 'apvc_starting_count', $record->views );
        }
    }
    
    public static function create_records( $offset, $limit )
    {
        global  $wpdb ;
        $history_table = $wpdb->prefix . 'avc_page_visit_history';
        $old_records = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $history_table . ' LIMIT %d, %d', $offset, $limit ) );
        $data = array();
        $index = 0;
        foreach ( $old_records as $record ) {
            $data[$index]['rawData']['type'] = 'singular';
            $data[$index]['rawData']['singular_id'] = absint( $record->article_id );
            $data[$index]['rawData']['author_id'] = get_post_field( 'post_author', $record->article_id );
            $data[$index]['user_device']['device_type'] = $record->device_type;
            $data[$index]['user_device']['device_os'] = $record->operating_system;
            $data[$index]['user_device']['device_browser'] = $record->browser_full_name;
            $data[$index]['user_device']['device_browser_ver'] = $record->browser_version;
            $data[$index]['referrer_url'] = $record->http_referer;
            $data[$index]['ip_address'] = $record->ip_address;
            $data[$index]['user_agent'] = $record->browser_full_name . ' ' . $record->browser_version;
            $data[$index]['campaign'] = array();
            $date = DateTime::createFromFormat( 'Y-m-d H:i:s', $record->date );
            $last_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $record->last_date );
            $data[$index]['viewed_at'] = $date->format( 'Y-m-d H:i:s' );
            $data[$index]['signature'] = md5( APVC_Salt::request_payload_salt() . json_encode( $data[$index]['rawData'] ) );
            $ip_address = $record->ip_address;
            $user_agent = $data[$index]['user_agent'];
            $user_device = $data[$index]['user_device'];
            $visitor = new APVC_Visitor( $ip_address, $user_agent );
            $signature = md5( APVC_Salt::request_payload_salt() . json_encode( $data[$index]['rawData'] ) );
            $campaign = array();
            if ( $signature === $data[$index]['signature'] ) {
                new APVC_Register_View(
                    $data[$index]['rawData'],
                    $data[$index]['referrer_url'],
                    $visitor,
                    $campaign,
                    $date->format( 'Y-m-d H:i:s' ),
                    $user_device,
                    $last_date->format( 'Y-m-d H:i:s' )
                );
            }
            $index++;
        }
        return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $history_table . ' LIMIT %d, %d', $offset, $limit + 1 ) );
    }
    
    public static function create_tables_and_settings( $action = 'default' )
    {
        global  $wpdb ;
        $new_tables = $wpdb->prefix . 'apvc_page_views';
        if ( 'completed' === get_option( 'apvc_migration_process_bulk_90x', true ) && $wpdb->get_var( "SHOW TABLES LIKE '{$new_tables}'" ) == $new_tables ) {
            return;
        }
        $charset_collate = $wpdb->get_charset_collate();
        $apvc_query = new APVC_Query();
        if ( !is_dir( APVC_EXPORT_DIR ) ) {
            mkdir( APVC_EXPORT_DIR, 0777, true );
        }
        if ( !is_dir( APVC_GEO_DB_DIR ) ) {
            mkdir( APVC_GEO_DB_DIR, 0777, true );
        }
        
        if ( 0 === get_option( 'apvc_basic_settings', 0 ) ) {
            $option_value = array();
            $option_value['apvc_enable_counter'] = 'on';
            $option_value['apvc_post_types'] = array( 'post', 'page' );
            $option_value['apvc_exclude_users'] = array();
            $option_value['apvc_exclude_posts'] = array();
            $option_value['apvc_exclude_ips'] = array();
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_basic_settings', $option_value );
            }
        }
        
        
        if ( 0 === get_option( 'apvc_widget_settings', 0 ) ) {
            $option_value = array();
            $option_value['apvc_show_icon'] = 'on';
            $option_value['apvc_widget_display'] = 'disable';
            $option_value['apvc_default_counter_text_color'] = '#000000';
            $option_value['apvc_default_counter_border_color'] = '#000000';
            $option_value['apvc_default_background_color'] = '#E5E5E59E';
            $option_value['apvc_default_border_radius'] = 5;
            $option_value['apvc_default_border_style'] = 'solid';
            $option_value['apvc_default_font_style'] = 'normal';
            $option_value['apvc_default_border_width'] = 2;
            $option_value['apvc_widget_display_fe'] = 'center';
            $option_value['apvc_widget_width'] = 300;
            $option_value['apvc_widget_padding'] = 5;
            $option_value['apvc_default_label'] = __( 'Total Visits:', 'apvc' );
            $option_value['apvc_today_label'] = __( "Today's Visits:", 'apvc' );
            $option_value['apvc_total_counts_label'] = __( 'All time total visits:', 'apvc' );
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_settings', $option_value );
            }
        }
        
        
        if ( 0 === get_option( 'apvc_widget_visibility_settings', 0 ) ) {
            $option_value = array();
            $option_value['apvc_total_visits_cr_page'] = '';
            $option_value['apvc_show_total_counts'] = '';
            $option_value['apvc_show_today_counts'] = 'on';
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_visibility_settings', $option_value );
            }
        }
        
        
        if ( 0 === get_option( 'apvc_widget_template_settings', 0 ) ) {
            $option_value = array();
            $option_value['apvc_widget_templates'] = '-';
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_widget_template_settings', $option_value );
            }
        }
        
        
        if ( 0 === get_option( 'apvc_advanced_settings', 0 ) ) {
            $option_value = array();
            $option_value['apvc_show_shorthand_counter'] = '';
            $option_value['apvc_track_loggedin_users'] = '';
            if ( is_array( $option_value ) && !empty($option_value) ) {
                update_option( 'apvc_advanced_settings', $option_value );
            }
        }
        
        $referrers_table = $apvc_query::get_table_name( $apvc_query::REFERRERS );
        $wpdb->query( 'DROP TABLE `' . $referrers_table . '`' );
        if ( self::is_table_exists( $referrers_table ) ) {
            $wpdb->query( "CREATE TABLE {$referrers_table} (\n\t               id bigint(20) UNSIGNED AUTO_INCREMENT,\n\t               domain_name varchar(256) NOT NULL,\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $sessions_table = $apvc_query::get_table_name( $apvc_query::SESSIONS );
        $wpdb->query( 'DROP TABLE `' . $sessions_table . '`' );
        if ( self::is_table_exists( $sessions_table ) ) {
            $wpdb->query( "CREATE TABLE {$sessions_table} (\n\t               user_session_id bigint(20) unsigned AUTO_INCREMENT,\n\t               visitor_id varchar(256) NULL,\n\t               first_view_id bigint(20) unsigned NULL,\n\t               last_view_id bigint(20) unsigned NULL,\n\t               referrer_id bigint(20) unsigned NULL,\n\t               campaign_id bigint(20) unsigned NULL,\n\t               created_at datetime,\n\t               device_type varchar(256) NULL,\n\t               device_os varchar(256) NULL,\n\t               device_browser varchar(256) NULL,\n\t               device_browser_ver varchar(256) NULL,\n\t               city_id bigint(20) unsigned NULL,\n\t               state_id bigint(20) unsigned NULL,\t\n\t               country_id bigint(20) unsigned NULL,\t               \n\t               site_id bigint(20) unsigned NULL,\n\t               session_ended_at datetime NULL,\n\t               PRIMARY KEY (user_session_id),\n\t               INDEX (visitor_id, first_view_id, last_view_id,referrer_id,campaign_id)\n\t           ) {$charset_collate}" );
        }
        $cities_table = $apvc_query::get_table_name( $apvc_query::CITIES );
        $wpdb->query( 'DROP TABLE `' . $cities_table . '`' );
        if ( self::is_table_exists( $cities_table ) ) {
            $wpdb->query( "CREATE TABLE {$cities_table} (\n\t               city_id bigint(20) unsigned AUTO_INCREMENT,\n\t               country_id bigint(20) unsigned,\n\t               city varchar(64) NULL,\n\t               PRIMARY KEY (city_id),\n\t               INDEX (city_id, country_id)\n\t           ) {$charset_collate}" );
        }
        $states_table = $apvc_query::get_table_name( $apvc_query::STATES );
        if ( self::is_table_exists( $states_table ) ) {
            $wpdb->query( "CREATE TABLE {$states_table} (\n\t               state_id bigint(20) unsigned AUTO_INCREMENT,\n\t               country_id bigint(20) unsigned,\n\t               state_name varchar(64) NULL,\n\t               PRIMARY KEY (country_id),\n\t               INDEX (state_id, country_id)\n\t           ) {$charset_collate}" );
        }
        $countries_table = $apvc_query::get_table_name( $apvc_query::COUNTRIES );
        $wpdb->query( 'DROP TABLE `' . $countries_table . '`' );
        if ( self::is_table_exists( $countries_table ) ) {
            $wpdb->query( "CREATE TABLE {$countries_table} (\n\t               country_id bigint(20) unsigned AUTO_INCREMENT,\n\t               country_code varchar(4) NULL,\n\t               country varchar(64) NULL,\n\t               continent varchar(16) NULL,\n\t               PRIMARY KEY (country_id),\n\t               INDEX (country_id, country)\n\t           ) {$charset_collate}" );
        }
        $views_table = $apvc_query::get_table_name( $apvc_query::VIEWS );
        $wpdb->query( 'DROP TABLE `' . $views_table . '`' );
        if ( self::is_table_exists( $views_table ) ) {
            $wpdb->query( "CREATE TABLE {$views_table} (\n\t               id bigint(20) unsigned AUTO_INCREMENT,\n\t               page_id bigint(20) unsigned,\n\t               viewed_at datetime NULL DEFAULT CURRENT_TIMESTAMP,\n\t               page bigint(20) unsigned,\n\t               user_session_id bigint(20) unsigned NULL,\n\t               exit_page varchar(256) NULL,\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $views_data_table = $apvc_query::get_table_name( $apvc_query::VIEWS_DATA );
        $wpdb->query( 'DROP TABLE `' . $views_data_table . '`' );
        if ( self::is_table_exists( $views_data_table ) ) {
            $wpdb->query( "CREATE TABLE {$views_data_table} (\n\t               id bigint(20) unsigned AUTO_INCREMENT,\n\t               type varchar(256),\n\t               singular_id bigint(20) unsigned NULL,\n\t               author_id bigint(20) unsigned NULL,\n\t               date_archive varchar(256) NULL,\n\t               search_querystring varchar(256) NULL,\n\t               post_type varchar(256) NULL,\n\t               term_id varchar(256) NULL,\n\t               stored_title varchar(256) NULL,\n\t               stored_url varchar(256) NULL,\n\t               stored_type varchar(256) NULL,\n\t               stored_type_label varchar(256) NULL,\n\t               stored_author_id varchar(256) NULL,\n\t               stored_author varchar(256) NULL,\n\t               stored_date varchar(256) NULL,\n\t               stored_category varchar(256) NULL,\n\t               views bigint(20),\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $referrer_types_table = $apvc_query::get_table_name( $apvc_query::REFERRER_TYPES );
        $wpdb->query( 'DROP TABLE `' . $referrer_types_table . '`' );
        
        if ( self::is_table_exists( $referrer_types_table ) ) {
            $wpdb->query( "CREATE TABLE {$referrer_types_table} (\n\t               referrer_group_id bigint(20) unsigned AUTO_INCREMENT,\n\t               name varchar(2048),\n\t               domain varchar(2048),\n\t               domain_to_match varchar(2048),\n\t               type ENUM('Search', 'Social', 'Referrer'),\n\t               PRIMARY KEY (referrer_group_id)\n\t           ) {$charset_collate}" );
            global  $wpdb ;
            $known_referrers = self::$referrers;
            // Upsert predefined groups
            foreach ( $known_referrers as $group ) {
                foreach ( $group['domains'] as $domain ) {
                    $existing_domain = $wpdb->get_row( $wpdb->prepare(
                        "SELECT * FROM {$referrer_types_table} WHERE name = %s AND domain = %s AND domain_to_match = %s AND type = %s",
                        $group['name'],
                        $group['domains'][0],
                        $domain,
                        $group['type']
                    ) );
                    $domain_not_found = \is_null( $existing_domain );
                    if ( $domain_not_found ) {
                        $wpdb->insert( $referrer_types_table, array(
                            'name'            => $group['name'],
                            'domain'          => $group['domains'][0],
                            'domain_to_match' => $domain,
                            'type'            => $group['type'],
                        ) );
                    }
                }
            }
        }
        
        $wc_orders_table = $apvc_query::get_table_name( $apvc_query::WC_ORDERS );
        $wpdb->query( 'DROP TABLE `' . $wc_orders_table . '`' );
        if ( self::is_table_exists( $wc_orders_table ) ) {
            $wpdb->query( "CREATE TABLE {$wc_orders_table} (\n\t               order_id bigint(20) unsigned,\n\t               view_id bigint(20) unsigned,\n\t               visitor_id varchar(2048) NULL,\n\t               order_total bigint(20) unsigned,\n\t               total_refunded bigint(20) unsigned,\n\t               total_refunds bigint(20) unsigned,\n\t               discount bigint(20) unsigned,\n\t               coupon_used varchar(2048) NULL,\n\t               integration varchar(2048) NULL,\n\t               order_status varchar(2048) NULL,\n\t               created_at datetime,\n\t               PRIMARY KEY (order_id)\n\t           ) {$charset_collate}" );
        }
        $shortcodes_table = $apvc_query::get_table_name( $apvc_query::SHORTCODES );
        $wpdb->query( 'DROP TABLE `' . $shortcodes_table . '`' );
        if ( self::is_table_exists( $shortcodes_table ) ) {
            $wpdb->query( "CREATE TABLE {$shortcodes_table} (\n\t               id bigint(20) unsigned AUTO_INCREMENT,\n\t               title varchar(2048),\n\t               shortcode varchar(2048),\n\t               configuration varchar(2048),\n\t               date datetime,\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $wpdb->query( 'TRUNCATE TABLE `' . $referrers_table . '`' );
        $wpdb->query( 'TRUNCATE TABLE `' . $sessions_table . '`' );
        $wpdb->query( 'TRUNCATE TABLE `' . $views_table . '`' );
        $wpdb->query( 'TRUNCATE TABLE `' . $views_data_table . '`' );
        $wpdb->query( 'TRUNCATE TABLE `' . $referrer_types_table . '`' );
        $wpdb->query( 'TRUNCATE TABLE `' . $shortcodes_table . '`' );
        if ( 'fresh' == $action ) {
            return update_option( 'apvc_migration_process_bulk_90x', 'completed', false );
        }
        return update_option( 'apvc_version', ADVANCED_PAGE_VISIT_COUNTER_VERSION, false );
    }
    
    public static function migrate_records_count()
    {
        global  $wpdb ;
        $history_table = $wpdb->prefix . 'avc_page_visit_history';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$history_table}'" ) == $history_table ) {
            return $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) AS count FROM ' . $history_table . '  WHERE id > %d', 0 ) );
        }
        return;
    }
    
    public static function is_table_exists( $table_name )
    {
        global  $wpdb ;
        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
        if ( !$wpdb->get_var( $query ) == $table_name ) {
            return true;
        }
        return false;
    }
    
    public static function update_migration()
    {
        update_option( 'apvc_migration_process_bulk_90x', 'completed' );
    }
    
    public static function start_fresh( $action )
    {
        return self::create_tables_and_settings( $action );
    }
    
    public static function clean_old_stats()
    {
        global  $wpdb ;
        $history_table = $wpdb->prefix . 'avc_page_visit_history';
        $location_table = $wpdb->prefix . 'apvc_user_locations';
        $wpdb->query( 'DROP TABLE IF EXISTS ' . $location_table );
        return $wpdb->query( 'DROP TABLE IF EXISTS ' . $history_table );
    }

}