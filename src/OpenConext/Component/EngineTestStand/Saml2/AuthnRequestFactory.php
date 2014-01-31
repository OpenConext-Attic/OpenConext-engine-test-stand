<?php

namespace OpenConext\EngineTestStand\Saml2;

class AuthnRequestFactory
{
    public function createFromEntityDescriptor(\SAML2_XML_md_EntityDescriptor $descriptor, $destination)
    {
        $authnRequest = new \SAML2_AuthnRequest();
        $authnRequest->setIssuer($descriptor->entityID);
        $authnRequest->setDestination($destination);
        return $authnRequest;
    }
}
