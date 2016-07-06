<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Collection;
use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;

class ElasticSearchResults implements SearchResultsInterface
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

        $items = array_get($this->result, 'hits.hits', []);

        foreach ($items as $hit) {
            $ids[array_get($hit, '_id')] = $hit;
        }

        $items = $this->model->getQueryForFoundDocuments($ids)->get();

        $items->each(function(Searchable $item) use($items) {
            // In addition to setting the attributes
            // from the index, we will set the score as well.
            $item->setDocumentScore(array_get($items, $item->getKey() . '._score'));

            // Set our document version
            $item->documentVersion = array_get($items, $item->getKey() . '._version');
        });

        return $items;
    }
}