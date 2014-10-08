<?php

namespace OpenConext\Bundle\FunctionalTestingBundle\Command;

use OpenConext\Component\EngineBlockFixtures\DataStore\JsonDataStore;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OpenConext\Component\EngineTestStand\Helper\FileOrStdInHelper;

/**
 * Dump the contents of the (fake) Service Registry
 * @package OpenConext\Bundle\FunctionalTestingBundle\Command
 */
class DumpServiceRegistryCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dump:sr')
            ->setDescription('Find all sessions from log output on STDIN or for a given file')
            ->addArgument('file', InputArgument::OPTIONAL, 'File to get sessions from.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var JsonDataStore $srDataStore */
        $srDataStore = $this->getContainer()->get('openconext_functional_testing.data_store.service_registry');
        $output->write(print_r($srDataStore->load(), true));
        return 0;
    }
}
