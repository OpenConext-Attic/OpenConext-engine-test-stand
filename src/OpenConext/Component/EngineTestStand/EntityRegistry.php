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

    /**
     * @return MockIdentityProvider|MockServiceProvider
     * @throws \RuntimeException
     */
    public function getOnly()
    {
        $count = $this->count();

        if ($count === 0) {
            throw new \RuntimeException("No entities registered yet (use before definition)");
        }

        if ($count !== 1) {
            throw new \RuntimeException("More than 1 entities registered, unable to get a single entity");
        }

        return $this->getIterator()->current();
    }

    public function clear()
    {
        $this->parameters = array();
        return $this;
    }

    public function save()
    {
        $this->dataStore->save($this->parameters);
        return $this;
    }

    public function __destruct()
    {
        $this->save();
    }
}
