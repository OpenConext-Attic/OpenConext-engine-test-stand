<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineBlock\LogChunkParser;
use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\MockServiceProvider;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineTestStand\MockServiceProviderFactory;
use OpenConext\Component\EngineBlockFixtures\ServiceRegistryFixture;

/**
 * Class MockSpContext
 * @package OpenConext\Component\EngineTestStand\Features\Context
 */
class MockSpContext extends AbstractSubContext
{
    protected $mockSpRegistry;
    protected $mockIdpRegistry;
    protected $serviceRegistryFixture;
    protected $mockSpFactory;
    /**
     * @var EngineBlock
     */
    protected $engineBlock;

    /**
     * @param ServiceRegistryFixture $serviceRegistryFixture
     * @param EngineBlock $engineBlock
     * @param MockServiceProviderFactory $mockSpFactory
     * @param EntityRegistry $mockSpRegistry
     * @param EntityRegistry $mockIdpRegistry
     */
    public function __construct(
        ServiceRegistryFixture $serviceRegistryFixture,
        EngineBlock $engineBlock,
        MockServiceProviderFactory $mockSpFactory,
        EntityRegistry $mockSpRegistry,
        EntityRegistry $mockIdpRegistry
    ) {
        $this->serviceRegistryFixture = $serviceRegistryFixture;
        $this->engineBlock = $engineBlock;
        $this->mockSpFactory = $mockSpFactory;
        $this->mockSpRegistry = $mockSpRegistry;
        $this->mockIdpRegistry = $mockIdpRegistry;
    }

    /**
     * @When /^I log in at "([^"]*)"$/
     */
    public function iLogInAt($spName)
    {
        $this->getMainContext()->getMinkContext()->visit(
            $this->mockSpRegistry->get($spName)
                ->loginUrl()
        );
    }

    /**
     * @Given /^a Service Provider named "([^"]*)"$/
     */
    public function aServiceProviderNamedWithEntityid($name)
    {
        $mockSp = $this->mockSpFactory->createNew($name);
        $this->mockSpRegistry->set($name, $mockSp);
        $this->serviceRegistryFixture->registerSp($mockSp->entityId(), $mockSp->assertionConsumerServiceLocation());
    }

    /**
     * @Given /^SP "([^"]*)" may only access "([^"]*)"$/
     */
    public function spMayOnlyAccess($spName, $idpName)
    {
        $spEntityId = $this->mockSpRegistry->get($spName)->entityId();

        $idpEntityId = $this->mockIdpRegistry->get($idpName)->entityId();

        $this->serviceRegistryFixture->blacklist($spEntityId);
        $this->serviceRegistryFixture->allow($spEntityId, $idpEntityId);

        // Override the Destination for the Response
        $this->mockIdpRegistry->get($idpName)->overrideResponseDestination(
            $this->engineBlock->assertionConsumerLocation()
        );
    }

    /**
     * @Given /^SP "([^"]*)" is configured to generate a AuthnRequest like the one at "([^"]*)"$/
     */
    public function spIsConfiguredToGenerateAAuthnrequestLikeTheOneAt($spName, $authnRequestLogFile)
    {
        // Parse an AuthnRequest out of the log file
        $logReader = new LogChunkParser($authnRequestLogFile);
        $authnRequest = $logReader->getMessage(LogChunkParser::MESSAGE_TYPE_AUTHN_REQUEST);

        $this->printDebug(print_r($authnRequest, true));

        // Write out how the SP should behave
        /** @var MockServiceProvider $mockSp */
        $mockSp = $this->mockSpRegistry->get($spName);

        $oldEntityId = $mockSp->entityId();
        $newEntityId = $authnRequest->getIssuer();

        $mockSp
            ->setEntityId($newEntityId)
            ->setAuthnRequest($authnRequest);

        // Determine the ACS URL for the Mock SP
        $acsUrl = $mockSp->assertionConsumerServiceLocation();

        // Override the ACS Location for the SP used in the response to go to the Mock SP
        $this->serviceRegistryFixture
            ->remove($oldEntityId)
            ->setEntityAcsLocation($newEntityId, $acsUrl);
    }

    /**
     * @Given /^SP "([^"]*)" does not require consent$/
     */
    public function spDoesNotRequireConsent($spName)
    {
        /** @var MockServiceProvider $mockSp */
        $mockSp = $this->mockSpRegistry->get($spName);

        $this->serviceRegistryFixture->setEntityNoConsent($mockSp->entityId());
    }
}
