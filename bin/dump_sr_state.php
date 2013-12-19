#!/bin/env php
<?php 
var_dump(
    json_decode(
        file_get_contents("/tmp/eb-fixtures/janus/entities")
    , true)
);
