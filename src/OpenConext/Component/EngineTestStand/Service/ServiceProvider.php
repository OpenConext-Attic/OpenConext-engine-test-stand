<?php

namespace OpenConext\EngineTestStand\Service;

use OpenConext\EngineTestStand\Config;
use OpenConext\EngineTestStand\Fixture\MockSpsFixtureAbstract;

class ServiceProvider
{
    protected $baseUrl;
    protected $triggerLoginRedirectPath;
    protected $acsPath;
    protected $name;
    protected $descriptor;

    public function __construct(
        $baseUrl,
        $triggerLoginRedirectPath,
        $acsPath,
        $spName,
        \SAML2_XML_md_EntityDescriptor $entityDescriptor
    ) {
        $this->baseUrl                  = $baseUrl;
        $this->triggerLoginRedirectPath = $triggerLoginRedirectPath;
        $this->acsPath                  = $acsPath;

        $this->name         = $spName;
        $this->descriptor   = $entityDescriptor;
    }

    public function loginUrl()
    {
        return $this->baseUrl .  str_replace('{name}', urlencode($this->name), $this->triggerLoginRedirectPath);
    }

    public function assertionConsumerServiceLocation()
    {
        return $this->baseUrl . str_replace('{name}', urlencode($this->name), $this->acsPath);
    }
}
