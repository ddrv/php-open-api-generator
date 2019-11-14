<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Exception;

use Exception;
use Throwable;

final class ArgumentOutOfListException extends Exception
{

    public function __construct(string $argument, array $values, $code = 0, Throwable $previous = null)
    {
        $message = 'argument ' . $argument;
        if (!$values) {
            $message .= ' not allowed for this schema';
        } else {
            $last = array_pop($values);
            $message .= ' can be ' . implode(', ', $values) . ' or ' . $last;
        }
        parent::__construct($message, $code, $previous);
    }
}
