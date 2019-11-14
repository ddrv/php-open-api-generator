<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Closure;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;

final class Components extends Unit
{

    /**
     * @var AbstractSchema[]
     */
    private $schemas = [];

    /**
     * @var Response[]
     */
    private $responses = [];

    /**
     * @var Parameter[]
     */
    private $parameters = [];

    /**
     * @var AbstractExample[]
     */
    private $examples = [];

    /**
     * @var RequestBody[]
     */
    private $requestBodies = [];

    /**
     * @var Header[]
     */
    private $headers = [];

    /**
     * @var AbstractSecurityScheme[]
     */
    private $securitySchemes = [];

    /**
     * @var CallbackRequest[]
     */
    private $callbacks = [];

    /**
     * @var Closure[]
     */
    private $refSetters = [];

    public function __construct()
    {
        $fn1 = function (AbstractSchema $schema, ?string $value) {
            $property = 'ref';
            $schema->$property = $value;
            return $schema->$property;
        };
        $fn2 = function (Response $response, ?string $value) {
            $property = 'ref';
            $response->$property = $value;
            return $response->$property;
        };
        $fn3 = function (AbstractHeaderOrParameter $parameter, ?string $value) {
            $property = 'ref';
            $parameter->$property = $value;
            return $parameter->$property;
        };
        $fn4 = function (AbstractExample $example, ?string $value) {
            $property = 'ref';
            $example->$property = $value;
            return $example->$property;
        };
        $fn5 = function (RequestBody $requestBody, ?string $value) {
            $property = 'ref';
            $requestBody->$property = $value;
            return $requestBody->$property;
        };
        $fn6 = function (AbstractSecurityScheme $securityScheme, ?string $value) {
            $property = 'ref';
            $securityScheme->$property = $value;
            return $securityScheme->$property;
        };
        $fn7 = function (CallbackRequest $callback, ?string $value) {
            $property = 'ref';
            $callback->$property = $value;
            return $callback->$property;
        };
        $this->refSetters = [
            'schema'      => Closure::bind($fn1, null, AbstractSchema::class),
            'response'    => Closure::bind($fn2, null, Response::class),
            'parameter'   => Closure::bind($fn3, null, AbstractHeaderOrParameter::class),
            'example'     => Closure::bind($fn4, null, AbstractExample::class),
            'requestBody' => Closure::bind($fn5, null, RequestBody::class),
            'security'    => Closure::bind($fn6, null, AbstractSecurityScheme::class),
            'callback'    => Closure::bind($fn7, null, CallbackRequest::class),
        ];
    }

    /**
     * @param string         $name
     * @param AbstractSchema $schema
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setSchema(string $name, AbstractSchema $schema): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeSchema($name);
        $this->refSetters['schema']($schema, '#/components/schemas/' . $name);
        $this->schemas[$name] = $schema;
        return $this;
    }

    public function removeSchema(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->schemas)) {
            $this->refSetters['schema']($this->schemas[$name], null);
            unset($this->schemas[$name]);
        }
        return $this;
    }

    /**
     * @return AbstractSchema[]
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * @param string   $name
     * @param Response $response
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setResponse(string $name, Response $response): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeResponse($name);
        $this->refSetters['response']($response, '#/components/responses/' . $name);
        $this->responses[$name] = $response;
        return $this;
    }

    public function removeResponse(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->responses)) {
            $this->refSetters['response']($this->responses[$name], null);
            unset($this->responses[$name]);
        }
        return $this;
    }

    /**
     * @return Response[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param string    $name
     * @param Parameter $parameter
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setParameter(string $name, Parameter $parameter): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeParameter($name);
        $this->refSetters['parameter']($parameter, '#/components/parameters/' . $name);
        $this->parameters[$name] = $parameter;
        return $this;
    }

    public function removeParameter(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->parameters)) {
            $this->refSetters['parameter']($this->parameters[$name], null);
            unset($this->parameters[$name]);
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

    /**
     * @param string          $name
     * @param AbstractExample $example
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setExample(string $name, AbstractExample $example): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeExample($name);
        $this->refSetters['example']($example, '#/components/examples/' . $name);
        $this->examples[$name] = $example;
        return $this;
    }

    public function removeExample(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->examples)) {
            $this->refSetters['example']($this->examples[$name], null);
            unset($this->examples[$name]);
        }
        return $this;
    }

    /**
     * @return AbstractExample[]
     */
    public function getExamples(): array
    {
        return $this->examples;
    }

