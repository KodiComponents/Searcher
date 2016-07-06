<?php

namespace KodiComponents\Searcher\Configurators;

use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\ElasticSearchConfiguratorInterface;

class ElasticSearch extends Configurator implements ElasticSearchConfiguratorInterface
{
    /**
     * @var int
     */
    protected $numberOfShards;

    /**
     * @var int
     */
    protected $numberOfReplicas;

    /**
     * @var array
     */
    protected $mappingProperties;

    /**
     * ElasticSearch constructor.
     *
     * @param Model    $model
     * @param array    $mappingProperties
     * @param int|null $numberOfShards
     * @param int|null $numberOfReplicas
     */
    public function __construct(Model $model, array $mappingProperties, $numberOfShards = null, $numberOfReplicas = null)
    {
        parent::__construct($model);
        
        $this->mappingProperties = $mappingProperties;
        $this->numberOfShards = $numberOfShards;
        $this->numberOfReplicas = $numberOfReplicas;
    }

    /**
     * @return string
     */
    public function engine()
    {
        return 'elastic_search';
    }

    /**
     * Get Mapping Properties.
     *
     * @return array
     */
    public function getMappingProperties()
    {
        return $this->mappingProperties;
    }

    /**
     * Set Mapping Properties.
     *
     * @param array $mapping
     *
     * @internal param array $mappingProperties
     */
    public function setMappingProperties(array $mapping)
    {
        $this->mappingProperties = $mapping;
    }

    /**
     * @return int
     */
    public function getNumberOfShards()
    {
        return $this->numberOfShards;
    }

    /**
     * @return int
     */
    public function getNumberOfReplicas()
    {
        return $this->numberOfReplicas;
    }

    /**
     * @param string $query
     *
     * @return array
     */
    public function getSearchParams($query)
    {
        $params = $this->getParams($this->getModel());

        unset($params['id']);

        $params['body']['query']['match']['_all'] = $query;

        return $params;
    }

    /**
     * @param Model $model
     *
     * @return array
     */
    public function getParams(Model $model)
    {
        return [
            'index' => $this->index(),
            'type' => $model->getTable(),
            'id' => $model->getKey(),
        ];
    }

    /**
     * @param Model $model
     *
     * @return array
     */
    public function getDocumentData(Model $model)
    {
        return $model->toArray();
    }
}