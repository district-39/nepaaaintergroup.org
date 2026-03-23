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
 * @copyright Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace GoDaddy\WordPress\MWC\GiftCertificates;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * Class for handling main query and rewrite rules
 *
 * @since 3.0.0
 */
class MWC_Gift_Certificates_Query {


	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		// comments (voucher notes) handling
		add_filter( 'comments_clauses',   array( $this, 'exclude_voucher_notes_from_queries' ), 10, 1 );
		add_action( 'comment_feed_join',  array( $this, 'exclude_voucher_notes_from_feed_join' ) );
		add_action( 'comment_feed_where', array( $this, 'exclude_voucher_notes_from_feed_where' ) );
	}


	/**
	 * Excludes vouchers notes from queries and RSS
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 * @param array $clauses
	 * @return array
	 */
	public function exclude_voucher_notes_from_queries( $clauses ) {
		global $wpdb;

		if ( ! $this->shouldExcludeVoucherNotesFromQueries() ) {
			return $clauses;
		}

		if ( ! $clauses['join'] ) {
			$clauses['join'] = '';
		}

		if ( ! strstr( $clauses['join'], "JOIN $wpdb->posts" ) ) {
			$clauses['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
		}

		$clauses['where'] = $this->buildExcludeVoucherWhereClause($clauses['where'] ?? '');

		return $clauses;
	}

	protected function shouldExcludeVoucherNotesFromQueries() : bool
	{
		global $typenow;

		return 'wc_voucher' !== $typenow || ! is_admin() || ! current_user_can( 'manage_woocommerce' );
	}

	protected function buildExcludeVoucherWhereClause(string $whereClause) : string
	{
		global $wpdb;

		if (! $whereClause) {
			return " $wpdb->posts.post_type <> 'wc_voucher' ";
		}

		$databaseTableName = $wpdb->posts;
		// this accounts for WC introducing a table alias in the where clause to exclude product reviews
		if (false !== strpos( $whereClause, "wp_posts_to_exclude_reviews.post_type" )) {
			$databaseTableName = "wp_posts_to_exclude_reviews";
		}

		if ( false !== strpos( $whereClause, "wp_posts_to_exclude_reviews.post_type NOT IN ('product')" ) ) {
			return str_replace( "wp_posts_to_exclude_reviews.post_type NOT IN ('product')", "wp_posts_to_exclude_reviews.post_type NOT IN ('product', 'wc_voucher')", $whereClause );
		} else {
			$whereClause .= " AND $databaseTableName.post_type <> 'wc_voucher' ";

			return $whereClause;
		}
	}


	/**
	 * Excludes voucher notes from queries and RSS
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 * @param string $join
	 * @return string
	 */
	public function exclude_voucher_notes_from_feed_join( $join ) {
		global $wpdb;

		if ( ! strstr( $join, $wpdb->posts ) ) {
			$join = " LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID ";
		}

		return $join;
	}


	/**
	 * Excludes voucher notes from queries and RSS
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 * @param string $where
	 * @return string
	 */
	public function exclude_voucher_notes_from_feed_where( $where ) {
		global $wpdb;

		if ( $where ) {
			$where .= ' AND ';
		}

		$where .= " $wpdb->posts.post_type <> 'wc_voucher' ";

		return $where;
	}


}
