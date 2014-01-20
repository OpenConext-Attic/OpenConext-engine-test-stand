<?php

namespace OpenConext\EngineTestStand\Features\Context;

use OpenConext\EngineTestStand\Service\EngineBlock;
use OpenConext\EngineTestStand\Service\LogReader;
use OpenConext\EngineTestStand\ServiceRegistry\ServiceRegistryMock;

class EngineBlockContext extends AbstractSubContext
{
    const IDPS_CONFIG_NAME = 'idps-config-url';
    const SPS_CONFIG_NAME  = 'sps-config-url';

    /**
     * @Given /^an EngineBlock instance configured with JSON data$/
     */
    public function anEngineblockInstanceConfiguredWithJsonData()
    {
        $serviceRegistry = ServiceRegistryMock::create();
        $config = $this->getMainContext()->getApplicationConfig();

        // Add all known IdPs
        $serviceRegistry->reset();
        $serviceRegistry->addSpsFromJsonExport($config->expect(self::SPS_CONFIG_NAME));
        $serviceRegistry->addIdpsFromJsonExport($config->expect(self::IDPS_CONFIG_NAME));

        $eb = EngineBlock::create($config);
        $eb->clearNewIds();
    }

    /**
     * @Given /^EngineBlock is expected to send a AuthnRequest like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAAuthnrequestLikeTheOneAt($authnRequestLogFile)
    {
        $config = $this->getMainContext()->getApplicationConfig();

        // Parse an AuthnRequest out of the log file
        $logReader = LogReader::create($authnRequestLogFile);
        $authnRequest = $logReader->getAuthnRequest();
        $hostname = parse_url($authnRequest->getIssuer(), PHP_URL_HOST);

        $engineBlock = EngineBlock::create($config);
        $engineBlock->overrideHostname($hostname);
        $engineBlock->setNewIdsToUse(array(EngineBlock::ID_USAGE_SAML2_REQUEST => $authnRequest->getId()));
    }

    /**
     * @Given /^EngineBlock is expected to send a Response like the one at "([^"]*)"$/
     */
    public function engineblockIsExpectedToSendAResponseLikeTheOneAt($responseLogFile)
    {
        $config = $this->getMainContext()->getApplicationConfig();

        // Prefix the filepath with the root dir if it's not an absolute path.
        if ($responseLogFile[0] !== '/') {
            $responseLogFile = OPENCONEXT_ETS_ROOT_DIR . '/' . $responseLogFile;
        }

        // Parse an AuthnRequest out of the log file
        $logReader = LogReader::create($responseLogFile);
        $response = $logReader->getResponse();
        $responseAssertions = $response->getAssertions();

        $engineBlock = EngineBlock::create($config);
        $engineBlock->setNewIdsToUse(array(
            EngineBlock::ID_USAGE_SAML2_RESPONSE => $response->getId(),
            EngineBLock::ID_USAGE_SAML2_ASSERTION => $responseAssertions[0]->getId(),
        ));
    }
}
