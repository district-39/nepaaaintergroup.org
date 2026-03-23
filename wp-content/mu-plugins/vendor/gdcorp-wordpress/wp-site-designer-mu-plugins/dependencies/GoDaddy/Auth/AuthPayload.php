<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class AuthPayload
{
    const EXPIRED_REASON_DEFAULT = 1;
    const EXPIRED_REASON_HBI = 2;
    const EXPIRED_REASON_VAT = 3;

    // authentication method (basic, e2s, s2s, e2s2s, cert2s)
    public $auth = '';
    // issued at (JWT reserved)
    public $iat = 0;
    // authentication time
    public $hbi = 0;
    // JWT ID (JWT reserved)
    public $jti = '';
    // type, should be "JWT" or empty (JWT reserved)
    public $typ = '';
    public $ftc = 0;
    // "validation at", defaults to $this->iat; valid only for 10 minutes
    public $vat = 0;
    // "remember me", old values were true/false; new values may be 0/1, hence the type is omitted.
    public $per;

    /**
     * @param int $level Impact level, 0 to 3. Level 0 always returns false. 1 for low impact operations (1d-180d;
     *                   depending on "vat" and "per"), 2 for medium (12h-7d-30d), and 3 for high (1h).
     *                   Level >3 always returns true.
     *
     * @return bool True if the token should be considered expired for the required level.
     */
    public function isExpired(int $level, bool $forcedHeartbeat=false, &$reason=null): bool
    {
        $reason = self::EXPIRED_REASON_DEFAULT;

        // Get the current time in seconds from UTC time
        $now = time();

        $vat = $this->vat; // verified at time
        if ($vat === 0) {
            $vat = $this->iat;
        }

        if($forcedHeartbeat) {
            // 5 minute forced vat freshness
            if ($now > $vat + 5 * 60) {
                $reason = self::EXPIRED_REASON_VAT;
                return true;
            }
            $vatExpired = false;
        } else {
            // 10 minutes vat expiration
            $vatExpired = ($now > $vat + 10 * 60);
        }

        // Return false if the level is 0
        if ($level === 0) {
            return false;
        }

        // Return true if the level is > 3
        if ($level > 3) {
            return true;
        }

        // For a level 3 (high) we use hbi instead of iat for expiration
        $iat = $this->iat;
        if ($level === 3) {
            if ($this->hbi === 0) {
                return true;
            } else {
                $iat = $this->hbi;
            }
        }

        $expirationSeconds = 0;

        if ($level === 1) {
            if ($this->per && !$vatExpired) {
                return false;
            } elseif ($this->per || !$vatExpired) {
                $expirationSeconds = 60 * 60 * 24 * 180;
            } else {
                $expirationSeconds = 60 * 60 * 24;
            }
        } elseif ($level === 2) {
            if ($this->per && !$vatExpired) {
                return false;
            } elseif ($this->per || !$vatExpired) {
                $expirationSeconds = 60 * 60 * 24 * 7;
            } else {
                $expirationSeconds = 60 * 60 * 12;
            }
        } elseif ($level === 3) {
            $reason = self::EXPIRED_REASON_HBI;
            $expirationSeconds = 60 * 60;
        }

        return $now > $iat + $expirationSeconds;
    }
}
