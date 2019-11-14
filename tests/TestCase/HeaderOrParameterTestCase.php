<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Document\AbstractHeaderOrParameter;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use PHPUnit\Framework\TestCase;

abstract class HeaderOrParameterTestCase extends TestCase
{

    /**
     * @dataProvider provideSetBool
     *
     * @param bool $value
     * @param bool $hasKey
     */
    public function testSetRequired(bool $value, bool $hasKey)
    {
        $element = $this->getElement();
        $element->setRequired($value);
        $this->assertSame($value, $element->isRequired());
        $array = $element->toArray();
        $this->checkInArray($value, $array, 'required', $hasKey);
    }

    /**
     * @dataProvider provideSetBool
     *
     * @param bool $value
     * @param bool $hasKey
     */
    public function testSetAllowEmptyValue(bool $value, bool $hasKey)
    {
        $element = $this->getElement();
        $element->setAllowEmptyValue($value);
        $this->assertSame($value, $element->isAllowEmptyValue());
        $array = $element->toArray();
        $this->checkInArray($value, $array, 'allowEmptyValue', $hasKey);
    }

    /**
     * @dataProvider provideSetBool
     *
     * @param bool $value
     * @param bool $hasKey
     */
    public function testSetDeprecated(bool $value, bool $hasKey)
    {
        $element = $this->getElement();
        $element->setDeprecated($value);
        $this->assertSame($value, $element->isDeprecated());
        $array = $element->toArray();
        $this->checkInArray($value, $array, 'deprecated', $hasKey);
    }

    /**
     * @dataProvider provideSetDescription
     *
     * @param string|null $value
     * @param bool        $hasKey
     */
    public function testSetDescription(?string $value, bool $hasKey)
    {
        $element = $this->getElement();
        $element->setDescription($value);
        $value = trim((string)$value);
        if (!$value) {
            $value = null;
        }
        $this->assertSame($value, $element->getDescription());
        $array = $element->toArray();
        $this->checkInArray($value, $array, 'description', $hasKey);
    }

    /**
     * @dataProvider provideSetSchema
     *
     * @param AbstractSchema|null $value
     */
    public function testSetSchema(?AbstractSchema $value)
    {
        $element = $this->getElement();
        $element->setSchema($value);
        $this->assertSame(json_encode($value), json_encode($element->getSchema()));
        $array = $element->toArray();
        if ($value) {
            $this->assertArrayHasKey('schema', $array);
            $this->assertSame(json_encode($value->toArray()), json_encode($array['schema']));
        } else {
            $this->assertArrayNotHasKey('schema', $array);
        }
    }

    private function checkInArray($value, array $array, string $key, bool $hasKey)
    {
        if ($hasKey) {
            $this->assertArrayHasKey($key, $array);
            $this->assertSame($value, $array[$key]);
        } else {
            $this->assertArrayNotHasKey($key, $array);
        }
    }

    public function provideSetBool(): array
    {
        return [
            [true,  true],
            [false, false],
        ];
    }

    public function provideSetDescription(): array
    {
        return [
            [null, false],
            ['',   false],
            ['  ', false],
            ['ok', true],
        ];
    }

    public function provideSetSchema(): array
    {
        return [
            [null],
            [new IntegerSchema()],
        ];
    }

    abstract public function getElement(): AbstractHeaderOrParameter;
}
