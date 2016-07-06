<?php

namespace KodiComponents\Searcher\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ElasticSearchConfiguratorInterface extends SearchConfiguratorInterface
{
    /**
     * @param Model $model
     *
     * @return array
     */
    public function getParams(Model $model);

    /**
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function getSearchParams($query, array $params = []);

    /**
     * Get Mapping Properties.
     *
     * @return array
     */
    public function getMappingProperties();

    /**
     * Set Mapping Properties.
     *
     * @param array $mapping
     *
     * @internal param array $mappingProperties
     */
    public function setMappingProperties(array $mapping);

    /**
     * @return int
     */
    public function getNumberOfShards();

    /**
     * @return int
     */
    public function getNumberOfReplicas();

    /**
     * @param Model $model
     *
     * @return array
     */
    public function getDocumentData(Model $model);
}