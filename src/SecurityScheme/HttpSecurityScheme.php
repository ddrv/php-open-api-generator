<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidSchemeException;

final class HttpSecurityScheme extends AbstractHttpSecurityScheme
{

    /**
     * @param string $scheme
     *
     * @return $this
     *
     * @throws InvalidSchemeException
     */
    public function setScheme(string $scheme): self
    {
        $this->__construct($scheme, $this->getBearerFormat());
        return $this;
    }

    public function setBearerFormat(?string $bearerFormat): self
    {

        if (!is_null($bearerFormat)) {
            $bearerFormat = trim($bearerFormat);
        }
        if (!$bearerFormat) {
            $bearerFormat = null;
        }
        $this->bearerFormat = $bearerFormat;
        return $this;
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray();
    }
}
