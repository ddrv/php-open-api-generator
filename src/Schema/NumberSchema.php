<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;

/**
 * @method NumberSchema setFormat(?string $format)
 * @method NumberSchema setNullable(bool $nullable = true)
 * @method NumberSchema setDescription(?string $description)
 */
final class NumberSchema extends AbstractSchemaNumeric
{

    public const FORMAT_FLOAT  = 'float';
    public const FORMAT_DOUBLE = 'double';

    protected $int = false;

    public function setMinimum(?float $minimum, bool $exclusive = false): self
    {
        $this->setMinimumValue($minimum, $exclusive);
        return $this;
    }

    public function getMinimum(): ?float
    {
        return $this->getMinimumValue();
    }

    public function getMaximum(): ?float
    {
        return $this->getMaximumValue();
    }

    /**
     * @param float|null $maximum
     * @param bool       $exclusive
     *
     * @return $this
     *
     * @throws MaximalLimitShouldBeBiggerException
     */
    public function setMaximum(?float $maximum, bool $exclusive = false): self
    {
        $this->setMaximumValue($maximum, $exclusive);
        return $this;
    }

    public function setMultipleOf(?float $multipleOf): self
    {
        $this->setMultipleOfValue($multipleOf);
        return $this;
    }

    public function getMultipleOf(): ?float
    {
        return $this->getMultipleOfValue();
    }

    protected function getAllowedFormats(): array
    {
        return [
            self::FORMAT_FLOAT,
            self::FORMAT_DOUBLE,
        ];
    }
}
