<?php

namespace OpenConext\Component\EngineTestStand\Service;

/**
 * Class IdentityProvider
 * @package OpenConext\Component\EngineTestStand\Service'
 */
class IdentityProvider
{
    protected $baseUrl;
    protected $name;
    protected $descriptor;

    /**
     * @param $baseUrl
     * @param $path
     * @param $idpName
     * @param \SAML2_XML_md_EntityDescriptor $entityDescriptor
     */
    public function __construct(
        $baseUrl,
        $path,
        $idpName,
        \SAML2_XML_md_EntityDescriptor $entityDescriptor
    ) {
        $this->baseUrl      = $baseUrl;
        $this->path         = $path;
        $this->name         = $idpName;
        $this->descriptor   = $entityDescriptor;
    }

    /**
     * @return string
     */
    public function singleSignOnLocation()
    {
        return $this->baseUrl . str_replace('{name}', urlencode($this->name), $this->path);
    }
}
