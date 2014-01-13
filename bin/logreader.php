#!/bin/env php
<?php

$content = file_get_contents(__DIR__ . '/../idp.response.log');

$chunkStartMatches = array();
$chunkEndMatches = array();
if (!preg_match('/!CHUNKSTART>.+samlp:Response/', $content, $chunkStartMatches) || !preg_match('/!CHUNKEND>/', $content, $chunkEndMatches)) {
    throw new \RuntimeException('No samlp:Response found or incomplete chunk!');
}

// Chop off everything before the CHUNKSTART
$content = substr($content, strpos($content, $chunkStartMatches[0]));
// ... and after the first newline after CHUNKEND
$content = substr($content, 0, strpos($content, "\n", strpos($content, $chunkEndMatches[0])));

$contentLines = explode("\n", $content);
foreach ($contentLines as &$line) {
    $line = preg_replace('/^.+CHUNK\w*>/', '', $line);
    $line = preg_replace('/\\\n/', "\n", $line);
}
$content = implode("\n", $contentLines);

$xmlMatches = array();
if (!preg_match('/<\?xml.+<samlp:Response.+<\/samlp:Response>/s', $content, $xmlMatches)) {
    throw new \RuntimeException('Can not find raw XML Response in log dump!');
}


print $xmlMatches[0];
