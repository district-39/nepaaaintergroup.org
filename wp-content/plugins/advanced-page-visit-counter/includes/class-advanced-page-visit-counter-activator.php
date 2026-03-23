<?php

/**
 * Fired during plugin activation
 *
 * @link       https://iamankitpanchal.com
 * @since      1.0.0
 *
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Advanced_Page_Visit_Counter
 * @subpackage Advanced_Page_Visit_Counter/includes
 * @author     Page Visit Counter <developer@pagevisitcounter.com>
 */
class Advanced_Page_Visit_Counter_Activator
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
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        global  $wpdb ;
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
        if ( self::is_table_exists( $referrers_table ) ) {
            $wpdb->query( "CREATE TABLE {$referrers_table} (\n\t               id bigint(20) UNSIGNED AUTO_INCREMENT,\n\t               domain_name varchar(256) NOT NULL,\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $sessions_table = $apvc_query::get_table_name( $apvc_query::SESSIONS );
        if ( self::is_table_exists( $sessions_table ) ) {
            $wpdb->query( "CREATE TABLE {$sessions_table} (\n\t               user_session_id bigint(20) unsigned AUTO_INCREMENT,\n\t               visitor_id varchar(256) NULL,\n\t               first_view_id bigint(20) unsigned NULL,\n\t               last_view_id bigint(20) unsigned NULL,\n\t               referrer_id bigint(20) unsigned NULL,\n\t               campaign_id bigint(20) unsigned NULL,\n\t               created_at datetime,\n\t               device_type varchar(256) NULL,\n\t               device_os varchar(256) NULL,\n\t               device_browser varchar(256) NULL,\n\t               device_browser_ver varchar(256) NULL,\n\t               city_id bigint(20) unsigned NULL,\n\t               state_id bigint(20) unsigned NULL,\t               \n\t               country_id bigint(20) unsigned NULL,\t               \n\t               site_id bigint(20) unsigned NULL,\n\t               session_ended_at datetime NULL,\n\t               PRIMARY KEY (user_session_id),\n\t               INDEX (visitor_id, first_view_id, last_view_id,referrer_id,campaign_id)\n\t           ) {$charset_collate}" );
        }
        $cities_table = $apvc_query::get_table_name( $apvc_query::CITIES );
        if ( self::is_table_exists( $cities_table ) ) {
            $wpdb->query( "CREATE TABLE {$cities_table} (\n\t               city_id bigint(20) unsigned AUTO_INCREMENT,\n\t               country_id bigint(20) unsigned,\n\t               city varchar(64) NULL,\n\t               PRIMARY KEY (city_id),\n\t               INDEX (city_id, country_id)\n\t           ) {$charset_collate}" );
        }
        $states_table = $apvc_query::get_table_name( $apvc_query::STATES );
        if ( self::is_table_exists( $states_table ) ) {
            $wpdb->query( "CREATE TABLE {$states_table} (\n\t               state_id bigint(20) unsigned AUTO_INCREMENT,\n\t               country_id bigint(20) unsigned,\n\t               state_name varchar(64) NULL,\n\t               PRIMARY KEY (country_id),\n\t               INDEX (state_id, country_id)\n\t           ) {$charset_collate}" );
        }
        $countries_table = $apvc_query::get_table_name( $apvc_query::COUNTRIES );
        if ( self::is_table_exists( $countries_table ) ) {
            $wpdb->query( "CREATE TABLE {$countries_table} (\n\t               country_id bigint(20) unsigned AUTO_INCREMENT,\n\t               country_code varchar(4) NULL,\n\t               country varchar(64) NULL,\n\t               continent varchar(16) NULL,\n\t               PRIMARY KEY (country_id),\n\t               INDEX (country_id, country)\n\t           ) {$charset_collate}" );
        }
        $views_table = $apvc_query::get_table_name( $apvc_query::VIEWS );
        if ( self::is_table_exists( $views_table ) ) {
            $wpdb->query( "CREATE TABLE {$views_table} (\n\t               id bigint(20) unsigned AUTO_INCREMENT,\n\t               page_id bigint(20) unsigned,\n\t               viewed_at datetime NULL DEFAULT CURRENT_TIMESTAMP,\n\t               page bigint(20) unsigned,\n\t               user_session_id bigint(20) unsigned NULL,\n\t               exit_page varchar(256) NULL,\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $views_data_table = $apvc_query::get_table_name( $apvc_query::VIEWS_DATA );
        if ( self::is_table_exists( $views_data_table ) ) {
            $wpdb->query( "CREATE TABLE {$views_data_table} (\n\t               id bigint(20) unsigned AUTO_INCREMENT,\n\t               type varchar(256),\n\t               singular_id bigint(20) unsigned NULL,\n\t               author_id bigint(20) unsigned NULL,\n\t               date_archive varchar(256) NULL,\n\t               search_querystring varchar(256) NULL,\n\t               post_type varchar(256) NULL,\n\t               term_id varchar(256) NULL,\n\t               stored_title varchar(256) NULL,\n\t               stored_url varchar(256) NULL,\n\t               stored_type varchar(256) NULL,\n\t               stored_type_label varchar(256) NULL,\n\t               stored_author_id varchar(256) NULL,\n\t               stored_author varchar(256) NULL,\n\t               stored_date varchar(256) NULL,\n\t               stored_category varchar(256) NULL,\n\t               views bigint(20),\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        $referrer_types_table = $apvc_query::get_table_name( $apvc_query::REFERRER_TYPES );
        
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
        if ( self::is_table_exists( $wc_orders_table ) ) {
            $wpdb->query( "CREATE TABLE {$wc_orders_table} (\n\t               order_id bigint(20) unsigned,\n\t               view_id bigint(20) unsigned,\n\t               visitor_id varchar(2048) NULL,\n\t               order_total bigint(20) unsigned,\n\t               total_refunded bigint(20) unsigned,\n\t               total_refunds bigint(20) unsigned,\n\t               discount bigint(20) unsigned,\n\t               coupon_used varchar(2048) NULL,\n\t               integration varchar(2048) NULL,\n\t               order_status varchar(2048) NULL,\n\t               created_at datetime,\n\t               PRIMARY KEY (order_id)\n\t           ) {$charset_collate}" );
        }
        $shortcodes_table = $apvc_query::get_table_name( $apvc_query::SHORTCODES );
        if ( self::is_table_exists( $shortcodes_table ) ) {
            $wpdb->query( "CREATE TABLE {$shortcodes_table} (\n\t               id bigint(20) unsigned AUTO_INCREMENT,\n\t               title varchar(2048),\n\t               shortcode varchar(2048),\n\t               configuration varchar(2048),\n\t               date datetime,\n\t               PRIMARY KEY (id)\n\t           ) {$charset_collate}" );
        }
        
        if ( !file_exists( APVC_GEO_DB_DIR . '/apvc-location-database.mmdb' ) ) {
            $hook = 'apvc_download_geo_files';
            
            if ( !wp_get_schedule( $hook ) ) {
                $next_run = strtotime( '+10 second' );
                wp_schedule_single_event( $next_run, $hook );
            }
        
        }
        
        $old_data_table = $apvc_query::get_table_name( $apvc_query::OLD_STATS_TABLE, 'yes' );
        if ( !self::is_table_exists( $old_data_table ) && 'completed' !== get_option( 'apvc_migration_process_bulk_90x', true ) ) {
            update_option( 'apvc_migration_process_bulk_90x', 'not_started' );
        }
        update_option( 'apvc_version', '9.0.0', false );
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

}