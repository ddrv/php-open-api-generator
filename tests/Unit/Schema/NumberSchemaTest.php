<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\NumberSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\NumericSchemaTestCase;

class NumberSchemaTest extends NumericSchemaTestCase
{

    public function getFormatData(): array
    {
        $formats = [
            [NumberSchema::FORMAT_DOUBLE, 'double', null],
            [NumberSchema::FORMAT_FLOAT,  'float',  null],
        ];
        return array_merge(parent::getFormatData(), $formats);
    }

    public function getMultipleOfData(): array
    {
        return [
            [null],
            [1.05],
            [2.03],
        ];
    }

    public function getLimitData(): array
    {
        return [
            [null, false, null, false, null],
            [null, true,  null, true,  null],
            [-1.2, false, 10.3, false, null],
            [-1.2, true,  10.3, true,  null],
            [2.54, false, 10.3, false, null],
            [2.54, true,  null, true,  null],
            [null, false, 2.23, false, null],
            [null, true,  2.23, true,  null],
            [10.4, false, 2.23, false, MaximalLimitShouldBeBiggerException::class],
            [10.4, true,  2.23, true,  MaximalLimitShouldBeBiggerException::class],
        ];
    }

    /**
     * @return NumberSchema
     *
     * @throws ArgumentOutOfListException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new NumberSchema();
    }

    public function getType(): string
    {
        return 'number';
    }
}
