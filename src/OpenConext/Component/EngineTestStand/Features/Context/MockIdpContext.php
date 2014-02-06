<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineBlock\LogChunkParser;
use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\MockIdentityProvider;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineBlockFixtures\ServiceRegistryFixture;
use OpenConext\Component\EngineTestStand\MockIdentityProviderFactory;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class MockIdpContext
 * @package OpenConext\Component\EngineTestStand\Features\Context
 */
class MockIdpContext extends AbstractSubContext
{
    /**
     * @var ServiceRegistryFixture
     */
    protected $serviceRegistryFixture;

    /**
     * @var EngineBlock
     */
    protected $engineBlock;

    /**
     * @var ParameterBag
     */
    protected $mockIdpRegistry;

    /**
     * @var MockIdentityProviderFactory
     */
    protected $mockIdpFactory;

    /**
     * @param ServiceRegistryFixture $serviceRegistryFixture
     * @param EngineBlock $engineBlock
     * @param EntityRegistry $mockIdpRegistry
     * @param MockIdentityProviderFactory $idpFactory
     */
    public function __construct(
        ServiceRegistryFixture $serviceRegistryFixture,
        EngineBlock $engineBlock,
        MockIdentityProviderFactory $idpFactory,
        EntityRegistry $mockIdpRegistry
    ) {
        $this->serviceRegistryFixture = $serviceRegistryFixture;
        $this->engineBlock = $engineBlock;
        $this->mockIdpFactory = $idpFactory;
        $this->mockIdpRegistry = $mockIdpRegistry;
    }

    /**
     * @Given /^an Identity Provider named "([^"]*)"$/
     */
    public function anIdentityProviderNamedWithEntityid($name)
    {
        $this->mockIdpRegistry->set($name, $this->mockIdpFactory->createNew($name));
    }

    /**
     * @Given /^IdP "([^"]*)" is configured to return a Response like the one at "([^"]*)"$/
     */
    public function idpIsConfiguredToReturnAResponseLikeTheOneAt($idpName, $responseLogFile)
    {
        // Parse a Response out of the log file
        $logReader = new LogChunkParser($responseLogFile);
        $response = $logReader->getResponse();

        $this->printDebug(print_r($response, true));

        // Write out how the IDP should behave
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $this->mockIdpRegistry->get($idpName);
        $mockIdp->setResponse($response);

        $ssoUrl = $mockIdp->singleSignOnLocation();

        // Override the SSO Location for the IDP used in the response to go to the Mock Idp
        $this->serviceRegistryFixture->setEntitySsoLocation($response->getIssuer(), $ssoUrl);

        $this->engineBlock->overrideTime($response->getIssueInstant());
    }
}
