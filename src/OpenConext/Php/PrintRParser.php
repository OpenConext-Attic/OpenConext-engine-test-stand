<?php

namespace OpenConext\Php;

/**
 * Parses an OpenConext-EngineBlock print_r or a SAML Response.
 * @package OpenConext\Php
 */
class PrintRParser
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Create a new PrintRParser giving it the content it needs to parse.
     *
     * @param $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Turn on 'echo' debugging (off by default).
     */
    public function setDebugMode()
    {
        $this->debug = true;
    }

    /**
     * Parse the given content into an array.
     *
     * @return array
     */
    public function parse()
    {
        return $this->parseArray();
    }

    protected function parseArray()
    {
        $this->debug(__FUNCTION__);
        $this->optionalSpace();
        $this->consume('Array');
        $this->newline();
        $this->optionalSpace();
        $this->consume('\(');
        $array = array();
        $this->debug('Array START');
        while ($this->lookAhead("\n *\\[[\\w\\d:]+\\] =>")) {
            $array = $this->arrayContent($array);
        }
        $this->debug('Array STOP');
        $this->newline();
        $this->optionalSpace();
        $this->consume('\)');
        $this->optionalNewline();
        return $array;
    }

    protected function arrayContent($array)
    {
        $this->debug(__FUNCTION__);
        $key = $this->arrayKey();
        $this->optionalSpace();
        $this->consume('=>');
        $this->optionalSpace();
        $value = $this->arrayValue();

        $array[$key] = $value;

        return $array;
    }

    protected function arrayKey()
    {
        $this->debug(__FUNCTION__);
        $this->newline();
        $this->optionalSpace();
        $this->consume('\[');
        $key = $this->consume('[\w\d:]+');
        $this->consume('\]');

        return $key;
    }

    protected function arrayValue()
    {
        $this->debug(__FUNCTION__);
        $this->optionalSpace();
        if ($this->lookAhead('Array')) {
            return $this->parseArray();
        }

        if ($this->lookAhead("\n")) {
            return '';
        }

        return $this->multilineString();
    }

    protected function multilineString()
    {
        $this->debug(__FUNCTION__);
        $string = $this->consume("[^\n]+");

        $isMultiline = false;
        while(!$this->lookAhead("[\n]{1,2} *\\[[\\w\\d:]+\\] =>") && !$this->lookAhead("[\n]{1,2} *\\)\n")) {
            $string .= $this->newline();
            $string .= $this->consume('.+');
            $isMultiline = true;
        }
        if ($isMultiline) {
            $this->optionalNewline();
        }

        return $string;
    }

    protected function optionalSpace()
    {
        $this->debug(__FUNCTION__);
        return $this->consume(' *');
    }

    protected function requiredSpace()
    {
        $this->debug(__FUNCTION__);
        return $this->consume(' +');
    }

    protected function newline()
    {
        $this->debug(__FUNCTION__);
        return $this->consume("\n");
    }

    protected function optionalNewline()
    {
        $this->debug(__FUNCTION__);
        if ($this->lookAhead("\n")) {
            return $this->newline();
        }
        return false;
    }

    protected function consume($terminal)
    {
        $match = $this->match($terminal);

        // Throw a fit if we can't find what we expected.
        if ($match === false) {
            $terminal = str_replace("\n", '\n', $terminal);
            throw new \RuntimeException(
                "Unable to match terminal '$terminal' in content: '" .
                str_replace("\n", '\n', substr($this->content, 0, 50)) . "'..."
            );
        }

        // Strip the consumed bit off the beginning.
        $this->content = substr($this->content, strlen($match));
        $this->debug('consumed: "' . str_replace("\n",'\n', $match) . '"');

        return $match;
    }

    protected function lookAhead($terminal)
    {
        $matched = $this->match($terminal);
        $this->debug(
            "lookAhead('" . str_replace("\n", '\n', $terminal) . "') " .
            ($matched ? "found: '" . str_replace("\n", '\n', $matched) . "'" : 'not found' ) .
            " in content: '" . str_replace("\n", '\n', substr($this->content, 0, 60)) . "'"
        );
        return ($matched !== false);
    }

    protected function match($terminal)
    {
        // Escape the delimeter.
        $terminal = str_replace('/', '\\/', $terminal);
        $regex = '/^(' . $terminal . ')/';

        $matches = array();
        if (!preg_match($regex, $this->content, $matches)) {
            return false;
        }

        return $matches[0];
    }

    protected function debug($line)
    {
        if (!$this->debug) {
            return;
        }

        echo $line . PHP_EOL;
    }
}
