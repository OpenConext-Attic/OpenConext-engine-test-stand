<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

use OpenConext\Component\EngineTestStand\Service\EngineBlock;

class AuthnRequestFactory
{
    public function createFromEntityDescriptor(\SAML2_XML_md_EntityDescriptor $descriptor, EngineBlock $engineBlock)
    {
        $authnRequest = new \SAML2_AuthnRequest();
        $authnRequest->setIssuer($descriptor->entityID);

        if (isset($descriptor->Extensions['TransparentIdp'])) {
            $destination = $engineBlock->transparentSsoLocation($descriptor->Extensions['TransparentIdp']);
        }
        else {
            $destination = $engineBlock->singleSignOnLocation();
        }
        $authnRequest->setDestination($destination);

        return $authnRequest;
    }
}
