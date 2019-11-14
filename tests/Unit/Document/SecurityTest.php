<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\Security;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $name
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testConstruct(string $name, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $security = new Security($name);

        $check = trim($name);
        $this->assertSame($check, $security->getName());
        $this->assertCount(0, $security->getScopes());
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $name
     * @param string|null $exception
     *
     * @throws InvalidNameException
     */
    public function testSetName(string $name, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $security = new Security($name);
        $security->setName($name);
        $check = trim($name);
        $this->assertSame($check, $security->getName());
    }

    /**
     * @dataProvider provideAddScope
     *
     * @param string[] $scopes
     * @param string[] $check
     *
     * @throws InvalidNameException
     */
    public function testAddScope(array $scopes, array $check)
    {
        $security = new Security('test');
        foreach ($scopes as $scope) {
            $security->addScope($scope);
        }
        $this->assertCount(count($check), $security->getScopes());
        foreach ($check as $item) {
            $this->assertContains($item, $security->getScopes());
        }
    }

    /**
     * @dataProvider provideRemoveScope
     *
     * @param string[] $add
     * @param string[] $remove
     * @param string[] $check
     *
     * @throws InvalidNameException
     */
    public function testRemoveScope(array $add, array $remove, array $check)
    {
        $security = new Security('test');
        foreach ($add as $scope) {
            $security->addScope($scope);
        }
        foreach ($remove as $scope) {
            $security->removeScope($scope);
        }
        $this->assertCount(count($check), $security->getScopes());
        foreach ($check as $item) {
            $this->assertContains($item, $security->getScopes());
        }
    }

    public function provideConstruct(): array
    {
        return [
            ['PHPUnit', null],
            [' Test  ', null],
            [' te-st ', null],
            [' Te st ', InvalidNameException::class],
            ['       ', InvalidNameException::class],
        ];
    }

    public function provideAddScope(): array
    {
        return [
            [['scope1', 'scope2'],   ['scope1', 'scope2']],
            [['scope1', 'scope1'],   ['scope1']],
            [['scope1', ' scope1 '], ['scope1']],
            [['scope1', '        '], ['scope1']],
            [['scope1', ''],         ['scope1']],
        ];
    }

    public function provideRemoveScope(): array
    {
        return [
            [['scope1', 'scope2', 'scope3'], ['scope2'],                     ['scope1', 'scope3']],
            [['scope1', 'scope2', 'scope3'], ['scope1', 'scope2', 'scope3'], []],
            [['scope1', 'scope2', 'scope3'], [' scope1                   '], ['scope2', 'scope3']],
            [['scope1', 'scope2', 'scope3'], [' scope1 ', 'scope1', '', ''], ['scope2', 'scope3']],
        ];
    }
}
