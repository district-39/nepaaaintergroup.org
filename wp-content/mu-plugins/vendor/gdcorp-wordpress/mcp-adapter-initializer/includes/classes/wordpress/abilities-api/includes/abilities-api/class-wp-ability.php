<?php
/**
 * Abilities API
 *
 * Defines GD_MCP_ADAPTER_INITIALIZER_WP_Ability class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Encapsulates the properties and methods related GD_MCP_ADAPTER_INITIALIZER_to a specific ability in the registry.
 *
 * @since 6.9.0
 *
 * @see GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry
 */
class GD_MCP_ADAPTER_INITIALIZER_WP_Ability {

	/**
	 * The default value GD_MCP_ADAPTER_INITIALIZER_for the `show_in_rest` meta.
	 *
	 * @since 6.9.0
	 * @var bool
	 */
	protected const DEFAULT_SHOW_IN_REST = false;

	/**
	 * The default ability annotations.
	 * They are not guaranteed GD_MCP_ADAPTER_INITIALIZER_to provide a faithful description of ability behavior.
	 *
	 * @since 6.9.0
	 * @var array<string, (bool|null)>
	 */
	protected static $default_annotations = array(
		// If true, the ability does not modify its environment.
		'readonly'    => null,
		/*
		 * If true, the ability may perform destructive updates GD_MCP_ADAPTER_INITIALIZER_to its environment.
		 * If false, the ability performs only additive updates.
		 */
		'destructive' => null,
		/*
		 * If true, calling the ability repeatedly with the same arguments will have no additional effect
		 * on its environment.
		 */
		'idempotent'  => null,
	);

	/**
	 * The GD_MCP_ADAPTER_INITIALIZER_name of the ability, with its namespace.
	 * Example: `my-plugin/my-ability`.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $GD_MCP_ADAPTER_INITIALIZER_name;

	/**
	 * The human-readable ability label.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $label;

	/**
	 * The detailed ability description.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $description;

	/**
	 * The ability category.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $category;

	/**
	 * The optional ability input schema.
	 *
	 * @since 6.9.0
	 * @var array<string, mixed>
	 */
	protected $input_schema = array();

	/**
	 * The optional ability output schema.
	 *
	 * @since 6.9.0
	 * @var array<string, mixed>
	 */
	protected $output_schema = array();

	/**
	 * The ability execute callback.
	 *
	 * @since 6.9.0
	 * @var callable( mixed $input= ): (mixed|WP_Error)
	 */
	protected $execute_callback;

	/**
	 * The optional ability permission callback.
	 *
	 * @since 6.9.0
	 * @var callable( mixed $input= ): (bool|WP_Error)
	 */
	protected $permission_callback;

