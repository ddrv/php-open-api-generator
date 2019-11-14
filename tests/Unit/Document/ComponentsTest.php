<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractExample;
use Ddrv\OpenApiGenerator\Document\CallbackRequest;
use Ddrv\OpenApiGenerator\Document\Components;
use Ddrv\OpenApiGenerator\Document\Example;
use Ddrv\OpenApiGenerator\Document\ExternalExample;
use Ddrv\OpenApiGenerator\Document\Header;
use Ddrv\OpenApiGenerator\Document\Operation;
use Ddrv\OpenApiGenerator\Document\Parameter;
use Ddrv\OpenApiGenerator\Document\RequestBody;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidHeaderNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidOpenIdConnectUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\InvalidSchemeException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidValueException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\Schema\AnySchema;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\BooleanSchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpBasicSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpJwtSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\HttpSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\OpenIdConnectSecurityScheme;
use PHPUnit\Framework\TestCase;

class ComponentsTest extends TestCase
{

    /**
     * @dataProvider provideSetSchema
     *
     * @param string         $name
     * @param AbstractSchema $schema
     * @param string|null    $exception
     *
     * @throws InvalidNameException
     */
    public function testSetSchema(string $name, AbstractSchema $schema, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setSchema($name, $schema);
        $all = $components->getSchemas();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(AbstractSchema::class, $all[$name]);
        $this->assertArrayHasKey('schemas', $array);
        $this->assertArrayHasKey($name, $array['schemas']);
        $ref = '#/components/schemas/' . $name;
        $this->assertSame($ref, $schema->getRef());
        $arr = $schema->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveSchema
     *
     * @param AbstractSchema[] $add
     * @param string[]         $remove
     * @param string[]         $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveSchema(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setSchema($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeSchema($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getSchemas();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('schemas', $array);
            $this->assertCount($count, $array['schemas']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('schemas', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(AbstractSchema::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['schemas']);
            $this->assertSame('#/components/schemas/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetResponse
     *
     * @param string      $name
     * @param Response    $response
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testSetResponse(string $name, Response $response, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setResponse($name, $response);
        $all = $components->getResponses();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(Response::class, $all[$name]);
        $this->assertArrayHasKey('responses', $array);
        $this->assertArrayHasKey($name, $array['responses']);
        $ref = '#/components/responses/' . $name;
        $this->assertSame($ref, $response->getRef());
        $arr = $response->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveResponse
     *
     * @param Response[] $add
     * @param string[]   $remove
     * @param string[]   $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveResponse(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setResponse($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeResponse($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getResponses();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('responses', $array);
            $this->assertCount($count, $array['responses']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('responses', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(Response::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['responses']);
            $this->assertSame('#/components/responses/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetParameter
     *
     * @param string      $name
     * @param Parameter   $parameter
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testSetParameter(string $name, Parameter $parameter, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setParameter($name, $parameter);
        $all = $components->getParameters();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(Parameter::class, $all[$name]);
        $this->assertArrayHasKey('parameters', $array);
        $this->assertArrayHasKey($name, $array['parameters']);
        $ref = '#/components/parameters/' . $name;
        $this->assertSame($ref, $parameter->getRef());
        $arr = $parameter->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveParameter
     *
     * @param Parameter[] $add
     * @param string[]    $remove
     * @param string[]    $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveParameter(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setParameter($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeParameter($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getParameters();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('parameters', $array);
            $this->assertCount($count, $array['parameters']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('parameters', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(Parameter::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['parameters']);
            $this->assertSame('#/components/parameters/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetExample
     *
     * @param string          $name
     * @param AbstractExample $example
     * @param string|null     $exception
     *
     * @throws InvalidNameException
     */
    public function testSetExample(string $name, AbstractExample $example, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setExample($name, $example);
        $all = $components->getExamples();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(AbstractExample::class, $all[$name]);
        $this->assertArrayHasKey('examples', $array);
        $this->assertArrayHasKey($name, $array['examples']);
        $ref = '#/components/examples/' . $name;
        $this->assertSame($ref, $example->getRef());
        $arr = $example->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveExample
     *
     * @param AbstractExample[] $add
     * @param string[]          $remove
     * @param string[]          $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveExample(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setExample($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeExample($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getExamples();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('examples', $array);
            $this->assertCount($count, $array['examples']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('examples', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(AbstractExample::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['examples']);
            $this->assertSame('#/components/examples/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetRequestBody
     *
     * @param string      $name
     * @param RequestBody $requestBody
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testSetRequestBody(string $name, RequestBody $requestBody, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setRequestBody($name, $requestBody);
        $all = $components->getRequestBodies();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(RequestBody::class, $all[$name]);
        $this->assertArrayHasKey('requestBodies', $array);
        $this->assertArrayHasKey($name, $array['requestBodies']);
        $ref = '#/components/requestBodies/' . $name;
        $this->assertSame($ref, $requestBody->getRef());
        $arr = $requestBody->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveRequestBody
     *
     * @param RequestBody[] $add
     * @param string[]      $remove
     * @param string[]      $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveRequestBody(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setRequestBody($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeRequestBody($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getRequestBodies();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('requestBodies', $array);
            $this->assertCount($count, $array['requestBodies']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('requestBodies', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(RequestBody::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['requestBodies']);
            $this->assertSame('#/components/requestBodies/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetHeader
     *
     * @param string      $name
     * @param Header      $header
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testSetHeader(string $name, Header $header, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setHeader($name, $header);
        $all = $components->getHeaders();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(Header::class, $all[$name]);
        $this->assertArrayHasKey('headers', $array);
        $this->assertArrayHasKey($name, $array['headers']);
        $ref = '#/components/headers/' . $name;
        $this->assertSame($ref, $header->getRef());
        $arr = $header->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveHeader
     *
     * @param Header[] $add
     * @param string[] $remove
     * @param string[] $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveHeader(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setHeader($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeHeader($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getHeaders();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('headers', $array);
            $this->assertCount($count, $array['headers']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('headers', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(Header::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['headers']);
            $this->assertSame('#/components/headers/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetSecurityScheme
     *
     * @param string                 $name
     * @param AbstractSecurityScheme $securityScheme
     * @param string|null            $exception
     *
     * @throws InvalidNameException
     */
    public function testSetSecurityScheme(string $name, AbstractSecurityScheme $securityScheme, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setSecurityScheme($name, $securityScheme);
        $all = $components->getSecuritySchemes();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(AbstractSecurityScheme::class, $all[$name]);
        $this->assertArrayHasKey('securitySchemes', $array);
        $this->assertArrayHasKey($name, $array['securitySchemes']);
        $ref = '#/components/securitySchemes/' . $name;
        $this->assertSame($ref, $securityScheme->getRef());
        $arr = $securityScheme->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveSecurityScheme
     *
     * @param AbstractSecurityScheme[] $add
     * @param string[]                 $remove
     * @param string[]                 $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveSecurityScheme(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setSecurityScheme($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeSecurityScheme($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getSecuritySchemes();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('securitySchemes', $array);
            $this->assertCount($count, $array['securitySchemes']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('securitySchemes', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(AbstractSecurityScheme::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['securitySchemes']);
            $this->assertSame('#/components/securitySchemes/' . $name, $add[$name]->getRef());
        }
    }

    /**
     * @dataProvider provideSetCallback
     *
     * @param string                 $name
     * @param CallbackRequest $callback
     * @param string|null            $exception
     *
     * @throws InvalidNameException
     */
    public function testSetCallback(string $name, CallbackRequest $callback, ?string $exception)
    {
        $components = $this->getComponents();
        if ($exception) {
            $this->expectException($exception);
        }
        $components->setCallback($name, $callback);
        $all = $components->getCallbacks();
        $array = $components->toArray();
        $name = trim($name);
        $this->assertArrayHasKey($name, $all);
        $this->assertInstanceOf(CallbackRequest::class, $all[$name]);
        $this->assertArrayHasKey('callbacks', $array);
        $this->assertArrayHasKey($name, $array['callbacks']);
        $ref = '#/components/callbacks/' . $name;
        $this->assertSame($ref, $callback->getRef());
        $arr = $callback->toArray(true);
        $this->assertArrayHasKey('$ref', $arr);
        $this->assertSame($ref, $arr['$ref']);
    }

    /**
     * @dataProvider provideRemoveCallback
     *
     * @param CallbackRequest[] $add
     * @param string[]                 $remove
     * @param string[]                 $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveCallback(array $add, array $remove, array $check)
    {
        $components = $this->getComponents();
        foreach ($add as $name => $schema) {
            $components->setCallback($name, $schema);
        }
        foreach ($remove as $name) {
            $components->removeCallback($name);
            if (array_key_exists($name, $add)) {
                $this->assertNull($add[$name]->getRef());
                $arr = $add[$name]->toArray(true);
                $this->assertArrayNotHasKey('$ref', $arr);
            }
        }
        $all = $components->getCallbacks();
        $array = $components->toArray();
        $count = count($check);
        if ($count) {
            $this->assertCount($count, $all);
            $this->assertArrayHasKey('callbacks', $array);
            $this->assertCount($count, $array['callbacks']);
            $this->assertFalse($components->isEmpty());
        } else {
            $this->assertArrayNotHasKey('callbacks', $array);
            $this->assertTrue($components->isEmpty());
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $all);
            $this->assertInstanceOf(CallbackRequest::class, $all[$name]);
            $this->assertArrayHasKey($name, $array['callbacks']);
            $this->assertSame('#/components/callbacks/' . $name, $add[$name]->getRef());
        }
    }

    public function setIsEmpty()
    {
        $components = $this->getComponents();
        $this->assertTrue($components->isEmpty());
    }

    public function provideSetSchema(): array
    {
        $schema = new BooleanSchema();
        return [
            ['schema-1',   clone $schema, null],
            [' schema-1 ', clone $schema, null],
            [' ',          clone $schema, InvalidNameException::class],
            ['',           clone $schema, InvalidNameException::class],
        ];
    }

    public function provideRemoveSchema(): array
    {
        $s1 = new BooleanSchema();
        $s2 = new BooleanSchema();
        $s3 = new BooleanSchema();
        return [
            [['s1' => $s1, 's2' => $s2, 's3' => $s3], [],           ['s1', 's2', 's3']],
            [[],                                      ['s1', 's1'], []],
            [['s1' => $s1, 's2' => $s2, 's3' => $s3], ['s2', 's3'], ['s1']],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function provideSetResponse(): array
    {
        $response = new Response('text/plain', new StringSchema());
        return [
            ['response-1',   clone $response, null],
            [' response-1 ', clone $response, null],
            [' ',            clone $response, InvalidNameException::class],
            ['',             clone $response, InvalidNameException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidPatternException
     */
    public function provideRemoveResponse(): array
    {
        $r1 = new Response('text/plain', new StringSchema());
        $r2 = new Response('text/html', new StringSchema());
        $r3 = new Response('application/json', new StringSchema());
        return [
            [['r1' => $r1, 'r2' => $r2, 'r3' => $r3], [],           ['r1', 'r2', 'r3']],
            [[],                                      ['r1', 'r1'], []],
            [['r1' => $r1, 'r2' => $r2, 'r3' => $r3], ['r2', 'r3'], ['r1']],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidNameException
     * @throws InvalidHeaderNameException
     */
    public function provideSetParameter(): array
    {
        $parameter = new Parameter(Parameter::IN_QUERY, 'page');
        return [
            ['parameter-1',   clone $parameter, null],
            [' parameter-1 ', clone $parameter, null],
            ['  ',            clone $parameter, InvalidNameException::class],
            ['',              clone $parameter, InvalidNameException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function provideRemoveParameter(): array
    {
        $p1 = new Parameter(Parameter::IN_QUERY, 'page');
        $p2 = new Parameter(Parameter::IN_COOKIE, 'sid');
        $p3 = new Parameter(Parameter::IN_PATH, 'id');
        return [
            [['p1' => $p1, 'p2' => $p2, 'p3' => $p3], [],           ['p1', 'p2', 'p3']],
            [[],                                      ['p1', 'p1'], []],
            [['p1' => $p1, 'p2' => $p2, 'p3' => $p3], ['p2', 'p3'], ['p1']],
        ];
    }

    /**
     * @return array
     *
     * @throws InvalidValueException
     */
    public function provideSetExample(): array
    {
        return [
            ['example-1',   new Example(['success' => true]),             null],
            [' example-1 ', new ExternalExample('https://goo.gl/qwerty'), null],
            ['  ',          new Example(['success' => true]),             InvalidNameException::class],
            ['',            new Example(['success' => true]),             InvalidNameException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws InvalidValueException
     */
    public function provideRemoveExample(): array
    {
        $e1 = new Example(['success' => true]);
        $e2 = new Example(['success' => false]);
        $e3 = new ExternalExample('https://goo.gl/qwerty');
        return [
            [['e1' => $e1, 'e2' => $e2, 'e3' => $e3], [],           ['e1', 'e2', 'e3']],
            [[],                                      ['e1', 'e1'], []],
            [['e1' => $e1, 'e2' => $e2, 'e3' => $e3], ['e2', 'e3'], ['e1']],
        ];
    }

    /**
     * @return array
     *
     * @throws InvalidContentTypeException
     * @throws ArgumentOutOfListException
     */
    public function provideSetRequestBody(): array
    {
        $requestBody = new RequestBody('application/json', new AnySchema());
        return [
            ['request-1',   clone $requestBody, null],
            [' request-1 ', clone $requestBody, null],
            ['  ',          clone $requestBody, InvalidNameException::class],
            ['',            clone $requestBody, InvalidNameException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     */
    public function provideRemoveRequestBody(): array
    {
        $b1 = new RequestBody('application/json', new AnySchema());
        $b2 = new RequestBody('application/json', new AnySchema());
        $b3 = new RequestBody('application/json', new AnySchema());
        return [
            [['b1' => $b1, 'b2' => $b2, 'b3' => $b3], [],           ['b1', 'b2', 'b3']],
            [[],                                      ['b1', 'b1'], []],
            [['b1' => $b1, 'b2' => $b2, 'b3' => $b3], ['b2', 'b3'], ['b1']],
        ];
    }

    /**
     * @return array
     */
    public function provideSetHeader(): array
    {
        $header = new Header();
        return [
            ['header-1',   clone $header, null],
            [' header-1 ', clone $header, null],
            ['  ',         clone $header, InvalidNameException::class],
            ['',           clone $header, InvalidNameException::class],
        ];
    }

    /**
     * @return array
     */
    public function provideRemoveHeader(): array
    {
        $h1 = new Header();
        $h2 = new Header();
        $h3 = new Header();
        return [
            [['h1' => $h1, 'h2' => $h2, 'h3' => $h3], [],           ['h1', 'h2', 'h3']],
            [[],                                      ['h1', 'h1'], []],
            [['h1' => $h1, 'h2' => $h2, 'h3' => $h3], ['h2', 'h3'], ['h1']],
        ];
    }

    /**
     * @return array
     * @throws InvalidSchemeException
     * @throws InvalidOpenIdConnectUrlException
     */
    public function provideSetSecurityScheme(): array
    {
        return [
            ['header-1',   new HttpBasicSecurityScheme(),                                null],
            [' header-1 ', new OpenIdConnectSecurityScheme('http://openid.exmaple.com'), null],
            ['  ',         new HttpJwtSecurityScheme(),                                  InvalidNameException::class],
            ['',           new HttpJwtSecurityScheme(),                                  InvalidNameException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws InvalidOpenIdConnectUrlException
     * @throws InvalidSchemeException
     */
    public function provideRemoveSecurityScheme(): array
    {
        $s1 = new HttpBasicSecurityScheme();
        $s2 = new OpenIdConnectSecurityScheme('http://openid.exmaple.com');
        $s3 = new HttpJwtSecurityScheme();
        return [
            [['s1' => $s1, 's2' => $s2, 's3' => $s3], [],           ['s1', 's2', 's3']],
            [[],                                      ['s1', 's1'], []],
            [['s1' => $s1, 's2' => $s2, 's3' => $s3], ['s2', 's3'], ['s1']],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidUrlException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function provideSetCallback(): array
    {
        $response = new Response('application/json', new ArraySchema(new IntegerSchema()));
        $operation = new Operation(new Responses($response), null);
        $callback = new CallbackRequest('/callback/{id}', 'POST', $operation);
        return [
            ['callback-1',   clone $callback, null],
            [' callback-1 ', clone $callback, null],
            ['  ',           clone $callback, InvalidNameException::class],
            ['',             clone $callback, InvalidNameException::class],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidContentTypeException
     * @throws InvalidUrlException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function provideRemoveCallback(): array
    {
        $response = new Response('application/json', new ArraySchema(new IntegerSchema()));
        $operation = new Operation(new Responses($response), null);
        $callback = new CallbackRequest('/callback/{id}', 'POST', $operation);
        $c1 = clone $callback;
        $c2 = clone $callback;
        $c3 = clone $callback;
        return [
            [['c1' => $c1, 'c2' => $c2, 'c3' => $c3], [],           ['c1', 'c2', 'c3']],
            [[],                                      ['c1', 'c1'], []],
            [['c1' => $c1, 'c2' => $c2, 'c3' => $c3], ['c2', 'c3'], ['c1']],
        ];
    }

    private function getComponents(): Components
    {
        return new Components();
    }
}
