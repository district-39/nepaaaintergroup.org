<?php

namespace GoDaddy\Auth;

interface AuthHttpClientInterface
{
    public const TOKEN_SERVICE_URI = '/v1/api/token';
    public const KEY_SERVICE_URI = '/v1/api/key/';

    /**
     * Gets the Json Web Token via a post request to the token service.
     *
     * Be sure to use the TOKEN_SERVICE_URI constant as your URI for the post request.
     * https://confluence.godaddy.com/display/AUTH/API
     *
     * @param string $host       Host with optional port, must not contain protocol nor path.
     * @param array  $parameters POST parameters to pass.
     *
     * @return AuthResult
     */
    public function getJwt(string $host, array $parameters = []): AuthResult;

    /**
     * Gets the public key via a get request to the key service.
     *
     * Be sure to use the KEY_SERVICE_URI + publicKeyId as your URI for the get request.
     * https://confluence.godaddy.com/display/AUTH/API
     *
     * @param string $host Host with optional port, must not contain protocol nor path.
     * @param string $publicKeyId
     *
     * @return AuthResult
     */
    public function getPublicKey(string $host, string $publicKeyId): AuthResult;
}
