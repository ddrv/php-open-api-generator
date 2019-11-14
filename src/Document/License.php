<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidNameException;

final class License extends Unit
{

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @param string      $name
     * @param string|null $url
     *
     * @throws InvalidNameException
     */
    public function __construct(string $name, ?string $url = null)
    {
        $this
            ->setName($name)
            ->setUrl($url)
        ;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setUrl(?string $url): self
    {
        if ($url) {
            $url = trim($url);
        }
        if (!$url) {
            $url = null;
        }
        $this->url = $url;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function toArray(): array
    {
        $result = [
            'name' => $this->getName(),
        ];
        if ($this->getUrl()) {
            $result['url'] = $this->getUrl();
        }
        return $result;
    }
}
