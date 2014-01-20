<?php

use \OpenConext\EngineTestStand\Fixture\IdpFixture;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;
use \OpenConext\EngineTestStand\Saml2\Compat\Container;

// Project root
define('OPENCONEXT_ETS_ROOT_DIR', __DIR__ . '/../');

// Bootstrap the application (define dependencies)
/** @var \Silex\Application $app */
$app = require_once OPENCONEXT_ETS_ROOT_DIR . '/src/bootstrap.php';

######
# Metadata
######
$app->get('/{idpName}/metadata', function(Request $request, $idpName) {
    $entityMetadata = new SAML2_XML_md_EntityDescriptor();
    $entityMetadata->entityID = $request->getSchemeAndHttpHost() . "idp.php/{$idpName}/metadata";

    $acsService = new SAML2_XML_md_IndexedEndpointType();
    $acsService->index = 0;
    $acsService->Binding  = SAML2_Const::BINDING_HTTP_REDIRECT;
    $acsService->Location = $request->getSchemeAndHttpHost() . "/idp.php/{$idpName}/sso";

    $idpSsoDescriptor = new SAML2_XML_md_IDPSSODescriptor();
    $idpSsoDescriptor->protocolSupportEnumeration = array(SAML2_Const::NS_SAMLP);
    $idpSsoDescriptor->SingleSignOnService[] = $acsService;

    $entityMetadata->RoleDescriptor[] = $idpSsoDescriptor;
    return new Response(
        $entityMetadata->toXML()->ownerDocument->saveXML(),
        200,
        array('Content-Type' => 'application/xml')
    );
});

######
# Return a Response
######
$app->get('/{idpName}/sso', function(Silex\Application $app, $idpName) {
    /** @var IdpFixture $idpFixture */
    $idpFixture = $app['idp-fixture'];

    $redirectBinding = new SAML2_HTTPRedirect();
    $message = $redirectBinding->receive();

    if (!$message instanceof SAML2_AuthnRequest) {
        throw new \RuntimeException('Unknown message type: ' . get_class($message));
    }
    $authnRequest = $message;

    $entityDescriptor = $idpFixture->get(urldecode($idpName));
    /** @var SAML2_Response $response */
    $response = array_reduce($entityDescriptor->Extensions, function(&$result, $item) {
        return ($item instanceof SAML2_Response ? $item : $result);
    });

    $destination = (isset($entityDescriptor->Extensions['DestinationOverride']) ?
        $entityDescriptor->Extensions['DestinationOverride'] :
        ($authnRequest->getAssertionConsumerServiceURL() ?
            $authnRequest->getAssertionConsumerServiceURL() :
            $response->getDestination()));

    /** @var \OpenConext\EngineTestStand\Saml2\Compat\Container $container */
    $container = SAML2_Utils::getContainer();
    $container->postRedirect(
        $destination,
        array(
            'authnRequestXml'=> htmlentities($container->getLastDebugMessageOfType(Container::DEBUG_TYPE_IN)),
            'SAMLResponse' => base64_encode($response->xml),
        )
    );
    return $container->getPostResponse();
});

$app->run();
