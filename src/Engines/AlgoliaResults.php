<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Collection;
use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;

class AlgoliaResults implements SearchResultsInterface
{
    /**
     * @var array
     */
    protected $result;

    /**
     * @var Searchable
     */
    protected $model;

    /**
     * @param Searchable $model
     * @param array $result
     */
    public function __construct(Searchable $model, array $result)
    {
        $this->result = $result;
        $this->model = $model;
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

        $items = $this->model->getQueryForFoundDocuments($ids)->get();

        return $items;
    }
}