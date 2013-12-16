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

        $matches = [];
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
}
