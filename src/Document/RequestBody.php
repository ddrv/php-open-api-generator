<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Schema\AbstractSchema;

/**
 * @method RequestBody setContent(string $contentType, AbstractSchema $schema)
 * @method RequestBody removeContent(string $contentType)
 * @method RequestBody setDescription(?string $description)
 */
final class RequestBody extends AbstractHttpMessage
{

    /**
     * @var bool
     */
    private $required = false;

    /**
     * @var string|null
     */
    private $ref;

    public function setRequired(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        if ($this->isRequired()) {
            $result['required'] = $this->isRequired();
        }
        return $result;
    }
}
