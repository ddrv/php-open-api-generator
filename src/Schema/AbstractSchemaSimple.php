<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;

abstract class AbstractSchemaSimple extends AbstractSchema
{

    public const TYPE_STRING  = 'string';
    public const TYPE_NUMBER  = 'number';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_ARRAY   = 'array';
    public const TYPE_OBJECT  = 'object';
    public const TYPE_ANY     = 'any';

    protected const TYPES = [
        self::TYPE_STRING,
        self::TYPE_NUMBER,
        self::TYPE_INTEGER,
        self::TYPE_BOOLEAN,
        self::TYPE_ARRAY,
        self::TYPE_OBJECT,
        self::TYPE_ANY,
    ];

    protected const DEFAULT_NULLABLE = false;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $format;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var bool
     */
    protected $nullable = self::DEFAULT_NULLABLE;

    /**
     * @var string|null
     */
    protected $pattern;

    /**
     * @param string      $type
     * @param string|null $format
     *
     * @throws ArgumentOutOfListException
     */
    public function __construct(string $type, ?string $format)
    {
        $this->setFormat($format);
        $types = self::TYPES;
        if (!in_array($type, $types)) {
            throw new ArgumentOutOfListException('type', $types, 1);
        }
        $this->type = $type;
    }

    public function setNullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * @param string|null $format
     *
     * @return $this
     *
     * @throws ArgumentOutOfListException
     */
    public function setFormat(?string $format): self
    {
        $formats = $this->getAllowedFormats();
        if (is_null($format)) {
            $this->format = null;
            return $this;
        }
        if (!in_array($format, $formats)) {
            throw new ArgumentOutOfListException('format', $formats, 2);
        }
        $this->format = $format;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setDescription(?string $description): self
    {
        if ($description) {
            $description = trim($description);
        }
        if (!$description) {
            $description = null;
        }
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(bool $autoRef = true): array
    {
        $result = [
            'type' => $this->getType(),
        ];
        if ($this->getFormat()) {
            $result['format'] = $this->getFormat();
        }
        if ($this->getPattern()) {
            $result['pattern'] = $this->getPattern();
        }
        if ($this->isNullable() !== self::DEFAULT_NULLABLE) {
            $result['nullable'] = $this->isNullable();
        }
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        return $result;
    }

    abstract protected function getAllowedFormats(): array;
}
