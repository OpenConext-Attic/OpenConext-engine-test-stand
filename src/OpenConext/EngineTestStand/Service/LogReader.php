<?php

namespace OpenConext\EngineTestStand\Service;

class LogReader
{
    protected $logFile;

    public static function create($log)
    {
        return new static($log);
    }

    protected function __construct($logFile)
    {
        $this->logFile = $logFile;
    }

    public function getAuthnRequest()
    {
        $content = file_get_contents($this->logFile);

        return $this->getAuthnRequestFromUrl($content);
    }

    protected function getAuthnRequestFromUrl($content)
    {
        $matches = array();
        if (!preg_match('/SAMLRequest=([A-Za-z0-9+\/%]+)/', $content, $matches)) {
            throw new \RuntimeException("No SAMLRequest found in logfile {$this->logFile}?");
        }

        $request = $matches[1];
        $request = urldecode($request);
        $request = base64_decode($request);
        if (!$request) {
            throw new \RuntimeException("Unable to base64 decode found SAMLRequest: '{$matches[1]}'");
        }
        $request = gzinflate($request);
        if (!$request) {
            throw new \RuntimeException("Unable to gzip inflate found SAMLRequest: '{$matches[1]}'");
        }
        $document = new \DOMDocument();
        $document->loadXML($request);

        return new \SAML2_AuthnRequest($document->firstChild);
    }

    public function getResponse()
    {
        $xml = $this->getResponseXml();

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $response = new \SAML2_Response($document->firstChild);
        $response->xml = $xml;
        return $response;
    }

    public function getResponseXml()
    {
        $content = file_get_contents($this->logFile);

        return $this->getResponseXmlFromLogDump($content);
    }

    protected function getResponseXmlFromLogDump($content)
    {
        $chunkStartMatches = array();
        $chunkEndMatches = array();
        if (!preg_match('/!CHUNKSTART>.+samlp:Response/', $content, $chunkStartMatches) || !preg_match('/!CHUNKEND>/', $content, $chunkEndMatches)) {
            throw new \RuntimeException('No samlp:Response found or incomplete chunk!');
        }

        // Chop off everything before the CHUNKSTART
        $content = substr($content, strpos($content, $chunkStartMatches[0]));
        // ... and after the first newline after CHUNKEND
        $content = substr($content, 0, strpos($content, "\n", strpos($content, $chunkEndMatches[0])));

        // Remove everything before CHUNK>|CHUNKSTART>|CHUNKEND>
        $content = preg_replace('/!CHUNKSTART>\s*/sU', '', $content);
        $content = preg_replace('/\n.+CHUNK>/sU', '', $content);
        $content = preg_replace('/\n.+CHUNKEND>/sU', '', $content);
        // And turn all \n literals into actual newlines
        $content = preg_replace('/\\\n/', "\n", $content);

        $xmlMatches = array();
        if (!preg_match('/<\?xml.+<samlp:Response.+<\/samlp:Response>/s', $content, $xmlMatches)) {
            throw new \RuntimeException('Can not find raw XML Response in log dump!');
        }
        return $xmlMatches[0];
    }
}
