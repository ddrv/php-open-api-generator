<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Document\AbstractPathItem;
use Ddrv\OpenApiGenerator\Document\Operation;
use Ddrv\OpenApiGenerator\Document\Parameter;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Document\Server;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use PHPUnit\Framework\TestCase;

abstract class PathItemTestCase extends TestCase
{

    /**
     * @dataProvider provideSetNullableString
     *
     * @param string|null $summary
     */
    public function testSetSummary(?string $summary)
    {
        $pathItem = $this->getPathItem();
        $pathItem->setSummary($summary);
        $array = $this->getArray($pathItem);
        $check = trim((string)$summary);
        if ($check) {
            $this->assertSame($check, $pathItem->getSummary());
            $this->assertArrayHasKey('summary', $array);
            $this->assertSame($check, $array['summary']);
        } else {
            $this->assertNull($pathItem->getSummary());
            $this->assertArrayNotHasKey('summary', $array);
        }
    }

    /**
     * @dataProvider provideSetNullableString
     *
     * @param string|null $description
     */
    public function testSetDescription(?string $description)
    {
        $pathItem = $this->getPathItem();
        $pathItem->setDescription($description);
        $array = $this->getArray($pathItem);
        $check = trim((string)$description);
        if ($check) {
            $this->assertSame($check, $pathItem->getDescription());
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($check, $array['description']);
        } else {
            $this->assertNull($pathItem->getDescription());
            $this->assertArrayNotHasKey('description', $array);
        }
    }

