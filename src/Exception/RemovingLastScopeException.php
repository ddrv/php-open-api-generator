<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class RemovingLastScopeException extends Exception
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct('removing last scope', 127, $previous);
    }
}
