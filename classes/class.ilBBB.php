<?php

use BigBlueButton\Util\UrlBuilder;
use BigBlueButton\BigBlueButton;

/**
 * Class ilBBB
 */
class ilBBB extends BigBlueButton
{
    public function __construct($securitySecret=null, $baseUrl=null)
    {
        parent::__construct();
        $this->securitySecret = $securitySecret;
        $this->bbbServerBaseUrl = $baseUrl;
        $this->urlBuilder       = new UrlBuilder($this->securitySecret, $this->bbbServerBaseUrl);
        //Add Proxy
        if(ilProxySettings::_getInstance()->isActive())
        {
            $proxyHost = ilProxySettings::_getInstance()->getHost();
            $proxyPort = ilProxySettings::_getInstance()->getPort();
            $this->curlopts         = [
                CURLOPT_PROXY => $proxyHost . ":" . $proxyPort
            ];
        }
    }
}
