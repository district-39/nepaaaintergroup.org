<?php

namespace GoDaddy\Auth;

class AuthPayloadS2P extends AuthPayloadPass
{
    /** @var S2P */
    public $s2p;
    /** @var AuthPayloadShopperBasic */
    public $del;

    public function getPassInfo(): PassInfo
    {
        $passInfo = new PassInfo();

        $passInfo->passId   = $this->s2p->passId;
        $passInfo->apps     = $this->s2p->apps;
        $passInfo->username = $this->s2p->username;

        return $passInfo;
    }
}
