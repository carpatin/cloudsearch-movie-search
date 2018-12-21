<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigureAnalysisScheme
 *
 * @package App\Command
 */
class ConfigureAnalysisScheme extends AbstractCommand
{
    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->setName('sdk:configure-analysis-scheme-domain');

        $this->setDescription('Configure analysis scheme');

        $this->setHelp('This command allows you to configure analysis scheme for cloudSearch domain');
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
        $output->writeln(' - Start configuring analysis scheme.');

        // get cloudSearch client
        try {
            $cloudSearchClient = $this->getSdkClient();
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return;
        }

        // get domain name option
        $domainName = $input->getOption('domain-name');

        // create and configure analysis scheme
        $this->configureAnalysisScheme($cloudSearchClient, $domainName);

        $output->writeln(' - Finished configuring analysis scheme.');
    }

    /**
     * Configures analysis scheme for domain
     *
     * @param \Aws\CloudSearch\CloudSearchClient $cloudSearchClient
     * @param string $domainName
     */
    protected function configureAnalysisScheme($cloudSearchClient, $domainName)
    {
        $cloudSearchClient->defineAnalysisScheme([
            'DomainName' => $domainName,
            'AnalysisScheme' => [
                'AnalysisSchemeName' => 'english',
                'AnalysisSchemeLanguage' => 'en',
                'AnalysisOptions' => [
                    "AlgorithmicStemming" => "none",
                ]
            ]
        ]);
        $cloudSearchClient->defineAnalysisScheme([
            'DomainName' => $domainName,
            'AnalysisScheme' => [
                'AnalysisSchemeName' => 'romanian',
                'AnalysisSchemeLanguage' => 'ro',
                'AnalysisOptions' => [
                    "AlgorithmicStemming" => "none",
                ]
            ]
        ]);
        $cloudSearchClient->defineAnalysisScheme([
            'DomainName' => $domainName,
            'AnalysisScheme' => [
                'AnalysisSchemeName' => 'fuzzy_english',
                'AnalysisSchemeLanguage' => 'en',
                'AnalysisOptions' => [
                    'AlgorithmicStemming' => 'full',
                    'Synonyms' => "{\"groups\": [[\"usual\", \"common\", \"typical\"]], \"aliases\": {\"car\": [\"jeep\", \"truck\", \"auto\"]}}",
                    'Stopwords' => "[\"a\", \"an\", \"and\", \"are\", \"as\", \"at\", \"be\", \"but\"]"
                ]
            ]
        ]);
        $cloudSearchClient->defineAnalysisScheme([
            'DomainName' => $domainName,
            'AnalysisScheme' => [
                'AnalysisSchemeName' => 'fuzzy_romanian',
                'AnalysisSchemeLanguage' => 'ro',
                'AnalysisOptions' => [
                    'AlgorithmicStemming' => 'full',
                    'Synonyms' => "{\"groups\": [[\"obişnuit\", \"comun\", \"tipic\"]], \"aliases\": {\"maşină\": [\"automobil\", \"camion\", \"dubă\"]}}",
                    'Stopwords' => "[\"un\",\"o\",\"pe\",\"la\"]"
                ]
            ]
        ]);
    }
}
