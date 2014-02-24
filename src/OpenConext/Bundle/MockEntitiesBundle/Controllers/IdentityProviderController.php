<?php

namespace OpenConext\Bundle\MockEntitiesBundle\Controllers;

use OpenConext\Component\EngineTestStand\MockIdentityProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\Saml2\ResponseFactory;
use OpenConext\Component\EngineTestStand\Saml2\Compat\Container;
use Symfony\Component\Routing\Annotation\Route;

class IdentityProviderController extends Controller
{
    /**
     * @Route("/{idpName}/metadata", name="mock_idp_metadata")
     *
     * @param $idpName
     * @return Response
     */
    public function metadataAction($idpName)
    {
        $idpRegistry = $this->get('openconext_mock_entities.idp_registry');
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $idpRegistry->get($idpName);
        $entityDescriptor = $mockIdp->getEntityDescriptor();

        return new Response(
            $entityDescriptor->toXML()->ownerDocument->saveXML(),
            200,
            array('Content-Type' => 'application/xml')
        );
    }

    /**
     * @Route("/{idpName}/sso", name="mock_idp_sso")
     *
     * @param $idpName
     * @return Response
     * @throws \RuntimeException
     */
    public function singleSignOnAction(Request $request, $idpName)
    {
        if ($request->isMethod('GET')) {
            $redirectBinding = new \SAML2_HTTPRedirect();
            $message = $redirectBinding->receive();
        }
        else if ($request->isMethod('POST')) {
            $postBinding = new \SAML2_HTTPPost();
            $message = $postBinding->receive();
        }
        else {
            throw new \RuntimeException('Unsupported HTTP method');
        }

        if (!$message instanceof \SAML2_AuthnRequest) {
            throw new \RuntimeException('Unknown message type: ' . get_class($message));
        }
        $authnRequest = $message;

        $idpRegistry = $this->get('openconext_mock_entities.idp_registry');
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $idpRegistry->get($idpName);

        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->get('openconext_mock_entities.saml_response_factory');
        $response = $responseFactory->createForEntityWithRequest($mockIdp->getEntityDescriptor(), $authnRequest);

        $destination = ($mockIdp->hasDestinationOverride() ?
            $mockIdp->getDestinationOverride() :
            ($authnRequest->getAssertionConsumerServiceURL() ?
                $authnRequest->getAssertionConsumerServiceURL() :
                $response->getDestination()));

        /** @var Container $container */
        $container = \SAML2_Utils::getContainer();
        $authnRequestXml = $container->getLastDebugMessageOfType(Container::DEBUG_TYPE_IN);
        $container->postRedirect(
            $destination,
            array(
                'authnRequestXml'=> htmlentities($authnRequestXml),
                'SAMLResponse' => base64_encode($response->xml),
            )
        );
        return $container->getPostResponse();
    }
}
