<?php

namespace OpenConext\Component\EngineBlock\Fixture;

use OpenConext\Component\EngineBlock\DataStore\JsonDataStore;

class IdFixture
{
    protected $dataStore;
    protected $frames = array();

    function __construct($dataStore)
    {
        $this->dataStore = $dataStore;
        $this->frames = $this->dataStore->load();
    }

    public function addFrame(IdFrame $frame)
    {
        $this->frames[] = $frame;
    }

    public function clear()
    {
        $this->frames = array();
    }

    public function __destruct()
    {
        $this->dataStore->save($this->frames);
    }
}
