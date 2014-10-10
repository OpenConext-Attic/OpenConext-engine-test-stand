<?php

namespace OpenConext\Component\EngineTestStand;

class MockServiceProvider extends AbstractMockEntityRole
{
    public function loginUrl()
    {
        return $this->loginUrlRedirect();
    }

    public function loginUrlRedirect()
    {
        return $this->descriptor->Extensions['LoginRedirectUrl'];
    }

    public function loginUrlPost()
    {
        return $this->descriptor->Extensions['LoginPostUrl'];
    }

    public function assertionConsumerServiceLocation()
    {
        /** @var \SAML2_XML_md_SPSSODescriptor $role */
        $role = $this->getSsoRole();
        return $role->AssertionConsumerService[0]->Location;
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
