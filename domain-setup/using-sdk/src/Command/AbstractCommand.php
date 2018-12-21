<?php

namespace App\Command;

use \Aws\Sdk as Sdk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AbstractCommand
 *
 * @package App\Command
 */
abstract class AbstractCommand extends Command
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    /**
     * Configures command
     */
    public function configure()
    {
        parent::configure();

        $this->addOption('domain-name', null, InputOption::VALUE_REQUIRED, 'Domain name', 'movies');
    }

    /**
     * Returns SDK client
     *
     * @return \Aws\CloudSearch\CloudSearchClient
     *
     * @throws \Exception
     */
    protected function getSdkClient()
    {
        // read credentials file
        $fs = new Filesystem();
        $credentialsPath = $this->container->get('kernel')->getProjectDir() . '/' . 'config/aws-credentials.txt';
        if (!$fs->exists($credentialsPath))
        {
            throw new \Exception(
                'The file aws-credentials.txt is missing or could not be read. Please create it or review it.'
            );
        }
        $credentials = parse_ini_file($credentialsPath);

        // create SDK client
        $sdk = new Sdk([
            'region'      => 'eu-central-1',
            'version'     => '2013-01-01',
            'credentials' => [
                'key'    => $credentials['accessKey'],
                'secret' => $credentials['secretKey'],
            ]
        ]);
        $cloudSearchClient = $sdk->createCloudSearch();

        return $cloudSearchClient;
    }
}
