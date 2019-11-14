<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Schema\AbstractSchema;

abstract class AbstractHeaderOrParameter extends Unit
{

    protected const DEFAULT_REQUIRED          = false;
    protected const DEFAULT_DEPRECATED        = false;
    protected const DEFAULT_ALLOW_EMPTY_VALUE = false;

    protected $required;
    protected $deprecated;
    protected $allowEmptyValue;
    protected $description;

    /**
     * @var AbstractSchema|null
     */
    private $schema;

    /**
     * @var string|null
     */
    protected $ref;

    public function __construct()
    {
        $this
            ->setRequired(self::DEFAULT_REQUIRED)
            ->setAllowEmptyValue(self::DEFAULT_ALLOW_EMPTY_VALUE)
            ->setDeprecated(self::DEFAULT_DEPRECATED)
        ;
    }

    public function setRequired(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setAllowEmptyValue(bool $allowEmptyValue = true): self
    {
        $this->allowEmptyValue = $allowEmptyValue;
        return $this;
    }

    public function isAllowEmptyValue(): bool
    {
        return $this->allowEmptyValue;
    }

    public function setDeprecated(bool $deprecated = true): self
    {
        $this->deprecated = $deprecated;
        return $this;
    }

    public function isDeprecated(): bool
    {
        return $this->deprecated;
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

    public function setSchema(?AbstractSchema $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    public function getSchema(): ?AbstractSchema
    {
        return $this->schema;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function toArray(): array
    {
        $result = [];
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        if ($this->isRequired() !== self::DEFAULT_REQUIRED) {
            $result['required'] = $this->isRequired();
        }
        if ($this->isDeprecated() !== self::DEFAULT_DEPRECATED) {
            $result['deprecated'] = $this->isDeprecated();
        }
        if ($this->isAllowEmptyValue() !== self::DEFAULT_ALLOW_EMPTY_VALUE) {
            $result['allowEmptyValue'] = $this->isAllowEmptyValue();
        }
        if ($this->getSchema()) {
            $result['schema'] = $this->getSchema()->toArray();
        }
        return $result;
    }
}
