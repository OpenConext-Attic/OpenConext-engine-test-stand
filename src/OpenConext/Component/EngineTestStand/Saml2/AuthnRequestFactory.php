<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

use OpenConext\Component\EngineTestStand\Service\EngineBlock;

class AuthnRequestFactory
{
    public function createFromEntityDescriptor(\SAML2_XML_md_EntityDescriptor $descriptor, EngineBlock $engineBlock)
    {
        // Create the AuthnRequest (or retrieve a stored AuthNRequest)
        if (isset($descriptor->Extensions['AuthnRequest'])) {
            $authnRequest = $descriptor->Extensions['AuthnRequest'];
        }
        else {
            $authnRequest = new \SAML2_AuthnRequest();
            $authnRequest->setIssuer($descriptor->entityID);
        }

        // Set / override the Destination
        if (isset($descriptor->Extensions['TransparentIdp'])) {
            $destination = $engineBlock->transparentSsoLocation($descriptor->Extensions['TransparentIdp']);
        }
        else {
            $destination = $engineBlock->singleSignOnLocation();
        }
        $authnRequest->setDestination($destination);

        // Done
        return $authnRequest;
    }
}
