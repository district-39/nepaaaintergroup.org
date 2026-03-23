<?php

namespace GoDaddy\Auth;

class AuthPayloadE2S extends AuthPayloadShopper
{
    /** @var E2S */
    public $e2s;
    /** @var AuthPayloadEmployee */
    public $del;

    public function __construct()
    {
        $this->e2s = new E2S();
        $this->del = new AuthPayloadEmployee();
    }

    public function getShopper(): ShopperInfo
    {
        $shopper            = new ShopperInfo();
        $shopper->shopperId = $this->e2s->shopperId;
        $shopper->plId      = $this->e2s->plid;
        $shopper->firstname = $this->e2s->firstname;
        $shopper->lastname  = $this->e2s->lastname;
        $shopper->username  = $this->e2s->username;
        return $shopper;
    }
}
