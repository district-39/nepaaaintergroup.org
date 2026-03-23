<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Poynt\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\Http\Request as CommonRequest;
use GoDaddy\WordPress\MWC\Common\Http\Response;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Core\Payments\Poynt\Gateways\AccessTokenGateway;

/**
 * Poynt API base request class.
 *
 * @since 2.10.0
 */
class Request extends CommonRequest
{
    /** @var string request method */
    public $method = 'GET';

    /** @var int default timeout in seconds */
    public $timeout = 10;

    /** @var string request route */
    protected $route = '';

    /** @var string|null The Poynt-Request-Id header value */
    protected ?string $poyntRequestId = null;

    /**
     * Request constructor.
     *
     * @since 2.10.0
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->setUserAgentHeader()
            ->setFullUrl()
            ->sslVerify()
            ->timeout();
    }

    public function getTimeout() : int
    {
        return TypeHelper::int(Configuration::get('payments.poynt.api.timeout'), $this->timeout);
    }

    /**
     * Sends the request.
     *
     * @return Response
     * @throws Exception
     */
    public function send() : Response
    {
        $this->setNewAccessToken()
            ->setAuthorizationHeader()
            ->ensurePoyntRequestIdHeader();

        /** @var Response $response */
        $response = parent::send();

        return $response;
    }

    /**
     * Ensures the Poynt-Request-Id header is set before sending.
     *
     * Generates a UUID if one hasn't been set via setPoyntRequestIdHeader().
     *
     * @return self
     */
    protected function ensurePoyntRequestIdHeader() : Request
    {
        if (! $this->poyntRequestId) {
            $this->poyntRequestId = wp_generate_uuid4();
        }

        $this->headers['Poynt-Request-Id'] = $this->poyntRequestId;

        return $this;
    }

    /**
     * Sets a new access token for the request.
     *
     * @return Request
     * @throws Exception
     */
    protected function setNewAccessToken() : Request
    {
        $accessToken = AccessTokenGateway::getNewInstance()->generateToken();

        Configuration::set('payments.poynt.api.token', $accessToken);

        update_option('mwc_payments_poynt_api_token', $accessToken);

        return $this;
    }

    /**
     * Sets the Authorization bearer request header.
     *
     * @since 2.10.0
     *
     * @return self
     *
     * @throws Exception
     */
    protected function setAuthorizationHeader() : Request
    {
        if ($bearerToken = Configuration::get('payments.poynt.api.token')) {
            $this->headers = ArrayHelper::combine($this->headers, ['Authorization' => "Bearer {$bearerToken}"]);
        }

        return $this;
    }

    /**
     * Sets the user agent request header.
     *
     * @since 2.10.0
     *
     * @return self
     *
     * @throws Exception
     */
    protected function setUserAgentHeader() : Request
    {
        $this->headers = ArrayHelper::combine((array) $this->headers, [
            'Accept'       => 'application/json',
            'Api-Version'  => '1.2',
            'Content-Type' => 'application/json',
            'User-Agent'   => Configuration::get('payments.poynt.api.userAgent'),
        ]);

        return $this;
    }

    /**
     * Sets the Poynt request ID for this request.
     *
     * The ID will be used as the Poynt-Request-Id header when send() is called.
     *
     * @param string $value
     * @return $this
     */
    public function setPoyntRequestId(string $value) : Request
    {
        $this->poyntRequestId = $value;
        $this->headers['Poynt-Request-Id'] = $value;

        return $this;
    }

    /**
     * Sets the request full URL.
     *
     * @since 2.10.0
     *
     * @return self
     *
     * @throws Exception
     */
    protected function setFullUrl() : Request
    {
        $this->url = StringHelper::endWith($this->getRootUrl(), '/').$this->route;

        return $this;
    }

    /**
     * Gets the API root URL, depending on environment.
     *
     * @return string
     * @throws Exception
     */
    public function getRootUrl() : string
    {
        return (string) ManagedWooCommerceRepository::isProductionEnvironment() ? Configuration::get('payments.poynt.api.productionRoot', '') : Configuration::get('payments.poynt.api.stagingRoot', '');
    }
}
