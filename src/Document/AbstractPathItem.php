<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastOperationException;

abstract class AbstractPathItem extends Unit
{

    public const METHOD_GET     = 'get';
    public const METHOD_PUT     = 'put';
    public const METHOD_POST    = 'post';
    public const METHOD_DELETE  = 'delete';
    public const METHOD_OPTIONS = 'options';
    public const METHOD_HEAD    = 'head';
    public const METHOD_PATCH   = 'patch';
    public const METHOD_TRACE   = 'trace';

    private const METHODS = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
        self::METHOD_OPTIONS,
        self::METHOD_HEAD,
        self::METHOD_PATCH,
        self::METHOD_TRACE,
    ];

    /**
     * @var string|null
     */
    private $summary;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var Operation[]
     */
    protected $operations = [];

    /**
     * @var Server[]
     */
    private $servers = [];

    /**
     * @var Parameter[]
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     * @param string $method
     * @param Operation $operation
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidUrlException
     */
    public function __construct(string $path, string $method, Operation $operation)
    {
        $this
            ->setPath($path)
            ->setOperation($method, $operation)
        ;
    }

    /**
     * @param string $path
     *
     * @return $this
     *
     * @throws InvalidUrlException
     */
    public function setPath(string $path): self
    {
        $path = trim($path);
        if (!$path) {
            throw new InvalidUrlException();
        }
        $this->path = $path;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string    $method
     * @param Operation $operation
     *
     * @return $this
     *
     * @throws ArgumentOutOfListException
     */
    public function setOperation(string $method, Operation $operation): self
    {
        $method = mb_strtolower(trim($method));
        if (!in_array($method, self::METHODS)) {
            throw new ArgumentOutOfListException('method', self::METHODS);
        }
        $this->operations[$method] = $operation;
        return $this;
    }

    public function getOperation(string $method): ?Operation
    {
        $method = mb_strtolower(trim($method));
        if (!array_key_exists($method, $this->operations)) {
            return null;
        }
        return $this->operations[$method];
    }

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

    public function addServer(Server $server): self
    {
        $this->servers[$server->getUrl()] = $server;

        return $this;
    }

    public function removeServer(Server $server): self
    {
        $key = $server->getUrl();
        if (array_key_exists($key, $this->servers)) {
            unset($this->servers[$key]);
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

    public function addParameter(Parameter $parameter): self
    {
        $key = $this->getParameterUnique($parameter);
        foreach ($this->parameters as $number => $item) {
            if ($this->getParameterUnique($item) === $key) {
                $this->parameters[$number] = $parameter;
                return $this;
            }
        }
        $this->parameters[] = $parameter;
        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        $key = $this->getParameterUnique($parameter);
        foreach ($this->parameters as $number => $item) {
            if ($this->getParameterUnique($item) === $key) {
                unset($this->parameters[$number]);
                return $this;
            }
        }
        return $this;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function toArray(): array
    {
        $result = [];
        if ($this->getSummary()) {
            $result['summary'] = $this->getSummary();
        }
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        foreach (self::METHODS as $method) {
            $operation = $this->getOperation($method);
            if ($operation) {
                $result[$method] = $operation->toArray();
            }
        }
        foreach ($this->getServers() as $server) {
            $result['servers'][] = $server->toArray();
        }
        foreach ($this->getParameters() as $parameter) {
            $result['parameters'][] = $parameter->toArray();
        }
        return [
            $this->getPath() => $result,
        ];
    }

    private function getParameterUnique(Parameter $parameter): string
    {
        return $parameter->getIn() . ':' . $parameter->getName();
    }
}
