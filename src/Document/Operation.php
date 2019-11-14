<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

final class Operation extends Unit
{

    /**
     * @var string[]
     */
    private $tags = [];

    /**
     * @var string|null
     */
    private $summary;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var ExternalDocs|null
     */
    private $externalDocs;

    /**
     * @var string|null
     */
    private $operationId;

    /**
     * @var Parameter[]
     */
    private $parameters = [];

    /**
     * @var RequestBody|null
     */
    private $requestBody;

    /**
     * Responses
     */
    private $responses;

    /**
     * @var PathItem[]
     */
    private $callbacks = [];

    /**
     * @var bool
     */
    private $deprecated = false;

    /**
     * @var Security[]
     */
    private $security = [];

    /**
     * @var Server[]
     */
    private $servers = [];

    public function __construct(Responses $responses, ?RequestBody $requestBody)
    {
        $this
            ->setResponses($responses)
            ->setRequestBody($requestBody)
        ;
    }

    public function setResponses(Responses $responses): self
    {
        $this->responses = $responses;
        return $this;
    }

    public function getResponses(): Responses
    {
        return $this->responses;
    }

    public function setRequestBody(?RequestBody $requestBody): self
    {
        $this->requestBody = $requestBody;
        return $this;
    }

    public function getRequestBody(): ?RequestBody
    {
        return $this->requestBody;
    }

    public function setOperationId(?string $operationId): self
    {
        if ($operationId) {
            $operationId = trim($operationId);
        }
        if (!$operationId) {
            $operationId = null;
        }
        $this->operationId = $operationId;
        return $this;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
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

    public function setDeprecated(bool $deprecated = true): self
    {
        $this->deprecated = $deprecated;
        return $this;
    }

    public function isDeprecated(): bool
    {
        return $this->deprecated;
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

    public function addTag(string $tag): self
    {
        $tag = mb_strtolower(trim($tag));
        if (!$tag) {
            return $this;
        }
        $this->tags[$tag] = $tag;
        return $this;
    }

    public function removeTag(string $tag): self
    {
        $key = mb_strtolower(trim($tag));
        if (array_key_exists($tag, $this->tags)) {
            unset($this->tags[$key]);
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return array_values($this->tags);
    }

    public function setCallback(string $name, CallbackRequest $callback): self
    {
        $this->callbacks[$name] = $callback;
        return $this;
    }

    public function removeCallback(string $name): self
    {
        if (array_key_exists($name, $this->callbacks)) {
            unset($this->callbacks[$name]);
        }
        return $this;
    }

    /**
     * @return CallbackRequest[]
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
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

    public function removeSecurity(Security $security): self
    {
        foreach ($this->security as $key => $item) {
            if ($item->getName() === $security->getName()) {
                unset($this->security[$key]);
                return $this;
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

    public function toArray(): array
    {
        $result = [];
        foreach ($this->getTags() as $tag) {
            $result['tags'][] = $tag;
        }
        if ($this->getSummary()) {
            $result['summary'] = $this->getSummary();
        }
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        if ($this->getExternalDocs()) {
            $result['externalDocs'] = $this->getExternalDocs()->toArray();
        }
        if ($this->getOperationId()) {
            $result['operationId'] = $this->getOperationId();
        }
        foreach ($this->getParameters() as $parameter) {
            $result['parameters'][] = $parameter->toArray();
        }
        if ($this->getRequestBody()) {
            $result['requestBody'] = $this->getRequestBody()->toArray();
        }
        $result['responses'] = $this->getResponses()->toArray();
        foreach ($this->getCallbacks() as $key => $callback) {
            $result['callbacks'][$key] = $callback->toArray();
        }
        if ($this->isDeprecated()) {
            $result['deprecated'] = $this->isDeprecated();
        }
        foreach ($this->getSecurity() as $security) {
            $result['security'][$security->getName()] = $security->getScopes();
        }
        foreach ($this->getServers() as $server) {
            $result['servers'][] = $server->toArray();
        }
        return $result;
    }

    private function getParameterUnique(Parameter $parameter): string
    {
        return $parameter->getIn() . ':' . $parameter->getName();
    }
}
