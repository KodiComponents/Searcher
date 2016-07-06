<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use KodiComponents\Searcher\Contracts\Model;
use KodiComponents\Searcher\Contracts\Searchable;
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
     * @param string $query
     * @param int|null $perPage
     * @param int $offset
     *
     * @return SearchResultsInterface
     */
    public function search($query = "", $perPage = null, $offset = 0)
    {
        /** @var \Illuminate\Database\Query\Builder $q */
        $q = $this->getModel()->newQuery();

        foreach ($this->getModel()->getSearchParams() as $field) {
            $q->orWhere($field, 'like', "%{$query}%");
        }

        return new MysqlLikeResults($q->get());
    }

    /**
     * Create Index.
     *
     * @return void
     */
    public function createIndex() {}

    /**
     * @return void
     */
    public function deleteIndex() {}

    /**
     * Index Exists.
     *
     * Does this index exist?
     *
     * @return bool
     */
    public function indexExists() {}

    /**
     * @param Searchable $model
     *
     * @return void
     */
    public function addDocumentToIndex(Searchable $model) {}

    /**
     * @param Searchable $model
     *
     * @return void
     */
    public function deleteDocumentFromIndex(Searchable $model) {}

    /**
     * @param Searchable $model
     *
     * @return void
     */
    public function reindexDocument(Searchable $model) {}
}