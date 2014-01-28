<?php

namespace OpenConext\Bundle\ReplayToolsBundle\Command;

use OpenConext\Bundle\ReplayToolsBundle\Command\Helper\FileOrStdInHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OpenConext\Bundle\ReplayToolsBundle\Command\Helper\StdinHelper;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class FlowDescribeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('functional-testing:flow:describe')
            ->setDescription('Describe all flows form the given sessions.')
            ->addArgument('logfile', InputArgument::OPTIONAL, 'File to get flows from.')
            ->addArgument('sessionFile', InputArgument::OPTIONAL, 'File to get sessions from.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command describes flows found for given sessions:

<info>grep "something" engineblock.log | app/console fu:sess:find | %command.full_name%</info>

The optional argument specifies to read from a file (by default it reads from the standard input):

<info>app/console %command.full_name%</info> engineblock.log
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
