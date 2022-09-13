<?php
declare(strict_types=1);

namespace Spatie\ElasticsearchQueryBuilder\Exceptions;

class UnexpectedResponseType extends \Exception
{
    public function __construct(string $responseType)
    {
        parent::__construct(
            sprintf(
                'Unexpected response type `%s` returned. Are you using the correct version?',
                $responseType)
        );
    }
}
