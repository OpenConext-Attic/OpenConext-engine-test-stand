<?php

namespace OpenConext\EngineTestStand\Service;

use OpenConext\Corto\XmlToArray;
use OpenConext\Php\PrintRParser;

class LogReader
{
    protected $logFile;

    public static function create($file)
    {
        // Absolutize file path.
        if (substr($file, 0, 1) !== '/') {
            $file = __DIR__ . '/../../../../' . $file;
        }

        if (!is_file($file)) {
            throw new \RuntimeException("Can not find log file '$file'.");
        }

        return new static($file);
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

        $authnRequest = new \SAML2_AuthnRequest($document->firstChild);
        $authnRequest->xml = $request;
        return $authnRequest;
    }

    public function getResponse()
    {
        $response = $this->getResponseStructure();

        if (isset($response['__']['Raw'])) {
            $xml = $response['__']['Raw'];
        }
        else {
            $xml = XmlToArray::array2xml($response);
        }

        $document = new \DOMDocument();
        $document->loadXML($xml);

        $response = new \SAML2_Response($document->firstChild);
        $response->xml = $xml;
        return $response;
    }

    public function getResponseStructure()
    {
        $content = $this->getContent();
        $content = $this->cleanContent($content);

        $parser = new PrintRParser($content);
        $response = $parser->parse();

        return $response;
    }

    protected function getContent()
    {
        return file_get_contents($this->logFile);
    }

    protected function cleanContent($content)
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

        return $content;
    }
}