    /**
     * @dataProvider provideAddParameter
     *
     * @param Parameter[] $add
     * @param Parameter[] $check
     */
    public function testAddParameter(array $add, array $check)
    {
        $pathItem = $this->getPathItem();
        foreach ($add as $parameter) {
            $pathItem->addParameter($parameter);
        }
        $array = $this->getArray($pathItem);
        $count = count($check);
        $this->assertCount($count, $pathItem->getParameters());
        $this->assertArrayHasKey('parameters', $array);
        $this->assertCount($count, $array['parameters']);

        foreach ($check as $parameter) {
            $isOk = false;
            foreach ($pathItem->getParameters() as $p) {
                if ($p->getHash() === $parameter->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @dataProvider provideRemoveParameter
     *
     * @param Parameter[] $add
     * @param Parameter[] $remove
     * @param Parameter[] $check
     */
    public function testRemoveParameter(array $add, array $remove, array $check)
    {
        $pathItem = $this->getPathItem();
        foreach ($add as $parameter) {
            $pathItem->addParameter($parameter);
        }
        foreach ($remove as $parameter) {
            $pathItem->removeParameter($parameter);
        }
        $array = $this->getArray($pathItem);
        $count = count($check);
        $this->assertCount($count, $pathItem->getParameters());
        if ($count) {
            $this->assertArrayHasKey('parameters', $array);
            $this->assertCount($count, $array['parameters']);
        } else {
            $this->assertArrayNotHasKey('parameters', $array);
        }

        foreach ($check as $parameter) {
            $isOk = false;
            foreach ($pathItem->getParameters() as $p) {
                if ($p->getHash() === $parameter->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @dataProvider provideAddServer
     *
     * @param Server[] $add
     * @param Server[] $check
     */
    public function testAddServer(array $add, array $check)
    {
        $pathItem = $this->getPathItem();
        foreach ($add as $server) {
            $pathItem->addServer($server);
        }
        $array = $this->getArray($pathItem);
        $count = count($check);
        $this->assertCount($count, $pathItem->getServers());
        if ($count) {
            $this->assertArrayHasKey('servers', $array);
            $this->assertCount($count, $array['servers']);
        }

        foreach ($check as $server) {
            $isOk = false;
            foreach ($pathItem->getServers() as $p) {
                if ($p->getHash() === $server->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @dataProvider provideRemoveServer
     *
     * @param Server[] $add
     * @param Server[] $remove
     * @param Server[] $check
     */
    public function testRemoveServer(array $add, array $remove, array $check)
    {
        $pathItem = $this->getPathItem();
        foreach ($add as $server) {
            $pathItem->addServer($server);
        }
        foreach ($remove as $server) {
            $pathItem->removeServer($server);
        }
        $array = $this->getArray($pathItem);
        $count = count($check);
        $this->assertCount($count, $pathItem->getServers());
        if ($count) {
            $this->assertArrayHasKey('servers', $array);
            $this->assertCount($count, $array['servers']);
        } else {
            $this->assertArrayNotHasKey('servers', $array);
        }

        foreach ($check as $server) {
            $isOk = false;
            foreach ($pathItem->getServers() as $p) {
                if ($p->getHash() === $server->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function provideConstruct(): array
    {
        $responses = new Responses(new Response('text/plain', new StringSchema()));
        $operation = new Operation($responses, null);
        return [
            ['https://example.com', 'GET',     $operation, null],
            ['https://example.com', 'GeT',     $operation, null],
            ['https://example.com', 'get',     $operation, null],
            ['https://example.com', 'PUT',     $operation, null],
            ['https://example.com', 'POST',    $operation, null],
            ['https://example.com', 'DELETE',  $operation, null],
            ['https://example.com', 'OPTIONS', $operation, null],
            ['https://example.com', 'HEAD',    $operation, null],
            ['https://example.com', 'PATCH',   $operation, null],
            ['https://example.com', 'TRACE',   $operation, null],
            [' https://exmpl.com ', ' GET ',   $operation, null],
            ['https://example.com', 'git',     $operation, ArgumentOutOfListException::class],
            ['                   ', 'git',     $operation, InvalidUrlException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function provideSetOperation(): array
    {
        $responses = new Responses(new Response('text/plain', new StringSchema()));
        $operation = new Operation($responses, null);
        return [
            ['GET',     $operation, null],
            ['GeT',     $operation, null],
            ['get',     $operation, null],
            ['PUT',     $operation, null],
            ['POST',    $operation, null],
            ['DELETE',  $operation, null],
            ['OPTIONS', $operation, null],
            ['HEAD',    $operation, null],
            ['PATCH',   $operation, null],
            ['TRACE',   $operation, null],
            [' GET ',   $operation, null],
            ['git',     $operation, ArgumentOutOfListException::class],
        ];
    }

    public function provideSetNullableString(): array
    {
        return [
            [' some text '],
            ['some text'],
            [' '],
            [null]
        ];
    }

    public function provideAddParameter(): array
    {
        $p1 = new Parameter(Parameter::IN_PATH, 'id');
        $p2 = new Parameter(Parameter::IN_HEADER, 'x-auth');
        $p3 = (new Parameter(Parameter::IN_PATH, 'id'))->setDescription('replaced');
        $p4 = new Parameter(Parameter::IN_QUERY, 'page');
        return [
            [[$p1, $p2, $p3], [$p2, $p3]],
            [[$p1, $p2, $p4], [$p1, $p2, $p4]],
        ];
    }

    public function provideRemoveParameter(): array
    {
        $p1 = new Parameter(Parameter::IN_HEADER, 'x-auth');
        $p2 = new Parameter(Parameter::IN_PATH, 'id');
        $p3 = new Parameter(Parameter::IN_QUERY, 'page');
        $r1 = (new Parameter(Parameter::IN_HEADER, 'x-auth'))->setDescription('replaced');
        $r2 = (new Parameter(Parameter::IN_PATH, 'id'))->setDescription('replaced');
        $r3 = (new Parameter(Parameter::IN_QUERY, 'page'))->setDescription('replaced');

        return [
            [[$p1, $p2, $p3], [$p1, $p2, $p3], []],
            [[$p1, $p2, $p3], [$r1, $r2, $r3], []],
            [[$p1, $p2],      [$p1, $r3],      [$p2]],
            [[$p1, $p2, $p3], [$r1, $r2],      [$p3]],
            [[$p1, $p2, $p3], [$p1, $r3],      [$p2]],
        ];
    }

    public function provideAddServer(): array
    {
        $s1 = new Server('https://api-1');
        $s2 = new Server('https://api-2');
        $s3 = new Server('https://api-1', 'local API server');
        return [
            [[$s1],           [$s1]],
            [[$s1, $s2],      [$s1, $s2]],
            [[$s1, $s2, $s3], [$s2, $s3]],
        ];
    }

    public function provideRemoveServer(): array
    {
        $s1 = new Server('https://api-1');
        $s2 = new Server('https://api-2');
        $r1 = new Server('https://api-1', 'removed');
        $r2 = new Server('https://api-2', 'removed');
        return [
            [[$s1, $s2], [$s1, $s2], []],
            [[$s1, $s2], [$r1, $r2], []],
            [[$s1, $s2], [$r2],      [$s1]],

        ];
    }

    /**
     * @param AbstractPathItem $item
     *
     * @return array
     */
    protected function getArray(AbstractPathItem $item): array
    {
        $array = $item->toArray();
        return $array[$item->getPath()];
    }

    protected function checkOperation(AbstractPathItem $pathItem, string $method, Operation $operation, array $methods)
    {
        $method = mb_strtolower(trim($method));
        $array = $pathItem->toArray();
        $this->assertCount(1, $array);
        $this->assertArrayHasKey($pathItem->getPath(), $array);
        $this->assertInstanceOf(Operation::class, $pathItem->getOperation($method));
        $this->assertSame($operation->getHash(), $pathItem->getOperation($method)->getHash());
        $array = $array[$pathItem->getPath()];
        $this->assertCount(count($methods), $array);
        $this->assertArrayHasKey($method, $array);
        foreach ($methods as $m) {
            $this->assertArrayHasKey($m, $array);
        }
    }

    /**
     * @return AbstractPathItem
     */
    abstract protected function getPathItem(): AbstractPathItem;
}
