<?php

namespace OpenConext\Component\EngineTestStand\Helper;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FileOrStdInHelper
 * @package OpenConext\Bundle\LogReplayBundle\Command\Helper
 */
class FileOrStdInHelper
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $argName;

    /**
     * @param InputInterface    $input
     * @param OutputInterface   $output
     * @param string            $argName
     */
    public function __construct(InputInterface $input, OutputInterface $output, $argName = 'file')
    {
        $this->input = $input;
        $this->output = $output;
        $this->argName = $argName;
    }

    /**
     * @param $fn
     * @return array
     */
    public function mapLines($fn)
    {
        $stream = $this->getStream();

        $output = array();
        while (!feof($stream)) {
            $line = stream_get_line($stream, 1024, "\n");

            $fnOutput = $fn($line);
            if ($fnOutput) {
                $output[] = $fnOutput;
            }
        }
        return $output;
    }

    /**
     * @return resource
     * @throws \Doctrine\Common\Proxy\Exception\InvalidArgumentException
     */
    protected function getStream()
    {
        $filename = $this->input->getArgument($this->argName);
        if (!$filename) {
            return STDIN;
        }

        if (!is_file($filename)) {
            throw new InvalidArgumentException("File '$filename' doesn't exist.");
        }

        return fopen($filename, 'r');
    }
}
