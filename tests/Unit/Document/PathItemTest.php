<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractPathItem;
use Ddrv\OpenApiGenerator\Document\Operation;
use Ddrv\OpenApiGenerator\Document\PathItem;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastOperationException;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\PathItemTestCase;

class PathItemTest extends PathItemTestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $url
     * @param string      $method
     * @param Operation   $operation
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidUrlException
     */
    public function testConstruct(string $url, string $method, Operation $operation, ?string $exception)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $callback = new PathItem($url, $method, $operation);
        $url = trim($url);
        $this->assertSame($url, $callback->getPath());
        $m = mb_strtolower(trim($method));
        $this->checkOperation($callback, $method, $operation, [$m]);
    }

    /**
     * @dataProvider provideSetOperation
     *
     * @param string      $method
     * @param Operation   $operation
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     * @throws InvalidUrlException
     */
    public function testSetOperation(string $method, Operation $operation, ?string $exception)
    {
        $pathItem = $this->getPathItem();
        if ($exception) {
            $this->expectException($exception);
        }
        $pathItem->setOperation($method, $operation);
        $m = mb_strtolower(trim($method));
        $methods[$m] = $m;
        $methods['get'] = 'get';
        $this->checkOperation($pathItem, $method, $operation, $methods);
    }

    /**
     * @dataProvider provideRemoveOperation
     *
     * @param Operation[] $add
     * @param string[]    $remove
     * @param string[]    $methods
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     * @throws RemovingLastOperationException
     * @throws InvalidUrlException
     */
    public function testRemoveOperation(array $add, array $remove, array $methods, ?string $exception)
    {
        $pathItem = $this->getPathItem();
        foreach ($add as $method => $operation) {
            $pathItem->setOperation($method, $operation);
        }
        if ($exception) {
            $this->expectException($exception);
        }
        foreach ($remove as $method) {
            $pathItem->removeOperation($method);
        }
        $array = $this->getArray($pathItem);
        foreach ($remove as $method) {
            $this->assertNull($pathItem->getOperation($method));
            $this->assertArrayNotHasKey($method, $array);
        }
        foreach ($methods as $method) {
            $this->assertInstanceOf(Operation::class, $pathItem->getOperation($method));
            $this->assertArrayHasKey($method, $array);
        }
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function provideRemoveOperation(): array
    {
        $responses = new Responses(new Response('text/plain', new StringSchema()));
        $operation = new Operation($responses, null);
        $add = [
            'get' => $operation,
            'put' => $operation,
            'post' => $operation,
            'delete' => $operation,
            'options' => $operation,
            'head' => $operation,
            'patch' => $operation,
            'trace' => $operation,
        ];
        $methods = array_keys($add);
        $remove = [];
        $result = [];
        while ($methods) {
            $remove[] = array_shift($methods);
            $exception = $methods ? null : RemovingLastOperationException::class;
            $result[] = [$add, $remove, $methods, $exception];
        }
        return $result;
    }

    /**
     * @return PathItem
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     * @throws InvalidUrlException
     */
    protected function getPathItem(): AbstractPathItem
    {
        $response = new Response('application/json', new ArraySchema(new IntegerSchema()));
        return new PathItem('https://api.example.com', 'GET', new Operation(new Responses($response), null));
    }
}
