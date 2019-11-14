<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\ExternalDocs;
use Ddrv\OpenApiGenerator\Document\Tag;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use PHPUnit\Framework\TestCase;

class ExternalDocsTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string $url
     * @param string|null $description
     * @param string|null $exception
     *
     * @throws InvalidUrlException
     */
    public function testConstruct(string $url, ?string $description, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $externalDocs = new ExternalDocs($url, $description);
        $count = 2;
        $url = trim($url);
        $description = trim((string)$description);
        if (!$description) {
            $description = null;
            $count = 1;
        }
        $this->assertSame($url, $externalDocs->getUrl());
        $this->assertSame($description, $externalDocs->getDescription());
        $array = $externalDocs->toArray();
        $this->assertCount($count, $array);
        $this->assertArrayHasKey('url', $array);
        if ($description) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($description, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
        $this->assertSame($url, $array['url']);
    }

    /**
     * @dataProvider provideSetUrl
     *
     * @param string      $url
     * @param string|null $exception
     *
     * @throws InvalidUrlException
     */
    public function testSetUrl(string $url, ?string $exception)
    {
        $externalDocs = new ExternalDocs('http://example.io');
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $externalDocs->setUrl($url);
        $url = trim($url);
        $array = $externalDocs->toArray();
        $this->assertArrayHasKey('url', $array);
        $this->assertSame($url, $array['url']);
        $this->assertSame($url, $externalDocs->getUrl());
    }

    /**
     * @dataProvider provideSetDescription
     *
     * @param string      $description
     * @param string|null $check
     *
     * @throws InvalidUrlException
     */
    public function testSetDescription(?string $description, ?string $check)
    {
        $externalDocs = new ExternalDocs('http://example.io');
        $externalDocs->setDescription($description);
        $array = $externalDocs->toArray();
        if ($check) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($check, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
        $this->assertSame($check, $externalDocs->getDescription());
    }

    public function provideConstruct(): array
    {
        return [
            ['tag', 'Best tag',   null],
            ['tag', null,         null],
            ['tag', ' ',          null],
            ['tag', ' Best tag ', null],
            [' t ', '',           null],
            ['',    '',           InvalidUrlException::class],
            ['   ', '',           InvalidUrlException::class],
        ];
    }

    public function provideSetDescription(): array
    {
        return [
            ['Best doc', 'Best doc', null],
            [null,       null,       null],
            ['',         null,       null],
            [' Best   ', 'Best',     null],
        ];
    }

    public function provideSetUrl(): array
    {
        return [
            ['http://docs.example.io/v1', null],
            [' http://docs.example.com ', null],
            ['',                          InvalidUrlException::class],
            ['   ',                       InvalidUrlException::class],
        ];
    }
}
