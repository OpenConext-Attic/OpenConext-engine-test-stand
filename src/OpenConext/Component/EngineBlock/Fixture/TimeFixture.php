<?php

namespace OpenConext\Component\EngineBlock\Fixture;

use OpenConext\Component\EngineBlock\DataStore\JsonDataStore;

class TimeFixture
{
    protected $fixture;
    protected $time;

    public function __construct(JsonDataStore $fixture)
    {
        $this->fixture = $fixture;

        $this->load();
    }

    protected function load()
    {
        $time = $this->fixture->load(false);
        if ($time === false) {
            return;
        }

        $this->time = $time;
    }

    public function set($time)
    {
        $this->time = (string) (int) $time;
    }

    public function __destruct()
    {
        if (!isset($this->time)) {
            return;
        }

        $this->fixture->save($this->time);
    }
}
