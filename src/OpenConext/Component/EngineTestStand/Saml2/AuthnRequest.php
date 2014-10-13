<?php

namespace OpenConext\Component\EngineTestStand\Saml2;

class AuthnRequest extends \SAML2_Response
{
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $xml;
    }

    public function toXml()
    {
        if (isset($this->xml)) {
            return $this->xml;
        }

        return $this->toUnsignedXML()->ownerDocument->saveXML();
    }
}