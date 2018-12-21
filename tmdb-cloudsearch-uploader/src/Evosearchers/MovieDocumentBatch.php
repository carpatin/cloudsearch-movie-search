<?php

namespace Evosearchers;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class MovieDocumentBatch
 *
 * @package Evosearchers
 */
class MovieDocumentBatch
{

    /**
     * @const Fields that are agnostic to language, their value is not language dependent.
     */
    const FIELDS_LANGUAGE_AGNOSTIC = [
        'id',
        'vote_count',
        'vote_average',
        'original_title',
    ];

    /**
     * @const Fields that contain dates
     */
    const FIELDS_DATE = [
        'release_date',
    ];

    /**
     * @const Fields with single value which are language specific and analysis is light for them.
     */
    const FIELDS_LANGUAGE_SPECIFIC_SIMPLE = ['title'];

    /**
     * @const Fields with single value which are language specific and analysis is more complex for them: stemming and stopwords.
     */
    const FIELDS_LANGUAGE_SPECIFIC_FUZZY = ['overview'];

    /**
     * @const Fields with multiple values which are language specific
     */
    const FIELDS_LANGUAGE_SPECIFIC_MULTI = ['genres'];

    /**
     * The contents of the batch
     *
     * @var array
     */
    private $documents = [];

    /**
     * Adds movies (as received from TMDB) to the batch as CS ready documents.
     *
     * @param array  $movies
     * @param string $language
     */
    public function addMovies(array $movies, string $language): void
    {
        foreach ($movies as $movie)
        {
            $movieId = $movie['id'];
            if (!isset($this->documents[$movieId]))
            {
                $this->documents[$movieId] = $this->extractLanguageAgnosticFields($movie);
            }
            $this->documents[$movieId] =
                array_merge($this->documents[$movieId], $this->extractLanguageDependentFields($movie, $language));
        }
    }

    /**
     * @param array $movie
     *
     * @return array
     */
    private function extractLanguageAgnosticFields(array $movie): array
    {
        $extracted = [];

        foreach (self::FIELDS_LANGUAGE_AGNOSTIC as $field)
        {
            if (isset($movie[$field]))
            {
                $extracted[$field] = $movie[$field];
            }
        }

        foreach (self::FIELDS_DATE as $field)
        {
            if (isset($movie[$field]))
            {
                $date = \DateTime::createFromFormat("Y-m-d", $movie[$field]);
                $extracted[$field] = $date->format("Y-m-d\TH:i:s.v\Z");
            }
        }

        return $extracted;
    }

    /**
     * @param array  $movie
     * @param string $language
     *
     * @return array
     */
    private function extractLanguageDependentFields(array $movie, string $language): array
    {
        $extracted = [];

        foreach (self::FIELDS_LANGUAGE_SPECIFIC_SIMPLE as $field)
        {
            if (isset($movie[$field]))
            {
                $extracted[$field.'_'.$language] = $movie[$field];
            }
        }

        foreach (self::FIELDS_LANGUAGE_SPECIFIC_FUZZY as $field)
        {
            if (isset($movie[$field]))
            {
                $extracted[$field.'_fuzzy_'.$language] = $movie[$field];
            }
        }

        foreach (self::FIELDS_LANGUAGE_SPECIFIC_MULTI as $field)
        {
            if (isset($movie[$field]))
            {
                $extracted[$field.'_multi_'.$language] = $movie[$field];
            }
        }

        return $extracted;
    }

    /**
     * Write batch contents to a file
     *
     * @param string $directory
     *
     * @return string The file name
     */
    public function writeToFile(string $directory): string
    {
        $filesystem = new Filesystem();
        $filename = date('YMdHis');
        $filepath = $directory.DIRECTORY_SEPARATOR.$filename;
        foreach ($this->documents as $movie)
        {
            $filesystem->appendToFile($filepath, json_encode($movie).PHP_EOL);
        }

        return $filename;
    }

    /**
     * @return array
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @param array $document
     */
    public function addDocument(array $document)
    {
        $this->documents[] = $document;
    }
}
