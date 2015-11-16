<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use Behat\Behat\Exception\PendingException;
use OpenConext\Component\EngineBlockFixtures\IdFixture;
use OpenConext\Component\EngineBlockFixtures\IdFrame;
use OpenConext\Component\EngineBlock\LogChunkParser;
use OpenConext\Component\EngineTestStand\EntityRegistry;
use OpenConext\Component\EngineTestStand\MockIdentityProvider;
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
     * @var ServiceRegistryFixture
     */
    private $serviceRegistryFixture;

    /**
     * @var EngineBlock
     */
    private $engineBlock;

    /**
     * @var EntityRegistry
     */
    private $mockIdpRegistry;

    /**
     *
     */
    public function __construct(
        ServiceRegistryFixture $serviceRegistry,
        EngineBlock $engineBlock,
        EntityRegistry $mockIdpRegistry,
        $spsConfigUrl,
        $idpsConfigUrl
    ) {
        $this->serviceRegistryFixture = $serviceRegistry;
        $this->engineBlock = $engineBlock;
        $this->mockIdpRegistry = $mockIdpRegistry;
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
                "OpenConext EngineBlock",
                "https://engine.$domain/authentication/sp/metadata",
                "https://engine.$domain/authentication/sp/consume-assertion"
            )
            ->registerIdp(
                "OpenConext EngineBlock",
                "https://engine.$domain/authentication/idp/metadata",
                "https://engine.$domain/authentication/idp/single-sign-on"
            )
            ->save();
        $this->engineBlock->clearNewIds();
    }

    /**
     * @Given /^an EngineBlock instance configured with JSON data$/
     */
    public function anEngineblockInstanceConfiguredWithJsonData()
    {
        // Add all known IdPs
        $this->serviceRegistryFixture
            ->reset()
            ->addSpsFromJsonExport($this->spsConfigUrl)
            ->addIdpsFromJsonExport($this->idpsConfigUrl)
            ->save();

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
            $this->getMainContext()->getMinkContext()->pressButton('submitbutton');
            return;
        }

        // Default to HTTP-Redirect
        $this->getMainContext()->getMinkContext()->clickLink('GO');
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

        $this->engineBlock->getIdsToUse(IdFixture::FRAME_RESPONSE)
        // EB will generate internal responses, for now just let it give all Responses the same id
            ->set(IdFrame::ID_USAGE_SAML2_RESPONSE, $response->getId())
            ->set(IdFrame::ID_USAGE_SAML2_ASSERTION, $responseAssertions[0]->getId())
            ->set(IdFrame::ID_USAGE_SAML2_RESPONSE, $response->getId())
            ->set(IdFrame::ID_USAGE_SAML2_ASSERTION, $responseAssertions[0]->getId())
            ->set(IdFrame::ID_USAGE_SAML2_RESPONSE, $response->getId())
            ->set(IdFrame::ID_USAGE_SAML2_ASSERTION, $responseAssertions[0]->getId());
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

    /**
     * @Given /^I give my consent$/
     */
    public function iGiveMyConsent()
    {
        $mink = $this->getMainContext()->getMinkContext();
        if (strstr($mink->getSession()->getPage()->getHtml(), 'accept_terms_button')) {
            $mink->pressButton('accept_terms_button');
        }
    }

    /**
     * @Given /^I select "([^"]*)" on the WAYF$/
     */
    public function iSelectOnTheWAYF($idpName)
    {
        /** @var MockIdentityProvider $mockIdp */
        $mockIdp = $this->mockIdpRegistry->get($idpName);

        if (!$mockIdp) {
            throw new \RuntimeException(
                "Unable to find idp with name '$idpName'"
            );
        }

        $selector = 'input[type="submit"][data-entityid="' . $mockIdp->entityId() . '"]';

        $mink = $this->getMainContext()->getMinkContext()->getSession()->getPage();
        $button = $mink->find('css', $selector);

        if (!$button) {
            throw new \RuntimeException(
                "Unable to find button with selector '$selector'"
            );
        }
        $button->click();
    }
}
