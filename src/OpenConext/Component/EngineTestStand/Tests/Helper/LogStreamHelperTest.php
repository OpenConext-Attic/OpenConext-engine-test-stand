<?php

namespace OpenConext\Component\EngineTestStand\Tests\Helper;

use OpenConext\Component\EngineTestStand\Helper\LogStreamHelper;

class LogStreamHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testForeach()
    {
        $filePath = __DIR__ . '/../../Resources/fixtures/system.example.log';
        $stream = fopen($filePath,'r');
        $logStream = new LogStreamHelper($stream);

        $counter = 0;
        $logStream->foreachLine(function($line) use (&$counter) {
            $counter++;
        });

        $logStream->rewind();

        $this->assertEquals(count(file($filePath)), $counter);
    }
}
