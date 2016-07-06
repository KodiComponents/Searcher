<?php

namespace KodiComponents\Searcher;

use Illuminate\Support\ServiceProvider;

class SearcherServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['search.engine'] = $this->app->share(function ($app) {
            return new SearchManager($app);
        });

        $this->app->singleton('searcher', function ($app) {
            return new Searcher($app['search.engine']);
        });

        $this->app->alias('searcher', Searcher::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['search.engine', 'searcher'];
    }
}