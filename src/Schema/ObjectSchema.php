<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;

/**
 * @method ObjectSchema setFormat(?string $format)
 * @method ObjectSchema setNullable(bool $nullable = true)
 * @method ObjectSchema setDescription(?string $description)
 */
final class ObjectSchema extends AbstractSchemaSimple
{

    /**
     * @var int|null
     */
    private $minProperties;

    /**
     * @var int|null
     */
    private $maxProperties;

    /**
     * @var AbstractSchema|null
     */
    private $additionalProperties;

    /**
     * @var ObjectSchemaProperty[]
     */
    private $properties = [];

    /**
     * @var string[]
     */
    private $required = [];

    public function __construct()
    {
        parent::__construct(parent::TYPE_OBJECT, null);
    }

    /**
     * @param int|null $minProperties
     *
     * @return $this
     *
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function setMinProperties(?int $minProperties): self
    {
        if (!is_null($minProperties) && $minProperties < 0) {
            throw new MinimalLimitShouldBeBiggerException('minProperties', 0);
        }
        $this->minProperties = $minProperties;
        return $this;
    }

    public function getMinProperties(): ?int
    {
        return $this->minProperties;
    }

    /**
     * @param int|null $maxProperties
     *
     * @return $this
     *
     * @throws MaximalLimitShouldBeBiggerException
     */
    public function setMaxProperties(?int $maxProperties): self
    {
        $minProperties = $this->getMinProperties();
        if (!is_null($maxProperties)) {
            if (!is_null($minProperties) && $maxProperties < $minProperties) {
                throw new MaximalLimitShouldBeBiggerException('maxProperties', $minProperties);
            }
            if ($maxProperties < 0) {
                throw new MaximalLimitShouldBeBiggerException('maxProperties', 0);
            }
        }
        $this->maxProperties = $maxProperties;
        return $this;
    }

    public function getMaxProperties(): ?int
    {
        return $this->maxProperties;
    }

    public function setProperty(AbstractSchema $schema, string $name, ?string $access = null, bool $required = false)
    {
        if ($required) {
            $this->required[$name] = $name;
        }
        $this->properties[$name] = new ObjectSchemaProperty($schema, $access);
    }

    public function removeProperty(string $name): self
    {
        if (!array_key_exists($name, $this->properties)) {
            return $this;
        }
        unset($this->properties[$name]);
        if (array_key_exists($name, $this->required)) {
            unset($this->required[$name]);
        }
        return $this;
    }

    public function setAdditionalProperties(?AbstractSchema $additionalProperties): self
    {
        $this->additionalProperties = $additionalProperties;
        return $this;
    }

    /**
     * @return ObjectSchemaProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string[]
     */
    public function getRequired(): array
    {
        return array_values($this->required);
    }

    public function getAdditionalProperties(): ?AbstractSchema
    {
        return $this->additionalProperties;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        foreach ($this->getProperties() as $key => $property) {
            $result['properties'][$key] = $property->toArray();
        }
        if ($this->getRequired()) {
            $result['required'] = $this->getRequired();
        }
        if ($this->getMinProperties() > 0) {
            $result['minProperties'] = $this->getMinProperties();
        }
        if ($this->getMaxProperties() > 0) {
            $result['maxProperties'] = $this->getMaxProperties();
        }
        if ($this->getAdditionalProperties()) {
            if ($this->getAdditionalProperties() instanceof AnySchema) {
                $result['additionalProperties'] = true;
            } else {
                $result['additionalProperties'] = $this->getAdditionalProperties()->toArray();
            }
        }
        return $result;
    }

    protected function getAllowedFormats(): array
    {
        return [];
    }
}
