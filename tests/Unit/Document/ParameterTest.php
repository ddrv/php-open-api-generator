<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractHeaderOrParameter;
use Ddrv\OpenApiGenerator\Document\Components;
use Ddrv\OpenApiGenerator\Document\Parameter;
use Ddrv\OpenApiGenerator\Exception\ArgumentOutOfListException;
use Ddrv\OpenApiGenerator\Exception\InvalidHeaderNameException;
use Ddrv\OpenApiGenerator\Exception\InvalidNameException;
use Ddrv\OpenApiGenerator\Schema\IntegerSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\HeaderOrParameterTestCase;

class ParameterTest extends HeaderOrParameterTestCase
{

    /**
     * @dataProvider provideIn
     *
     * @param string      $in
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function testSetIn(string $in, ?string $exception)
    {
        $parameter = $this->getElement();
        if ($exception) {
            $this->expectException($exception);
        }
        $in = mb_strtolower(trim($in));
        $parameter->setIn($in);
        $this->assertSame($in, $parameter->getIn());
    }

    /**
     * @dataProvider provideName
     *
     * @param string      $name
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function testSetName(string $name, ?string $exception)
    {
        $parameter = $this->getElement();
        if ($exception) {
            $this->expectException($exception);
        }
        $parameter->setName($name);
        $name = trim($name);
        $this->assertSame($name, $parameter->getName());
    }

    /**
     * @dataProvider provideHeaderName
     *
     * @param string $name
     * @param string|null $exception
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function testSetHeaderName(string $name, ?string $exception)
    {
        $parameter = new Parameter(Parameter::IN_HEADER, 'ok');
        if ($exception) {
            $this->expectException($exception);
        }
        $parameter->setName($name);
        $name = trim($name);
        $this->assertSame($name, $parameter->getName());
    }

    public function testSetRequiredForPath()
    {
        $parameter = new Parameter(Parameter::IN_PATH, 'id');
        $this->assertTrue($parameter->isRequired());
        $parameter->setRequired(false);
        $this->assertTrue($parameter->isRequired());
        $parameter->setRequired(true);
        $this->assertTrue($parameter->isRequired());
    }

    public function provideIn(): array
    {
        return [
            ['path',   null],
            [' path ', null],
            ['PATH',   null],
            ['query',  null],
            ['header', null],
            ['cookie', null],
            ['',       ArgumentOutOfListException::class],
            ['      ', ArgumentOutOfListException::class],
            ['other',  ArgumentOutOfListException::class],
        ];
    }

    public function provideName(): array
    {
        return [
            ['name',   null],
            ['',       InvalidNameException::class],
            ['      ', InvalidNameException::class],
        ];
    }

    public function provideHeaderName(): array
    {
        return [
            ['x-auth',       null],
            ['accept',        InvalidHeaderNameException::class],
            ['content-type',  InvalidHeaderNameException::class],
            ['authorization', InvalidHeaderNameException::class],
        ];
    }

    /**
     * @return Parameter
     *
     * @throws ArgumentOutOfListException
     * @throws InvalidHeaderNameException
     * @throws InvalidNameException
     */
    public function getElement(): AbstractHeaderOrParameter
    {
        return new Parameter(Parameter::IN_QUERY, 'page');
    }
}
