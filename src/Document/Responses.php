<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidHttpStatusException;

final class Responses extends Unit
{

    /**
     * @var Response[]
     */
    private $responses = [];

    public function __construct(Response $defaultResponse)
    {
        $this->setDefaultResponse($defaultResponse);
    }

    public function setDefaultResponse(Response $response): self
    {
        $this->responses['default'] = $response;
        return $this;
    }

    public function getDefaultResponse(): Response
    {
        return $this->responses['default'];
    }

    /**
     * @param int      $httpStatus
     * @param Response $response
     *
     * @return $this
     *
     * @throws InvalidHttpStatusException
     */
    public function setResponse(int $httpStatus, Response $response): self
    {
        if ($httpStatus < 100 || $httpStatus > 599) {
            throw new InvalidHttpStatusException();
        }
        $key = (string)$httpStatus;
        $this->responses[$key] = $response;
        return $this;
    }

    public function getResponse(int $httpStatus): ?Response
    {
        $key = (string)$httpStatus;
        if (!array_key_exists($key, $this->responses)) {
            return null;
        }
        return $this->responses[$key];
    }

    /**
     * @return Response[]
     */
    public function getResponses(): array
    {
        $result = $this->responses;
        unset($result['default']);
        return $result;
    }

    public function removeResponse(int $httpStatus): self
    {
        $key = (string)$httpStatus;
        if (array_key_exists($key, $this->responses)) {
            unset($this->responses[$key]);
        }
        return $this;
    }

    public function toArray(): array
    {
        ksort($this->responses);
        $result = [];
        foreach ($this->responses as $key => $response) {
            $result[$key] = $response->toArray();
        }
        return $result;
    }
}
