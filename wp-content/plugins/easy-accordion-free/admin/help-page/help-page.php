<?php
/**
 * The help page for the Easy Accordion Free
 *
 * @package Easy Accordion Free
 * @subpackage easy-accordion-free/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The help class for the Easy Accordion Free
 */
class Easy_Accordion_Free_Help {

	/**
	 * Single instance of the class
	 *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Plugins Path variable.
	 *
	 * @var array
	 */
	protected static $plugins = array(
		'woo-product-slider'             => 'main.php',
		'gallery-slider-for-woocommerce' => 'woo-gallery-slider.php',
		'post-carousel'                  => 'main.php',
		'easy-accordion-free'            => 'plugin-main.php',
		'logo-carousel-free'             => 'main.php',
		'location-weather'               => 'main.php',
		'woo-quickview'                  => 'woo-quick-view.php',
		'wp-expand-tabs-free'            => 'plugin-main.php',

	);

	/**
	 * Welcome pages
	 *
	 * @var array
	 */
	public $pages = array(
		'eap_help',
	);


	/**
	 * Not show this plugin list.
	 *
	 * @var array
	 */
	protected static $not_show_plugin_list = array( 'aitasi-coming-soon', 'latest-posts', 'widget-post-slider', 'easy-lightbox-wp', 'easy-accordion-free' );

