<?php

namespace OpenConext\EngineTestStand\Features\Context;

use OpenConext\EngineTestStand\Fixture\IdpFixture;
use OpenConext\EngineTestStand\Fixture\SpFixture;
use OpenConext\EngineTestStand\Service\EngineBlock;
use OpenConext\EngineTestStand\Service\LogReader;
use OpenConext\EngineTestStand\Service\ServiceProvider;
use OpenConext\EngineTestStand\Fixture\ServiceRegistryFixture;
use OpenConext\EngineTestStand\ServiceRegistry\ServiceRegistryMock;

class MockSpContext extends AbstractSubContext
{
    const SP_FIXTURE_CONFIG_NAME = 'sp-fixture-file';

    /**
     * @When /^I log in at "([^"]*)"$/
     */
    public function iLogInAt($spName)
    {
        $config  = $this->getMainContext()->getApplicationConfig();
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $fixture = SpFixture::create($spFixtureFile);

        $serviceProvider = ServiceProvider::create($spName, $fixture->get($spName), $config);
        $loginUrl = $serviceProvider->loginUrl();

        $this->getMainContext()->getMinkContext()->visit($loginUrl);
    }

    /**
     * @Given /^a Service Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function aServiceProviderNamedWithEntityid($name, $entityId)
    {
        $config = $this->getMainContext()->getApplicationConfig();
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);

        $spFixture = SpFixture::create($spFixtureFile);
        $spFixture->register($name, $entityId);
    }

    /**
     * @Given /^SP "([^"]*)" may only access "([^"]*)"$/
     */
    public function spMayOnlyAccess($spName, $idpName)
    {
        $config = $this->getMainContext()->getApplicationConfig();
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $spFixture = SpFixture::create($spFixtureFile);
        $spEntityId = $spFixture->get($spName)->entityID;

        $idpFixtureFile = $config->expect(MockIdpContext::IDP_FIXTURE_CONFIG_NAME);
        $idpFixture = IdpFixture::create($idpFixtureFile);
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
        // Parse an AuthnRequest out of the log file
        $logReader = LogReader::create($authnRequestLogFile);
        $authnRequest = $logReader->getAuthnRequest();

        var_dump($authnRequest);

        // Write out how the SP should behave
        $config = $this->getMainContext()->getApplicationConfig();
        $spFixtureFile = $config->expect(self::SP_FIXTURE_CONFIG_NAME);
        $spFixture = SpFixture::create($spFixtureFile);
        $spFixture->configureFromAuthnRequest($spName, $authnRequest);

        // Determine the ACS URL for the Mock SP
        $serviceProvider = ServiceProvider::create($spName, $spFixture->get($spName), $config);
        $acsUrl = $serviceProvider->assertionConsumerServiceLocation();

        // Override the ACS Location for the SP used in the response to go to the Mock SP
        $serviceRegistry = ServiceRegistryMock::create();
        $serviceRegistry->setEntityAcsLocation($authnRequest->getIssuer(), $acsUrl);
    }
}
