<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;

/**
 * @method ArraySchema setFormat(?string $format)
 * @method ArraySchema setNullable(bool $nullable = true)
 * @method ArraySchema setDescription(?string $description)
 */
final class ArraySchema extends AbstractSchemaSimple
{

    private const DEFAULT_UNIQUE_ITEMS = false;

    /**
     * @var AbstractSchema
     */
    private $items;

    /**
     * @var int|null
     */
    private $minItems;

    /**
     * @var int|null
     */
    private $maxItems;

    /**
     * @var bool
     */
    private $uniqueItems;

    /**
     * @param AbstractSchema $items
     *
     * @throws ArgumentOutOfListException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function __construct(AbstractSchema $items)
    {
        parent::__construct(parent::TYPE_ARRAY, null);
        $this
            ->setItems($items)
            ->setUniqueItems(false)
            ->setMinItems(0)
            ->setMaxItems(null)
        ;
    }

    public function setItems(AbstractSchema $schema): self
    {
        $this->items = $schema;
        return $this;
    }

    /**
     * @param int|null $minItems
     *
     * @return $this
     *
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function setMinItems(?int $minItems): self
    {
        if (!is_null($minItems) && $minItems < 0) {
            throw new MinimalLimitShouldBeBiggerException('minItems', 0);
        }
        $this->minItems = $minItems;
        return $this;
    }

    public function getMinItems(): ?int
    {
        return $this->minItems;
    }

    /**
     * @param int|null $maxItems
     *
     * @return $this
     *
     * @throws MaximalLimitShouldBeBiggerException
     */
    public function setMaxItems(?int $maxItems): self
    {
        $minItems = $this->getMinItems();
        if (!is_null($maxItems)) {
            if (!is_null($minItems) && $maxItems < $minItems) {
                throw new MaximalLimitShouldBeBiggerException('maxItems', $minItems);
            }
            if ($maxItems < 0) {
                throw new MaximalLimitShouldBeBiggerException('maxItems', 0);
            }
        }
        $this->maxItems = $maxItems;
        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

    public function setUniqueItems(bool $uniqueItems = true): self
    {
        $this->uniqueItems = $uniqueItems;
        return $this;
    }

    public function isUniqueItems(): bool
    {
        return $this->uniqueItems;
    }

    public function getItems(): ?AbstractSchema
    {
        return $this->items;
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
        $result = parent::toArray();
        $result['items'] = $this->getItems()->toArray();

        if ($this->getMinItems() > 0) {
            $result['minItems'] = $this->getMinItems();
        }
        if ($this->getMaxItems() > 0) {
            $result['maxItems'] = $this->getMaxItems();
        }
        if (!is_null($this->isUniqueItems()) && $this->isUniqueItems() !== self::DEFAULT_UNIQUE_ITEMS) {
            $result['uniqueItems'] = $this->isUniqueItems();
        }
        return $result;
    }
}
