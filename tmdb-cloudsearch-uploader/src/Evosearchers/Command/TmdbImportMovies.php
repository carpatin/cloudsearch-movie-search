<?php

namespace Evosearchers\Command;

use Evosearchers\TmdbMovieImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that imports movies from TMDB to a local import file
 *
 * @package Evosearchers\Command
 */
class TmdbImportMovies extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->addArgument('languages', InputArgument::REQUIRED, 'Languages comma separated list to load content for');
        $this->addArgument('pages', InputArgument::REQUIRED, 'Number of pages to load per year');
        $this->addArgument('years', InputArgument::REQUIRED, 'List of comma separated years to get movies for');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $years = explode(',', $input->getArgument('years'));
        $pages = (int)$input->getArgument('pages');
        $languages = explode(',', $input->getArgument('languages'));

        /** @var TmdbMovieImporter $tmdbImporter */
        $tmdbImporter = $this->getApplication()->getContainer()->get('tmdb_movie_importer');
        $movieBatch = $tmdbImporter->importMovieBatch($years, $languages, $pages);
        $file = $movieBatch->writeToFile($this->getApplication()->getContainer()->getParameter('data_dir'));
        $output->writeln(sprintf('Saved movie documents imported to file %s', $file));
    }
}
