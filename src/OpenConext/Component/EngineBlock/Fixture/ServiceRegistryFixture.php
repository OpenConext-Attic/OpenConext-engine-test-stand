<?php

namespace OpenConext\EngineTestStand\ServiceRegistry;

class ServiceRegistryFixture
{
    const DIR = '/tmp/eb-fixtures/janus/';
    const FILENAME = 'entities';

    const TYPE_SP = 1;
    const TYPE_IDP = 2;

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

    public function reset()
    {
        $this->entities = array();
        return $this;
    }

    public function setEntitySsoLocation($entityId, $ssoLocation)
    {
        $this->entities[$entityId]['SingleSignOnService:0:Location'] = $ssoLocation;
        return $this;
    }

    public function setEntityAcsLocation($entityId, $acsLocation)
    {
        $this->entities[$entityId]['AssertionConsumerService:0:Location'] = $acsLocation;
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
        $entities = json_decode(file_get_contents($configExportUrl), true);

        foreach ($entities as $entity) {
            $entity = $this->flattenArray($entity);
            $entity['workflowState'] = 'prodaccepted';

            $entityId = $entity['entityid'];

            $this->entities[$entityId] = $entity;

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
        $filename = self::DIR . 'blacklisted-' . md5($entityId);
        file_put_contents($filename, $entityId);
    }

    public function whitelist($entityId)
    {
        @unlink(self::DIR . 'blacklisted-' . md5($entityId));
    }

    public function allow($spEntityId, $idpEntityId)
    {
        @unlink(self::DIR . 'connection-forbidden-' . md5($spEntityId) . '-' . md5($idpEntityId));
        $allowedFilePath = self::DIR . 'connection-allowed-' . md5($spEntityId) . '-' . md5($idpEntityId);
        file_put_contents($allowedFilePath, $spEntityId . ' - ' . $idpEntityId);
    }

    public function block($spEntityId, $idpEntityId)
    {
        @unlink(self::DIR . 'connection-allowed-' . md5($spEntityId) . '-' . md5($idpEntityId));
        $forbiddenFilePath = self::DIR . 'connection-forbidden-' . md5($spEntityId) . '-' . md5($idpEntityId);
        file_put_contents($forbiddenFilePath, $spEntityId . ' - ' . $idpEntityId);
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
