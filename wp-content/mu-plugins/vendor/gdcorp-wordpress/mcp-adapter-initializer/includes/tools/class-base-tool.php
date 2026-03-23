<?php

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\MCP\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for MCP tools providing common functionality.
 */
abstract class Base_Tool {
	/**
	 * Execute wrapper that sets admin user before calling the actual execute method
	 *
	 * @param array $input Input parameters.
	 *
	 * @return array Execution result
	 */
	final public function execute_with_admin( array $input ): array {
		$this->set_admin_user();

		return $this->execute( $input );
	}

	/**
	 * Execute the tool - to be implemented by child classes
	 *
	 * @param array $input Input parameters.
	 *
	 * @return array Execution result
	 */
	abstract public function execute( array $input ): array;

	/**
	 * Set the current user to an administrator for permission purposes
	 *
	 * @return void
	 */
	protected function set_admin_user(): void {
		$admins = get_users(
			array(
				'role'    => 'Administrator',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'number'  => 1,
			)
		);

		if ( ! empty( $admins ) ) {
			wp_set_current_user( $admins[0]->ID );
		}
	}

	/**
	 * Load WordPress admin file
	 * Wrapper method to load WordPress admin files
	 * In test environment, functions are pre-defined in bootstrap.php
	 *
	 * @param string $filename Admin filename (e.g., 'plugin.php').
	 *
	 * @return void
	 */
	protected function load_admin_file( string $filename ): void {
		// In tests (PHPUnit), functions are pre-defined in bootstrap.php, so skip loading.
		if ( defined( 'PHPUNIT_RUNNING' ) ) {
			return;
		}

		// In production, dynamically load the file.
		$base_path = ABSPATH . 'wp-admin/includes/';
		$full_path = $base_path . $filename;

		if ( file_exists( $full_path ) ) {
			// @codeCoverageIgnoreStart
			// Use eval with string concatenation to hide from Patchwork tokenizer.
			// phpcs:ignore Squiz.PHP.Eval.Discouraged,Generic.Strings.UnnecessaryStringConcat.Found
			eval( 'require' . '_once $full_path;' );
			// @codeCoverageIgnoreEnd
		}
	}

	/**
	 * Build an output schema for a tool
	 *
	 * @param string $description Additional description to add to the schema.
	 * @param array  $properties Additional properties to add to the schema.
	 * @param array  $required Additional required properties to add to the schema.
	 *
	 * @return array Output schema
	 */
	protected function build_output_schema( string $description = '', array $properties = array(), array $required = array() ): array {
		$schema = array(
			'type'       => 'object',
			'properties' => array(
				'success' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the operation succeeded', 'mcp-adapter-initializer' ),
				),
				'message' => array(
					'type'        => 'string',
					'description' => __( 'Success or error message' ),
				),
			),
			'required'   => array( 'success' ),
		);

		if ( ! empty( $description ) ) {
			$schema['description'] = $description;
		}

		if ( ! empty( $properties ) ) {
			$schema['properties'] = array_merge( $schema['properties'], $properties );
		}

		if ( ! empty( $required ) ) {
			$schema['required'] = array_merge( $schema['required'], $required );
		}

		return $schema;
	}
}
