<?php

namespace OpenConext\Component\EngineTestStand\Features\Context;

use OpenConext\Component\EngineTestStand\Service\LogChunkParser;

/**
 * Class ReplayContext
 * @package OpenConext\Component\EngineTestStand\Features\Context
 */
class ReplayContext extends AbstractSubContext
{
    /**
     * @Then /^the request should be compared with the one at "([^"]*)"$/
     */
    public function theRequestShouldBeComparedWithTheOneAt($requestLogFile)
    {
        $node = $this->getMainContext()->getMinkContext()->getSession()->getPage()->findById('authnRequestXml');
        if (!$node) {
            throw new \RuntimeException('authnRequestXml id not found on page?');
        }
        $authnRequestXml = trim(html_entity_decode($node->getHtml()));
        if (empty($authnRequestXml)) {
            throw new \RuntimeException('authnRequestXml is on page, but no content found?');
        }

        // Parse a Response out of the log file
        $logReader = new LogChunkParser($requestLogFile);
        $request = $logReader->getAuthnRequest();

        $this->printDebug(print_r($request, true));

        $originalRequestXml = $this->formatXml($request->xml);
        $replayedRequestXml = $this->formatXml($authnRequestXml);

        $this->printDebug($originalRequestXml);
        $this->printDebug($replayedRequestXml);

        $diff = new \Diff(
            explode("\n", $originalRequestXml),
            explode("\n", $replayedRequestXml)
        );
        $renderer = new \Diff_Renderer_Text_Unified;
        echo $diff->render($renderer);
    }

    /**
     * @Then /^the response should be compared with the one at "([^"]*)"$/
     */
    public function theResponseShouldBeComparedWithTheOneAt($responseLogFile)
    {
        // Parse a Response out of the log file
        $logReader = new LogChunkParser($responseLogFile);
        $response = $logReader->getResponse();
        $originalResponseXml = $this->formatXml($response->xml);
        $replayedResponseXml = $this->formatXml($this->getMainContext()->getPageContent());

        $this->printDebug($originalResponseXml);
        $this->printDebug($replayedResponseXml);

        $diff = new \Diff(
            explode("\n", $originalResponseXml),
            explode("\n", $replayedResponseXml)
        );
        $renderer = new \Diff_Renderer_Text_Unified;
        echo $diff->render($renderer);
    }

    /**
     * @param $xml
     * @return string
     */
    protected function formatXml($xml)
    {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml);
        $dom->formatOutput = TRUE;
        return $dom->saveXml();
    }
}
