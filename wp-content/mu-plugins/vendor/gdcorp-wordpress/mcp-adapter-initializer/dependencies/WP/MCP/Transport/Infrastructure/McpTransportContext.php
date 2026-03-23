<?php
/**
 * Transport context object for dependency injection.
 *
 * @package McpAdapter
 */

declare( strict_types=1 );

namespace GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure;

use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Core\McpServer;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Initialize\InitializeHandler;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Prompts\PromptsHandler;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Resources\ResourcesHandler;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\System\SystemHandler;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Tools\ToolsHandler;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\ErrorHandling\Contracts\McpErrorHandlerInterface;
use GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\Observability\Contracts\McpObservabilityHandlerInterface;

/**
 * Transport context object for dependency injection.
 *
 * Contains all dependencies needed by transport implementations,
 * promoting loose coupling and easier testing.
 *
 * Note: The request_router parameter is optional. If not provided,
 * a RequestRouter instance will be automatically created with this
 * context as its dependency.
 */
class McpTransportContext {

	/**
	 * Initialize the transport context.
	 *
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Core\McpServer             $mcp_server The MCP server instance.
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Initialize\InitializeHandler     $initialize_handler The initialize handler.
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Tools\ToolsHandler          $tools_handler The tools handler.
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Resources\ResourcesHandler      $resources_handler The resources handler.
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Prompts\PromptsHandler        $prompts_handler The prompts handler.
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\System\SystemHandler         $system_handler The system handler.
	 * @param string                $observability_handler The observability handler class name.
	 * @param \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\RequestRouter|null $request_router The request router service.
	 * @param callable|null         $transport_permission_callback Optional custom permission callback for transport-level authentication.
	 */
	/**
	 * The MCP server instance.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Core\McpServer
	 */
	public McpServer $mcp_server;

	/**
	 * The initialize handler.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Initialize\InitializeHandler
	 */
	public InitializeHandler $initialize_handler;

	/**
	 * The tools handler.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Tools\ToolsHandler
	 */
	public ToolsHandler $tools_handler;

	/**
	 * The resources handler.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Resources\ResourcesHandler
	 */
	public ResourcesHandler $resources_handler;

	/**
	 * The prompts handler.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Prompts\PromptsHandler
	 */
	public PromptsHandler $prompts_handler;

	/**
	 * The system handler.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\System\SystemHandler
	 */
	public SystemHandler $system_handler;

	/**
	 * The observability handler instance.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\Observability\Contracts\McpObservabilityHandlerInterface
	 */
	public McpObservabilityHandlerInterface $observability_handler;

	/**
	 * The error handler instance.
	 *
	 * @var \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\ErrorHandling\Contracts\McpErrorHandlerInterface
	 */
	public McpErrorHandlerInterface $error_handler;

	/**
	 * The request router service.
	 */
	public RequestRouter $request_router;

	/**
	 * Optional custom permission callback for transport-level authentication.
	 *
	 * @var callable|callable-string|null
	 */
	public $transport_permission_callback;

	/**
	 * Initialize the transport context.
	 *
	 * @param array{
	 *   mcp_server: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Core\McpServer,
	 *   initialize_handler: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Initialize\InitializeHandler,
	 *   tools_handler: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Tools\ToolsHandler,
	 *   resources_handler: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Resources\ResourcesHandler,
	 *   prompts_handler: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\Prompts\PromptsHandler,
	 *   system_handler: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Handlers\System\SystemHandler,
	 *   observability_handler: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\Observability\Contracts\McpObservabilityHandlerInterface,
	 *   request_router?: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Transport\Infrastructure\RequestRouter,
	 *   transport_permission_callback?: callable|null,
	 *   error_handler?: \GoDaddy\WordPress\Plugins\MCPAdapterInitializer\Dependencies\WP\MCP\Infrastructure\ErrorHandling\Contracts\McpErrorHandlerInterface
	 * } $properties Properties to set on the context.
	 * Note: request_router is optional and will be auto-created if not provided.
	 */
	public function __construct( array $properties ) {
		foreach ( $properties as $name => $value ) {
			$this->$name = $value;
		}

		// If request_router is provided, we're done
		if ( isset( $properties['request_router'] ) ) {
			return;
		}

		// Create request_router if not provided
		$this->request_router = new RequestRouter( $this );
	}
}
