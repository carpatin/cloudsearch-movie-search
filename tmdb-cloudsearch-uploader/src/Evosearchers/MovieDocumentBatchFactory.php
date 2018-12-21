<?php

namespace Evosearchers;

/**
 * @package Evosearchers
 */
class MovieDocumentBatchFactory
{

    /**
     * @var string
     */
    private $dataDirectory;

    /**
     * @param string $dataDirectory
     */
    public function __construct(string $dataDirectory)
    {
        $this->dataDirectory = $dataDirectory;
    }

    /**
     * @param string $file Name of file to import from
     *
     * @return MovieDocumentBatch
     */
    public function createFromFile(string $file): MovieDocumentBatch
    {
        $filepath = $this->dataDirectory.DIRECTORY_SEPARATOR.$file;
        $fileObject = new \SplFileObject($filepath);

        $batch = new MovieDocumentBatch();
        foreach ($fileObject as $line)
        {
            if (empty($line))
            {
                continue;
            }

            $document = json_decode($line, true);
            $batch->addDocument($document);
        }

        return $batch;
    }

}
