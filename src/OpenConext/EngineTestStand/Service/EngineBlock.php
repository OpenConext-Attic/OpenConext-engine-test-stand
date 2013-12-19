<?php

namespace OpenConext\EngineTestStand\Service;

use OpenConext\EngineTestStand\Config;

class EngineBlock
{
    const BASE_URL_CONFIG_NAME = 'engineblock-url';

    const IDP_METADATA_URL = '/authentication/idp/metadata';
    const SP_METADATA_URL = '/authentication/sp/metadata';
    const SSO_URL = '/authentication/idp/single-sign-on';
    const ACS_URL = '/authentication/sp/consume-assertion';

    protected $config;

    /**
     * @param Config $config
     * @return static
     */
    public static function create(Config $config)
    {
        return new static($config);
    }

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function idpEntityId()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = self::IDP_METADATA_URL;
        return $host . $path;
    }

    public function singleSignOnLocation()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = self::SSO_URL;
        return $host . $path;
    }

    public function spEntityId()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = self::SP_METADATA_URL;
        return $host . $path;
    }

    public function assertionConsumerLocation()
    {
        $host = $this->config->expect(self::BASE_URL_CONFIG_NAME);
        $path = self::ACS_URL;
        return $host . $path;
    }
}
