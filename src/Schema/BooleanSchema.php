<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

/**
 * @method BooleanSchema setFormat(?string $format)
 * @method BooleanSchema setNullable(bool $nullable = true)
 * @method BooleanSchema setDescription(?string $description)
 */
final class BooleanSchema extends AbstractSchemaSimple
{

    public function __construct()
    {
        parent::__construct(parent::TYPE_BOOLEAN, null);
    }

    protected function getAllowedFormats(): array
    {
        return [];
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray(false);
    }
}
