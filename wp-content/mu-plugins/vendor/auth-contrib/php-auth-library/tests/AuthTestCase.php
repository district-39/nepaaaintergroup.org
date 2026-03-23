<?php

namespace GoDaddy\Auth\Tests;

use GoDaddy\Auth\AuthKeyFileCache;
use GoDaddy\Auth\AuthManager;
use GoDaddy\Auth\AuthPayloadE2S;
use GoDaddy\Auth\AuthPayloadE2S2S;
use GoDaddy\Auth\AuthPayloadS2S;
use GoDaddy\Auth\AuthPayloadShopper;
use GoDaddy\Auth\AuthPayloadShopperBasic;
use PHPUnit\Framework\TestCase;

class AuthTestCase extends TestCase
{
    /** @var AuthManager */
    protected static $authManager;

    protected const SSO_TEST_HOSTNAME = "sso.test-godaddy.com";
    protected const SSO_DEVELOPMENT_HOSTNAME = "sso.dev-godaddy.com";

    protected const EXPIRY_IMPACT_LEVEL_NONE = 0;
    protected const EXPIRY_IMPACT_LEVEL_LOW = 1;
    protected const EXPIRY_IMPACT_LEVEL_MEDIUM = 2;
    protected const EXPIRY_IMPACT_LEVEL_HIGH = 3;

