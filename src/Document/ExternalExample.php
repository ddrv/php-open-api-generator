<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidValueException;

final class ExternalExample extends AbstractExample
{

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     *
     * @throws InvalidValueException
     */
    public function __construct(string $value)
    {
        $this->setValue($value);
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @throws InvalidValueException
     */
    public function setValue(string $value): self
    {
        $value = trim($value);
        if (empty($value)) {
            throw new InvalidValueException();
        }
        $this->value = $value;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        $result['externalValue'] = $this->getValue();
        return $result;
    }
}
