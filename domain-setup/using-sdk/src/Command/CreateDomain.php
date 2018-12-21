<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreateDomain
 *
 * @package App\Command
 */
class CreateDomain extends AbstractCommand
{

    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->setName('sdk:create-domain');

        $this->setDescription('Create a new domain');

        $this->setHelp('This command allows you to create a new cloudSearch domain.');

        $this->addOption('domain-name', null, InputOption::VALUE_REQUIRED, 'Domain name', 'movies');
    }

    /**
     * Executes command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(' - Start creating new domain.');

        // get cloudSearch client
        try {
            $cloudSearchClient = $this->getSdkClient();
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return;
        }

        // get domain name option
        $domainName = $input->getOption('domain-name');

        // create domain
        $this->createDomain($cloudSearchClient, $domainName);

        $output->writeln(' - Finished creating domain.');
    }

    /**
     * Creates domain
     *
     * @param \Aws\CloudSearch\CloudSearchClient $cloudSearchClient
     * @param string $domainName
     */
    protected function createDomain($cloudSearchClient, $domainName)
    {
        $cloudSearchClient->createDomain([
            'DomainName' => $domainName,
        ]);
    }
}
