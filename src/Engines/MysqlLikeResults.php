<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Collection;
use KodiComponents\Searcher\Contracts\SearchResultsInterface;

class MysqlLikeResults implements SearchResultsInterface
{
    /**
     * @var Collection
     */
    protected $result;

    /**
     * @param Collection $result
     */
    public function __construct(Collection $result)
    {
        $this->result = $result;
    }

    /**
     * @return Collection
     */
    public function getResult()
    {
        return $this->result;
    }
}