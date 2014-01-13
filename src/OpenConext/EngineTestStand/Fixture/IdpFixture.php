<?php

namespace OpenConext\EngineTestStand\Fixture;

class IdpFixture extends RoleFixture
{
    public function configureFromResponse($idpName, \SAML2_Response $response)
    {
        $entity = new \SAML2_XML_md_EntityDescriptor();
        $entity->entityID = $response->getIssuer();

        $entity->Extensions[] = $response;
        $this->fixture[$idpName] = $entity;
    }

    public function overrideResponseDestination($idpName, $acsUrl)
    {
        if (!isset($this->fixture[$idpName])) {
            throw new \RuntimeException("IDP $idpName does not exist?");
        }

        /** @var \SAML2_XML_md_EntityDescriptor $fixture */
        $fixture = $this->fixture[$idpName];
        $fixture->Extensions['DestinationOverride'] = $acsUrl;
    }
}
