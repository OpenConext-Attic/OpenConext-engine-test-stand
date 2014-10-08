<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\MockIdentityProvider;
use OpenConext\Component\EngineTestStand\MockServiceProvider;

class ResponseFactory
{
    public function createForEntityWithRequest(
        MockIdentityProvider $mockIdp,
        \SAML2_AuthnRequest $request
    ) {
        $fixedResponse = $mockIdp->getFixedResponse();
        if ($fixedResponse) {
            return $fixedResponse;
        }

        $key = new \XMLSecurityKey(\XMLSecurityKey::RSA_SHA256, array('type'=> 'private'));
        $key->loadKey($mockIdp->getPrivateKeyPem());

        $requestId = $request->getId();
        $idpEntityId = $mockIdp->entityId();
        $responseId = \SAML2_Compat_ContainerSingleton::getInstance()->generateId();
        $assertionId = \SAML2_Compat_ContainerSingleton::getInstance()->generateId();

        $now        = gmdate('Y-m-d\TH:i:s\Z');
        $tomorrow   = gmdate('Y-m-d\TH:i:s\Z', time() + (24 * 60 * 60));

        $uid                    = 'admin';
        $schacHomeOrganization  = 'engine-test-stand.openconext.org';

        $topStatusCode      = $mockIdp->getStatusCodeTop();
        $secondStatusCode   = $mockIdp->getStatusCodeSecond();
        $statusMessage      = $mockIdp->getStatusMessage();

        $statusXml = "<samlp:StatusCode Value=\"$topStatusCode\">";
        if ($secondStatusCode) {
            $statusXml .= "<samlp:StatusCode Value=\"$secondStatusCode\" />";
        }
        $statusXml .= '</samlp:StatusCode>';
        if ($statusMessage) {
            $statusXml .= '<samlp:StatusMessage>' . htmlspecialchars($statusMessage, ENT_COMPAT) . '</samlp:StatusMessage>';
        }

        $document = new \DOMDocument();
        $document->loadXML(<<<RESPONSE
<samlp:Response
  xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
  xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
  ID="$responseId"
  IssueInstant="$now"
  InResponseTo="$requestId"
  Version="2.0">
    <saml:Issuer>$idpEntityId</saml:Issuer>
    <samlp:Status>$statusXml</samlp:Status>
    <saml:Assertion IssueInstant="$now" Version="2.0" ID="$assertionId">
        <saml:Issuer>$idpEntityId</saml:Issuer>
        <saml:Subject>
            <saml:NameID>$uid@$schacHomeOrganization</saml:NameID>
            <saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
                <saml:SubjectConfirmationData
                  NotOnOrAfter="$tomorrow"
                  InResponseTo="$requestId" />
            </saml:SubjectConfirmation>
        </saml:Subject>
        <saml:AuthnStatement AuthnInstant="$now">
            <saml:AuthnContext>
                <saml:AuthnContextClassRef>
                    urn:oasis:names:tc:SAML:2.0:ac:classes:Password
                </saml:AuthnContextClassRef>
            </saml:AuthnContext>
        </saml:AuthnStatement>
        <saml:AttributeStatement>
            <saml:Attribute Name="urn:mace:dir:attribute-def:uid">
                <saml:AttributeValue>$uid</saml:AttributeValue>
            </saml:Attribute>
            <saml:Attribute Name="urn:mace:terena.org:attribute-def:schacHomeOrganization">
                <saml:AttributeValue>$schacHomeOrganization</saml:AttributeValue>
            </saml:Attribute>
        </saml:AttributeStatement>
    </saml:Assertion>
</samlp:Response>
RESPONSE
);

        $response = new \SAML2_Response($document->firstChild);
        $response->setSignatureKey($key);
        $response->xml = $response->toSignedXML()->ownerDocument->saveXML();

        return $response;
    }
}
