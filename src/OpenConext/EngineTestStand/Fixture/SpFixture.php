<?php

namespace OpenConext\EngineTestStand\Fixture;

class SpFixture extends RoleFixture
{
    public function configureFromAuthnRequest(\SAML2_AuthnRequest $authnRequest)
    {
        $issuer = $authnRequest->getIssuer();
        $entity = new \SAML2_XML_md_EntityDescriptor();
        $entity->entityID = $issuer;
        $this->fixture[$issuer] = $entity;
    }
}
