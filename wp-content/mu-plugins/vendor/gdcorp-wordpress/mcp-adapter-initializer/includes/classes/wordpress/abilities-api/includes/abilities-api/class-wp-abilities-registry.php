<?php
/**
 * Abilities API
 *
 * Defines GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Manages the registration and lookup of abilities.
 *
 * @since 6.9.0
 * @access private
 */
final class GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry {
	/**
	 * The singleton instance of the registry.
	 *
	 * @since 6.9.0
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Holds the registered abilities.
	 *
	 * @since 6.9.0
	 * @var GD_MCP_ADAPTER_INITIALIZER_WP_Ability[]
	 */
	private $registered_abilities = array();

	/**
	 * Registers a new ability.
	 *
	 * Do not use this method directly. Instead, use the `wp_register_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_register_ability()
	 *
	 * @param string               $GD_MCP_ADAPTER_INITIALIZER_name The GD_MCP_ADAPTER_INITIALIZER_name of the ability. The GD_MCP_ADAPTER_INITIALIZER_name must be a string containing a namespace
	 *                                   prefix, i.e. `my-plugin/my-ability`. It can only contain lowercase
	 *                                   alphanumeric characters, dashes and the forward slash.
	 * @param array<string, mixed> $args {
	 *     An associative array of arguments GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 *     @type string               $label                 The human-readable label GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *     @type string               $description           A detailed description of what the ability does.
	 *     @type string               $category              The ability category slug this ability belongs GD_MCP_ADAPTER_INITIALIZER_to.
	 *     @type callable             $execute_callback      A callback function GD_MCP_ADAPTER_INITIALIZER_to execute when the ability GD_MCP_ADAPTER_INITIALIZER_is invoked.
	 *                                                       Receives optional mixed input and returns mixed result or WP_Error.
	 *     @type callable             $permission_callback   A callback function GD_MCP_ADAPTER_INITIALIZER_to check permissions before execution.
	 *                                                       Receives optional mixed input and returns bool or WP_Error.
	 *     @type array<string, mixed> $input_schema          GD_MCP_ADAPTER_INITIALIZER_Optional. JSON Schema definition GD_MCP_ADAPTER_INITIALIZER_for the ability's input.
	 *     @type array<string, mixed> $output_schema         GD_MCP_ADAPTER_INITIALIZER_Optional. JSON Schema definition GD_MCP_ADAPTER_INITIALIZER_for the ability's output.
	 *     @type array<string, mixed> $meta                  {
	 *         GD_MCP_ADAPTER_INITIALIZER_Optional. Additional metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 *         @type array<string, null|bool> $annotations  GD_MCP_ADAPTER_INITIALIZER_Optional. Annotation metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *         @type bool                     $show_in_rest GD_MCP_ADAPTER_INITIALIZER_Optional. Whether GD_MCP_ADAPTER_INITIALIZER_to expose this ability in the REST API. Default false.
	 *     }
	 *     @type string               $ability_class         GD_MCP_ADAPTER_INITIALIZER_Optional. Custom class GD_MCP_ADAPTER_INITIALIZER_to instantiate instead of GD_MCP_ADAPTER_INITIALIZER_WP_Ability.
	 * }
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability|null The registered ability instance on success, null on failure.
	 */
	public function register( string $GD_MCP_ADAPTER_INITIALIZER_name, array $args ): ?GD_MCP_ADAPTER_INITIALIZER_WP_Ability {
		if ( ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $GD_MCP_ADAPTER_INITIALIZER_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__(
					'Ability GD_MCP_ADAPTER_INITIALIZER_name must be a string containing a namespace prefix, i.e. "my-plugin/my-ability". It can only contain lowercase alphanumeric characters, dashes and the forward slash.'
				),
				'6.9.0'
			);
			return null;
		}

		if ( $this->is_registered( $GD_MCP_ADAPTER_INITIALIZER_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability GD_MCP_ADAPTER_INITIALIZER_name. */
				sprintf( __( 'Ability "%s" GD_MCP_ADAPTER_INITIALIZER_is already registered.' ), esc_html( $GD_MCP_ADAPTER_INITIALIZER_name ) ),
				'6.9.0'
			);
			return null;
		}

