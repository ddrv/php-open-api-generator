<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\SecurityScheme;

final class HttpBasicSecurityScheme extends AbstractHttpSecurityScheme
{

    public function __construct()
    {
        parent::__construct('basic', null);
    }

    public function toArray(bool $autoRef = true): array
    {
        if ($this->getRef() && $autoRef) {
            return ['$ref' => $this->getRef()];
        }
        return parent::toArray();
    }
}
