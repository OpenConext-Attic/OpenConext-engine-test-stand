<?php

use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \OpenConext\EngineTestStand\Saml2\AuthnRequest\AuthnRequestFactory;
use \OpenConext\EngineTestStand\Fixture\SpFixture;
use \OpenConext\EngineTestStand\Config;

// Bootstrap the application (define dependencies)
require __DIR__ . '/../vendor/autoload.php';

/** @var \Silex\Application $app */
$app = require __DIR__ . '/../src/bootstrap.php';

######
# Start SSO with a HTTP-Redirect
######
$app->get('/{spName}/login-redirect', function(Silex\Application $app, $spName) {
    /** @var SpFixture $spFixture */
    $spFixture = $app['sp-fixture'];
    /** @var Config $config */
    $config = $app['config'];

    $factory = new AuthnRequestFactory();
    $authnRequest = $factory->createFromEntityDescriptor(
        $spFixture->get(urldecode($spName)),
        $config->expect('engineblock-url') . '/authentication/idp/single-sign-on'
    );

    $redirect = new SAML2_HTTPRedirect();
    $url = $redirect->getRedirectURL($authnRequest);
    return new RedirectResponse($url);
});

######
# Start SSO with a HTTP-POST
######
$app->get('/{spName}/login-post', function(Silex\Application $app, $spName) {
    /** @var SpFixture $spFixture */
    $spFixture = $app['sp-fixture'];
    /** @var \OpenConext\EngineTestStand\Config $config */
    $config = $app['config'];

    $factory = new AuthnRequestFactory();
    $authnRequest = $factory->createFromEntityDescriptor(
        $spFixture->get(urldecode($spName)),
        $config->expect('engineblock-url') . '/authentication/idp/single-sign-on'
    );

    $redirect = new SAML2_HTTPPost();
    $redirect->send($authnRequest);
});

$app->post('/{spName}/acs', function() {
    try {
        $httpPostBinding = new SAML2_HTTPPost();
        $message = $httpPostBinding->receive();
    }
    catch (Exception $e1) {
        try {
            $httpRedirectBinding = new SAML2_HTTPRedirect();
            $message = $httpRedirectBinding->receive();
        }
        catch (Exception $e2) {
            throw new \RuntimeException(
                'Unable to retrieve SAML message?',
                1,
                $e1
            );
        }
    }

    if (!($message instanceof SAML2_Response)) {
        throw new \RuntimeException('Unrecognized message type received: ' . get_class($message));
    }

    $message->xml = base64_decode($_POST['SAMLResponse']);

    return new Response(
        $message->xml,
        200,
        array('Content-Type' => 'application/xml')
    );
});

$app->get('/{spName}/metadata', function(Request $request, $spName) {
    $entityMetadata = new SAML2_XML_md_EntityDescriptor();
    $entityMetadata->entityID = $request->getSchemeAndHttpHost() . "sp.php/{$spName}/metadata";

    $acsService = new SAML2_XML_md_IndexedEndpointType();
    $acsService->index = 0;
    $acsService->Binding  = SAML2_Const::BINDING_HTTP_POST;
    $acsService->Location = $request->getSchemeAndHttpHost() . "/sp.php/{$spName}/acs";

    $spSsoDescriptor = new SAML2_XML_md_SPSSODescriptor();
    $spSsoDescriptor->protocolSupportEnumeration = array(SAML2_Const::NS_SAMLP);
    $spSsoDescriptor->AssertionConsumerService[] = $acsService;

    $entityMetadata->RoleDescriptor[] = $spSsoDescriptor;
    return new Response(
        $entityMetadata->toXML()->ownerDocument->saveXML(),
        200,
        array('Content-Type' => 'application/xml')
    );
});

$app->run();
