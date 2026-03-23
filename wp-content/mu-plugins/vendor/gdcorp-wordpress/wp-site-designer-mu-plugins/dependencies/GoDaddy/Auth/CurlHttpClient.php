<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class CurlHttpClient implements HttpClientInterface
{
    private const MAX_RETRIES = 3;
    private $agent = 'PHP Curl';

    public function get(string $url): string
    {
        return $this->send($url);
    }

    public function post(string $url, array $data): string
    {
        return $this->send($url, $data);
    }

    public function setAgent(string $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Sends a GET or POST request (depending on presence of $postData) and returns a response.
     *
     * @param string     $url
     * @param array|null $postData Array to POST, null to GET.
     *
     * @return string
     * @throws \Exception
     */
    private function send($url, ?array $postData = null): string
    {
        if (!function_exists('curl_init') || !function_exists('curl_exec')) {
            throw new \RuntimeException('The PHP curl extension must be enabled');
        }
        $retries = 0;
        again:
        error_clear_last();
        $ch = @curl_init($url);
        if ($ch === false) {
            $error = error_get_last();
            throw new \Exception(sprintf('curl init error: %s', $error['message'] ?? 'unknown error'));
        }
        if ($postData !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));
        }
        curl_setopt_array($ch, [
            CURLOPT_USERAGENT => $this->agent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_PROTOCOLS      => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
        ]);
        error_clear_last();
        $result    = @curl_exec($ch);
        $errorCode = @curl_errno($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (($httpCode >= 500 || $errorCode > 0) && $retries < self::MAX_RETRIES) {
            $retries++;
            usleep(100000 * pow($retries, 2));
            goto again;
        }
        if ($result === false) {
            $curlError = curl_error($ch) ?: 'unknown error';
            throw new \Exception(sprintf('curl exec error: %s', $curlError));
        }
        if ($httpCode >= 500 || $httpCode < 200) {
            throw new \Exception(sprintf('unexpected response: %s', $result));
        }
        return $result;
    }
}