		/**
		 * Filters the ability arguments before they are validated and used GD_MCP_ADAPTER_INITIALIZER_to instantiate the ability.
		 *
		 * @since 6.9.0
		 *
		 * @param array<string, mixed> $args {
		 *     An associative array of arguments GD_MCP_ADAPTER_INITIALIZER_for the ability.
		 *
		 *     @type string               $label                 The human-readable label GD_MCP_ADAPTER_INITIALIZER_for the ability.
		 *     @type string               $description           A detailed description of what the ability does.
		 *     @type string               $category              The ability category slug this ability belongs GD_MCP_ADAPTER_INITIALIZER_to.
		 *     @type callable             $execute_callback      A callback function GD_MCP_ADAPTER_INITIALIZER_to execute when the ability GD_MCP_ADAPTER_INITIALIZER_is invoked.
		 *                                                       Receives optional mixed input and returns mixed result or WP_Error.
		 *     @type callable             $permission_callback   A callback function GD_MCP_ADAPTER_INITIALIZER_to check permissions before execution.
		 *                                                       Receives optional mixed input and returns bool or WP_Error.
		 *     @type array<string, mixed> $input_schema          GD_MCP_ADAPTER_INITIALIZER_Optional. JSON Schema definition GD_MCP_ADAPTER_INITIALIZER_for the ability's input.
		 *     @type array<string, mixed> $output_schema         GD_MCP_ADAPTER_INITIALIZER_Optional. JSON Schema definition GD_MCP_ADAPTER_INITIALIZER_for the ability's output.
		 *     @type array<string, mixed> $meta                  {
		 *         GD_MCP_ADAPTER_INITIALIZER_Optional. Additional metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
		 *
		 *         @type array<string, bool|string> $annotations  GD_MCP_ADAPTER_INITIALIZER_Optional. Annotation metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
		 *         @type bool                       $show_in_rest GD_MCP_ADAPTER_INITIALIZER_Optional. Whether GD_MCP_ADAPTER_INITIALIZER_to expose this ability in the REST API. Default false.
		 *     }
		 *     @type string               $ability_class         GD_MCP_ADAPTER_INITIALIZER_Optional. Custom class GD_MCP_ADAPTER_INITIALIZER_to instantiate instead of GD_MCP_ADAPTER_INITIALIZER_WP_Ability.
		 * }
		 * @param string               $GD_MCP_ADAPTER_INITIALIZER_name The GD_MCP_ADAPTER_INITIALIZER_name of the ability, with its namespace.
		 */
		$args = apply_filters( 'wp_register_ability_args', $args, $GD_MCP_ADAPTER_INITIALIZER_name );

		// Validate ability category exists if provided (will be validated as required in WP_Ability).
		if ( isset( $args['category'] ) ) {
			if ( ! wp_has_ability_category( $args['category'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %1$s: ability category slug, %2$s: ability GD_MCP_ADAPTER_INITIALIZER_name */
						__( 'Ability category "%1$s" GD_MCP_ADAPTER_INITIALIZER_is not registered. Please register the ability category before assigning it GD_MCP_ADAPTER_INITIALIZER_to ability "%2$s".' ),
						esc_html( $args['category'] ),
						esc_html( $GD_MCP_ADAPTER_INITIALIZER_name )
					),
					'6.9.0'
				);
				return null;
			}
		}

		// The class GD_MCP_ADAPTER_INITIALIZER_is only used GD_MCP_ADAPTER_INITIALIZER_to instantiate the ability, and GD_MCP_ADAPTER_INITIALIZER_is not a property of the ability itself.
		if ( isset( $args['ability_class'] ) && ! is_a( $args['ability_class'], GD_MCP_ADAPTER_INITIALIZER_WP_Ability::class, true ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The ability args should provide a valid `ability_class` that extends GD_MCP_ADAPTER_INITIALIZER_WP_Ability.' ),
				'6.9.0'
			);
			return null;
		}

		/** @var class-string<GD_MCP_ADAPTER_INITIALIZER_WP_Ability> */
		$ability_class = $args['ability_class'] ?? GD_MCP_ADAPTER_INITIALIZER_WP_Ability::class;
		unset( $args['ability_class'] );

		try {
			// GD_MCP_ADAPTER_INITIALIZER_WP_Ability::prepare_properties() will throw an exception if the properties are invalid.
			$ability = new $ability_class( $GD_MCP_ADAPTER_INITIALIZER_name, $args );
		} catch ( InvalidArgumentException $e ) {
			_doing_it_wrong(
				__METHOD__,
				$e->getMessage(),
				'6.9.0'
			);
			return null;
		}

