<?php

namespace OpenConext\Component\EngineTestStand;

class MockServiceProvider
{
    protected $name;
    protected $descriptor;

    public function __construct(
        $name,
        \SAML2_XML_md_EntityDescriptor $descriptor
    ) {
        $this->name = $name;
        $this->descriptor = $descriptor;
    }

    public function entityId()
    {
        return $this->descriptor->entityID;
    }

    public function getEntityDescriptor()
    {
        return $this->descriptor;
    }

    public function loginUrl()
    {
        return $this->descriptor->Extensions['LoginRedirectUrl'];
    }

    public function assertionConsumerServiceLocation()
    {
        foreach ($this->descriptor->RoleDescriptor as $role) {
            if (!$role instanceof \SAML2_XML_md_SPSSODescriptor) {
                continue;
            }

            $acsService = $role->AssertionConsumerService[0];

            return $acsService->Location;
        }

        throw new \RuntimeException('No SPSSODescriptor for MockServiceProvider?');
    }

    public function setEntityId($entityId)
    {
        $this->descriptor->entityID = $entityId;
        return $this;
    }

    public function setAuthnRequest(\SAML2_AuthnRequest $authnRequest)
    {
        $this->descriptor->Extensions['AuthnRequest'] = $authnRequest;
        return $this;
    }

    public function useIdpTransparently($entityId)
    {
        $this->descriptor->Extensions['TransparentIdp'] = $entityId;
        return $this;
    }

    public function useUnsolicited()
    {
        $this->descriptor->Extensions['Unsollicited'] = true;
        return $this;
    }

    public function mustUseUnsolicited()
    {
        return isset($this->descriptor->Extensions['Unsollicited']) && $this->descriptor->Extensions['Unsollicited'];
    }
}
