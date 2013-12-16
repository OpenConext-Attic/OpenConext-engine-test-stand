<?php

namespace OpenConext\EngineTestStand\Saml2\AuthnRequest;

class AuthnRequestFactory
{
    public function createFromEntityDescriptor(\SAML2_XML_md_EntityDescriptor $descriptor, $destination)
    {
        $authnRequest = new \SAML2_AuthnRequest();
        $authnRequest->setIssuer($descriptor->entityID);
        $authnRequest->setDestination($destination);
        return $authnRequest;
    }

    protected function getSpRoleFromEntityDescriptor($spName, \SAML2_XML_md_EntityDescriptor $descriptor)
    {
        $spRoles = array_filter(
            $descriptor->RoleDescriptor,
            function($roleDescriptor) {
                return $roleDescriptor instanceof \SAML2_XML_md_SPSSODescriptor;
            }
        );
        if (empty($spRoles)) {
            throw new \RuntimeException("Entity '$spName' does not have SPSSODescriptors?");
        }
        if (count($spRoles) > 1) {
            throw new \RuntimeException("Entity '$spName' has multiple SPSSODescriptors, I don't know which one to use?");
        }
        /** @var \SAML2_XML_md_SPSSODescriptor $spRole */
        return $spRoles[0];
    }
}
