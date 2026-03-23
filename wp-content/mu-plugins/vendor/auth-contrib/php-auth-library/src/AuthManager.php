<?php

namespace GoDaddy\Auth;

class AuthManager implements AuthManagerInterface
{
    private $authClient;
    /** @var AuthKeyCacheInterface */
    private $keyCache;
    private $mapper;
    private $reauthReason = 1;

    public function __construct(AuthHttpClientInterface $authClient = null, AuthKeyCacheInterface $keyCache = null, string $appCode)
    {
        if ($authClient === null) {
            $authClient = new AuthHttpClient(null, $appCode);
        }
        if ($keyCache === null) {
            // Attempt to not share the cache with other application deployments.
            $keyCache = new AuthKeyFileCache(sys_get_temp_dir().'/godaddy-key-cache_'.md5(__FILE__), 60 * 60 * 24 * 40);
        }
        $this->authClient = $authClient;
        $this->keyCache   = $keyCache;
        $this->mapper     = new ObjectMapper();
    }

    public function getTokenFromCreds(string $host, string $user, string $password, string $realm): ?AuthToken
    {
        $authResult = $this->authClient->getJwt($host, ['username' => $user, 'password' => $password, 'realm' => $realm]);

        if ($authResult->getAuthException() !== null) {
            throw $authResult->getAuthException();
        }
        if (!strlen($authResult->getResult())) {
            return null;
        }
        $result = new AuthToken();
        $this->mapper->mapJsonToObject($authResult->getResult(), $result);

        if ($result->code < 0) {
            throw new AuthException($result->message, $result->code);
        } elseif ($result->code !== 1) {
            throw new AuthException(sprintf('Got invalid token'));
        }

        return $result;
    }

    public function getAuthLLSIDP(string $host, string $rawToken): ?AuthLLSIDP
    {
        $authResult = $this->getPayloadJson($host, $rawToken);
        if ($authResult->getAuthException() !== null) {
            throw $authResult->getAuthException();
        }
        if (!strlen($authResult->getResult())) {
            return null;
        }
        $result = new AuthLLSIDP();
        $this->mapper->mapJsonToObject($authResult->getResult(), $result);
        return $result;
    }

    public function getAuthPayloadSSLCert(string $host, string $rawToken): ?AuthPayloadSSLCert
    {
        $authResult = $this->getPayloadJson($host, $rawToken);
        if ($authResult->getAuthException() !== null) {
            throw $authResult->getAuthException();
        }
        if (!strlen($authResult->getResult())) {
            return null;
        }
        $result = new AuthPayloadSSLCert();
        $this->mapper->mapJsonToObject($authResult->getResult(), $result);
        if ($result->typ !== 'cert') {
            return null;
        }
        return $result;
    }

    public function getAuthPayloadShopper(string $host, string $rawToken, ?array $auths, int $level, bool $forcedHeartbeat=false): ?AuthPayloadShopper
    {
        $authResult = $this->getPayloadJson($host, $rawToken);
        if ($authResult->getAuthException() !== null) {
            throw $authResult->getAuthException();
        }
        if (!strlen($authResult->getResult())) {
            return null;
        }
        $decoded = json_decode($authResult->getResult(), true);
        if (empty($decoded['typ']) || $decoded['typ'] !== 'idp') {
            return null;
        }
        $auth = $decoded['auth'] ?? 'basic';
        switch ($auth) {
            case 's2s':
                $result = new AuthPayloadS2S();
                break;
            case 'e2s':
                $result = new AuthPayloadE2S();
                break;
            case 'e2s2s':
                $result = new AuthPayloadE2S2S();
                break;
            case 'cert2s':
                $result = new AuthPayloadCert2S();
                break;
            case 'basic':
                $result = new AuthPayloadShopperBasic();
                break;
            default:
                return null;
        }
        $this->mapper->mapDataToObject($decoded, $result);
        if (is_array($auths) && count($auths) && !in_array($auth, $auths)) {
            return null;
        }

        if($result->isExpired($level, $forcedHeartbeat, $reason)) {
            $this->reauthReason = $reason;
            return null;
        }

        return $result;
    }

    public function getAuthPayloadPass(string $host, string $rawToken, ?array $auths, int $level): ?AuthPayloadPass
    {
        $authResult = $this->getPayloadJson($host, $rawToken);
        if ($authResult->getAuthException() !== null) {
            throw $authResult->getAuthException();
        }
        if (!strlen($authResult->getResult())) {
            return null;
        }
        $decoded = json_decode($authResult->getResult(), true);
        if (empty($decoded['typ']) || $decoded['typ'] !== 'pass') {
            return null;
        }
        $auth = $decoded['auth'] ?? 'basic';
        switch ($auth) {
            case "s2p":
                $result = new AuthPayloadS2P();
                break;
            case "e2p":
                $result = new AuthPayloadE2P();
                break;
            case "basic":
                $result = new AuthPayloadPassBasic();
                break;
            default:
                return null;
        }
        $this->mapper->mapDataToObject($decoded, $result);
        if (count($auths) && !in_array($auth, $auths)) {
            return null;
        }
        if ($result->isExpired($level)) {
            return null;
        }
        return $result;
    }

