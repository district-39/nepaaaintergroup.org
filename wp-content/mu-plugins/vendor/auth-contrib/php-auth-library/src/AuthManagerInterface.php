<?php

namespace GoDaddy\Auth;

interface AuthManagerInterface
{
    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $realm
     *
     * @return AuthToken|null
     */
    public function getTokenFromCreds(string $host, string $user, string $password, string $realm): ?AuthToken;

    /**
     * @param string $host
     * @param string $rawToken
     *
     * @return AuthLLSIDP|null
     */
    public function getAuthLLSIDP(string $host, string $rawToken): ?AuthLLSIDP;

    /**
     * @param string $host
     * @param string $rawToken
     *
     * @return AuthPayloadSSLCert|null
     */
    public function getAuthPayloadSSLCert(string $host, string $rawToken): ?AuthPayloadSSLCert;

    /**
     * @param string   $host
     * @param string   $rawToken
     * @param string[] $auths
     * @param int      $level
     *
     * @return AuthPayloadShopper|null
     */
    public function getAuthPayloadShopper(string $host, string $rawToken, ?array $auths, int $level): ?AuthPayloadShopper;

    /**
     * @param string   $host
     * @param string   $rawToken
     * @param string[] $auths
     * @param int      $level
     *
     * @return AuthPayloadPass|null
     */
    public function getAuthPayloadPass(string $host, string $rawToken, ?array $auths, int $level): ?AuthPayloadPass;

    /**
     * @param string $host
     * @param string $rawToken
     * @param int    $level
     *
     * @return AuthPayloadEmployee|null
     */
    public function getAuthPayloadEmployee(string $host, string $rawToken, int $level): ?AuthPayloadEmployee;
}
