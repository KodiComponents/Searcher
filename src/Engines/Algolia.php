<?php

namespace KodiComponents\Searcher\Engines;

use AlgoliaSearch\Index;
use Illuminate\Support\Arr;
use KodiComponents\Searcher\Contracts\Model;
use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;
use KodiComponents\Searcher\Exceptions\DocumentMissingException;

class Algolia extends Engine
{

    /**
     * @var \Vinkla\Algolia\AlgoliaManager|\AlgoliaSearch\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \AlgoliaSearch\Index
     */
    protected $index;

    /**
     * Algolia constructor.
     *
     * @param \Vinkla\Algolia\AlgoliaManager|\AlgoliaSearch\Client $client
     * @param array $config
     */
    public function __construct(\Vinkla\Algolia\AlgoliaManager $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $query
     *
     * @return SearchResultsInterface
     */
    public function search($query = "")
    {
        $this->getIndex($this->getModel())->search($query, $this->getModel()->getSearchParams());
    }

    /**
     * Create Index.
     *
     * @return Index
     */
    public function createIndex()
    {
        return $this->client->initIndex($this->getModel()->getIndexName());
    }

    /**
     * @return void
     */
    public function deleteIndex()
    {
        $this->client->deleteIndex($this->getModel()->getIndexName());
    }

    /**
     * Index Exists.
     *
     * Does this index exist?
     *
     * @return bool
     */
    public function indexExists()
    {
        foreach (Arr::get($this->client->listIndexes(), 'items', []) as $index) {
            if ($index['name'] == $this->getModel()->getIndexName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Searchable $model
     *
     * @throws DocumentMissingException
     */
    public function addDocumentToIndex(Searchable $model)
    {
        if (! $model->exists) {
            throw new DocumentMissingException('Document does not exist.');
        }
        
        $this->getIndex()->addObject($model->getSearchDocumentData(), $model->getKey());
    }

    /**
     * @param Searchable $model
     *
     * @throws \Exception
     */
    public function deleteDocumentFromIndex(Searchable $model)
    {
        $this->getIndex()->deleteObject($model->getKey());
    }

    /**
     * @param Searchable $model
     */
    public function reindexDocument(Searchable $model)
    {
        $this->getIndex()->saveObject($model->getSearchDocumentData(), $model->getKey());
    }

    /**
     * @return Index
     */
    protected function getIndex()
    {
        if (! $this->index) {
            $this->createIndex($this->getModel());
        }

        return $this->index;
    }
}