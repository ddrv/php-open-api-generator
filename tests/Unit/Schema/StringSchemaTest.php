<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Schema;

use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidPatternException;
use Ddrv\OpenApiGenerator\Exception\MaximalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Exception\MinimalLimitShouldBeBiggerException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchemaSimple;
use Ddrv\OpenApiGenerator\Schema\StringSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\SimpleSchemaTestCase;

class StringSchemaTest extends SimpleSchemaTestCase
{

    /**
     * @dataProvider getPatternData
     *
     * @param string|null $pattern
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     */
    public function testPattern(?string $pattern, ?string $exception)
    {
        $schema = $this->getSchema();
        if ($exception) {
            $this->expectException($exception);
        }
        $schema->setPattern($pattern);
        $this->assertSame($pattern, $schema->getPattern());
        $array = $schema->toArray();
        if (is_null($pattern)) {
            $this->assertArrayNotHasKey('pattern', $array);
        } else {
            $this->assertArrayHasKey('pattern', $array);
            $this->assertSame($pattern, $array['pattern']);
        }
    }

    /**
     * @dataProvider getLengthData
     *
     * @param int|null $minLength
     * @param int|null $maxLength
     *
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     * @throws MaximalLimitShouldBeBiggerException
     * @throws MinimalLimitShouldBeBiggerException
     */
    public function testLength(?int $minLength, ?int $maxLength, ?string $exception)
    {
        $schema = $this->getSchema();
        if ($exception) {
            $this->expectException($exception);
        }
        $schema
            ->setMinLength($minLength)
            ->setMaxLength($maxLength)
        ;
        $this->assertSame($minLength, $schema->getMinLength());
        $this->assertSame($maxLength, $schema->getMaxLength());
        $array = $schema->toArray();
        if (is_null($minLength)) {
            $this->assertArrayNotHasKey('minLength', $array);
        } else {
            $this->assertArrayHasKey('minLength', $array);
            $this->assertSame($minLength, $array['minLength']);
        }
        if (is_null($maxLength)) {
            $this->assertArrayNotHasKey('maxLength', $array);
        } else {
            $this->assertArrayHasKey('maxLength', $array);
            $this->assertSame($maxLength, $array['maxLength']);
        }
    }

    public function getFormatData(): array
    {
        $formats = [
            [StringSchema::FORMAT_BINARY,    'binary',    null],
            [StringSchema::FORMAT_BYTE,      'byte',      null],
            [StringSchema::FORMAT_DATE,      'date',      null],
            [StringSchema::FORMAT_DATE_TIME, 'date-time', null],
            [StringSchema::FORMAT_EMAIL,     'email',     null],
            [StringSchema::FORMAT_PASSWORD,  'password',  null],
            [StringSchema::FORMAT_URI,       'uri',       null],
            [StringSchema::FORMAT_UUID,      'uuid',      null],
            [StringSchema::FORMAT_HOSTNAME,  'hostname',  null],
            [StringSchema::FORMAT_IP_V4,     'ipv4',      null],
            [StringSchema::FORMAT_IP_V6,     'ipv6',      null],
        ];
        return array_merge(parent::getFormatData(), $formats);
    }

    public function getPatternData(): array
    {
        return [
            [null,          null],
            ['^[a-z0-9]+$', null],
            ['in/correct!', InvalidPatternException::class],
        ];
    }

    public function getLengthData(): array
    {
        return [
            [null, null, null],
            [0,    null, null],
            [2000, 3000, null],
            [2000, null, null],
            [null, 2000, null],
            [null, 0,    MaximalLimitShouldBeBiggerException::class],
            [-100, 1000, MinimalLimitShouldBeBiggerException::class],
            [3000, 2000, MaximalLimitShouldBeBiggerException::class],
        ];
    }

    /**
     * @return StringSchema
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidPatternException
     */
    public function getSchema(): AbstractSchemaSimple
    {
        return new StringSchema();
    }

    public function getType(): string
    {
        return 'string';
    }
}
