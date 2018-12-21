<?php

namespace Evosearchers\Command;

use Evosearchers\CloudsearchUploader;
use Evosearchers\TmdbMovieImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that:
 * - imports movies from TMDB into a document batch
 * - uploads documents batch to the given Cloudsearch domain
 *
 * @package Evosearchers\Command
 */
class FullMoviesImportAndUpload extends Command
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->addArgument('languages', InputArgument::REQUIRED, 'Languages comma separated list to load content for');
        $this->addArgument('pages', InputArgument::REQUIRED, 'Number of pages to load per year');
        $this->addArgument('years', InputArgument::REQUIRED, 'List of comma separated years to get movies for');
        $this->addArgument('domain', InputArgument::REQUIRED, 'Full host of the Cloudsearch domain to upload to');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = explode(',', $input->getArgument('languages'));
        $years = explode(',', $input->getArgument('years'));
        $pages = (int)$input->getArgument('pages');
        $domain = $input->getArgument('domain');

        $output->writeln('Importing movies data from TMDB...');
        /** @var TmdbMovieImporter $tmdbImporter */
        $tmdbImporter = $this->getApplication()->getContainer()->get('tmdb_movie_importer');
        $movieBatch = $tmdbImporter->importMovieBatch($years, $languages, $pages);

        $output->writeln('Uploading movies data to Cloudsearch...');
        /** @var CloudsearchUploader $cloudSearchUploader */
        $cloudSearchUploader = $this->getApplication()->getContainer()->get('cloudsearch_updater');
        $cloudSearchUploader->uploadMovieDocumentBatch($movieBatch, $domain);
    }
}
