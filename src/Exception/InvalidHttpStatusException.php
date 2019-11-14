<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class InvalidHttpStatusException extends Exception
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('http status is invalid', 127, $previous);
    }
}
