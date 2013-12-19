<?php

namespace OpenConext\Saml2;

class Response extends \SAML2_Response
{
    /**
     * Constructor for SAML 2 response messages.
     *
     * @param DOMElement|NULL $xml The input message.
     */
    public function __construct(DOMElement $xml = NULL)
    {
        parent::__construct('Response', $xml);

        $assertions = $this->getAssertions();

        if ($xml === NULL) {
            return;
        }

        for ($node = $xml->firstChild; $node !== NULL; $node = $node->nextSibling) {
            if ($node->namespaceURI !== SAML2_Const::NS_SAML) {
                continue;
            }

            if ($node->localName === 'Assertion') {
                $assertions[] = new Assertion($node);
            } elseif ($node->localName === 'EncryptedAssertion') {
                $assertions[] = new \SAML2_EncryptedAssertion($node);
            }
        }
        $this->setAssertions($assertions);
    }
}
