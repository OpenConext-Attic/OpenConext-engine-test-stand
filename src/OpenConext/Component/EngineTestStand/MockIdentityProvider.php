<?php

namespace OpenConext\Component\EngineTestStand;

/**
 * Class MockIdentityProvider
 * @package OpenConext\Component\EngineTestStand
 */
class MockIdentityProvider
{
    protected $name;
    protected $descriptor;

    public function __construct($name, \SAML2_XML_md_EntityDescriptor $descriptor)
    {
        $this->name = $name;
        $this->descriptor = $descriptor;
    }

    public function entityId()
    {
        return $this->descriptor->entityID;
    }

    public function singleSignOnLocation()
    {
        return $this->getIdpSsoRole()->SingleSignOnService[0]->Location;
    }

    public function setResponse(\SAML2_Response $response)
    {
        $this->descriptor->entityID = $response->getIssuer();
        $this->descriptor->Extensions['Response'] = $response;
    }

    public function overrideResponseDestination($acsUrl)
    {
        $this->descriptor->Extensions['DestinationOverride'] = $acsUrl;
    }

    public function hasDestinationOverride()
    {
        return isset($this->descriptor->Extensions['DestinationOverride']);
    }

    public function getDestinationOverride()
    {
        return $this->descriptor->Extensions['DestinationOverride'];
    }

    public function getEntityDescriptor()
    {
        return $this->descriptor;
    }

    public function publicKeyCertData()
    {
        $role = $this->getIdpSsoRole();

        foreach ($role->KeyDescriptor[0]->KeyInfo->info as $info) {
            if (!$info instanceof \SAML2_XML_ds_X509Data) {
                continue;
            }

            foreach ($info->data as $data) {
                if (!$data instanceof \SAML2_XML_ds_X509Certificate) {
                    continue;
                }

                return $data->certificate;
            }
        }
        throw new \RuntimeException("MockIdp does not have KeyInfo with an X509Certificate");
    }

    /**
     * @return \SAML2_XML_md_IDPSSODescriptor
     * @throws \RuntimeException
     */
    private function getIdpSsoRole()
    {
        foreach ($this->descriptor->RoleDescriptor as $role) {
            if (!$role instanceof \SAML2_XML_md_IDPSSODescriptor) {
                continue;
            }

            return $role;
        }
        throw new \RuntimeException('No IDPSSODescriptor for MockIdentityProvider?');
    }

    public function setCertificate($certificateFile)
    {
        $certData = str_replace(
            array("-----BEGIN CERTIFICATE-----","-----END CERTIFICATE-----", "\n"),
            '',
            $this->getFileContents($certificateFile)
        );

        $role = $this->getIdpSsoRole();

        foreach ($role->KeyDescriptor[0]->KeyInfo->info as $info) {
            if (!$info instanceof \SAML2_XML_ds_X509Data) {
                continue;
            }

            foreach ($info->data as $data) {
                if (!$data instanceof \SAML2_XML_ds_X509Certificate) {
                    continue;
                }

                $data->certificate = $certData;
            }
        }
        throw new \RuntimeException("MockIdp does not have KeyInfo with an X509Certificate");
    }

    public function setPrivateKey($privateKeyFile)
    {
        $role = $this->getIdpSsoRole();

        foreach ($role->KeyDescriptor[0]->KeyInfo->info as $info) {
            if (!$info instanceof \SAML2_XML_Chunk) {
                continue;
            }

            if ($info->localName !== 'PrivateKey') {
                continue;
            }

            $info->xml->nodeValue = $this->getFileContents($privateKeyFile);
        }
    }

    public function setStatusMessage($statusMessage)
    {
        $role = $this->getIdpSsoRole();

        $role->Extensions['StatusMessage'] = $statusMessage;
    }

    public function setStatusCode($topLevelStatusCode, $secondLevelStatusCode = '')
    {
        $role = $this->getIdpSsoRole();

        $role->Extensions['StatusCodeTop'] = $this->getFullyQualifiedStatusCode($topLevelStatusCode);

        if (!empty($secondLevelStatusCode)) {
            $role->Extensions['StatusCodeSecond'] = $this->getFullyQualifiedStatusCode($secondLevelStatusCode);
        }
    }

    private function getFullyQualifiedStatusCode($shortStatusCode)
    {
        $class = new \ReflectionClass('\\SAML2_Const');
        $constants = $class->getConstants();
        foreach ($constants as $constName => $constValue) {
            if (strpos($constName, 'STATUS_') !== 0) {
                continue;
            }

            if (strpos($constValue, $shortStatusCode) === false) {
                continue;
            }

            return $constValue;
        }

        throw new \RuntimeException("'$shortStatusCode' is not a valid status code");
    }

    public function getPrivateKeyPem()
    {
        /** @var \SAML2_XML_md_SPSSODescriptor $spssoRole */
        $idpSsoRole = $this->getIdpSsoRole();

        /** @var \SAML2_XML_Chunk $certificate */
        $certificate = array_reduce(
            $idpSsoRole->KeyDescriptor[0]->KeyInfo->info,
            function($carry, $info) {
                return $carry ? $carry : $info instanceof \SAML2_XML_Chunk ? $info : false;
            }
        );

        return $certificate->xml->textContent;
    }

    public function getFixedResponse()
    {
        return array_reduce(
            $this->getEntityDescriptor()->Extensions,
            function (&$result, $item) {
                return ($item instanceof \SAML2_Response ? $item : $result);
            },
            false
        );
    }

    public function getStatusCodeTop()
    {
        $role = $this->getIdpSsoRole();

        if (!isset($role->Extensions['StatusCodeTop'])) {
            return \SAML2_Const::STATUS_SUCCESS;
        }

        return $role->Extensions['StatusCodeTop'];
    }

    public function getStatusCodeSecond()
    {
        $role = $this->getIdpSsoRole();

        if (!isset($role->Extensions['StatusCodeSecond'])) {
            return '';
        }

        return $role->Extensions['StatusCodeSecond'];
    }

    public function getStatusMessage()
    {
        $role = $this->getIdpSsoRole();

        if (!isset($role->Extensions['StatusMessage'])) {
            return '';
        }

        return $role->Extensions['StatusMessage'];
    }

    private function getFileContents($filePath)
    {
        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }

        $componentPath = __DIR__ . '/../../';
        $fullFilePath = $componentPath . $filePath;
        if (file_exists($fullFilePath)) {
            return file_get_contents($fullFilePath);
        }

        throw new \RuntimeException('Unable to find file: ' . $filePath);
    }
}
