<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidSchemeException;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpBearerSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpJwtSecurityScheme;
use Tests\Ddrv\OpenApiGenerator\TestCase\HttpSecuritySchemeTestCase;

class HttpJwtSecuritySchemeTest extends HttpSecuritySchemeTestCase
{
    public function getScheme(): string
    {
        return 'bearer';
    }

    public function getBearerFormat(): ?string
    {
        return 'JWT';
    }

    /**
     * @return AbstractSecurityScheme
     * @throws InvalidSchemeException
     */
    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new HttpJwtSecurityScheme();
    }
}
