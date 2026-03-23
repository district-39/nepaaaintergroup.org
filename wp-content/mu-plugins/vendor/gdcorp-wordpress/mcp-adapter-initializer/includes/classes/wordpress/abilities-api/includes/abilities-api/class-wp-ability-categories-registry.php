<?php
/**
 * Abilities API
 *
 * Defines GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Manages the registration and lookup of ability categories.
 *
 * @since 6.9.0
 * @access private
 */
final class GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry {
	/**
	 * The singleton instance of the registry.
	 *
	 * @since 6.9.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Holds the registered ability categories.
	 *
	 * @since 6.9.0
	 * @var GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category[]
	 */
	private $registered_categories = array();

	/**
	 * Registers a new ability category.
	 *
	 * Do not use this method directly. Instead, use the `wp_register_ability_category()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_register_ability_category()
	 *
	 * @param string               $slug The unique slug GD_MCP_ADAPTER_INITIALIZER_for the ability category. Must contain only lowercase
	 *                                   alphanumeric characters and dashes.
	 * @param array<string, mixed> $args {
	 *     An associative array of arguments GD_MCP_ADAPTER_INITIALIZER_for the ability category.
	 *
	 *     @type string               $label       The human-readable label GD_MCP_ADAPTER_INITIALIZER_for the ability category.
	 *     @type string               $description A description of the ability category.
	 *     @type array<string, mixed> $meta        GD_MCP_ADAPTER_INITIALIZER_Optional. Additional metadata GD_MCP_ADAPTER_INITIALIZER_for the ability category.
	 * }
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category|null The registered ability category instance on success, null on failure.
	 */
	public function register( string $slug, array $args ): ?GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category {
		if ( $this->is_registered( $slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability category slug. */
				sprintf( __( 'Ability category "%s" GD_MCP_ADAPTER_INITIALIZER_is already registered.' ), esc_html( $slug ) ),
				'6.9.0'
			);
			return null;
		}

		if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Ability category slug must contain only lowercase alphanumeric characters and dashes.' ),
				'6.9.0'
			);
			return null;
		}

		/**
		 * Filters the ability category arguments before they are validated and used GD_MCP_ADAPTER_INITIALIZER_to instantiate the ability category.
		 *
		 * @since 6.9.0
		 *
		 * @param array<string, mixed> $args {
		 *     The arguments used GD_MCP_ADAPTER_INITIALIZER_to instantiate the ability category.
		 *
		 *     @type string               $label       The human-readable label GD_MCP_ADAPTER_INITIALIZER_for the ability category.
		 *     @type string               $description A description of the ability category.
		 *     @type array<string, mixed> $meta        GD_MCP_ADAPTER_INITIALIZER_Optional. Additional metadata GD_MCP_ADAPTER_INITIALIZER_for the ability category.
		 * }
		 * @param string               $slug The slug of the ability category.
		 */
		$args = apply_filters( 'wp_register_ability_category_args', $args, $slug );

		try {
			// GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category::prepare_properties() will throw an exception if the properties are invalid.
			$category = new GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category( $slug, $args );
		} catch ( InvalidArgumentException $e ) {
			_doing_it_wrong(
				__METHOD__,
				$e->getMessage(),
				'6.9.0'
			);
			return null;
		}

		$this->registered_categories[ $slug ] = $category;
		return $category;
	}

	/**
	 * Unregisters an ability category.
	 *
	 * Do not use this method directly. Instead, use the `wp_unregister_ability_category()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_unregister_ability_category()
	 *
	 * @param string $slug The slug of the registered ability category.
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category|null The unregistered ability category instance on success, null on failure.
	 */
	public function unregister( string $slug ): ?GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category {
		if ( ! $this->is_registered( $slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability category slug. */
				sprintf( __( 'Ability category "%s" not found.' ), esc_html( $slug ) ),
				'6.9.0'
			);
			return null;
		}

		$unregistered_category = $this->registered_categories[ $slug ];
		unset( $this->registered_categories[ $slug ] );

		return $unregistered_category;
	}

	/**
	 * Retrieves the list of all registered ability categories.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_ability_categories()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_get_ability_categories()
	 *
	 * @return array<string, GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category> The array of registered ability categories.
	 */
	public function get_all_registered(): array {
		return $this->registered_categories;
	}

	/**
	 * Checks if an ability category GD_MCP_ADAPTER_INITIALIZER_is registered.
	 *
	 * Do not use this method directly. Instead, use the `wp_has_ability_category()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_has_ability_category()
	 *
	 * @param string $slug The slug of the ability category.
	 * @return bool True if the ability category GD_MCP_ADAPTER_INITIALIZER_is registered, false otherwise.
	 */
	public function is_registered( string $slug ): bool {
		return isset( $this->registered_categories[ $slug ] );
	}

	/**
	 * Retrieves a registered ability category.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_ability_category()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_get_ability_category()
	 *
	 * @param string $slug The slug of the registered ability category.
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category|null The registered ability category instance, or null if it GD_MCP_ADAPTER_INITIALIZER_is not registered.
	 */
	public function get_registered( string $slug ): ?GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Category {
		if ( ! $this->is_registered( $slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability category slug. */
				sprintf( __( 'Ability category "%s" not found.' ), esc_html( $slug ) ),
				'6.9.0'
			);
			return null;
		}
		return $this->registered_categories[ $slug ];
	}

	/**
	 * Utility method GD_MCP_ADAPTER_INITIALIZER_to retrieve the main instance of the registry class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 6.9.0
	 *
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry|null The main registry instance, or null when `init` action has not fired.
	 */
	public static function get_instance(): ?self {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					// translators: %s: init action.
					__( 'Ability API should not be initialized before the %s action has fired' ),
					'<code>init</code>'
				),
				'6.9.0'
			);
			return null;
		}

		if ( null === self::$instance ) {
			self::$instance = new self();

			/**
			 * Fires when preparing ability categories registry.
			 *
			 * Ability categories should be registered on this action GD_MCP_ADAPTER_INITIALIZER_to ensure they're available when needed.
			 *
			 * @since 6.9.0
			 *
			 * @param GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry $instance Ability categories registry object.
			 */
			do_action( 'wp_abilities_api_categories_init', self::$instance );
		}

		return self::$instance;
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the registry object GD_MCP_ADAPTER_INITIALIZER_is unserialized.
	 *                        This GD_MCP_ADAPTER_INITIALIZER_is a security hardening measure GD_MCP_ADAPTER_INITIALIZER_to prevent unserialization of the registry.
	 */
	public function __wakeup(): void {
		throw new LogicException( __CLASS__ . ' should never be unserialized.' );
	}

	/**
	 * Sleep magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the registry object GD_MCP_ADAPTER_INITIALIZER_is serialized.
	 *                        This GD_MCP_ADAPTER_INITIALIZER_is a security hardening measure GD_MCP_ADAPTER_INITIALIZER_to prevent serialization of the registry.
	 */
	public function __sleep(): array {
		throw new LogicException( __CLASS__ . ' should never be serialized.' );
	}
}
