<?php

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Document\AbstractHttpMessage;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastContentException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\Schema\AllOfSchema;
use Ddrv\OpenApiGenerator\Schema\AnySchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use PHPUnit\Framework\TestCase;

abstract class HttpMessageTestCase extends TestCase
{

    /**
     * @dataProvider provideContent
     *
     * @param string         $contentType
     * @param AbstractSchema $schema
     * @param string|null    $exception
     *
     * @throws InvalidContentTypeException
     */
    public function testSetContent(string $contentType, AbstractSchema $schema, ?string $exception)
    {
        $element = $this->getElement();
        if ($exception) {
            $this->expectException($exception);
        }
        $element->setContent($contentType, $schema);
        $this->checkContent($element, $contentType);
    }

    public function checkContent(AbstractHttpMessage $element, string $contentType)
    {
        $contentType = trim($contentType);
        $array = $element->toArray();
        $this->assertArrayHasKey($contentType, $array['content']);
        $this->assertArrayHasKey($contentType, $element->getContents());
        $this->assertInstanceOf(AbstractSchema::class, $element->getContent($contentType));
    }

    public function checkDescription(AbstractHttpMessage $element, ?string $description)
    {
        $array = $element->toArray();
        if ($description) {
            $description = trim($description);
        }
        if ($description) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($description, $array['description']);
            $this->assertSame($description, $element->getDescription());
        } else {
            $this->assertArrayNotHasKey('description', $array);
            $this->assertNull($element->getDescription());
        }
    }

    /**
     * @throws InvalidContentTypeException
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     * @throws RemovingLastScopeException
     */
    public function removeContent()
    {
        $element = $this->getElement();
        $element
            ->setContent('text/plain', new StringSchema())
            ->setContent('text/html', new StringSchema())
        ;
        $this->assertInstanceOf(AbstractSchema::class, $element->getContent('text/plain'));
        $element->removeContent('text/plain');
        $this->assertNull($element->getContent('text/plain'));
    }

    /**
     * @throws RemovingLastScopeException
     */
    public function removeLastContent()
    {
        $element = $this->getElement();
        $types = array_keys($element->getContents());
        $last = array_pop($types);
        foreach ($types as $type) {
            $element->removeContent($type);
        }
        $this->expectException(RemovingLastContentException::class);
        $element->removeContent($last);
    }

    /**
     * @dataProvider provideDescription
     *
     * @param string|null $description
     */
    public function testSetDescription(?string $description)
    {
        $element = $this->getElement();
        $element->setDescription($description);
        $description = trim((string)$description);
        if (!$description) {
            $description = null;
        }
        $this->assertSame($description, $element->getDescription());
        $array = $element->toArray();
        if (is_null($description)) {
            $this->assertArrayNotHasKey('description', $array);
        } else {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($description, $array['description']);
        }
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param string         $contentType
     * @param AbstractSchema $schema
     * @param string|null    $description
     * @param string|null    $exception
     */
    public function testConstruct(string $contentType, AbstractSchema $schema, ?string $description, ?string $exception)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $element = $this->make($contentType, $schema, $description);
        $this->checkContent($element, $contentType);
        $this->checkDescription($element, $description);
        $array = $element->toArray();
        $count = 1;
        if (array_key_exists('description', $array)) {
            $count++;
        }
        $this->assertCount($count, $array);
    }

    public function provideContent(): array
    {
        return [
            ['text/plain', new StringSchema(), null],
            [' text/html', new AnySchema(), null],
            ['          ', new AllOfSchema(), InvalidContentTypeException::class],
            ['',           new StringSchema(), InvalidContentTypeException::class],
        ];
    }

    public function provideDescription(): array
    {
        return [
            [null],
            ['description'],
            [' description '],
        ];
    }

    public function provideConstruct(): array
    {
        return [
            ['text/plain', new StringSchema(), null, null],
            ['text/plain', new StringSchema(), '  ', null],
            ['text/plain', new StringSchema(), ' !', null],
            [' text/html', new StringSchema(), 'ok', null],
            ['          ', new StringSchema(), null, InvalidContentTypeException::class],
            ['',           new StringSchema(), null, InvalidContentTypeException::class],
        ];
    }

    public function getElement(): AbstractHttpMessage
    {
        return $this->make('text/plain', new StringSchema(), null);
    }

    abstract public function make(string $type, AbstractSchema $schema, ?string $description): AbstractHttpMessage;
}
