<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;

class MysqlLike extends Engine
{

    /**
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $config;

    /**
     * MySQL constructor.
     *
     * @param ConnectionInterface $db
     * @param array $config
     */
    public function __construct(ConnectionInterface $db, array $config = null)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * @return \KodiComponents\Searcher\Configurators\MysqlLike
     */
    public function getConfigurator()
    {
        return parent::getConfigurator();
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return SearchResultsInterface
     */
    public function search($query, array $params = [])
    {
        /** @var Builder $builder */
        $builder = $this->getModel()->newQuery();

        $this->getConfigurator()->prepareQuery($builder);

        return new MysqlLikeResults($builder->get());
    }
}