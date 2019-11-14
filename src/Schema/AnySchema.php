<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

/**
 * @method AnySchema setFormat(?string $format)
 * @method AnySchema setNullable(bool $nullable = true)
 * @method AnySchema setDescription(?string $description)
 */
final class AnySchema extends AbstractSchemaSimple
{

    public function __construct()
    {
        parent::__construct(parent::TYPE_ANY, null);
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        unset($result['type']);
        $result['nullable'] = $this->isNullable();
        return $result;
    }

    protected function getAllowedFormats(): array
    {
        return [];
    }
}
