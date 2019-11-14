<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

abstract class AbstractExample extends Unit
{

    /**
     * @var string|null
     */
    private $summary;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $ref;

    /**
     * @param string|null $summary
     *
     * @return $this
     */
    public function setSummary(?string $summary): self
    {
        if ($summary) {
            $summary = trim($summary);
        }
        if (!$summary) {
            $summary = null;
        }
        $this->summary = $summary;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setDescription(?string $description): self
    {
        if ($description) {
            $description = trim($description);
        }
        if (!$description) {
            $description = null;
        }
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function toArray(bool $autoRef = true): array
    {
        $result = [];
        if ($this->getSummary()) {
            $result['summary'] = $this->getSummary();
        }
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        return $result;
    }
}
