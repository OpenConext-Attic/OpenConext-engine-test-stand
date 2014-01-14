<?php

namespace OpenConext\Php;

require __DIR__ . '/../../../vendor/autoload.php';

class PrintRParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParser()
    {
        $content = <<<CONTENT
Array
(
    [__t] => samlp:Response
    [__] => Array
        (
            [paramname] => SAMLResponse
            [RelayState] => https://profile.acc.surfconext.nl/
            [destinationid] => https://profile.acc.surfconext.nl/simplesaml/module.php/saml/sp/metadata.php/default-sp
            [ProtocolBinding] => urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST
            [OriginalResponse] => Array
                (
                    [__t] => samlp:Response
                    [_ID] => _397e7b0f86f8a63dd5aa5d728fdde7710f08b17377
                    [_InResponseTo] => CORTO0486c083eec648d4fd99c570d3c2a084fe007ea7
                    [_Version] => 2.0
                    [_IssueInstant] => 2013-12-09T09:25:43Z
                    [_Destination] => https://engine.acc.surfconext.nl/authentication/sp/consume-assertion
                    [saml:Issuer] => Array
                        (
                            [__v] => https://surfguest.nl
                        )

                    [samlp:Status] => Array
                        (
                            [samlp:StatusCode] => Array
                                (
                                    [_Value] => urn:oasis:names:tc:SAML:2.0:status:Success
                                )

                        )

                    [saml:Assertion] => Array
                        (
                            [_Version] => 2.0
                            [_ID] => pfx4f3b5c51-fa08-2355-87d9-119d889c7c31
                            [_IssueInstant] => 2013-12-09T09:25:43Z
                            [saml:Issuer] => Array
                                (
                                    [__v] => https://surfguest.nl
                                )

                            [ds:Signature] => Array
                                (
                                    [ds:SignedInfo] => Array
                                        (
                                            [ds:CanonicalizationMethod] => Array
                                                (
                                                    [_Algorithm] => http://www.w3.org/2001/10/xml-exc-c14n#
                                                )

                                            [ds:SignatureMethod] => Array
                                                (
                                                    [_Algorithm] => http://www.w3.org/2000/09/xmldsig#rsa-sha1
                                                )

                                            [ds:Reference] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [_URI] => #pfx4f3b5c51-fa08-2355-87d9-119d889c7c31
                                                            [ds:Transforms] => Array
                                                                (
                                                                    [ds:Transform] => Array
                                                                        (
                                                                            [0] => Array
                                                                                (
                                                                                    [_Algorithm] => http://www.w3.org/2000/09/xmldsig#enveloped-signature
                                                                                )

                                                                            [1] => Array
                                                                                (
                                                                                    [_Algorithm] => http://www.w3.org/2001/10/xml-exc-c14n#
                                                                                )

                                                                        )

                                                                )

                                                            [ds:DigestMethod] => Array
                                                                (
                                                                    [_Algorithm] => http://www.w3.org/2000/09/xmldsig#sha1
                                                                )

                                                            [ds:DigestValue] => Array
                                                                (
                                                                    [__v] => 19svEWuYOELBRX4+imwseUFOWRA=
                                                                )

                                                        )

                                                )

                                        )

                                    [ds:SignatureValue] => Array
                                        (
                                            [__v] => JtNUWqIHb5hTf67AFe0nnafoWHGuH8zpdZwqrmyLeP/ZKvS1xSfhNheUbxJ42iB7l5nod5tG0DHSgp6bOZZVqYZGtj8zu+pt41BzTAuMbY4aSQJDFJrX2XCbrkgdTLsQKqjNygGGXd3ajr3rT+LfPr8UMQkiIn8FEnYI19+bH1KeqSzP8nA8Fu7W5BOIifvwuAfiJPjauMdVJC3I3MrgKgDocy0Z3sFF+bNGf1kSKSsZ2bKE0rA4axDMfQpo8S7xMdZbI35U1rs4AD0UauiWC8n7nm6pIGOFyF6KZ/5uScMX9oEWyvpXVUC84tHr3EGGkMwvyYDXOSoyIZ+ZGcsobg==
                                        )

                                )

                            [saml:Subject] => Array
                                (
                                    [saml:NameID] => Array
                                        (
                                            [_Format] => urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
                                            [__v] => urn:collab:person:surfguest.nl:joostd
                                        )

                                    [saml:SubjectConfirmation] => Array
                                        (
                                            [_Method] => urn:oasis:names:tc:SAML:2.0:cm:bearer
                                            [saml:SubjectConfirmationData] => Array
                                                (
                                                    [_NotOnOrAfter] => 2013-12-09T09:30:43Z
                                                    [_InResponseTo] => CORTO0486c083eec648d4fd99c570d3c2a084fe007ea7
                                                    [_Recipient] => https://engine.acc.surfconext.nl/authentication/sp/consume-assertion
                                                )

                                        )

                                )

                            [saml:Conditions] => Array
                                (
                                    [_NotBefore] => 2013-12-09T09:25:13Z
                                    [_NotOnOrAfter] => 2013-12-09T09:30:43Z
                                    [saml:AudienceRestriction] => Array
                                        (
                                            [saml:Audience] => Array
                                                (
                                                    [__v] => https://engine.acc.surfconext.nl/authentication/sp/metadata
                                                )

                                        )

                                )

                            [saml:AuthnStatement] => Array
                                (
                                    [_AuthnInstant] => 2013-12-09T09:25:43Z
                                    [_SessionIndex] => 54fdbe427c50036b272dcf2cca7b5e9a
                                    [saml:AuthnContext] => Array
                                        (
                                            [saml:AuthnContextClassRef] => Array
                                                (
                                                    [__v] => urn:oasis:names:tc:SAML:2.0:ac:classes:Password
                                                )

                                        )

                                )

                            [saml:AttributeStatement] => Array
                                (
                                    [0] => Array
                                        (
                                            [saml:Attribute] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:uid
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => joostd
                                                                        )

                                                                )

                                                        )

                                                    [1] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:cn
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => Joost van Dijk
                                                                        )

                                                                )

                                                        )

                                                    [2] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:givenName
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => Joost
                                                                        )

                                                                )

                                                        )

                                                    [3] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:sn
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => van Dijk
                                                                        )

                                                                )

                                                        )

                                                    [4] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:displayName
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => Joost van Dijk
                                                                        )

                                                                )

                                                        )

                                                    [5] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:mail
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => joost.vandijk@surfnet.nl
                                                                        )

                                                                )

                                                        )

                                                    [6] => Array
                                                        (
                                                            [_Name] => urn:mace:terena.org:attribute-def:schacHomeOrganization
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => surfguest.nl
                                                                        )

                                                                )

                                                        )

                                                    [7] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:eduPersonPrincipalName
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => joostd@SURFguest.nl
                                                                        )

                                                                )

                                                        )

                                                    [8] => Array
                                                        (
                                                            [_Name] => urn:oid:1.3.6.1.4.1.1076.20.100.10.10.1
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => member
                                                                        )

                                                                )

                                                        )

                                                    [9] => Array
                                                        (
                                                            [_Name] => urn:mace:dir:attribute-def:isMemberOf
                                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                                            [saml:AttributeValue] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [__v] => urn:collab:org:surf.nl
                                                                        )

                                                                )

                                                        )

                                                )

                                        )

                                )

                        )

                    [__] => Array
                        (
                            [ProtocolBinding] => urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST
                            [RelayState] =>
                            [Raw] => <?xml version="1.0"?>
<samlp:Response xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" ID="_397e7b0f86f8a63dd5aa5d728fdde7710f08b17377" InResponseTo="CORTO0486c083eec648d4fd99c570d3c2a084fe007ea7" Version="2.0" IssueInstant="2013-12-09T09:25:43Z" Destination="https://engine.acc.surfconext.nl/authentication/sp/consume-assertion">
  <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">https://surfguest.nl</saml:Issuer>
  <samlp:Status xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol">
    <samlp:StatusCode xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" Value="urn:oasis:names:tc:SAML:2.0:status:Success"/>
  </samlp:Status>
  <saml:Assertion Version="2.0" ID="pfx4f3b5c51-fa08-2355-87d9-119d889c7c31" IssueInstant="2013-12-09T09:25:43Z">
    <saml:Issuer>https://surfguest.nl</saml:Issuer>
    <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
  <ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
    <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
  <ds:Reference URI="#pfx4f3b5c51-fa08-2355-87d9-119d889c7c31"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/><ds:DigestValue>19svEWuYOELBRX4+imwseUFOWRA=</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>JtNUWqIHb5hTf67AFe0nnafoWHGuH8zpdZwqrmyLeP/ZKvS1xSfhNheUbxJ42iB7l5nod5tG0DHSgp6bOZZVqYZGtj8zu+pt41BzTAuMbY4aSQJDFJrX2XCbrkgdTLsQKqjNygGGXd3ajr3rT+LfPr8UMQkiIn8FEnYI19+bH1KeqSzP8nA8Fu7W5BOIifvwuAfiJPjauMdVJC3I3MrgKgDocy0Z3sFF+bNGf1kSKSsZ2bKE0rA4axDMfQpo8S7xMdZbI35U1rs4AD0UauiWC8n7nm6pIGOFyF6KZ/5uScMX9oEWyvpXVUC84tHr3EGGkMwvyYDXOSoyIZ+ZGcsobg==</ds:SignatureValue>
</ds:Signature><saml:Subject>
      <saml:NameID Format="urn:oasis:names:tc:SAML:2.0:nameid-format:persistent">joostd@surfguest.nl</saml:NameID>
      <saml:SubjectConfirmation Method="urn:oasis:names:tc:SAML:2.0:cm:bearer">
        <saml:SubjectConfirmationData NotOnOrAfter="2013-12-09T09:30:43Z" InResponseTo="CORTO0486c083eec648d4fd99c570d3c2a084fe007ea7" Recipient="https://engine.acc.surfconext.nl/authentication/sp/consume-assertion"/>
      </saml:SubjectConfirmation>
    </saml:Subject>
    <saml:Conditions NotBefore="2013-12-09T09:25:13Z" NotOnOrAfter="2013-12-09T09:30:43Z">
      <saml:AudienceRestriction>
        <saml:Audience>https://engine.acc.surfconext.nl/authentication/sp/metadata</saml:Audience>
      </saml:AudienceRestriction>
    </saml:Conditions>
    <saml:AuthnStatement AuthnInstant="2013-12-09T09:25:43Z" SessionIndex="54fdbe427c50036b272dcf2cca7b5e9a">
      <saml:AuthnContext>
        <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Password</saml:AuthnContextClassRef>
      </saml:AuthnContext>
    </saml:AuthnStatement>
    <saml:AttributeStatement>
      <saml:Attribute Name="urn:mace:dir:attribute-def:uid">
        <saml:AttributeValue>joostd</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:dir:attribute-def:cn">
        <saml:AttributeValue>Joost van Dijk</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:dir:attribute-def:givenName">
        <saml:AttributeValue>Joost</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:dir:attribute-def:sn">
        <saml:AttributeValue>van Dijk</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:dir:attribute-def:displayName">
        <saml:AttributeValue>Joost van Dijk</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:dir:attribute-def:mail">
        <saml:AttributeValue>joost.vandijk@surfnet.nl</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:terena.org:attribute-def:schacHomeOrganization">
        <saml:AttributeValue>surfguest.nl</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:mace:dir:attribute-def:eduPersonPrincipalName">
        <saml:AttributeValue>joostd@SURFguest.nl</saml:AttributeValue>
      </saml:Attribute>
      <saml:Attribute Name="urn:oid:1.3.6.1.4.1.1076.20.100.10.10.1">
        <saml:AttributeValue>member</saml:AttributeValue>
      </saml:Attribute>
    </saml:AttributeStatement>
  </saml:Assertion>
</samlp:Response>

                            [paramname] => SAMLResponse
                            [IntendedNameId] => joostd@surfguest.nl
                            [collabPersonId] => urn:collab:person:surfguest.nl:joostd
                            [OriginalNameId] => Array
                                (
                                    [_Format] => urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
                                    [__v] => joostd@surfguest.nl
                                )

                        )

                )

            [OriginalIssuer] => https://surfguest.nl
            [IntendedNameId] => urn:collab:person:surfguest.nl:joostd
        )

    [_xmlns:samlp] => urn:oasis:names:tc:SAML:2.0:protocol
    [_xmlns:saml] => urn:oasis:names:tc:SAML:2.0:assertion
    [_ID] => CORTOd8baa3fa58a79dc3e46a9195ce0208a32e4d1c71
    [_Version] => 2.0
    [_IssueInstant] => 2013-12-09T09:25:44Z
    [_InResponseTo] => _73ccafcb27f968fb72b24351fdeb59567b0d06bd28
    [saml:Issuer] => Array
        (
            [__v] => https://engine.acc.surfconext.nl/authentication/idp/metadata
        )

    [samlp:Status] => Array
        (
            [samlp:StatusCode] => Array
                (
                    [_Value] => urn:oasis:names:tc:SAML:2.0:status:Success
                )

        )

    [_Destination] => https://profile.acc.surfconext.nl/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp
    [_Consent] => urn:oasis:names:tc:SAML:2.0:consent:inapplicable
    [saml:Assertion] => Array
        (
            [__t] => saml:Assertion
            [_xmlns:saml] => urn:oasis:names:tc:SAML:2.0:assertion
            [_ID] => CORTO95313c0da6bc0558859224ab3088ea762b3e629a
            [_IssueInstant] => 2013-12-09T09:25:44Z
            [_Version] => 2.0
            [saml:Issuer] => Array
                (
                    [__v] => https://engine.acc.surfconext.nl/authentication/idp/metadata
                )

            [ds:Signature] => Array
                (
                    [__t] => ds:Signature
                    [_xmlns:ds] => http://www.w3.org/2000/09/xmldsig#
                    [ds:SignedInfo] => Array
                        (
                            [__t] => ds:SignedInfo
                            [_xmlns:ds] => http://www.w3.org/2000/09/xmldsig#
                            [ds:CanonicalizationMethod] => Array
                                (
                                    [_Algorithm] => http://www.w3.org/2001/10/xml-exc-c14n#
                                )

                            [ds:SignatureMethod] => Array
                                (
                                    [_Algorithm] => http://www.w3.org/2000/09/xmldsig#rsa-sha1
                                )

                            [ds:Reference] => Array
                                (
                                    [0] => Array
                                        (
                                            [_URI] => #CORTO95313c0da6bc0558859224ab3088ea762b3e629a
                                            [ds:Transforms] => Array
                                                (
                                                    [ds:Transform] => Array
                                                        (
                                                            [0] => Array
                                                                (
                                                                    [_Algorithm] => http://www.w3.org/2000/09/xmldsig#enveloped-signature
                                                                )

                                                            [1] => Array
                                                                (
                                                                    [_Algorithm] => http://www.w3.org/2001/10/xml-exc-c14n#
                                                                )

                                                        )

                                                )

                                            [ds:DigestMethod] => Array
                                                (
                                                    [_Algorithm] => http://www.w3.org/2000/09/xmldsig#sha1
                                                )

                                            [ds:DigestValue] => Array
                                                (
                                                    [__v] => kHkI0K4ne4BUZ2Zk4oEZvwQ+qJE=
                                                )

                                        )

                                )

                        )

                    [ds:SignatureValue] => Array
                        (
                            [__v] => nuk3NjO7Ln2E2UM7cOjkHPi+ZCwDi9kGBaqnG45PwhtvK4TamkRatr2LTT6cSauB39FoZzIUOhC27zyfx62FI0A2sxY14uj+ysAjczlcJmbtltWpQ9fMGs8MbvNHadhvkve9uJOi+OgZeiVlOmGZcRn5E/4+p1+GCfSx6+flT5Q=
                        )

                    [ds:KeyInfo] => Array
                        (
                            [ds:X509Data] => Array
                                (
                                    [ds:X509Certificate] => Array
                                        (
                                            [__v] => MIICgTCCAeoCCQCbOlrWDdX7FTANBgkqhkiG9w0BAQUFADCBhDELMAkGA1UEBhMC
Tk8xGDAWBgNVBAgTD0FuZHJlYXMgU29sYmVyZzEMMAoGA1UEBxMDRm9vMRAwDgYD
VQQKEwdVTklORVRUMRgwFgYDVQQDEw9mZWlkZS5lcmxhbmcubm8xITAfBgkqhkiG
9w0BCQEWEmFuZHJlYXNAdW5pbmV0dC5ubzAeFw0wNzA2MTUxMjAxMzVaFw0wNzA4
MTQxMjAxMzVaMIGEMQswCQYDVQQGEwJOTzEYMBYGA1UECBMPQW5kcmVhcyBTb2xi
ZXJnMQwwCgYDVQQHEwNGb28xEDAOBgNVBAoTB1VOSU5FVFQxGDAWBgNVBAMTD2Zl
aWRlLmVybGFuZy5ubzEhMB8GCSqGSIb3DQEJARYSYW5kcmVhc0B1bmluZXR0Lm5v
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDivbhR7P516x/S3BqKxupQe0LO
NoliupiBOesCO3SHbDrl3+q9IbfnfmE04rNuMcPsIxB161TdDpIesLCn7c8aPHIS
KOtPlAeTZSnb8QAu7aRjZq3+PbrP5uW3TcfCGPtKTytHOge/OlJbo078dVhXQ14d
1EDwXJW1rRXuUt4C8QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBACDVfp86HObqY+e8
BUoWQ9+VMQx1ASDohBjwOsg2WykUqRXF+dLfcUH9dWR63CtZIKFDbStNomPnQz7n
bK+onygwBspVEbnHuUihZq3ZUdmumQqCw4Uvs/1Uvq3orOo/WJVhTyvLgFVK2Qar
Q4/67OZfHd7R+POBXhophSMv1ZOo

                                        )

                                )

                        )

                )

            [saml:Subject] => Array
                (
                    [saml:NameID] => Array
                        (
                            [_Format] => urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified
                            [__v] => urn:collab:person:surfguest.nl:joostd
                        )

                    [saml:SubjectConfirmation] => Array
                        (
                            [_Method] => urn:oasis:names:tc:SAML:2.0:cm:bearer
                            [saml:SubjectConfirmationData] => Array
                                (
                                    [_NotOnOrAfter] => 2013-12-09T09:30:44Z
                                    [_InResponseTo] => _73ccafcb27f968fb72b24351fdeb59567b0d06bd28
                                    [_Recipient] => https://profile.acc.surfconext.nl/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp
                                )

                        )

                )

            [saml:Conditions] => Array
                (
                    [_NotBefore] => 2013-12-09T09:25:44Z
                    [_NotOnOrAfter] => 2013-12-09T09:30:44Z
                    [saml:AudienceRestriction] => Array
                        (
                            [saml:Audience] => Array
                                (
                                    [__v] => https://profile.acc.surfconext.nl/simplesaml/module.php/saml/sp/metadata.php/default-sp
                                )

                        )

                )

            [saml:AuthnStatement] => Array
                (
                    [_AuthnInstant] => 2013-12-09T09:25:43Z
                    [_SessionIndex] => 54fdbe427c50036b272dcf2cca7b5e9a
                    [saml:AuthnContext] => Array
                        (
                            [saml:AuthnContextClassRef] => Array
                                (
                                    [__v] => urn:oasis:names:tc:SAML:2.0:ac:classes:Password
                                )

                            [saml:AuthenticatingAuthority] => Array
                                (
                                    [0] => Array
                                        (
                                            [__v] => https://surfguest.nl
                                        )

                                )

                        )

                )

            [saml:AttributeStatement] => Array
                (
                    [0] => Array
                        (
                            [saml:Attribute] => Array
                                (
                                    [0] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:uid
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => joostd
                                                        )

                                                )

                                        )

                                    [1] => Array
                                        (
                                            [_Name] => urn:oid:0.9.2342.19200300.100.1.1
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => joostd
                                                        )

                                                )

                                        )

                                    [2] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:cn
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => Joost van Dijk
                                                        )

                                                )

                                        )

                                    [3] => Array
                                        (
                                            [_Name] => urn:oid:2.5.4.3
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => Joost van Dijk
                                                        )

                                                )

                                        )

                                    [4] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:givenName
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => Joost
                                                        )

                                                )

                                        )

                                    [5] => Array
                                        (
                                            [_Name] => urn:oid:2.5.4.42
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => Joost
                                                        )

                                                )

                                        )

                                    [6] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:sn
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => van Dijk
                                                        )

                                                )

                                        )

                                    [7] => Array
                                        (
                                            [_Name] => urn:oid:2.5.4.4
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => van Dijk
                                                        )

                                                )

                                        )

                                    [8] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:displayName
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => Joost van Dijk
                                                        )

                                                )

                                        )

                                    [9] => Array
                                        (
                                            [_Name] => urn:oid:2.16.840.1.113730.3.1.241
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => Joost van Dijk
                                                        )

                                                )

                                        )

                                    [10] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:mail
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => joost.vandijk@surfnet.nl
                                                        )

                                                )

                                        )

                                    [11] => Array
                                        (
                                            [_Name] => urn:oid:0.9.2342.19200300.100.1.3
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => joost.vandijk@surfnet.nl
                                                        )

                                                )

                                        )

                                    [12] => Array
                                        (
                                            [_Name] => urn:mace:terena.org:attribute-def:schacHomeOrganization
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => surfguest.nl
                                                        )

                                                )

                                        )

                                    [13] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.25178.1.2.9
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => surfguest.nl
                                                        )

                                                )

                                        )

                                    [14] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.1466.115.121.1.15
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => surfguest.nl
                                                        )

                                                )

                                        )

                                    [15] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:eduPersonPrincipalName
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => joostd@SURFguest.nl
                                                        )

                                                )

                                        )

                                    [16] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.5923.1.1.1.6
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => joostd@SURFguest.nl
                                                        )

                                                )

                                        )

                                    [17] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.1076.20.100.10.10.1
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => member
                                                        )

                                                )

                                        )

                                    [18] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:isMemberOf
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => urn:collab:org:surf.nl
                                                        )

                                                )

                                        )

                                    [19] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.5923.1.5.1.1
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => urn:collab:org:surf.nl
                                                        )

                                                )

                                        )

                                    [20] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.1076.20.40.40.1
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => urn:collab:person:surfguest.nl:joostd
                                                        )

                                                )

                                        )

                                    [21] => Array
                                        (
                                            [_Name] => urn:nl.surfconext.licenseInfo
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [__v] => LICENSE_UNKNOWN
                                                        )

                                                )

                                        )

                                    [22] => Array
                                        (
                                            [_Name] => urn:mace:dir:attribute-def:eduPersonTargetedID
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [saml:NameID] => Array
                                                                (
                                                                    [_Format] => urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified
                                                                    [__v] => urn:collab:person:surfguest.nl:joostd
                                                                )

                                                        )

                                                )

                                        )

                                    [23] => Array
                                        (
                                            [_Name] => urn:oid:1.3.6.1.4.1.5923.1.1.1.10
                                            [_NameFormat] => urn:oasis:names:tc:SAML:2.0:attrname-format:uri
                                            [saml:AttributeValue] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [saml:NameID] => Array
                                                                (
                                                                    [_Format] => urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified
                                                                    [__v] => urn:collab:person:surfguest.nl:joostd
                                                                )

                                                        )

                                                )

                                        )

                                )

                        )

                )

            [__] => Array
                (
                    [Signed] => 1
                )

        )

)
CONTENT;

        $parser = new PrintRParser($content);
        $parsed = $parser->parse();

        $this->assertNotEmpty($parsed);

        $this->markTestIncomplete('Parser should be able to output what came in');
        return;

        $reprinted = print_r($parsed, true);
        $reprinted = substr($reprinted, 0, strlen($reprinted) - 1);
        file_put_contents('/tmp/content-original', $content);
        file_put_contents('/tmp/content-parsed', $reprinted);
        $this->assertEquals($content, $reprinted);
    }
}
