<?php

namespace OpenConext\EngineTestStand\Fixture;

abstract class AbstractFixture
{
    protected $file;
    protected $fixture;

    public static function create($file)
    {
        $fixture = unserialize(file_get_contents($file));
        return new static($file, $fixture);
    }

    protected function __construct($file, $fixture)
    {
        $this->file = $file;
        $this->fixture = $fixture;
    }

    protected function save($fixtureData = null)
    {
        if (!$fixtureData) {
            $fixtureData = $this->fixture;
        }

        file_put_contents($this->file, serialize($fixtureData));
    }
}
