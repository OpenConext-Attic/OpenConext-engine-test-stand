#!/bin/env php
<?php

if (!isset($argv[1])) {
    die('Please specify log to read (example: ./bin/logreader.php fixtures/replay/eb.response.log' . PHP_EOL);
}

require __DIR__ . '/../vendor/autoload.php';

use \OpenConext\EngineTestStand\Service\LogReader;

$logReader = LogReader::create(getcwd() . '/' . $argv[1]);
var_dump($logReader->getResponse());
