<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpBasicSecurityScheme;
use Tests\Ddrv\OpenApiGenerator\TestCase\HttpSecuritySchemeTestCase;

class HttpBasicSecuritySchemeTest extends HttpSecuritySchemeTestCase
{
    public function getScheme(): string
    {
        return 'basic';
    }

    public function getBearerFormat(): ?string
    {
        return null;
    }

    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new HttpBasicSecurityScheme();
    }
}
