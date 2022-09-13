<?php

namespace Spatie\ElasticsearchQueryBuilder;

use Spatie\ElasticsearchQueryBuilder\Sorts\SortInterface;

class SortCollection
{
    protected array $sorts;

    public function __construct(SortInterface ...$sorts)
    {
        $this->sorts = $sorts;
    }

    public function add(SortInterface $sort): self
    {
        $this->sorts[] = $sort;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->sorts);
    }

    public function toArray(): array
    {
        $sorts = [];

        foreach ($this->sorts as $sort) {
            $sorts[] = $sort->toArray();
        }

        return $sorts;
    }
}