    protected const MALFORMED_TOKEN = "malformed";
    protected const VALID_JOMAX_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIllYZFg1eVE4OHcifQ.eyJhY2NvdW50TmFtZSI6ICJvdmVybGR0ZXN0cmVwNSIsICJhdXRoIjogImJhc2ljIiwgImZhY3RvcnMiOiB7ImtfcHciOiAxNDg2Njc4MjYxfSwgImZpcnN0bmFtZSI6ICJPdmVybG9yZCIsICJmdGMiOiAxLCAiZ3JvdXBzIjogW10sICJpYXQiOiAxNDg2Njc4MjYxLCAianRpIjogIlJTOGNUbWhJOWJKUjV4emNydjlQcFEiLCAibGFzdG5hbWUiOiAiVGVzdFJlcDUiLCAidHlwIjogImpvbWF4In0.REdDSrajUO9HPqJmwxPa4wuX1xDQz6lmr1RovZtB5-Ita0l-xXvGU29ATNmraz68j2aNK0axoi-de79OHE-qysl1dYLDVnbCpaTeqMZGF7pEBd03n9gslh1Y7DcRbjZ1ZPSgN8L37ip9Rd1HcQOb5iG0XMcrsdn12ojsgzcx2ZQ";
    protected const VALID_BASIC_SHOPPER_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIllYZFg1eVE4OHcifQ.eyJhdXRoIjogImJhc2ljIiwgImZhY3RvcnMiOiB7ImtfcHciOiAxNDg2Njc4MjEzfSwgImZpcnN0bmFtZSI6ICJmYWtlIiwgImZ0YyI6IDEsICJoYmkiOiAxNDg2Njc4MjEzLCAiaWF0IjogMTQ4NjY3ODIxMywgImp0aSI6ICJjZk9QdlBLakdfRHJFUk9yR3p6WGNnIiwgImxhc3RuYW1lIjogIm5hbWUiLCAicGxpZCI6ICIxIiwgInNob3BwZXJJZCI6ICI3MjE1NTIiLCAidHlwIjogImlkcCIsICJ1c2VybmFtZSI6ICJjcm14LWludGVncmF0aW9uLXRlc3QifQ.Y9AbkGl2Prnz9DdUkN4ukhOjY22NBDjw0zy9sQNmQEXGptn53PMe61uEUWR33QogTFLN2VJTWA5tCNC0pCjnlrwMJ7-5XUI_PvmonGRATn2MRPq4e63505zD5RNGhwpBDoaW95N0IGeZi-gpg5V-oOqHSQHiclW60M380AP2brQ";
    protected const VALID_E2S2S_SHOPPER_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIllYZFg1eVE4OHcifQ.eyJhdXRoIjogImUyczJzIiwgImRlbCI6IHsiYXV0aCI6ICJlMnMiLCAiZGVsIjogeyJhY2NvdW50TmFtZSI6ICJqeGF5YWxhIiwgImF1dGgiOiAiYmFzaWMiLCAiZmFjdG9ycyI6IHsia19wdyI6IDE0ODY2ODYyNjl9LCAiZmlyc3RuYW1lIjogIkpha2UiLCAiZnRjIjogMSwgImdyb3VwcyI6IFsiRGV2LURvbWFpbnMgUGxhdGZvcm0iLCAiRGV2ZWxvcG1lbnQiXSwgImlhdCI6IDE0ODY2ODYyNjksICJqdGkiOiAiZE1KUV9ZcGFmODJzczZSUEkxeDhYdyIsICJsYXN0bmFtZSI6ICJBeWFsYSIsICJ0eXAiOiAiam9tYXgifSwgImUycyI6IHsiZmlyc3RuYW1lIjogIlRvbnkiLCAiZnRjIjogMCwgImxhc3RuYW1lIjogIkZvd2xlciIsICJwbGlkIjogIjEiLCAic2hvcHBlcklkIjogIjEwMjMyMTgiLCAidXNlcm5hbWUiOiAidXNlcm5hbWUxMjNhZm93bGVyIn0sICJpYXQiOiAxNDg2Njg2MzExLCAianRpIjogInNWalNuYnZibXJDOF8zTmptRVVKcmciLCAidHlwIjogImlkcCJ9LCAiZTJzMnMiOiB7ImFsIjogMywgImRpc3AiOiAiUm9iZXJ0NDAgQ2hlbiIsICJmaXJzdG5hbWUiOiAiSm9yZGFuIiwgImZ0YyI6IDAsICJsYXN0bmFtZSI6ICJXZWJwcm8iLCAicGxpZCI6ICIxIiwgInNob3BwZXJJZCI6ICIzMDgxNzIiLCAidXNlcm5hbWUiOiAicm9iZXJ0LmNoZW4rNDAifSwgImlhdCI6IDE0ODY2ODYzOTcsICJqdGkiOiAiaW9BWFI4RE9pcXBQc2UydVFtV0VjUSIsICJ0eXAiOiAiaWRwIn0.PtbkU18-Caf96BRxTmIJJvfYh1fS3DrRxSwAw-c65OkHL6RdoERpezUn-EMYkrtPXF9yXll9IpcOs_tjQZK8VpJ5y2EbB9ru0_iXKWi-vGIy7RdEjntQbrHBO2VWstWHCnhhvO2X8reswq8eJsOa3lWyMSG8us_9uTO37xutvlA";
    protected const VALID_E2S_SHOPPER_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIllYZFg1eVE4OHcifQ.eyJhdXRoIjogImUycyIsICJkZWwiOiB7ImFjY291bnROYW1lIjogIm92ZXJsZHRlc3RyZXA1IiwgImF1dGgiOiAiYmFzaWMiLCAiZmFjdG9ycyI6IHsia19wdyI6IDE0ODY2Nzc4MDB9LCAiZmlyc3RuYW1lIjogIk92ZXJsb3JkIiwgImZ0YyI6IDEsICJncm91cHMiOiBbXSwgImlhdCI6IDE0ODY2Nzc4MDAsICJqdGkiOiAidkZ0enlVcVBOXzhLZW44NkdDdnVWQSIsICJsYXN0bmFtZSI6ICJUZXN0UmVwNSIsICJ0eXAiOiAiam9tYXgifSwgImUycyI6IHsiZmlyc3RuYW1lIjogImZha2UiLCAiZnRjIjogMCwgImxhc3RuYW1lIjogIm5hbWUiLCAicGxpZCI6ICIxIiwgInNob3BwZXJJZCI6ICI3MjE1NTIiLCAidXNlcm5hbWUiOiAiY3JteC1pbnRlZ3JhdGlvbi10ZXN0In0sICJpYXQiOiAxNDg2Njc3ODAxLCAianRpIjogInI5c01VYXRZZ2JMY1BKWmVSbUdlenciLCAidHlwIjogImlkcCJ9.S2jA0BuM1mV9sj7wJcAJsLZxoKmuU6hihAoaAKTIW3sfvskzwEKew3JvNzF63XoCkltrrpf5Q5G02kPIbgFrYNkiZanw8O5bV_OcjcmpbvOKqj1Ku-M_iohYDH3BIVvVG2U1qIWvKtzFIZOyCnTxIIW8YyGgAPfPkm4aEg2Zt38";
    protected const VALID_S2S_SHOPPER_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIllYZFg1eVE4OHcifQ.eyJhdXRoIjogInMycyIsICJkZWwiOiB7ImF1dGgiOiAiYmFzaWMiLCAiZmFjdG9ycyI6IHsia19wdyI6IDE0ODY2ODU4Mzh9LCAiZmlyc3RuYW1lIjogIlRvbnkiLCAiZnRjIjogMSwgImhiaSI6IDE0ODY2ODU4MzgsICJpYXQiOiAxNDg2Njg1ODM4LCAianRpIjogIlNTLTJKaEpkMjJVNE9iVGdPSGt1TXciLCAibGFzdG5hbWUiOiAiRm93bGVyIiwgInBsaWQiOiAiMSIsICJzaG9wcGVySWQiOiAiMTAyMzIxOCIsICJ0eXAiOiAiaWRwIiwgInVzZXJuYW1lIjogInVzZXJuYW1lMTIzYWZvd2xlciJ9LCAiaWF0IjogMTQ4NjY4NTg3MywgImp0aSI6ICJ0R25TSkhsWVdvQ0xEaTJNcllsSVdBIiwgInMycyI6IHsiYWwiOiAzLCAiZGlzcCI6ICJSb2JlcnQ0MCBDaGVuIiwgImZpcnN0bmFtZSI6ICJKb3JkYW4iLCAiZnRjIjogMCwgImxhc3RuYW1lIjogIldlYnBybyIsICJwbGlkIjogIjEiLCAic2hvcHBlcklkIjogIjMwODE3MiIsICJ1c2VybmFtZSI6ICJyb2JlcnQuY2hlbis0MCJ9LCAidHlwIjogImlkcCJ9.B6yk4Fux5hlZ-ZH2DjEAOKVuf6NcOiJI5r0a0Ai81OlqS03AXvIwA37i8xoUYEcnpEeB5nSvU-pRvENJiXIkOmDh8Fl6a8p_QRI9tZyZ23dqAt7j6Eb1uHqGCWHio1Kc3gUzKjmNIDv5jQeK2hLgryhYueEqna6lwPSKIVw7kXk";
    protected const VALID_CERT2S_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIkhjWEx2X29scFEifQ.eyJhdXRoIjogImNlcnQycyIsICJjZXJ0MnMiOiB7ImZpcnN0bmFtZSI6ICJHZXJhbGQiLCAiZnRjIjogMCwgImxhc3RuYW1lIjogIkNhYnVheSIsICJwbGlkIjogIjEiLCAic2hvcHBlcklkIjogIjkwMzgxMyIsICJ1c2VybmFtZSI6ICJhdXRodGVzdCJ9LCAiZGVsIjogeyJhdXRoIjogImJhc2ljIiwgImNuIjogImxvZ2luLmRldi5jbGllbnQuaW50LmdvZGFkZHkuY29tIiwgImZhY3RvcnMiOiB7InBfY2VydCI6IDE0ODk1MjgxNTJ9LCAiZnRjIjogMSwgImlhdCI6IDE0ODk1MjgxNTIsICJqdGkiOiAiV2JyQjRYUnJNekNSMWc0OWRKTWFKdyIsICJvIjogIiIsICJvdSI6ICJEb21haW4gQ29udHJvbCBWYWxpZGF0ZWQiLCAidHlwIjogImNlcnQifSwgImlhdCI6IDE0ODk1MjgxNTIsICJqdGkiOiAiV2JyQjRYUnJNekNSMWc0OWRKTWFKdyIsICJ0eXAiOiAiaWRwIn0.XdpnJ1nUUzQe_p7vdATLw2SSbTks7ERyJkN2THntYSWwxjIV9Qltjua74ndOYnbgNuON203bBe5xKp39qYXOMB0pqb32ozNTuiyoPcyWvm9LCl12FWSbkzQJsXIdCjNm_lwwyapClDcZCdVymQQJJFJQyWjlEZ07JIn85s-qymg";
    protected const INVALID_KEY_TOKEN = "ew0KICAiYWxnIjogIlJTMjU2IiwNCiAgImtpZCI6ICJiYWRpZCINCn0=.ew0KICAiYXV0aCI6ICJiYXNpYyIsDQogICJpYXQiOiAxNDk5Mzc2MDE3LA0KICAidHlwIjogImlkcCIsDQogICJzaG9wcGVySWQiOiAiYmFkc2hvcHBlcmlkIiwNCiAgInBsaWQiOiAiMSIsDQp9.LAG15KGpc-LRKBigmn9Xl1bkyU8VcJlAIf0c90NhwxJqYHXxYWBON8-7saCYaBcxNCNLT-FYLH1540hQMpR_3CHm-YBGTHxNS9oRdTGS7gz2PCeVTA6ZLv3E2s3i_epROYu2Rtq29x8sVf-6cbj540U3vIZfF02bE_faiSBaBTM";

