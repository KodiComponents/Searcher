<?php

namespace KodiComponents\Searcher\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{

    /**
     * Document Score.
     *
     * Hit score when using data
     *
     * @var null|int
     */
    protected $documentScore = null;

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->getTable();
    }

    /**
     * @return int|null
     */
    public function getDocumentScore()
    {
        return $this->documentScore;
    }

    /**
     * @param int $score
     *
     * @return $this
     */
    public function setDocumentScore($score)
    {
        $this->documentScore = (int) $score;

        return $this;
    }

    /**
     * @return array
     */
    public function getSearchDocumentData()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function getSearchParams()
    {
        return [];
    }
    
    /**s
     * @param array $ids
     *
     * @return Builder
     */
    public function getQueryForFoundDocuments(array $ids)
    {
        return static::newQuery()->whereIn('id', $ids);
    }
}