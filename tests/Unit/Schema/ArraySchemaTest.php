<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\ArraySchema;
use Ddrv\OpenApiGenerator\Schema\BooleanSchema;
use Ddrv\OpenApiGenerator\Schema\NumberSchema;
use Ddrv\OpenApiGenerator\Schema\OneOfSchema;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\SimpleSchemaTestCase;

class ArraySchemaTest extends SimpleSchemaTestCase
{

    /**
     * @dataProvider getData
     *
     * @param ArraySchema $schema
     * @param AbstractSchema    $item
     */
    public function testType(ArraySchema $schema, AbstractSchema $item)
    {
        $this->assertSame($item->getHash(), $schema->getItems()->getHash());
        $array = $schema->toArray();
        $this->assertArrayHasKey('items', $array);
        $this->assertSame(json_encode($item), json_encode($array['items']));
    }

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
            ->setMinItems($min)
            ->setMaxItems($max)
        ;
        $this->assertSame($min, $schema->getMinItems());
        $this->assertSame($max, $schema->getMaxItems());
        $array = $schema->toArray();
        if (is_null($min) || $min === 0) {
            $this->assertArrayNotHasKey('minItems', $array);
        } else {
            $this->assertArrayHasKey('minItems', $array);
            $this->assertSame($min, $array['minItems']);
        }
        if (is_null($max) || $max === 0) {
            $this->assertArrayNotHasKey('maxItems', $array);
        } else {
            $this->assertArrayHasKey('maxItems', $array);
            $this->assertSame($max, $array['maxItems']);
        }
    }

    /**
     * @dataProvider getUniqueItemsData
     *
     * @param bool $unique
     *
     * @throws ArgumentOutOfListException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testUniqueItems(bool $unique)
    {
        $schema = $this->getSchema();
        $schema->setUniqueItems($unique);
        $this->assertSame($unique, $schema->isUniqueItems());
        $array = $schema->toArray();
        if ($unique) {
            $this->assertArrayHasKey('uniqueItems', $array);
            $this->assertTrue($array['uniqueItems']);
        } else {
            $this->assertArrayNotHasKey('uniqueItems', $array);
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
        $number = new NumberSchema();
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

    public function getUniqueItemsData(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @return ArraySchema
     *
     * @throws ArgumentOutOfListException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new ArraySchema(new BooleanSchema());
    }

    public function getType(): string
    {
        return 'array';
    }
}
