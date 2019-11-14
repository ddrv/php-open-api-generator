<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class InvalidUrlException extends Exception
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('url is invalid', 127, $previous);
    }
}
