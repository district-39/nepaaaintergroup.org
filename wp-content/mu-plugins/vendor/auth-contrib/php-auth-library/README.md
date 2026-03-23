# Info
This is a direct port of [JavaAuthLib](https://github.secureserver.net/auth-contrib/JavaAuthLib), so most of the documentation is the same. Its purpose is to fully follow GoDaddy standards, and to be trivial to maintain, keeping up with any changes to the JavaAuthLib.

## Requirements

- PHP 7.1+
- PHP cURL extension
- Optional: Composer, for autoloading and integration with existing projects; otherwise you will have to register your own PSR-4 autoloader that maps `GoDaddy\Auth\` to `src/`

## Installation

There are no external dependencies, all that's required is to do a `composer install` so the autoloader gets created.

## Usage
When you use the library you create an instances of the `AuthManager`. Its dependencies are an HTTP client to fetch the keys and a key file cache. Both have default implementations, respectfully using cURL extension and disk cache.

Once the AuthManager is created, there are five synchronous functions that are generally used to parse, verify, and validate tokens.

```php
interface AuthManagerInterface {
    function getTokenFromCreds(string $host, string $user, string $password, string $realm): ?AuthToken;
    function getAuthLLSIDP(string $host, string $rawToken): ?AuthLLSIDP;
    function getAuthPayloadSSLCert(string $host, string $rawToken): ?AuthPayloadSSLCert;
    function getAuthPayloadShopper(string $host, string $rawToken, ?array $auths, int $level): ?AuthPayloadShopper;
    function getAuthPayloadPass(string $host, string $rawToken, ?array $auths, int $level): ?AuthPayloadPass;
    function getAuthPayloadEmployee(string $host, string $rawToken, int $level): ?AuthPayloadEmployee;
}
```

Each of these methods will deal with various auth cookies: `lls_idp`, `auth_idp`, `auth_pass`, and `auth_jomax`.

##### Parameters:
- `host`: domain of the service (e.g. `sso.godaddy.com`, `sso.dev-godaddy.com`, `sso.secureserver.net`)
- `rawToken`: value of the string containing the JWT (typically from a cookie)
- `auths`: List of types accepted (`basic`, `e2s`, `s2s`, `e2s2s`, `cert2s`) appropriate for the scenario
- `level`: 0, 1, 2, or 3.  A level of 0 will do no expiry checks. 1 for low impact operations, 2 for medium, and 3 for high.

##### Returns:

If the token is expired, a null will be returned. If the token is invalid or the underlying sso service returns invalid data, an exception is thrown. Otherwise the appropriate token type is returned.

If null is returned, it is up to the caller to redirect to sso. This includes a redirect to /login or /hbi (for level 3). Exceptions should be rare (in case of network/API faults), and should be logged. Feel free to open issues regarding them; adding more/more specific exceptions is not a problem. 

## Example

```php
require __DIR__ . '/vendor/autoload.php';

use GoDaddy\Auth\AuthKeyFileCache as AuthKeyFileCache;
use GoDaddy\Auth\AuthManager as AuthManager;

$jwt = ''; // JSON Web Token typically comes in the HTTP Authorization header or cookie
$host = 'sso.godaddy.com';
$myApp = 'appcode';
$allowedTypes = ['basic', 's2s'];
$impactLevel = 1; // 1 = low, 2 = medium, 3 = high
$forceHeartbeat = true; // true = use new forced heartbeat expiration rules

$keyCache = new AuthKeyFileCache(sys_get_temp_dir().'/gd_keys', 60*60*24*40); // GoDaddy policy is to cache the keys for max 40 days

$authManager = new AuthManager(null, $keyCache, $myApp); // First argument is optional custom HTTP client; uses cURL by default
if($authToken = $authManager->getAuthPayloadShopper($host, $jwt, $allowedTypes, $impactLevel, $forceHeartbeat))
    print $authToken->getShopper()->shopperId;
else
    print 'redirect to login with qstring param auth_reason=' . $authManager->getReauthReason();
```

## Testing

You need to be connected to the GoDaddy VPN first. You can either install dev-dependencies via `composer install --dev` and run `./vendor/bin/phpunit`; or run your own `phpunit` binary.

# Changelog

#### v1.1.0

- Breaking Change: updated AuthManager to require 'app' parameter.
- Client agent provided within API calls

#### v1.0.2

Updated medium (level 2) policy JWT check - if the session is "remembered" and "verified" pass the check immediately. Old behavior was to check if it's at most 30 days after JWT issue time.

##### v1.0.1

Added URL-safe base64 encoding for key file cache; so key ID does not have to be checked for suspicious characters.
