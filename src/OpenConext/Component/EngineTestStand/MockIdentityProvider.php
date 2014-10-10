<?php

namespace OpenConext\Component\EngineTestStand;

/**
 * Class MockIdentityProvider
 * @package OpenConext\Component\EngineTestStand
 */
class MockIdentityProvider extends AbstractMockEntityRole
{
    public function singleSignOnLocation()
    {
        return $this->getSsoRole()->SingleSignOnService[0]->Location;
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

    public function setStatusMessage($statusMessage)
    {
        $role = $this->getSsoRole();

        $role->Extensions['StatusMessage'] = $statusMessage;
    }

    public function setStatusCode($topLevelStatusCode, $secondLevelStatusCode = '')
    {
        $role = $this->getSsoRole();

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
        $role = $this->getSsoRole();

        if (!isset($role->Extensions['StatusCodeTop'])) {
            return \SAML2_Const::STATUS_SUCCESS;
        }

        return $role->Extensions['StatusCodeTop'];
    }

    public function getStatusCodeSecond()
    {
        $role = $this->getSsoRole();

        if (!isset($role->Extensions['StatusCodeSecond'])) {
            return '';
        }

        return $role->Extensions['StatusCodeSecond'];
    }

    public function getStatusMessage()
    {
        $role = $this->getSsoRole();

        if (!isset($role->Extensions['StatusMessage'])) {
            return '';
        }

        return $role->Extensions['StatusMessage'];
    }

    protected function getRoleClass()
    {
        return '\SAML2_XML_md_IDPSSODescriptor';
    }
}
