<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\BooleanSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\SimpleSchemaTestCase;

class BooleanSchemaTest extends SimpleSchemaTestCase
{

    /**
     * @return BooleanSchema
     *
     * @throws ArgumentOutOfListException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new BooleanSchema();
    }

    public function getType(): string
    {
        return 'boolean';
    }
}
