<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\ApiKeySecurityScheme;
use Tests\Ddrv\OpenApiGenerator\TestCase\SecuritySchemeTestCase;

class ApiKeySecuritySchemeTest extends SecuritySchemeTestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $name
     * @param string      $in
     * @param string|null $exception
     *
     * @throws InvalidNameException
     * @throws ArgumentOutOfListException
     */
    public function testConstruct(string $in, string $name, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $securityScheme = new ApiKeySecurityScheme($in, $name);
        $this->assertSame($name, $securityScheme->getName());
        $this->assertSame($in, $securityScheme->getIn());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('in', $array);
        $this->assertSame($name, $array['name']);
        $this->assertSame($in, $array['in']);
    }

    /**
     * @dataProvider provideSetIn
     *
     * @param string      $in
     * @param string|null $exception
     *
     * @throws InvalidNameException
     * @throws ArgumentOutOfListException
     */
    public function testSetIn(string $in, ?string $exception)
    {
        $securityScheme = $this->getSecurityScheme();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $securityScheme->setIn($in);
        $this->assertSame($in, $securityScheme->getIn());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('in', $array);
        $this->assertSame($in, $array['in']);
    }

    /**
     * @dataProvider provideSetName
     *
     * @param string      $name
     * @param string|null $exception
     *
     * @throws InvalidNameException
     * @throws ArgumentOutOfListException
     */
    public function testSetName(string $name, ?string $exception)
    {
        $securityScheme = $this->getSecurityScheme();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $securityScheme->setName($name);
        $name = trim($name);
        $this->assertSame($name, $securityScheme->getName());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame($name, $array['name']);
    }

    public function provideConstruct(): array
    {
        return [
            [ApiKeySecurityScheme::IN_HEADER, 'Header-Name', null],
            [ApiKeySecurityScheme::IN_QUERY,  'query_param', null],
            [ApiKeySecurityScheme::IN_COOKIE, 'cookie_name', null],
            ['undefined-in-parameter-value!', 'cookie_name', ArgumentOutOfListException::class],
            [ApiKeySecurityScheme::IN_HEADER, '',            InvalidNameException::class],
            [ApiKeySecurityScheme::IN_HEADER, '           ', InvalidNameException::class],
        ];
    }

    public function provideSetName(): array
    {
        return [
            ['it_ok', null],
            [' ok  ', null],
            ['     ', InvalidNameException::class],
            ['',      InvalidNameException::class],
        ];
    }

    public function provideSetIn(): array
    {
        return [
            [ApiKeySecurityScheme::IN_HEADER, null],
            [ApiKeySecurityScheme::IN_QUERY,  null],
            [ApiKeySecurityScheme::IN_COOKIE, null],
            ['undefined-in-parameter-value!', ArgumentOutOfListException::class],
        ];
    }

    public function getType(): string
    {
        return 'apiKey';
    }

    /**
     * @return ApiKeySecurityScheme
     *
     * @throws InvalidNameException
     * @throws ArgumentOutOfListException
     */
    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new ApiKeySecurityScheme(ApiKeySecurityScheme::IN_HEADER, 'X-Auth');
    }
}
