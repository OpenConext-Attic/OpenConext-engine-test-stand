<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

class ResponseFactory
{
    public function createForEntityWithRequest(
        \SAML2_XML_md_EntityDescriptor $entityDescriptor,
        \SAML2_AuthnRequest $request
    ) {
        /** @var \SAML2_Response $response */
        return array_reduce($entityDescriptor->Extensions, function(&$result, $item) {
            return ($item instanceof \SAML2_Response ? $item : $result);
        });
    }
}
