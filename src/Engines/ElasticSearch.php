<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\ElasticSearchConfiguratorInterface;
use KodiComponents\Searcher\Contracts\Indexable;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;
use KodiComponents\Searcher\Exceptions\DocumentMissingException;

class ElasticSearch extends Engine implements Indexable
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
     * @return ElasticSearchConfiguratorInterface
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
        return new ElasticSearchResults(
            $this,
            $this->client->search(
                $this->getConfigurator()->getSearchParams($query)
            )
        );
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

        $params = $this->getConfigurator()->getParams($model);

        // Get our document body data.
        $params['body'] = $this->getConfigurator()->getDocumentData($model);

        $this->client->index($params);
    }

    /**
     * @param Model $model
     */
    public function deleteDocumentFromIndex(Model $model)
    {
        $this->client->delete($model);
    }

    /**
     * @param Model $model
     *
     * @throws DocumentMissingException
     */
    public function reindexDocument(Model $model)
    {
        $this->deleteDocumentFromIndex($model);
        $this->addDocumentToIndex($model);
    }

    /**
     * Create Index.
     */
    public function createIndex()
    {
        $index = ['index' => $this->getConfigurator()->index()];

        if ($shards = $this->getConfigurator()->getNumberOfShards()) {
            $index['body']['settings']['number_of_shards'] = $shards;
        }

        if ($replicas = $this->getConfigurator()->getNumberOfReplicas()) {
            $index['body']['settings']['number_of_replicas'] = $replicas;
        }

        $this->client->indices()->create($index);

        $this->rebuildMapping();
    }

    /**
     * @return void
     */
    public function deleteIndex()
    {
        $this->client->indices()->delete([
            'index' => $this->getConfigurator()->index()
        ]);

        $this->deleteMapping();
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
        return $this->client->indices()->exists([
            'index' => $this->getConfigurator()->index()
        ]);
    }

    /**
     * Get Mapping.
     */
    public function getMapping()
    {
        return $this->client->indices()->getMapping(
            $this->getConfigurator()->getSearchParams($this->getModel())
        );
    }

    /**
     * Put Mapping.
     *
     * @param  bool $ignoreConflicts
     *
     * @return void
     */
    public function putMapping($ignoreConflicts = false)
    {
        $mapping = $this->getConfigurator()->getSearchParams($this->getModel());

        unset($mapping['id']);

        $params = [
            '_source' => ['enabled' => true],
            'properties' => $this->getConfigurator()->getMappingProperties(),
        ];

        $mapping['body'][$this->getModel()->getTable()] = $params;
        $mapping['ignore_conflicts'] = $ignoreConflicts;

        $this->client->indices()->putMapping($mapping);
    }

    /**
     * Delete Mapping.
     *
     * @return void
     */
    public function deleteMapping()
    {
        $mapping = $this->getConfigurator()->getSearchParams($this->getModel());

        $this->client->indices()->deleteMapping($mapping);
    }

    /**
     * Rebuild Mapping.
     *
     * This will delete and then re-add
     * the mapping for this model.
     *
     * @return void
     */
    public function rebuildMapping()
    {
        // If the mapping exists, let's delete it.
        if ($this->mappingExists()) {
            $this->deleteMapping();
        }

        // Don't need ignore conflicts because if we
        // just removed the mapping there shouldn't
        // be any conflicts.
        $this->putMapping();
    }

    /**
     * Mapping Exists.
     *
     * @return bool
     */
    public function mappingExists()
    {
        $mapping = $this->getConfigurator()->getMapping();

        return (empty($mapping)) ? false : true;
    }
}