    public function getAuthPayloadEmployee(string $host, string $rawToken, int $level): ?AuthPayloadEmployee
    {
        $authResult = $this->getPayloadJson($host, $rawToken);
        if ($authResult->getAuthException() !== null) {
            throw $authResult->getAuthException();
        }
        if (!strlen($authResult->getResult())) {
            return null;
        }
        $decoded = json_decode($authResult->getResult(), true);
        if (empty($decoded['typ']) || $decoded['typ'] !== 'jomax') {
            return null;
        }
        $result = new AuthPayloadEmployee();
        $this->mapper->mapDataToObject($decoded, $result);
        if ($result->isExpired($level)) {
            return null;
        }
        return $result;
    }

    public function getReauthReason(): int
    {
        return $this->reauthReason;
    }

    /**
     * URL-safe base64-encoded strings translate + and / and - to _ respectively, this function reverses that
     * transformation and decodes the payload.
     *
     * @param string $data
     *
     * @return string Decoded binary data.
     *
     * @throws \Exception When the data passed contains invalid characters.
     */
    private static function base64DecodeUrl(string $data): string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);
        if ($decoded === false) {
            throw new \Exception('Got invalid base64-url-encoded string');
        }
        return $decoded;
    }

    /**
     * @param string $host
     * @param string $rawToken
     *
     * @return AuthResult
     */
    private function getPayloadJson(string $host, string $rawToken): AuthResult
    {
        try {
            $tokenParts = explode('.', $rawToken);
            if (count($tokenParts) !== 3) {
                return new AuthResult('', null);
            }

            // Get the header and parse it into the data structure
            $headerJson = self::base64DecodeUrl($tokenParts[0]);
            $authHeader = new AuthHeader();
            $this->mapper->mapJsonToObject($headerJson, $authHeader);

            switch ($authHeader->alg) {
                case 'RS256':
                    $alg = OPENSSL_ALGO_SHA256;
                    break;
                case 'RS384':
                    $alg = OPENSSL_ALGO_SHA384;
                    break;
                case 'RS512':
                    $alg = OPENSSL_ALGO_SHA512;
                    break;
                default:
                    throw new AuthException(sprintf('Unsupported algorithm: "%s"', $authHeader->alg));
            }

            if (!strlen($authHeader->kid)) {
                throw new AuthException('Key ID not set in JWT header');
            }

            // Get the certificate to validate the signature
            $key = $this->keyCache->get($authHeader->kid);
            if ($key === null) {
                $key = $this->fetchKey($host, $authHeader->kid);
                $this->keyCache->set($authHeader->kid, $key);
            }

            $signature = self::base64DecodeUrl($tokenParts[2]);
            $verify    = openssl_verify($tokenParts[0].'.'.$tokenParts[1], $signature, $key, $alg);
            if ($verify === 0) {
                throw new AuthException('Key verification failed');
            } elseif ($verify === 1) {
                // continue
            } else {
                $error = null;
                while (strlen($line = openssl_error_string())) {
                    $error .= $line;
                }
                throw new AuthException(sprintf("openSSL verify error:\n%s", $error ?? 'unknown error'));
            }
            return new AuthResult(self::base64DecodeUrl($tokenParts[1]));
        } catch (AuthException $e) {
            return new AuthResult(null, $e);
        } catch (\Exception $e) {
            return new AuthResult(null, new AuthException('Authentication failed', 0, $e));
        }
    }

    private function fetchKey(string $host, $kid): string
    {
        $keyResult = $this->authClient->getPublicKey($host, $kid);
        if ($keyResult->getAuthException() !== null) {
            throw $keyResult->getAuthException();
        }
        if (!strlen($keyResult->getResult())) {
            // Double-check key because it's stored in cache and shared.
            throw new AuthException('Got an empty key with ID: %s', $kid);
        }
        $authKey = new AuthKey();
        $this->mapper->mapJsonToObject($keyResult->getResult(), $authKey);
        if ($authKey->code < 0) {
            throw new AuthException($authKey->message, $authKey->code);
        }
        if ($authKey->code !== 1 || empty($authKey->data) || !strlen($authKey->data->n) || !strlen($authKey->data->e)) {
            throw new AuthException('Got invalid public key data');
        }
        return PemEncoder::publicKeyToPKCS8(self::base64DecodeUrl($authKey->data->n), self::base64DecodeUrl($authKey->data->e));
    }
}
