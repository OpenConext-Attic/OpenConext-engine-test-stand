<?php

namespace OpenConext\Component\EngineTestStand;

use OpenConext\Component\EngineBlockFixtures\DataStore\SerializedDataStore;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class EntityRegistry
 * @package OpenConext\Component\EngineTestStand
 */
class EntityRegistry extends ParameterBag
{
    protected $filePath;

    /**
     * @param SerializedDataStore $dataStore
     */
    public function __construct(SerializedDataStore $dataStore)
    {
        $this->dataStore = $dataStore;

        $this->parameters = $dataStore->load();
    }

    public function __destruct()
    {
        $this->dataStore->save($this->parameters);
    }
}
