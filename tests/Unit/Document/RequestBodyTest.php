<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractHttpMessage;
use Ddrv\OpenApiGenerator\Document\RequestBody;
use Ddrv\OpenApiGenerator\Exception\InvalidContentTypeException;
use Ddrv\OpenApiGenerator\Schema\AbstractSchema;
use Tests\Ddrv\OpenApiGenerator\TestCase\HttpMessageTestCase;

/**
 * @method RequestBody getElement()
 */
class RequestBodyTest extends HttpMessageTestCase
{

    /**
     * @dataProvider provideRequired
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
        if (!$hasKey) {
            $this->assertArrayNotHasKey('required', $array);
        } else {
            $this->assertArrayHasKey('required', $array);
            $this->assertSame($value, $array['required']);
        }
    }

    public function provideRequired(): array
    {
        return [
            [true,  true],
            [false, false],
        ];
    }

    /**
     * @param string $type
     * @param AbstractSchema $schema
     * @param string|null $description
     *
     * @return RequestBody
     *
     * @throws InvalidContentTypeException
     */
    public function make(string $type, AbstractSchema $schema, ?string $description): AbstractHttpMessage
    {
        return new RequestBody($type, $schema, $description);
    }
}
