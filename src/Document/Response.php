<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;

/**
 * @method Response setContent(string $contentType, AbstractSchema $schema)
 * @method Response removeContent(string $contentType)
 * @method Response setDescription(?string $description)
 */
final class Response extends AbstractHttpMessage
{

    /**
     * @var Header[]
     */
    private $headers = [];

    /**
     * @var string|null
     */
    private $ref;

    /**
     * @return Header[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @param Header $header
     *
     * @return $this
     *
     * @throws InvalidNameException
     */
    public function addHeader(string $name, Header $header): self
    {
        $name = trim($name);
        if (!$name) {
            throw new InvalidNameException();
        }
        $this->headers[$name] = $header;
        return $this;
    }

    public function removeHeader(string $name): self
    {
        if (array_key_exists($name, $this->headers)) {
            unset($this->headers[$name]);
        }
        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        $result = parent::toArray();
        foreach ($this->getHeaders() as $name => $header) {
            $result['headers'][$name] = $header->toArray();
        }
        return $result;
    }
}
