<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class InvalidTitleException extends Exception
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('title is invalid', 127, $previous);
    }
}
