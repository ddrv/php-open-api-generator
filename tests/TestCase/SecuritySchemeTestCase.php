<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use PHPUnit\Framework\TestCase;

abstract class SecuritySchemeTestCase extends TestCase
{

    public function testType()
    {
        $securityScheme = $this->getSecurityScheme();
        $this->assertSame($this->getType(), $securityScheme->getType());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('type', $array);
        $this->assertSame($this->getType(), $array['type']);
    }

    /**
     * @dataProvider provideSetDescription
     *
     * @param string|null $description
     */
    public function testSetDescription(?string $description)
    {
        $securityScheme = $this->getSecurityScheme();
        $securityScheme->setDescription($description);
        if (is_null($description)) {
            $check = $description;
        } else {
            $check = trim($description);
        }
        if (!$check) {
            $check = null;
        }
        $this->assertSame($check, $securityScheme->getDescription());
        $array = $securityScheme->toArray();
        if (is_null($check)) {
            $this->assertArrayNotHasKey('description', $array);
        } else {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($check, $array['description']);
        }
    }

    public function provideSetDescription(): array
    {
        return [
            [null],
            ['  '],
            ['ok'],
            ['ok'],
            [' ok '],
            [''],
        ];
    }

    abstract public function getSecurityScheme(): AbstractSecurityScheme;

    abstract public function getType(): string;
}
