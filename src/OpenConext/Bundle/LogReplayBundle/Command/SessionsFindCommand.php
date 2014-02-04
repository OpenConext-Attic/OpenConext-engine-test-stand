<?php

namespace OpenConext\Bundle\LogReplayBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OpenConext\Component\EngineTestStand\Helper\FileOrStdInHelper;

/**
 * Find sessions in a given log file.
 * @package OpenConext\Bundle\LogReplayBundle\Command
 */
class SessionsFindCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('replay:sessions:find')
            ->setDescription('Find all sessions from log output on STDIN or for a given file')
            ->addArgument('file', InputArgument::OPTIONAL, 'File to get sessions from.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command finds session identifiers in log output:

<info>grep "something" engineblock.log | php %command.full_name%</info>

The optional argument specifies to read from a file (by default it reads from the standard input):

<info>php %command.full_name%</info> engineblock.log
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logStream = new FileOrStdInHelper($input, $output);

        $sessions = array();
        try {
            $logStream->mapLines(function($line) use (&$sessions, $output) {
                $matches = array();
                if (!preg_match('/EB\[([\w\d]+)\]\[[\w\d]+\]/', $line, $matches)) {
                    return;
                }
                $sessionId = $matches[1];
                unset($matches);

                if (in_array($sessionId, $sessions)) {
                    return;
                }

                $output->writeln($sessionId);
                $sessions[] = $sessionId;
            });
        } catch(\InvalidArgumentException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return 64;
        }
        return 0;
    }
}
