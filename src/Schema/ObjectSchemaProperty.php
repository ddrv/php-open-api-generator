<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use InvalidArgumentException;

final class ObjectSchemaProperty
{

    public const ACCESS_READ_ONLY  = 'readOnly';
    public const ACCESS_WRITE_ONLY = 'writeOnly';

    private const ACCESSES = [
        self::ACCESS_READ_ONLY,
        self::ACCESS_WRITE_ONLY,
    ];

    /**
     * @var AbstractSchema
     */
    private $schema;

    /**
     * @var string|null
     */
    protected $access = null;

    public function __construct(AbstractSchema $schema, ?string $access = null)
    {
        $this
            ->setSchema($schema)
            ->setAccess($access)
        ;
    }

    public function setSchema(AbstractSchema $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    public function getSchema(): AbstractSchema
    {
        return $this->schema;
    }

    public function setAccess(?string $access): self
    {
        if (!is_null($access) && !in_array($access, self::ACCESSES)) {
            throw new InvalidArgumentException(
                'argument access can be ' . implode(', ', self::ACCESSES) . ' or null',
                3
            );
        }
        $this->access = $access;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccess(): ?string
    {
        return $this->access;
    }

    public function toArray(): array
    {
        $result = $this->schema->toArray();
        if ($this->access) {
            $result[$this->access] = true;
        }
        return $result;
    }
}
