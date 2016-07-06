<?php

namespace KodiComponents\Searcher\Contracts;

interface SearchEngineInterface
{
    /**
     * @param Searchable $model
     *
     * @return void
     */
    public function setModel(Searchable $model);

    /**
     * @param string $query
     *
     * @return SearchResultsInterface
     */
    public function search($query = "");

    /**
     * Create Index.
     *
     * @return void
     */
    public function createIndex();

    /**
     * @return void
     */
    public function deleteIndex();

    /**
     * Index Exists.
     *
     * Does this index exist?
     *
     * @return bool
     */
    public function indexExists();

    /**
     * @param Searchable $model
     */
    public function addDocumentToIndex(Searchable $model);

    /**
     * @param Searchable $model
     */
    public function deleteDocumentFromIndex(Searchable $model);

    /**
     * @param Searchable $model
     */
    public function reindexDocument(Searchable $model);

    /**
     * @param array|Model|Searchable[] $documents
     *
     * @return void
     */
    public function addAllToIndex($documents);

    /**
     * @param array|Model|Searchable[] $documents
     *
     * @return void
     */
    public function deleteAllFromIndex($documents);

    /**
     * @param array|Model|Searchable[] $documents
     *
     * @return void
     */
    public function reindexAll($documents);
}