<?php

namespace KodiComponents\Searcher\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Indexable
{
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
     * @param Model $model
     */
    public function addDocumentToIndex(Model $model);

    /**
     * @param Model $model
     */
    public function deleteDocumentFromIndex(Model $model);

    /**
     * @param Model $model
     */
    public function reindexDocument(Model $model);

    /**
     * @param array|Model[] $documents
     *
     * @return void
     */
    public function addAllToIndex($documents);

    /**
     * @param array|Model[] $documents
     *
     * @return void
     */
    public function deleteAllFromIndex($documents);

    /**
     * @param array|Model[] $documents
     *
     * @return void
     */
    public function reindexAll($documents);
}