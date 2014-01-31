<?php

namespace OpenConext\EngineTestStand\Service;

class IdentityProvider
{
    protected $baseUrl;
    protected $name;
    protected $descriptor;

    public function __construct(
        $baseUrl,
        $route,
        $idpName,
        \SAML2_XML_md_EntityDescriptor $entityDescriptor
    ) {
        $this->baseUrl  = $baseUrl;
        $this->route    = $route;
        $this->name     = $idpName;
        $this->descriptor = $entityDescriptor;
    }

    public function singleSignOnLocation()
    {
        return $this->baseUrl . str_replace('{name}', urlencode($this->name), $this->route);
    }
}
