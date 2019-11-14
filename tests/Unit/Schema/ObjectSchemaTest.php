<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\ObjectSchemaProperty;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\AllOfSchema;
use Ddrv\OpenApiGenerator\Schema\AnySchema;
use Ddrv\OpenApiGenerator\Schema\AnyOfSchema;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\ObjectSchema;
use Ddrv\OpenApiGenerator\Schema\OneOfSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\SimpleSchemaTestCase;

class ObjectSchemaTest extends SimpleSchemaTestCase
{

    /**
     * @dataProvider getLimitData
     *
     * @param int|null    $min
     * @param int|null    $max
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testLimit(?int $min, ?int $max, ?string $exception)
    {
        $schema = $this->getSchema();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $schema
            ->setMinProperties($min)
            ->setMaxProperties($max)
        ;
        $this->assertSame($min, $schema->getMinProperties());
        $this->assertSame($max, $schema->getMaxProperties());
        $array = $schema->toArray();
        if (is_null($min) || $min === 0) {
            $this->assertArrayNotHasKey('minProperties', $array);
        } else {
            $this->assertArrayHasKey('minProperties', $array);
            $this->assertSame($min, $array['minProperties']);
        }
        if (is_null($max) || $max === 0) {
            $this->assertArrayNotHasKey('maxProperties', $array);
        } else {
            $this->assertArrayHasKey('maxProperties', $array);
            $this->assertSame($max, $array['maxProperties']);
        }
    }

    /**
     * @dataProvider getAdditionalProperties
     *
     * @param AbstractSchema    $additionalProperties
     * @param bool|null $inArrayValue
     *
     * @throws ArgumentOutOfListException
     */
    public function testAdditionalProperties(AbstractSchema $additionalProperties, ?bool $inArrayValue)
    {
        $schema = $this->getSchema();
        $schema->setAdditionalProperties($additionalProperties);
        $array = $schema->toArray();
        if (is_null($additionalProperties)) {
            $this->assertNull($schema->getAdditionalProperties());
            $this->assertArrayNotHasKey('additionalProperties', $array);
        } else {
            $this->assertSame($additionalProperties->getHash(), $schema->getAdditionalProperties()->getHash());
            $this->assertArrayHasKey('additionalProperties', $array);
            if (is_null($inArrayValue)) {
                $this->assertSame(json_encode($additionalProperties), json_encode($array['additionalProperties']));
            } else {
                $this->assertSame($inArrayValue, $array['additionalProperties']);
            }
        }
    }

    /**
     * @dataProvider getPropertiesData
     *
     * @param AbstractSchema[] $properties
     * @param string[] $required
     * @param string[] $ro
     * @param string[] $wo
     *
     * @throws ArgumentOutOfListException
     */
    public function testProperties(array $properties, array $required, array $ro, array $wo)
    {
        $schema = $this->getSchema();
        foreach ($properties as $property => $s) {
            $access = null;
            $req = in_array($property, $required);
            if (in_array($property, $ro)) {
                $access = ObjectSchemaProperty::ACCESS_READ_ONLY;
            };
            if (in_array($property, $wo)) {
                $access = ObjectSchemaProperty::ACCESS_WRITE_ONLY;
            };
            $schema->setProperty($s, $property, $access, $req);
        }
        $array = $schema->toArray();
        $this->assertCount(count($properties), $schema->getProperties());
        $this->assertCount(count($required), $schema->getRequired());
        $this->assertArrayHasKey('properties', $array);
        if ($required) {
            $this->assertArrayHasKey('required', $array);
        } else {
            $this->assertArrayNotHasKey('required', $array);
        }
        foreach ($required as $name) {
            $this->assertContains($name, $schema->getRequired());
            $this->assertContains($name, $array['required']);
        }
        $props = $schema->getProperties();
        foreach ($properties as $name => $property) {
            $s = $props[$name];
            $this->assertSame(json_encode($property), json_encode($s->getSchema()));
        }
        foreach ($ro as $name) {
            $s = $props[$name];
            $this->assertSame(ObjectSchemaProperty::ACCESS_READ_ONLY, $s->getAccess());
            $this->assertArrayHasKey('readOnly', $array['properties'][$name]);
        }
        foreach ($wo as $name) {
            $s = $props[$name];
            $this->assertSame(ObjectSchemaProperty::ACCESS_WRITE_ONLY, $s->getAccess());
            $this->assertArrayHasKey('writeOnly', $array['properties'][$name]);
        }
    }

    /**
     * @return array
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function getData(): array
    {
        $number = new IntegerSchema();
        $string = new StringSchema();
        $oneOf = new OneOfSchema($number, $string);
        return [
            [
                new ArraySchema($number),
                $number,
            ],
            [
                new ArraySchema($string),
                $string,
            ],
            [
                new ArraySchema($oneOf),
                $oneOf,
            ],
        ];
    }

    public function getLimitData(): array
    {
        return [
            [null, null, null],
            [0,    0,    null],
            [1000, null, null],
            [null, 1000, null],
            [1000, 9999, null],
            [-100, null, MinimalLimitShouldBeBiggerException::class],
            [null, -100, MaximalLimitShouldBeBiggerException::class],
            [2000, 1000, MaximalLimitShouldBeBiggerException::class],
        ];
    }

    public function getAdditionalProperties(): array
    {
        $any = new AnySchema();
        $number = new IntegerSchema();
        $string = new StringSchema(StringSchema::FORMAT_UUID);
        $anyOf = new AnyOfSchema($any, $number, $string);
        return [
            [$any,    true],
            [$number, null],
            [$string, null],
            [$anyOf,  null],
        ];
    }

    public function getPropertiesData(): array
    {
        $any = new AnySchema();
        $number = new IntegerSchema();
        $string = new StringSchema(StringSchema::FORMAT_UUID);
        $all = new AllOfSchema($number, $string);
        return [
            [['foo' => $any, 'bar' => $number, 'baz' => $all], ['foo', 'baz'], ['baz'], ['foo']],
            [['foo' => $any, 'bar' => $number, 'baz' => $all], [],             [],      []],
        ];
    }

    /**
     * @return ObjectSchema
     *
     * @throws ArgumentOutOfListException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new ObjectSchema();
    }

    public function getType(): string
    {
        return 'object';
    }
}
