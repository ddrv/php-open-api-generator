<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractExample;
use Ddrv\OpenApiGenerator\Document\Example;
use Ddrv\OpenApiGenerator\Exception\InvalidValueException;
use Tests\Ddrv\OpenApiGenerator\TestCase\ExampleTestCase;

class ExampleTest extends ExampleTestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param array       $value
     * @param string|null $exception
     *
     * @throws InvalidValueException
     */
    public function testConstruct(array $value, ?string $exception)
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $example = new Example($value);
        $array = $example->toArray();
        $json = json_encode($value);
        $this->assertSame($json, json_encode($example->getValue()));
        $this->assertArrayHasKey('value', $array);
        $this->assertSame($json, json_encode($array['value']));
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param array      $value
     * @param string|null $exception
     *
     * @throws InvalidValueException
     */
    public function testSetValue(array $value, ?string $exception)
    {
        $example = $this->getExample();
        if ($exception) {
            $this->expectException($exception);
        }
        $example->setValue($value);
        $array = $example->toArray();
        $json = json_encode($value);
        $this->assertSame($json, json_encode($example->getValue()));
        $this->assertArrayHasKey('value', $array);
        $this->assertSame($json, json_encode($array['value']));
    }

    public function provideConstruct(): array
    {
        return [
            [['success' => true], null],
            [[1, 2, 3, 5, 8, 13], null],
            [[],                  InvalidValueException::class],
        ];
    }

    /**
     * @return Example
     *
     * @throws InvalidValueException
     */
    protected function getExample(): AbstractExample
    {
        return new Example(['success' => true]);
    }
}
