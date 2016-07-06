<?php

namespace KodiComponents\Searcher\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Elasticquent
{
    /**
     * @var string
     */
    public $searchEngine = 'elastic_search';

    /**
     * Document Score.
     *
     * Hit score when using data
     * from Elasticsearch results.
     *
     * @var null|int
     */
    protected $documentScore = null;

    /**
     * Document Version.
     *
     * Elasticsearch document version.
     *
     * @var null|int
     */
    protected $documentVersion = null;

    /**
     * Get Mapping Properties.
     *
     * @return array
     */
    public function getMappingProperties()
    {
        return $this->mappingProperties;
    }

    /**
     * Set Mapping Properties.
     *
     * @param array $mapping
     *
     * @internal param array $mappingProperties
     */
    public function setMappingProperties(array $mapping)
    {
        $this->mappingProperties = $mapping;
    }

    /**
     * Get Index Name.
     *
     * @return string
     */
    public function getIndexName()
    {
        return config('searcher.elastic_search.default_index', 'default');
    }

    /**
     * @return int|null
     */
    public function getDocumentScore()
    {
        return $this->documentScore;
    }

    /**
     * @param int $score
     *
     * @return $this
     */
    public function setDocumentScore($score)
    {
        $this->documentScore = (int) $score;

        return $this;
    }

    /**
     * Document Version.
     *
     * @return null|int
     */
    public function documentVersion()
    {
        return $this->documentVersion;
    }

    /**
     * Get Basic Elasticsearch Params.
     *
     * @return array
     */
    public function getSearchParams()
    {
        $params = [
            'index' => $this->getIndexName(),
            'type' => $this->getTable(),
            'id' => $this->getKey(),
        ];

        if (property_exists($this, 'searchSize')) {
            $params['body']['size'] = (int) $this->searchSize;
        }

        if (property_exists($this, 'searchOffset')) {
            $params['body']['from'] = (int) $this->searchOffset;
        }

        return $params;
    }

    /**
     * @return array
     */
    public function getSearchDocumentData()
    {
        return $this->toArray();
    }

    /**
     * @param array $ids
     *
     * @return Builder
     */
    public function getQueryForFoundDocuments(array $ids)
    {
        return static::newQuery()->whereIn($this->getKeyName(), $ids);
    }
}