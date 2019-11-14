<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidNameException;

final class Tag extends Unit
{

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var ExternalDocs|null
     */
    private $externalDocs;

    /**
     * @param string      $name
     * @param string|null $description
     *
     * @throws InvalidNameException
     */
    public function __construct(string $name, ?string $description = null)
    {
        $this->setName($name);
        $this->setDescription($description);
    }

    /**
     * @param string $name
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setName(string $name): self
    {
        $name = trim($name);
        if (!$name) {
            throw new InvalidNameException();
        }
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(?string $description): self
    {
        if ($description) {
            $description = trim($description);
        }
        if (!$description) {
            $description = $this->name;
        }
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setExternalDocs(?ExternalDocs $externalDocs): self
    {
        $this->externalDocs = $externalDocs;
        return $this;
    }

    public function getExternalDocs(): ?ExternalDocs
    {
        return $this->externalDocs;
    }

    public function toArray(): array
    {
        $result = [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        ];
        if ($this->getExternalDocs()) {
            $result['externalDocs'] = $this->getExternalDocs()->toArray();
        }
        return $result;
    }
}
