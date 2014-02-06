<?php

namespace OpenConext\Bundle\MockEntitiesBundle\Controllers;

use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\MockServiceProvider;
use OpenConext\Component\EngineTestStand\Saml2\AuthnRequestFactory;
use OpenConext\Component\EngineTestStand\Saml2\Compat\Container;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ServiceProviderController extends Controller
{
    /**
     * @Route("/{spName}/login-redirect", name="mock_sp_login_redirect")
     *
     * @return RedirectResponse
     */
    public function triggerLoginRedirect($spName)
    {
        $mockSpRegistry = $this->get('openconext_mock_entities.sp_registry');
        if (!$mockSpRegistry->has($spName)) {
            throw new BadRequestHttpException('No SP found for ' . $spName);
        }

        /** @var MockServiceProvider $mockSp */
        $mockSp = $mockSpRegistry->get($spName);
        /** @var EngineBlock $engineBlock */
        $engineBlock = $this->get('openconext_functional_testing.service.engine_block');

        $factory = new AuthnRequestFactory();
        $authnRequest = $factory->createFromEntityDescriptor(
            $mockSp->getEntityDescriptor(),
            $engineBlock->singleSignOnLocation()
        );

        $redirect = new \SAML2_HTTPRedirect();
        $url = $redirect->getRedirectURL($authnRequest);

        return new RedirectResponse($url);
    }

    /**
     * @Route("/{spName}/login-post", name="mock_sp_login_post")
     *
     * @param $spName
     */
    public function triggerLoginPost($spName)
    {
        /** @var EntityRegistry $mockSpRegistry */
        $mockSpRegistry = $this->get('openconext_mock_entities.sp_registry');
        if (!$mockSpRegistry->has($spName)) {
            throw new BadRequestHttpException('No SP found for ' . $spName);
        }

        /** @var MockServiceProvider $mockSp */
        $mockSp = $mockSpRegistry->get($spName);

        /** @var EngineBlock $engineBlock */
        $engineBlock = $this->get('openconext_functional_testing.service.engine_block');

        $factory = new AuthnRequestFactory();
        $authnRequest = $factory->createFromEntityDescriptor(
            $mockSp,
            $engineBlock->singleSignOnLocation()
        );

        $redirect = new \SAML2_HTTPPost();
        $redirect->send($authnRequest);

        /** @var Container $container */
        $container = \SAML2_Utils::getContainer();
        return $container->getPostResponse();
    }

    /**
     * @Route("/{spName}/acs", name="mock_sp_acs")
     *
     * @return Response
     * @throws \RuntimeException
     */
    public function assertionConsumerAction()
    {
        try {
            $httpPostBinding = new \SAML2_HTTPPost();
            $message = $httpPostBinding->receive();
        }
        catch (\Exception $e1) {
            try {
                $httpRedirectBinding = new \SAML2_HTTPRedirect();
                $message = $httpRedirectBinding->receive();
            }
            catch (\Exception $e2) {
                throw new \RuntimeException(
                    'Unable to retrieve SAML message?',
                    1,
                    $e1
                );
            }
        }

        if (!($message instanceof \SAML2_Response)) {
            throw new \RuntimeException('Unrecognized message type received: ' . get_class($message));
        }

        $message->xml = base64_decode($_POST['SAMLResponse']);

        return new Response(
            $message->xml,
            200,
            array('Content-Type' => 'application/xml')
        );
    }

    /**
     * @Route("/{spName}/metadata", name="mock_sp_metadata")
     *
     * @param $spName
     * @return Response
     */
    public function metadataAction($spName)
    {
        /** @var EntityRegistry $mockSpRegistry */
        $mockSpRegistry = $this->get('openconext_mock_entities.sp_registry');
        /** @var MockServiceProvider $mockSp */
        $mockSp = $mockSpRegistry->get($spName);

        return new Response(
            $mockSp->getEntityDescriptor()->toXML()->ownerDocument->saveXML(),
            200,
            array('Content-Type' => 'application/xml')
        );
    }
}
