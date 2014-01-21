<?php

use \OpenConext\EngineTestStand\Config;
use \OpenConext\EngineTestStand\Fixture\SpFixture;
use \OpenConext\EngineTestStand\Fixture\IdpFixture;

SAML2_Compat_ContainerSingleton::setContainer(new \OpenConext\EngineTestStand\Saml2\Compat\Container());

$app = new Silex\Application();
$app['debug'] = true;
$app['config'] = $app->share(function() {
    return Config::create('config.json');
});
$app['sp-fixture'] = $app->share(function() use ($app) {
    /** @var Config $config */
    $config = $app['config'];
    return SpFixture::create($config->expect('sp-fixture-file'));
});

$app['idp-fixture'] = $app->share(function() use ($app) {
    /** @var Config $config */
    $config = $app['config'];
    return IdpFixture::create($config->expect('idp-fixture-file'));
});
return $app;
