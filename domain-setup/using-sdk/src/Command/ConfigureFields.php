<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigureFields
 *
 * @package App\Command
 */
class ConfigureFields extends AbstractCommand
{
    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->setName('sdk:configure-fields-domain');

        $this->setDescription('Configure fields');

        $this->setHelp('This command allows you to configure fields for cloudSearch domain');
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
        $output->writeln(' - Start configuring fields.');

        // get cloudSearch client
        try {
            $cloudSearchClient = $this->getSdkClient();
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return;
        }

        // get domain name option
        $domainName = $input->getOption('domain-name');

        // create and configure fields
        $this->configureFields($cloudSearchClient, $domainName);

        $output->writeln(' - Finished configuring fields.');
    }

    /**
     * Configures fields for domain
     *
     * @param \Aws\CloudSearch\CloudSearchClient $cloudSearchClient
     * @param string $domainName
     */
    protected function configureFields($cloudSearchClient, $domainName)
    {
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => 'vote_count',
                'IndexFieldType'   => 'int',
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => 'vote_average',
                'IndexFieldType'   => 'double',
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => 'original_title',
                'IndexFieldType'   => 'literal',
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => 'release_date',
                'IndexFieldType'   => 'date',
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => '*_en',
                'IndexFieldType'   => 'text',
                'TextOptions' => [
                    'AnalysisScheme' => 'english',
                ],
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => '*_ro',
                'IndexFieldType'   => 'text',
                'TextOptions' => [
                    'AnalysisScheme' => 'romanian',
                ],
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => '*_fuzzy_en',
                'IndexFieldType'   => 'text',
                'TextOptions' => [
                    'AnalysisScheme' => 'fuzzy_english',
                ],
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => '*_fuzzy_ro',
                'IndexFieldType'   => 'text',
                'TextOptions' => [
                    'AnalysisScheme' => 'fuzzy_romanian',
                ],
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => '*_multi_en',
                'IndexFieldType'   => 'text-array',
                'TextArrayOptions' => [
                    'AnalysisScheme' => 'english',
                ],
            ],
        ]);
        $cloudSearchClient->defineIndexField([
            'DomainName' => $domainName,
            'IndexField'      => [
                'IndexFieldName' => '*_multi_ro',
                'IndexFieldType'   => 'text-array',
                'TextArrayOptions' => [
                    'AnalysisScheme' => 'romanian',
                ],
            ],
        ]);
    }
}
