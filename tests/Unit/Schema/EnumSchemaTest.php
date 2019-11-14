<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\EnumSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\SimpleSchemaTestCase;

class EnumSchemaTest extends SimpleSchemaTestCase
{

    /**
     * @dataProvider getData
     *
     * @param EnumSchema $schema
     * @param array $values
     */
    public function testType(EnumSchema $schema, array $values)
    {
        $this->assertEnum($values, $schema->getEnum());
        $array = $schema->toArray();
        $this->assertArrayHasKey('enum', $array);
        $this->assertEnum($values, $array['enum']);
    }

    public function assertEnum(array $expected, array $actual)
    {
        $this->assertCount(count($expected), $actual);
        foreach ($expected as $value) {
            $this->assertContains($value, $actual);
        }
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     */
    public function getData(): array
    {
        return [
            [
                new EnumSchema('v1', 'v2', 'v3'),
                ['v1', 'v2', 'v3'],
            ],
            [
                $this->getSchema()->addValue('v1')->addValue('v2')->addValue('v3'),
                ['v1', 'v2', 'v3'],
            ],
            [
                $this->getSchema()->addValue('v1')->addValue('v2')->addValue('v3')->removeValue('v2'),
                ['v1', 'v3'],
            ],
            [
                $this->getSchema()->addValue('v1')->addValue('v2')->addValue('v3')->addValue('v2'),
                ['v1', 'v2', 'v3'],
            ],
        ];
    }

    /**
     * @return EnumSchema
     *
     * @throws ArgumentOutOfListException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new EnumSchema();
    }

    public function getType(): string
    {
        return 'string';
    }
}
