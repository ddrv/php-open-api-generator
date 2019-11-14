<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractExample;
use Ddrv\OpenApiGenerator\Document\ExternalExample;
use Ddrv\OpenApiGenerator\Exception\InvalidValueException;
use Tests\Ddrv\OpenApiGenerator\TestCase\ExampleTestCase;

class ExternalExampleTest extends ExampleTestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $value
     * @param string|null $exception
     *
     * @throws InvalidValueException
     */
    public function testConstruct(string $value, ?string $exception)
    {
        $check = trim($value);
        if ($exception) {
            $this->expectException($exception);
        }
        $example = new ExternalExample($value);
        $array = $example->toArray();
        $this->assertSame($check, $example->getValue());
        $this->assertArrayHasKey('externalValue', $array);
        $this->assertSame($check, $array['externalValue']);
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param string      $value
     * @param string|null $exception
     *
     * @throws InvalidValueException
     */
    public function testSetValue(string $value, ?string $exception)
    {
        $check = trim($value);
        $example = $this->getExample();
        if ($exception) {
            $this->expectException($exception);
        }
        $example->setValue($value);
        $array = $example->toArray();
        $this->assertSame($check, $example->getValue());
        $this->assertArrayHasKey('externalValue', $array);
        $this->assertSame($check, $array['externalValue']);
    }

    public function provideConstruct(): array
    {
        return [
            ['https://example.com/requests/auth.json', null],
            [' https://example.com/models/user.json ', null],
            ['                                      ', InvalidValueException::class],
            ['',                                       InvalidValueException::class],
        ];
    }

    /**
     * @return ExternalExample
     *
     * @throws InvalidValueException
     */
    protected function getExample(): AbstractExample
    {
        return new ExternalExample('https://example.com/example.json');
    }
}
