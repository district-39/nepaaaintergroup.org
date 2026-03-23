<?php

namespace GoDaddy\Auth;

class AuthPayloadCert2S extends AuthPayloadShopper
{
    /** @var Cert2S */
    public $cert2s;

    public function __construct()
    {
        $this->cert2s = new Cert2S();
    }

    public function getShopper(): ShopperInfo
    {
        $shopper            = new ShopperInfo();
        $shopper->shopperId = $this->cert2s->shopperId;
        $shopper->plId      = $this->cert2s->plid;
        $shopper->firstname = $this->cert2s->firstname;
        $shopper->lastname  = $this->cert2s->lastname;
        $shopper->username  = $this->cert2s->username;
        return $shopper;
    }
}
