<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\ExternalDocs;
use Ddrv\OpenApiGenerator\Document\Tag;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $name
     * @param string|null $description
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testConstruct(string $name, ?string $description, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $tag = new Tag($name, $description);
        $name = trim($name);
        $description = trim((string)$description);
        if (!$description) {
            $description = $name;
        }
        $this->assertSame($name, $tag->getName());
        $this->assertSame($description, $tag->getDescription());
        $array = $tag->toArray();
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertSame($name, $array['name']);
        $this->assertSame($description, $array['description']);
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
        $tag = new Tag('phpunit');
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $tag->setName($name);
        $name = trim($name);
        $array = $tag->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame($name, $array['name']);
        $this->assertSame($name, $tag->getName());
    }

    /**
     * @dataProvider provideSetDescription
     *
     * @param string      $description
     * @param string|null $check
     *
     * @throws InvalidNameException
     */
    public function testSetDescription(?string $description, ?string $check)
    {
        $tag = new Tag('phpunit');
        $tag->setDescription($description);
        $array = $tag->toArray();
        if (is_null($check)) {
            $check = $tag->getName();
        }
        $this->assertArrayHasKey('description', $array);
        $this->assertSame($check, $array['description']);
        $this->assertSame($check, $tag->getDescription());
    }

    /**
     * @dataProvider provideSetExternalDocs
     *
     * @param ExternalDocs|null $externalDocs
     *
     * @throws InvalidNameException
     */
    public function testSetExternalDocs(?ExternalDocs $externalDocs)
    {
        $tag = new Tag('phpunit');
        $tag->setExternalDocs($externalDocs);
        $array = $tag->toArray();
        if ($externalDocs) {
            $this->assertArrayHasKey('externalDocs', $array);
            $this->assertInstanceOf(ExternalDocs::class, $tag->getExternalDocs());
        } else {
            $this->assertArrayNotHasKey('externalDocs', $array);
            $this->assertNull($tag->getExternalDocs());
        }
    }

    public function provideConstruct(): array
    {
        return [
            ['tag', 'Best tag',   null],
            ['tag', null,         null],
            ['tag', ' ',          null],
            ['tag', ' Best tag ', null],
            [' t ', '',           null],
            ['',    '',           InvalidNameException::class],
            ['   ', '',           InvalidNameException::class],
        ];
    }

    public function provideSetDescription(): array
    {
        return [
            ['Best tag', 'Best tag', null],
            [null,       null,       null],
            ['',         null,       null],
            [' Best   ', 'Best',     null],
        ];
    }

    public function provideSetName(): array
    {
        return [
            ['tag', null],
            [' t ', null],
            ['',    InvalidNameException::class],
            ['   ', InvalidNameException::class],
        ];
    }

    public function provideSetExternalDocs(): array
    {
        return [
            [null],
            [new ExternalDocs('http://docs.example.com')],
        ];
    }
}
