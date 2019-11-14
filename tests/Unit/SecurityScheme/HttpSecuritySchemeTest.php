<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidSchemeException;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpSecurityScheme;
use ErrorException;
use Tests\Ddrv\OpenApiGenerator\TestCase\HttpSecuritySchemeTestCase;

/**
 * @method HttpSecurityScheme getHttpSecurityScheme()
 */
class HttpSecuritySchemeTest extends HttpSecuritySchemeTestCase
{

    /**
     * @dataProvider provideSetScheme
     *
     * @param string $scheme
     * @param string|null $exception
     *
     * @throws InvalidSchemeException
     * @throws ErrorException
     */
    public function testSetScheme(string $scheme, ?string $exception)
    {
        $http = $this->getHttpSecurityScheme();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $http->setScheme($scheme);
        $scheme = trim($scheme);
        $this->assertSame($scheme, $http->getScheme());
        $array = $http->toArray();
        $this->assertArrayHasKey('scheme', $array);
        $this->assertSame($scheme, $array['scheme']);
    }

    /**
     * @dataProvider provideSetBearerFormat
     *
     * @param string $bearerFormat
     *
     * @throws ErrorException
     */
    public function testSetBearerFormat(string $bearerFormat)
    {
        $http = $this->getHttpSecurityScheme();
        $http->setBearerFormat($bearerFormat);
        $bearerFormat = trim((string)$bearerFormat);
        if (!$bearerFormat) {
            $bearerFormat = null;
        }
        $this->assertSame($bearerFormat, $http->getBearerFormat());
        $array = $http->toArray();
        if (is_null($bearerFormat)) {
            $this->assertArrayNotHasKey('bearerFormat', $array);
        } else {
            $this->assertArrayHasKey('bearerFormat', $array);
            $this->assertSame($bearerFormat, $array['bearerFormat']);
        }
    }

    public function provideSetScheme(): array
    {
        return [
            ['',      InvalidSchemeException::class],
            ['  ',    InvalidSchemeException::class],
            ['ok',    null],
            [' ok  ', null],

        ];
    }

    public function provideSetBearerFormat(): array
    {
        return [
            [''],
            ['  '],
            ['JWT'],
            [' ok  '],

        ];
    }

    public function getScheme(): string
    {
        return 'bearer';
    }

    public function getBearerFormat(): ?string
    {
        return null;
    }

    /**
     * @return HttpSecurityScheme
     * @throws InvalidSchemeException
     */
    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new HttpSecurityScheme($this->getScheme(), $this->getBearerFormat());
    }
}
