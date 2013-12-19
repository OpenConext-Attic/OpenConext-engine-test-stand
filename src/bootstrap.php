<?php

use \OpenConext\EngineTestStand\Config;
use \OpenConext\EngineTestStand\Fixture\SpFixture;
use \OpenConext\EngineTestStand\Fixture\IdpFixture;

SAML2_Compat_ContainerSingleton::setContainer(new \OpenConext\EngineTestStand\Saml2\Compat\Container());

$app = new Silex\Application();
$app['debug'] = true;
$app['config'] = $app->share(function() {
    return Config::create(OPENCONEXT_ETS_ROOT_DIR . '/config.json');
});
$app['sp-fixture'] = $app->share(function() use ($app) {
    /** @var Config $config */
    $config = $app['config'];
    return SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $config->expect('sp-fixture-file'));
});

$app['idp-fixture'] = $app->share(function() use ($app) {
    /** @var Config $config */
    $config = $app['config'];
    return IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $config->expect('idp-fixture-file'));
});
return $app;
