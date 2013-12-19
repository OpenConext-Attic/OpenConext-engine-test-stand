<?php

namespace OpenConext\EngineTestStand;

class Config
{
    protected $configs = array();

    public static function create($file)
    {
        if (!file_exists($file)) {
            return new static(array());
        }
        return new static(json_decode(file_get_contents($file), true));
    }

    protected function __construct($configs)
    {
        $this->configs = $configs;
    }

    public function get($name, $defaultValue = null)
    {
        return isset($this->configs[$name]) ? $this->configs[$name] : $defaultValue;
    }

    public function expect($name)
    {
        if (!isset($this->configs[$name])) {
            throw new \RuntimeException("Application configuration missing '$name' property!");
        }
        return $this->configs[$name];
    }
}
