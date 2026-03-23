<?php
declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Utils;

// Prevent direct access.
use InvalidArgumentException;
use RuntimeException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configuration Utility.
 */
class Config {

	const ENV_PRODUCTION        = 'production';
	const ENV_TEST              = 'test';
	const ENV_DEVELOPMENT       = 'development';
	const ENV_DEVELOPMENT_SHORT = 'dev';
	const ENV_LOCAL             = 'local';

	/**
	 * Current environment.
	 *
	 * @var string
	 */
	protected string $env = '';

	/**
	 * Environment-specific configuration data.
	 *
	 * @var array
	 */
	protected $env_data = array();

	/**
	 * Default configuration data.
	 *
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->detect_env();
	}

	/**
	 * Get the current environment.
	 *
	 * @return string
	 */
	public function get_env(): string {
		return $this->env;
	}

	/**
	 * Detect the current environment based on SERVER_ENV and WP_ENVIRONMENT_TYPE.
	 * Sets the $env property accordingly.
	 *
	 * @return void
	 */
	protected function detect_env(): void {
		// Pick environment key from SERVER_ENV and WP_ENVIRONMENT_TYPE if set, and load specific config, else load 'production' by default.
		$env = getenv( 'SERVER_ENV' );

		switch ( $env ) {
			case self::ENV_DEVELOPMENT:
			case self::ENV_DEVELOPMENT_SHORT:
				$env = self::ENV_DEVELOPMENT;
				break;
			case self::ENV_TEST:
				$env = self::ENV_TEST;
				break;
			default:
				$env = self::ENV_PRODUCTION;
				break;
		}

		if ( function_exists( 'wp_get_environment_type' ) ) {
			$wp_env = wp_get_environment_type();
		} else {
			$wp_env = getenv( 'WP_ENVIRONMENT_TYPE' );
		}

		// In case of dev server env, there is subset allowed, based on WP_ENVIRONMENT_TYPE.
		if ( self::ENV_DEVELOPMENT === $env && self::ENV_LOCAL === $wp_env ) {
			$env = self::ENV_LOCAL;
		}

		$this->env = $env;
	}

	/**
	 * Load configuration from a JSON file.
	 *
	 * @param string $file_path Path to the JSON configuration file.
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException If the file does not exist.
	 * @throws RuntimeException If there is an error decoding the JSON.
	 */
	public function load_from_json( string $file_path ): void {
		if ( ! file_exists( $file_path ) ) {
			throw new InvalidArgumentException( "File not found: $file_path" );
		}

		$json_content = file_get_contents( $file_path );
		$config_data  = json_decode( $json_content, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new RuntimeException( 'Error decoding JSON: ' . json_last_error_msg() );
		}

		// Get product config to be stored in default_data.
		$this->default_data = $config_data['production'] ?? array();
		// Get env specific config to be stored in env_data.
		$this->env_data = $config_data[ $this->get_env() ] ?? array();
	}

	/**
	 * Get a configuration value by key, checking env-specific data first, then default data.
	 *
	 * @param string $key The configuration key.
	 * @param mixed  $default_value The default value to return if the key is not found.
	 *
	 * @return mixed The configuration value or the default value.
	 */
	public function get( string $key, $default_value = null ) {
		// Check in env-specific data first.
		if ( array_key_exists( $key, $this->env_data ) ) {
			return $this->env_data[ $key ];
		}

		// Fallback to default data.
		if ( array_key_exists( $key, $this->default_data ) ) {
			return $this->default_data[ $key ];
		}

		return $default_value;
	}

	/**
	 * Get allowed iframe origins from the configuration.
	 *
	 * @return string[]
	 */
	public function get_iframe_origins(): array {
		$origins = $this->get( 'iframe_origins', array() );
		if ( is_array( $origins ) ) {
			return $origins;
		}

		return array();
	}

	/**
	 * Get allowed API origins from the configuration.
	 *
	 * @return string[]
	 */
	public function get_api_origins(): array {
		$origins = $this->get( 'api_origins', array() );
		if ( is_array( $origins ) ) {
			return $origins;
		}

		return array();
	}

	/**
	 * Get the SSO URL from the configuration.
	 *
	 * @return string
	 */
	public function get_sso_url(): string {
		$sso_url = $this->get( 'sso_url', '' );
		if ( is_string( $sso_url ) ) {
			return $sso_url;
		}

		return '';
	}

	/**
	 * Get the WP Public API URL from the configuration.
	 *
	 * Used for request signature validation via the /validate endpoint.
	 *
	 * @return string
	 */
	public function get_wp_public_api_url(): string {
		// Allow override via constant.
		if ( defined( 'GD_WP_PUBLIC_API_URL' ) ) {
			$constant_value = constant( 'GD_WP_PUBLIC_API_URL' );
			if ( is_string( $constant_value ) && '' !== $constant_value ) {
				return $constant_value;
			}
		}

		$url = $this->get( 'wp_public_api_url', '' );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}

		return '';
	}
}
