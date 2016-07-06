<?php

namespace KodiComponents\Searcher\Engines;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use KodiComponents\Searcher\Contracts\SearchConfiguratorInterface;
use KodiComponents\Searcher\Contracts\SearchEngineInterface;

abstract class Engine implements SearchEngineInterface
{
    /**
     * @var SearchConfiguratorInterface
     */
    protected $configurator;

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->configurator->getModel();
    }

    /**
     * @return SearchConfiguratorInterface
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }

    /**
     * @param SearchConfiguratorInterface $config
     *
     * @return void
     */
    public function setConfigurator(SearchConfiguratorInterface $config)
    {
        $this->configurator = $config;
    }

    /**
     * @param array|Model[] $documents
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
     * @param array|Model[] $documents
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
     * @param array|Model[] $documents
     *
     * @return void
     */
    public function reindexAll($documents)
    {
        $this->deleteAllFromIndex($documents);
        $this->addAllToIndex($documents);
    }
}