<?php

namespace OpenConext\EngineTestStand\ServiceRegistry;

class ServiceRegistryMock
{
    const DIR = '/tmp/eb-fixtures/janus/';
    const FILENAME = 'entities';

    protected $entities = array();

    public static function create()
    {
        if (!file_exists(self::DIR . self::FILENAME)) {
            return new static();
        }
        return new static(json_decode(file_get_contents(self::DIR . self::FILENAME), true));
    }

    protected function __construct($entities = array())
    {
        $this->entities = $entities;
    }

    public function addIdpsFromMetadataUrl($metadataUrl)
    {
        $xml = file_get_contents($metadataUrl);

        $document = new \DOMDocument();
        $document->loadXML($xml);

        foreach ($document->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                $entities = new \SAML2_XML_md_EntitiesDescriptor($childNode);
                break;
            }
        }
        if (!isset($entities)) {
            throw new \RuntimeException("Metadata from $metadataUrl does not contain any elements?");
        }

        /** @var \SAML2_XML_md_EntityDescriptor $entity */
        foreach ($entities->children as $entity) {
            $ssoUrl = null;
            foreach ($entity->RoleDescriptor as $role) {
                if (!$role instanceof \SAML2_XML_md_IDPSSODescriptor) {
                    continue;
                }

                if (count($role->SingleSignOnService) > 1) {
                    throw new \RuntimeException('Multiple SSO services?');
                }

                $ssoUrl = $role->SingleSignOnService[0]->Location;
            }
            if (is_null($ssoUrl)) {
                throw new \RuntimeException("No SSO URL found for {$entity->entityID} at {$metadataUrl}?");
            }

            foreach ($entity->RoleDescriptor as $role) {
                if (!$role instanceof \SAML2_XML_md_IDPSSODescriptor) {
                    continue;
                }

                foreach ($role->KeyDescriptor as $key) {
                    if ($key->use !== 'signing') {
                        continue;
                    }

                    foreach ($key->KeyInfo->info as $info) {
                        if (!$info instanceof \SAML2_XML_ds_X509Data) {
                            continue;
                        }

                        foreach ($info->data as $dataElement) {
                            if (!$dataElement instanceof \SAML2_XML_ds_X509Certificate) {
                                continue;
                            }

                            $certData = preg_replace("/\\s/", '', $dataElement->certificate);
                        }
                    }
                }
            }
            if (!isset($certData)) {
                throw new \RuntimeException("Idp doesnt have a certificate?");
            }

            $this->addIdp($entity->entityID, $ssoUrl, $certData);
        }
    }

    public function addSp($entityId, $acsLocation, $certData = '')
    {
        $this->entities[$entityId] = array(
            'Types' => array(
                'SP'
            ),
            'AssertionConsumerService:0:Location' => $acsLocation,
            'AssertionConsumerService:0:Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'certData' => $certData,

            'EntityID' => $entityId,
            'workflowState' => 'prod',
        );
    }

    public function addIdp($entityId, $ssoLocation, $certData = '')
    {
        $this->entities[$entityId] = array(
            'Types' => array(
                'IDP'
            ),
            'SingleSignOnService:0:Location' => $ssoLocation,
            'SingleSignOnService:0:Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'certData' => $certData,

            'EntityID' => $entityId,
            'workflowState' => 'prod',
        );
    }

    public function addSpFromAuthnRequest($acsLocation, \SAML2_AuthnRequest $authnRequest)
    {
        $entityId = $authnRequest->getIssuer();
        $this->addSp($entityId, $acsLocation);
    }

    public function addIdpFromResponse($ssoLocation, \SAML2_Response $response)
    {
        $entityId = $response->getIssuer();
        $this->addIdp($entityId, $ssoLocation);
    }

    public function blacklist($entityId)
    {
        touch(self::DIR . 'blacklisted-' . md5($entityId));
    }

    public function whitelist($entityId)
    {
        @unlink(self::DIR . 'blacklisted-' . md5($entityId));
    }

    public function allow($spEntityId, $idpEntityId)
    {
        @unlink(self::DIR . 'connection-forbidden-' . md5($spEntityId) . '-' . md5($idpEntityId));
        touch(self::DIR . 'connection-allowed-' . md5($spEntityId) . '-' . md5($idpEntityId));
    }

    public function block($spEntityId, $idpEntityId)
    {
        @unlink(self::DIR . 'connection-allowed-' . md5($spEntityId) . '-' . md5($idpEntityId));
        touch(self::DIR . 'connection-forbidden-' . md5($spEntityId) . '-' . md5($idpEntityId));
    }

    public function save()
    {
        if (!file_exists(self::DIR)) {
            mkdir(self::DIR, 0777, true);
        }
        file_put_contents(self::DIR . self::FILENAME, json_encode($this->entities));
    }

    public function __destruct()
    {
        $this->save();
    }
}
