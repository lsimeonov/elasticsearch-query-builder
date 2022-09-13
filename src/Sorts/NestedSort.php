<?php
declare(strict_types=1);

namespace Spatie\ElasticsearchQueryBuilder\Sorts;

use Spatie\ElasticsearchQueryBuilder\Queries\Query;

class NestedSort implements SortInterface
{
    public static function create(string $field, string $path, string $order = 'desc', ?Query $filter = null): self
    {
        return new self($field, $path, $order, $filter);
    }

    public function __construct(
        protected readonly string $field,
        protected readonly string $path,
        protected readonly string $order,
        protected readonly ?Query $filter
    )
    {
    }

    public function toArray(): array
    {
        return [
            $this->field => [
                'nested' => array_filter([
                                             'path' => $this->path,
                                             'filter' => $this->filter?->toArray(),
                                         ]),
                'order' => $this->order,
            ],
        ];
    }

}
