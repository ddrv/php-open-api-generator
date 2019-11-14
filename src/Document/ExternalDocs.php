<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;

final class ExternalDocs extends Unit
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @param string      $url
     * @param string|null $description
     *
     * @throws InvalidUrlException
     */
    public function __construct(string $url, ?string $description = null)
    {
        $this
            ->setUrl($url)
            ->setDescription($description)
        ;
    }

    /**
     * @param string $url
     *
     * @return $this
     *
     * @throws InvalidUrlException
     */
    public function setUrl(string $url): self
    {
        $url = trim($url);
        if (!$url) {
            throw new InvalidUrlException();
        }
        $this->url = $url;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
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

    public function toArray(): array
    {
        $result = [
            'url' => $this->getUrl(),
        ];
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        return $result;
    }
}
