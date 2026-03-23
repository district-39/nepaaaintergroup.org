<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Activation / Deactivation
 * @category Functions
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2016-03-17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** Activation  & Deactivation  of Booking Calendar  */
class WPBC_BookingInstall extends WPBC_Install {

    /**
	 * Overload Booking Calendar option names and some other parameters
     * 
     * //FixIn: 7.0.1.12 
     * Important! for correct loading of trasnaltions later, we must  do not use here loacale of plugin. So here will be untranslated strings!!!
     *      
     */    
    public function get_init_option_names() {
        
        add_bk_action( 'wpdev_booking_activate_user', array( $this, 'wpbc_activate') );        // Hook  for MU User activation 
        
        $links = array(
                  'option-version_num'                  => 'booking_version_num'
                , 'option-is_delete_if_deactive'        => 'booking_is_delete_if_deactive'
                , 'option-activation_process'           => 'booking_activation_process'
                , 'transient-wpbc_activation_redirect'  => '_booking_activation_redirect'
                , 'message-delete_data'                 =>  '<strong>' . 'Warning!' . '</strong> '
                                                            . 'All booking data will be deleted when the plugin is deactivated.' 
                                                            . '<br />'
                                                            . sprintf( 'If you want to save your booking data, please uncheck the %s"Delete booking data"%s at the' 
                                                                       , '<strong>', '</strong>') 
                                                            . '<a href="' . esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-settings' ), 'admin.php' ) ) ) 
                                                                     . '&scroll_to_section=wpbc_general_settings_uninstall_tab"> ' . 'settings page' . '.'
                                                            . ' </a>'
                , 'link_settings'                       => '<a class="wpbc_plugins_links__settings" href="' . esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-settings' ), 'admin.php' ) ) )
                                                                       . '">'. "Settings" .'</a>'
                , 'link_whats_new'                      => '<a title="'. "Check new functionality in this plugin update." .'" href="' 
                                                                       . esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about'  ), 'index.php' ) ) ) 
                                                                       .'">'. "What's New".'</a>'
                , 'link_faq' => '<a title="FAQ" href="https://wpbookingcalendar.com/faq/">FAQ</a>'
                , 'link_up'  => '<a title="Check Pro functionality" href="https://wpbookingcalendar.com/features/#link_up" style="color: rgb(0, 163, 42);font-weight:700;">Get Pro</a>'
                , 'link_upgrade'  => '<a title="Upgrade to higher version" href="'
														. esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-settings', 'tab' => 'upgrade' ), 'admin.php' ) ) )
														. '" style="color: rgb(0, 163, 42);font-weight:700;">Get More</a>'
        );

		return $links;
        
    }

	/** Check if was updated from lower to  high version */
	public function is_update_from_lower_to_high_version() {

		$is_make_activation = false;

		// Check  conditions for different version about Upgrade
		if ( ( class_exists( 'wpdev_bk_personal' ) ) && ( ! wpbc_is_table_exists( 'bookingtypes' ) ) ) {
			$is_make_activation = true;
		}
		if ( ( ! $is_make_activation ) && ( class_exists( 'wpdev_bk_biz_s' ) ) && ( wpbc_is_field_in_table_exists( 'booking', 'pay_request' ) == 0 ) ) {
			$is_make_activation = true;
		}
		// FixIn: 9.9.0.13.
		if ( ( ! $is_make_activation ) && ( class_exists( 'wpdev_bk_biz_m' ) ) && ( ! wpbc_is_table_exists( 'booking_seasons' ) ) ) {
			$is_make_activation = true;
		}
		if ( ( ! $is_make_activation ) && ( class_exists( 'wpdev_bk_biz_l' ) ) && ( ! wpbc_is_table_exists( 'booking_coupons' ) ) ) {
			$is_make_activation = true;
		}
		if ( ( ! $is_make_activation ) && ( class_exists( 'wpdev_bk_multiuser' ) ) && ( wpbc_is_field_in_table_exists( 'booking_coupons', 'users' ) == 0 ) ) {
			$is_make_activation = true;
		}

		return $is_make_activation;
	}

}




// <editor-fold     defaultstate="collapsed"                        desc=" Support & Maintence "  >

/** Reindex DataBase to have "date key field" for be able to sort by dates. */
function wpbc_reindex_booking_db(){ global $wpdb;
    $is_show_messages = false;
//    if ( ( wpbc_is_settings_page('QUERY_STRING') ) && ( strpos($_SERVER[ 'QUERY_STRING' ],'reindex_sort_data=1') !== false ) )        
//         $is_show_messages = true;


    if ($is_show_messages)  {
        // Hide all settings
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
        ?><style type="text/css" rel="stylesheet" >
            #post_option .meta-box {
                display:none;
            }
            #post_option .button-primary {
                display:none;
            }
            #post_option .technical-booking-section {
                display:block;
            }
        </style>
        <?php
    }

    if  (wpbc_is_field_in_table_exists('booking','sort_date') == 0) {
        $simple_sql  = "ALTER TABLE {$wpdb->prefix}booking ADD COLUMN sort_date datetime"; // FixIn: 10.12.1.5.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$wpdb->query( $simple_sql );
    }

    // Refill the sort date index.
    if  (wpbc_is_field_in_table_exists('booking','sort_date') != 0) {

		$bookings_res = array();

			// 1. Select  all bookings ID, where sort_date is NULL in wp_booking.
			$sql = " SELECT booking_id as id FROM {$wpdb->prefix}booking as bk WHERE sort_date IS NULL";

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$bookings_res = $wpdb->get_results( $sql );

		if ( $is_show_messages ) {
			/* translators: 1: ... */
			echo esc_html( sprintf( __( '%1$s Found %2$s not indexed bookings %3$s', 'booking' ), ' ', count( $bookings_res ), '<br/>' ) );
		}


		if ( count( $bookings_res ) > 0 ) {
            $id_string = '';
            foreach ($bookings_res as $value) {  $id_string .= $value->id . ','; }
            $id_string = substr($id_string,0,-1);

            //2. Select all (FIRST ??) booking_date, where booking_id = booking_id from #1 in wp_bookingdates
            $sql  = " SELECT booking_id as id, booking_date as date" ;
            $sql .= " FROM {$wpdb->prefix}bookingdates as bdt" ;
            $sql .= " WHERE booking_id IN ( ". $id_string ." ) GROUP BY bdt.booking_id ORDER BY bdt.booking_date " ;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$sort_date_array = $wpdb->get_results( $sql );

			if ( $is_show_messages ) {
				/* translators: 1: ... */
				echo esc_html( sprintf( __( '%1$s Finish getting sort dates. %2$s', 'booking' ), ' ', '<br/>' ) );
			}

            //3. Insert  that firtst date into the bookings in wp_booking
            $ii=0;
            foreach ($sort_date_array as $value) { $ii++;
                $sql  = "UPDATE {$wpdb->prefix}booking  ";
                $sql .= " SET sort_date = '".$value->date. "' WHERE booking_id  = ". $value->id . " ";
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$wpdb->query( $sql );

				if ( $is_show_messages ) {
					/* translators: 1: ... */
					echo esc_html( sprintf( __( 'Updated booking: %s', 'booking' ), $value->id . '  [' . $ii . ' / ' . count( $bookings_res ) . '] <br/>' ) );
				}
            }
        }

    }


}

// </editor-fold>



////////////////////////////////////////////////////////////////////////////////
//   A c t i v a t e    &    D e a c t i v a t e
////////////////////////////////////////////////////////////////////////////////

