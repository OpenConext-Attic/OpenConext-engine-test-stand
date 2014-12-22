<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use Behat\Behat\Exception\PendingException;
use OpenConext\Component\EngineBlock\LogChunkParser;
use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\MockIdentityProvider;
use OpenConext\Component\EngineTestStand\MockServiceProvider;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineBlockFixtures\ServiceRegistryFixture;
use OpenConext\Component\EngineTestStand\MockIdentityProviderFactory;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class MockIdpContext
 * @package OpenConext\Component\EngineTestStand\Features\Context
 * @SuppressWarnings("PMD")
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
     * @var MockIdentityProviderFactory
     */
    protected $mockIdpFactory;

    /**
     * @var EntityRegistry
     */
    protected $mockIdpRegistry;

    /**
     * @var \OpenConext\Component\EngineTestStand\EntityRegistry
     */
    protected $mockSpRegistry;

    /**
     * @param ServiceRegistryFixture $serviceRegistryFixture
     * @param EngineBlock $engineBlock
     * @param MockIdentityProviderFactory $idpFactory
     * @param EntityRegistry $mockIdpRegistry
     * @param EntityRegistry $mockSpRegistry
     */
    public function __construct(
        ServiceRegistryFixture $serviceRegistryFixture,
        EngineBlock $engineBlock,
        MockIdentityProviderFactory $idpFactory,
        EntityRegistry $mockIdpRegistry,
        EntityRegistry $mockSpRegistry
    ) {
        $this->serviceRegistryFixture = $serviceRegistryFixture;
        $this->engineBlock = $engineBlock;
        $this->mockIdpFactory = $idpFactory;
        $this->mockIdpRegistry = $mockIdpRegistry;
        $this->mockSpRegistry = $mockSpRegistry;
    }

    /**
     * @Given /^an Identity Provider named "([^"]*)"$/
     */
    public function anIdentityProviderNamed($name)
    {
        $mockIdp = $this->mockIdpFactory->createNew($name);
        $this->mockIdpRegistry->set($name, $mockIdp)->save();
        $this->serviceRegistryFixture->registerIdp(
            $name,
            $mockIdp->entityId(),
            $mockIdp->singleSignOnLocation(),
            $mockIdp->publicKeyCertData()
        )->save();
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
        $this->serviceRegistryFixture
            ->setEntitySsoLocation($response->getIssuer(), $ssoUrl)
            ->save();

        $this->engineBlock->overrideTime($response->getIssueInstant());
    }

    /**
     * @Given /^the IdP uses a blacklist for access control$/
     */
    public function theIdpUsesABlacklistForAccessControl()
    {
        $this->serviceRegistryFixture
            ->blacklist($this->mockIdpRegistry->getOnly()->entityId())
            ->save();
    }

    /**
     * @Given /^IdP "([^"]*)" uses a blacklist for access control$/
     */
    public function idpUsesABlacklist($idpName)
    {
        $this->serviceRegistryFixture
            ->blacklist($this->mockIdpRegistry->get($idpName)->entityId())
            ->save();
    }

    /**
     * @Given /^IdP "([^"]*)" uses a whitelist for access control$/
     */
    public function idpUsesAWhitelist($idpName)
    {
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $this->mockIdpRegistry->get($idpName);

        $this->serviceRegistryFixture->whitelist($mockIdp->entityId());

        $this->serviceRegistryFixture->save();
    }

    /**
     * @Given /^IdP "([^"]*)" whitelists SP "([^"]*)"$/
     */
    public function idpWhitelistsSp($idpName, $spName)
    {
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $this->mockIdpRegistry->get($idpName);
        /** @var MockServiceProvider $mockSp */
        $mockSp  = $this->mockSpRegistry->get($spName);

        $this->serviceRegistryFixture->allow($mockSp->entityid(), $mockIdp->entityId());

        $this->serviceRegistryFixture->save();
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
     * @Given /^the IdP thinks it\'s EntityID is "([^"]*)"$/
     */
    public function theIdpThinksItSEntityidIs($entityId)
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();
        $idp->setEntityId($entityId);
        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^the IdP uses the HTTP Redirect Binding$/
     */
    public function theIdpUsesTheHttpRedirectBinding()
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();

        $idp->useHttpRedirect();

        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^the IdP does not send the attribute named "([^"]*)"$/
     */
    public function theIdpDoesNotSendTheAttributeNamed($attributeName)
    {
        /** @var MockIdentityProvider $idp */
        $idp = $this->mockIdpRegistry->getOnly();

        $idp->removeAttribute($attributeName);

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
