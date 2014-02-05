<?php

namespace OpenConext\Component\EngineBlock\Fixture;

use OpenConext\Component\EngineBlock\DataStore\AbstractDataStore;
use OpenConext\Component\EngineBlock\DataStore\FileFlags;

class ServiceRegistryFixture
{
    const TYPE_SP = 1;
    const TYPE_IDP = 2;

    protected $fixture;
    protected $fileFlags;
    protected $data;

    public function __construct(AbstractDataStore $dataStore, FileFlags $fileFlags)
    {
        $this->fixture = $dataStore;
        $this->fileFlags = $fileFlags;

        $this->data = $dataStore->load();
    }

    public function reset()
    {
        $this->data = array();
        return $this;
    }

    public function registerSp($entityId, $acsLocation)
    {
        $this->data[$entityId] = array(
            'workflowState' => 'prodaccepted',
            'entityId'      => $entityId,
            'AssertionConsumerService:0:Location' => $acsLocation,
        );
        return $this;
    }

    public function setEntitySsoLocation($entityId, $ssoLocation)
    {
        $this->data[$entityId]['SingleSignOnService:0:Location'] = $ssoLocation;
        return $this;
    }

    public function addSpsFromJsonExport($spsConfigExportUrl)
    {
        $this->addEntitiesFromJsonConfigExport($spsConfigExportUrl);
        return $this;
    }

    public function addIdpsFromJsonExport($idpsConfigExportUrl)
    {
        $this->addEntitiesFromJsonConfigExport($idpsConfigExportUrl);
        return $this;
    }

    protected function addEntitiesFromJsonConfigExport($configExportUrl, $type = self::TYPE_SP)
    {
        echo "Downloading ServiceRegistry configuration from: '{$configExportUrl}'..." . PHP_EOL;
        $data = file_get_contents($configExportUrl);
        if (!$data) {
            throw new \RuntimeException('Unable to get data from: ' . $configExportUrl);
        }
        $entities = json_decode($data, true);
        if ($entities === false) {
            throw new \RuntimeException('Unable to decode json: ' . $data);
        }

        foreach ($entities as $entity) {
            $entity = $this->flattenArray($entity);
            $entity['workflowState'] = 'prodaccepted';

            $entityId = $entity['entityid'];

            $this->data[$entityId] = $entity;

            if (!empty($entity['allowedEntities'])) {
                $this->whitelist($entityId);

                foreach ($entity['allowedEntities'] as $allowedEntityId) {
                    if ($type === self::TYPE_SP) {
                        $this->allow($entityId, $allowedEntityId);
                    }
                    else {
                        $this->allow($allowedEntityId, $entityId);
                    }
                }
            }

            if (!empty($entity['blockedEntities'])) {
                $this->blacklist($entityId);
                foreach ($entity['blockedEntities'] as $blockedEntityId) {
                    $this->block($entityId, $blockedEntityId);
                    if ($type === self::TYPE_SP) {
                        $this->block($entityId, $blockedEntityId);
                    }
                    else {
                        $this->block($blockedEntityId, $entityId);
                    }
                }
            }
        }
    }

    protected function flattenArray(array $array, array $newArray = array(), $prefix = false)
    {
        foreach ($array as $name => $value) {
            if (is_array($value)) {
                $newArray = $this->flattenArray($value, $newArray, $prefix . $name . ':');
            }
            else {
                $newArray[$prefix . $name] = $value;
            }
        }
        return $newArray;
    }

    public function blacklist($entityId)
    {
        $this->fileFlags->on('blacklisted-' . md5($entityId), $entityId);
    }

    public function whitelist($entityId)
    {
        $this->fileFlags->off('blacklisted-' . md5($entityId), $entityId);
    }

    public function allow($spEntityId, $idpEntityId)
    {
        $this->fileFlags->off('connection-forbidden-' . md5($spEntityId) . '-' . md5($idpEntityId));
        $this->fileFlags->on(
            'connection-allowed-' . md5($spEntityId) . '-' . md5($idpEntityId),
            $spEntityId . ' - ' . $idpEntityId
        );
    }

    public function block($spEntityId, $idpEntityId)
    {
        $this->fileFlags->off('connection-allowed-' . md5($spEntityId) . '-' . md5($idpEntityId));
        $this->fileFlags->on(
            'connection-forbidden-' . md5($spEntityId) . '-' . md5($idpEntityId),
            $spEntityId . ' - ' . $idpEntityId
        );
    }

    public function __destruct()
    {
        $this->fixture->save($this->data);
    }
}
