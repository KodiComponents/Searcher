<?php

namespace KodiComponents\Searcher\Engines;

use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;
use KodiComponents\Searcher\Exceptions\DocumentMissingException;

class ElasticSearch extends Engine
{

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * Elasticsearch constructor.
     *
     * @param \Elasticsearch\Client $client
     * @param array $config
     */
    public function __construct(\Elasticsearch\Client $client, array $config)
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
        $params = $this->getModel()->getSearchParams();

        unset($params['id']);

        $params['body']['query']['match']['_all'] = $query;
        $result = $this->client->search($params);

        return new ElasticSearchResults($this->getModel(), $result);
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

        $params = $model->getSearchParams();

        // Get our document body data.
        $params['body'] = $model->getSearchDocumentData();

        // The id for the document must always mirror the
        // key for this model, even if it is set to something
        // other than an auto-incrementing value. That way we
        // can do things like remove the document from
        // the index, or get the document from the index.
        $params['id'] = $model->getKey();

        $this->client->index($params);
    }

    /**
     * @param Searchable $model
     */
    public function deleteDocumentFromIndex(Searchable $model)
    {
        $this->client->delete($model);
    }

    /**
     * @param Searchable $model
     *
     * @throws DocumentMissingException
     */
    public function reindexDocument(Searchable $model)
    {
        $this->deleteDocumentFromIndex($model);
        $this->addDocumentToIndex($model);
    }

    /**
     * Create Index.
     */
    public function createIndex()
    {
        $index = ['index' => $this->getModel()->getIndexName()];

        if (property_exists($this->getModel(), 'number_of_shards')) {
            $index['body']['settings']['number_of_shards'] = $this->getModel()->number_of_shards;
        }

        if (property_exists($this->getModel(), 'number_of_replicas')) {
            $index['body']['settings']['number_of_replicas'] = $this->getModel()->number_of_replicas;
        }

        $this->client->indices()->create($this->getModel()->getIndexName());
    }

    /**
     * @return void
     */
    public function deleteIndex()
    {
        $this->client->indices()->delete(['index' => $this->getModel()->getIndexName()]);
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
        return $this->client->indices()->exists(['index' => $this->getModel()->getIndexName()]);
    }
}