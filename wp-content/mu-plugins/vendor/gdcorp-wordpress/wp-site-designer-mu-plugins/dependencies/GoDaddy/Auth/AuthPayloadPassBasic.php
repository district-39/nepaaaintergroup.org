<?php

namespace GoDaddy\WordPress\Plugins\SiteDesigner\Dependencies\GoDaddy\Auth;

class AuthPayloadPassBasic extends AuthPayloadPass
{
    public $passId = '';
    /** @var string[] */
    public $apps = [];
    public $username = '';

    public function getPassInfo(): PassInfo
    {
        $passInfo = new PassInfo();

        $passInfo->passId   = $this->passId;
        $passInfo->apps     = $this->apps;
        $passInfo->username = $this->username;

        return $passInfo;
    }
}
