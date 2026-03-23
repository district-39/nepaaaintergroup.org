<?php
/**
 * MCP WooCommerce Integration Class
 *
 * @package mcp-adapter-initializer
 * @since 0.1.6
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles WooCommerce integration with MCP server
 */
class MCP_WooCommerce {

	/**
	 * Class instance
	 *
	 * @var MCP_WooCommerce|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return MCP_WooCommerce
	 */
	public static function get_instance(): MCP_WooCommerce {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation
	 */
	private function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load WooCommerce MCP dependencies
	 * @return void
	 */
	private function load_dependencies(): void {

		if ( ! $this->is_woocommerce_active() ) {
			return;
		}

		$wc_path = WP_PLUGIN_DIR . '/woocommerce/';

		// load \Automattic\WooCommerce\Internal\Abilities\REST\RestAbilityFactory
		$wc_ability_factory_path = $wc_path . 'src/Internal/Abilities/REST/RestAbilityFactory.php';
		if ( file_exists( $wc_ability_factory_path ) ) {
			require_once $wc_ability_factory_path;
		} else {
			error_log( 'MCP: RestAbilityFactory not found at ' . $wc_ability_factory_path );
		}
	}

	/**
	 * Register WooCommerce abilities with MCP
	 *
	 * @return void
	 */
	public function register_abilities(): void {
		// Register woo tools
		if ( $this->is_woocommerce_active() ) {
			$this->override_permissions();
			foreach ( $this->get_woo_configurations() as $config ) {
				$controller_class = $config['controller'];
				if ( ! class_exists( $controller_class ) ) {
					error_log( 'WooCommerce controller class not found: ' . $controller_class );
				}
				\Automattic\WooCommerce\Internal\Abilities\REST\RestAbilityFactory::register_controller_abilities( $config );
			}
		}
	}

	/**
	 * Disable MCP validation temporarily
	 *
	 * @return void
	 */
	public function disable_validation(): void {
		if ( $this->is_woocommerce_active() ) {
			/*
			 * Temporarily disable MCP validation during server creation.
			 * Workaround for validator bug with union types (e.g., ["integer", "null"]).
			 * This will be removed once the mcp-adapter validator bug is fixed.
			 *
			 * @see https://github.com/WordPress/mcp-adapter/issues/47
			 */
			add_filter( 'mcp_validation_enabled', '__return_false', 999 );
		}
	}

	/**
	 * Re-enable MCP validation
	 *
	 * @return void
	 */
	public function enable_validation(): void {
		if ( $this->is_woocommerce_active() ) {
			// Remove the filter to re-enable validation
			remove_filter( 'mcp_validation_enabled', '__return_true', 999 );
		}
	}

	/**
	 * Check if WooCommerce plugin is active
	 *
	 * @return bool True if WooCommerce is active, false otherwise.
	 */
	private function is_woocommerce_active(): bool {
		$active_plugins = get_option( 'active_plugins', '' );
		if ( empty( $active_plugins ) || ! is_array( $active_plugins ) ) {
			return false;
		}
		$woocommerce_active = in_array( 'woocommerce/woocommerce.php', $active_plugins, true );

		if ( ! $woocommerce_active ) {
			return false;
		}

		$wc_mcp_path = WP_PLUGIN_DIR . '/woocommerce/src/Internal/MCP/';

		if ( ! file_exists( $wc_mcp_path ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get REST controller configurations with explicit IDs, labels, and descriptions.
	 *
	 * @return array Controller configurations.
	 */
	private function get_woo_configurations(): array {
		return array(
			array(
				'controller' => \WC_REST_Products_Controller::class,
				'route'      => '/wc/v3/products',
				'abilities'  => array(
					array(
						'id'          => 'woocommerce/products-list',
						'operation'   => 'list',
						'label'       => __( 'List Products', 'woocommerce' ),
						'description' => __( 'Retrieve a paginated list of products with optional filters for status, category, price range, and other attributes.', 'woocommerce' ),
					),
					array(
						'id'          => 'woocommerce/products-get',
						'operation'   => 'get',
						'label'       => __( 'Get Product', 'woocommerce' ),
						'description' => __( 'Retrieve detailed information about a single product by ID, including price, description, images, and metadata.', 'woocommerce' ),
					),
					array(
						'id'          => 'woocommerce/products-create',
						'operation'   => 'create',
						'label'       => __( 'Create Product', 'woocommerce' ),
						'description' => __( 'Create a new product in WooCommerce with name, price, description, and other product attributes.', 'woocommerce' ),
					),
					array(
						'id'          => 'woocommerce/products-update',
						'operation'   => 'update',
						'label'       => __( 'Update Product', 'woocommerce' ),
						'description' => __( 'Update an existing product by modifying its attributes such as price, stock, description, or metadata.', 'woocommerce' ),
					),
					array(
						'id'          => 'woocommerce/products-delete',
						'operation'   => 'delete',
						'label'       => __( 'Delete Product', 'woocommerce' ),
						'description' => __( 'Permanently delete a product from the store. This action cannot be undone.', 'woocommerce' ),
					),
				),
			),
		);
	}

	/**
	 * Get woo commerce abilities to expose as tools
	 *
	 * @return array
	 */
	public function get_exposed_abilities(): array {
		// Woo abilities
		$abilities = array();
		if ( $this->is_woocommerce_active() ) {
			$all_abilities = wp_get_abilities();
			foreach ( $all_abilities as $ability_id => $ability ) {
				if ( strpos( $ability_id, 'woocommerce/' ) === 0 ) {
					$abilities[] = $ability_id;
				}
			}
		}

		return $abilities;
	}

	/**
	 * Override WooCommerce REST permissions for MCP
	 */
	private function override_permissions(): void {
		// Override WooCommerce REST ability permissions to always allow
		add_filter( 'woocommerce_check_rest_ability_permissions_for_method', '__return_true', 10, 3 );
		add_filter( 'woocommerce_rest_check_permissions', '__return_true', 10, 4 );
	}
}
