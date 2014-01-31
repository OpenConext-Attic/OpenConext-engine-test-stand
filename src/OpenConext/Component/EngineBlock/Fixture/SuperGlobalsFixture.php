<?php

namespace OpenConext\Component\EngineBlock\Fixture;

class SuperGlobalsFixture
{
    const SERVER = '_SERVER';

    public function set($superGlobal, $name, $value)
    {
        @mkdir(self::DIR);
        file_put_contents(
            self::DIR . self::SUPER_GLOBAL_SERVER_FILENAME,
            json_encode(array('HTTP_HOST' => $hostname))
        );
    }
}
