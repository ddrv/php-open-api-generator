<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

final class HttpJwtSecurityScheme extends AbstractHttpSecurityScheme
{

    public function __construct()
    {
        parent::__construct('bearer', 'JWT');
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray();
    }
}