	/**
	 * Easy_Accordion_Free_Help construct function.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'analytics_admin_menu' ), 55 );
		add_action( 'admin_menu', array( $this, 'faq_form_admin_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'help_admin_menu' ), 80 );

        $page   = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';// @codingStandardsIgnoreLine
		if ( 'eap_help' !== $page ) {
			return;
		}
		add_action( 'admin_print_scripts', array( $this, 'disable_admin_notices' ) );
		add_action( 'eapro_enqueue', array( $this, 'help_page_enqueue_scripts' ) );
	}

	/**
	 * Main Easy_Accordion_Free_Help Instance
	 *
	 * @static
	 * @see Easy_Accordion_Free_Help()
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Help_page_enqueue_scripts function.
	 *
	 * @return void
	 */
	public function help_page_enqueue_scripts() {
		wp_enqueue_style( 'sp-easy-accordion-help', SP_EA_URL . 'admin/help-page/css/help-page.min.css', array(), SP_EA_VERSION );
		wp_enqueue_style( 'sp-easy-accordion-fontello', SP_EA_URL . 'admin/help-page/css/fontello.min.css', array(), SP_EA_VERSION );

		wp_enqueue_script( 'sp-easy-accordion-help', SP_EA_URL . 'admin/help-page/js/help-page.min.js', array(), SP_EA_VERSION, true );
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function help_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_easy_accordion',
			__( 'Easy Accordion', 'easy-accordion-free' ),
			__( 'Recommended', 'easy-accordion-free' ),
			'manage_options',
			'edit.php?post_type=sp_easy_accordion&page=eap_help#recommended'
		);
		add_submenu_page(
			'edit.php?post_type=sp_easy_accordion',
			__( 'Easy Accordion', 'easy-accordion-free' ),
			__( 'Lite vs Pro', 'easy-accordion-free' ),
			'manage_options',
			'edit.php?post_type=sp_easy_accordion&page=eap_help#lite-to-pro'
		);
		add_submenu_page(
			'edit.php?post_type=sp_easy_accordion',
			__( 'Easy Accordion Help', 'easy-accordion-free' ),
			__( 'Get Help', 'easy-accordion-free' ),
			'manage_options',
			'eap_help',
			array(
				$this,
				'help_page_callback',
			)
		);
	}

	/**
	 * Add admin menu analytics sub menu.
	 *
	 * @return void
	 */
	public function analytics_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_easy_accordion',
			__( 'Easy Accordion Analytics', 'easy-accordion-free' ),
			__( 'Analytics', 'easy-accordion-free' ) . '<span class="eap-menu-new-indicator" style="color: #f18200;font-size: 9px; padding-left: 3px;">' . __( ' NEW!', 'easy-accordion-free' ) . '</span>',
			'manage_options',
			'eap_analytics',
			array(
				$this,
				'analytics_page_callback',
			)
		);
	}

	/**
	 * The Easy Accordion analytics Callback.
	 *
	 * @return void
	 */
	public function analytics_page_callback() {
		?>
		<div class="sp-eap-indicator-notice">Want to know <a href="https://easyaccordion.io/faq-analytics" target="_blank">valuable insights or analytics</a> into FAQs performance? To track impressions, clicks, and more to optimize engagement, <a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>Upgrade to Pro!</b></a></div>
		<div class="sp-eap-indicator">
		<?php echo esc_html( $this->sp_eap_upgrade_to_pro_btn() ); ?>
		<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/analytics.webp' ); ?>" alt="faqs-indicator">
		</div>
		<?php
	}

	/**
	 * Display the upgrade to pro button.
	 *
	 * @return void
	 */
	public function sp_eap_upgrade_to_pro_btn() {
		?>
		<div class="sp-eap-upgrade-to-pro-button">
			<a href="https://easyaccordion.io/pricing/?ref=1" target="_blank">
				Upgrade to Pro!
			</a>
		</div>
		<?php
	}

	/**
	 * Add admin menu analytics sub menu.
	 *
	 * @return void
	 */
	public function faq_form_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_easy_accordion',
			__( 'Easy Accordion Form', 'easy-accordion-free' ),
			__( 'FAQ Forms', 'easy-accordion-free' ) . '<span class="eap-menu-new-indicator" style="color: #f18200;font-size: 9px; padding-left: 3px;">' . __( ' HOT!', 'easy-accordion-free' ) . '</span>',
			'manage_options',
			'eap_form',
			array(
				$this,
				'form_page_callback',
			)
		);
	}

	/**
	 * The Easy Accordion analytics Callback.
	 *
	 * @return void
	 */
	public function form_page_callback() {
		?>
		<div class="sp-eap-indicator-notice">To allow users to submit FAQ suggestions using the <a href="https://easyaccordion.io/faq-forms/" target="_blank"><b>FAQs Form</b></a>, <a href="https://easyaccordion.io/pricing/?ref=1" target="_blank"><b>Upgrade to Pro!</b></a></div>
		<div class="eap-faq-form">
		<?php echo esc_html( $this->sp_eap_upgrade_to_pro_btn() ); ?>
		<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/form.webp' ); ?>" alt="forms" class="eap-form-img"/>
		</div>
		<?php
	}

	/**
	 * Spea_ajax_help_page function.
	 *
	 * @return void
	 */
	public function spea_plugins_info_api_help_page() {
		$plugins_arr = get_transient( 'spea_plugins_data' );
		if ( ! $plugins_arr ) {
			$args    = (object) array(
				'author'   => 'shapedplugin',
				'per_page' => '120',
				'page'     => '1',
				'fields'   => array(
					'slug',
					'name',
					'version',
					'downloaded',
					'active_installs',
					'last_updated',
					'rating',
					'num_ratings',
					'short_description',
					'author',
					'icons',
				),
			);
			$request = array(
				'action'  => 'query_plugins',
				'timeout' => 30,
				'request' => serialize( $args ),
			);
			// https://codex.wordpress.org/WordPress.org_API.
			$url      = 'http://api.wordpress.org/plugins/info/1.0/';
			$response = wp_remote_post( $url, array( 'body' => $request ) );

			if ( ! is_wp_error( $response ) ) {

				$plugins_arr = array();
				$plugins     = unserialize( $response['body'] );

				if ( isset( $plugins->plugins ) && ( count( $plugins->plugins ) > 0 ) ) {
					foreach ( $plugins->plugins as $pl ) {
						if ( ! in_array( $pl->slug, self::$not_show_plugin_list, true ) ) {
							$plugins_arr[] = array(
								'slug'              => $pl->slug,
								'name'              => $pl->name,
								'version'           => $pl->version,
								'downloaded'        => $pl->downloaded,
								'active_installs'   => $pl->active_installs,
								'last_updated'      => strtotime( $pl->last_updated ),
								'rating'            => $pl->rating,
								'num_ratings'       => $pl->num_ratings,
								'short_description' => $pl->short_description,
								'icons'             => $pl->icons['2x'],
							);
						}
					}
				}

				set_transient( 'spea_plugins_data', $plugins_arr, 24 * HOUR_IN_SECONDS );
			}
		}

		if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
			array_multisort( array_column( $plugins_arr, 'active_installs' ), SORT_DESC, $plugins_arr );

			foreach ( $plugins_arr as $plugin ) {
				$plugin_slug = $plugin['slug'];
				$plugin_icon = ! empty( $plugin['icons'] ) ? $plugin['icons'] : '';
				$image_type  = 'png';
				if ( isset( self::$plugins[ $plugin_slug ] ) ) {
					$plugin_file = self::$plugins[ $plugin_slug ];
				} else {
					$plugin_file = $plugin_slug . '.php';
				}

				switch ( $plugin_slug ) {
					case 'styble':
						$image_type = 'jpg';
						break;
					case 'location-weather':
					case 'gallery-slider-for-woocommerce':
					case 'testimonial-free':
					case 'logo-carousel-free':
						$image_type = 'gif';
						break;
				}

				$details_link = network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] . '&amp;TB_iframe=true&amp;width=600&amp;height=550' );
				?>
				<div class="plugin-card <?php echo esc_attr( $plugin_slug ); ?>" id="<?php echo esc_attr( $plugin_slug ); ?>">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a class="thickbox" title="<?php echo esc_attr( $plugin['name'] ); ?>" href="<?php echo esc_url( $details_link ); ?>">
									<?php echo esc_html( $plugin['name'] ); ?>
									<img src="<?php echo esc_url( $plugin_icon ); ?>" class="plugin-icon"/>
								</a>
							</h3>
						</div>
						<div class="action-links">
							<ul class="plugin-action-buttons">
								<li>
						<?php
						if ( $this->is_plugin_installed( $plugin_slug, $plugin_file ) ) {
							if ( $this->is_plugin_active( $plugin_slug, $plugin_file ) ) {
								?>
										<button type="button" class="button button-disabled" disabled="disabled">Active</button>
									<?php
							} else {
								?>
											<a href="<?php echo esc_url( $this->activate_plugin_link( $plugin_slug, $plugin_file ) ); ?>" class="button button-primary activate-now">
									<?php esc_html_e( 'Activate', 'easy-accordion-free' ); ?>
											</a>
									<?php
							}
						} else {
							?>
								<a href="<?php echo esc_url( $this->install_plugin_link( $plugin_slug ) ); ?>" class="button install-now">
								<?php esc_html_e( 'Install Now', 'easy-accordion-free' ); ?>
										</a>
								<?php } ?>
								</li>
								<li>
									<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal" aria-label="<?php echo esc_attr( 'More information about ' . $plugin['name'] ); ?>" title="<?php echo esc_attr( $plugin['name'] ); ?>">
								<?php esc_html_e( 'More Details', 'easy-accordion-free' ); ?>
									</a>
								</li>
							</ul>
						</div>
						<div class="desc column-description">
							<p><?php echo esc_html( isset( $plugin['short_description'] ) ? $plugin['short_description'] : '' ); ?></p>
							<p class="authors"> <cite>By <a href="https://shapedplugin.com/">ShapedPlugin LLC</a></cite></p>
						</div>
					</div>
					<?php
					echo '<div class="plugin-card-bottom">';

					if ( isset( $plugin['rating'], $plugin['num_ratings'] ) ) {
						?>
						<div class="vers column-rating">
							<?php
							wp_star_rating(
								array(
									'rating' => $plugin['rating'],
									'type'   => 'percent',
									'number' => $plugin['num_ratings'],
								)
							);
							?>
							<span class="num-ratings">(<?php echo esc_html( number_format_i18n( $plugin['num_ratings'] ) ); ?>)</span>
						</div>
						<?php
					}
					if ( isset( $plugin['version'] ) ) {
						?>
						<div class="column-updated">
							<strong><?php esc_html_e( 'Version:', 'easy-accordion-free' ); ?></strong>
							<span><?php echo esc_html( $plugin['version'] ); ?></span>
						</div>
							<?php
					}

					if ( isset( $plugin['active_installs'] ) ) {
						?>
						<div class="column-downloaded">
						<?php echo esc_html( number_format_i18n( $plugin['active_installs'] ) ) . esc_html__( '+ Active Installations', 'easy-accordion-free' ); ?>
						</div>
									<?php
					}

					if ( isset( $plugin['last_updated'] ) ) {
						?>
						<div class="column-compatibility">
							<strong><?php esc_html_e( 'Last Updated:', 'easy-accordion-free' ); ?></strong>
							<span>
							<?php
							/* translators: %s: property modify last update time. */
							printf( esc_html__( '%s ago', 'easy-accordion-free' ), esc_html( human_time_diff( $plugin['last_updated'] ) ) );
							?>
							</span>
						</div>
									<?php
					}

					echo '</div>';
					?>
				</div>
				<?php
			}
		}
	}

	/**
	 * Check plugins installed function.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_installed( $plugin_slug, $plugin_file ) {
		return file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Check active plugin function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return boolean
	 */
	public function is_plugin_active( $plugin_slug, $plugin_file ) {
		return is_plugin_active( $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Install plugin link.
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @return string
	 */
	public function install_plugin_link( $plugin_slug ) {
		return wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
	}

	/**
	 * Active Plugin Link function
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_file Plugin file.
	 * @return string
	 */
	public function activate_plugin_link( $plugin_slug, $plugin_file ) {
		return wp_nonce_url( admin_url( 'edit.php?post_type=sp_easy_accordion&page=eap_help&action=activate&plugin=' . $plugin_slug . '/' . $plugin_file . '#recommended' ), 'activate-plugin_' . $plugin_slug . '/' . $plugin_file );
	}

	/**
	 * Making page as clean as possible
	 */
	public function disable_admin_notices() {

		global $wp_filter;

		if ( isset( $_GET['post_type'] ) && isset( $_GET['page'] ) && 'sp_easy_accordion' === wp_unslash( $_GET['post_type'] ) && in_array( wp_unslash( $_GET['page'] ), $this->pages ) ) { // @codingStandardsIgnoreLine

			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}

	/**
	 * The Easy Accordion Help Callback.
	 *
	 * @return void
	 */
	public function help_page_callback() {
		add_thickbox();

		$action   = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$plugin   = isset( $_GET['plugin'] ) ? sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) : '';
		$_wpnonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( isset( $action, $plugin ) && ( 'activate' === $action ) && wp_verify_nonce( $_wpnonce, 'activate-plugin_' . $plugin ) ) {
			activate_plugin( $plugin, '', false, true );
		}

		if ( isset( $action, $plugin ) && ( 'deactivate' === $action ) && wp_verify_nonce( $_wpnonce, 'deactivate-plugin_' . $plugin ) ) {
			deactivate_plugins( $plugin, '', false, true );
		}

		?>
		<div class="sp-easy-accordion-help">
			<!-- Header section start -->
			<section class="spea__help header">
				<div class="spea-header-area-top">
					<p>You’re currently using <b>Easy Accordion Lite</b>. To access additional features, consider <a target="_blank" href="https://easyaccordion.io/pricing/?ref=1" ><b>upgrading to Pro!</b></a> 🚀</p>
				</div>
				<div class="spea-header-area">
					<div class="spea-container">
						<div class="spea-header-logo">
							<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/logo.svg' ); ?>" alt="">
							<span><?php echo esc_html( SP_EA_VERSION ); ?></span>
						</div>
					</div>
					<div class="spea-header-logo-shape">
						<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/logo-shape.svg' ); ?>" alt="">
					</div>
				</div>
				<div class="spea-header-nav">
					<div class="spea-container">
						<div class="spea-header-nav-menu">
							<ul>
								<li><a class="active" data-id="get-start-tab"  href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=sp_easy_accordion&page=eap_help#get-start' ); ?>"><i class="spea-icon-play"></i> Get Started</a></li>
								<li><a href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=sp_easy_accordion&page=eap_help#recommended' ); ?>" data-id="recommended-tab"><i class="spea-icon-recommended"></i> Recommended</a></li>
								<li><a href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=sp_easy_accordion&page=eap_help#lite-to-pro' ); ?>" data-id="lite-to-pro-tab"><i class="spea-icon-lite-to-pro-icon"></i> Lite Vs Pro</a></li>
								<li><a href="<?php echo esc_url( home_url( '' ) . '/wp-admin/edit.php?post_type=sp_easy_accordion&page=eap_help#about-us' ); ?>" data-id="about-us-tab"><i class="spea-icon-info-circled-alt"></i> About Us</a></li>
							</ul>
						</div>
					</div>
				</div>
			</section>
			<!-- Header section end -->

			<!-- Start Page -->
			<section class="spea__help start-page" id="get-start-tab">
				<div class="spea-container">
					<div class="spea-start-page-wrap">
						<div class="spea-video-area">
							<h2 class='spea-section-title'>Welcome to Easy Accordion!</h2>
							<span class='spea-normal-paragraph'>Thank you for installing Easy Accordion! This video will help you get started with the plugin. Enjoy!</span>
							<iframe width="724" height="405" src="https://www.youtube.com/embed/jQwLyM0Zb3M?si=V5_AUPkcUeDUA8P2" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
							<ul>
								<li><a class='spea-medium-btn' href="<?php echo esc_url( home_url( '/' ) . 'wp-admin/post-new.php?post_type=sp_easy_accordion' ); ?>">Create a Accordion Group</a></li>
								<li><a target="_blank" class='spea-medium-btn' href="https://easyaccordion.io/easy-accordion-free-demo/">Live Demo</a></li>
								<li><a target="_blank" class='spea-medium-btn arrow-btn' href="https://easyaccordion.io/">Explore Easy Accordion <i class="spea-icon-button-arrow-icon"></i></a></li>
							</ul>
						</div>
						<div class="spea-start-page-sidebar">
							<div class="spea-start-page-sidebar-info-box">
								<div class="spea-info-box-title">
									<h4><i class="spea-icon-doc-icon"></i> Documentation</h4>
								</div>
								<span class='spea-normal-paragraph'>Explore Easy Accordion plugin capabilities in our enriched documentation.</span>
								<a target="_blank" class='spea-small-btn' href="https://docs.shapedplugin.com/docs/easy-accordion/introduction/">Browse Now</a>
							</div>
							<div class="spea-start-page-sidebar-info-box">
								<div class="spea-info-box-title">
									<h4><i class="spea-icon-support"></i> Technical Support</h4>
								</div>
								<span class='spea-normal-paragraph'>For personalized assistance, reach out to our skilled support team for prompt help.</span>
								<a target="_blank" class='spea-small-btn' href="https://shapedplugin.com/create-new-ticket/">Ask Now</a>
							</div>
							<div class="spea-start-page-sidebar-info-box">
								<div class="spea-info-box-title">
									<h4><i class="spea-icon-team-icon"></i> Join The Community</h4>
								</div>
								<span class='spea-normal-paragraph'>Join the official ShapedPlugin Facebook group to share your experiences, thoughts, and ideas.</span>
								<a target="_blank" class='spea-small-btn' href="https://www.facebook.com/groups/ShapedPlugin/">Join Now</a>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Lite To Pro Page -->
			<section class="spea__help lite-to-pro-page" id="lite-to-pro-tab">
				<div class="spea-container">
					<div class="spea-call-to-action-top">
						<h2 class="spea-section-title">Lite vs Pro Comparison</h2>
						<a target="_blank" href="https://easyaccordion.io/pricing/?ref=1" class='spea-big-btn'>Upgrade to Pro Now!</a>
					</div>
					<div class="spea-lite-to-pro-wrap">
						<div class="spea-features">
							<ul>
								<li class='spea-header'>
									<span class='spea-title'>FEATURES</span>
									<span class='spea-free'>Lite</span>
									<span class='spea-pro'><i class='spea-icon-pro'></i> PRO</span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>All Free Version Features</span>
									<span class='spea-free spea-check-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Layout Presets (Vertical, Multicolumn, and Horizontal) <i class="spea-new">New</i></span>
									<span class='spea-free'><b>1</b></span>
									<span class='spea-pro'><b>3</b></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Pre-made Accordion Themes </span>
									<span class='spea-free'><b>1</b></span>
									<span class='spea-pro'><b>17+</b></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Unlimited Multi-level or Nested Accordion FAQs <i class='spea-hot'>hot</i></span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Create Accordion FAQs from Posts, Pages, Custom Post Types, Taxonomies, etc.</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Make any Accordion Item Inactive </span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Hide Less Important Accordion Item</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>WooCommerce Product Custom FAQ Tab <i class="spea-hot">Hot</i></span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion FAQs Schema Markup Supported <i class="spea-hot">Hot</i></span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion AutoPlay Activator Event</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Set Custom Accordion to be Opened on Page Load</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Change Accordion Item to URL</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Expand/Collapse All Button <i class="spea-new">New</i></span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Ajax Accordion FAQ Search Field <i class='spea-new'>New</i></span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Expand & Collapse Icon Style</span>
									<span class='spea-free'><b>1</b></span>
									<span class='spea-pro'><b>18</b></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Expand & Collapse Icon Active and Hover Color</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Add Title Icon from Icon Library and Custom Image Icons</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Title Icon, Size, Color, etc.</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Title Gradient Background Color</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Set Accordion Title HTML Tag</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Title and Description Padding</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Strip All HTML Tags from the Description Content</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>20+ Animation Effects for Accordion Description Content</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Multiple Ajax Paginations for Accordion (Load More, Infinite Scroll, Number, etc.) <i class="spea-new">New</i></span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Accordion Items to Show Per Page</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Import/Export Accordion FAQs</span>
									<span class='spea-free spea-check-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Stylize your FAQs' Typography with 1500+ Google Fonts</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>All Premium Features, Security Enhancements, and Compatibility</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
								<li class='spea-body'>
									<span class='spea-title'>Priority Top-notch Support</span>
									<span class='spea-free spea-close-icon'></span>
									<span class='spea-pro spea-check-icon'></span>
								</li>
							</ul>
						</div>
						<div class="spea-upgrade-to-pro">
							<h2 class='spea-section-title'>Upgrade To PRO & Enjoy Advanced Features!</h2>
							<span class='spea-section-subtitle'>Already, <b>55,000+</b> people are using Easy Accordion on their websites to create beautiful showcase, why won’t you!</span>
							<div class="spea-upgrade-to-pro-btn">
								<div class="spea-action-btn">
									<a target="_blank" href="https://easyaccordion.io/pricing/?ref=1" class='spea-big-btn'>Upgrade to Pro Now!</a>
									<span class='spea-small-paragraph'>14-Day No-Questions-Asked <a target="_blank" href="https://shapedplugin.com/refund-policy/">Refund Policy</a></span>
								</div>
								<a target="_blank" href="https://easyaccordion.io/" class='spea-big-btn-border'>See All Features</a>
								<a target="_blank" href="https://easyaccordion.io/vertical-accordion/" class='spea-big-btn-border spea-live-pro-demo'>Pro Live Demo</a>
							</div>
						</div>
					</div>
					<div class="spea-testimonial">
						<div class="spea-testimonial-title-section">
							<span class='spea-testimonial-subtitle'>NO NEED TO TAKE OUR WORD FOR IT</span>
							<h2 class="spea-section-title">Our Users Love Easy Accordion Pro!</h2>
						</div>
						<div class="spea-testimonial-wrap">
							<div class="spea-testimonial-area">
								<div class="spea-testimonial-content">
									<p>Just wanted to drop a quick note to confirm that not only does the plugin operate as well as – if not better then – you’d expect from the description and other reviews here, but also they have o...</p>
								</div>
								<div class="spea-testimonial-info">
									<div class="spea-img">
										<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/michael.png' ); ?>" alt="">
									</div>
									<div class="spea-info">
										<h3>Michael Kastler</h3>
										<div class="spea-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
							<div class="spea-testimonial-area">
								<div class="spea-testimonial-content">
									<p>My colleagues are very impressed with the result of the multiple accordion. Just what we needed:-) Very useful having the video tutorial, many alternatives don’t. However there is a piece missing from...</p>
								</div>
								<div class="spea-testimonial-info">
									<div class="spea-img">
										<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/joel.png' ); ?>" alt="">
									</div>
									<div class="spea-info">
										<h3>Joel Roberts</h3>
										<div class="spea-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
							<div class="spea-testimonial-area">
								<div class="spea-testimonial-content">
									<p>Nice, simple plugin with a few useful extra options in the Pro version. However, it is the service/support that needs a special mention. I got prompt and helpful replies within a few hours (allowing for...</p>
								</div>
								<div class="spea-testimonial-info">
									<div class="spea-img">
										<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/richard.png' ); ?>" alt="">
									</div>
									<div class="spea-info">
										<h3>Richard Joss</h3>
										<div class="spea-star">
											<i>★★★★★</i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- Recommended Page -->
			<section id="recommended-tab" class="spea-recommended-page">
				<div class="spea-container">
					<h2 class="spea-section-title">Enhance your Website with our Free Robust Plugins</h2>
					<div class="spea-wp-list-table plugin-install-php">
						<div class="spea-recommended-plugins" id="the-list">
							<?php
								$this->spea_plugins_info_api_help_page();
							?>
						</div>
					</div>
				</div>
			</section>

			<!-- About Page -->
			<section id="about-us-tab" class="spea__help about-page">
				<div class="spea-container">
					<div class="spea-about-box">
						<div class="spea-about-info">
							<h3>The Most Powerful Accordion and FAQs Builder plugin for WordPress from the Easy Accordion Team, ShapedPlugin, LLC</h3>
							<p>At <b>ShapedPlugin LLC</b>, we have been looking for the best way to create FAQ pages or sections on WordPress sites. Unfortunately, we couldn't find any suitable plugin that met our needs. Hence, we set a simple goal: to develop a highly customizable and full-featured Accordion and FAQs builder plugin to minimize customer support costs.</p>
							<p>The Easy Accordion plugin provides a convenient way to create visually appealing FAQ pages to reduce customer costs. Check it out now and experience the difference!</p>
							<div class="spea-about-btn">
								<a target="_blank" href="https://easyaccordion.io/" class='spea-medium-btn'>Explore Easy Accordion</a>
								<a target="_blank" href="https://shapedplugin.com/about-us/" class='spea-medium-btn spea-arrow-btn'>More About Us <i class="spea-icon-button-arrow-icon"></i></a>
							</div>
						</div>
						<div class="spea-about-img">
							<img src="<?php echo esc_url( SP_EA_URL . 'admin/help-page/img/shapedplugin-team.jpg' ); ?>" alt="ShapedPlugin Team">
							<span>Team ShapedPlugin LLC at WordCamp Sylhet</span>
						</div>
					</div>
					<?php
					$plugins_arr = get_transient( 'spea_plugins_data' );
					$plugin_icon = array();
					if ( is_array( $plugins_arr ) && ( count( $plugins_arr ) > 0 ) ) {
						foreach ( $plugins_arr as $plugin ) {
							$plugin_icon[ $plugin['slug'] ] = $plugin['icons'];
						}
					}
					?>
					<div class="spea-our-plugin-list">
						<h3 class="spea-section-title">Upgrade your Website with our High-quality Plugins!</h3>
						<?php if ( ! empty( $plugin_icon['wp-carousel-free'] ) ) : ?>
						<div class="spea-our-plugin-list-wrap">
							<a target="_blank" class="spea-our-plugin-list-box" href="https://wpcarousel.io/?ref=1">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['wp-carousel-free'] ); ?>" alt="WP Carousel">
								<h4>WP Carousel</h4>
								<p>The most powerful and user-friendly multi-purpose carousel, slider, & gallery plugin for WordPress.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://realtestimonials.io/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['testimonial-free'] ); ?>" alt="Real Testimonials">
								<h4>Real Testimonials</h4>
								<p>Simply collect, manage, and display Testimonials on your website and boost conversions.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://smartpostshow.com/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['post-carousel'] ); ?>" alt="Smart Post Show">
								<h4>Smart Post Show</h4>
								<p>Filter and display posts (any post types), pages, taxonomy, custom taxonomy, and custom field, in beautiful layouts.</p>
							</a>
							<a target="_blank" href="https://wooproductslider.io/" class="spea-our-plugin-list-box">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-product-slider'] ); ?>" alt="Product Slider for WooCommerce">
								<h4>Product Slider for WooCommerce</h4>
								<p>Boost sales by interactive product Slider, Grid, and Table in your WooCommerce website or store.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://woogallery.io">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['gallery-slider-for-woocommerce'] ); ?>" alt="WooGallery">
								<h4>WooGallery</h4>
								<p>Product gallery slider and additional variation images gallery for WooCommerce and boost your sales.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://getwpteam.com/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['team-free'] ); ?>" alt="WP Team">
								<h4>WP Team</h4>
								<p>Display your team members smartly who are at the heart of your company or organization!</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://logocarousel.com/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['logo-carousel-free'] ); ?>" alt="Logo Carousel">
								<h4>Logo Carousel</h4>
								<p>Showcase a group of logo images with Title, Description, Tooltips, Links, and Popup as a grid or in a carousel.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://shapedplugin.com/smart-swatches-for-woocommerce/?ref=1">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['smart-swatches'] ); ?>" alt="Smart Swatches for WooCommerce">
								<h4>Smart Swatches for WooCommerce</h4>
								<p>Appealing color, image, and button variation swatches on your WooCommerce Shop and Product pages in minutes to increase sales.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://shapedplugin.com/woocategory/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-category-slider-grid'] ); ?>" alt="WooCategory">
								<h4>WooCategory</h4>
								<p>Display by filtering the list of categories aesthetically and boosting sales.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://wptabs.com/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['wp-expand-tabs-free'] ); ?>" alt="WP Tabs">
								<h4>WP Tabs</h4>
								<p>Display tabbed content smartly & quickly on your WordPress site without coding skills.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://shapedplugin.com/plugin/woocommerce-quick-view-pro/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['woo-quickview'] ); ?>" alt="Quick View for WooCommerce">
								<h4>Quick View for WooCommerce</h4>
								<p>Quickly view product information with smooth animation via AJAX in a nice Modal without opening the product page.</p>
							</a>
							<a target="_blank" class="spea-our-plugin-list-box" href="https://shapedplugin.com/smart-brands/">
								<i class="spea-icon-button-arrow-icon"></i>
								<img src="<?php echo esc_url( $plugin_icon['smart-brands-for-woocommerce'] ); ?>" alt="Smart Brands">
								<h4>Smart Brands</h4>
								<p>Smart Brands for WooCommerce Pro helps you display product brands in an attractive way on your online store.</p>
							</a>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- Footer Section -->
			<section class="spea-footer">
				<div class="spea-footer-top">
					<p><span>Made With <i class="spea-icon-heart"></i> </span> By the <a target="_blank" href="https://shapedplugin.com/">ShapedPlugin LLC</a> Team</p>
					<p>Get connected with</p>
					<ul>
						<li><a target="_blank" href="https://www.facebook.com/ShapedPlugin/"><i class="spea-icon-fb"></i></a></li>
						<li><a target="_blank" href="https://twitter.com/intent/follow?screen_name=ShapedPlugin"><i class="spea-icon-x"></i></a></li>
						<li><a target="_blank" href="https://profiles.wordpress.org/shapedplugin/#content-plugins"><i class="spea-icon-wp-icon"></i></a></li>
						<li><a target="_blank" href="https://youtube.com/@ShapedPlugin?sub_confirmation=1"><i class="spea-icon-youtube-play"></i></a></li>
					</ul>
				</div>
			</section>
		</div>
		<?php
	}
}

Easy_Accordion_Free_Help::instance();
