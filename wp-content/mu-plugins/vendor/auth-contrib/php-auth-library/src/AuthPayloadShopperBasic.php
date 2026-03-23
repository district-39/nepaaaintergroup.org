<?php

namespace GoDaddy\Auth;

class AuthPayloadShopperBasic extends AuthPayloadShopper
{
    public $shopperId = '';
    public $plid = '';
    public $firstname = '';
    public $username = '';
    public $lastname = '';

    public function getShopper(): ShopperInfo
    {
        $shopper            = new ShopperInfo();
        $shopper->shopperId = $this->shopperId;
        $shopper->plId      = $this->plid;
        $shopper->firstname = $this->firstname;
        $shopper->lastname  = $this->lastname;
        $shopper->username  = $this->username;
        return $shopper;
    }
}
