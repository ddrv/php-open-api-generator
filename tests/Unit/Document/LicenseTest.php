<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\License;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use PHPUnit\Framework\TestCase;

class LicenseTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $name
     * @param string|null $url
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testConstruct(string $name, ?string $url, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $license = new License($name, $url);
        $count = 2;
        $name = trim($name);
        $urlCheck = trim((string)$url);
        if (!$urlCheck) {
            $urlCheck = null;
            $count--;
        }
        $this->assertSame($urlCheck, $license->getUrl());
        $this->assertSame($name, $license->getName());
        $array = $license->toArray();
        $this->assertCount($count, $array);
        $this->assertArrayHasKey('name', $array);
        if ($urlCheck) {
            $this->assertArrayHasKey('url', $array);
            $this->assertSame($urlCheck, $array['url']);
        } else {
            $this->assertArrayNotHasKey('url', $array);
        }
    }

    /**
     * @dataProvider provideSetName
     *
     * @param string      $name
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testSetName(string $name, ?string $exception)
    {
        $license = new License('Proprietary');
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $license->setName($name);
        $array = $license->toArray();
        $name = trim($name);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame($name, $array['name']);
        $this->assertSame($name, $license->getName());
    }

    /**
     * @dataProvider provideSetUrl
     *
     * @param string|null $url
     *
     * @throws InvalidNameException
     */
    public function testSetUrl(?string $url)
    {
        $license = new License('Proprietary', 'https://example.com/licanse');
        $license->setUrl($url);
        $array = $license->toArray();
        $check = trim((string)$url);
        if (!$check) {
            $check = null;
        }
        if ($check) {
            $this->assertArrayHasKey('url', $array);
            $this->assertSame($check, $array['url']);
            $this->assertSame($check, $license->getUrl());
        } else {
            $this->assertArrayNotHasKey('url', $array);
        }
    }

    public function provideConstruct(): array
    {
        return [
            ['MIT',   'https://opensource.org/licenses/mit-license.php',   null],
            ['MIT',   '                                               ',   null],
            ['MIT',   null,                                                null],
            [' MIT ', null,                                                null],
            ['',      null,                                                InvalidNameException::class],
        ];
    }

    public function provideSetUrl(): array
    {
        return [
            ['http://site.com/license'],
            [' http://site.com/license '],
            [''],
            [null],
        ];
    }

    public function provideSetName(): array
    {
        return [
            ['MIT',   null],
            [' MIT ', null],
            ['     ', InvalidNameException::class],
            ['',      InvalidNameException::class],
        ];
    }
}
