<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\SecurityScheme\AbstractHttpSecurityScheme;
use ErrorException;

abstract class HttpSecuritySchemeTestCase extends SecuritySchemeTestCase
{

    /**
     * @throws ErrorException
     */
    public function testType()
    {
        parent::testType();
        $securityScheme = $this->getHttpSecurityScheme();
        $bearerFormat = $this->getBearerFormat();
        $this->assertSame($this->getScheme(), $securityScheme->getScheme());
        $this->assertSame($bearerFormat, $securityScheme->getBearerFormat());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('scheme', $array);
        $this->assertSame($this->getScheme(), $array['scheme']);
        if (!is_null($bearerFormat)) {
            $this->assertArrayHasKey('bearerFormat', $array);
            $this->assertSame($bearerFormat, $array['bearerFormat']);
        }
    }

    public function getType(): string
    {
        return 'http';
    }

    /**
     * @return AbstractHttpSecurityScheme
     *
     * @throws ErrorException
     */
    public function getHttpSecurityScheme(): AbstractHttpSecurityScheme
    {
        $securityScheme = $this->getSecurityScheme();
        if (!$securityScheme instanceof AbstractHttpSecurityScheme) {
            throw new ErrorException(
                'Method getSecurityScheme() should be return instance of AbstractHttpSecurityScheme'
            );
        }
        return $securityScheme;
    }

    abstract public function getScheme(): string;

    abstract public function getBearerFormat(): ?string;
}
