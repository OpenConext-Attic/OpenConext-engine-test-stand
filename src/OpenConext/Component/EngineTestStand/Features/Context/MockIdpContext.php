<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use Behat\Behat\Exception\PendingException;
use OpenConext\Component\EngineTestStand\Fixture\MockIdpsFixture;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineTestStand\Service\IdentityProvider;
use OpenConext\Component\EngineTestStand\Service\LogChunkParser;
use OpenConext\Component\EngineBlock\Fixture\ServiceRegistryFixture;
use OpenConext\Component\EngineTestStand\Service\MockIdentityProvider;
use OpenConext\Component\EngineTestStand\Service\MockIdentityProviderFactory;
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
    protected $idpFactory;

    /**
     * @param ServiceRegistryFixture        $serviceRegistryFixture
     * @param EngineBlock                   $engineBlock
     * @param ParameterBag                  $mockIdpRegistry
     * @param MockIdentityProviderFactory   $idpFactory
     */
    protected function __construct(
        ServiceRegistryFixture $serviceRegistryFixture,
        EngineBlock $engineBlock,
        ParameterBag $mockIdpRegistry,
        MockIdentityProviderFactory $idpFactory
    ) {
        $this->serviceRegistryFixture = $serviceRegistryFixture;
        $this->engineBlock = $engineBlock;
        $this->mockIdpRegistry = $mockIdpRegistry;
        $this->idpFactory = $idpFactory;
    }

    /**
     * @Given /^an Identity Provider named "([^"]*)" with EntityID "([^"]*)"$/
     */
    public function anIdentityProviderNamedWithEntityid($name, $entityId)
    {
        $this->mockIdpRegistry->set($name, new MockIdentityProvider($name, $entityId));
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
        $mockIdp = $this->mockIdpRegistry->get($idpName);
        $mockIdp->configureFromResponse($response);

        $ssoUrl = $mockIdp->singleSignOnLocation();

        // Override the SSO Location for the IDP used in the response to go to the Mock Idp
        $this->serviceRegistryFixture->setEntitySsoLocation($response->getIssuer(), $ssoUrl);

        $this->engineBlock->overrideTime($response->getIssueInstant());
    }
}
