<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\NumericSchemaTestCase;

class IntegerSchemaTest extends NumericSchemaTestCase
{

    public function getFormatData(): array
    {
        $formats =  [
            [IntegerSchema::FORMAT_INT_32, 'int32', null],
            [IntegerSchema::FORMAT_INT_64, 'int64', null],
        ];
        return array_merge(parent::getFormatData(), $formats);
    }

    public function getLimitData(): array
    {
        return [
            [null, false, null, false, null],
            [null, true,  null, true,  null],
            [-100, false, 1000, false, null],
            [-100, true,  1000, true,  null],
            [2000, false, 3000, false, null],
            [2000, true,  null, true,  null],
            [null, false, 2000, false, null],
            [null, true,  2000, true,  null],
            [3000, false, 2000, false, MaximalLimitShouldBeBiggerException::class],
            [3000, true,  2000, true,  MaximalLimitShouldBeBiggerException::class],
        ];
    }

    public function getMultipleOfData(): array
    {
        return [
            [null],
            [2000],
        ];
    }

    /**
     * @return IntegerSchema
     *
     * @throws ArgumentOutOfListException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        $schema = new IntegerSchema();
        try {
            $schema->setMinimum(null)->setMaximum(null)->setMultipleOf(null);
            $schema->getMultipleOf();
            $schema->getMaximum();
            $schema->getMinimum();
        } catch (MaximalLimitShouldBeBiggerException $e) {
        }
        return $schema;
    }

    public function getType(): string
    {
        return 'integer';
    }
}
