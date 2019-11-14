<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractHttpMessage;
use Ddrv\OpenApiGenerator\Document\Header;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\HttpMessageTestCase;

/**
 * @method Response getElement()
 */
class ResponseTest extends HttpMessageTestCase
{

    /**
     * @dataProvider provideAddHeader
     *
     * @param Header[] $headers
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testAddHeader(array $headers, ?string $exception)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $element = $this->getElement();
        foreach ($headers as $name => $header) {
            $element->addHeader($name, $header);
        }
        $this->checkHeadersCount($element, array_keys($headers));
    }

    /**
     * @dataProvider provideRemoveHeader
     *
     * @param Header[] $add
     * @param string[] $remove
     *
     * @throws InvalidNameException
     */
    public function testRemoveHeader(array $add, array $remove)
    {
        $element = $this->getElement();
        foreach ($add as $name => $header) {
            $element->addHeader($name, $header);
        }
        foreach ($remove as $header) {
            $element->removeHeader($header);
        }
        $this->checkHeadersCount($element, array_diff(array_keys($add), $remove));
    }

    private function checkHeadersCount(Response $element, array $headers)
    {
        $count = count($headers);
        $array = $element->toArray();
        $this->assertCount($count, $element->getHeaders());
        if ($count) {
            $this->assertArrayHasKey('headers', $array);
            $this->assertCount($count, $array['headers']);
        } else {
            $this->assertArrayNotHasKey('headers', $array);
        }
        foreach ($headers as $header) {
            $this->assertContains($header, array_keys($element->getHeaders()));
            $this->assertContains($header, array_keys($array['headers']));
        }
    }

    public function provideAddHeader(): array
    {
        $h1 = new Header();
        $h2 = (new Header())->setDescription('header 2')->setRequired(true);
        return [
            [['x-auth' => $h1, 'x-real-ip' => $h2], null],
            [['' => $h1],                           InvalidNameException::class],
            [['   ' => $h2],                        InvalidNameException::class],
        ];
    }

    public function provideRemoveHeader(): array
    {
        $h1 = new Header();
        $h2 = (new Header())->setDescription('header 2')->setRequired(true);
        return [
            [['x-auth' => $h1, 'x-real-ip' => $h2], []],
            [['x-auth' => $h1, 'x-real-ip' => $h2], ['x-auth']],
            [['x-auth' => $h1, 'x-real-ip' => $h2], ['x-real-ip']],
        ];
    }

    /**
     * @param string $type
     * @param AbstractSchema $schema
     * @param string|null $description
     *
     * @return Response
     *
     * @throws InvalidContentTypeException
     */
    public function make(string $type, AbstractSchema $schema, ?string $description): AbstractHttpMessage
    {
        return new Response($type, $schema, $description);
    }
}
