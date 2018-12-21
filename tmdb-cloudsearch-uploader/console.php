<?php

require __DIR__.'/vendor/autoload.php';

use Aws\Sdk;
use Evosearchers\Application;
use Evosearchers\CloudsearchUploader;
use Evosearchers\MovieDocumentBatchFactory;
use Evosearchers\TmdbMovieImporter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

const AWS_VERSION = '2013-01-01';
const AWS_REGION = 'eu-central-1';

// Setup DI container
$container = new ContainerBuilder();

// Import configuration into DI container
$fileLocator = new FileLocator([__DIR__.'/config']);
$loader = new YamlFileLoader($container, $fileLocator);
$loader->load('config.yaml');

$container->setParameter('data_dir', __DIR__.'/data');

// Setup container instances
$apiKey = $container->getParameter('tmdb')['api_key'];
$container->register('document_batch_factory', MovieDocumentBatchFactory::class)
    ->setArguments([$container->getParameter('data_dir')]);


$apiKey = $container->getParameter('tmdb')['api_key'];
$container->register('tmdb_movie_importer', TmdbMovieImporter::class)
    ->setArguments([$apiKey, new Reference('document_batch_factory')]);


// Setup AWS SDK in DI container
$accessKey = $container->getParameter('cloudsearch')['accessKey'];
$secretKey = $container->getParameter('cloudsearch')['secretKey'];

$container->register('aws_sdk', Sdk::class)
    ->addArgument(
        [
            'credentials' => array(
                'key'    => $accessKey,
                'secret' => $secretKey,
            ),
            'version'     => AWS_VERSION,
            'region'      => AWS_REGION,
        ]
    );

$container->register('cloudsearch_updater', CloudsearchUploader::class)
    ->addArgument(new Reference('aws_sdk'));


// Create application and add commands
$application = new Application($container, 'TMDB to Cloudsearch');
$application->add(new Evosearchers\Command\CloudsearchMoviesUpload('cloudsearch:moviesUpload'));
$application->add(new Evosearchers\Command\TmdbImportMovies('tmdb:moviesImport'));
$application->add(new Evosearchers\Command\FullMoviesImportAndUpload('full:moviesImportAndUpload'));

$application->run();