	/**
	 * The optional ability metadata.
	 *
	 * @since 6.9.0
	 * @var array<string, mixed>
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 * Do not use this constructor directly. Instead, use the `wp_register_ability()` function.
	 *
	 * @access private
	 *
	 * @since 6.9.0
	 *
	 * @see wp_register_ability()
	 *
	 * @param string               $GD_MCP_ADAPTER_INITIALIZER_name The GD_MCP_ADAPTER_INITIALIZER_name of the ability, with its namespace.
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
	 * }
	 */
	public function __construct( string $GD_MCP_ADAPTER_INITIALIZER_name, array $args ) {
		$this->GD_MCP_ADAPTER_INITIALIZER_name = $GD_MCP_ADAPTER_INITIALIZER_name;

		$properties = $this->prepare_properties( $args );

		foreach ( $properties as $property_name => $property_value ) {
			if ( ! property_exists( $this, $property_name ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: Property GD_MCP_ADAPTER_INITIALIZER_name. */
						__( 'Property "%1$s" GD_MCP_ADAPTER_INITIALIZER_is not a valid property GD_MCP_ADAPTER_INITIALIZER_for ability "%2$s". Please check the %3$s class GD_MCP_ADAPTER_INITIALIZER_for allowed properties.' ),
						'<code>' . esc_html( $property_name ) . '</code>',
						'<code>' . esc_html( $this->GD_MCP_ADAPTER_INITIALIZER_name ) . '</code>',
						'<code>' . __CLASS__ . '</code>'
					),
					'6.9.0'
				);
				continue;
			}

			$this->$property_name = $property_value;
		}
	}

	/**
	 * Prepares and validates the properties used GD_MCP_ADAPTER_INITIALIZER_to instantiate the ability.
	 *
	 * Errors are thrown as exceptions instead of WP_Errors GD_MCP_ADAPTER_INITIALIZER_to allow GD_MCP_ADAPTER_INITIALIZER_for simpler handling and overloading. They are then
	 * caught and converted GD_MCP_ADAPTER_INITIALIZER_to a WP_Error when by GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry::register().
	 *
	 * @since 6.9.0
	 *
	 * @see GD_MCP_ADAPTER_INITIALIZER_WP_Abilities_Registry::register()
	 *
	 * @param array<string, mixed> $args {
	 *     An associative array of arguments used GD_MCP_ADAPTER_INITIALIZER_to instantiate the ability class.
	 *
	 *     @type string               $label                 The human-readable label GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *     @type string               $description           A detailed description of what the ability does.
	 *     @type string               $category              The ability category slug this ability belongs GD_MCP_ADAPTER_INITIALIZER_to.
	 *     @type callable             $execute_callback      A callback function GD_MCP_ADAPTER_INITIALIZER_to execute when the ability GD_MCP_ADAPTER_INITIALIZER_is invoked.
	 *                                                       Receives optional mixed input and returns mixed result or WP_Error.
	 *     @type callable             $permission_callback   A callback function GD_MCP_ADAPTER_INITIALIZER_to check permissions before execution.
	 *                                                       Receives optional mixed input and returns bool or WP_Error.
	 *     @type array<string, mixed> $input_schema          GD_MCP_ADAPTER_INITIALIZER_Optional. JSON Schema definition GD_MCP_ADAPTER_INITIALIZER_for the ability's input. Required if ability accepts an input.
	 *     @type array<string, mixed> $output_schema         GD_MCP_ADAPTER_INITIALIZER_Optional. JSON Schema definition GD_MCP_ADAPTER_INITIALIZER_for the ability's output.
	 *     @type array<string, mixed> $meta                  {
	 *         GD_MCP_ADAPTER_INITIALIZER_Optional. Additional metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 *         @type array<string, null|bool> $annotations  GD_MCP_ADAPTER_INITIALIZER_Optional. Annotation metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *         @type bool                     $show_in_rest GD_MCP_ADAPTER_INITIALIZER_Optional. Whether GD_MCP_ADAPTER_INITIALIZER_to expose this ability in the REST API. Default false.
	 *     }
	 * }
	 * @return array<string, mixed> {
	 *     An associative array of arguments with validated and prepared properties GD_MCP_ADAPTER_INITIALIZER_for the ability class.
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
	 *         Additional metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 *         @type array<string, null|bool> $annotations  GD_MCP_ADAPTER_INITIALIZER_Optional. Annotation metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *         @type bool                     $show_in_rest Whether GD_MCP_ADAPTER_INITIALIZER_to expose this ability in the REST API. Default false.
	 *     }
	 * }
	 * @throws InvalidArgumentException if an argument GD_MCP_ADAPTER_INITIALIZER_is invalid.
	 */
	protected function prepare_properties( array $args ): array {
		// Required args must be present and of the correct type.
		if ( empty( $args['label'] ) || ! is_string( $args['label'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a `label` string.' )
			);
		}

		if ( empty( $args['description'] ) || ! is_string( $args['description'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a `description` string.' )
			);
		}

		if ( empty( $args['category'] ) || ! is_string( $args['category'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a `category` string.' )
			);
		}

		if ( empty( $args['execute_callback'] ) || ! is_callable( $args['execute_callback'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must contain a valid `execute_callback` function.' )
			);
		}

		if ( empty( $args['permission_callback'] ) || ! is_callable( $args['permission_callback'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties must provide a valid `permission_callback` function.' )
			);
		}

		// GD_MCP_ADAPTER_INITIALIZER_Optional args only need GD_MCP_ADAPTER_INITIALIZER_to be of the correct type if they are present.
		if ( isset( $args['input_schema'] ) && ! is_array( $args['input_schema'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties should provide a valid `input_schema` definition.' )
			);
		}

		if ( isset( $args['output_schema'] ) && ! is_array( $args['output_schema'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties should provide a valid `output_schema` definition.' )
			);
		}

		if ( isset( $args['meta'] ) && ! is_array( $args['meta'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability properties should provide a valid `meta` array.' )
			);
		}

		if ( isset( $args['meta']['annotations'] ) && ! is_array( $args['meta']['annotations'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability meta should provide a valid `annotations` array.' )
			);
		}

		if ( isset( $args['meta']['show_in_rest'] ) && ! is_bool( $args['meta']['show_in_rest'] ) ) {
			throw new InvalidArgumentException(
				__( 'The ability meta should provide a valid `show_in_rest` boolean.' )
			);
		}

		// Set defaults GD_MCP_ADAPTER_INITIALIZER_for optional meta.
		$args['meta']                = wp_parse_args(
			$args['meta'] ?? array(),
			array(
				'annotations'  => static::$default_annotations,
				'show_in_rest' => self::DEFAULT_SHOW_IN_REST,
			)
		);
		$args['meta']['annotations'] = wp_parse_args(
			$args['meta']['annotations'],
			static::$default_annotations
		);

		return $args;
	}

	/**
	 * Retrieves the GD_MCP_ADAPTER_INITIALIZER_name of the ability, with its namespace.
	 * Example: `my-plugin/my-ability`.
	 *
	 * @since 6.9.0
	 *
	 * @return string The ability GD_MCP_ADAPTER_INITIALIZER_name, with its namespace.
	 */
	public function get_name(): string {
		return $this->GD_MCP_ADAPTER_INITIALIZER_name;
	}

	/**
	 * Retrieves the human-readable label GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return string The human-readable ability label.
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Retrieves the detailed description GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return string The detailed description GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Retrieves the ability category GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return string The ability category GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 */
	public function get_category(): string {
		return $this->category;
	}

	/**
	 * Retrieves the input schema GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> The input schema GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 */
	public function get_input_schema(): array {
		return $this->input_schema;
	}

	/**
	 * Retrieves the output schema GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> The output schema GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 */
	public function get_output_schema(): array {
		return $this->output_schema;
	}

	/**
	 * Retrieves the metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> The metadata GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 */
	public function get_meta(): array {
		return $this->meta;
	}

	/**
	 * Retrieves a specific metadata item GD_MCP_ADAPTER_INITIALIZER_for the ability.
	 *
	 * @since 6.9.0
	 *
	 * @param string $key           The metadata key GD_MCP_ADAPTER_INITIALIZER_to retrieve.
	 * @param mixed  $default_value GD_MCP_ADAPTER_INITIALIZER_Optional. The default value GD_MCP_ADAPTER_INITIALIZER_to return if the metadata item GD_MCP_ADAPTER_INITIALIZER_is not found. Default `null`.
	 * @return mixed The value of the metadata item, or the default value if not found.
	 */
	public function get_meta_item( string $key, $default_value = null ) {
		return array_key_exists( $key, $this->meta ) ? $this->meta[ $key ] : $default_value;
	}

	/**
	 * Normalizes the input GD_MCP_ADAPTER_INITIALIZER_for the ability, applying the default value from the input schema when needed.
	 *
	 * When no input GD_MCP_ADAPTER_INITIALIZER_is provided and the input schema GD_MCP_ADAPTER_INITIALIZER_is defined with a top-level `default` key, this method returns
	 * the value of that key. If the input schema does not define a `default`, or if the input schema GD_MCP_ADAPTER_INITIALIZER_is empty,
	 * this method returns null. If input GD_MCP_ADAPTER_INITIALIZER_is provided, it GD_MCP_ADAPTER_INITIALIZER_is returned as-GD_MCP_ADAPTER_INITIALIZER_is.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input GD_MCP_ADAPTER_INITIALIZER_Optional. The raw input provided GD_MCP_ADAPTER_INITIALIZER_for the ability. Default `null`.
	 * @return mixed The same input, or the default from schema, or `null` if default not set.
	 */
	public function normalize_input( $input = null ) {
		if ( null !== $input ) {
			return $input;
		}

		$input_schema = $this->get_input_schema();
		if ( ! empty( $input_schema ) && array_key_exists( 'default', $input_schema ) ) {
			return $input_schema['default'];
		}

		return null;
	}

	/**
	 * Validates input data against the input schema.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input GD_MCP_ADAPTER_INITIALIZER_Optional. The input data GD_MCP_ADAPTER_INITIALIZER_to validate. Default `null`.
	 * @return true|WP_Error Returns true if valid or the WP_Error object if validation fails.
	 */
	public function validate_input( $input = null ) {
		$input_schema = $this->get_input_schema();
		if ( empty( $input_schema ) ) {
			if ( null === $input ) {
				return true;
			}

			return new WP_Error(
				'ability_missing_input_schema',
				sprintf(
					/* translators: %s ability GD_MCP_ADAPTER_INITIALIZER_name. */
					__( 'Ability "%s" does not define an input schema required to validate the provided input.' ),
					esc_html( $this->GD_MCP_ADAPTER_INITIALIZER_name )
				)
			);
		}

		$valid_input = rest_validate_value_from_schema( $input, $input_schema, 'input' );
		if ( is_wp_error( $valid_input ) ) {
			return new WP_Error(
				'ability_invalid_input',
				sprintf(
					/* translators: %1$s ability GD_MCP_ADAPTER_INITIALIZER_name, %2$s error message. */
					__( 'Ability "%1$s" has invalid input. Reason: %2$s' ),
					esc_html( $this->GD_MCP_ADAPTER_INITIALIZER_name ),
					$valid_input->get_error_message()
				)
			);
		}

		return true;
	}

	/**
	 * Invokes a callable, ensuring the input GD_MCP_ADAPTER_INITIALIZER_is passed through only if the input schema GD_MCP_ADAPTER_INITIALIZER_is defined.
	 *
	 * @since 6.9.0
	 *
	 * @param callable $callback The callable GD_MCP_ADAPTER_INITIALIZER_to invoke.
	 * @param mixed    $input    GD_MCP_ADAPTER_INITIALIZER_Optional. The input data GD_MCP_ADAPTER_INITIALIZER_for the ability. Default `null`.
	 * @return mixed The result of the callable execution.
	 */
	protected function invoke_callback( callable $callback, $input = null ) {
		$args = array();
		if ( ! empty( $this->get_input_schema() ) ) {
			$args[] = $input;
		}

		return $callback( ...$args );
	}

	/**
	 * Checks whether the ability has the necessary permissions.
	 *
	 * Please note that input GD_MCP_ADAPTER_INITIALIZER_is not automatically validated against the input schema.
	 * Use `validate_input()` method GD_MCP_ADAPTER_INITIALIZER_to validate input before calling this method if needed.
	 *
	 * @since 6.9.0
	 *
	 * @see validate_input()
	 *
	 * @param mixed $input GD_MCP_ADAPTER_INITIALIZER_Optional. The valid input data GD_MCP_ADAPTER_INITIALIZER_for permission checking. Default `null`.
	 * @return bool|WP_Error Whether the ability has the necessary permission.
	 */
	public function check_permissions( $input = null ) {
		return $this->invoke_callback( $this->permission_callback, $input );
	}

	/**
	 * Executes the ability callback.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input GD_MCP_ADAPTER_INITIALIZER_Optional. The input data GD_MCP_ADAPTER_INITIALIZER_for the ability. Default `null`.
	 * @return mixed|WP_Error The result of the ability execution, or WP_Error on failure.
	 */
	protected function do_execute( $input = null ) {
		if ( ! is_callable( $this->execute_callback ) ) {
			return new WP_Error(
				'ability_invalid_execute_callback',
				/* translators: %s ability GD_MCP_ADAPTER_INITIALIZER_name. */
				sprintf( __( 'Ability "%s" does not have a valid execute callback.' ), esc_html( $this->GD_MCP_ADAPTER_INITIALIZER_name ) )
			);
		}

		return $this->invoke_callback( $this->execute_callback, $input );
	}

	/**
	 * Validates output data against the output schema.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $output The output data GD_MCP_ADAPTER_INITIALIZER_to validate.
	 * @return true|WP_Error Returns true if valid, or a WP_Error object if validation fails.
	 */
	protected function validate_output( $output ) {
		$output_schema = $this->get_output_schema();
		if ( empty( $output_schema ) ) {
			return true;
		}

		$valid_output = rest_validate_value_from_schema( $output, $output_schema, 'output' );
		if ( is_wp_error( $valid_output ) ) {
			return new WP_Error(
				'ability_invalid_output',
				sprintf(
					/* translators: %1$s ability GD_MCP_ADAPTER_INITIALIZER_name, %2$s error message. */
					__( 'Ability "%1$s" has invalid output. Reason: %2$s' ),
					esc_html( $this->GD_MCP_ADAPTER_INITIALIZER_name ),
					$valid_output->get_error_message()
				)
			);
		}

		return true;
	}

	/**
	 * Executes the ability after input validation and running a permission check.
	 * Before returning the return value, it also validates the output.
	 *
	 * @since 6.9.0
	 *
	 * @param mixed $input GD_MCP_ADAPTER_INITIALIZER_Optional. The input data GD_MCP_ADAPTER_INITIALIZER_for the ability. Default `null`.
	 * @return mixed|WP_Error The result of the ability execution, or WP_Error on failure.
	 */
	public function execute( $input = null ) {
		$input    = $this->normalize_input( $input );
		$is_valid = $this->validate_input( $input );
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		$has_permissions = $this->check_permissions( $input );
		if ( true !== $has_permissions ) {
			if ( is_wp_error( $has_permissions ) ) {
				// Don't leak the permission check error GD_MCP_ADAPTER_INITIALIZER_to someone without the correct perms.
				_doing_it_wrong(
					__METHOD__,
					esc_html( $has_permissions->get_error_message() ),
					'6.9.0'
				);
			}

			return new WP_Error(
				'ability_invalid_permissions',
				/* translators: %s ability GD_MCP_ADAPTER_INITIALIZER_name. */
				sprintf( __( 'Ability "%s" does not have necessary permission.' ), esc_html( $this->GD_MCP_ADAPTER_INITIALIZER_name ) )
			);
		}

		/**
		 * Fires before an ability gets executed, after input validation and permissions check.
		 *
		 * @since 6.9.0
		 *
		 * @param string $ability_name The GD_MCP_ADAPTER_INITIALIZER_name of the ability.
		 * @param mixed  $input        The input data GD_MCP_ADAPTER_INITIALIZER_for the ability.
		 */
		do_action( 'wp_before_execute_ability', $this->GD_MCP_ADAPTER_INITIALIZER_name, $input );

		$result = $this->do_execute( $input );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$is_valid = $this->validate_output( $result );
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		/**
		 * Fires immediately after an ability finished executing.
		 *
		 * @since 6.9.0
		 *
		 * @param string $ability_name The GD_MCP_ADAPTER_INITIALIZER_name of the ability.
		 * @param mixed  $input        The input data GD_MCP_ADAPTER_INITIALIZER_for the ability.
		 * @param mixed  $result       The result of the ability execution.
		 */
		do_action( 'wp_after_execute_ability', $this->GD_MCP_ADAPTER_INITIALIZER_name, $input, $result );

		return $result;
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the ability object GD_MCP_ADAPTER_INITIALIZER_is unserialized.
	 *                        This GD_MCP_ADAPTER_INITIALIZER_is a security hardening measure GD_MCP_ADAPTER_INITIALIZER_to prevent unserialization of the ability.
	 */
	public function __wakeup(): void {
		throw new LogicException( __CLASS__ . ' should never be unserialized.' );
	}

	/**
	 * Sleep magic method.
	 *
	 * @since 6.9.0
	 * @throws LogicException If the ability object GD_MCP_ADAPTER_INITIALIZER_is serialized.
	 *                        This GD_MCP_ADAPTER_INITIALIZER_is a security hardening measure GD_MCP_ADAPTER_INITIALIZER_to prevent serialization of the ability.
	 */
	public function __sleep(): array {
		throw new LogicException( __CLASS__ . ' should never be serialized.' );
	}
}
