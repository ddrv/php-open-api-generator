<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator;

use Ddrv\OpenApiGenerator\Document\Components;
use Ddrv\OpenApiGenerator\Document\ExternalDocs;
use Ddrv\OpenApiGenerator\Document\Info;
use Ddrv\OpenApiGenerator\Document\PathItem;
use Ddrv\OpenApiGenerator\Document\Security;
use Ddrv\OpenApiGenerator\Document\Server;
use Ddrv\OpenApiGenerator\Document\Tag;
use JsonSerializable;

/**
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md
 */
final class OpenApi implements JsonSerializable
{

    public const OPEN_API_VERSION = '3.0.2';

    /**
     * @var Info
     */
    private $info;

    /**
     * @var Server[]
     */
    private $servers = [];

    /**
     * @var PathItem[]
     */
    private $paths = [];

    /**
     * @var Components
     */
    private $components;

    /**
     * @var Security[]
     */
    private $security = [];

    /**
     * @var Tag[]
     */
    private $tags = [];

    /**
     * @var ExternalDocs|null
     */
    private $externalDocs;

    public function __construct(Info $info)
    {
        $this
            ->setInfo($info)
            ->setComponents(new Components())
        ;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }

    public function setInfo(Info $info): self
    {
        $this->info = $info;
        return $this;
    }

    public function getComponents(): Components
    {
        return $this->components;
    }

    public function setComponents(Components $components): self
    {
        $this->components = $components;
        return $this;
    }

    public function addServer(Server $server): self
    {
        foreach ($this->servers as $key => $item) {
            if ($item->getUrl() === $server->getUrl()) {
                $this->servers[$key] = $server;
                return $this;
            }
        }
        $this->servers[] = $server;
        return $this;
    }

    public function removeServer(string $url): self
    {
        $url = trim($url);
        foreach ($this->servers as $key => $item) {
            if ($item->getUrl() === $url) {
                unset($this->servers[$key]);
            }
        }
        return $this;
    }

    /**
     * @return Server[]
     */
    public function getServers(): array
    {
        return array_values($this->servers);
    }

    public function addSecurity(Security $security): self
    {
        foreach ($this->security as $key => $item) {
            if ($item->getName() === $security->getName()) {
                $this->security[$key] = $security;
                return $this;
            }
        }
        $this->security[] = $security;
        return $this;
    }

    public function removeSecurity(string $name): self
    {
        $name = trim($name);
        foreach ($this->security as $key => $item) {
            if ($item->getName() === $name) {
                unset($this->security[$key]);
            }
        }
        return $this;
    }

    /**
     * @return Security[]
     */
    public function getSecurity(): array
    {
        return array_values($this->security);
    }

    public function addTag(Tag $tag): self
    {
        foreach ($this->tags as $key => $item) {
            if ($item->getName() === $tag->getName()) {
                $this->tags[$key] = $tag;
                return $this;
            }
        }
        $this->tags[] = $tag;
        return $this;
    }

    public function removeTag(string $name): self
    {
        $name = trim($name);
        foreach ($this->tags as $key => $item) {
            if ($item->getName() === $name) {
                unset($this->tags[$key]);
            }
        }
        return $this;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return array_values($this->tags);
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

    /**
     * @param PathItem $pathItem
     *
     * @return $this
     */
    public function addPath(PathItem $pathItem)
    {
        foreach ($this->paths as $key => $path) {
            if ($path->getPath() === $pathItem->getPath()) {
                $this->paths[$key] = $pathItem;
                return $this;
            }
        }
        $this->paths[] = $pathItem;
        return $this;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function removePath(string $path)
    {
        foreach ($this->paths as $key => $pathItem) {
            if ($pathItem->getPath() === $path) {
                unset($this->paths[$key]);
            }
        }
        return $this;
    }

    /**
     * @return PathItem[]
     */
    public function getPaths()
    {
        return array_values($this->paths);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $spec = [
            'openapi' => self::OPEN_API_VERSION,
            'info' => $this->getInfo()->toArray(),
        ];
        foreach ($this->getServers() as $server) {
            $spec['servers'][] = $server->toArray();
        }
        $spec['paths'] = [];
        foreach ($this->getPaths() as $pathItem) {
            $spec['paths'] = array_replace($spec['paths'], $pathItem->toArray());
        }
        if (!$this->getComponents()->isEmpty()) {
            $spec['components'] = $this->getComponents()->toArray();
        }
        $security = [];
        foreach ($this->getSecurity() as $item) {
            $security = array_replace($security, $item->toArray());
        }
        if ($security) {
            $spec['security'] = $security;
        }
        foreach ($this->getTags() as $tag) {
            $spec['tags'][] = $tag->toArray();
        }
        if ($this->getExternalDocs()) {
            $spec['externalDocs'] = $this->getExternalDocs()->toArray();
        }
        return $spec;
    }
}
