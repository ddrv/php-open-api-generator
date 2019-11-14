<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\CallbackRequest;
use Ddrv\OpenApiGenerator\Document\ExternalDocs;
use Ddrv\OpenApiGenerator\Document\Operation;
use Ddrv\OpenApiGenerator\Document\Parameter;
use Ddrv\OpenApiGenerator\Document\RequestBody;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Document\Security;
use Ddrv\OpenApiGenerator\Document\Server;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidHttpStatusException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\BooleanSchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\ObjectSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param Responses        $responses
     * @param RequestBody|null $requestBody
     */
    public function testConstruct(Responses $responses, ?RequestBody $requestBody)
    {
        $operation = new Operation($responses, $requestBody);
        $this->checkResponses($operation, $responses);
        $this->checkRequestBody($operation, $requestBody);
    }

    /**
     * @dataProvider provideSetResponses
     *
     * @param Responses $responses
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetResponses(Responses $responses)
    {
        $operation = $this->getOperation();
        $operation->setResponses($responses);
        $this->checkResponses($operation, $responses);
    }

    /**
     * @dataProvider provideSetRequestBody
     *
     * @param RequestBody $requestBody
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetRequestBody(?RequestBody $requestBody)
    {
        $operation = $this->getOperation();
        $operation->setRequestBody($requestBody);
        $this->checkRequestBody($operation, $requestBody);
    }

    /**
     * @dataProvider provideStringOrNull
     *
     * @param string|null $value
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetOperationId(?string $value)
    {
        $setter = function (Operation $operation, ?string $value) {
            $operation->setOperationId($value);
        };
        $getter = function (Operation $operation) {
            return $operation->getOperationId();
        };
        $key = 'operationId';
        $this->stringOrNull($value, $setter, $getter, $key);
    }

    /**
     * @dataProvider provideStringOrNull
     *
     * @param string|null $value
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetSummary(?string $value)
    {
        $setter = function (Operation $operation, ?string $value) {
            $operation->setSummary($value);
        };
        $getter = function (Operation $operation) {
            return $operation->getSummary();
        };
        $key = 'summary';
        $this->stringOrNull($value, $setter, $getter, $key);
    }

    /**
     * @dataProvider provideStringOrNull
     *
     * @param string|null $value
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetDescription(?string $value)
    {
        $setter = function (Operation $operation, ?string $value) {
            $operation->setDescription($value);
        };
        $getter = function (Operation $operation) {
            return $operation->getDescription();
        };
        $key = 'description';
        $this->stringOrNull($value, $setter, $getter, $key);
    }

    /**
     * @dataProvider provideBool
     *
     * @param bool $value
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetDeprecated(bool $value)
    {
        $operation = $this->getOperation();
        $operation->setDeprecated($value);
        $this->assertSame($value, $operation->isDeprecated());
        $array = $operation->toArray();
        if ($value) {
            $this->assertArrayHasKey('deprecated', $array);
            $this->assertTrue($array['deprecated']);
        } else {
            $this->assertArrayNotHasKey('deprecated', $array);
        }
    }

    /**
     * @dataProvider provideSetExternalDocs
     *
     * @param ExternalDocs|null $externalDocs
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSetExternalDocs(?ExternalDocs $externalDocs)
    {
        $operation = $this->getOperation();
        $operation->setExternalDocs($externalDocs);
        $array = $operation->toArray();
        if ($externalDocs) {
            $this->assertArrayHasKey('externalDocs', $array);
            $this->assertInstanceOf(ExternalDocs::class, $operation->getExternalDocs());
        } else {
            $this->assertArrayNotHasKey('externalDocs', $array);
            $this->assertNull($operation->getExternalDocs());
        }
    }

    /**
     * @dataProvider provideTag
     *
     * @param string[] $add
     * @param string[] $remove
     * @param string[] $check
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testTag(array $add, array $remove, array $check)
    {
        $operation = $this->getOperation();
        $added = [];
        foreach ($add as $tag) {
            $operation->addTag($tag);
            $t = trim($tag);
            if ($t) {
                $added[$t] = $t;
            }
        }
        $this->checkTags($operation, $added);

        foreach ($remove as $tag) {
            $operation->removeTag($tag);
        }
        $this->checkTags($operation, $check);
    }

    /**
     * @dataProvider provideAddServer
     *
     * @param Server[] $add
     * @param Server[] $check
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testAddServer(array $add, array $check)
    {
        $operation = $this->getOperation();
        foreach ($add as $server) {
            $operation->addServer($server);
        }
        $array = $operation->toArray();
        $count = count($check);
        $this->assertArrayHasKey('servers', $array);
        $this->assertCount($count, $array['servers']);
        $this->assertCount($count, $operation->getServers());

        foreach ($check as $server) {
            $isOk = false;
            foreach ($operation->getServers() as $p) {
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
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testRemoveServer(array $add, array $remove, array $check)
    {
        $operation = $this->getOperation();
        $count = count($check);
        foreach ($add as $server) {
            $operation->addServer($server);
        }
        foreach ($remove as $server) {
            $operation->removeServer($server);
        }
        $array = $operation->toArray();
        $this->assertCount($count, $operation->getServers());
        if (!$count) {
            $this->assertArrayNotHasKey('servers', $array);
        } else {
            $this->assertArrayHasKey('servers', $array);
            $this->assertCount($count, $array['servers']);
        }

        foreach ($check as $server) {
            $isOk = false;
            foreach ($operation->getServers() as $p) {
                if ($p->getHash() === $server->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @dataProvider provideAddParameter
     *
     * @param Parameter[] $add
     * @param Parameter[] $check
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testAddParameter(array $add, array $check)
    {
        $operation = $this->getOperation();
        foreach ($add as $parameter) {
            $operation->addParameter($parameter);
        }
        $count = count($check);
        $array = $operation->toArray();
        $this->assertArrayHasKey('parameters', $array);
        $this->assertCount($count, $array['parameters']);
        $this->assertCount($count, $operation->getParameters());

        foreach ($check as $parameter) {
            $isOk = false;
            foreach ($operation->getParameters() as $p) {
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
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testRemoveParameter(array $add, array $remove, array $check)
    {
        $count = count($check);
        $operation = $this->getOperation();
        foreach ($add as $parameter) {
            $operation->addParameter($parameter);
        }
        foreach ($remove as $parameter) {
            $operation->removeParameter($parameter);
        }
        $array = $operation->toArray();
        $this->assertCount($count, $operation->getParameters());
        if (!$count) {
            $this->assertArrayNotHasKey('parameters', $array);
        } else {
            $this->assertArrayHasKey('parameters', $array);
            $this->assertCount($count, $array['parameters']);
        }

        foreach ($check as $parameter) {
            $isOk = false;
            foreach ($operation->getParameters() as $p) {
                if ($p->getHash() === $parameter->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @dataProvider provideCallback
     *
     * @param CallbackRequest[] $add
     * @param string[]          $remove
     * @param string[]          $check
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testCallbacks(array $add, array $remove, array $check)
    {
        $operation = $this->getOperation();
        foreach ($add as $name => $callback) {
            $operation->setCallback($name, $callback);
        }
        $array = $operation->toArray();
        $this->assertArrayHasKey('callbacks', $array);
        $added = $operation->getCallbacks();
        $this->assertCount(count($add), $added);
        foreach (array_keys($add) as $key) {
            $this->assertInstanceOf(CallbackRequest::class, $added[$key]);
        }

        foreach ($remove as $key) {
            $operation->removeCallback($key);
        }
        $all = $operation->getCallbacks();
        $this->assertCount(count($check), $all);
        foreach ($check as $key) {
            $this->assertInstanceOf(CallbackRequest::class, $all[$key]);
        }
    }

    /**
     * @dataProvider provideSecurity
     *
     * @param Security[] $add
     * @param Security[] $remove
     * @param string[]   $check
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testSecurity(array $add, array $remove, array $check)
    {
        $operation = $this->getOperation();
        $keys = [];
        foreach ($add as $name => $security) {
            $operation->addSecurity($security);
            $keys[$security->getName()] = $security->getName();
        }
        $added = $operation->getSecurity();
        $this->assertCount(count($keys), $added);
        foreach ($added as $security) {
            $this->assertInstanceOf(Security::class, $security);
            $this->assertContains($security->getName(), $keys);
        }

        foreach ($remove as $security) {
            $operation->removeSecurity($security);
        }
        $all = $operation->getSecurity();
        $this->assertCount(count($check), $all);
        foreach ($all as $security) {
            $this->assertContains($security->getName(), $check);
        }
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function provideConstruct(): array
    {
        $responses = new Responses(new Response('application/json', new ArraySchema(new IntegerSchema())));
        $requestBody = new RequestBody('application/json', new ArraySchema(new IntegerSchema()));
        return [
            [$responses, $requestBody],
            [$responses, null],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     * @throws InvalidPatternException
     */
    public function provideSetRequestBody(): array
    {
        $requestBody1 = new RequestBody('application/json', new ArraySchema(new IntegerSchema()));
        $requestBody2 = new RequestBody('text/plain', new StringSchema(StringSchema::FORMAT_DATE_TIME));
        return [
            [$requestBody1],
            [$requestBody2],
            [null]
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     * @throws InvalidPatternException
     */
    public function provideSetResponses(): array
    {
        $responses1 = new Responses(new Response('application/json', new ArraySchema(new IntegerSchema())));
        $responses2 = new Responses(new Response('text/plain', new StringSchema(StringSchema::FORMAT_DATE_TIME)));
        return [
            [$responses1],
            [$responses2],
        ];
    }

    public function provideStringOrNull(): array
    {
        return [
            [' text '],
            ['text'],
            ['  '],
            [''],
            [null],
        ];
    }

    public function provideBool(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public function provideSetExternalDocs(): array
    {
        return [
            [null],
            [new ExternalDocs('http://docs.example.com')],
        ];
    }

    public function provideTag(): array
    {
        return [
            [['tag-1', 'tag-2', 'tag-3'], [],                               ['tag-1', 'tag-2', 'tag-3']],
            [['tag-1', 'tag-1', 'tag-2'], [],                               ['tag-1', 'tag-2']],
            [['tag-1', '      tag-1   '], [],                               ['tag-1']],
            [['', ' ', '              '], [],                               []],
            [['tag-1', 'tag-2', 'tag-3'], ['tag-1', '  tag-1  ', '', '  '], ['tag-2', 'tag-3']],
            [['tag-1', 'tag-2', 'tag-3'], ['tag-1', 'tag-2'],               ['tag-3']],
            [['tag-1', 'tag-2', 'tag-3'], ['tag-1', 'tag-2', 'tag-3'],      []],
        ];
    }

    public function provideAddServer(): array
    {
        $s1 = new Server('https://srv-1');
        $s2 = new Server('https://srv-2');
        $s3 = new Server('https://srv-1', 'local API server');
        return [
            [[$s1],           [$s1]],
            [[$s1, $s2],      [$s1, $s2]],
            [[$s1, $s2, $s3], [$s2, $s3]],
        ];
    }

    public function provideRemoveServer(): array
    {
        $s1 = new Server('https://srv-1');
        $s2 = new Server('https://srv-2');
        $r1 = new Server('https://srv-1', 'removed');
        $r2 = new Server('https://srv-2', 'removed');
        return [
            [[$s1, $s2], [$s1, $s2], []],
            [[$s1, $s2], [$r1, $r2], []],
            [[$s1, $s2], [$r2],      [$s1]],

        ];
    }

    public function provideAddParameter(): array
    {
        $p1 = new Parameter(Parameter::IN_PATH, 'user');
        $p2 = new Parameter(Parameter::IN_HEADER, 'x-token');
        $p3 = (new Parameter(Parameter::IN_PATH, 'user'))->setDescription('replaced');
        $p4 = new Parameter(Parameter::IN_QUERY, 'offset');
        return [
            [[$p1, $p2, $p3], [$p2, $p3]],
            [[$p1, $p2, $p4], [$p1, $p2, $p4]],
        ];
    }

    public function provideRemoveParameter(): array
    {
        $p1 = new Parameter(Parameter::IN_HEADER, 'x-token');
        $p2 = new Parameter(Parameter::IN_PATH, 'user');
        $p3 = new Parameter(Parameter::IN_QUERY, 'offset');
        $r1 = (new Parameter(Parameter::IN_HEADER, 'x-token'))->setDescription('replaced');
        $r2 = (new Parameter(Parameter::IN_PATH, 'user'))->setDescription('replaced');
        $r3 = (new Parameter(Parameter::IN_QUERY, 'offset'))->setDescription('replaced');

        return [
            [[$p1, $p2],      [$p1, $r3],      [$p2]],
            [[$p1, $p2, $p3], [$r1, $r2],      [$p3]],
            [[$p1, $p2, $p3], [$p1, $p2, $p3], []],
            [[$p1, $p2, $p3], [$r1, $r2, $r3], []],
            [[$p1, $p2, $p3], [$p1, $r3],      [$p2]],
        ];
    }

    /**
     * @return array
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     * @throws InvalidHttpStatusException
     * @throws InvalidUrlException
     */
    public function provideCallback(): array
    {
        $error = new ObjectSchema();
        $error->setProperty(new IntegerSchema(), 'code');
        $error->setProperty(new StringSchema(), 'message');
        $success = new ObjectSchema();
        $success->setProperty(new BooleanSchema(), 'success');
        $responses = new Responses(new Response('application/json', $error));
        $responses->setResponse(200, new Response('application/json', $success));
        $cb1 = new CallbackRequest('http://callback.example.com', 'post', new Operation($responses, null));
        $request = new RequestBody('application/json', new StringSchema());
        $cb2 = new CallbackRequest('http://callback.example.com', 'patch', new Operation($responses, $request));
        return [
            [['cb1' => $cb1, 'cb2' => $cb2], [],             ['cb1', 'cb2']],
            [['cb1' => $cb1, 'cb2' => $cb2], ['cb2'],        ['cb1']],
            [['cb1' => $cb1, 'cb2' => $cb2], ['cb1', 'cb2'], []],
        ];
    }

    public function provideSecurity(): array
    {
        $s1 = new Security('s1');
        $s2 = new Security('s2');
        $s3 = (new Security('s3'))->addScope('phpunit.test');
        $s4 = new Security('s1');
        return [
            [[$s1, $s2, $s3], [],              ['s1', 's2', 's3']],
            [[$s1, $s2, $s4], [],              ['s1', 's2']],
            [[$s1, $s2],      [$s4],           ['s2']],
            [[$s1, $s2, $s3], [$s1, $s2, $s3], []],
        ];
    }

    /**
     * @param string|null $value
     * @param callable    $setter
     * @param callable    $getter
     * @param string      $key
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    private function stringOrNull(?string $value, callable $setter, callable $getter, string $key)
    {
        $operation = $this->getOperation();
        $setter($operation, $value);
        $check = trim((string)$value);
        if (!$check) {
            $check = null;
        }
        $this->assertSame($check, $getter($operation));
        $array = $operation->toArray();
        if ($check) {
            $this->assertArrayHasKey($key, $array);
            $this->assertSame($check, $array[$key]);
        } else {
            $this->assertArrayNotHasKey($key, $array);
        }
    }

    private function checkResponses(Operation $operation, Responses $responses)
    {
        $array = $operation->toArray();
        $this->assertArrayHasKey('responses', $array);
        $this->assertSame($responses->getHash(), $operation->getResponses()->getHash());
        $this->assertInstanceOf(Responses::class, $operation->getResponses());
        foreach ($responses->getResponses() as $status => $response) {
            $this->assertArrayHasKey((string)$status, $array['responses']);
        }
    }

    private function checkRequestBody(Operation $operation, ?RequestBody $requestBody)
    {
        $array = $operation->toArray();
        if ($requestBody) {
            $this->assertInstanceOf(RequestBody::class, $operation->getRequestBody());
            $this->assertArrayHasKey('requestBody', $array);
            $this->assertSame($requestBody->getHash(), $operation->getRequestBody()->getHash());
        } else {
            $this->assertNull($operation->getRequestBody());
            $this->assertArrayNotHasKey('requestBody', $array);
        }
    }

    /**
     * @return Operation
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    private function getOperation(): Operation
    {
        $responses = new Responses(new Response('application/json', new ArraySchema(new IntegerSchema())));
        $requestBody = new RequestBody('application/json', new ArraySchema(new IntegerSchema()));
        return new Operation($responses, $requestBody);
    }

    private function checkTags(Operation $operation, array $check)
    {
        $count = count($check);
        $array = $operation->toArray();
        $this->assertCount($count, $operation->getTags());
        if ($count) {
            $this->assertArrayHasKey('tags', $array);
            $this->assertCount($count, $array['tags']);
            foreach ($check as $tag) {
                $this->assertContains($tag, $array['tags']);
            }
        } else {
            $this->assertArrayNotHasKey('tags', $array);
        }
    }
}
