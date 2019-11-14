<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\Document;

use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;

abstract class AbstractHttpMessage extends Unit
{

    /**
     * @var AbstractSchema[]
     */
    private $content = [];

    /**
     * @var string|null
     */
    private $description;

    /**
     * @param string         $contentType
     * @param AbstractSchema $schema
     * @param string|null    $description
     *
     * @throws InvalidContentTypeException
     */
    public function __construct(string $contentType, AbstractSchema $schema, ?string $description = null)
    {
        $this
            ->setContent($contentType, $schema)
            ->setDescription($description)
        ;
    }

    /**
     * @param string         $contentType
     * @param AbstractSchema $schema
     *
     * @return $this
     *
     * @throws InvalidContentTypeException
     */
    public function setContent(string $contentType, AbstractSchema $schema): self
    {
        $contentType = trim($contentType);
        if (!$contentType) {
            throw new InvalidContentTypeException();
        }
        $this->content[$contentType] = $schema;
        return $this;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     *
     * @throws RemovingLastScopeException
     */
    public function removeContent(string $contentType): self
    {
        if (count($contentType) == 1) {
            throw new RemovingLastScopeException();
        }
        if (array_key_exists($contentType, $this->content)) {
            unset($this->content[$contentType]);
        }
        return $this;
    }

    public function getContent(string $contentType): ?AbstractSchema
    {
        if (!array_key_exists($contentType, $this->content)) {
            return null;
        }
        return $this->content[$contentType];
    }

    /**
     * @return AbstractSchema[]
     */
    public function getContents(): array
    {
        return $this->content;
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

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->content as $type => $schema) {
            $result['content'][$type]['schema'] = $schema->toArray();
        }
        if ($this->getDescription()) {
            $result['description'] = $this->getDescription();
        }
        return $result;
    }
}
