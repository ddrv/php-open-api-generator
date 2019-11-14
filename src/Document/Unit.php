<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use JsonSerializable;

abstract class Unit implements JsonSerializable
{

    final public function getHash(): string
    {
        return md5(json_encode($this));
    }

    abstract public function toArray(): array;

    public function jsonSerialize()
    {
        return (object)$this->toArray();
    }
}
