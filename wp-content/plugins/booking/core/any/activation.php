<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @category Installation 
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-04-09, 2016-03-17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** 
 * Activation | Deactivation Class  
 */
abstract class WPBC_Install {

	/**
	 * Init option
	 *
	 * @var array
	 */
	private $init_option;

	/**
	 * Constructor
	 */
	public function __construct() {

		$default_init_option_names = array(
			'option-version_num'                 => 'booking_version_num',
			'option-is_delete_if_deactive'       => 'booking_is_delete_if_deactive',
			'option-activation_process'          => 'booking_activation_process',
			'transient-wpbc_activation_redirect' => '_booking_activation_redirect',
			'message-delete_data'                => '<strong>Warning!</strong> All plugin data will be deleted when plugin had deactivated.<br />' . sprintf( 'If you want to save your plugin data, please uncheck the %s"Delete plugin data"%s at the', '<strong>', '</strong>' ),
			'link_settings'                      => '<a href="">Settings</a>',
			'link_whats_new'                     => '<a href="">Whats New</a>',
		);

		$init_option = $this->get_init_option_names();

		$this->init_option = wp_parse_args( $init_option, $default_init_option_names );

		// WordPress > Plugins > "Activate" link.
		register_activation_hook( WPBC_FILE, array( $this, 'wpbc_activate_initial' ) );

		// WordPress > Plugins > "Deactivate" link.
		register_deactivation_hook( WPBC_FILE, array( $this, 'wpbc_deactivate' ) );

		// Upgrade during bulk upgrade of plugins.
		add_filter( 'upgrader_post_install', array( $this, 'wpbc_install_in_bulk_upgrade' ), 10, 2 );

		// Settings link at the plugin page.
		add_filter( 'plugin_action_links', array( $this, 'plugin_links' ), 10, 2 );
		// Warning message in plugin info.
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

		// TODO: 2025-06-06 - required to  be commented,  if we use the Pro version  as separate plugin.
		$this->check_if_need_to_update();                                                                  // Check upgrade, if was no activation process
	}

    
    /**
	 * Must be overloaded in child CLASS
     * 
     * * Important! for correct loading of trasnaltions later, we must  do not use here loacale of plugin. So here will be untranslated strings!!!
     * 
     *  Exmaple:
     *         return  array(
                  'option-version_num'                   => 'booking_version_num'
                , 'option-is_delete_if_deactive'         => 'booking_is_delete_if_deactive'
                , 'option-activation_process'            => 'booking_activation_process'
                , 'transient-wpbc_activation_redirect'   => '_booking_activation_redirect'
                , 'message-delete_data'                  =>  '<strong>Warning !!!</strong> ' . 'All plugin data will be deleted when plugin had deactivated.' . '<br />'
                                                            . sprintf( 'If you want to save your plugin data, please uncheck the %s"Delete plugin data"%s at the settings page.', '<strong>', '</strong>')
                , 'link_settings'                        => '<a href="">Settings</a>'
                , 'link_whats_new'                       => '<a href="">Whats New</a>'            
        );
     */
    abstract function get_init_option_names();

    
    /**
	 * Must be overloaded in child CLASS
     *  Exmaple:
     * 
        return false
    */
    abstract function is_update_from_lower_to_high_version();

    
	// -----------------------------------------------------------------------------------------------------------------
    // <editor-fold defaultstate="collapsed" desc="    Update info of plugin at the plugins section   ">
    
