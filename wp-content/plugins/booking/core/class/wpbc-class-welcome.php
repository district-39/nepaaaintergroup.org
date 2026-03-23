<?php
/**
 * Welcome Page Class
 * Shows a feature overview for the new version (major).
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 * @version     2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WPBC_PLUGIN_DIR . '/core/class/welcome_current.php';

class WPBC_Welcome {

	/**
	 * Minimum capability, such  as 'read' or 'manage_options'
	 * @var string
	 */
	public $minimum_capability = 'read';

	/**
	 * Path to images.
	 * @var string
	 */
	public $asset_path = ( true ) ? 'https://wpbookingcalendar.com/assets/' : 'http://beta/assets/';

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menus' ) );

		add_action( 'admin_init', array( $this, 'welcome' ) );

		add_action( 'load-dashboard_page_wpbc-about', array( $this, 'wpbc_define_page_title_about' ) );
	}

	public function wpbc_define_page_title_about() {    // FixIn: 9.6.2.12.
		global $title;
		if ( ! isset( $title ) ) {
			$title = 'Welcome to Booking Calendar';
		}
	}

	public function show_separator() {
		echo '<div class="clear" style="height:1px;border-bottom:1px solid #DFDFDF;"></div>';
	}

	public function show_header( $text = '', $header_type = 'h3', $style = '' ) {
		echo '<', esc_attr( $header_type );
		if ( ! empty( $style ) ) {
			echo " style='" . esc_attr( $style ) . "'";
		}
		echo '>';
		echo wp_kses_post( wpbc_replace_to_strong_symbols( $text ) );
		echo '</', esc_attr( $header_type ), '>';
	}

	public function show_col_section( $sections_array = array() ) {

		$columns_num = count( $sections_array );

		if ( isset( $sections_array['h3'] ) ) {
			$columns_num --;
		}
		if ( isset( $sections_array['h2'] ) ) {
			$columns_num --;
		}
		?>
		<div class="changelog"><?php

			if ( isset( $sections_array['h3'] ) ) {
				echo "<h3>" . wp_kses_post( wpbc_replace_to_strong_symbols( $sections_array['h3'] ) ) . "</h3>";
				unset( $sections_array['h3'] );
			}
			if ( isset( $sections_array['h2'] ) ) {
				echo "<h2>" . wp_kses_post( wpbc_replace_to_strong_symbols( $sections_array['h2'] ) ) . "</h2>";
				unset( $sections_array['h2'] );
			}

			?>
			<div class="feature-section <?php
			if ( $columns_num == 2 ) {
				echo ' two-col';
			}
			if ( $columns_num == 3 ) {
				echo ' three-col';
			} ?>">
				<?php
				foreach ( $sections_array as $section_key => $section ) {
					$col_num = ( $section_key + 1 );
					if ( $columns_num == $col_num ) {
						$is_last_feature = ' last-feature ';
					} else {
						$is_last_feature = '';
					}

					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo "<div class='col col-{$col_num}{$is_last_feature}'>";

					if ( isset( $section['header'] ) ) {
						echo "<h4>" . wp_kses_post( wpbc_replace_to_strong_symbols( $section['header'] ) ) . "</h4>";
					}
					if ( isset( $section['h4'] ) ) {
						echo "<h4>" . wp_kses_post( wpbc_replace_to_strong_symbols( $section['h4'] ) ) . "</h4>";
					}
					if ( isset( $section['h3'] ) ) {
						echo "<h3>" . wp_kses_post( wpbc_replace_to_strong_symbols( $section['h3'] ) ) . "</h3>";
					}
					if ( isset( $section['h2'] ) ) {
						echo "<h2>" . wp_kses_post( wpbc_replace_to_strong_symbols( $section['h2'] ) ) . "</h2>";
					}
					if ( isset( $section['text'] ) ) {
						echo wp_kses_post( wpbc_replace_to_strong_symbols( $section['text'] ) );
					}
					if ( isset( $section['img'] ) ) {

						$is_full_link = strpos( $section['img'], 'http' );
						if ( false === $is_full_link ) {
							// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
							echo '<img src="' . esc_url( $this->asset_path . $section['img'] ) . '" ';
						} else {
							// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
							echo '<img src="' . esc_url( $section['img'] ) . '" ';
						}
						if ( isset( $section['img_style'] ) ) {
							echo ' style="' . esc_attr( $section['img_style'] ) . '" ';
						}
						echo ' class="wpbc-section-image" />';
					}

					echo "</div>";
				}
				?>
			</div>
		</div>
		<?php
	}

	public function get_img( $img, $img_style = '' ) {

		$is_full_link = strpos( $img, 'http' );
		if ( false === $is_full_link ) {
			// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
			$img_result = '<img src="' . esc_url( $this->asset_path . $img ) . '" ';
		} else {
			// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
			$img_result = '<img src="' . esc_url( $img ) . '" ';
		}

		if ( ! empty( $img_style ) ) {
			$img_result .= ' style="' . esc_attr( $img_style ) . '" ';
		}
		$img_result .= ' class="wpbc-section-image" />';

		return $img_result;
	}

	// -----------------------------------------------------------------------------------------------------------------
	// Menu
	public function admin_menus() {
		// What's New.
		add_dashboard_page( sprintf( 'Welcome to Booking Calendar' ), sprintf( 'What\'s New' ), $this->minimum_capability, 'wpbc-about', array( $this, 'content_whats_new' ) );
		remove_submenu_page( 'index.php', 'wpbc-about' );
	}

	// Head.
	public function admin_head() {
		remove_submenu_page( 'index.php', 'wpbc-about' );
	}

	// Title
	public function title_section() {

		list( $display_version ) = explode( '-', WPDEV_BK_VERSION );
		//$display_version = WP_BK_VERSION_NUM;
		?>
		<h1><?php echo wp_kses_post( sprintf( 'Welcome to Booking Calendar %s', $display_version ) ); ?></h1>
		<div class="about-text"><?php
			echo ( 'Booking Calendar is ready to receive and manage bookings from your visitors!' );
			?></div>


        <h2 class="nav-tab-wrapper">
        <?php
        $is_about_tab_active = $is_about_premium_tab_active = $is_getting_started_tab_active = '';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        if ( ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc-about' ) )
            $is_about_tab_active = ' nav-tab-active ';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        if ( ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc-about-premium' ) )
            $is_about_premium_tab_active = ' nav-tab-active ';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        if ( ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wpbc-getting-started' ) )
            $is_getting_started_tab_active = ' nav-tab-active ';
        ?>
            <a class="nav-tab<?php echo esc_attr( $is_about_tab_active ); ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
            'page' => 'wpbc-about' ), 'index.php' ) ) ); ?>">
                    <?php echo( "What's New" ); ?>
                <a class="nav-tab<?php echo esc_attr( $is_getting_started_tab_active ); ?>" href="https://wpbookingcalendar.com/faq/#using">
        <?php echo( "Get Started" ); ?>
                </a><a class="nav-tab<?php echo esc_attr( $is_about_premium_tab_active ); ?>" href="https://wpbookingcalendar.com/features/">
        <?php echo( "Get even more functionality" ); // echo( "Even more Premium Features" ); ?>
                </a>
        </h2>                
        <?php
    }

	/**
	 * Maintenance section
	 */
	public function maintence_section() {

		if ( ! ( ( defined( 'WP_BK_MINOR_UPDATE' ) ) && ( WP_BK_MINOR_UPDATE ) ) ) {
			return;
		}

		list( $display_version ) = explode( '-', WPDEV_BK_VERSION );
		?>
		<div class="changelog point-releases" style="margin: 40px 0 50px;">
			<h3><?php
				echo( "Maintenance Release" ); ?></h3>
			<p><strong><?php
					echo wp_kses_post( sprintf( 'Version %s', $display_version ) ); ?></strong> <?php
				echo 'addressed some minor issues and improvement in functionality'; ?>.
				<?php
				echo wp_kses_post( sprintf( 'For more information, see %sthe release notes%s', '<a href="https://wpbookingcalendar.com/changelog/" target="_blank">', '</a>' ) ); ?>
				.
			</p>
		</div>
		<?php
	}

    // Start
    public function welcome() {

        $booking_activation_process = get_bk_option( 'booking_activation_process' );
        if ( $booking_activation_process == 'On' )
            return;

        // Bail if no activation redirect transient is set
		if ( ! get_transient( '_booking_activation_redirect' ) ) { // $.
			return;
		}

        // Delete the redirect transient
        delete_transient( '_booking_activation_redirect' );

        // Bail if DEMO or activating from network, or bulk, or within an iFrame.

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        if ( wpbc_is_this_demo() || is_network_admin() || isset( $_GET[ 'activate-multi' ] ) || defined( 'IFRAME_REQUEST' ) )
            return;

        // Set mark,  that  we already redirected to About screen               //FixIn: 5.4.5
        $redirect_for_version = get_bk_option( 'booking_activation_redirect_for_version' );
        if ( $redirect_for_version == WP_BK_VERSION_NUM )
            return;
        else
            update_bk_option( 'booking_activation_redirect_for_version', WP_BK_VERSION_NUM );
        
        wp_safe_redirect( admin_url( 'index.php?page=wpbc-about' ) );
        exit;
    }


    // CONTENT /////////////////////////////////////////////////////////////////

	public function show_go_links() {
		?>
		<div style="display: flex;flex-flow: row wrap;justify-content: center;gap: 2em;margin: 3em 0 1em;">
			<a class="button button-primary"
			   href="<?php
			   echo esc_url( wpbc_get_bookings_url() . '&tab=vm_booking_listing' ); ?>"
			   style="font-size: 20px;padding: 0.15em 1.5em;"><?php
				esc_html_e( 'Go to Booking Admin Panel', 'booking' ); ?></a>
			<?php
			$wp_post_booking_absolute = wpbc_stp_wiz__is_exist_published_page_with_booking_form();
			if ( ! empty( $wp_post_booking_absolute ) ) {
				?>
				<a class="button button-secondary"
				   style="font-size: 20px;padding: 0.15em 1em;"
				   href="<?php
				   echo esc_url( $wp_post_booking_absolute ); ?>"
				><?php
					esc_html_e( 'Go to page with booking form', 'booking' ); ?></a>
			<?php
			} ?>
		</div>
		<?php
	}

	public function content_whats_new() {

		echo '<div class="wrap about-wrap wpbc-welcome-page">';

		$this->title_section();

		$this->show_go_links();

		$this->maintence_section();

		$this->section_9_8_css();

		wpbc_welcome_section_10_14( $this );
		wpbc_welcome_section_10_13( $this );
//		wpbc_welcome_section_10_12( $this );
//		wpbc_welcome_section_10_11( $this );

		/*
					wpbc_welcome_section_10_10( $this );

					wpbc_welcome_section_10_9( $this );

					wpbc_welcome_section_10_8( $this );

					wpbc_welcome_section_10_7( $this );

					wpbc_welcome_section_10_6( $this );

					wpbc_welcome_section_10_5( $this );

					wpbc_welcome_section_10_4( $this );

					wpbc_welcome_section_10_3( $this );

					wpbc_welcome_section_10_2( $this );

					wpbc_welcome_section_10_1( $this );

					wpbc_welcome_section_10_0( $this );

					wpbc_welcome_section_9_9( $this );

					wpbc_welcome_section_9_8( $this );
		*/

		$this->show_go_links();

		echo '<div';
	}


	function expand_section_start( $section_param_arr ) {

		?>
		<div class="clear" style="margin-top:20px;"></div><?php

		if ( $section_param_arr['show_expand'] ) {

			?><a    id="wpbc_show_advanced_section_link_show"
					class="wpbc_expand_section_link"
					href="javascript:void(0)"
					onclick="javascript:jQuery( '.version_update_<?php
					echo esc_attr( str_replace( array(
						'.', ' ',
					), '_', $section_param_arr['version_num'] ) ); ?>' ).toggle();"
			>+ Show changes in version update <span
				style="font-size: 1.35em;font-weight: 600;color: #079;font-family: Consolas,Monaco,monospace;padding-left:12px;"><?php
				echo esc_html( $section_param_arr['version_num'] ); ?></span>
			</a>
			<div class="version_update_<?php
			echo esc_attr( str_replace( array(
				'.', ' ',
			), '_', $section_param_arr['version_num'] ) ); ?>" style="display:none;">
			<?php

		}

		?><h2 style='font-size: 1.9em;text-align:left;'>What's New in Booking Calendar <span
			style="font-size: 1.1em;font-weight: 600;font-family: Consolas,Monaco,monospace;padding-left: 10px;color: #5F5F5F;"
		><?php
			echo esc_html( $section_param_arr['version_num'] ); ?></span></h2><?php

	}


	function expand_section_end( $section_param_arr ) {
		if ( $section_param_arr['show_expand'] ) {
			?></div><?php
		}
	}


		function section_img_url( $relative_path_to_img ) {
			return esc_url( $this->asset_path . $relative_path_to_img );
		}


		function section_9_8_css(){

			?><style type="text/css">
				.about-wrap.wpbc-welcome-page {
					position: relative;
					margin: 25px 40px 0 20px;
					max-width: 1050px;
					font-size: 15px;
					clear: both;
				}
				.wpbc_wn_container{
					margin: 24px auto;
					overflow: hidden;
				}
				.wpbc_wn_section{
					display: flex;
					flex-flow: row wrap;
					justify-content: space-between;
					align-content: flex-start;
					align-items: flex-start;
					font-size: 1.05em;
					line-height: 2rem;
					margin: 1em 0;
				}
				.wpbc_wn_col{
					flex: 1 1 50%;
					padding: 1.5em 2em;
					box-sizing: border-box;
				}
				@media screen and (max-width: 782px) {
					.wpbc_wn_col{
					 flex: 1 1 100%;
					}
				}
				.wpbc_wn_separator{
					flex: 1 1 100%;
				}
				.wpbc_wn_col * {
					 margin: 0;
					line-height: 2.2em;
				}
				.wpbc_wn_section > h2,
				.wpbc_wn_section > h3{
					margin: 0 0  0.25em;
					font-size: 2em;
					line-height: 2.16em;
					font-weight: 600;
					flex: 1 1 100%;
					text-align: center;
					padding:0 1.5em;
				}
				.wpbc_wn_section h3 {
					font-size: 1.25em;
					text-align: left;
					margin: 0.25em 0 0.5em;
				}
				.wpbc_wn_section img{
					border-radius: 2px;
					box-shadow: none;
					width: 99%;
					margin: 0.5em 0 auto;
					padding: 3px;
					background: #fff;
					border: 1px solid #ccc;
				}
				.wpbc_wn_container .wpbc_hr_dots {
					height: 1px;
					border:none;
					border-bottom: 0.5rem dotted #a9a9a9;
					width: 3rem;
					margin:0  auto;
					clear: both:;
					display: block;
					position: relative;
				}
			</style>
			<?php
		}
}

$wpbc_welcome = new WPBC_Welcome();