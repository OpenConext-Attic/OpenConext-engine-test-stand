<?php

namespace OpenConext\Bundle\LogReplayBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OpenConext\Component\EngineTestStand\Helper\FileOrStdInHelper;

/**
 * For a given list of session identifiers, filter out those without a login flow.
 * @package OpenConext\Bundle\LogReplayBundle\Command
 */
class FlowFilterCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('replay:flow:filter')
            ->setDescription('Find all sessions that have an attached flow')
            ->addArgument('logfile', InputArgument::REQUIRED, 'File to get flows from')
            ->addArgument('sessionFile', InputArgument::OPTIONAL, 'File to get sessions from.')

            ->setHelp(<<<EOF
The <info>%command.name%</info> filters out the sessions with incomplete flows:

<info>grep "something" engineblock.log | app/console functional-testing:sessions:find | %command.full_name% engineblock.log</info>

The optional argument specifies to read from a file (by default it reads from the standard input):

<info>php %command.full_name% engineblock.log engineblock.log</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logFile = $input->getArgument('logfile');
        if (!is_file($logFile)) {
            $output->writeln("<error>Logfile does not exist</error>");
            return 64;
        }
        $logStream = fopen($logFile, 'r');

        $sessionsStream = new FileOrStdInHelper($input, $output, 'sessionFile');
        $that = $this;
        $sessionsStream->mapLines(function($line) use ($logStream, $output, $that) {
            $sessionId = trim($line);
            if (!$sessionId) {
                return;
            }

            $that->filterForSession($sessionId, $logStream, $output);
        });

        fclose($logStream);

        return 0;
    }

    protected function filterForSession($sessionId, $logStream, OutputInterface $output)
    {
        // The four horsemen^H^H^H^H^H^H^H^H^H messages we need to reconstruct a flow.
        $hasSpRequest   = false;
        $hasEbRequest   = false;
        $hasIdpResponse = false;
        $hasEbResponse  = false;

        rewind($logStream);
        $history = array(
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
        );
        while (!feof($logStream)) {
            $logLine = stream_get_line($logStream, 1024, "\n");

            if (strpos($logLine, $sessionId) === false) {
                continue;
            }

            if (!preg_match("/EB\\[$sessionId\\]\\[/", $logLine)) {
                continue;
            }

            if (strpos($logLine, '[Message INFO] Received response') !== false) {
                $hasIdpResponse = true;
            }

            if (strpos($logLine, '[Message INFO] Received request') !== false) {
                $hasSpRequest = true;
            }

            if (strpos($logLine, '[Message INFO] Redirecting to ') !== false && strpos($logLine, 'SAMLRequest') !== false) {
                $hasEbRequest = true;
            }

            if (strpos($logLine, '[Message INFO] HTTP-Post: Sending Message') !== false) {
                $hasAttributeValue = false;
                foreach ($history as $historyLine) {
                    if ($hasAttributeValue || strpos($historyLine, '[saml:AttributeValue]')) {
                        $hasAttributeValue = true;
                    }
                }

                if ($hasAttributeValue) {
                    $hasEbResponse = true;
                }
                else {
                    $hasEbRequest = true;
                }
            }

            if ($hasSpRequest && $hasEbRequest && $hasIdpResponse && $hasEbResponse) {
                $output->writeln($sessionId);
                return; // Done! Next session id
            }

            $history[] = $logLine;
            if (count($history) > 5) {
                array_shift($history);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function executeOld(InputInterface $input, OutputInterface $output)
    {
        $logFile = $input->getArgument('logfile');
        if (!is_file($logFile)) {
            $output->writeln("<error>Logfile does not exist</error>");
            return 64;
        }
        $logStream = fopen($logFile, 'r');

        $sessionsStream = new FileOrStdInHelper($input, $output, 'sessionFile');
        $sessions = $sessionsStream->mapLines(function($line) { return trim($line); });

        $count = 0;
        rewind($logStream);

        while (!feof($logStream)) {
            $logLine = stream_get_line($logStream, 1024, "\n");
            $count++;

            if ($count % 10000 === 0) {
                $output->writeln("Linecount: $count");
            }

            foreach ($sessions as $index => $sessionId) {
                if (strpos($logLine, $sessionId) === false) {
                    continue;
                }

                if (!preg_match("/EB\\[[\\w\\d]+\\]\\[$sessionId\\]/", $logLine)) {
                    continue;
                }

                $output->writeln($sessionId);
                unset($sessions[$index]);
            }
        }

        return 0;
    }
}
