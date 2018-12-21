<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TriggerReindexing
 * 
 * @package App\Command
 */
class TriggerReindexing extends AbstractCommand
{

    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->setName('sdk:trigger-reindexing');

        $this->setDescription('Triggers reindexing');

        $this->setHelp('This command allows you to reindex the domain');
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
        $output->writeln(' - Start indexing.');

        // get cloudSearch client
        try {
            $cloudSearchClient = $this->getSdkClient();
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return;
        }

        // get domain name option
        $domainName = $input->getOption('domain-name');

        // trigger reindexing
        $this->triggerReindex($cloudSearchClient, $domainName);

        $output->writeln(' - Finished indexing.');
    }

    /**
     * Triggers reindexing
     *
     * @param \Aws\CloudSearch\CloudSearchClient $cloudSearchClient
     * @param string $domainName
     */
    protected function triggerReindex($cloudSearchClient, $domainName)
    {
        $cloudSearchClient->indexDocuments([
            'DomainName' => $domainName,
        ]);
    }
}
