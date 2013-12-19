<?php

namespace OpenConext\EngineTestStand\Service;

use OpenConext\EngineTestStand\Config;

class IdentityProvider
{
    const BASE_URL_CONFIG_NAME = 'engine-test-stand-url';

    const SSO_URL = '/idp.php/{name}/sso';

    protected $name;
    protected $descriptor;
    protected $config;

    /**
     * @param $idpName
     * @param \SAML2_XML_md_EntityDescriptor $entityDescriptor
     * @param Config $config
     * @return static
     */
    public static function create($idpName, \SAML2_XML_md_EntityDescriptor $entityDescriptor, Config $config)
    {
        return new static($idpName, $entityDescriptor, $config);
    }

    public function __construct($idpName, \SAML2_XML_md_EntityDescriptor $entityDescriptor, Config $config)
    {
        $this->name = $idpName;
        $this->descriptor = $entityDescriptor;
        $this->config = $config;
    }

    public function singleSignOnLocation()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = str_replace('{name}', urlencode($this->name), self::SSO_URL);
        return $host . $path;
    }
}