    /**
     * @param string      $name
     * @param RequestBody $requestBody
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setRequestBody(string $name, RequestBody $requestBody): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeRequestBody($name);
        $this->refSetters['requestBody']($requestBody, '#/components/requestBodies/' . $name);
        $this->requestBodies[$name] = $requestBody;
        return $this;
    }

    public function removeRequestBody(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->requestBodies)) {
            $this->refSetters['requestBody']($this->requestBodies[$name], null);
            unset($this->requestBodies[$name]);
        }
        return $this;
    }

    /**
     * @return RequestBody[]
     */
    public function getRequestBodies(): array
    {
        return $this->requestBodies;
    }

    /**
     * @param string $name
     * @param Header $header
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setHeader(string $name, Header $header): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeHeader($name);
        $this->refSetters['parameter']($header, '#/components/headers/' . $name);
        $this->headers[$name] = $header;
        return $this;
    }

    public function removeHeader(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->headers)) {
            $this->refSetters['parameter']($this->headers[$name], null);
            unset($this->headers[$name]);
        }
        return $this;
    }

    /**
     * @return Header[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string                 $name
     * @param AbstractSecurityScheme $securityScheme
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setSecurityScheme(string $name, AbstractSecurityScheme $securityScheme): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeSecurityScheme($name);
        $this->refSetters['security']($securityScheme, '#/components/securitySchemes/' . $name);
        $this->securitySchemes[$name] = $securityScheme;
        return $this;
    }

    public function removeSecurityScheme(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->securitySchemes)) {
            $this->refSetters['security']($this->securitySchemes[$name], null);
            unset($this->securitySchemes[$name]);
        }
        return $this;
    }

    /**
     * @return AbstractSecurityScheme[]
     */
    public function getSecuritySchemes(): array
    {
        return $this->securitySchemes;
    }

    /**
     * @param string            $name
     * @param CallbackRequest $callback
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function setCallback(string $name, CallbackRequest $callback): self
    {
        $name = trim($name);
        $this->checkName($name);
        $this->removeCallback($name);
        $this->refSetters['callback']($callback, '#/components/callbacks/' . $name);
        $this->callbacks[$name] = $callback;
        return $this;
    }

    public function removeCallback(string $name): self
    {
        $name = trim($name);
        if (array_key_exists($name, $this->callbacks)) {
            $this->refSetters['callback']($this->callbacks[$name], null);
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

    public function isEmpty(): bool
    {
        if (
            empty($this->getResponses())
            && empty($this->getSchemas())
            && empty($this->getCallbacks())
            && empty($this->getParameters())
            && empty($this->getHeaders())
            && empty($this->getExamples())
            && empty($this->getRequestBodies())
            && empty($this->getSecuritySchemes())
        ) {
            return true;
        }
        return false;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->getSchemas() as $name => $schema) {
            $result['schemas'][$name] = $schema->toArray(false);
        }
        foreach ($this->getResponses() as $name => $response) {
            $result['responses'][$name] = $response->toArray(false);
        }
        foreach ($this->getParameters() as $name => $parameter) {
            $result['parameters'][$name] = $parameter->toArray(false);
        }
        foreach ($this->getExamples() as $name => $example) {
            $result['examples'][$name] = $example->toArray(false);
        }
        foreach ($this->getRequestBodies() as $name => $requestBody) {
            $result['requestBodies'][$name] = $requestBody->toArray(false);
        }
        foreach ($this->getHeaders() as $name => $header) {
            $result['headers'][$name] = $header->toArray(false);
        }
        foreach ($this->getSecuritySchemes() as $name => $securityScheme) {
            $result['securitySchemes'][$name] = $securityScheme->toArray(false);
        }
        foreach ($this->getCallbacks() as $name => $callback) {
            $result['callbacks'][$name] = $callback->toArray(false);
        }
        return $result;
    }

    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws InvalidNameException
     */
    private function checkName(string $name): bool
    {
        if (!preg_match('/^[a-zA-Z0-9.\-_]+$/ui', $name)) {
            throw new InvalidNameException();
        };
        return true;
    }
}
