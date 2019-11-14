<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;

abstract class AbstractSchemaNumeric extends AbstractSchemaSimple
{

    protected $int = false;

    protected const DEFAULT_EXCLUSIVE = false;

    /**
     * @var float|null
     */
    protected $minimum;

    /**
     * @var bool
     */
    protected $exclusiveMinimum = self::DEFAULT_EXCLUSIVE;

    /**
     * @var float|null
     */
    protected $maximum;

    /**
     * @var bool
     */
    protected $exclusiveMaximum = self::DEFAULT_EXCLUSIVE;

    /**
     * @var float|null
     */
    protected $multipleOf;

    public function __construct(?string $format = null)
    {
        $type = $this->int ? parent::TYPE_INTEGER : parent::TYPE_NUMBER;
        parent::__construct($type, $format);
    }

    public function isExclusiveMinimum(): bool
    {
        return $this->exclusiveMinimum;
    }

    public function isExclusiveMaximum(): bool
    {
        return $this->exclusiveMaximum;
    }

    protected function setMinimumValue(?float $minimum, bool $exclusive = false): self
    {
        $this->minimum = $minimum;
        if (is_null($minimum)) {
            $exclusive = false;
        }
        $this->exclusiveMinimum = $exclusive;
        return $this;
    }

    protected function getMinimumValue(): ?float
    {
        return $this->minimum;
    }

    protected function getMaximumValue(): ?float
    {
        return $this->maximum;
    }

    /**
     * @param float|null $maximum
     * @param bool $exclusive
     * @return $this
     * @throws MaximalLimitShouldBeBiggerException
     */
    protected function setMaximumValue(?float $maximum, bool $exclusive = false): self
    {
        $minimum = $this->getMinimumValue();
        if (!is_null($minimum) && !is_null($maximum) && $maximum < $minimum) {
            throw new MaximalLimitShouldBeBiggerException('maximum', $minimum);
        }
        $this->maximum = $maximum;
        if (is_null($maximum)) {
            $exclusive = false;
        }
        $this->exclusiveMaximum = $exclusive;
        return $this;
    }

    protected function setMultipleOfValue(?float $multipleOf): self
    {
        $this->multipleOf = $multipleOf;
        return $this;
    }

    protected function getMultipleOfValue(): ?float
    {
        return $this->multipleOf;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        $minimum = $this->getMinimumValue();
        $maximum = $this->getMaximumValue();
        if (!is_null($minimum)) {
            $result['minimum'] = $this->int ? (int)$minimum : $minimum;
        }
        if (!is_null($maximum)) {
            $result['maximum'] = $this->int ? (int)$maximum : $maximum;
        }
        if ($this->isExclusiveMinimum() !== self::DEFAULT_EXCLUSIVE) {
            $result['exclusiveMinimum'] = $this->isExclusiveMinimum();
        }
        if ($this->isExclusiveMaximum() !== self::DEFAULT_EXCLUSIVE) {
            $result['exclusiveMaximum'] = $this->isExclusiveMaximum();
        }
        $multipleOf = $this->getMultipleOfValue();
        if (!is_null($multipleOf)) {
            $result['multipleOf'] = $this->int ? (int)$multipleOf : $multipleOf;
        }
        return $result;
    }
}
