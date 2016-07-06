<?php

namespace KodiComponents\Searcher\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SearchConfiguratorInterface
{
    /**
     * @return string
     */
    public function engine();

    /**
     * @return string
     */
    public function index();

    /**
     * @return Model
     */
    public function getModel();

    /**
     * @param array $ids
     *
     * @return Builder
     */
    public function getQueryForFoundDocuments(array $ids);
}