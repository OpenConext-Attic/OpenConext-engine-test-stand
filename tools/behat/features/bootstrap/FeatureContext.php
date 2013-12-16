<?php

define('OPENCONEXT_ETS_ROOT_DIR', realpath(__DIR__ . '/../../../../'));

use Behat\MinkExtension\Context\MinkContext;
use OpenConext\EngineTestStand\Fixture\IdpFixture;
use OpenConext\EngineTestStand\Fixture\SpFixture;
use OpenConext\EngineTestStand\Fixture\ServiceRegistryFixture;
use OpenConext\EngineTestStand\Config;
use OpenConext\EngineTestStand\Service\LogReader;
use OpenConext\EngineTestStand\Service\ServiceProvider;

// Require 3rd-party libraries here:
require OPENCONEXT_ETS_ROOT_DIR . '/vendor/autoload.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    const CONFIG_FILE = "/config.json";

    const IDP_FIXTURE_CONFIG_NAME = 'idp-fixture-file';
    const SP_FIXTURE_CONFIG_NAME = 'idp-fixture-file';
    const SERVICEREGISTRY_FIXTURE_CONFIG_NAME = 'serviceregistry-fixture-file';

    /**
     * @Given /^an Identity Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function anIdentityProviderNamedWithEntityid($name, $entityId)
    {
        $idpFixtureFile = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE)
            ->expect(self::IDP_FIXTURE_CONFIG_NAME);

        IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile)
            ->register($name, $entityId);
    }

    /**
     * @Given /^a Service Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function aServiceProviderNamedWithEntityid($name, $entityId)
    {
        $spFixtureFile = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE)
            ->expect(self::SP_FIXTURE_CONFIG_NAME);

        SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile)
            ->register($name, $entityId);
    }

    /**
     * @Given /^SP "([^"]*)" is configured to generate a AuthnRequest like the one at "([^"]*)"$/
     */
    public function spIsConfiguredToGenerateAAuthnrequestLikeTheOneAt($spName, $authnRequestLogFile)
    {
        /** @var SAML2_AuthnRequest $authnRequest */
        $authnRequest = LogReader::create(
            ($authnRequestLogFile[0] === '/' ? '' : OPENCONEXT_ETS_ROOT_DIR . '/') . $authnRequestLogFile
        )->getAuthnRequest();

        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);

        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile)
            ->configureFromAuthnRequest($authnRequest);

        $serviceRegistryFixtureFile = $config->expect(self::SERVICEREGISTRY_FIXTURE_CONFIG_NAME);
        ServiceRegistryFixture::create(OPENCONEXT_ETS_ROOT_DIR . $serviceRegistryFixtureFile)
            ->addSpFromAuthnRequest($authnRequest);
    }

    /**
     * @When /^I log in at "([^"]*)"$/
     */
    public function iLogInAt($spName)
    {
        $config  = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $fixture = SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile);

        $loginUrl = ServiceProvider::create($spName, $fixture->get($spName), $config)
            ->loginUrl();

        $this->visit($loginUrl);
    }
}
