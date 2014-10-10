<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use Behat\Behat\Exception\PendingException;
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
     * @var EntityRegistry
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
    public function anIdentityProviderNamed($name)
    {
        $mockIdp = $this->mockIdpFactory->createNew($name);
        $this->mockIdpRegistry->set($name, $mockIdp);
        $this->serviceRegistryFixture->registerIdp(
            $mockIdp->entityId(),
            $mockIdp->singleSignOnLocation(),
            $mockIdp->publicKeyCertData()
        );

        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^IdP "([^"]*)" is configured to return a Response like the one at "([^"]*)"$/
     */
    public function idpIsConfiguredToReturnAResponseLikeTheOneAt($idpName, $responseLogFile)
    {
        // Parse a Response out of the log file
        $logReader = new LogChunkParser($responseLogFile);
        $response = $logReader->getMessage(LogChunkParser::MESSAGE_TYPE_RESPONSE);

        $this->printDebug(print_r($response, true));

        // Write out how the IDP should behave
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $this->mockIdpRegistry->get($idpName);
        $mockIdp->setResponse($response);
        $this->mockIdpRegistry->save();

        $ssoUrl = $mockIdp->singleSignOnLocation();

        // Override the SSO Location for the IDP used in the response to go to the Mock Idp
        $this->serviceRegistryFixture->setEntitySsoLocation($response->getIssuer(), $ssoUrl);

        $this->engineBlock->overrideTime($response->getIssueInstant());
    }

    /**
     * @Given /^the IdP uses a blacklist for access control$/
     */
    public function theIdpUsesABlacklistForAccessControl()
    {
        $this->serviceRegistryFixture->blacklist($this->mockIdpRegistry->getOnly()->entityId());
    }

    /**
     * @Given /^the IdP is configured to always return Responses with StatusCode (\w+)\/(\w+)$/
     */
    public function theIdpIsConfiguredToAlwaysReturnResponsesWithStatuscode($topStatusCode, $secondStatusCode)
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();
        $idp->setStatusCode($topStatusCode, $secondStatusCode);
        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^the IdP is configured to always return Responses with StatusMessage "([^"]*)"$/
     */
    public function theIdpIsConfiguredToAlwaysReturnResponsesWithStatusmessage($statusMessage)
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();
        $idp->setStatusMessage($statusMessage);
        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^the IdP uses the private key at "([^"]*)"$/
     */
    public function theIdpUsesThePrivateKeyAt($privateKeyFile)
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();
        $idp->setPrivateKey($privateKeyFile);
        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^the IdP uses the certificate at "([^"]*)"$/
     */
    public function theIdpUsesTheCertificateAt($publicKeyFile)
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();
        $idp->setCertificate($publicKeyFile);
        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^no registered Idps/
     */
    public function noRegisteredIdentityProviders()
    {
        $this->mockIdpRegistry->clear()->save();
    }

    /**
     * @Given /^I pass through the IdP$/
     */
    public function iPassThroughTheIdp()
    {
        $mink = $this->getMainContext()->getMinkContext();
        $mink->pressButton('GO');
    }
}
