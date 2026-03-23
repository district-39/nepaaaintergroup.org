<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthPayloadE2S;
use GoDaddy\Auth\AuthPayloadE2S2S;
use GoDaddy\Auth\AuthPayloadS2S;
use GoDaddy\Auth\AuthPayloadShopper;
use GoDaddy\Auth\AuthPayloadShopperBasic;
use GoDaddy\Auth\ObjectMapper;
use Mockery;

class GetAuthPayloadShopperTest extends AuthTestCase
{
    public function testMalformedToken()
    {
        $token = $this->getPayload(self::MALFORMED_TOKEN);
        $this->assertNull($token);
    }

    public function testValidBasicToken()
    {
        $token = $this->getPayload(self::VALID_BASIC_SHOPPER_TOKEN);
        // Cast $token of type AuthPayloadShopper to AuthPayloadShopperBasic.
        (new ObjectMapper)->mapDataToObject(get_object_vars($token), $token = new AuthPayloadShopperBasic());

        $this->assertBasicTokenValid($token);
    }

    public function testValidE2SToken()
    {
        $token = $this->getPayload(self::VALID_E2S_SHOPPER_TOKEN);
        // Cast $token of type AuthPayloadShopper to AuthPayloadE2S.
        (new ObjectMapper)->mapDataToObject(get_object_vars($token), $token = new AuthPayloadE2S());
        $this->assertE2STokenValid($token);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testValidS2SToken()
    {
        $stub = Mockery::mock('overload:GoDaddy\Auth\AuthResult');;
        $time = time();
        $stub->shouldReceive('getAuthException')->andReturns(null);
        $stub->shouldReceive('getResult')->andReturns(
        <<<EOT
            {
                "auth": "s2s",
                "del": {
                    "auth": "basic",
                    "factors": {
                    "k_pw": 1486685838
                    },
                    "firstname": "Tony",
                    "ftc": 1,
                    "hbi": 1486685838,
                    "vat": {$time},
                    "iat": {$time},
                    "jti": "SS-2JhJd22U4ObTgOHkuMw",
                    "lastname": "Fowler",
                    "plid": "1",
                    "shopperId": "1023218",
                    "typ": "idp",
                    "username": "username123afowler"
                },
                "vat": {$time},
                "iat": {$time},
                "jti": "tGnSJHlYWoCLDi2MrYlIWA",
                "s2s": {
                    "al": 3,
                    "disp": "Robert40 Chen",
                    "firstname": "Jordan",
                    "ftc": 0,
                    "lastname": "Webpro",
                    "plid": "1",
                    "shopperId": "308172",
                    "username": "robert.chen+40"
                },
                "typ": "idp"
            }
        EOT
        );

        $token = $this->getPayload(self::VALID_S2S_SHOPPER_TOKEN);

        // Cast $token of type AuthPayloadShopper to AuthPayloadS2S.
        (new ObjectMapper())->mapDataToObject(get_object_vars($token), $token = new AuthPayloadS2S());
        $this->assertS2STokenValid($token);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testInvalidS2SToken()
    {
        $stub = Mockery::mock('overload:GoDaddy\Auth\AuthResult');;
        $expiredTime = time() - (6 * 60); // Should expire after 5 mins.
        $stub->shouldReceive('getAuthException')->andReturns(null);
        $stub->shouldReceive('getResult')->andReturns(
            <<<EOT
            {
                "auth": "s2s",
                "del": {
                    "auth": "basic",
                    "factors": {
                    "k_pw": 1486685838
                    },
                    "firstname": "Tony",
                    "ftc": 1,
                    "hbi": 1486685838,
                    "vat": {$expiredTime},
                    "iat": {$expiredTime},
                    "jti": "SS-2JhJd22U4ObTgOHkuMw",
                    "lastname": "Fowler",
                    "plid": "1",
                    "shopperId": "1023218",
                    "typ": "idp",
                    "username": "username123afowler"
                },
                "vat": {$expiredTime},
                "iat": {$expiredTime},
                "jti": "tGnSJHlYWoCLDi2MrYlIWA",
                "s2s": {
                    "al": 3,
                    "disp": "Robert40 Chen",
                    "firstname": "Jordan",
                    "ftc": 0,
                    "lastname": "Webpro",
                    "plid": "1",
                    "shopperId": "308172",
                    "username": "robert.chen+40"
                },
                "typ": "idp"
            }
        EOT
        );

        // It's a valid s2s token with an invalid.
        $token = $this->getPayload(self::VALID_S2S_SHOPPER_TOKEN);
        $this->assertNull($token);
        $this->assertEquals(3, self::$authManager->getReauthReason());
    }

    public function testValidE2S2SToken()
    {
        $token = $this->getPayload(self::VALID_E2S2S_SHOPPER_TOKEN);
        // Cast $token of type AuthPayloadShopper to AuthPayloadE2S2S.
        (new ObjectMapper())->mapDataToObject(get_object_vars($token), $token = new AuthPayloadE2S2S());
        $this->assertE2S2STokenValid($token);
    }

    public function testValidCert2SToken()
    {
        $token = self::$authManager->getAuthPayloadShopper(self::SSO_DEVELOPMENT_HOSTNAME, self::VALID_CERT2S_TOKEN, null, self::EXPIRY_IMPACT_LEVEL_NONE);
        $this->assertCert2STokenValid($token);
    }

    public function testForcedHeartbeat()
    {
        $token = self::$authManager->getAuthPayloadShopper(self::SSO_TEST_HOSTNAME, self::VALID_BASIC_SHOPPER_TOKEN, null, self::EXPIRY_IMPACT_LEVEL_NONE, true);
        $this->assertNull($token);
    }

    public function testReAuthReasonDefault()
    {
        $token = self::$authManager->getAuthPayloadShopper(self::SSO_TEST_HOSTNAME, self::VALID_BASIC_SHOPPER_TOKEN, null, self::EXPIRY_IMPACT_LEVEL_LOW);
        $this->assertNull($token);
        $this->assertEquals(1, self::$authManager->getReauthReason());
    }

    public function testReAuthReasonHBI()
    {
        $token = self::$authManager->getAuthPayloadShopper(self::SSO_TEST_HOSTNAME, self::VALID_BASIC_SHOPPER_TOKEN, null, self::EXPIRY_IMPACT_LEVEL_HIGH);
        $this->assertNull($token);
        $this->assertEquals(2, self::$authManager->getReauthReason());
    }

    public function testReAuthReasonHeartBeat()
    {
        $token = self::$authManager->getAuthPayloadShopper(self::SSO_TEST_HOSTNAME, self::VALID_BASIC_SHOPPER_TOKEN, null, self::EXPIRY_IMPACT_LEVEL_NONE, true);
        $this->assertNull($token);
        $this->assertEquals(3, self::$authManager->getReauthReason());
    }


    /**
     * @expectedException \GoDaddy\Auth\AuthException
     * @expectedExceptionMessage Failed to find key badid
     */
    public function testInvalidKeyToken()
    {
        $this->expectException(\GoDaddy\Auth\AuthException::class);
        $this->expectExceptionMessage('Failed to find key badid');
        $this->getPayload(self::INVALID_KEY_TOKEN);
    }

    private function getPayload(string $token): ?AuthPayloadShopper
    {
        return self::$authManager->getAuthPayloadShopper(self::SSO_TEST_HOSTNAME, $token, null, self::EXPIRY_IMPACT_LEVEL_NONE);
    }
}
