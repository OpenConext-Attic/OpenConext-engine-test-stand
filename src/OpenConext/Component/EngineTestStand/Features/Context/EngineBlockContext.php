<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineBlockFixtures\IdFixture;
use OpenConext\Component\EngineBlockFixtures\IdFrame;
use OpenConext\Component\EngineBlock\LogChunkParser;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineBlockFixtures\ServiceRegistryFixture;

/**
 * Class EngineBlockContext
 * @package OpenConext\Component\EngineTestStand\Features\Context
 */
class EngineBlockContext extends AbstractSubContext
{
    /**
     * @var string
     */
    protected $idpsConfigUrl;

    /**
     * @var string
     */
    protected $spsConfigUrl;

    /**
     *
     */
    public function __construct(
        ServiceRegistryFixture $serviceRegistry,
        EngineBlock $engineBlock,
        $spsConfigUrl,
        $idpsConfigUrl
    ) {
        $this->serviceRegistryFixture = $serviceRegistry;
        $this->engineBlock = $engineBlock;
        $this->spsConfigUrl = $spsConfigUrl;
        $this->idpsConfigUrl = $idpsConfigUrl;
    }

    /**
     * @Given /^an EngineBlock instance on "([^"]*)"$/
     */
    public function anEngineBlockInstanceOn($domain)
    {
        // Add all known IdPs
        $this->serviceRegistryFixture
            ->reset()
            ->registerSp(
                "https://engine.$domain/authentication/sp/metadata",
                "https://engine.$domain/authentication/sp/consume-assertion"
            )
            ->registerIdp(
                "https://engine.$domain/authentication/idp/metadata",
                "https://engine.$domain/authentication/idp/single-sign-on"
            );
        $this->engineBlock->clearNewIds();
    }

    /**
     * @Given /^an EngineBlock instance configured with JSON data$/
     */
    public function anEngineblockInstanceConfiguredWithJsonData()
    {
        // Add all known IdPs
        $this->serviceRegistryFixture->reset();
        $this->serviceRegistryFixture->addSpsFromJsonExport($this->spsConfigUrl);
        $this->serviceRegistryFixture->addIdpsFromJsonExport($this->idpsConfigUrl);

        $this->engineBlock->clearNewIds();
    }

    /**
     * @Given /^I follow the EB debug screen to the IdP$/
     */
    public function iFollowTheEbDebugScreenToTheIdp()
    {
        // Support for HTTP-Post
        $hasSubmitButton = $this->getMainContext()->getMinkContext()->getSession()->getPage()->findButton('Submit');
        if ($hasSubmitButton) {
            return $this->getMainContext()->getMinkContext()->pressButton('submitbutton');
        }

        // Default to HTTP-Redirect
        return $this->getMainContext()->getMinkContext()->clickLink('GO');
    }

    /**
     * @Given /^EngineBlock is expected to send a AuthnRequest like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAAuthnrequestLikeTheOneAt($authnRequestLogFile)
    {
        // Parse an AuthnRequest out of the log file
        $logReader = new LogChunkParser($authnRequestLogFile);
        $authnRequest = $logReader->getMessage(LogChunkParser::MESSAGE_TYPE_AUTHN_REQUEST);

        $hostname = parse_url($authnRequest->getIssuer(), PHP_URL_HOST);
        $this->engineBlock->overrideHostname($hostname);

        $frame = $this->engineBlock->getIdsToUse(IdFixture::FRAME_REQUEST);
        $frame->set(IdFrame::ID_USAGE_SAML2_REQUEST, $authnRequest->getId());
    }

    /**
     * @Given /^EngineBlock is expected to send a Response like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAResponseLikeTheOneAt($responseLogFile)
    {
        // Parse an AuthnRequest out of the log file
        $logReader = new LogChunkParser($responseLogFile);
        $response = $logReader->getMessage(LogChunkParser::MESSAGE_TYPE_RESPONSE);
        $responseAssertions = $response->getAssertions();

        $frame = $this->engineBlock->getIdsToUse(IdFixture::FRAME_RESPONSE);
        // EB will generate internal responses, for now just let it give all Responses the same id
        $frame->set(IdFrame::ID_USAGE_SAML2_RESPONSE, $response->getId());
        $frame->set(IdFrame::ID_USAGE_SAML2_ASSERTION, $responseAssertions[0]->getId());
        $frame->set(IdFrame::ID_USAGE_SAML2_RESPONSE, $response->getId());
        $frame->set(IdFrame::ID_USAGE_SAML2_ASSERTION, $responseAssertions[0]->getId());
        $frame->set(IdFrame::ID_USAGE_SAML2_RESPONSE, $response->getId());
        $frame->set(IdFrame::ID_USAGE_SAML2_ASSERTION, $responseAssertions[0]->getId());
    }

    /**
     * @Given /^I print the configured ids$/
     */
    public function iPrintTheConfiguredIds()
    {
        $idFixture = $this->engineBlock->getIdFixture();
        $this->printDebug(print_r($idFixture));
    }

    /**
     * @Given /^I pass through EngineBlock$/
     */
    public function iPassThroughEngineblock()
    {
        $mink = $this->getMainContext()->getMinkContext();
        $mink->pressButton('Submit');
    }
}
