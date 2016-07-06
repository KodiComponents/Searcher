<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchEngineInterface;

abstract class Engine implements SearchEngineInterface
{

    /**
     * @var Searchable
     */
    protected $model;

    /**
     * @return Searchable
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Searchable $model
     */
    public function setModel(Searchable $model)
    {
        $this->model = $model;
    }

    /**
     * @param array|Model|Searchable[] $documents
     *
     * @return void
     */
    public function addAllToIndex($documents)
    {
        if ($documents instanceof Builder) {
            $documents = $documents->get();
        }

        foreach ($documents as $document) {
            $this->addDocumentToIndex($document);
        }
    }

    /**
     * @param array|Model|Searchable[] $documents
     *
     * @return void
     */
    public function deleteAllFromIndex($documents)
    {
        if ($documents instanceof Builder) {
            $documents = $documents->get();
        }

        foreach ($documents as $document) {
            $this->deleteDocumentFromIndex($document);
        }
    }

    /**
     * @param array|Model|Searchable[] $documents
     *
     * @return void
     */
    public function reindexAll($documents)
    {
        $this->deleteAllFromIndex($documents);
        $this->addAllToIndex($documents);
    }
}