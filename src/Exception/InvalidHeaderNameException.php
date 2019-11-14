<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class InvalidHeaderNameException extends Exception
{

    public function __construct(array $disallowed, Throwable $previous = null)
    {
        $last = array_pop($disallowed);
        parent::__construct('header name can not be ' . implode(', ', $disallowed) . ' and ' . $last, 127, $previous);
    }
}
