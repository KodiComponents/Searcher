<?php

namespace KodiComponents\Searcher;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use KodiComponents\Searcher\Contracts\Indexable;
use KodiComponents\Searcher\Contracts\Searchable;
use KodiComponents\Searcher\Contracts\SearchConfiguratorInterface;
use KodiComponents\Searcher\Contracts\SearchEngineInterface;

class Searcher
{

    /**
     * @var Collection
     */
    protected $models;

    /**
     * @var SearchManager
     */
    protected $manager;

    /**
     * Searcher constructor.
     *
     * @param SearchManager $manager
     *
     */
    public function __construct(SearchManager $manager)
    {
        $this->models = new Collection();
        $this->manager   = $manager;
    }

    /**
     * @return SearchManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param SearchConfiguratorInterface $config
     *
     * @return $this
     */
    public function register(SearchConfiguratorInterface $config = null)
    {
        /** @var SearchEngineInterface $engine */
        $this->models->put(
            get_class($config->getModel()),
            $engine = $this->manager->driver($config->engine())
        );

        $engine->setConfigurator($config);

        return $this;
    }
    /**
     * @return SearchEngineInterface[]
     */
    public function getRegistered()
    {
        return $this->models->toArray();
    }

    /**
     * @param string|Model $model
     * @param string       $query
     * @param int|null     $perPage
     * @param string       $pageName
     *
     * @return LengthAwarePaginator|Collection
     * @throws \Exception
     */
    public function searchIn($model, $query = "", $perPage = null, $pageName = 'page')
    {
        if ($model instanceof Model) {
            $model = get_class($model);
        }

        if (! $this->models->offsetExists($model)) {
            throw new \Exception("Search engine for model [{$model}] not found");
        }

        return $this->_search([$this->models->get($model)], $query = "", $perPage = null, $pageName = 'page');
    }

    /**
     * @param string $query
     * @param int|null $perPage
     * @param string $pageName
     *
     * @return Collection|LengthAwarePaginator
     */
    public function search($query = "", $perPage = null, $pageName = 'page')
    {
        return $this->_search($this->getRegistered(), $query = "", $perPage = null, $pageName = 'page');
    }

    /**
     * @param SearchEngineInterface[]  $engines
     * @param string $query
     * @param null   $perPage
     * @param string $pageName
     *
     * @return LengthAwarePaginator|Collection
     */
    protected function _search(array $engines, $query = "", $perPage = null, $pageName = 'page')
    {
        $results = new Collection();

        foreach ($engines as $engine) {
            $search = $engine->search($query);

            foreach ($search->getResult() as $item) {
                $results->push($item);
            }
        }

        if (is_null($perPage)) {
            return $results;
        }

        $page = Paginator::resolveCurrentPage($pageName);

        return new LengthAwarePaginator($results, $results->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @return $this
     */
    public function createIndex()
    {
        $this->getIndexable()->each(function(Indexable $engine) {
            if (! $engine->indexExists()) {
                $engine->createIndex();
            }
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function deleteIndex()
    {
        $this->getIndexable()->each(function(Indexable $engine) {
            if ($engine->indexExists()) {
                $engine->deleteIndex();
            }
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function reindex()
    {
        $this->getIndexable()->each(function(Indexable $engine) {
            if ($engine->indexExists()) {
                $engine->deleteIndex();
            }

            $engine->createIndex();
        });

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIndexable()
    {
        return $this->models->filter(function($engine) {
            return ($engine instanceOf Indexable);
        });
    }
}