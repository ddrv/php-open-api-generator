<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\Server;
use Ddrv\OpenApiGenerator\Document\ServerVariable;
use Ddrv\OpenApiGenerator\Exception\InvalidUrlException;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $url
     * @param string|null $description
     * @param string[]    $variables
     * @param int         $count
     * @param string|null $exception
     *
     * @throws InvalidUrlException
     */
    public function testConstruct(string $url, ?string $description, array $variables, int $count, ?string $exception)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $server = new Server($url, $description);
        $array = $server->toArray();
        $this->assertCount($count, $array);
        $this->checkUrlAndDefaultVariables($server, $url, $variables);
        $this->checkDescription($server, $description);
    }

    /**
     * @dataProvider provideSetUrl
     *
     * @param string      $url
     * @param string[]    $variables
     * @param string|null $exception
     *
     * @throws InvalidUrlException
     */
    public function testSetUrl(string $url, array $variables, ?string $exception)
    {
        $server = new Server('http://{test_user}:{test_password}@localhost');
        if ($exception) {
            $this->expectException($exception);
        }
        $server->setUrl($url);
        $this->checkUrlAndDefaultVariables($server, $url, $variables);
    }

    /**
     * @dataProvider provideSetDescription
     *
     * @param string|null $description
     *
     * @throws InvalidUrlException
     */
    public function testSetDescription(?string $description)
    {
        $server = new Server('http://{test_user}:{test_password}@localhost', 'default description');
        $server->setDescription($description);
        $this->checkDescription($server, $description);
    }

    /**
     * @dataProvider provideSetVariable
     *
     * @param string           $url
     * @param ServerVariable[] $variables
     * @param string[]         $check
     *
     * @throws InvalidUrlException
     */
    public function testSetVariable(string $url, array $variables, array $check)
    {
        $server = new Server($url);
        foreach ($variables as $name => $variable) {
            $server->setVariable($name, $variable);
        }
        $array = $server->toArray();
        if ($check) {
            $this->assertArrayHasKey('variables', $array);
            $this->assertCount(count($check), $array['variables']);
        } else {
            $this->assertArrayNotHasKey('variables', $array);
        }
        foreach ($check as $name) {
            $this->assertArrayHasKey($name, $server->getVariables());
            $this->assertArrayHasKey($name, $array['variables']);
        }
    }

    public function provideConstruct(): array
    {
        return [
            ['http://localhost',                 'Description',    [],                   2, null],
            [' http://localhost ',               '              ', [],                   1, null],
            ['http://{user}:{password}@test.io', 'Test instance',  ['user', 'password'], 3, null],
            ['http://test.io/v{ver}/?ver={ver}',  null,            ['ver'],              2, null],
            ['',                                  null,            [],                   0, InvalidUrlException::class],
            ['                                 ', null,            [],                   0, InvalidUrlException::class],
        ];
    }

    public function provideSetUrl(): array
    {
        return [
            ['http://localhost',                             [],                   null],
            [' http://localhost ',                           [],                   null],
            ['http://{user}:{password}@test.io',             ['user', 'password'], null],
            ['http://test.io/v{version}/?version={version}', ['version'],          null],
            ['',                                             [],                   InvalidUrlException::class],
            ['                                            ', [],                   InvalidUrlException::class],
        ];
    }

    public function provideSetDescription(): array
    {
        return [
            [null],
            [''],
            [' '],
            ['description'],
            [' description '],
        ];
    }

    public function provideSetVariable(): array
    {
        $user = new ServerVariable('admin', 'User\'s subdomain');
        $version = new ServerVariable('1.0', 'API version');
        $version->addVariant('2.0')->addVariant('2.1')->addVariant('3.0');
        $unused = new ServerVariable('unused');
        return [
            ['http://{user}.host.io/v{ver}', ['user' => $user, 'ver' => $version],     ['user', 'ver']],
            ['http://host.io/v1',            ['unused' => $unused],                    []],
            ['http://host.io/v{ver}',        ['unused' => $unused, 'ver' => $version], ['ver']],
            ['http://host.io/v{ver}',        [],                                       ['ver']],
        ];
    }

    private function checkDescription(Server $server, ?string $description)
    {
        $description = trim((string)$description);
        if (!$description) {
            $description = null;
        }
        $array = $server->toArray();
        $this->assertSame($description, $server->getDescription());
        if ($description) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($description, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
    }

    private function checkUrlAndDefaultVariables(Server $server, string $url, array $variables)
    {
        $url = trim($url);
        $this->assertSame($url, $server->getUrl());
        $array = $server->toArray();
        $this->assertCount(count($variables), $server->getVariables());
        if ($variables) {
            $this->assertArrayHasKey('variables', $array);
            $this->assertCount(count($variables), $array['variables']);
        } else {
            $this->assertArrayNotHasKey('variables', $array);
        }
        $this->assertArrayHasKey('url', $array);
        $this->assertSame($url, $array['url']);
        foreach ($variables as $variable) {
            $this->assertArrayHasKey($variable, $server->getVariables());
            $this->assertArrayHasKey($variable, $array['variables']);
        }
    }
}
