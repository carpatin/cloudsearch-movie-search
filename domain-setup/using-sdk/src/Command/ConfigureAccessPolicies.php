<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigureAccessPolicies
 *
 * @package App\Command
 */
class ConfigureAccessPolicies extends AbstractCommand
{
    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->setName('sdk:configure-access-policies-domain');

        $this->setDescription('Configure access policies');

        $this->setHelp('This command allows you to configure access policies for cloudSearch domain');
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
        $output->writeln(' - Start configuring access policies.');

        // get cloudSearch client
        try {
            $cloudSearchClient = $this->getSdkClient();
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            return;
        }

        // get domain name option
        $domainName = $input->getOption('domain-name');

        // configure access policies
        $this->configureAccessPolicies($cloudSearchClient, $domainName);

        $output->writeln(' - Finished configuring access policies.');
    }

    /**
     * Configures access policies for domain
     *
     * @param \Aws\CloudSearch\CloudSearchClient $cloudSearchClient
     * @param string $domainName
     */
    protected function configureAccessPolicies($cloudSearchClient, $domainName)
    {
        $cloudSearchClient->updateServiceAccessPolicies([
            'DomainName' => $domainName,
            'AccessPolicies' => '{
              "Version": "2012-10-17",
              "Statement": [
                {
                  "Effect": "Allow",
                  "Principal": {
                    "AWS": [
                      "*"
                    ]
                  },
                  "Action": [
                    "cloudsearch:*"
                  ]
                }
              ]
            }',
        ]);
    }
}
