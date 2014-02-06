<?php

namespace OpenConext\Component\EngineTestStand;

/**
 * Class MockIdentityProvider
 * @package OpenConext\Component\EngineTestStand
 */
class MockIdentityProvider
{
    protected $name;
    protected $descriptor;

    public function __construct($name, \SAML2_XML_md_EntityDescriptor $descriptor)
    {
        $this->name = $name;
        $this->descriptor = $descriptor;
    }

    public function entityId()
    {
        return $this->descriptor->entityID;
    }

    public function singleSignOnLocation()
    {
        foreach ($this->descriptor->RoleDescriptor as $role) {
            if (!$role instanceof \SAML2_XML_md_IDPSSODescriptor) {
                continue;
            }

            $ssoService = $role->SingleSignOnService[0];

            return $ssoService->Location;
        }

        throw new \RuntimeException('No IDPSSODescriptor for MockServiceProvider?');
    }

    public function setResponse(\SAML2_Response $response)
    {
        $this->descriptor->entityID = $response->getIssuer();
        $this->descriptor->Extensions['Response'] = $response;
    }

    public function overrideResponseDestination($acsUrl)
    {
        $this->descriptor->Extensions['DestinationOverride'] = $acsUrl;
    }

    public function hasDestinationOverride()
    {
        return isset($this->descriptor->Extensions['DestinationOverride']);
    }

    public function getDestinationOverride()
    {
        return $this->descriptor->Extensions['DestinationOverride'];
    }

    public function getEntityDescriptor()
    {
        return $this->descriptor;
    }
}
