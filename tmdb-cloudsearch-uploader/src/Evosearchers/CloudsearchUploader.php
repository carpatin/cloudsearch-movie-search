<?php

namespace Evosearchers;

use Aws\Sdk;

class CloudsearchUploader
{

    /**
     * @const Cloudsearch documents batch size
     */
    const BATCH_SIZE = 100;

    /**
     * @var Sdk
     */
    private $sdk;

    /**
     * @param Sdk $sdk
     */
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    /**
     * @param MovieDocumentBatch $batch
     * @param string             $domain
     *
     * @return int The uploaded documents count
     */
    public function uploadMovieDocumentBatch(MovieDocumentBatch $batch, string $domain): int
    {

        $client = $this->sdk->createCloudSearchDomain(['endpoint' => $this->getEndpoint($domain)]);

        $documentsChunks = array_chunk($batch->getDocuments(), self::BATCH_SIZE);
        $uploaded = 0;
        foreach ($documentsChunks as $chunk)
        {
            $sdfChunk = $this->prepareSdfChunk($chunk);

            try
            {
                $client->uploadDocuments(
                    [
                        'documents'   => json_encode($sdfChunk),
                        'contentType' => 'application/json',
                    ]
                );
                $uploaded += count($sdfChunk);
            } catch (\Exception $ex)
            {
                // best effort upload, we don't stop because of chunk failed
                print $ex->getMessage();
            }
        }

        return $uploaded;
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    private function getEndpoint(string $domain): string
    {
        return 'https://doc-'.$domain;
    }

    /**
     * Prepares Cloudsearch SDFs for a chunk of documents
     *
     * @param array $chunk Chunk of documents
     *
     * @return array
     */
    private function prepareSdfChunk($chunk)
    {
        return array_map(
            function ($document) {
                $id = $document['id'];
                unset($document['id']);
                return [
                    'type'   => 'add',
                    'id'     => $id,
                    'fields' => $document,
                ];
            },
            $chunk
        );
    }
}
