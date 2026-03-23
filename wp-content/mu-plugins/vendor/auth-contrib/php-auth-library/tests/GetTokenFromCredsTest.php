<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthException;
use GoDaddy\Auth\AuthPayloadShopper;
use GoDaddy\Auth\AuthPayloadShopperBasic;
use GoDaddy\Auth\ObjectMapper;

class GetTokenFromCredsTest extends AuthTestCase
{
    private const VALID_KEY = "TVejhA5B_3Dw7PKxT9zzVArqEXqdUkE";
    private const VALID_SECRET = "4y8GXaJPd6kHnyimuFPct2";
    private const API_KEY_REALM = "apikey";

    private const INVALID_KEY = "invalid";
    private const INVALID_SECRET = "invalid";

    public function testValidCredentialsShouldReturnValidJwt()
    {
        $retrievedJwt = self::$authManager->getTokenFromCreds(self::SSO_TEST_HOSTNAME, self::VALID_KEY, self::VALID_SECRET, self::API_KEY_REALM);
        $this->assertNotNull($retrievedJwt);
        $this->assertNotEmpty($retrievedJwt->data);
        $token = $this->getPayload($retrievedJwt->data);
        // Cast $token of type AuthPayloadShopper to AuthPayloadShopperBasic.
        (new ObjectMapper)->mapDataToObject(get_object_vars($token), $token = new AuthPayloadShopperBasic());

        $payload = $token;
        $this->assertNotNull($payload);
        $shopper = $payload->getShopper();
        $this->assertNotNull($shopper);
        $this->assertEquals("basic", $payload->auth);
        $this->assertEquals("idp", $payload->typ);
        $this->assertEquals("541426", $payload->shopperId);
    }

    /**
     * @expectedException \GoDaddy\Auth\AuthException
     * @expectedExceptionMessage Invalid shopper
     */
    public function testInvalidCredentialsShouldThrowException()
    {
        $this->expectException(\GoDaddy\Auth\AuthException::class);
        $this->expectExceptionMessage('Invalid shopper');
        self::$authManager->getTokenFromCreds(self::SSO_TEST_HOSTNAME, self::INVALID_KEY, self::INVALID_SECRET, self::API_KEY_REALM);
    }

    private function getPayload(string $token): ?AuthPayloadShopper
    {
        return self::$authManager->getAuthPayloadShopper(self::SSO_TEST_HOSTNAME, $token, null, self::EXPIRY_IMPACT_LEVEL_NONE);
    }
}