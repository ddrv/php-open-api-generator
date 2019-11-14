<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class MaximalLimitShouldBeBiggerException extends Exception
{

    public function __construct(string $limit, float $value, $code = 0, Throwable $previous = null)
    {
        parent::__construct('limit ' . $limit . ' should be bigger of ' . $value, $code, $previous);
    }
}
