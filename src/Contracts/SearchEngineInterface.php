<?php

namespace KodiComponents\Searcher\Contracts;

interface SearchEngineInterface
{
    /**
     * @param SearchConfiguratorInterface $config
     *
     * @return void
     */
    public function setConfigurator(SearchConfiguratorInterface $config);

    /**
     * @return SearchConfiguratorInterface
     */
    public function getConfigurator();

    /**
     * @param string $query
     *
     * @return SearchResultsInterface
     */
    public function search($query = "");
}