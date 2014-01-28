<?php

namespace OpenConext\EngineTestStand\Features\Context;

use OpenConext\EngineTestStand\Service\LogReader;

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
        $logReader = LogReader::create($requestLogFile);
        $request = $logReader->getAuthnRequest();
        var_dump($request);
        $originalRequestXml = $this->formatXml($request->xml);
        $replayedRequestXml = $this->formatXml($authnRequestXml);

        var_dump($originalRequestXml);
        var_dump($replayedRequestXml);

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
        $logReader = LogReader::create($responseLogFile);
        $response = $logReader->getResponse();
        $originalResponseXml = $this->formatXml($response->xml);
        $replayedResponseXml = $this->formatXml($this->getMainContext()->getPageContent());

        var_dump($originalResponseXml);
        var_dump($replayedResponseXml);

        $diff = new \Diff(
            explode("\n", $originalResponseXml),
            explode("\n", $replayedResponseXml)
        );
        $renderer = new \Diff_Renderer_Text_Unified;
        echo $diff->render($renderer);
    }

    protected function formatXml($xml)
    {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml);
        $dom->formatOutput = TRUE;
        return $dom->saveXml();
    }
}
