<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class OneOfArgumentIsRequiredException extends Exception
{

    public function __construct(array $arguments, Throwable $previous = null)
    {
        $last = array_pop($arguments);
        parent::__construct(
            'one of arguments [' . implode(', ', $arguments) . ' or ' . $last . '] is required',
            127,
            $previous
        );
    }
}
