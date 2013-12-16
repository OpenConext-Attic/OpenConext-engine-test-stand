<?php

use \OpenConext\EngineTestStand\Fixture\SpFixture;
use \Symfony\Component\HttpFoundation\RedirectResponse;

// Project root
define('OPENCONEXT_ETS_ROOT_DIR', __DIR__ . './../');

// Include Composer Autoloader
require_once OPENCONEXT_ETS_ROOT_DIR. '/vendor/autoload.php';

// Bootstrap the application (define dependencies)
/** @var \Silex\Application $app */
$app = require_once OPENCONEXT_ETS_ROOT_DIR . '/src/bootstrap.php';

######
# Start SSO with a HTTP-Redirect
######
$app->get('/{spName}/login-redirect', function(Silex\Application $app, $spName) {
    /** @var SpFixture $spFixture */
    $spFixture = $app['sp-fixture'];
    /** @var \OpenConext\EngineTestStand\Config $config */
    $config = $app['config'];

    $factory = new \OpenConext\EngineTestStand\Saml2\AuthnRequest\AuthnRequestFactory();
    $authnRequest = $factory->createFromEntityDescriptor(
        $spFixture->get($spName),
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

    $factory = new \OpenConext\EngineTestStand\Saml2\AuthnRequest\AuthnRequestFactory();
    $authnRequest = $factory->createFromEntityDescriptor(
        $spFixture->get($spName),
        $config->expect('engineblock-url') . '/authentication/idp/single-sign-on'
    );

    $redirect = new SAML2_HTTPPost();
    $redirect->send($authnRequest);
});

$app->run();
