<?php

namespace App\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetupMoviesDomain
 *
 * @package App\Command
 */
class SetupMoviesDomain extends AbstractCommand
{

    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->setName('sdk:setup-movies-domain');

        $this->setDescription('Create and configure new domain');

        $this->setHelp('This command allows you to create and configure the domain for movies. ' .
            'Running this command it will configure access policies, analysis scheme and fields.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start creating and configuring movies domain.<info>');

        // get domain name option
        $domainName = $input->getOption('domain-name');

        // create domain
        $command = $this->getApplication()->find('sdk:create-domain');
        $commandInput = new ArrayInput(array('--domain-name' => $domainName));
        $command->run($commandInput, $output);

        // configure access policies
        $command = $this->getApplication()->find('sdk:configure-access-policies-domain');
        $commandInput = new ArrayInput(array('--domain-name' => $domainName));
        $command->run($commandInput, $output);

        // create and configure analysis scheme
        $command = $this->getApplication()->find('sdk:configure-analysis-scheme-domain');
        $commandInput = new ArrayInput(array('--domain-name' => $domainName));
        $command->run($commandInput, $output);

        // create and configure fields
        $command = $this->getApplication()->find('sdk:configure-fields-domain');
        $commandInput = new ArrayInput(array('--domain-name' => $domainName));
        $command->run($commandInput, $output);

        // trigger reindexing
        $command = $this->getApplication()->find('sdk:trigger-reindexing');
        $commandInput = new ArrayInput(array('--domain-name' => $domainName));
        $command->run($commandInput, $output);

        $output->writeln('<info>The movies domain was created and configured.<info>');
    }
}
