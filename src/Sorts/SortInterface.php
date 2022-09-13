<?php
declare(strict_types=1);

namespace Spatie\ElasticsearchQueryBuilder\Sorts;

interface SortInterface
{
    public function toArray(): array;
}