    public const VALID_SSL_TO_JWT_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIkNEc1FJMFVhU1EifQ.eyJqdGkiOiAiUXVTOUp2aTJuREFHY3ZGMkxiRFJHQT09IiwgImlhdCI6IDE0OTkxMTQ1MTMsICJhdXRoIjogImJhc2ljIiwgInR5cCI6ICJjZXJ0IiwgImZhY3RvcnMiOiB7InBfY2VydCI6IDE0OTkxMTQ1MTN9LCAic2JqIjogeyJvIjogIkdvRGFkZHkuY29tLCBMTEMuIiwgIm91IjogIklUIiwgImNuIjogImFwaS1zaG9wcGVyLmRldi5jbGllbnQuaW50LmdvZGFkZHkuY29tIn19.hpy_EmOt1pSjSCXL2Hdm64scj2VUYGXvvxP5-tV0rjoxJwtVHSpt1FShnRftJFahq3APq5OBmlvwo-5N29w1IVkHrJau4iudEExG_rmRQMVHWDQum-cevE7wENwt-f7Cxdk7pRJ9ghYwTwqLv4VXoYn2XU292Jl2z9sqngwWlW8";
    public const INVALID_SSL_TO_JWT_TOKEN = "eyJhbGciOiAiUlMyNTYiLCAia2lkIjogIllYZFg1eVE4OHcifQ.eyJhY2NvdW50TmFtZSI6ICJvdmVybGR0ZXN0cmVwNSIsICJhdXRoIjogImJhc2ljIiwgImZhY3RvcnMiOiB7ImtfcHciOiAxNDg2Njc4MjYxfSwgImZpcnN0bmFtZSI6ICJPdmVybG9yZCIsICJmdGMiOiAxLCAiZ3JvdXBzIjogW10sICJpYXQiOiAxNDg2Njc4MjYxLCAianRpIjogIlJTOGNUbWhJOWJKUjV4emNydjlQcFEiLCAibGFzdG5hbWUiOiAiVGVzdFJlcDUiLCAidHlwIjogImpvbWF4In0.REdDSrajUO9HPqJmwxPa4wuX1xDQz6lmr1RovZtB5-Ita0l-xXvGU29ATNmraz68j2aNK0axoi-de79OHE-qysl1dYLDVnbCpaTeqMZGF7pEBd03n9gslh1Y7DcRbjZ1ZPSgN8L37ip9Rd1HcQOb5iG0XMcrsdn12ojsgzcx2ZQ";
    public const MALFORMED_SSL_TO_JWT_TOKEN = "";

