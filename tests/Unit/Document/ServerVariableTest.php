<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\ServerVariable;
use PHPUnit\Framework\TestCase;

class ServerVariableTest extends TestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string|null $default
     * @param string|null $description
     */
    public function testConstruct(?string $default, ?string $description)
    {
        $serverVariable = new ServerVariable($default, $description);
        $count = 2;
        $description = trim((string)$description);
        if (!$description) {
            $description = null;
            $count--;
        }

        $this->assertSame($default, $serverVariable->getDefault());
        $this->assertCount(0, $serverVariable->getEnum());
        $array = $serverVariable->toArray();
        $this->assertCount($count, $array);
        $this->assertArrayHasKey('default', $array);
        $this->assertSame($default, $array['default']);
        if ($description) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($description, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param string|null $default
     * @param string|null $description
     */
    public function testSetDefault(?string $default, ?string $description)
    {
        unset($description);
        $serverVariable = new ServerVariable('value');
        $serverVariable->setDefault($default);
        $this->assertSame($default, $serverVariable->getDefault());
        $array = $serverVariable->toArray();
        $this->assertArrayHasKey('default', $array);
        $this->assertSame($default, $array['default']);
    }

    /**
     * @dataProvider provideDescription
     *
     * @param string|null $description
     */
    public function testSetDescription(?string $description)
    {
        $serverVariable = new ServerVariable('value');
        $check = trim((string)$description);
        if (!$check) {
            $check = null;
        }
        $serverVariable->setDescription($description);
        $this->assertSame($check, $serverVariable->getDescription());
        $array = $serverVariable->toArray();
        if ($check) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($check, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
    }

    /**
     * @dataProvider provideAddVariant
     *
     * @param string|null $default
     * @param string[]    $variants
     * @param string[]    $check
     */
    public function testAddVariant(?string $default, array $variants, array $check)
    {
        $serverVariable = new ServerVariable($default);
        foreach ($variants as $variant) {
            $serverVariable->addVariant($variant);
        }
        $this->checkEnum($serverVariable, $check);
    }

    /**
     * @dataProvider provideRemoveVariant
     *
     * @param string|null $default
     * @param string[]    $add
     * @param string[]    $remove
     * @param string[]    $check
     */
    public function testRemoveVariant(?string $default, array $add, array $remove, array $check)
    {
        $serverVariable = new ServerVariable($default);
        foreach ($add as $variant) {
            $serverVariable->addVariant($variant);
        }
        foreach ($remove as $variant) {
            $serverVariable->removeVariant($variant);
        }
        $this->checkEnum($serverVariable, $check);
    }

    private function checkEnum(ServerVariable $serverVariable, array $check)
    {
        $count = count($check);
        $array = $serverVariable->toArray();
        $this->assertCount($count, $serverVariable->getEnum());
        if ($count) {
            $this->assertCount($count, $array['enum']);
        } else {
            $this->assertArrayNotHasKey('enum', $array);
        }
        foreach ($check as $item) {
            $this->assertContains($item, $serverVariable->getEnum());
            $this->assertContains($item, $array['enum']);
        }
    }

    public function provideConstruct(): array
    {
        return [
            [null,      null],
            ['PHPUnit', null],
            [' Test  ', 'description'],
            [' te-st ', 'description'],
            [' Te st ', 'description'],
            ['       ', 'description'],
        ];
    }

    public function provideDescription(): array
    {
        return [
            [null],
            ['description'],
            [' description '],
        ];
    }

    public function provideAddVariant(): array
    {
        return [
            ['default', ['v1', 'v2'],     ['v1', 'v2', 'default']],
            ['v1',      ['v1', 'v1'],     ['v1']],
            ['v1',      ['v1', ' v1 '],   ['v1', ' v1 ']],
            ['v1',      ['v1', '    '],   ['v1', '    ']],
            ['v1',      ['v1', '', null], ['v1', '', null]],
        ];
    }

    public function provideRemoveVariant(): array
    {
        return [
            ['df', ['v1', 'v2', 'v3'], ['v2'],                 ['v1', 'v3', 'df']],
            ['v1', ['v1', 'v2', 'v3'], ['v1', 'v2', 'v3'],     []],
            ['v1', ['v1', 'v2', 'v3'], [' v1           '],     ['v1', 'v2', 'v3']],
            [null, ['v1', 'v2', 'v3'], [' v1 ', 'v1', '', ''], ['v2', 'v3', null]],
            ['df', ['v1', 'v2', 'v3'], ['v1', 'v2', 'v3',],    []],
        ];
    }
}
