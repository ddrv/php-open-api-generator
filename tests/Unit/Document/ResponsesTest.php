<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidHttpStatusException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Schema\ObjectSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use PHPUnit\Framework\TestCase;

class ResponsesTest extends TestCase
{

    /**
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function testConstruct()
    {
        $responses = new Responses(new Response('text/plain', new StringSchema()));
        $this->assertInstanceOf(Response::class, $responses->getDefaultResponse());
    }

    /**
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function testSetDefaultResponse()
    {
        $responses = new Responses(new Response('text/plain', new StringSchema(), 'old'));
        $newDefaultResponse = new Response('application/json', new ObjectSchema(), 'new');
        $this->assertSame('old', $responses->getDefaultResponse()->getDescription());
        $responses->setDefaultResponse($newDefaultResponse);
        $this->assertInstanceOf(Response::class, $responses->getDefaultResponse());
        $this->assertSame('new', $responses->getDefaultResponse()->getDescription());
    }

    /**
     * @dataProvider provideSetResponse
     *
     * @param Response    $response
     * @param int         $httpStatus
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidHttpStatusException
     * @throws InvalidPatternException
     */
    public function testSetResponse(Response $response, int $httpStatus, ?string $exception)
    {
        $responses = new Responses(new Response('text/plain', new StringSchema()));
        $check = uniqid('phpunit-');
        $response->setDescription($check);
        if ($exception) {
            $this->expectException($exception);
        }
        $responses->setResponse($httpStatus, $response);
        $this->assertInstanceOf(Response::class, $responses->getResponse($httpStatus));
        $this->assertSame($check, $responses->getResponse($httpStatus)->getDescription());
    }

    /**
     * @dataProvider provideRemoveResponse
     *
     * @param Response    $response
     * @param int[]         $add
     * @param int[]         $remove
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidHttpStatusException
     * @throws InvalidPatternException
     */
    public function testRemoveResponse(Response $response, array $add, array $remove)
    {
        $responses = new Responses(new Response('text/plain', new StringSchema()));
        foreach ($add as $httpStatus) {
            $responses->setResponse($httpStatus, $response);
        }
        foreach ($remove as $httpStatus) {
            $responses->removeResponse($httpStatus);
            $array = $responses->toArray();
            $this->assertNull($responses->getResponse($httpStatus));
            $this->assertArrayNotHasKey((string)$httpStatus, $array);
        }
        $diff = array_diff($add, $remove);
        $array = $responses->toArray();
        foreach ($diff as $httpStatus) {
            $this->assertArrayHasKey((string)$httpStatus, $array);
            $this->assertInstanceOf(Response::class, $responses->getResponse($httpStatus));
        }
        $count = count($diff);
        $this->assertCount($count, $responses->getResponses());
        $this->assertCount($count + 1, $array);
    }

    /**
     * @return array[]
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     */
    public function provideSetResponse(): array
    {
        $response = new Response('application/json', new ObjectSchema(), 'OK');
        $result = [
            [$response, 99, InvalidHttpStatusException::class],
        ];
        for ($status = 100; $status <= 599; $status++) {
            $result[] = [$response, $status,  null];
        }
        $result[] = [$response, 600, InvalidHttpStatusException::class];
        return $result;
    }

    /**
     * @return array[]
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     */
    public function provideRemoveResponse(): array
    {
        $response = new Response('application/json', new ObjectSchema(), 'OK');

        return [
            [$response, [200, 404, 418, 500], [418]],
        ];
    }
}
