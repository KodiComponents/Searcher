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
     *
     * @return SearchResultsInterface
     */
    public function search($query = "")
    {
        /** @var Builder $builder */
        $builder = $this->getModel()->newQuery();

        foreach ($this->getConfigurator()->getSearchFields() as $index => $field) {
            if(is_callable($field)) {
                $field($builder, $query);
            } else {
                $builder->orWhere($field, 'like', "%{$query}%");
            }
        }

        return new MysqlLikeResults($builder->get());
    }
}