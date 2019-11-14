<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use InvalidArgumentException;

abstract class AbstractSchemaMultiple extends AbstractSchema
{

    public const TYPE_ONE_OF = 'oneOf';
    public const TYPE_ALL_OF = 'allOf';
    public const TYPE_ANY_OF = 'anyOf';

    private const TYPES = [
        self::TYPE_ONE_OF,
        self::TYPE_ALL_OF,
        self::TYPE_ANY_OF,
    ];

    private $schemas;

    private $type;

    public function __construct(string $type, AbstractSchema ...$schemas)
    {
        $types = self::TYPES;
        if (!in_array($type, $types)) {
            $last = array_pop($types);
            throw new InvalidArgumentException(
                'argument type can be ' . implode(', ', $types) . ' or ' . $last,
                1
            );
        }
        foreach ($schemas as $schema) {
            $this->schemas[] = $schema;
        }
        $this->type = $type;
        $this->schemas = $schemas;
    }

    /**
     * @return AbstractSchema[]
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    public function addSchema(AbstractSchema $schema)
    {
        foreach ($this->schemas as $item) {
            if ($item->getHash() === $schema->getHash()) {
                return $this;
            }
        }
        $this->schemas[] = $schema;
        return $this;
    }

    public function removeSchema(AbstractSchema $schema)
    {
        foreach ($this->schemas as $key => $item) {
            if ($item->getHash() === $schema->getHash()) {
                unset($this->schemas[$key]);
            }
        }
        return $this;
    }

    final public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = [
            $this->type => [],
        ];
        foreach ($this->schemas as $schema) {
            $result[$this->type][] = $schema->toArray();
        }
        return $result;
    }
}
