<?php

namespace GoDaddy\WordPress\MWC\Core\Features\Commerce\WebVitals\Interceptors\Handlers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Components\Contracts\DelayedInstantiationComponentContract;
use GoDaddy\WordPress\MWC\Common\Exceptions\SentryException;
use GoDaddy\WordPress\MWC\Common\Interceptors\AbstractInterceptor;
use GoDaddy\WordPress\MWC\Common\Register\Contracts\RegistrableContract;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\Traits\CanDetermineWhetherIsStagingSiteTrait;

class RenderWebVitalsInlineScriptInterceptor extends AbstractInterceptor implements DelayedInstantiationComponentContract
{
    use CanDetermineWhetherIsStagingSiteTrait;

    /**
     * Configures the component to be instantiated when the wp action is triggered.
     *
     * The component will only be instantiated if the current request meets the conditions
     * defined in {@see shouldRenderInlineScript}.
     */
    public static function scheduleInstantiation(callable $callback) : void
    {
        static::executeRegistrableCatchingExceptions(
            Register::action()
                ->setGroup('wp_loaded')
                ->setCondition([static::class, 'shouldRenderInlineScript'])
                ->setHandler($callback)
        );
    }

    /**
     * Executes the given {@see RegistrableContract} instance catching any exceptions thrown in the process.
     *
     * Any exception caught will be reported to Sentry.
     */
    protected static function executeRegistrableCatchingExceptions(RegistrableContract $registrable) : void
    {
        try {
            $registrable->execute();
        } catch (Exception $exception) {
            SentryException::getNewInstance("An error occurred trying to register a hook handler: {$exception->getMessage()}", $exception);
        }
    }

    /**
     * Determines whether we should instantiate the interceptor that renders the Web Vitals inline script.
     *
     * Among other scenarios, we want to avoid instantiating the interceptor on Ajax or REST API requests.
     *
     * Based on https://github.com/gdcorp-wordpress/wp-paas-system-plugin/blob/2723bce0ea8caf7a1c96975b20b2005c7ab2f4a8/gd-system-plugin/includes/class-rum.php#L175-L190
     */
    public static function shouldRenderInlineScript() : bool
    {
        return ! WordPressRepository::isAjax()
            && ! WordPressRepository::isApiRequest()
            && ! static::isAmpEndpoint()
            && ! static::isStagingSite()
            && ! static::isDebugEnabled();
    }

    protected static function isAmpEndpoint() : bool
    {
        return function_exists('is_amp_endpoint') && is_amp_endpoint();
    }

    protected static function isDebugEnabled() : bool
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }

    public function addHooks() : void
    {
        static::executeRegistrableCatchingExceptions(
            Register::action()
                ->setGroup('wp_footer')
                ->setHandler([RenderWebVitalsInlineScriptHandler::class, 'handle'])
                ->setPriority(PHP_INT_MAX)
        );

        static::executeRegistrableCatchingExceptions(
            Register::action()
                ->setGroup('admin_footer')
                ->setHandler([RenderWebVitalsInlineScriptHandler::class, 'handle'])
                ->setPriority(PHP_INT_MAX)
        );
    }
}
