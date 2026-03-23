<?php

namespace GoDaddy\Auth;

class AuthResult
{
    private $result;
    private $authException;

    /**
     * AuthResult constructor.
     *
     * @param string|null        $result
     * @param AuthException|null $authException
     */
    public function __construct(string $result = null, AuthException $authException = null)
    {
        if ($result === null && $authException === null) {
            $authException = new AuthException('empty result');
        }
        $this->result        = $result;
        $this->authException = $authException;
    }

    /**
     * @return string|null
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * @return AuthException|null
     */
    public function getAuthException(): ?AuthException
    {
        return $this->authException;
    }
}
