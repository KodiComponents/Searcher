<?php

namespace KodiComponents\Searcher\Configurators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\SearchConfiguratorInterface;

abstract class Configurator implements SearchConfiguratorInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * ElasticSearch constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function index()
    {
        return $this->getModel()->getTable();
    }

    /**
     * @param array $ids
     *
     * @return Builder
     */
    public function getQueryForFoundDocuments(array $ids)
    {
        return $this->getModel()->newQuery()->whereIn(
            $this->getModel()->getKeyName(),
            $ids
        );
    }
}