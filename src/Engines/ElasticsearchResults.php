<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\SearchEngineInterface;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;

class ElasticSearchResults implements SearchResultsInterface
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

        $items = array_get($this->result, 'hits.hits', []);

        foreach ($items as $hit) {
            $ids[array_get($hit, '_id')] = $hit;
        }

        $items = $this->engine->getConfigurator()->getQueryForFoundDocuments($ids)->get();

        //$items->each(function(Model $item) use($items) {
        //    // In addition to setting the attributes
        //    // from the index, we will set the score as well.
        //    $item->setDocumentScore(array_get($items, $item->getKey() . '._score'));
        //
        //    // Set our document version
        //    $item->documentVersion = array_get($items, $item->getKey() . '._version');
        //});

        return $items;
    }
}