<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

use OpenConext\Component\EngineTestStand\MockServiceProvider;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;

class AuthnRequestFactory
{
    public function createForRequestFromTo(MockServiceProvider $mockSp, EngineBlock $engineBlock)
    {
        $request = $mockSp->getAuthnRequest();

        // Set / override the Destination
        $transparentIdp = $mockSp->getTransparentIdp();
        if (!empty($transparentIdp)) {
            $destination = $engineBlock->transparentSsoLocation($transparentIdp);
        }
        else {
            $destination = $engineBlock->singleSignOnLocation();
        }
        $request->setDestination($destination);

        return $request;
    }
}
