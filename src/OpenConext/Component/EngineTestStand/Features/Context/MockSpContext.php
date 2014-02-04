<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineTestStand\Fixture\MockIdpsFixture;
use OpenConext\Component\EngineTestStand\Fixture\MockSpsFixture;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineTestStand\Service\LogChunkParser;
use OpenConext\Component\EngineTestStand\Service\MockServiceProviderFactory;
use OpenConext\Component\EngineBlock\Fixture\ServiceRegistryFixture;

class MockSpContext extends AbstractSubContext
{
    protected $mockSpRegistry;
    protected $mockIdpRegistry;
    protected $serviceRegistryFixture;
    protected $spFactory;
    /**
     * @var EngineBlock
     */
    protected $engineBlock;

    public function __construct(
        MockServiceProviderFactory $spFactory,
        MockSpsFixture $spsFixture,
        MockIdpsFixture $idpsFixture,
        ServiceRegistryFixture $serviceRegistryFixture,
        EngineBlock $engineBlock
    ) {
        $this->spFactory = $spFactory;
        $this->mockSpRegistry = $spsFixture;
        $this->mockIdpRegistry = $idpsFixture;
        $this->serviceRegistryFixture = $serviceRegistryFixture;
    }

    /**
     * @When /^I log in at "([^"]*)"$/
     */
    public function iLogInAt($spName)
    {
        $this->getMainContext()->getMinkContext()->visit(
            $this->spFactory->createForName($spName)
                ->loginUrl()
        );
    }

    /**
     * @Given /^a Service Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function aServiceProviderNamedWithEntityid($name, $entityId)
    {
        $this->mockSpRegistry->set($name, new MockServiceProvider($name, $entityId));
    }

    /**
     * @Given /^SP "([^"]*)" may only access "([^"]*)"$/
     */
    public function spMayOnlyAccess($spName, $idpName)
    {
        $spEntityId = $this->mockSpRegistry->get($spName)->entityID;

        $idpEntityId = $this->mockIdpRegistry->get($idpName)->entityID;

        $this->serviceRegistryFixture->blacklist($spEntityId);
        $this->serviceRegistryFixture->allow($spEntityId, $idpEntityId);

        // Override the Destination for the Response
        $this->mockIdpRegistry->overrideResponseDestination($idpName, $this->engineBlock->assertionConsumerLocation());
    }

    /**
     * @Given /^SP "([^"]*)" is configured to generate a AuthnRequest like the one at "([^"]*)"$/
     */
    public function spIsConfiguredToGenerateAAuthnrequestLikeTheOneAt($spName, $authnRequestLogFile)
    {
        // Parse an AuthnRequest out of the log file
        $logReader = new LogChunkParser($authnRequestLogFile);
        $authnRequest = $logReader->getAuthnRequest();

        var_dump($authnRequest);

        // Write out how the SP should behave
        $mockSp = $this->mockSpRegistry->get($spName);
        $mockSp->configureFromAuthnRequest($authnRequest);

        // Determine the ACS URL for the Mock SP
        $serviceProvider = $this->spFactory->createForName($spName);
        $acsUrl = $serviceProvider->assertionConsumerServiceLocation();

        // Override the ACS Location for the SP used in the response to go to the Mock SP
        $this->serviceRegistryFixture->setEntityAcsLocation($authnRequest->getIssuer(), $acsUrl);
    }
}
