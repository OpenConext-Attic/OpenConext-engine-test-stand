<?php

namespace OpenConext\Component\EngineBlock\Fixture;

class IdFixture
{
    protected $frames = array();

    public function __constructor()
    {
    }

    public function addFrame(IdFrame $frame)
    {
        $this->frames[] = $frame;
    }

    public function save()
    {
        $dir = '/tmp/eb-fixtures/saml2/';
        $path = $dir . 'id';
        @mkdir($dir);

        $fixture = array();
        if (file_exists($path)) {
            $fixture = json_decode(file_get_contents($path), true);
        }

        $fixture[] = $idFrame;

        file_put_contents($path, json_encode($fixture));
        chmod($path, 0777);
    }

    public function clear()
    {

    }
}
