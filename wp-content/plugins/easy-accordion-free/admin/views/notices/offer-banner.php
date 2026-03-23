<?php
/**
 * Plugin offer banner.
 *
 * @since        3.0.5
 * @version      3.0.5
 *
 * @package    Easy_Accordion_Free
 * @subpackage Easy_Accordion_Free/admin/views/notices
 * @author     ShapedPlugin<support@shapedplugin.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ShapedPlugin_Offer_Banner' ) ) {
	/**
	 * The class for all admin offer banner.
	 */
	class ShapedPlugin_Offer_Banner {

		/**
		 * The single instance of the class.
		 *
		 * @var self
		 * @since
		 */
		private static $instance = null;

		/**
		 * Class constructor.
		 */
		private function __construct() {
			add_action( 'admin_notices', array( $this, 'render_offer_banner' ) );
			add_action( 'wp_ajax_shapedplugin_dismiss_offer_banner', array( $this, 'dismiss_offer_banner' ) );
		}

		/**
		 * Retrieves the singleton instance of the class.
		 *
		 * This method ensures that only one instance of the class is created (singleton pattern).
		 *
		 * @return self The singleton instance of the class.
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Retrieves the active offers.
		 *
		 * @return array The list of active offers.
		 */
		public function get_active_offers() {
			$now = time();

			// Define offer durations.
			$offers = array(
				'black_friday' => array(
					'id'    => 'black_friday_2025',
					'start' => strtotime( '2025-11-18 00:00:00' ),
					'end'   => strtotime( '2025-12-14 23:59:59' ),
					'image' => SP_EA_URL . 'admin/img/offer-banner/bfcm-offer-banner.svg',
					'link'  => 'https://easyaccordion.io/pricing/?campaign=ea&ref=423',
				),
				'new_year'     => array(
					'id'    => 'new_year_2026',
					'start' => strtotime( '2025-12-26 00:00:00' ),
					'end'   => strtotime( '2026-01-14 23:59:59' ),
					'image' => SP_EA_URL . 'admin/img/offer-banner/new-year-offer-banner.svg',
					'link'  => 'https://easyaccordion.io/pricing/?campaign=ea&ref=423',
				),
			);

			$active_offers = array();

			foreach ( $offers as $key => $offer ) {
				if ( $now >= $offer['start'] && $now <= $offer['end'] ) {
					$active_offers[ $key ] = $offer;
				}
			}

			return $active_offers;
		}

		/**
		 * Renders the offer banner on the page.
		 *
		 * @return void
		 */
		public function render_offer_banner() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$active_offers = $this->get_active_offers();

			if ( empty( $active_offers ) ) {
				return;
			}

			foreach ( $active_offers as $offer ) {
				$option_key = 'shapedplugin_offer_banner_dismissed_' . $offer['id'];

				// Check dismissed status.
				if ( get_option( $option_key ) ) {
					continue;
				}

				$nonce = wp_create_nonce( 'smart_tabs_offer_dismiss' );
				?>
				<div id="shapedplugin-offer-banner" class="notice notice-info is-dismissible">
					<a href="<?php echo esc_url( $offer['link'] ); ?>" target="_blank">
						<img src="<?php echo esc_url( $offer['image'] ); ?>" alt="ShapedPlugin Offer" style="width:100%;height:auto;">
					</a>
				
					<button type="button"
						class="notice-dismiss shapedplugin-offer-banner-dismiss"
						data-offer-id="<?php echo esc_attr( $offer['id'] ); ?>"
						data-nonce="<?php echo esc_attr( $nonce ); ?>">
					</button>
				</div>
				<?php
			}
			?>
			<script type="text/javascript">
				(function($){
					$(document).on('click', '#shapedplugin-offer-banner .notice-dismiss', function(e){
						e.preventDefault();
						const nonce = $(this).data('nonce');
						const offerID = $(this).data('offer-id')
						$.post(ajaxurl, {
							action: 'shapedplugin_dismiss_offer_banner',
							offer_id: offerID,
							nonce: nonce
						});
						$('#shapedplugin-offer-banner').fadeOut(300);
					});
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Handles the AJAX request to dismiss the offer banner.
		 *
		 * @return void
		 */
		public function dismiss_offer_banner() {
			check_ajax_referer( 'smart_tabs_offer_dismiss', 'nonce' );
			$offer_id = isset( $_POST['offer_id'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_id'] ) ) : '';

			update_option( 'shapedplugin_offer_banner_dismissed_' . $offer_id, true );

			wp_send_json_success();
		}
	}

	// Initialize the offer banner.
	ShapedPlugin_Offer_Banner::instance();
}