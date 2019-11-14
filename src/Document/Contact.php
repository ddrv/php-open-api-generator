<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\OneOfArgumentIsRequiredException;

final class Contact extends Unit
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
     * @var string|null
     */
    private $email;

    /**
     * @var bool
     */
    private $initiated = false;

    /**
     * @param string|null $name
     * @param string|null $url
     * @param string|null $email
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function __construct(?string $name, ?string $url, ?string $email)
    {
        $this
            ->setName($name)
            ->setUrl($url)
            ->setEmail($email)
        ;
        $this->check();
        $this->initiated = true;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function setName(?string $name): self
    {
        if ($name) {
            $name = trim($name);
        }
        if (!$name) {
            $name = null;
        }
        $this->name = $name;
        if ($this->initiated) {
            $this->check();
        }
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $url
     *
     * @return $this
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function setUrl(?string $url): self
    {
        if ($url) {
            $url = trim($url);
        }
        if (!$url) {
            $url = null;
        }
        $this->url = $url;
        if ($this->initiated) {
            $this->check();
        }
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     *
     * @throws OneOfArgumentIsRequiredException
     */
    public function setEmail(?string $email): self
    {
        if ($email) {
            $email = trim($email);
        }
        if (!$email) {
            $email = null;
        }
        $this->email = $email;
        if ($this->initiated) {
            $this->check();
        }
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function toArray(): array
    {
        $result = [];
        if ($this->getName()) {
            $result['name'] = $this->getName();
        }
        if ($this->getUrl()) {
            $result['url'] = $this->getUrl();
        }
        if ($this->getEmail()) {
            $result['email'] = $this->getEmail();
        }
        return $result;
    }

    /**
     * @return bool
     *
     * @throws OneOfArgumentIsRequiredException
     */
    private function check(): bool
    {
        if (!$this->getName() && !$this->getUrl() && !$this->getEmail()) {
            throw new OneOfArgumentIsRequiredException(['name', 'url', 'email']);
        }
        return true;
    }
}
