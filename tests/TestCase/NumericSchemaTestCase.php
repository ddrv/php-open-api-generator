<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Ddrv\OpenApiGenerator\Schema\NumberSchema;

abstract class NumericSchemaTestCase extends SimpleSchemaTestCase
{

    /**
     * @dataProvider getMultipleOfData
     *
     * @param $value
     */
    public function testMultipleOf($value)
    {
        /** @var IntegerSchema|NumberSchema $schema */
        $schema = $this->getSchema();
        $schema->setMultipleOf($value);
        $this->assertSame($value, $schema->getMultipleOf());
        $array = $schema->toArray();
        if (!is_null($value)) {
            $this->assertArrayHasKey('multipleOf', $array);
            $this->assertSame($value, $array['multipleOf']);
        } else {
            $this->assertArrayNotHasKey('multipleOf', $array);
        }
    }

    /**
     * @dataProvider getLimitData
     *
     * @param int|float|null $min
     * @param bool           $excludeMin
     * @param int|float|null $max
     * @param bool           $excludeMax
     * @param string|null    $exception
     *
     * @throws MaximalLimitShouldBeBiggerException
     */
    public function testLimit($min, $excludeMin, $max, $excludeMax, $exception)
    {
        /** @var IntegerSchema|NumberSchema $schema */
        $schema = $this->getSchema();
        if ($exception) {
            $this->expectException($exception);
        }
        $schema
            ->setMinimum($min, $excludeMin)
            ->setMaximum($max, $excludeMax)
        ;
        if (is_null($min)) {
            $excludeMin = false;
        }
        if (is_null($max)) {
            $excludeMax = false;
        }
        $this->assertSame($min, $schema->getMinimum());
        $this->assertSame($excludeMin, $schema->isExclusiveMinimum());
        $this->assertSame($max, $schema->getMaximum());
        $this->assertSame($excludeMax, $schema->isExclusiveMaximum());
        $array = $schema->toArray();
        if (is_null($max)) {
            $this->assertArrayNotHasKey('maximum', $array);
        } else {
            $this->assertArrayHasKey('maximum', $array);
            $this->assertSame($max, $array['maximum']);
        }
        if (is_null($min)) {
            $this->assertArrayNotHasKey('minimum', $array);
        } else {
            $this->assertArrayHasKey('minimum', $array);
            $this->assertSame($min, $array['minimum']);
        }
        if ($excludeMax) {
            $this->assertArrayHasKey('exclusiveMaximum', $array);
            $this->assertTrue($array['exclusiveMaximum']);
        } else {
            $this->assertArrayNotHasKey('exclusiveMaximum', $array);
        }
        if ($excludeMin) {
            $this->assertArrayHasKey('exclusiveMinimum', $array);
            $this->assertTrue($array['exclusiveMinimum']);
        } else {
            $this->assertArrayNotHasKey('exclusiveMinimum', $array);
        }
    }

    abstract public function getMultipleOfData(): array;

    abstract public function getLimitData(): array;
}
