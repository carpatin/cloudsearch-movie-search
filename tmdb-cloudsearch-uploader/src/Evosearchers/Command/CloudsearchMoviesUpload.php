<?php

namespace Evosearchers\Command;

use Evosearchers\CloudsearchUploader;
use Evosearchers\MovieDocumentBatch;
use Evosearchers\MovieDocumentBatchFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command that uploads all documents in an import file to a CS domain
 *
 * @package Evosearchers\Command
 */
class CloudsearchMoviesUpload extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The JSON data file');
        $this->addArgument('domain', InputArgument::REQUIRED, 'The cloudsearch domain');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $domain = $input->getArgument('domain');

        /** @var MovieDocumentBatchFactory $batchFactory */
        $batchFactory = $this->getApplication()->getContainer()->get('document_batch_factory');
        $batch = $batchFactory->createFromFile($file);

        /** @var CloudsearchUploader $cloudSearchUploader */
        $cloudSearchUploader = $this->getApplication()->getContainer()->get('cloudsearch_updater');
        $documentCount = $cloudSearchUploader->uploadMovieDocumentBatch($batch, $domain);
        $output->writeln(sprintf('Uploaded %d documents to Cloudsearch domain %s', $documentCount, $domain));
    }
}
