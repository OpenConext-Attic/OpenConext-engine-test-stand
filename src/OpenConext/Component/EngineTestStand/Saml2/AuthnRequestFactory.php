<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

use OpenConext\Component\EngineTestStand\MockServiceProvider;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use XMLSecurityKey;

/**
 * Class AuthnRequestFactory
 * @package OpenConext\Component\EngineTestStand\Saml2
 */
class AuthnRequestFactory
{
    /**
     * @param MockServiceProvider $mockSp
     * @param EngineBlock $engineBlock
     * @return AuthnRequest
     * @throws \Exception
     */
    public function createForRequestFromTo(MockServiceProvider $mockSp, EngineBlock $engineBlock)
    {
        $request = $mockSp->getAuthnRequest();

        // Set / override the Destination
        $transparentIdp = $mockSp->getTransparentIdp();
        if (!empty($transparentIdp)) {
            $destination = $engineBlock->transparentSsoLocation($transparentIdp);
        } else {
            $destination = $engineBlock->singleSignOnLocation();
        }
        $request->setDestination($destination);

        if ($mockSp->mustSignAuthnRequests()) {
            $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));
            $key->loadKey($mockSp->getPrivateKeyPem());
            $request->setSignatureKey($key);
        }

        return $request;
    }
}
