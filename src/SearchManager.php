<?php

namespace KodiComponents\Searcher;

use Illuminate\Support\Manager;
use KodiComponents\Searcher\Contracts\SearchEngineInterface;
use KodiComponents\Searcher\Engines\Algolia;
use KodiComponents\Searcher\Engines\ElasticSearch;
use KodiComponents\Searcher\Engines\MysqlLike;

class SearchManager extends Manager
{
    /**
     * @return Elasticsearch
     */
    public function createElasticSearchDriver()
    {
        $config = $this->app['config']['searcher']['elastic_search'];

        return new ElasticSearch(\Elasticsearch\ClientBuilder::create()->build(), $config);
    }

    /**
     * @return Algolia
     */
    public function createAlgoliaDriver()
    {
        $config = $this->app['config']['searcher']['algolia'];

        return new Algolia($this->app['algolia.factory'], $config);
    }

    /**
     * @return MySQLLike
     */
    public function createMysqlLikeDriver()
    {
        $config = $this->app['config']['searcher']['mysql_like'];

        return new MysqlLike($this->app['db.connection'], $config);
    }

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return SearchEngineInterface
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        $this->drivers[] = $this->createDriver($driver);

        return $this->drivers[] = $this->createDriver($driver);
    }


    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('searcher.default_engines', 'mysql_like');
    }

    /**
     * Set the default driver name.
     *
     * @param  string $name
     *
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['searcher.default_engine'] = $name;
    }
}