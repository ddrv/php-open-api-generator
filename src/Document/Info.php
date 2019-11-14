<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidTitleException;
use Ddrv\OpenApiGenerator\Exception\InvalidVersionException;

final class Info extends Unit
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $termsOfService;

    /**
     * @var Contact|null
     */
    private $contact;

    /**
     * @var License|null
     */
    private $license;

    /**
     * @var string
     */
    private $version;

    /**
     * @param string $title
     * @param string $version
     *
     * @throws InvalidTitleException
     * @throws InvalidVersionException
     */
    public function __construct(string $title, string $version)
    {
        $this
            ->setTitle($title)
            ->setVersion($version)
        ;
    }

    /**
     * @param string $title
     *
     * @return $this
     *
     * @throws InvalidTitleException
     */
    public function setTitle(string $title): self
    {
        $title = trim($title);
        if (!$title) {
            throw new InvalidTitleException();
        }
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $version
     *
     * @return $this
     *
     * @throws InvalidVersionException
     */
    public function setVersion(string $version): self
    {
        $version = trim($version);
        if (!$version) {
            throw new InvalidVersionException();
        }
        $this->version = $version;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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

    public function getTermsOfService(): ?string
    {
        return $this->termsOfService;
    }

    public function setTermsOfService(?string $termsOfService): self
    {
        if ($termsOfService) {
            $termsOfService = trim($termsOfService);
        }
        if (!$termsOfService) {
            $termsOfService = null;
        }
        $this->termsOfService = $termsOfService;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(?License $license): self
    {
        $this->license = $license;
        return $this;
    }

    public function toArray(): array
    {
        $result = [
            'title' => $this->getTitle(),
        ];
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        if ($this->getTermsOfService()) {
            $result['termsOfService'] = $this->getTermsOfService();
        }
        if ($this->getContact()) {
            $result['contact'] = $this->getContact()->toArray();
        }
        if ($this->getLicense()) {
            $result['license'] = $this->getLicense()->toArray();
        }
        $result['version'] = $this->getVersion();
        return $result;
    }
}
