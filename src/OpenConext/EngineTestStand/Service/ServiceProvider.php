<?php

namespace OpenConext\EngineTestStand\Service;

use OpenConext\EngineTestStand\Config;
use OpenConext\EngineTestStand\Fixture\SpFixture;

class ServiceProvider
{
    const LOGIN_URL = '/sp.php/{name}/login-redirect';
    const ACS_URL = '/sp.php/{name}/acs';
    const BASE_URL_CONFIG_NAME = 'engine-test-stand-url';

    protected $name;
    protected $descriptor;
    protected $config;

    /**
     * @param $spName
     * @param \SAML2_XML_md_EntityDescriptor $entityDescriptor
     * @param Config $config
     * @return static
     */
    public static function create($spName, \SAML2_XML_md_EntityDescriptor $entityDescriptor, Config $config)
    {
        return new static($spName, $entityDescriptor, $config);
    }

    public function __construct($spName, \SAML2_XML_md_EntityDescriptor $entityDescriptor, Config $config)
    {
        $this->name = $spName;
        $this->descriptor = $entityDescriptor;
        $this->config = $config;
    }

    public function loginUrl()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = str_replace('{name}', urlencode($this->name), self::LOGIN_URL);
        return $host . $path;
    }

    public function assertionConsumerServiceLocation()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = str_replace('{name}', urlencode($this->name), self::ACS_URL);
        return $host . $path;
    }
}
