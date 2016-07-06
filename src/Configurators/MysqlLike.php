<?php

namespace KodiComponents\Searcher\Configurators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MysqlLike extends Configurator
{
    /**
     * @var array
     */
    private $searchFields;

    /**
     * MysqlLike constructor.
     *
     * @param Model $model
     * @param array $searchFields
     */
    public function __construct(Model $model, array $searchFields)
    {
        parent::__construct($model);

        $this->searchFields = $searchFields;
    }

    /**
     * @return string
     */
    public function engine()
    {
        return 'mysql_like';
    }

    /**
     * @return array
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * @param mixed   $query
     * @param Builder $builder
     */
    public function prepareQuery($query, Builder $builder)
    {
        foreach ($this->getSearchFields() as $field) {
            $builder->orWhere($field, 'like', "%{$query}%");
        }
    }
}