<?php

namespace KodiComponents\Searcher;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use KodiComponents\Searcher\Contracts\Searchable;
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
     * @param Searchable $model
     *
     * @return $this
     */
    public function register(Searchable $model)
    {
        /** @var SearchEngineInterface $engine */
        $this->models->put(get_class($model), $engine = $this->manager->driver($this->getModelEngine($model)));

        $engine->setModel($model);

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
     * @param string $query
     * @param int|null $perPage
     * @param string $pageName
     *
     * @return Collection|LengthAwarePaginator
     */
    public function search($query = "", $perPage = null, $pageName = 'page')
    {
        $results = new Collection();

        foreach ($this->getRegistered() as $engine) {
            $search = $engine->search($query);

            foreach ($search->getResult() as $item) {
                $results->push($item);
            }
        }

        $result = $results->sortBy(function (Searchable $item) {
            return $item->getDocumentScore();
        });

        if (is_null($perPage)) {
            return $result;
        }

        $page = Paginator::resolveCurrentPage($pageName);

        return new LengthAwarePaginator($result, $result->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @param Searchable $model
     *
     * @return string|null
     */
    protected function getModelEngine(Searchable $model)
    {
        return property_exists($model, 'searchEngine') ? $model->searchEngine : null;
    }
}