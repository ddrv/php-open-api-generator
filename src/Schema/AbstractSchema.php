<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Document\Unit;

abstract class AbstractSchema extends Unit
{

    /**
     * @var string|null
     */
    protected $ref;

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function toArray(bool $autoRef = true): array
    {
        return parent::toArray();
    }
}
