<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Collection;
use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchEngineInterface;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;

class AlgoliaResults implements SearchResultsInterface
{
    /**
     * @var array
     */
    protected $result;

    /**
     * @var SearchEngineInterface
     */
    protected $engine;

    /**
     * @param SearchEngineInterface $engine
     * @param array $result
     */
    public function __construct(SearchEngineInterface $engine, array $result)
    {
        $this->result = $result;
        $this->engine = $engine;
    }

    /**
     * @return Collection
     */
    public function getResult()
    {
        $ids = [];

        foreach ($this->result['hits'] as $hit) {
            $ids[array_get($hit, 'objectID')] = $hit;
        }

        return $this
            ->engine
            ->getConfigurator()
            ->getQueryForFoundDocuments($ids)
            ->get();
    }
}