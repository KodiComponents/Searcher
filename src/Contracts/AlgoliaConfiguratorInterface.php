<?php

namespace KodiComponents\Searcher\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AlgoliaConfiguratorInterface extends SearchConfiguratorInterface
{
    /**
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function getSearchParams($query, array $params = []);

    /**
     * @param Model $model
     *
     * @return array
     */
    public function getDocumentData(Model $model);
}