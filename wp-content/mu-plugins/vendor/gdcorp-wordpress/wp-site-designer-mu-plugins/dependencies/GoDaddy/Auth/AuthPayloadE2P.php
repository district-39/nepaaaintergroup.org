<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class AuthPayloadE2P extends AuthPayloadPass
{
    /** @var E2P */
    public $e2p;
    /** @var AuthPayloadEmployee */
    public $del;

    public function getPassInfo(): PassInfo
    {
        $passInfo           = new PassInfo();
        $passInfo->passId   = $this->e2p->passId;
        $passInfo->apps     = $this->e2p->apps;
        $passInfo->username = $this->e2p->username;
        return $passInfo;
    }
}
