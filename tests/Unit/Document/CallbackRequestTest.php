<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractPathItem;
use Ddrv\OpenApiGenerator\Document\CallbackRequest;
use Ddrv\OpenApiGenerator\Document\Operation;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\PathItemTestCase;

class CallbackRequestTest extends PathItemTestCase
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
        $callback = new CallbackRequest($url, $method, $operation);
        $url = trim($url);
        $this->assertSame($url, $callback->getPath());
        $m = mb_strtolower(trim($method));
        $this->checkOperation($callback, $method, $operation, [$m]);
    }

    /**
     * @dataProvider provideSetOperation
     *
     * @param string $method
     * @param Operation $operation
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
        $callback = $this->getPathItem();
        if ($exception) {
            $this->expectException($exception);
        }
        $callback->setOperation($method, $operation);
        $m = mb_strtolower(trim($method));
        $this->checkOperation($callback, $method, $operation, [$m]);
    }

    /**
     * @return CallbackRequest
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
        return new CallbackRequest('http://callback.example.com', 'GET', new Operation(new Responses($response), null));
    }
}
