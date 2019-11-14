<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidValueException;

final class Example extends AbstractExample
{

    /**
     * @var array
     */
    private $value;

    /**
     * @param array $value
     *
     * @throws InvalidValueException
     */
    public function __construct(array $value)
    {
        $this->setValue($value);
    }

    /**
     * @param array $value
     *
     * @return $this
     *
     * @throws InvalidValueException
     */
    public function setValue(array $value): self
    {
        if (empty($value)) {
            throw new InvalidValueException();
        }
        $this->value = $value;
        return $this;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        $result['value'] = $this->getValue();
        return $result;
    }
}
