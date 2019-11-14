<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidSchemeException;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpBearerSecurityScheme;
use Tests\Ddrv\OpenApiGenerator\TestCase\HttpSecuritySchemeTestCase;

class HttpBearerSecuritySchemeTest extends HttpSecuritySchemeTestCase
{
    public function getScheme(): string
    {
        return 'bearer';
    }

    public function getBearerFormat(): ?string
    {
        return 'Token';
    }

    /**
     * @return AbstractSecurityScheme
     * @throws InvalidSchemeException
     */
    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new HttpBearerSecurityScheme($this->getBearerFormat());
    }
}
