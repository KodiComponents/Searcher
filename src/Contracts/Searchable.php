<?php

namespace KodiComponents\Searcher\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Searchable
{
    /**
     * @return string
     */
    public function getIndexName();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return int|null
     */
    public function getDocumentScore();

    /**
     * @param int $score
     *
     * @return $this
     */
    public function setDocumentScore($score);

    /**
     * @return array
     */
    public function getSearchParams();

    /**
     * @return array
     */
    public function getSearchDocumentData();

    /**
     * @param array $ids
     *
     * @return Builder
     */
    public function getQueryForFoundDocuments(array $ids);
}