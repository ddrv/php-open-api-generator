<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit;

use Ddrv\OpenApiGenerator\Document\Components;
use Ddrv\OpenApiGenerator\Document\ExternalDocs;
use Ddrv\OpenApiGenerator\Document\Header;
use Ddrv\OpenApiGenerator\Document\Info;
use Ddrv\OpenApiGenerator\Document\Operation;
use Ddrv\OpenApiGenerator\Document\Parameter;
use Ddrv\OpenApiGenerator\Document\PathItem;
use Ddrv\OpenApiGenerator\Document\RequestBody;
use Ddrv\OpenApiGenerator\Document\Response;
use Ddrv\OpenApiGenerator\Document\Responses;
use Ddrv\OpenApiGenerator\Document\Security;
use Ddrv\OpenApiGenerator\Document\Server;
use Ddrv\OpenApiGenerator\Document\Tag;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Exception\InvalidHeaderNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidHttpStatusException;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\OpenApi;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\BooleanSchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\ObjectSchema;
use Ddrv\OpenApiGenerator\Schema\ObjectSchemaProperty;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use PHPUnit\Framework\TestCase;

class OpenApiTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     * @param Info $info
     */
    public function testConstruct(Info $info)
    {
        $openApi = new OpenApi($info);
        $this->checkInfo($openApi, $info);
        $this->checkComponents($openApi, new Components());
        $array = $openApi->toArray();
        $this->assertArrayHasKey('openapi', $array);
        $this->assertSame('3.0.2', $array['openapi']);
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param Info $info
     */
    public function testSetInfo(Info $info)
    {
        $openApi = $this->getOpenApi();
        $openApi->setInfo($info);
        $this->checkInfo($openApi, $info);
    }
    /**
     * @dataProvider provideSetComponents
     *
     * @param Components $components
     */
    public function testSetComponents(Components $components)
    {
        $openApi = $this->getOpenApi();
        $openApi->setComponents($components);
        $this->checkComponents($openApi, $components);
    }

    /**
     * @dataProvider provideSetExternalDocs
     *
     * @param ExternalDocs|null $externalDocs
     */
    public function testSetExternalDocs(?ExternalDocs $externalDocs)
    {
        $openApi = $this->getOpenApi();
        $openApi->setExternalDocs($externalDocs);
        $array = $openApi->toArray();
        if ($externalDocs) {
            $this->assertArrayHasKey('externalDocs', $array);
            $this->assertInstanceOf(ExternalDocs::class, $openApi->getExternalDocs());
        } else {
            $this->assertArrayNotHasKey('externalDocs', $array);
            $this->assertNull($openApi->getExternalDocs());
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
        $openapi = $this->getOpenApi();
        foreach ($add as $server) {
            $openapi->addServer($server);
        }
        $count = count($check);
        $array = $openapi->toArray();
        $this->assertCount($count, $openapi->getServers());
        $this->assertArrayHasKey('servers', $array);
        $this->assertCount($count, $array['servers']);

        foreach ($check as $server) {
            $isOk = false;
            foreach ($openapi->getServers() as $p) {
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
     * @param string[] $remove
     * @param Server[] $check
     */
    public function testRemoveServer(array $add, array $remove, array $check)
    {
        $openapi = $this->getOpenApi();
        foreach ($add as $server) {
            $openapi->addServer($server);
        }
        $count = count($check);
        foreach ($remove as $url) {
            $openapi->removeServer($url);
        }
        $array = $openapi->toArray();
        if (!$count) {
            $this->assertArrayNotHasKey('servers', $array);
        } else {
            $this->assertArrayHasKey('servers', $array);
            $this->assertCount($count, $array['servers']);
        }
        $this->assertCount($count, $openapi->getServers());

        foreach ($check as $server) {
            $isOk = false;
            foreach ($openapi->getServers() as $p) {
                if ($p->getHash() === $server->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @dataProvider provideTags
     *
     * @param Tag[]    $add
     * @param string[] $remove
     * @param Tag[]    $check
     */
    public function testTag(array $add, array $remove, array $check)
    {
        $openApi = $this->getOpenApi();
        $added = [];
        foreach ($add as $tag) {
            $added[$tag->getName()] = $tag;
            $openApi->addTag($tag);
        }
        $this->checkTags($openApi, $added);
        foreach ($remove as $name) {
            $openApi->removeTag($name);
        }
        $this->checkTags($openApi, $check);
    }

    /**
     * @dataProvider provideSecurity
     *
     * @param Security[] $add
     * @param string[]   $remove
     * @param Security[] $check
     */
    public function testSecurity(array $add, array $remove, array $check)
    {
        $openApi = $this->getOpenApi();
        $added = [];
        foreach ($add as $security) {
            $added[$security->getName()] = $security;
            $openApi->addSecurity($security);
        }
        $this->checkSecurity($openApi, $added);
        foreach ($remove as $name) {
            $openApi->removeSecurity($name);
        }
        $this->checkSecurity($openApi, $check);
    }

    /**
     * @dataProvider providePathItems
     *
     * @param PathItem[] $add
     * @param string[]   $remove
     * @param string[]   $check
     */
    public function testPathItem(array $add, array $remove, array $check)
    {
        $openApi = $this->getOpenApi();
        $added = [];
        foreach ($add as $pathItem) {
            $openApi->addPath($pathItem);
            $added[$pathItem->getPath()] = $pathItem->getPath();
        }
        $this->checkPathItems($openApi, $added);
        foreach ($remove as $path) {
            $openApi->removePath($path);
        }
        $this->checkPathItems($openApi, $check);
    }

    public function provideConstruct(): array
    {
        return [
            [new Info('app', '1.0.0')],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidNameException
     */
    public function provideSetComponents(): array
    {
        return [
            [new Components()],
            [(new Components())->setSchema('bool', new BooleanSchema())],
        ];
    }

    public function provideSetExternalDocs(): array
    {
        return [
            [null],
            [new ExternalDocs('http://docs.example.com')],
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
        $s1 = new Server('https://srv-01');
        $s2 = new Server('https://srv-02');
        $r1 = new Server('https://srv-01', 'removed');
        $r2 = new Server('https://srv-02', 'removed');
        return [
            [[$s1, $s2],           ['https://srv-01', 'https://srv-02'], []],
            [[$s1, $s2],           ['https://srv-01', 'https://srv-02'], []],
            [[$s1, $s2],           ['https://srv-02'],                   [$s1]],
            [[$s1, $s2, $r1, $r2], ['https://srv-02'],                   [$r1]],

        ];
    }

    public function provideTags(): array
    {
        $t1 = new Tag('general');
        $t2 = new Tag('users');
        $t3 = new Tag('general', 'replaced');
        return [
            [[$t1],           [],                   [$t1]],
            [[$t1, $t2],      [],                   [$t1, $t2]],
            [[$t1, $t2, $t3], [],                   [$t2, $t3]],
            [[$t1, $t2, $t3], ['general'],          [$t2]],
            [[$t1, $t2],      ['users'],            [$t1]],
            [[$t1, $t2],      ['general', 'users'], []],
        ];
    }

    public function provideSecurity(): array
    {
        $s1 = (new Security('s1'))->addScope('test:exec')->addScope('test:read');
        $s2 = (new Security('s2'))->addScope('test:exec');
        $s3 = (new Security('s1'))->addScope('test:write');
        return [
            [[$s1],           [],           [$s1]],
            [[$s1, $s2],      [],           [$s1, $s2]],
            [[$s1, $s2, $s3], [],           [$s2, $s3]],
            [[$s1, $s2, $s3], ['s1'],       [$s2]],
            [[$s1, $s2],      ['s2'],       [$s1]],
            [[$s1, $s2],      ['s1', 's2'], []],
        ];
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidNameException
     * @throws InvalidContentTypeException
     * @throws InvalidHeaderNameException
     * @throws InvalidHttpStatusException
     * @throws InvalidPatternException
     * @throws InvalidUrlException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function providePathItems()
    {
        $id = (new IntegerSchema())->setMinimum(1);
        $error = new ObjectSchema();
        $error->setProperty((new IntegerSchema())->setMinimum(-100)->setMaximum(100), 'code');
        $error->setProperty(new StringSchema(), 'message');
        $ro = ObjectSchemaProperty::ACCESS_READ_ONLY;
        $user = new ObjectSchema();
        $user->setProperty($id, 'id', $ro);
        $user->setProperty(new StringSchema(StringSchema::FORMAT_EMAIL), 'email', null, true);
        $user->setProperty(new StringSchema(), 'password', ObjectSchemaProperty::ACCESS_WRITE_ONLY, true);
        $user->setProperty(new StringSchema(), 'name', null, true);
        $user->setProperty(new StringSchema(StringSchema::FORMAT_DATE_TIME), 'created_at', $ro);
        $users = (new ArraySchema($user))->setUniqueItems(true);
        $errorResponse = (new Response('application/json', $error))->setDescription('Error');
        $listResponse = (new Response('application/json', $users))->setDescription('Users list');
        $itemResponse = (new Response('application/json', $user))->setDescription('User entity');
        $newItemResponse = clone ($itemResponse);
        $url = new StringSchema(StringSchema::FORMAT_URI);
        $location = (new Header())->setSchema($url)->setDescription('Link to new user');
        $newItemResponse->setDescription('Created user')->addHeader('Location', $location);
        $emptyResponse = (new Response('text/plain', new StringSchema()))->setDescription('No content');
        $responses = new Responses($errorResponse);
        $rsp1 = clone $responses;
        $rsp1->setResponse(200, $listResponse);

        $rsp2 = clone $responses;
        $rsp2->setResponse(201, $newItemResponse);

        $rsp3 = clone $responses;
        $rsp3->setResponse(200, $itemResponse);

        $rsp4 = clone $responses;
        $rsp4->setResponse(200, $itemResponse);

        $rsp5 = clone $responses;
        $rsp5->setResponse(204, $emptyResponse);

        $body = new RequestBody('application/json', $user);

        $path1 = new PathItem('/users', 'GET', new Operation($rsp1, null));
        $path1->setOperation('POST', new Operation($rsp2, $body));
        $path2 = new PathItem('/users/{id}', 'GET', new Operation($rsp3, null));

        $idParam = (new Parameter(Parameter::IN_PATH, 'id'))->setSchema($id);
        $path2
            ->setOperation('PUT', new Operation($rsp4, $body))
            ->setOperation('DELETE', new Operation($rsp5, null))
            ->addParameter($idParam)
        ;
        return [
            [[$path1],         [],                            ['/users']],
            [[$path1, $path2], ['/users'],                    ['/users/{id}']],
            [[$path1, $path2], ['/users', '/users/{id}'],     []],
            [[$path1, $path2], ['/undefined', '/users/{id}'], ['/users']],
        ];
    }

    private function checkInfo(OpenApi $openApi, Info $info)
    {
        $this->assertSame($info->getHash(), $openApi->getInfo()->getHash());
        $array = $openApi->toArray();
        $this->assertArrayHasKey('info', $array);
    }

    private function checkComponents(OpenApi $openApi, Components $components)
    {
        $this->assertSame($components->getHash(), $openApi->getComponents()->getHash());
        $array = $openApi->toArray();
        if ($components->isEmpty()) {
            $this->assertArrayNotHasKey('components', $array);
        } else {
            $this->assertArrayHasKey('components', $array);
        }
    }

    private function checkPathItems(OpenApi $openApi, array $check)
    {
        $count = count($check);
        $pathItems = $openApi->getPaths();
        $this->assertCount($count, $pathItems);
        foreach ($pathItems as $pathItem) {
            $this->assertInstanceOf(PathItem::class, $pathItem);
            $this->assertContains($pathItem->getPath(), $check);
        }
        $array = $openApi->toArray();
        $this->assertArrayHasKey('paths', $array);
        $this->assertCount($count, $array['paths']);
        foreach ($check as $path) {
            $this->assertArrayHasKey($path, $array['paths']);
        }
    }

    /**
     * @param OpenApi $openApi
     * @param Tag[] $check
     */
    private function checkTags(OpenApi $openApi, array $check)
    {
        $count = count($check);
        $array = $openApi->toArray();
        $this->assertCount($count, $openApi->getTags());
        if ($count) {
            $this->assertArrayHasKey('tags', $array);
            $this->assertCount($count, $array['tags']);
        } else {
            $this->assertArrayNotHasKey('tags', $array);
        }
        foreach ($check as $tag) {
            $isOk = false;
            foreach ($openApi->getTags() as $t) {
                if ($t->getHash() === $tag->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    /**
     * @param OpenApi    $openApi
     * @param Security[] $check
     */
    private function checkSecurity(OpenApi $openApi, array $check)
    {
        $count = count($check);
        $array = $openApi->toArray();
        $this->assertCount($count, $openApi->getSecurity());
        if ($count) {
            $this->assertArrayHasKey('security', $array);
            $this->assertCount($count, $array['security']);
        } else {
            $this->assertArrayNotHasKey('security', $array);
        }
        foreach ($check as $security) {
            $isOk = false;
            foreach ($openApi->getSecurity() as $t) {
                if ($t->getHash() === $security->getHash()) {
                    $isOk = true;
                }
            }
            $this->assertTrue($isOk);
        }
    }

    private function getOpenApi(): OpenApi
    {
        return new OpenApi(new Info('open-api-generator', '1.0.0'));
    }
}
