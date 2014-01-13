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

    const IDP_FIXTURE_CONFIG_NAME               = 'idp-fixture-file';
    const SP_FIXTURE_CONFIG_NAME                = 'sp-fixture-file';

    const SERVICEREGISTRY_FIXTURE_CONFIG_NAME   = 'serviceregistry-fixture-file';

    const IDPS_CONFIG_NAME                      = 'idps-config-url';
    const SPS_CONFIG_NAME                       = 'sps-config-url';

    /**
     * @Given /^an EngineBlock instance configured with JSON data$/
     */
    public function anEngineblockInstanceConfiguredWithJsonData()
    {
        $serviceRegistry = ServiceRegistryMock::create();
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);

        // Add all known IdPs
        $serviceRegistry->reset();
        $serviceRegistry->addSpsFromJsonExport($config->expect(self::SPS_CONFIG_NAME));
        $serviceRegistry->addIdpsFromJsonExport($config->expect(self::IDPS_CONFIG_NAME));

    }

    /**
     * @Given /^EngineBlock is expected to send a AuthnRequest like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAAuthnrequestLikeTheOneAt($authnRequestLogFile)
    {
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);

        // Prefix the filepath with the root dir if it's not an absolute path.
        if ($authnRequestLogFile[0] !== '/') {
            $authnRequestLogFile = OPENCONEXT_ETS_ROOT_DIR . '/' . $authnRequestLogFile;
        }

        // Parse an AuthnRequest out of the log file
        $logReader = LogReader::create($authnRequestLogFile);
        $authnRequest = $logReader->getAuthnRequest();
        $hostname = parse_url($authnRequest->getIssuer(), PHP_URL_HOST);

        $engineBlock = EngineBlock::create($config);
        $engineBlock->overrideHostname($hostname);
        $engineBlock->setNewIdToUse($authnRequest->getId());
    }

    /**
     * @Given /^an Identity Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function anIdentityProviderNamedWithEntityid($name, $entityId)
    {
        $idpFixtureFile = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE)
            ->expect(self::IDP_FIXTURE_CONFIG_NAME);

        $idpFixture = IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile);
        $idpFixture->register($name, $entityId);
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

        // Override the Destination for the Response
        $idpFixture->overrideResponseDestination($idpName, EngineBlock::create($config)->assertionConsumerLocation());
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

        // Determine the ACS URL for the Mock SP
        $serviceProvider = ServiceProvider::create($spName, $spFixture->get($spName), $config);
        $acsUrl = $serviceProvider->assertionConsumerServiceLocation();

        // Override the ACS Location for the SP used in the response to go to the Mock SP
        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->setEntityAcsLocation($authnRequest->getIssuer(), $acsUrl);
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

        // Write out how the IDP should behave
        $config = Config::create(OPENCONEXT_ETS_ROOT_DIR . self::CONFIG_FILE);
        $idpFixtureFile = $config->expect(self::IDP_FIXTURE_CONFIG_NAME);
        $idpFixture = IdpFixture::create(OPENCONEXT_ETS_ROOT_DIR . $idpFixtureFile);
        $idpFixture->configureFromResponse($idpName, $response);

        // Determine the SSO Location for the Mock Idp
        $identityProvider = IdentityProvider::create($idpName, $idpFixture->get($idpName), $config);
        $ssoUrl = $identityProvider->singleSignOnLocation();

        // Override the SSO Location for the IDP used in the response to go to the Mock Idp
        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->setEntitySsoLocation($response->getIssuer(), $ssoUrl);

        $engineBlock = EngineBlock::create($config);
        $engineBlock->overrideTime($response->getIssueInstant());
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
