<?php

/**
 * Class ilBBB
 */
class ilBBB extends \BigBlueButton\BigBlueButton
{
    public function __construct($securitySecret=null, $baseUrl=null)
    {
        parent::__construct();
        $this->securitySecret = $securitySecret;
        $this->bbbServerBaseUrl = $baseUrl;
        $this->urlBuilder       = new UrlBuilder($this->securitySecret, $this->bbbServerBaseUrl);
        //Add Proxy
        require_once('Services/Http/classes/class.ilProxySettings.php');
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
