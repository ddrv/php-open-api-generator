<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;

final class CallbackRequest extends AbstractPathItem
{

    /**
     * @var string|null
     */
    private $ref;

    public function getRef(): ?string
    {
        return $this->ref;
    }

    /**
     * @param string    $method
     * @param Operation $operation
     *
     * @return $this
     *
     * @throws ArgumentOutOfListException
     */
    public function setOperation(string $method, Operation $operation): AbstractPathItem
    {
        $this->operations = [];
        return parent::setOperation($method, $operation);
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray();
    }
}
