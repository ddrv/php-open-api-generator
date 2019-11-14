<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use PHPUnit\Framework\TestCase;

abstract class SimpleSchemaTestCase extends TestCase
{

    /**
     * @dataProvider getBaseData
     *
     * @param AbstractSchemaSimple $schema
     * @param array      $values
     * @param array      $keys
     */
    public function testBase(AbstractSchemaSimple $schema, array $values, array $keys)
    {
        foreach ($values as $field => $assertion) {
            $value = uniqid();
            switch ($field) {
                case 'type':
                    $value = $schema->getType();
                    break;
                case 'format':
                    $value = $schema->getFormat();
                    break;
                case 'pattern':
                    $value = $schema->getPattern();
                    break;
                case 'description':
                    $value = $schema->getDescription();
                    break;
                case 'nullable':
                    $value = $schema->isNullable();
                    break;
            }
            $this->assertSame($assertion, $value);
        }
        $array = $schema->toArray();
        $this->assertCount(count($keys), $array);
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    /**
     * @dataProvider getFormatData
     *
     * @param string|null $format
     * @param string|null $check
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     */
    public function testFormat(?string $format, ?string $check, ?string $exception)
    {
        $schema = $this->getSchema();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $schema->setFormat($format);
        $this->assertSame($check, $schema->getFormat());
        $array = $schema->toArray();
        if (is_null($format)) {
            $this->assertArrayNotHasKey('format', $array);
        } else {
            $this->assertArrayHasKey('format', $array);
            $this->assertSame($check, $array['format']);
        }
    }



    final public function getBaseData(): array
    {
        $type = $this->getType();
        $keys = [];
        if ($type === 'any') {
            $keys[] = 'nullable';
        } else {
            $keys[] = 'type';
        }
        if ($type === 'array') {
            $keys[] = 'items';
        }
        return [
            [
                $this->getSchema(),
                ['type' => $type, 'format' => null, 'pattern' => null, 'description' => null, 'nullable' => false],
                $keys
            ],
            [
                $this->getSchema()->setNullable(true),
                ['type' => $type, 'format' => null, 'pattern' => null, 'description' => null, 'nullable' => true],
                $this->mergeFields($keys, ['nullable']),
            ],
            [
                $this->getSchema()->setNullable(true)->setNullable(false),
                ['type' => $type, 'format' => null, 'pattern' => null, 'description' => null, 'nullable' => false],
                $keys
            ],
            [
                $this->getSchema()->setDescription('foo'),
                ['type' => $type, 'format' => null, 'pattern' => null, 'description' => 'foo', 'nullable' => false],
                $this->mergeFields($keys, ['description']),
            ],
            [
                $this->getSchema()->setDescription('foo')->setDescription('bar'),
                ['type' => $type, 'format' => null, 'pattern' => null, 'description' => 'bar', 'nullable' => false],
                $this->mergeFields($keys, ['description']),
            ],
            [
                $this->getSchema()->setDescription('foo')->setDescription(null),
                ['type' => $type, 'format' => null, 'pattern' => null, 'description' => null, 'nullable' => false],
                $keys,
            ],
        ];
    }

    private function mergeFields(array $array, array $fields): array
    {
        return array_unique(array_merge($array, $fields));
    }

    public function getFormatData(): array
    {
        return [
            [null, null, null],
            ['--', null, ArgumentOutOfListException::class],
        ];
    }

    abstract public function getSchema(): AbstractSchemaSimple;

    abstract public function getType(): string;
}
