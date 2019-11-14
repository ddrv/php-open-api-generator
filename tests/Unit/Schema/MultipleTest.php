<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\Schema\AllOfSchema;
use Ddrv\OpenApiGenerator\Schema\AnyOfSchema;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaMultiple;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\BooleanSchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\OneOfSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use PHPUnit\Framework\TestCase;

class MultipleTest extends TestCase
{

    /**
     * @dataProvider getSchemaData
     *
     * @param AbstractSchemaMultiple $schema
     * @param string       $type
     * @param AbstractSchema[]     $refs
     */
    public function testSchema(AbstractSchemaMultiple $schema, string $type, array $refs)
    {
        foreach ($refs as $ref) {
            $schema->addSchema($ref);
        }
        foreach ($schema->getSchemas() as $k => $s) {
            $this->assertSame($refs[$k]->getRef(), $s->getRef());
        }
        $this->assertCount(count($refs), $schema->getSchemas());
        $array = $schema->toArray();
        $this->assertArrayHasKey($type, $array);
        $this->assertCount(count($refs), $array[$type]);
    }

    /**
     * @dataProvider getSchemaData
     *
     * @param AbstractSchemaMultiple $schema
     * @param string                 $type
     * @param AbstractSchema[]       $refs
     *
     * @throws ArgumentOutOfListException
     */
    public function testRemoveSchema(AbstractSchemaMultiple $schema, string $type, array $refs)
    {
        foreach ($refs as $ref) {
            $schema->addSchema($ref);
        }
        $schema->removeSchema(new IntegerSchema());
        foreach ($refs as $k => $s) {
            $this->assertSame($refs[$k]->getRef(), $s->getRef());
        }
        $this->assertCount(count($refs) - 1, $schema->getSchemas());
        $array = $schema->toArray();
        $this->assertArrayHasKey($type, $array);
        $this->assertCount(count($refs) - 1, $array[$type]);
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function getSchemaData(): array
    {
        $schemas = [
            new IntegerSchema(),
            new StringSchema(),
            new ArraySchema(new BooleanSchema()),
        ];
        return  [
            [new OneOfSchema(), 'oneOf', $schemas],
            [new AnyOfSchema(), 'anyOf', $schemas],
            [new AllOfSchema(), 'allOf', $schemas],
        ];
    }
}
