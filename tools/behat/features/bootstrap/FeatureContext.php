<?php

define('OPENCONEXT_ETS_ROOT_DIR', realpath(__DIR__ . '/../../../../'));

use Behat\MinkExtension\Context\MinkContext;
use OpenConext\EngineTestStand\Fixture\IdpFixture;
use OpenConext\EngineTestStand\Fixture\SpFixture;
use OpenConext\EngineTestStand\Config;
use OpenConext\EngineTestStand\Service\LogReader;
use OpenConext\EngineTestStand\Service\ServiceProvider;
use OpenConext\EngineTestStand\Service\IdentityProvider;
use OpenConext\EngineTestStand\ServiceRegistry\ServiceRegistryMock;
use OpenConext\EngineTestStand\Service\EngineBlock;

// Require 3rd-party libraries here:
require OPENCONEXT_ETS_ROOT_DIR . '/vendor/autoload.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    const CONFIG_FILE = "/config.json";

    const IDP_FIXTURE_CONFIG_NAME = 'idp-fixture-file';
    const SP_FIXTURE_CONFIG_NAME = 'sp-fixture-file';
    const SERVICEREGISTRY_FIXTURE_CONFIG_NAME = 'serviceregistry-fixture-file';
    const KNOWN_IDPS_CONFIG_NAME = 'known-idps-metadata-url';

    /**
     * @Given /^an EngineBlock instance$/
     */
    public function anEngineblockInstance()
    {
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $engineBlock = EngineBlock::create($config);

        $serviceRegistry = ServiceRegistryMock::create();

        // Add EngineBlock as an SP
        $serviceRegistry->addSp($engineBlock->spEntityId(), $engineBlock->assertionConsumerLocation());
        // Add EngineBlock as an IdP
        $serviceRegistry->addIdp($engineBlock->idpEntityId(), $engineBlock->assertionConsumerLocation());

        // Add all known IdPs
        $serviceRegistry->addIdpsFromMetadataUrl($config->expect(self::KNOWN_IDPS_CONFIG_NAME));
    }

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
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);

        $spFixture = SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile);
        $spFixture->register($name, $entityId);
    }

    /**
     * @Given /^SP "([^"]*)" may only access "([^"]*)"$/
     */
    public function spMayOnlyAccess($spName, $idpName)
    {
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $spFixture = SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile);
        $spEntityId = $spFixture->get($spName)->entityID;

        $idpFixtureFile = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE)
            ->expect(self::IDP_FIXTURE_CONFIG_NAME);
        $idpFixture = IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile);
        $idpEntityId = $idpFixture->get($idpName)->entityID;

        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->blacklist($spEntityId);
        $serviceRegistry->allow($spEntityId, $idpEntityId);

    }

    /**
     * @Given /^SP "([^"]*)" is configured to generate a AuthnRequest like the one at "([^"]*)"$/
     */
    public function spIsConfiguredToGenerateAAuthnrequestLikeTheOneAt($spName, $authnRequestLogFile)
    {
        // Prefix the filepath with the root dir if it's not an absolute path.
        if ($authnRequestLogFile[0] !== '/') {
            $authnRequestLogFile = OPENCONEXT_ETS_ROOT_DIR . '/' . $authnRequestLogFile;
        }

        // Parse an AuthnRequest out of the log file
        $logReader = LogReader::create($authnRequestLogFile);
        $authnRequest = $logReader->getAuthnRequest();

        var_dump($authnRequest);

        // Write out how the SP should behave
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $spFixture = SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile);
        $spFixture->configureFromAuthnRequest($spName, $authnRequest);

        $serviceProvider = ServiceProvider::create($spName, $spFixture->get($spName), $config);
        $acsLocation = $serviceProvider->assertionConsumerServiceLocation();

        // Write out how the ServiceRegistry should behave
        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->addSpFromAuthnRequest($acsLocation, $authnRequest);
    }

    /**
     * @Given /^IdP "([^"]*)" is configured to return a Response like the one at "([^"]*)"$/
     */
    public function idpIsConfiguredToReturnAResponseLikeTheOneAt($idpName, $responseLogFile)
    {
        // Prefix the filepath with the root dir if it's not an absolute path.
        if ($responseLogFile[0] !== '/') {
            $responseLogFile = OPENCONEXT_ETS_ROOT_DIR . '/' . $responseLogFile;
        }

        // Parse a Response out of the log file
        $logReader = LogReader::create($responseLogFile);
        $response = $logReader->getResponse();

        var_dump($response);

        // Write out how the SP should behave
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $idpFixtureFile = $config->expect(self::IDP_FIXTURE_CONFIG_NAME);
        $idpFixture = IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile);
        $idpFixture->configureFromResponse($idpName, $response);

        $identityProvider = IdentityProvider::create($idpName, $idpFixture->get($idpName), $config);
        $ssoLocation = $identityProvider->singleSignOnLocation();

        // Write out how the ServiceRegistry should behave
        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->addIdpFromResponse($ssoLocation, $response);
    }

    /**
     * @When /^I log in at "([^"]*)"$/
     */
    public function iLogInAt($spName)
    {
        $config  = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $fixture = SpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $spFixtureFile);

        $serviceProvider = ServiceProvider::create($spName, $fixture->get($spName), $config);
        $loginUrl = $serviceProvider->loginUrl();

        $this->visit($loginUrl);
    }
}
