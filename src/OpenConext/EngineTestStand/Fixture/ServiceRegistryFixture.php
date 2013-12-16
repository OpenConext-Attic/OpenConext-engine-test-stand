<?php

namespace OpenConext\EngineTestStand\Fixture;

class ServiceRegistryFixture extends AbstractFixture
{
    public function addSpFromAuthnRequest(\SAML2_AuthnRequest $authnRequest)
    {
        $issuer = $authnRequest->getIssuer();
        $entity = new \SAML2_XML_md_EntityDescriptor();
        $entity->entityID = $issuer;
        $this->fixture[$issuer] = $entity;
    }
}
