<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class RemovingLastOperationException extends Exception
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('removing last operation', 127, $previous);
    }
}
