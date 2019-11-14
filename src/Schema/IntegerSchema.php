<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Schema;

use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;

/**
 * @method IntegerSchema setFormat(?string $format)
 * @method IntegerSchema setNullable(bool $nullable = true)
 * @method IntegerSchema setDescription(?string $description)
 */
final class IntegerSchema extends AbstractSchemaNumeric
{

    public const FORMAT_INT_32 = 'int32';
    public const FORMAT_INT_64 = 'int64';

    protected $int = true;

    public function setMinimum(?int $minimum, bool $exclusive = false): self
    {
        if (!is_null($minimum)) {
            $minimum = (float)$minimum;
        }
        $this->setMinimumValue($minimum, $exclusive);
        return $this;
    }

    public function getMinimum(): ?int
    {
        $result = $this->getMinimumValue();
        if (!is_null($result)) {
            $result = (int)$result;
        }
        return $result;
    }

    public function getMaximum(): ?int
    {

        $result = $this->getMaximumValue();
        if (!is_null($result)) {
            $result = (int)$result;
        }
        return $result;
    }

    /**
     * @param int|null $maximum
     * @param bool     $exclusive
     *
     * @return $this
     *
     * @throws MaximalLimitShouldBeBiggerException
     */
    public function setMaximum(?int $maximum, bool $exclusive = false): self
    {

        if (!is_null($maximum)) {
            $maximum = (float)$maximum;
        }
        $this->setMaximumValue($maximum, $exclusive);
        return $this;
    }

    public function setMultipleOf(?int $multipleOf): self
    {
        if (!is_null($multipleOf)) {
            $multipleOf = (float)$multipleOf;
        }
        $this->setMultipleOfValue($multipleOf);
        return $this;
    }

    public function getMultipleOf(): ?int
    {
        $result = $this->getMultipleOfValue();
        if (!is_null($result)) {
            $result = (int)$result;
        }
        return $result;
    }

    protected function getAllowedFormats(): array
    {
        return [
            self::FORMAT_INT_32,
            self::FORMAT_INT_64,
        ];
    }
}
