<?php

namespace Evosearchers;

use Tmdb\ApiToken;
use Tmdb\Client;

/**
 * Imports movies from tMDB based on discover API - reads up to a given number of pages for a given list of years from tMDB
 * sorted by popularity and reads movie data in various languages.
 * Prepares and returns movie document batch.
 *
 * @package Evosearchers
 */
class TmdbMovieImporter
{

    private $apiKey;

    /**
     * @var Client
     */
    private $client;
    private $genres;
    private $batchFactory;

    /**
     * @param string                    $apiKey
     * @param MovieDocumentBatchFactory $batchFactory
     */
    public function __construct(string $apiKey, MovieDocumentBatchFactory $batchFactory)
    {
        $this->apiKey = $apiKey;
        $this->batchFactory = $batchFactory;
    }

    /**
     * @param array $years
     * @param array $languages
     * @param int   $pages
     *
     * @return MovieDocumentBatch
     */
    public function importMovieBatch(array $years, array $languages, int $pages): MovieDocumentBatch
    {
        $token = new ApiToken($this->apiKey);
        $this->client = new Client($token);

        $movieBatch = new MovieDocumentBatch();
        foreach ($years as $year)
        {
            $totalPages = 1;
            for ($page = 1; $page <= $pages && $page <= $totalPages; $page++)
            {
                foreach ($languages as $language)
                {
                    $selected = $this->client->getDiscoverApi()->discoverMovies(
                        [
                            'language'             => $language,
                            'page'                 => $page,
                            'primary_release_year' => $year,
                        ]
                    );

                    $totalPages = $selected['total_pages'];
                    $movies = $selected['results'];
                    $this->preProcessMovies($movies, $language);
                    $movieBatch->addMovies($movies, $language);
                }
            }
        }

        return $movieBatch;
    }

    /**
     * @param array  $movies
     * @param string $language
     */
    private function preProcessMovies(array &$movies, string $language)
    {
        foreach ($movies as &$movie)
        {
            $this->expandGenres($movie, $language);
        }
    }

    /**
     * @param array  $movie
     * @param string $language
     */
    private function expandGenres(array &$movie, string $language)
    {
        if (!isset($this->genres[$language]))
        {
            // Obtain genres from API and flatten array
            $genres = $this->client->getGenresApi()->getMovieGenres(['language' => $language]);
            $this->genres[$language] = [];
            foreach ($genres['genres'] as $genre)
            {
                $this->genres[$language][$genre['id']] = $genre['name'];
            }
        }

        $localizedGenres = $this->genres[$language];

        // Expand genre IDs array to genre names array for movie
        if (isset($movie['genre_ids']))
        {
            $genres = [];
            foreach ($movie['genre_ids'] as $genreId)
            {
                if (isset($localizedGenres[$genreId]))
                {
                    $genres[] = $localizedGenres[$genreId];
                }
            }
            $movie['genres'] = $genres;
        }
    }
}
