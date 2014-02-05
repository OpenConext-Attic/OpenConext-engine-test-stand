<?php

namespace OpenConext\Component\EngineBlock\DataStore;

abstract class AbstractDataStore
{
    protected $filePath;

    public function __construct($filePath)
    {
        $this->setFilePath($filePath);
    }

    protected function setFilePath($filePath)
    {
        if (is_writeable($this->filePath)) {
            $this->filePath = $filePath;
            return;
        }

        $directory = dirname($filePath);
        if (!file_exists($directory)) {
            $createdDirectory = mkdir($directory, 0755, true);
            if (!$createdDirectory) {
                throw new \RuntimeException('Unable to create directory: ' . $directory);
            }
        }

        if (!file_exists($filePath)) {
            $touched = touch($filePath);
            if (!$touched) {
                throw new \RuntimeException('Unable to create file: ' . $filePath);
            }
        }

        $this->filePath = $filePath;
    }

    public function load($default = array())
    {
        $fileContents = file_get_contents($this->filePath);
        if ($fileContents === false) {
            throw new \RuntimeException('Unable to load data from: ' . $this->filePath);
        }
        if (empty($fileContents)) {
            return $default;
        }

        $data = $this->decode($fileContents);
        if ($data === false) {
            throw new \RuntimeException('Unable to decode data from: ' . $this->filePath);
        }

        return $data;
    }

    public function save($data)
    {
        file_put_contents($this->filePath, $this->encode($data));
    }

    abstract protected function encode($data);

    abstract protected function decode($data);
}