    public static function setUpBeforeClass(): void
    {
        $dir   = sys_get_temp_dir().'/godaddy-key-cache_tests/';
        $cache = new AuthKeyFileCache($dir, 60);
        self::clearDir($dir);
        self::$authManager = new AuthManager(null, $cache, "MyApp");
    }

    private static function clearDir(string $dir): void
    {
        error_clear_last();
        $files = @scandir($dir);
        if ($files === false) {
            $error = error_get_last();
            throw new \Exception(sprintf('Failed listing key files: %s', $error['message'] ?? 'unknown error'));
        }

        $errors = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            error_clear_last();
            if (false === @unlink($dir.'/'.$file)) {
                $errors[] = error_get_last()['message'] ?? sprintf('unlink(%s): unknown error', $dir.'/'.$file);
            }
        }

        if (count($errors)) {
            throw new \Exception(sprintf('Failed to remove %d file(s): %s', count($errors), implode('; ', $errors)));
        }
    }

    protected function assertJomaxTokenValid($token)
    {
        $this->assertNotNull($token);
        $this->assertEquals('basic', $token->auth);
        $this->assertEquals('jomax', $token->typ);
        $this->assertEquals('Overlord', $token->firstname);
        $this->assertEquals('TestRep5', $token->lastname);
    }

    protected function assertBasicTokenValid(?AuthPayloadShopperBasic $payload)
    {
        $this->assertNotNull($payload);
        $shopper = $payload->getShopper();
        $this->assertNotNull($shopper);
        $this->assertEquals("basic", $payload->auth);
        $this->assertEquals("idp", $payload->typ);
        $this->assertEquals("crmx-integration-test", $shopper->username);
    }

    protected function assertE2STokenValid(?AuthPayloadE2S $payload)
    {
        $this->assertNotNull($payload);
        $shopper  = $payload->getShopper();
        $employee = $payload->del;
        $this->assertEquals("e2s", $payload->auth);
        $this->assertEquals("idp", $payload->typ);
        $this->assertEquals("crmx-integration-test", $shopper->username);
        $this->assertEquals("Overlord", $employee->firstname);
    }

    protected function assertS2STokenValid(?AuthPayloadS2S $payload)
    {
        $this->assertNotNull($payload);
        $this->assertEquals("idp", $payload->typ);

        $subordinateShopper = $payload->getShopper();
        $this->assertNotNull($subordinateShopper);
        $this->assertEquals("Webpro", $subordinateShopper->lastname);

        $delegatePayload = $payload->del;
        $this->assertNotNull($delegatePayload);

        $delegateShopper = $delegatePayload->getShopper();
        $this->assertNotNull($delegateShopper);
        $this->assertEquals("Tony", $delegateShopper->firstname);
    }

    protected function assertE2S2STokenValid(?AuthPayloadE2S2S $payload)
    {
        $this->assertNotNull($payload);
        $this->assertEquals("e2s2s", $payload->auth);
        $this->assertEquals("idp", $payload->typ);

        $subordinateShopper = $payload->getShopper();
        $this->assertNotNull($subordinateShopper);
        $this->assertEquals("Webpro", $subordinateShopper->lastname);

        $delegatePayload = $payload->del;
        $this->assertNotNull($delegatePayload);

        $delegateShopper = $delegatePayload->getShopper();
        $this->assertNotNull($delegateShopper);
        $this->assertEquals("Tony", $delegateShopper->firstname);

        $employee = $delegatePayload->del;
        $this->assertNotNull($employee);
        $this->assertEquals("Jake", $employee->firstname);
    }

    protected function assertCert2STokenValid(?AuthPayloadShopper $payload)
    {
        $this->assertNotNull($payload);
        $this->assertEquals("cert2s", $payload->auth);
        $this->assertNotNull($payload->getShopper());
        $this->assertEquals("903813", $payload->getShopper()->shopperId);
    }
}
