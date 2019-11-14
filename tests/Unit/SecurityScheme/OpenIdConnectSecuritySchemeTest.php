<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidOpenIdConnectUrlException;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\OpenIdConnectSecurityScheme;
use Tests\Ddrv\OpenApiGenerator\TestCase\SecuritySchemeTestCase;

class OpenIdConnectSecuritySchemeTest extends SecuritySchemeTestCase
{

    /**
     * @dataProvider provideSetOpenIdConnectUrl
     *
     * @param string|null $url
     * @param string|null $exception
     *
     * @throws InvalidOpenIdConnectUrlException
     */
    public function testSetOpenIdConnectUrl(?string $url, ?string $exception)
    {
        $securityScheme = $this->getSecurityScheme();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $securityScheme->setOpenIdConnectUrl($url);
        $this->assertSame($url, $securityScheme->getOpenIdConnectUrl());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('openIdConnectUrl', $array);
        $this->assertSame($url, $array['openIdConnectUrl']);
    }

    public function provideSetOpenIdConnectUrl(): array
    {
        return [
            ['',                    InvalidOpenIdConnectUrlException::class],
            ['                   ', InvalidOpenIdConnectUrlException::class],
            ['http://connect.com/', null],
        ];
    }

    public function getType(): string
    {
        return 'openIdConnect';
    }

    /**
     * @return OpenIdConnectSecurityScheme
     *
     * @throws InvalidOpenIdConnectUrlException
     */
    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new OpenIdConnectSecurityScheme('http://open-id.example.com/connect');
    }
}
