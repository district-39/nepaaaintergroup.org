<?php
/**
 * MWC Gift Certificates
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MWC Gift Certificates to newer
 * versions in the future. If you wish to customize MWC Gift Certificates for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-pdf-product-vouchers/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2025, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace GoDaddy\WordPress\MWC\GiftCertificates\Integrations;
use function GoDaddy\WordPress\MWC\GiftCertificates\wc_pdf_product_vouchers;

/**
 * Elementor integration
 *
 * @since 3.12.3
 */
class Elementor {


	/**
	 * Constructor.
	 *
	 * @since 3.12.3
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'load_template_functions' ] );
	}


	/**
	 * Ensures that the plugin template functions are loaded to avoid fatal errors.
	 *
	 * @since 3.12.3
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function load_template_functions() {

		if ( did_action( 'elementor/loaded' ) ) {
			require_once( wc_pdf_product_vouchers()->get_plugin_path() . '/src/functions/wc-pdf-product-vouchers-functions-template.php' );
		}
	}


}
