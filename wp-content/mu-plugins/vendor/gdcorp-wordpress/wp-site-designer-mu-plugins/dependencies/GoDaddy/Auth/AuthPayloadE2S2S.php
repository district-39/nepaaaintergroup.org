<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class AuthPayloadE2S2S extends AuthPayloadShopper
{
    /** @var E2S2S */
    public $e2s2s;
    /** @var AuthPayloadE2S */
    public $del;

    public function __construct()
    {
        $this->e2s2s = new E2S2S();
        $this->del   = new AuthPayloadE2S();
    }

    public function getShopper(): ShopperInfo
    {
        $shopper            = new ShopperInfo();
        $shopper->shopperId = $this->e2s2s->shopperId;
        $shopper->plId      = $this->e2s2s->plid;
        $shopper->firstname = $this->e2s2s->firstname;
        $shopper->lastname  = $this->e2s2s->lastname;
        $shopper->username  = $this->e2s2s->username;
        return $shopper;
    }
}
