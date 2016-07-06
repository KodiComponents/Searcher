<?php

namespace KodiComponents\Searcher\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AlgoliaConfiguratorInterface extends SearchConfiguratorInterface
{
    /**
     * @param string $query
     *
     * @return array
     */
    public function getSearchParams($query);

    /**
     * @param Model $model
     *
     * @return array
     */
    public function getDocumentData(Model $model);
}