    /** Update info of plugin at the plugins section */
    function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $context ) {

        $this_plugin = plugin_basename( WPBC_FILE );

        if ($plugin_file == $this_plugin ) {

            $is_delete_if_deactive =  get_bk_option( $this->init_option['option-is_delete_if_deactive'] ); // check

            if ($is_delete_if_deactive == 'On') { ?>
                <div class="plugin-update-tr">
                    <div class="update-message notice inline notice-warning notice-altNO" style="font-size: 1em;line-height: 2em;margin:0 5px 10px;border-left: 3px solid #dd7000;"><?php
	                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	                    echo $this->init_option['message-delete_data']; ?></div>
				</div>
                <?php
            }

			$version = wpbc_get_plugin_version_title();

			if ( 'Free' !== $version ) {
				$plugin_meta[0] .= ' | ' . __( 'Version type', 'booking' ) . ': <strong>Free + ' . ucfirst( $version ) . '</strong>';
			} else {
				$plugin_meta[0] .= ' | ' . __( 'Version type', 'booking' ) . ': <strong>' . ucfirst( $version ) . '</strong>';
			}

            /*
            [$plugin_meta] => Array
                (
                    [0] => Version 2.8.35
                    [1] => By wpdevelop
                    [2] => Visit plugin site
                )

            [$plugin_file] => booking/WPBC.php
            [$plugin_data] => Array
                (
                    [Name] => Booking Calendar
                    [PluginURI] => https://wpbookingcalendar.com/demo/
                    [Version] => 2.8.35
                    [Description] => Online booking and availability checking service for your site.
                    [Author] => wpdevelop
                    [AuthorURI] => https://wpbookingcalendar.com/
                    [TextDomain] =>
                    [DomainPath] =>
                    [Network] =>
                    [Title] => Booking Calendar
                    [AuthorName] => wpdevelop
                )

            [$context] => all
            /**/

            // Echo plugin description here
            return $plugin_meta;
            
        } else     
            return $plugin_meta;
    }


    // Adds Settings link to plugins settings
    function plugin_links($links, $file) {

        $this_plugin = plugin_basename( WPBC_FILE );

        if ( $file == $this_plugin ) {


            array_unshift( $links, $this->init_option['link_settings'] );
            
            //array_unshift( $links, $this->init_option['link_whats_new'] );
            array_unshift( $links, $this->init_option['link_faq'] );


			$links = array_merge( $links, array(
					'<a class="wpbc_plugins_links__start_tour" title="' . esc_attr( sprintf( __('We\'ll guide you through the steps to set up WP Booking Calendar on your site.','booking'), '<strong>WP Booking Calendar</strong>' ) ) . '" href="'
					 . esc_url( wpbc_get_settings_url() . '&wpbc_setup_wizard=reset&_wpnonce=' . wp_create_nonce( 'wpbc_settings_url_nonce' ) )
					//. esc_url( admin_url( add_query_arg( array( 'page' => 'wpbc-about' ), 'index.php' ) ) )
					. '">' . esc_attr__( 'Start Setup Wizard', 'booking' ) . '</a>'
				)
			);


			if ( ! class_exists( 'wpdev_bk_personal' ) ) {
				if ( wpbc_is_updated_paid_to_free() ) {
					array_unshift(
						$links,
						'<a style="font-weight:600;" class="wpbc_plugins_links__start_tour" title="' . esc_attr( __( 'Request Update', 'booking' ) ) . '" href="' . esc_url( 'https://wpbookingcalendar.com/request-update/' ) . '">' . esc_attr__( 'Request Pro Version', 'booking' ) . '</a>'
					);
				} else {
					array_unshift( $links, $this->init_option['link_up'] );
				}
			} else {
				if ( ! class_exists( 'wpdev_bk_multiuser' ) ) {
					array_unshift( $links, $this->init_option['link_upgrade'] );
				}
			}

        }
        return $links;
    }
    
    // </editor-fold>


	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Check  about ability to upgrade, if was no activation  process
	 *
	 * @return void
	 */
	private function check_if_need_to_update() {

		if ( is_admin() ) {

			$wpbc_version_num = get_option( $this->init_option['option-version_num'] );

			if ( false === $wpbc_version_num ) {
				$wpbc_version_num = '0';
			}

			$is_make_activation = false;

			if ( version_compare( WP_BK_VERSION_NUM, $wpbc_version_num ) > 0 ) {

				$is_make_activation = true;

			} else {

				// Check if we was update from free to paid or from lower to higher versions, and do not make normal activation. In this case we need to make update.
				// TODO: 2025-11-23 - Commented,  if we use the Pro version  as separate plugin.
				// $is_make_activation = $this->is_update_from_lower_to_high_version();

			}

			// Add hook  for initial activation.
			if ( $is_make_activation ) {
				// add_action( 'plugins_loaded', array( $this, 'wpbc_activate_initial' ), 1030 );
				add_action( 'init', array( $this, 'wpbc_activate_initial' ), 1030 );
			}
		}
	}


	/**
	 * Upgrade during bulk upgrade of plugins
	 *
	 * @param type $return_val
	 * @param type $hook_extra
	 *
	 * @return type
	 */
	public function wpbc_install_in_bulk_upgrade( $return_val, $hook_extra ) {

		if ( is_wp_error( $return_val ) ) {
			return $return_val;
		}

		if ( isset( $hook_extra ) ) {
			if ( isset( $hook_extra['plugin'] ) ) {
				$file_name = basename( WPBC_FILE );
				$pos       = strpos( $hook_extra['plugin'], trim( $file_name ) );
				if ( false !== $pos ) {
					$this->wpbc_activate();
				}
			}
		}

		return $return_val;
	}


	/**
	 * User clicked on "Activate" link at Plugins Menu.
	 *
	 * @return type
	 */
	public function wpbc_activate_initial() {

		// Activate the plugin.
		$this->wpbc_activate();

		// Bail if this demo or activating from network, or bulk.
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || wpbc_is_this_demo() ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			return;
		}

		// Add the transient to redirect - Showing Welcome screen.
		set_transient( $this->init_option['transient-wpbc_activation_redirect'], true, 30 );
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Run Activate
	 */
	public function wpbc_activate() {

		WPBC_Action_Scheduler_Compatibility::raise_memory_limit();
		WPBC_Action_Scheduler_Compatibility::raise_time_limit( 300 );

		update_bk_option( $this->init_option['option-activation_process'], 'On' );

		make_bk_action( 'wpbc_activation' );                                    // S T A R T.

		update_bk_option( $this->init_option['option-version_num'], WP_BK_VERSION_NUM );

		update_bk_option( $this->init_option['option-activation_process'], 'Off' );
	}


	/**
	 * Run Deactivate
	 */
	public function wpbc_deactivate() {

		WPBC_Action_Scheduler_Compatibility::raise_memory_limit();
		WPBC_Action_Scheduler_Compatibility::raise_time_limit( 300 );

		$is_delete_if_deactive = get_bk_option( $this->init_option['option-is_delete_if_deactive'] ); // check.

		if ( 'On' === $is_delete_if_deactive ) {

			make_bk_action( 'wpbc_deactivation' );                              // F I N I S H.

			delete_bk_option( $this->init_option['option-version_num'] );

			delete_bk_option( $this->init_option['option-activation_process'] );
		}
	}

}