<?php

use \OpenConext\EngineTestStand\Fixture\IdpFixture;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;

// Project root
define('OPENCONEXT_ETS_ROOT_DIR', __DIR__ . '/../');

// Include Composer Autoloader
require_once OPENCONEXT_ETS_ROOT_DIR. '/vendor/autoload.php';

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

    // @todo This will fail horribly when Response signing is used.
    $xml = str_replace(
        'Destination="' . $response->getDestination() . '"',
        'Destination="' . $authnRequest->getAssertionConsumerServiceURL() . '"',
        $response->xml
    );

    /** @var \OpenConext\EngineTestStand\Saml2\Compat\Container $container */
    $container = SAML2_Utils::getContainer();
    $container->postRedirect(
        $authnRequest->getAssertionConsumerServiceURL(),
        array('SAMLResponse' => base64_encode($xml))
    );
    return $container->getPostResponse();
});



$app->run();
