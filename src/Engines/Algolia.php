<?php

namespace KodiComponents\Searcher\Engines;

use AlgoliaSearch\Index;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use KodiComponents\Searcher\Contracts\AlgoliaConfiguratorInterface;
use KodiComponents\Searcher\Contracts\Indexable;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;
use KodiComponents\Searcher\Exceptions\DocumentMissingException;

class Algolia extends Engine implements Indexable
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
     * @return AlgoliaConfiguratorInterface
     */
    public function getConfigurator()
    {
        return parent::getConfigurator();
    }

    /**
     * @param string $query
     *
     * @return SearchResultsInterface
     */
    public function search($query = "")
    {
        $results = $this->getIndex()->search(
            $query,
            $this->getConfigurator()->getSearchParams($query)
        );

        return new AlgoliaResults($this, $results);
    }

    /**
     * Create Index.
     *
     * @return Index
     */
    public function createIndex()
    {
        return $this->client->initIndex($this->getConfigurator()->index());
    }

    /**
     * @return void
     */
    public function deleteIndex()
    {
        $this->client->deleteIndex($this->getConfigurator()->index());
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
            if ($index['name'] == $this->getConfigurator()->index()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Model $model
     *
     * @throws DocumentMissingException
     */
    public function addDocumentToIndex(Model $model)
    {
        if (! $model->exists) {
            throw new DocumentMissingException('Document does not exist.');
        }
        
        $this->getIndex()->addObject(
            $this->getConfigurator()->getDocumentData($model),
            $model->getKey()
        );
    }

    /**
     * @param Model $model
     *
     * @throws \Exception
     */
    public function deleteDocumentFromIndex(Model $model)
    {
        $this->getIndex()->deleteObject($model->getKey());
    }

    /**
     * @param Model $model
     */
    public function reindexDocument(Model $model)
    {
        $this->getIndex()->saveObject(
            $this->getConfigurator()->getDocumentData($model),
            $model->getKey()
        );
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