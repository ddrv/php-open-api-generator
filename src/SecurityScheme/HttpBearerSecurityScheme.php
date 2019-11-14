<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

final class HttpBearerSecurityScheme extends AbstractHttpSecurityScheme
{

    public function __construct(?string $bearerFormat = null)
    {
        parent::__construct('bearer', $bearerFormat);
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray();
    }
}
