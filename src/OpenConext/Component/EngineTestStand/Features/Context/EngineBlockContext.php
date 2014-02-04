<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineBlock\Fixture\IdFrame;
use OpenConext\Component\EngineTestStand\Service\EngineBlock;
use OpenConext\Component\EngineTestStand\Service\LogChunkParser;
use OpenConext\Component\EngineBlock\Fixture\ServiceRegistryFixture;

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
     * @Given /^EngineBlock is expected to send a AuthnRequest like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAAuthnrequestLikeTheOneAt($authnRequestLogFile)
    {
        // Parse an AuthnRequest out of the log file
        $logReader = new LogChunkParser($authnRequestLogFile);
        $authnRequest = $logReader->getAuthnRequest();

        $hostname = parse_url($authnRequest->getIssuer(), PHP_URL_HOST);
        $this->engineBlock->overrideHostname($hostname);

        $this->engineBlock->setNewIdsToUse(new IdFrame(array(
            IdFrame::ID_USAGE_SAML2_REQUEST => $authnRequest->getId()
        )));
    }

    /**
     * @Given /^EngineBlock is expected to send a Response like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAResponseLikeTheOneAt($responseLogFile)
    {
        // Parse an AuthnRequest out of the log file
        $logReader = new LogChunkParser($responseLogFile);
        $response = $logReader->getResponse();
        $responseAssertions = $response->getAssertions();

        $this->engineBlock->setNewIdsToUse(new IdFrame(array(
            IdFrame::ID_USAGE_SAML2_RESPONSE  => $response->getId(),
            IdFrame::ID_USAGE_SAML2_ASSERTION => $responseAssertions[0]->getId(),
        )));
    }
}
