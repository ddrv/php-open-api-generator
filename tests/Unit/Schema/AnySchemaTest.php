<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\AnySchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\SimpleSchemaTestCase;

class AnySchemaTest extends SimpleSchemaTestCase
{

    /**
     * @throws ArgumentOutOfListException
     */
    public function testSimple()
    {
        $schema = $this->getSchema();
        $array = $schema->toArray();
        $this->assertCount(1, $array);
        $this->assertArrayHasKey('nullable', $array);
    }

    /**
     * @return AnySchema
     *
     * @throws ArgumentOutOfListException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new AnySchema();
    }

    public function getType(): string
    {
        return 'any';
    }
}
