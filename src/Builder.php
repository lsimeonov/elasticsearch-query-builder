<?php

namespace Spatie\ElasticsearchQueryBuilder;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Spatie\ElasticsearchQueryBuilder\Aggregations\Aggregation;
use Spatie\ElasticsearchQueryBuilder\Exceptions\UnexpectedResponseType;
use Spatie\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\Query;
use Spatie\ElasticsearchQueryBuilder\Sorts\Sort;

class Builder
{
    protected ?BoolQuery $query = null;

    protected ?AggregationCollection $aggregations = null;

    protected ?SortCollection $sorts = null;

    protected ?string $searchIndex = null;

    protected ?int $size = null;

    protected ?int $from = null;

    protected ?array $searchAfter = null;

    protected ?array $fields = null;

    protected bool $withAggregations = true;

    public function __construct(protected Client $client)
    {
    }

    public function addQuery(Query $query, string $boolType = 'must'): static
    {
        if (!$this->query) {
            $this->query = new BoolQuery();
        }

        $this->query->add($query, $boolType);

        return $this;
    }

    public function addAggregation(Aggregation $aggregation): static
    {
        if (!$this->aggregations) {
            $this->aggregations = new AggregationCollection();
        }

        $this->aggregations->add($aggregation);

        return $this;
    }

    public function addSort(Sort $sort): static
    {
        if (!$this->sorts) {
            $this->sorts = new SortCollection();
        }

        $this->sorts->add($sort);

        return $this;
    }


    /**
     * @throws \Spatie\ElasticsearchQueryBuilder\Exceptions\UnexpectedResponseType
     * @throws \Elastic\Elasticsearch\Exception\ClientResponseException
     * @throws \Elastic\Elasticsearch\Exception\ServerResponseException
     */
    public function search(): array
    {
        $payload = $this->getPayload();

        $params = [
            'body' => $payload,
        ];

        if ($this->searchIndex) {
            $params['index'] = $this->searchIndex;
        }

        if ($this->size !== null) {
            $params['size'] = $this->size;
        }

        if ($this->from !== null) {
            $params['from'] = $this->from;
        }

        $response = $this->client->search($params);

        return $this->processResponse($response)->asArray();
    }

    public function count(): array
    {
        $payload = $this->getPayload();

        $params = [
            'body' => $payload,
        ];

        if ($this->searchIndex) {
            $params['index'] = $this->searchIndex;
        }

        $response = $this->client->count($params);

        return $this->processResponse($response)->asArray();
    }

    public function index(string $searchIndex): static
    {
        $this->searchIndex = $searchIndex;

        return $this;
    }

    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function from(int $from): static
    {
        $this->from = $from;

        return $this;
    }

    public function searchAfter(?array $searchAfter): static
    {
        $this->searchAfter = $searchAfter;

        return $this;
    }

    public function fields(array $fields): static
    {
        $this->fields = array_merge($this->fields ?? [], $fields);

        return $this;
    }

    public function withoutAggregations(): static
    {
        $this->withAggregations = false;

        return $this;
    }

    public function getPayload(): array
    {
        $payload = [];

        if ($this->query) {
            $payload['query'] = $this->query->toArray();
        }

        if ($this->withAggregations && $this->aggregations) {
            $payload['aggs'] = $this->aggregations->toArray();
        }

        if ($this->sorts) {
            $payload['sort'] = $this->sorts->toArray();
        }

        if ($this->fields) {
            $payload['_source'] = $this->fields;
        }

        if ($this->searchAfter) {
            $payload['search_after'] = $this->searchAfter;
        }

        return $payload;
    }

    /**
     * @param \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise $response
     * @return \Elastic\Elasticsearch\Response\Elasticsearch
     * @throws \Spatie\ElasticsearchQueryBuilder\Exceptions\UnexpectedResponseType
     */
    private function processResponse(Elasticsearch|Promise $response): Elasticsearch
    {
        if ($response instanceof Promise) {
            $response = $response->wait();
        }

        if (!$response instanceof Elasticsearch) {
            throw new UnexpectedResponseType(get_class($response));
        }
        return $response;
    }
}