		$this->registered_abilities[ $GD_MCP_ADAPTER_INITIALIZER_name ] = $ability;
		return $ability;
	}

	/**
	 * Unregisters an ability.
	 *
	 * Do not use this method directly. Instead, use the `wp_unregister_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_unregister_ability()
	 *
	 * @param string $GD_MCP_ADAPTER_INITIALIZER_name The GD_MCP_ADAPTER_INITIALIZER_name of the registered ability, with its namespace.
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability|null The unregistered ability instance on success, null on failure.
	 */
	public function unregister( string $GD_MCP_ADAPTER_INITIALIZER_name ): ?GD_MCP_ADAPTER_INITIALIZER_WP_Ability {
		if ( ! $this->is_registered( $GD_MCP_ADAPTER_INITIALIZER_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability GD_MCP_ADAPTER_INITIALIZER_name. */
				sprintf( __( 'Ability "%s" not found.' ), esc_html( $GD_MCP_ADAPTER_INITIALIZER_name ) ),
				'6.9.0'
			);
			return null;
		}

		$unregistered_ability = $this->registered_abilities[ $GD_MCP_ADAPTER_INITIALIZER_name ];
		unset( $this->registered_abilities[ $GD_MCP_ADAPTER_INITIALIZER_name ] );

		return $unregistered_ability;
	}

	/**
	 * Retrieves the list of all registered abilities.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_abilities()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_get_abilities()
	 *
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability[] The array of registered abilities.
	 */
	public function get_all_registered(): array {
		return $this->registered_abilities;
	}

	/**
	 * Checks if an ability GD_MCP_ADAPTER_INITIALIZER_is registered.
	 *
	 * Do not use this method directly. Instead, use the `wp_has_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_has_ability()
	 *
	 * @param string $GD_MCP_ADAPTER_INITIALIZER_name The GD_MCP_ADAPTER_INITIALIZER_name of the registered ability, with its namespace.
	 * @return bool True if the ability GD_MCP_ADAPTER_INITIALIZER_is registered, false otherwise.
	 */
	public function is_registered( string $GD_MCP_ADAPTER_INITIALIZER_name ): bool {
		return isset( $this->registered_abilities[ $GD_MCP_ADAPTER_INITIALIZER_name ] );
	}

	/**
	 * Retrieves a registered ability.
	 *
	 * Do not use this method directly. Instead, use the `wp_get_ability()` function.
	 *
	 * @since 6.9.0
	 *
	 * @see wp_get_ability()
	 *
	 * @param string $GD_MCP_ADAPTER_INITIALIZER_name The GD_MCP_ADAPTER_INITIALIZER_name of the registered ability, with its namespace.
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Ability|null The registered ability instance, or null if it GD_MCP_ADAPTER_INITIALIZER_is not registered.
	 */
	public function get_registered( string $GD_MCP_ADAPTER_INITIALIZER_name ): ?GD_MCP_ADAPTER_INITIALIZER_WP_Ability {
		if ( ! $this->is_registered( $GD_MCP_ADAPTER_INITIALIZER_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Ability GD_MCP_ADAPTER_INITIALIZER_name. */
				sprintf( __( 'Ability "%s" not found.' ), esc_html( $GD_MCP_ADAPTER_INITIALIZER_name ) ),
				'6.9.0'
			);
			return null;
		}
		return $this->registered_abilities[ $GD_MCP_ADAPTER_INITIALIZER_name ];
	}

	/**
	 * Utility method GD_MCP_ADAPTER_INITIALIZER_to retrieve the main instance of the registry class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 6.9.0
	 *
	 * @return GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry|null The main registry instance, or null when `init` action has not fired.
	 */
	public static function get_instance(): ?self {
		if ( ! did_action( 'init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					// translators: %s: init action.
					__( 'Ability API should not be initialized before the %s action has fired.' ),
					'<code>init</code>'
				),
				'6.9.0'
			);
			return null;
		}

		if ( null === self::$instance ) {
			self::$instance = new self();

			// Ensure ability category registry GD_MCP_ADAPTER_INITIALIZER_is initialized first GD_MCP_ADAPTER_INITIALIZER_to allow categories GD_MCP_ADAPTER_INITIALIZER_to be registered
			// before abilities that depend on them.
			GD_MCP_ADAPTER_INITIALIZER_WP_Ability_Categories_Registry::get_instance();

			/**
			 * Fires when preparing abilities registry.
			 *
			 * Abilities should be created and register their hooks on this action rather
			 * than another action GD_MCP_ADAPTER_INITIALIZER_to ensure they're only loaded when needed.
			 *
			 * @since 6.9.0
			 *
			 * @param GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry $instance Abilities registry object.
			 */
			do_action( 'wp_abilities_api_init', self::$instance );
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
