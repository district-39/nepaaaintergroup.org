<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class AuthHttpClient implements AuthHttpClientInterface
{
    private const PACKAGE_NAME = 'auth-contrib/php-auth-library';
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient = null, string $appCode)
    {
        if ($httpClient === null) {
            $httpClient = new CurlHttpClient();
        }
        $this->httpClient = $httpClient;
        $this->httpClient->setAgent($this->buildAgent($appCode));
    }

    public function getJwt(string $host, array $parameters = []): AuthResult
    {
        try {
            $response = $this->httpClient->post('https://'.$host.self::TOKEN_SERVICE_URI, $parameters);
            return new AuthResult($response);
        } catch (\Exception $e) {
            return new AuthResult(null, new AuthException($e));
        }
    }

    public function getPublicKey(string $host, string $publicKeyId): AuthResult
    {
        try {
            $response = $this->httpClient->get('https://'.$host.self::KEY_SERVICE_URI.$publicKeyId);
            return new AuthResult($response);
        } catch (\Exception $e) {
            return new AuthResult(null, new AuthException($e));
        }
    }

    public function setAppCode(string $appCode)
    {
        $this->appCode = $appCode;
    }

    private function buildAgent(string $appCode): string
    {
        $libVersion = $this->getLibraryVersion();

        return 'PHP/' . phpversion() . ' PhpAuthLibrary/' . $libVersion . ' App/' . $appCode;
    }

    private function getLibraryVersion(): string
    {
        $libVersion = 'Unknown';

        $packageDef = 'vendor/composer/installed.json';
        if(file_exists($packageDef))
        {
            $packageDef = file_get_contents($packageDef);
            $packages = json_decode($packageDef);
            if (is_object($packages) && isset($packages->packages) && is_array($packages->packages))
            {
                $packages = $packages->packages;
            }
            foreach($packages as $package)
            {
                if($package->name == self::PACKAGE_NAME)
                {
                    $libVersion = $package->version;
                }
            } 
        }

        return $libVersion;
    }
}
