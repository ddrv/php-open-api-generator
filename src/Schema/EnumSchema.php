<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

/**
 * @method EnumSchema setFormat(?string $format)
 * @method EnumSchema setNullable(bool $nullable = true)
 * @method EnumSchema setDescription(?string $description)
 */
final class EnumSchema extends AbstractSchemaSimple
{


    /**
     * @var string[]
     */
    private $enum = [];

    public function __construct(string ...$values)
    {
        parent::__construct(parent::TYPE_STRING, null);
        foreach ($values as $value) {
            $this->addValue($value);
        }
    }

    public function addValue(string $value): self
    {
        $this->enum[$value] = $value;
        return $this;
    }

    public function removeValue(string $value): self
    {
        if (array_key_exists($value, $this->enum)) {
            unset($this->enum[$value]);
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getEnum(): array
    {
        return array_values($this->enum);
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        foreach ($this->getEnum() as $value) {
            $result['enum'][] = $value;
        }
        return $result;
    }

    protected function getAllowedFormats(): array
    {
        return [];
    }
}
