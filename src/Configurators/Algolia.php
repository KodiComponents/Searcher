<?php

namespace KodiComponents\Searcher\Configurators;

use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\AlgoliaConfiguratorInterface;

class Algolia extends Configurator implements AlgoliaConfiguratorInterface
{
    /**
     * @return string
     */
    public function engine()
    {
        return 'algolia';
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function getSearchParams($query, array $params = [])
    {
        return [];
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