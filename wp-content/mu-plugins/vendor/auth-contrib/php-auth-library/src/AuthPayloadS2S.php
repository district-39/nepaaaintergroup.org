<?php

namespace GoDaddy\Auth;

class AuthPayloadS2S extends AuthPayloadShopper
{
    /** @var AuthPayloadShopperBasic */
    public $del;
    /** @var S2S */
    public $s2s;

    public $disp = '';
    public $al = 0;

    public function __construct()
    {
        $this->del = new AuthPayloadShopperBasic();
        $this->s2s = new S2S();
    }

    public function getShopper(): ShopperInfo
    {
        $shopper            = new ShopperInfo();
        $shopper->shopperId = $this->s2s->shopperId;
        $shopper->plId      = $this->s2s->plid;
        $shopper->firstname = $this->s2s->firstname;
        $shopper->lastname  = $this->s2s->lastname;
        $shopper->username  = $this->s2s->username;
        return $shopper;
    }

    /**
     * Overriding the isExpired method from AuthPayload.php by forcing heartbeat to true which will expire the token at vat + 5 min.
     *
     * @param integer $level
     * @param boolean $forcedHeartbeat
     * @param [type] $reason
     * @return boolean
     */
    public function isExpired(int $level, bool $forcedHeartbeat = false, &$reason = null): bool
    {
        return parent::isExpired($level, true, $reason);
    }
}