/** Activation */
function wpbc_booking_activate() {

	wpbc_load_translation();

    make_bk_action( 'wpbc_before_activation' );
    

    
    $version = wpbc_get_plugin_version_type();
    $is_demo = wpbc_is_this_demo();

	// -----------------------------------------------------------------------------------------------------------------
    // Options
	// -----------------------------------------------------------------------------------------------------------------
    $default_options_to_add = wpbc_get_default_options();
	// TODO: for Import / Export options,  we can  use this function to get all option_names and then just  get  the real  values from  the wp_options table.
    make_bk_action( 'wpbc_before_activation__add_options', $default_options_to_add );           // FixIn: 9.6.2.11.

    foreach ( $default_options_to_add as $default_option_name => $default_option_value ) {
        add_bk_option( $default_option_name, $default_option_value );
    }

    
	// -----------------------------------------------------------------------------------------------------------------
    // DB Tables
	// -----------------------------------------------------------------------------------------------------------------
    if ( true ){

		global $wpdb;

		// ----------------------------------------------------------------------------
		// Charset / Collation
		// ----------------------------------------------------------------------------
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		// ----------------------------------------------------------------------------
		// Table name
		// ----------------------------------------------------------------------------
		$table_name = $wpdb->prefix . 'booking';

		// ----------------------------------------------------------------------------
		// Queries queue for upgrades only
		// ----------------------------------------------------------------------------
		$wp_queries = array();

		// ----------------------------------------------------------------------------
		// 1) First install: CREATE booking TABLE.
		// ----------------------------------------------------------------------------
		if ( ! wpbc_is_table_exists( 'booking' ) ) {

			$create_sql = "CREATE TABLE {$table_name} (
							booking_id bigint(20) unsigned NOT NULL auto_increment,
							booking_type bigint(10) NOT NULL default 1,

							form TEXT,
							hash TEXT,
							booking_options TEXT,
							status varchar(200) NOT NULL default '',
					
							is_new bigint(10) NOT NULL default 1,
							sync_gid varchar(200) NOT NULL default '',
							trash bigint(10) NOT NULL default 0,
							is_trash datetime,
				
							sort_date datetime,
							modification_date datetime,				
							creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				
							PRIMARY KEY  (booking_id)
						) {$charset_collate};";

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->query( $create_sql );

			// ----------------------------------------------------------------------------
			// 2) Upgrade path: only add missing columns (your current logic)
			// ----------------------------------------------------------------------------
		} else {

			if ( wpbc_is_field_in_table_exists( 'booking', 'form' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN form TEXT";
			}

			if ( wpbc_is_field_in_table_exists( 'booking', 'modification_date' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN modification_date datetime";
			}

			// FixIn: 9.2.3.3.
			if ( wpbc_is_field_in_table_exists( 'booking', 'creation_date' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
			}

			if ( wpbc_is_field_in_table_exists( 'booking', 'sort_date' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN sort_date datetime";
			}

			if ( wpbc_is_field_in_table_exists( 'booking', 'status' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN status varchar(200) NOT NULL default ''";
			}

			if ( wpbc_is_field_in_table_exists( 'booking', 'is_new' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN is_new bigint(10) NOT NULL default 1";
			}

			// Version: 5.2 - Google ID for Sync
			if ( wpbc_is_field_in_table_exists( 'booking', 'sync_gid' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN sync_gid varchar(200) NOT NULL default ''";
			}

			// FixIn: 9.2.3.5.
			if ( wpbc_is_field_in_table_exists( 'booking', 'is_trash' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN is_trash datetime";
			}

			// FixIn: 6.1.1.10.
			if ( wpbc_is_field_in_table_exists( 'booking', 'trash' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN trash bigint(10) NOT NULL default 0";
			}

			// FixIn: 9.1.2.12.
			if ( wpbc_is_field_in_table_exists( 'booking', 'booking_options' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN booking_options TEXT";
			}

			if ( wpbc_is_field_in_table_exists( 'booking', 'hash' ) == 0 ) {
				$wp_queries[] = "ALTER TABLE {$table_name} ADD COLUMN hash TEXT";

				// Update hash  value only in last 100 bookings.
				$sql_check_table = "SELECT booking_id as id FROM {$wpdb->prefix}booking  ORDER BY booking_id DESC LIMIT 0, 100";

				$res = $wpdb->get_results( $sql_check_table );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				foreach ( $res as $l ) {
					$wp_queries[] = "UPDATE {$wpdb->prefix}booking SET hash = MD5('" . time() . '_' . wp_rand( 1000, 1000000 ) . "') WHERE booking_id = " . $l->id;  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				}
			}
		}

		// ----------------------------------------------------------------------------
		// Execute queued upgrade queries (only runs on upgrades, not new install)
		// ----------------------------------------------------------------------------
		if ( ! empty( $wp_queries ) ) {
			foreach ( $wp_queries as $sql ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$wpdb->query( $sql );
			}
		}


		$is_insert_test_bookings = false;

		// ----------------------------------------------------------------------------
		// 2. Install: CREATE bookingdate TABLE.
		// ----------------------------------------------------------------------------

		if ( ! wpbc_is_table_exists( 'bookingdates' ) ) {
			// Check if tables not exist yet.
			$simple_sql = "CREATE TABLE {$wpdb->prefix}bookingdates (
                     booking_dates_id bigint(20) unsigned NOT NULL auto_increment,
                     booking_id bigint(20) unsigned NOT NULL,
                     booking_date datetime NOT NULL default '0000-00-00 00:00:00',
                     approved bigint(20) unsigned NOT NULL default 0,
                     PRIMARY KEY  (booking_dates_id)
                    ) {$charset_collate}";

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->query( $simple_sql );

			if ( ! class_exists( 'wpdev_bk_personal' ) ) {
				$is_insert_test_bookings = true;
			}
		}

		if ( 0 === wpbc_is_index_in_table_exists( 'bookingdates', 'booking_id_dates' ) ) {
			// If we remove this index,  so  then its can  significant impact  to the speed of page loading at the Booking Listing and Timelines.
			// Index for SPEED opening Booking Listing and Timeline with  thousands of Bookings,  otherwise,  without this index, speed will be TOO SLOW...
			$simple_sql = "CREATE UNIQUE INDEX booking_id_dates ON {$wpdb->prefix}bookingdates ( booking_id, booking_date);";
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
			$wpdb->query( $simple_sql );
		}

		// ----------------------------------------------------------------------------
		// 3. Insert test bookings.
		// ----------------------------------------------------------------------------

		if ( $is_insert_test_bookings ) {
			// -- Test Booking #1 --
			$is_appr    = 1;
			$wp_queries_sub = "INSERT INTO {$wpdb->prefix}booking ( form, modification_date ) VALUES (
				 'text^name1^John~text^secondname1^Smith~text^email1^example-free@wpbookingcalendar.com~text^phone1^458-77-77~textarea^details1^This is a test booking showing booking for several days.', " . wpbc_sql_date_math_expr_explicit('', 'now') . " );";
			$wpdb->query( $wp_queries_sub );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

			$temp_id        = $wpdb->insert_id;
			$wp_queries_sub = "INSERT INTO {$wpdb->prefix}bookingdates ( booking_id, booking_date, approved  ) VALUES
					( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL 2 DAY", 'curdate' ) . " ," . $is_appr . "  ),
					( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL 3 DAY", 'curdate' ) . " ," . $is_appr . "  ),
					( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL 4 DAY", 'curdate' ) . " ," . $is_appr . "  );";
			$wpdb->query( $wp_queries_sub );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

			// -- Test Booking #2 --
			$is_appr    = 0;
			$wp_queries_sub = "INSERT INTO {$wpdb->prefix}booking ( form, modification_date ) VALUES (
				 'text^name1^Emma~text^secondname1^Robinson~text^email1^example-free@wpbookingcalendar.com~text^phone1^999-77-77~textarea^details1^This is a test booking showing booking for several days.', " . wpbc_sql_date_math_expr_explicit('', 'now') . " );";
			$wpdb->query( $wp_queries_sub );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

			$temp_id        = $wpdb->insert_id;
			$wp_queries_sub = "INSERT INTO {$wpdb->prefix}bookingdates ( booking_id, booking_date, approved  ) VALUES
					( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL 28 DAY", 'curdate' ) . " ," . $is_appr . "  ),
					( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL 29 DAY", 'curdate' ) . " ," . $is_appr . "  ),
					( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL 30 DAY", 'curdate' ) . " ," . $is_appr . "  );";
			$wpdb->query( $wp_queries_sub );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter


			// -- Test Booking #3 --
			$is_appr    = 1;
			$start_time = '10:00';
			$end_time   = '10:30';
			$wp_queries_sub = "INSERT INTO {$wpdb->prefix}booking ( form, modification_date, is_new ) VALUES (
				 'selectbox^rangetime1^".$start_time." - ".$end_time."~text^name1^Sophia~text^secondname1^Robinson~text^email1^example-free@wpbookingcalendar.com~text^phone1^458-77-77~textarea^details1^This is a test booking showing a one day time slot booking.', " . wpbc_sql_date_math_expr_explicit('', 'now') . ", 0 );";
			$wpdb->query( $wp_queries_sub );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter

			$temp_id = $wpdb->insert_id;

			$start_time_arr = explode( ':', $start_time );
			$end_time_arr   = explode( ':', $end_time );
			$wp_queries_sub = "INSERT INTO {$wpdb->prefix}bookingdates ( booking_id, booking_date, approved ) VALUES
								( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL '" . ( ( 8 * 24 ) + intval( $start_time_arr[0] ) ) . ':' . $start_time_arr[1] . ":01' HOUR_SECOND", 'curdate' ) . " ," . $is_appr . " ),
								( " . $temp_id . ", " . wpbc_sql_date_math_expr_explicit( "+ INTERVAL '" . ( ( 8 * 24 ) + intval( $end_time_arr[0]   ) ) . ':' . $end_time_arr[1]   . ":02' HOUR_SECOND", 'curdate' ) . " ," . $is_appr . "  );";
			$wpdb->query( $wp_queries_sub );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		}
    }

	make_bk_action( 'wpbc_free_version_activation' );

	// -----------------------------------------------------------------------------------------------------------------
	// Other versions Activation
	// -----------------------------------------------------------------------------------------------------------------
	make_bk_action( 'wpbc_other_versions_activation' );

	wpbc_reindex_booking_db();

	make_bk_action( 'wpbc_after_activation' );
}
add_bk_action( 'wpbc_activation',  'wpbc_booking_activate' );



// Deactivate.
function wpbc_booking_deactivate() {

	// -----------------------------------------------------------------------------------------------------------------
    // Options
	// -----------------------------------------------------------------------------------------------------------------
    $default_options_to_add = wpbc_get_default_options();
    foreach ( $default_options_to_add as $default_option_name => $default_option_value) {
        
        delete_bk_option( $default_option_name );
    }   
    
    
	// -----------------------------------------------------------------------------------------------------------------
    // Widgets
	// -----------------------------------------------------------------------------------------------------------------
    delete_bk_option( 'widget_bookingwidget' );
    delete_bk_option( 'widget_bookingsearchwidget' );
    delete_bk_option( 'widget_bookingselectwidget' );
    delete_bk_option( 'booking_activation_redirect_for_version' );
    
    
	// -----------------------------------------------------------------------------------------------------------------
    // DB Tables
	// -----------------------------------------------------------------------------------------------------------------
    global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}booking" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}bookingdates" );

    // Delete all users booking windows states.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter,
	if ( false === $wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%booking_%'" ) ) {
        debuge_error('Error during deleting user meta at DB',__FILE__,__LINE__);
        die();
    }
	
	// Delete or Drafts and Pending from demo sites
	if ( wpbc_is_this_demo() ) {  // Delete all temp posts at the demo sites: (post_status = pending || draft) && ( post_type = post ) && (post_author != 1)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter,
		$postss = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE ( post_status = 'pending' OR  post_status = 'draft' OR  post_status = 'auto-draft' OR  post_status = 'trash' OR  post_status = 'inherit' ) AND ( post_type='post' OR  post_type='revision') AND post_author != 1" );
		foreach ( $postss as $pp ) {
			wp_delete_post( $pp->ID, true );
		}
	}
	make_bk_action( 'wpbc_free_version_deactivation' );
	////////////////////////////////////////////////////////////////////////////
	// Other versions Deactivation
	////////////////////////////////////////////////////////////////////////////
	make_bk_action( 'wpbc_other_versions_deactivation' );
}
add_bk_action( 'wpbc_deactivation',  'wpbc_booking_deactivate' );


/**
 * Default Options
 *
 * Exmaple of getting options for deleting in MU:
 *
 * $option_name = '';
 * $is_get_multiuser_general_options = true;
 * $options_for_delete = wpbc_get_default_options( $option_name, $is_get_multiuser_general_options );
 */
function wpbc_get_default_options( $option_name = '', $is_get_multiuser_general_options = false ) {
        
    $is_demo = wpbc_is_this_demo();    
    $blg_title = str_replace( array( '"', "'" ), '', get_option( 'blogname' ) );
        
    $default_options  = array();

	// If this option here, then system get this option from "Super Admin" options configuration for "Regular" users.
    $mu_option4delete = array();

	// FixIn: 9.6.3.5.
	$default_options['booking_setup_wizard_page_steps_is_done'] = '';
 $mu_option4delete[]='booking_setup_wizard_page_steps_is_done';

    $default_options['booking_admin_cal_count'] = ($is_demo) ? '3' : '2';       
 $mu_option4delete[]='booking_admin_cal_count';                                 // $multiuser_general_option[] = implode( '', array_keys( array_slice( $default_options, -1 ) ) );
    $default_options['booking_skin'] = '/css/skins/25_5__square_1.css';           // FixIn: 10.11.4.2.
 $mu_option4delete[]='booking_skin';
	// FixIn: 9.6.3.5.
    $default_options['booking_listing_default_view_mode'] = 'vm_booking_listing';	// 'vm_calendar';		// FixIn: 9.6.3.5.
 $mu_option4delete[]='booking_listing_default_view_mode';

    $default_options['booking_view_days_num'] = (  ( ! class_exists( 'wpdev_bk_personal' ) ) ? '90' : '30' );
 $mu_option4delete[]='booking_view_days_num';
 // FixIn: 8.9.4.3.
    $default_options['booking_calendar_overview__day_mode__days_number_show'] = (  ( ! class_exists( 'wpdev_bk_personal' ) ) ? '7' : '7' );
 $mu_option4delete[]='booking_calendar_overview__day_mode__days_number_show';
    $default_options['booking_timeline__month_mode__days_number_show'] = (  ( ! class_exists( 'wpdev_bk_personal' ) ) ? '7' : '7' );
 $mu_option4delete[]='booking_timeline__month_mode__days_number_show';
 // FixIn: 8.6.1.13.
    $default_options['booking_max_monthes_in_calendar'] = '2y';
 $mu_option4delete[]='booking_max_monthes_in_calendar';
    $default_options['booking_client_cal_count'] = '1';
 $mu_option4delete[]='booking_client_cal_count';    
    $default_options['booking_start_day_weeek'] = '0';
 $mu_option4delete[]='booking_start_day_weeek';     
    $default_options['booking_title_after_reservation'] =  ( ! class_exists( 'wpdev_bk_biz_s' ) )
															? __( 'Your booking is received. We will confirm it soon. Many thanks!', 'booking' )
															: __( 'Your booking is received. Please proceed with payment to confirm it. Many thanks!', 'booking' );
 $mu_option4delete[]='booking_title_after_reservation';    
//    $default_options['booking_title_after_reservation_time'] = '7000';
 //$mu_option4delete[]='booking_title_after_reservation_time';
    $default_options['booking_type_of_thank_you_message'] = 'message';
 $mu_option4delete[]='booking_type_of_thank_you_message';
    $default_options['booking_thank_you_page_URL'] = '/thank-you';
 $mu_option4delete[]='booking_thank_you_page_URL';
    $default_options['booking_is_use_autofill_4_logged_user'] = ($is_demo) ? 'On' : 'Off';
 $mu_option4delete[]='booking_is_use_autofill_4_logged_user';

if ( defined( 'WPBC_NEW_FORM_BUILDER' ) && WPBC_NEW_FORM_BUILDER ) {
	$default_options['booking_use_bfb_form'] = 'On';
$mu_option4delete[]                      = 'booking_use_bfb_form';
}
 	// FixIn: 10.13.1.5.
    $default_options['booking_is_use_phone_validation'] = 'Off';
 $mu_option4delete[]='booking_is_use_phone_validation';
    $default_options['booking_date_format'] = get_option( 'date_format' );
 $mu_option4delete[]='booking_date_format';
    $default_options['booking_time_format'] = get_option( 'time_format' );	//'H:i';
 $mu_option4delete[]='booking_time_format';

	//FixIn:10.2.0.1	------------------------------------------------------------------------------------------------
 	// Confirmation  section:
	$default_options['booking_confirmation_header_enabled'] = 'On';
	/* translators: 1: ... */
	$default_options['booking_confirmation_header']         = sprintf( __( 'Your booking id: %s', 'booking' ), '<strong>[booking_id]</strong>' );
$mu_option4delete[] = 'booking_confirmation_header_enabled';
$mu_option4delete[] = 'booking_confirmation_header';

	$default_options['booking_confirmation__personal_info__header_enabled'] = 'On';
	$default_options['booking_confirmation__personal_info__title']          =  __( 'Personal information', 'booking' );
	$default_options['booking_confirmation__personal_info__content']        = '[content]';
$mu_option4delete[] = 'booking_confirmation__personal_info__header_enabled';
$mu_option4delete[] = 'booking_confirmation__personal_info__title';
$mu_option4delete[] = 'booking_confirmation__personal_info__content';

	$default_options['booking_confirmation__booking_details__header_enabled'] = 'On';
	$default_options['booking_confirmation__booking_details__title']          = __( 'Booking details', 'booking' );
	$default_options['booking_confirmation__booking_details__content'] =  ( class_exists( 'wpdev_bk_personal' ) )
																				? "<h4>[resource_title]</h4>[readable_dates][readable_times]\n[add_to_google_cal_button]\n" . '<a href="[visitorbookingediturl]">Edit Booking</a> | <a href="[visitorbookingcancelurl]">Cancel Booking</a>'
																				: "[readable_dates][readable_times]\n[add_to_google_cal_button]";

$mu_option4delete[] = 'booking_confirmation__booking_details__header_enabled';
$mu_option4delete[] = 'booking_confirmation__booking_details__title';
$mu_option4delete[] = 'booking_confirmation__booking_details__content';
	//FixIn:10.2.0.1	End	--------------------------------------------------------------------------------------------

    $default_options['booking_date_view_type'] = 'short';
 $mu_option4delete[]='booking_date_view_type';        
    $default_options['booking_is_delete_if_deactive'] = ($is_demo) ? 'On' : 'Off';
 $mu_option4delete[]='booking_is_delete_if_deactive';       
    $default_options['booking_dif_colors_approval_pending'] = 'On';             // Depricated
    $default_options['booking_is_use_hints_at_admin_panel'] = 'On';
 $mu_option4delete[]='booking_is_use_hints_at_admin_panel';
    $default_options['booking_is_load_js_css_on_specific_pages'] = 'Off';
 $mu_option4delete[]='booking_is_nonce_at_front_end';                                // FixIn: 10.1.1.2.
    $default_options['booking_is_nonce_at_front_end'] = 'Off';
	// FixIn: 9.8.6.2.
 $mu_option4delete[]='booking_load_balancer_max_threads';
	$default_options['booking_load_balancer_max_threads'] = ( $is_demo ) ? '1' : '3';


//FixIn: 7.2.1.15	
 $mu_option4delete[]='booking_is_show_system_debug_log';
    $default_options['booking_is_show_system_debug_log'] = 'Off';


 $mu_option4delete[]='booking_is_load_js_css_on_specific_pages';
    $default_options['booking_pages_for_load_js_css'] = '';
 $mu_option4delete[]='booking_pages_for_load_js_css';
    $default_options['booking_type_of_day_selections'] = ( ( get_bk_option( 'booking_range_selection_is_active' ) == 'On' ) && ( ! $is_demo ) ) ?  'range' : 'multiple';
 $mu_option4delete[]='booking_type_of_day_selections';

 	// FixIn: 8.7.11.10.
	$default_options['booking_timeslot_picker'] = 'On';
$mu_option4delete[]= 'booking_timeslot_picker';
	$default_options['booking_timeslot_picker_skin'] = '/css/time_picker_skins/light__24_8.css';
$mu_option4delete[]= 'booking_timeslot_picker_skin';

    $default_options['booking_timeslot_day_bg_as_available'] = 'Off';	//(  ( class_exists( 'wpdev_bk_personal' ) ) ? 'Off' : 'On' );                                                   // FixIn: 8.2.1.27.
 $mu_option4delete[]='booking_timeslot_day_bg_as_available';


	$default_options['booking_disable_timeslots_in_tooltip'] = 'Off';													//FixIn: 9.5.0.2.2
 $mu_option4delete[]='booking_disable_timeslots_in_tooltip';

	$default_options['booking_highlight_timeslot_word'] = __( 'Booked Times:', 'booking' );								// FixIn: 9.4.3.1.
 $mu_option4delete[]='booking_highlight_timeslot_word';

    $default_options['booking_form_is_using_bs_css'] = 'Off';
 $mu_option4delete[]='booking_form_is_using_bs_css';  
    $default_options['booking_form_format_type'] = 'vertical';
 $mu_option4delete[]='booking_form_format_type';
 
 // Does not exist  in  MU
    $default_options['booking_form_field_active1'] = 'On';
    $default_options['booking_form_field_required1'] = 'On';
    $default_options['booking_form_field_label1'] = 'First Name';
    $default_options['booking_form_field_active2'] = 'On';
    $default_options['booking_form_field_required2'] = 'On';
    $default_options['booking_form_field_label2'] = 'Last Name';
    $default_options['booking_form_field_active3'] = 'On';
    $default_options['booking_form_field_required3'] = 'On';
    $default_options['booking_form_field_label3'] = 'Email';
    $default_options['booking_form_field_active4'] = 'On';
    $default_options['booking_form_field_required4'] = 'Off';
    $default_options['booking_form_field_label4'] = 'Phone';
    $default_options['booking_form_field_active5'] = 'On';
    $default_options['booking_form_field_required5'] = 'Off';
    $default_options['booking_form_field_label5'] = 'Details';
    $default_options['booking_form_field_active6'] = 'Off';
    $default_options['booking_form_field_required6'] = 'Off';
    $default_options['booking_form_field_label6'] = 'Visitors';
    $default_options['booking_form_field_values6'] = "1\n2\n3\n4";
    
    
    $default_options['booking_is_days_always_available'] = 'Off';    
 $mu_option4delete[]='booking_is_days_always_available';

	// FixIn: 8.3.2.2.
    $default_options['booking_is_show_pending_days_as_available'] = 'Off';
 $mu_option4delete[]='booking_is_show_pending_days_as_available';

    $default_options['booking_unavailable_days_num_from_today'] = '0';
 $mu_option4delete[]='booking_unavailable_days_num_from_today';      
    $default_options['booking_unavailable_day0'] = 'Off';
 $mu_option4delete[]='booking_unavailable_day0';      
    $default_options['booking_unavailable_day1'] = 'Off';
 $mu_option4delete[]='booking_unavailable_day1';  
    $default_options['booking_unavailable_day2'] = 'Off';
 $mu_option4delete[]='booking_unavailable_day2';  
    $default_options['booking_unavailable_day3'] = 'Off';
 $mu_option4delete[]='booking_unavailable_day3';  
    $default_options['booking_unavailable_day4'] = 'Off';
 $mu_option4delete[]='booking_unavailable_day4';  
    $default_options['booking_unavailable_day5'] = 'Off';
 $mu_option4delete[]='booking_unavailable_day5';  
    $default_options['booking_unavailable_day6'] = 'Off';       
 $mu_option4delete[]='booking_unavailable_day6';  
    $default_options['booking_menu_position'] = ( $is_demo ) ? 'top' : 'top';
 $mu_option4delete[]='booking_menu_position';  
    $default_options['booking_translation_load_from'] = 'wp.org';
 $mu_option4delete[]='booking_translation_load_from';

	// FixIn: 10.14.12.1.
    $default_options['booking_user_role_booking'] = 'editor';
 $mu_option4delete[]='booking_user_role_booking';  
    $default_options['booking_user_role_availability'] = 'editor';
 $mu_option4delete[]='booking_user_role_availability';
    $default_options['booking_user_role_addbooking'] = 'editor';
 $mu_option4delete[]='booking_user_role_addbooking';
    $default_options['booking_user_role_resources'] = 'editor';
 $mu_option4delete[]='booking_user_role_resources';  
    $default_options['booking_user_role_settings'] = 'editor';
 $mu_option4delete[]='booking_user_role_settings';  
//FixIn: 9.8.15.2.6
if ( class_exists( 'wpdev_bk_biz_m' ) ) {
		$default_options['booking_user_role_prices'] = 'editor';
	 $mu_option4delete[]='booking_user_role_prices';
}

    // New admin ///////////////////////////////////////////////////////////////
    
    $default_options['booking_is_email_reservation_adress'] = 'On';
    $default_options['booking_email_reservation_adress'] = htmlspecialchars( '"Booking system" <' . get_option( 'admin_email' ) . '>' );
    $default_options['booking_email_reservation_from_adress'] = '[visitoremail]';
    $default_options['booking_email_reservation_subject'] = __( 'New booking', 'booking' );
    /* translators: 1: ... */
    $default_options['booking_email_reservation_content'] = htmlspecialchars( sprintf( __( 'You need to approve a new booking %1$s for: %2$s Person detail information:%3$s Currently a new booking is waiting for approval. Please visit the moderation panel%4$sThank you, %5$s', 'booking' ), '[bookingtype]', '[dates]<br/><br/>', '<br/> [content]<br/><br/>', ' [moderatelink]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );

    // New Visitor /////////////////////////////////////////////////////////////
    
    $default_options['booking_is_email_newbookingbyperson_adress'] = 'Off';    
    $default_options['booking_email_newbookingbyperson_adress'] = htmlspecialchars( '"Booking system" <' . get_option( 'admin_email' ) . '>' );
    $default_options['booking_email_newbookingbyperson_subject'] = __( 'New booking', 'booking' );
    if ( class_exists( 'wpdev_bk_personal' ) )
        /* translators: 1: ... */
        $default_options['booking_email_newbookingbyperson_content'] = htmlspecialchars( sprintf( __( 'Your reservation %1$s for: %2$s is processing now! We will send confirmation by email. %3$sYou can edit this booking at this page: %4$s  Thank you, %5$s', 'booking' ), '[bookingtype]', '[dates]', '<br/><br/>[content]<br/><br/>', '[visitorbookingediturl]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );
    else
        /* translators: 1: ... */
        $default_options['booking_email_newbookingbyperson_content'] = htmlspecialchars( sprintf( __( 'Your reservation %1$s for: %2$s is processing now! We will send confirmation by email. %3$s Thank you, %4$s', 'booking' ), '[bookingtype]', '[dates]', '<br/><br/>[content]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );
    
    // Approval ////////////////////////////////////////////////////////////////
    
    $default_options['booking_is_email_approval_adress'] = 'On';
    $default_options['booking_is_email_approval_send_copy_to_admin'] = 'Off';
    $default_options['booking_email_approval_adress'] = htmlspecialchars( '"Booking system" <' . get_option( 'admin_email' ) . '>' );
    $default_options['booking_email_approval_subject'] = __( 'Your booking has been approved', 'booking' );
    if ( class_exists( 'wpdev_bk_personal' ) ) {
		/* translators: 1: ... */
		$default_options['booking_email_approval_content'] = htmlspecialchars( sprintf( __( 'Your reservation %1$s for: %2$s has been approved.%3$sYou can edit the booking on this page: %4$s Thank you, %5$s', 'booking' ), '[bookingtype]', '[dates]', '<br/><br/>[content]<br/><br/>', '[visitorbookingediturl]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );
	} else {
		/* translators: 1: ... */
		$default_options['booking_email_approval_content'] = htmlspecialchars( sprintf( __( 'Your booking %1$s for: %2$s has been approved.%3$sThank you, %4$s', 'booking' ), '[bookingtype]', '[dates]', '<br/><br/>[content]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );
	}

    // Decline /////////////////////////////////////////////////////////////////
    
    $default_options['booking_is_email_deny_adress'] = 'On';
    $default_options['booking_is_email_deny_send_copy_to_admin'] = 'Off';
    $default_options['booking_email_deny_adress'] = htmlspecialchars( '"Booking system" <' . get_option( 'admin_email' ) . '>' );
    $default_options['booking_email_deny_subject'] = __( 'Your booking has been declined', 'booking' );
    /* translators: 1: ... */
    $default_options['booking_email_deny_content'] = htmlspecialchars( sprintf( __( 'Your booking %1$s for: %2$s has been  canceled. %3$sThank you, %4$s', 'booking' ), '[bookingtype]', '[dates]', '<br/>[denyreason]<br/><br/>[content]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );

	// -----------------------------------------------------------------------------------------------------------------
    $default_options['booking_widget_title'] = __( 'Booking form', 'booking' );
    $default_options['booking_widget_show'] = 'booking_form';
    $default_options['booking_widget_type'] = '1';
    $default_options['booking_widget_calendar_count'] = '1';
    $default_options['booking_widget_last_field'] = '';

    $default_options['booking_wpdev_copyright_adminpanel'] = 'On';
 $mu_option4delete[]='booking_wpdev_copyright_adminpanel';    
    $default_options['booking_is_show_powered_by_notice'] = ( WPBC_IS_PLAYGROUND ) ? 'Off' : 'On';
 $mu_option4delete[]='booking_is_show_powered_by_notice';  
    $default_options['booking_form_theme'] = '';
 $mu_option4delete[]='booking_form_theme';
    $default_options['booking_is_use_captcha'] = 'Off';
 $mu_option4delete[]='booking_is_use_captcha';
    $default_options['booking_is_show_legend'] = 'On';
 $mu_option4delete[]='booking_is_show_legend';
    $default_options['booking_send_button_title'] = __( 'Send', 'booking' );                // FixIn: 8.8.1.14.
 $mu_option4delete[]='booking_send_button_title';
    $default_options['booking_legend_is_show_item_available'] = 'On';
 $mu_option4delete[]='booking_legend_is_show_item_available';          
    $default_options['booking_legend_text_for_item_available'] = __( 'Available', 'booking' );
 $mu_option4delete[]='booking_legend_text_for_item_available';
    $default_options['booking_legend_is_show_item_pending'] = 'On';
 $mu_option4delete[]='booking_legend_is_show_item_pending';    
    $default_options['booking_legend_text_for_item_pending'] = __( 'Pending', 'booking' );
 $mu_option4delete[]='booking_legend_text_for_item_pending';    
    $default_options['booking_legend_is_show_item_approved'] = 'On';
 $mu_option4delete[]='booking_legend_is_show_item_approved';    
    $default_options['booking_legend_text_for_item_approved'] = __( 'Booked', 'booking' );
 $mu_option4delete[]='booking_legend_text_for_item_approved';
	// FixIn: 9.9.0.5.
    $default_options['booking_legend_is_show_item_unavailable'] = 'Off';
 $mu_option4delete[]='booking_legend_is_show_item_unavailable';
    $default_options['booking_legend_text_for_item_unavailable'] = __( 'Unavailable', 'booking' );
 $mu_option4delete[]='booking_legend_text_for_item_unavailable';

    // FixIn: 10.1.5.5.
	$default_options['booking_legend_is_show_item_partially'] = ( ( class_exists( 'wpdev_bk_personal' ) ) && ( ! class_exists( 'wpdev_bk_biz_s' ) ) ) ? 'Off' : 'On';
 $mu_option4delete[]='booking_legend_is_show_item_partially';
	$default_options['booking_legend_text_for_item_partially'] = __( 'Partially booked', 'booking' );
 $mu_option4delete[]='booking_legend_text_for_item_partially';

    $default_options['booking_legend_is_show_numbers'] = 'On';                    //FixIn:6.0.1.4						// FixIn: 8.1.3.8.
 $mu_option4delete[]='booking_legend_is_show_numbers';
    $default_options['booking_legend_is_vertical'] = 'Off';                    											// FixIn: 9.4.3.6.
 $mu_option4delete[]='booking_legend_is_vertical';

    /*
     Import previous (old version) Email  templates
     or get defult values from Email Classes,  if the emails was not saved
     if the emails was saved,  in this case  - return empty array(), Useful  for the Email Settings page  to loading data from  DB,  during activation Mail API class.
     So if we get empty array for values, in this case,  we need to  get this optiosn from DB.
     */
    // Get NEW Email Templates default data or Import previous saved 
    $emails_init = wpbc_import6_email__new_admin__get_fields_array_for_activation();
    $emails_init = array_merge( $emails_init, wpbc_import6_email__new_visitor__get_fields_array_for_activation() );
    $emails_init = array_merge( $emails_init, wpbc_import6_email__approved__get_fields_array_for_activation() );
    $emails_init = array_merge( $emails_init, wpbc_import6_email__deleted__get_fields_array_for_activation() );
    $emails_init = array_merge( $emails_init, wpbc_import6_email__deny__get_fields_array_for_activation() );
    $emails_init = array_merge( $emails_init, wpbc_import6_email__trash__get_fields_array_for_activation() );
    if ( class_exists( 'wpdev_bk_personal' ) ) 
        $emails_init = array_merge( $emails_init, wpbc_import6_email__modification__get_fields_array_for_activation() );
    if ( class_exists( 'wpdev_bk_biz_s' ) ) 
        $emails_init = array_merge( $emails_init, wpbc_import6_email__payment_request__get_fields_array_for_activation() );    
    foreach ( $emails_init as $email_key_name => $email_values ) {

        if ( ! empty( $email_values ) ) $default_options[ $email_key_name ] = $email_values;
        else                            $default_options[ $email_key_name ] = get_bk_option( $email_key_name );
    }

        
 $mu_option4delete[]='booking_gcal_auto_import_is_active';                      // Creation  in   wpbc-gcal.php file 
 $mu_option4delete[]='booking_gcal_auto_import_time';    
 $mu_option4delete[]='booking_cron';                                            // Creation  in wpbc-cron.php

	// FixIn: 8.0.1.6.
	//$default_options['booking_form_structure_type'] = 'vertical';
	// $default_options['booking_form_structure_type'] = 'form_right';        // FixIn: 10.3.0.6.
	$default_options['booking_form_structure_type'] = 'wizard_services_a'; // FixIn: 10.11.4.3.
	$default_options['booking_menu_go_pro'] = ( WPBC_IS_PLAYGROUND ) ? 'hide' : 'show';    // show | hide

//	$default_options['booking_form_layout_width']       = '440';
//	$default_options['booking_form_layout_width_px_pr'] = 'px';
//	$default_options['booking_form_layout_max_cols']    = 1;

	// FixIn: 10.11.4.3.
	$default_options['booking_form_layout_width']       = '100';
	$default_options['booking_form_layout_width_px_pr'] = '%';
	$default_options['booking_form_layout_max_cols']    = 2;


	// -----------------------------------------------------------------------------------------------------------------
    // PS
	// -----------------------------------------------------------------------------------------------------------------
    if ( class_exists( 'wpdev_bk_personal' ) ) {

        $default_options['booking_is_use_simple_booking_form'] = 'Off';                                              	// FixIn: 8.1.1.12.
     $mu_option4delete[]='booking_is_use_simple_booking_form';

        $default_options['booking_is_use_codehighlighter_booking_form'] = 'On';                                         // FixIn: 8.4.7.18.
     $mu_option4delete[]='booking_is_use_codehighlighter_booking_form';

        $default_options['booking_form']        = str_replace( '\\n\\', '', wpbc_get_default_booking_form() );
        $default_options['booking_form_show']   = str_replace( '\\n\\', '', wpbc_get_default_booking_form_show() );

        $default_options['booking_url_bookings_edit_by_visitors'] = site_url();
     $mu_option4delete[]='booking_url_bookings_edit_by_visitors';

        $default_options['booking_url_bookings_listing_by_customer'] = site_url();										//FixIn: 8.1.3.5.1
     $mu_option4delete[]='booking_url_bookings_listing_by_customer';

        $default_options['booking_default_booking_resource'] = '';                  // All resources
     $mu_option4delete[]='booking_default_booking_resource'; 
        $default_options['booking_is_change_hash_after_approvement'] = 'On';     // FixIn: 10.10.1.1.
     $mu_option4delete[]='booking_is_change_hash_after_approvement'; 
        $default_options['booking_email_modification_adress'] = htmlspecialchars( '"Booking system" <' . get_option( 'admin_email' ) . '>' );
        $default_options['booking_email_modification_subject'] = __( 'The reservation has been modified', 'booking' );
        /* translators: 1: ... */
        $default_options['booking_email_modification_content'] = htmlspecialchars( sprintf( __( 'The reservation %1$s for: %2$s has been modified. %3$sYou can edit this booking on this page: %4$s  Thank you, %5$s', 'booking' ), '[bookingtype]', '[dates]', '<br/><br/>[content]<br/><br/>', '[visitorbookingediturl]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );
        $default_options['booking_is_email_modification_adress'] = 'On';
        $default_options['booking_is_email_modification_send_copy_to_admin'] = 'Off';
        $default_options['booking_resourses_num_per_page'] = '10';
     $mu_option4delete[]='booking_resourses_num_per_page';         
        $default_options['booking_default_title_in_day_for_calendar_view_mode'] = '[id]: [name] [secondname]';
     $mu_option4delete[]='booking_default_title_in_day_for_calendar_view_mode';

	// FixIn: 8.1.3.31.
        $default_options['booking_calendar_overview_start_time'] = '0';
     $mu_option4delete[]='booking_calendar_overview_start_time';
        $default_options['booking_calendar_overview_end_time'] = '24';
     $mu_option4delete[]='booking_calendar_overview_end_time';

        $default_options['booking_default_title_in_day_for_timeline_front_end'] = '[name] [secondname]';
     $mu_option4delete[]='booking_default_title_in_day_for_timeline_front_end';
	 // FixIn: 10.14.10.1.
        $default_options['booking_is_show_popover_in_timeline_front_end'] = 'Off';
     $mu_option4delete[]='booking_is_show_popover_in_timeline_front_end';
	 // FixIn: 9.6.3.5.

        $default_options['booking_send_emails_off_addbooking'] = 'Off';												 // FixIn: 8.4.5.4.
     $mu_option4delete[]='booking_send_emails_off_addbooking';

	    // FixIn: 9.6.3.5.

        $default_options['booking_change_resource_skip_checking'] = 'Off';												 // FixIn: 8.4.5.4.
     $mu_option4delete[]='booking_change_resource_skip_checking';

        $default_options['booking_log_booking_actions'] = 'On';												 		// FixIn: 8.6.1.10.
     $mu_option4delete[]='booking_log_booking_actions';


    }

	// FixIn: 8.5.1.1.
	//	$default_options['booking_ics_force_import'] = 'Off';								// FixIn: 8.4.7.1.
		$default_options['booking_ics_force_trash_before_import'] = 'Off';					// FixIn: 8.4.7.12.
	//$mu_option4delete[]='booking_ics_force_trash_before_import';   						// No need to delete ^
		$default_options['booking_ics_import_append_checkout_day'] = 'Off';                                             //FixIn: 9.6.2.7 // FixIn: 9.5.4.1.
	//$mu_option4delete[]='booking_ics_import_append_checkout_day';   			// No need to delete ^

	// FixIn: 9.8.15.8.
	$default_options['booking_condition_import_only_new'] = (  get_bk_option( 'booking_ics_force_import' ) === 'On' )   ?  'Off' : 'On';
	$default_options['booking_condition_import_if_available'] = 'Off';

	// FixIn: 10.1.5.4.
        $default_options['booking_recurrent_time'] =  ( class_exists( 'wpdev_bk_personal' ) ) ? 'Off' : 'On';
     $mu_option4delete[]='booking_recurrent_time';

        $default_options['booking_calendar_allow_several_months_on_mobile'] =  'Off';
     $mu_option4delete[]='booking_calendar_allow_several_months_on_mobile';

	// -----------------------------------------------------------------------------------------------------------------
    // BS
	// -----------------------------------------------------------------------------------------------------------------
    if ( class_exists( 'wpdev_bk_biz_s' ) ) {
        
     //$mu_option4delete[]='booking_paypal_price_period';                         // No need to  delete,  this option, because different users can  have different settings. During init defined at ../booking/inc/gateways/page-gateways.php

		$default_options['booking_gateways_order'] = 'stripe_v3,paypal_std_co,paypal,authorizenet,sage,redsys,bank_transfer,pay_cash,ipay88,ideal';   //     Default Original Payment Gateways
	//$mu_option4delete[]='booking_gateways_order';        	// No need to delete ^

		// FixIn: 8.1.3.29.
		$default_options['booking_ics_import_add_change_over_time'] = 'On';
	//$mu_option4delete[]='booking_ics_import_add_change_over_time';        	// No need to delete ^

		// FixIn: 8.5.2.3.
		$default_options['booking_is_ics_export_only_approved'] = 'Off';
	//$mu_option4delete[]='booking_is_ics_export_only_approved';   				// No need to delete ^
		$default_options['booking_is_ics_export_imported_bookings'] = '';												// FixIn: 8.8.3.19.


        $default_options['booking_auto_approve_new_bookings_is_active'] = 'Off';
     $mu_option4delete[]='booking_auto_approve_new_bookings_is_active';

	// FixIn: 8.1.3.27.
        $default_options['booking_auto_approve_bookings_when_import'] = 'Off';
     $mu_option4delete[]='booking_auto_approve_bookings_when_import';
        $default_options['booking_auto_approve_bookings_when_zero_cost'] = 'Off';
     $mu_option4delete[]='booking_auto_approve_bookings_when_zero_cost';
        $default_options['booking_auto_approve_bookings_if_added_in_admin_panel'] = 'Off';
     $mu_option4delete[]='booking_auto_approve_bookings_if_added_in_admin_panel';

        $default_options['booking_auto_cancel_pending_unpaid_bk_is_active'] = 'Off';
     $mu_option4delete[]='booking_auto_cancel_pending_unpaid_bk_is_active';      
        $default_options['booking_auto_cancel_pending_unpaid_bk_time'] = '24';
     $mu_option4delete[]='booking_auto_cancel_pending_unpaid_bk_time';      
        $default_options['booking_auto_cancel_pending_unpaid_bk_is_send_email'] = 'On';
     $mu_option4delete[]='booking_auto_cancel_pending_unpaid_bk_is_send_email';      
        $default_options['booking_auto_cancel_pending_unpaid_bk_email_reason'] = __( 'This booking canceled because we did not receive payment and the administrator did not approve it.', 'booking' );
     $mu_option4delete[]='booking_auto_cancel_pending_unpaid_bk_email_reason';          
        $default_options['booking_range_selection_type'] = 'fixed';
     $mu_option4delete[]='booking_range_selection_type';
        $default_options['booking_range_selection_days_count'] = '3';
     $mu_option4delete[]='booking_range_selection_days_count';
        $default_options['booking_range_selection_days_max_count_dynamic'] = 30;
     $mu_option4delete[]='booking_range_selection_days_max_count_dynamic';
        $default_options['booking_range_selection_days_specific_num_dynamic'] = '';
     $mu_option4delete[]='booking_range_selection_days_specific_num_dynamic';
        $default_options['booking_range_start_day'] = '-1';
     $mu_option4delete[]='booking_range_start_day';
        $default_options['booking_range_selection_days_count_dynamic'] = '1';
     $mu_option4delete[]='booking_range_selection_days_count_dynamic';
        $default_options['booking_range_start_day_dynamic'] = '-1';
     $mu_option4delete[]='booking_range_start_day_dynamic';
        $default_options['booking_range_selection_time_is_active'] = 'Off';
     $mu_option4delete[]='booking_range_selection_time_is_active';
        $default_options['booking_range_selection_start_time'] = '12:00';
     $mu_option4delete[]='booking_range_selection_start_time';
        $default_options['booking_range_selection_end_time'] = '10:00';
     $mu_option4delete[]='booking_range_selection_end_time';
        $default_options['booking_change_over_days_triangles'] =  'On';         					// FixIn: 7.0.1.24.

     $mu_option4delete[]='booking_last_checkout_day_available';
        $default_options['booking_last_checkout_day_available'] = 'Off';         										// FixIn: 8.1.3.28.

     $mu_option4delete[]='booking_change_over_days_triangles';     

     $mu_option4delete[]='booking_change_over__is_excerpt_on_pages';
        $default_options['booking_change_over__is_excerpt_on_pages'] = 'Off';         									// FixIn: 8.9.4.10.
     $mu_option4delete[]='booking_change_over__excerpt_on_pages';
        $default_options['booking_change_over__excerpt_on_pages'] = '';         										// FixIn: 8.9.4.10.


        $default_options['booking_email_payment_request_adress'] = htmlspecialchars( '"Booking system" <' . get_option( 'admin_email' ) . '>' );
        $default_options['booking_email_payment_request_subject'] = __( 'You need to make payment for this reservation', 'booking' );
        /* translators: 1: ... */
        $default_options['booking_email_payment_request_content'] = htmlspecialchars( sprintf( __( 'You need to make payment %1$s for reservation %2$s at %3$s. %4$s Please make payment on this page: %5$s  Thank you, %6$s', 'booking' ), '[cost]', '[bookingtype]', '[dates]', '<br/><br/>[paymentreason]<br/><br/>[content]<br/><br/>', '[visitorbookingpayurl]<br/><br/>', $blg_title . '<br/>[siteurl]' ) );
        $default_options['booking_is_email_payment_request_adress'] = 'On';
        $default_options['booking_is_email_payment_request_send_copy_to_admin'] = 'Off';
    }
    
    
	// -----------------------------------------------------------------------------------------------------------------
    // BM
	// -----------------------------------------------------------------------------------------------------------------
    if ( class_exists( 'wpdev_bk_biz_m' ) ) {   

	    // FixIn: 8.1.3.15.
        $default_options['booking_is_show_booked_data_in_tooltips'] = 'Off';
     $mu_option4delete[]='booking_is_show_booked_data_in_tooltips';
        $default_options['booking_booked_data_in_tooltips'] =  '[name] [secondname]';
     $mu_option4delete[]='booking_booked_data_in_tooltips';

        $default_options['booking_number_for_pre_checkin_date_hint'] = '14';            // FixIn: 10.0.0.31.
     $mu_option4delete[]='booking_number_for_pre_checkin_date_hint';
        $default_options['booking_available_days_num_from_today'] = '';
     $mu_option4delete[]='booking_available_days_num_from_today';
        $default_options['booking_unavailable_extra_in_out'] = '';
     $mu_option4delete[]='booking_unavailable_extra_in_out';
        $default_options['booking_unavailable_extra_minutes_in'] = '';
     $mu_option4delete[]='booking_unavailable_extra_minutes_in';
        $default_options['booking_unavailable_extra_minutes_out'] = '';
     $mu_option4delete[]='booking_unavailable_extra_minutes_out';
        $default_options['booking_unavailable_extra_days_in'] = '';
     $mu_option4delete[]='booking_unavailable_extra_days_in';
        $default_options['booking_unavailable_extra_days_out'] = '';
     $mu_option4delete[]='booking_unavailable_extra_days_out';

        $default_options['booking_forms_extended'] = serialize( array() );
        $default_options['booking_advanced_costs_values'] = serialize( array() );
        $default_options['booking_is_resource_deposit_payment_active'] = 'On';
     $mu_option4delete[]='booking_is_resource_deposit_payment_active';        
        $default_options['booking_advanced_costs_calc_fixed_cost_with_procents'] = 'Off';
        $default_options['booking_is_show_cost_in_tooltips'] = 'Off';
     $mu_option4delete[]='booking_is_show_cost_in_tooltips';
        $default_options['booking_highlight_cost_word'] = __( 'Cost: ', 'booking' );
     $mu_option4delete[]='booking_highlight_cost_word';        
        $default_options['booking_is_show_cost_in_date_cell'] = 'Off';
     $mu_option4delete[]='booking_is_show_cost_in_date_cell';      
        $default_options['booking_cost_in_date_cell_currency'] = '&#36;';
     $mu_option4delete[]='booking_cost_in_date_cell_currency';         
        $default_options['booking_visitor_number_rate'] = '0';                  //Depricated
        $default_options['booking_visitor_number_rate_type'] = '%';             //Depricated    
    }
    
    
	// -----------------------------------------------------------------------------------------------------------------
    // BL
	// -----------------------------------------------------------------------------------------------------------------
    if ( class_exists( 'wpdev_bk_biz_l' ) ) {
    
// Get Key of Last added item to "$default_options" array
// $multiuser_general_option[] = implode( '', array_keys( array_slice( $default_options, -1 ) ) ); 
        
        $default_options['booking_check_out_available_for_parents'] = 'On';     
     $mu_option4delete[]='booking_check_out_available_for_parents';                 
        $default_options['booking_check_in_available_for_parents'] = 'Off';     
     $mu_option4delete[]='booking_check_in_available_for_parents';                 
        $default_options['booking_is_show_pending_days_as_available'] = 'Off';
     $mu_option4delete[]='booking_is_show_pending_days_as_available';                 
        $default_options['booking_auto_cancel_pending_bookings_for_approved_date'] = 'Off';
     $mu_option4delete[]='booking_auto_cancel_pending_bookings_for_approved_date';

        $default_options['booking_quantity_control'] = ( get_bk_option( 'booking_is_use_visitors_number_for_availability') == 'On' ) ? 'On' : 'Off';
     $mu_option4delete[]='booking_quantity_control';
        $default_options['booking_capacity_field'] = ( get_bk_option( 'booking_is_use_visitors_number_for_availability') == 'On' ) ? 'select^visitors' : '';
     $mu_option4delete[]='booking_capacity_field';

        $default_options['booking_is_show_availability_in_tooltips'] = 'On';                                            // FixIn: 8.1.3.8.
     $mu_option4delete[]='booking_is_show_availability_in_tooltips';                 
        $default_options['booking_highlight_availability_word'] = __( 'Available: ', 'booking' );
     $mu_option4delete[]='booking_highlight_availability_word';

        $default_options['booking_is_show_availability_in_date_cell'] = 'Off';                                          // FixIn: 10.6.4.1.
     $mu_option4delete[]='booking_is_show_availability_in_date_cell';
        $default_options['booking_highlight_availability_word_in_date_cell'] = __( 'available', 'booking' );
     $mu_option4delete[]='booking_highlight_availability_word_in_date_cell';


        $default_options['booking_is_dissbale_booking_for_different_sub_resources'] = 'Off';
     $mu_option4delete[]='booking_is_dissbale_booking_for_different_sub_resources';                 
// FixIn: 10.10.1.2          $default_options['booking_is_resource_no_update__during_editing'] = 'On';										// FixIn: 9.4.2.3.
// FixIn: 10.10.1.2       $mu_option4delete[]='booking_is_resource_no_update__during_editing';
        $default_options['booking_search_form_show'] = str_replace( '\\n\\r', "\n", wpbc_get_default_search_form_template( 'standard_search_form' ) );     	//FixIn:6.1.0.1		// FixIn: 8.5.2.11.
     $mu_option4delete[]='booking_search_form_show';                 
        $default_options['booking_found_search_item'] = str_replace( '\\n\\r', "\n", wpbc_get_default_search_results_template( 'advanced_search_results' ) );  //FixIn:6.1.0.1		// FixIn: 8.5.2.11.
     $mu_option4delete[]='booking_found_search_item';                 
        $default_options['booking_cache_expiration'] = '2d';
     $mu_option4delete[]='booking_cache_expiration';
        $default_options['booking_search_results_order'] = 'prioritet';													 // FixIn: 8.4.7.8.
     $mu_option4delete[]='booking_search_results_order';
        $default_options['booking_search_form_dates_format'] = 'yy-mm-dd';												// FixIn: 8.6.1.21.
     $mu_option4delete[]='booking_search_form_dates_format';
        $default_options['booking_search_results_days_select'] = 'Off';													// FixIn: 8.8.2.3.
     $mu_option4delete[]='booking_search_results_days_select';
        $default_options['booking_from_search_scroll_to_calendar'] = 'On';												// FixIn: 9.4.4.6.
     $mu_option4delete[]='booking_from_search_scroll_to_calendar';

        $default_options['booking_search_from_auto_open_checkout_cal'] = 'On';											// FixIn: 10.0.0.39.
     $mu_option4delete[]='booking_search_from_auto_open_checkout_cal';
        $default_options['booking_search_from__search_header'] = '[result_count] ' . __('Result(s) Found','booking');	// FixIn: 10.0.0.41.
     $mu_option4delete[]='booking_search_from__search_header';
        $default_options['booking_search_from__search_header_advanced'] = __( 'Extended Search Results', 'booking' );	// FixIn: 10.0.0.41.
     $mu_option4delete[]='booking_search_from__search_header_advanced';
        $default_options['booking_search_from__search_nothing_found'] = __('Nothing Found','booking');
     $mu_option4delete[]='booking_search_from__search_nothing_found';

        $default_options['booking_search_form__dates_required_enabled'] = 'Off';
     $mu_option4delete[]='booking_search_form__dates_required_enabled';
        $default_options['booking_search_form__dates_required_warning'] = __('Please select check-in and check-out dates to perform the search.','booking');
     $mu_option4delete[]='booking_search_form__dates_required_warning';


        //    Updated during regeneration  seacrh  cache
        //$default_options['booking_cache_content']='';
       $mu_option4delete[]='booking_cache_content';  
        //$default_options['booking_cache_created']=wpbc_datetime_localized( date ('Y-m-d H:i:s'), 'Y-m-d H:i:s' );
       $mu_option4delete[]='booking_cache_created';  
       $mu_option4delete[]='booking_resources_search_options';                                                          // FixIn: 10.0.0.18.
    }


	// -----------------------------------------------------------------------------------------------------------------
    // MU
	// -----------------------------------------------------------------------------------------------------------------
	if ( class_exists( 'wpdev_bk_multiuser' ) ) {

		// FixIn: 8.1.3.19.
			$default_options['booking_is_custom_forms_for_regular_users'] = 'Off';
		$mu_option4delete[]='booking_is_custom_forms_for_regular_users';

		// FixIn: 9.2.3.8.
			$default_options['booking_super_admin_receive_regular_user_payments'] = 'Off';
		$mu_option4delete[]='booking_super_admin_receive_regular_user_payments';
	}
    
    
    if ( ! $is_get_multiuser_general_options ) { 
        
        if ( ! empty( $option_name ) ) {
            
            if (isset( $default_options[ $option_name ] ))
                return $default_options[ $option_name ];                        // Return 1 option    
            else
                return  false;                                                  // Option does NOT exist
            
        } else {
            return $default_options;                                            // Return  ALL
        }
        
    } else
        return $mu_option4delete;                                               // Get options for MU
}


// <editor-fold     defaultstate="collapsed"                        desc="  Update Deprecated Options "  >
function wpbc_after_activation__update_deprecated_options() {
	update_bk_option( 'booking_form_is_using_bs_css', 'Off' );
}
add_bk_action( 'wpbc_after_activation', 'wpbc_after_activation__update_deprecated_options' );
// </editor-fold>
