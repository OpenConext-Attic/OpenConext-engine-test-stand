<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineBlock\LogChunkParser;
use OpenConext\Component\EngineBlockFixtures\IdFixture;
use OpenConext\Component\EngineBlockFixtures\IdFrame;
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
    /**
     * @var EntityRegistry
     */
    protected $mockSpRegistry;

    /**
     * @var EntityRegistry
     */
    protected $mockIdpRegistry;

    /**
     * @var ServiceRegistryFixture
     */
    protected $serviceRegistryFixture;

    /**
     * @var MockServiceProviderFactory
     */
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
     * @When /^I trigger the login \(either at "([^"]*)" or unsolicited at EB\)$/
     */
    public function iTriggerTheLoginEitherAtOrUnsolicitedAtEb($spName)
    {
        /** @var MockServiceProvider $mockSp */
        $mockSp = $this->mockSpRegistry->get($spName);

        if ($mockSp->mustUseUnsolicited()) {
            $ssoStartLocation = $this->engineBlock->unsolicitedLocation($mockSp->entityId());
        }
        else if ($mockSp->mustUsePost()) {
            $ssoStartLocation = $mockSp->loginUrlPost();
        }
        else {
            $ssoStartLocation = $mockSp->loginUrlRedirect();
        }

        $this->getMainContext()->getMinkContext()->visit($ssoStartLocation);
    }

    /**
     * @Given /^a Service Provider named "([^"]*)"$/
     */
    public function aServiceProviderNamedWithEntityid($name)
    {
        $mockSp = $this->anUnregisteredServiceProviderNamed($name);
        $this->serviceRegistryFixture->registerSp(
            $name,
            $mockSp->entityId(),
            $mockSp->assertionConsumerServiceLocation(),
            $mockSp->publicKeyCertData()
        )->save();
    }

    /**
     * @Given /^an unregistered Service Provider named "([^"]*)"$/
     */
    public function anUnregisteredServiceProviderNamed($name)
    {
        $mockSp = $this->mockSpFactory->createNew($name);
        $this->mockSpRegistry->set($name, $mockSp);
        $this->mockSpRegistry->save();
        return $mockSp;
    }

    /**
     * @Given /^SP "([^"]*)" may only access "([^"]*)"$/
     */
    public function spMayOnlyAccess($spName, $idpName)
    {
        $spEntityId = $this->mockSpRegistry->get($spName)->entityId();

        $idpEntityId = $this->mockIdpRegistry->get($idpName)->entityId();

        $this->serviceRegistryFixture
            ->blacklist($spEntityId)
            ->allow($spEntityId, $idpEntityId)
            ->save();

        // Override the Destination for the Response
        $this->mockIdpRegistry->get($idpName)->overrideResponseDestination(
            $this->engineBlock->assertionConsumerLocation()
        );
        $this->mockIdpRegistry->save();
    }

    /**
     * @Given /^SP "([^"]*)" is configured to generate a AuthnRequest like the one at "([^"]*)"$/
     */
    public function spIsConfiguredToGenerateAAuthnrequestLikeTheOneAt($spName, $authnRequestLogFile)
    {
        /** @var MockServiceProvider $mockSp */
        $mockSp = $this->mockSpRegistry->get($spName);

        $mockSpDefaultEntityId = $mockSp->entityId();
        $mockSpAcsLocation     = $mockSp->assertionConsumerServiceLocation();

        // First see if the request was even triggered by the SP, or if it was an unsolicited request
        // by EB.
        $logReader = new LogChunkParser($authnRequestLogFile);
        $unsolicitedRequest = $logReader->detectUnsolicitedRequest();
        if ($unsolicitedRequest) {
            $this->printDebug("Unsolicited Request:" . PHP_EOL . print_r($unsolicitedRequest, true));

            $mockSp->useUnsolicited();

            $requestIssuer = $unsolicitedRequest['saml:Issuer']['__v'];

            $frame = $this->engineBlock->getIdsToUse(IdFixture::FRAME_REQUEST);
            $frame->set(IdFrame::ID_USAGE_SAML2_REQUEST, $unsolicitedRequest['_ID']);
        } else {
            // If not, then parse an AuthnRequest out of the log file
            $authnRequest = $logReader->getMessage(LogChunkParser::MESSAGE_TYPE_AUTHN_REQUEST);
            $mockSp->setAuthnRequest($authnRequest);
            $this->printDebug(print_r($authnRequest, true));

            $requestIssuer = $authnRequest->getIssuer();
        }

        // Listen up Mock Service Provider, you must now pretend that you are the issuer of the request.
        $mockSp->setEntityId($requestIssuer);

        $this->mockSpRegistry->save();

        // Override the ACS Location for the SP used in the response to go to the Mock SP
        $this->serviceRegistryFixture
            ->remove($mockSpDefaultEntityId)
            ->setEntityAcsLocation($requestIssuer, $mockSpAcsLocation)
            ->save();
    }

    /**
     * @Given /^SP "([^"]*)" may run in transparent mode, if indicated in "([^"]*)"$/
     */
    public function spMayRunInTransparentModeIfIndicatedIn($spName, $sessionLogFIle)
    {
        $logReader = new LogChunkParser($sessionLogFIle);
        $entityId = $logReader->detectTransparentRequest();

        if (!$entityId) {
            return;
        }

        /** @var MockServiceProvider $mockSp */
        $mockSp = $this->mockSpRegistry->get($spName);
        $mockSp->useIdpTransparently($entityId);

        $this->mockSpRegistry->save();
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

    /**
     * @When /^I log in at "([^"]*)"$/
     */
    public function iLogInAt($spName)
    {
        $this->iTriggerTheLoginEitherAtOrUnsolicitedAtEb($spName);
    }

    /**
     * @Given /^the SP signs it\'s requests$/
     */
    public function theSpSignsItSRequests()
    {
        /** @var MockServiceProvider $sp */
        $sp = $this->mockSpRegistry->getOnly();
        $sp->signAuthnRequests();

        $this->mockSpRegistry->save();
    }

    /**
     * @Given /^SP "([^"]*)" uses a blacklist of access control$/
     */
    public function spUsesABlacklistOfAccessControl($spName)
    {
        /** @var MockServiceProvider $sp */
        $sp = $this->mockSpRegistry->get($spName);
        $this->serviceRegistryFixture
            ->blacklist($sp->entityId())
            ->save();
    }

    /**
     * @Given /^SP "([^"]*)" uses a whitelist for access control$/
     */
    public function spUsesAWhitelistForAccessControl($spName)
    {
        /** @var MockServiceProvider $sp */
        $sp = $this->mockSpRegistry->get($spName);
        $this->serviceRegistryFixture
            ->whitelist($sp->entityId())
            ->save();
    }

    /**
     * @Given /^the SP uses the HTTP POST Binding$/
     */
    public function theSpUsesTheHttpPostBinding()
    {
        $sp = $this->mockSpRegistry->getOnly();

        $sp->useHttpPost();

        $this->mockSpRegistry->save();
    }

    /**
     * @Given /^the SP uses the HTTP Redirect Binding$/
     */
    public function theSpUsesTheHttpRedirectBinding()
    {
        $sp = $this->mockSpRegistry->getOnly();

        $sp->useHttpRedirect();

        $this->mockSpRegistry->save();
    }

    /**
     * @Given /^no registered SPs/
     */
    public function noRegisteredServiceProviders()
    {
        $this->mockSpRegistry->clear()->save();
    }

    /**
     * @Given /^I pass through the SP$/
     */
    public function iPassThroughTheSp()
    {
        $mink = $this->getMainContext()->getMinkContext();
        $mink->pressButton('GO');
    }
}
