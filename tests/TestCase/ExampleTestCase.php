<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Document\AbstractExample;
use PHPUnit\Framework\TestCase;

abstract class ExampleTestCase extends TestCase
{

    /**
     * @dataProvider provideStringOrNull
     *
     * @param string|null $description
     */
    public function testSetDescription(?string $description)
    {
        $check = trim((string)$description);
        if (!$check) {
            $check = null;
        }
        $example = $this->getExample();
        $example->setDescription($description);
        $array = $example->toArray();
        $this->assertSame($check, $example->getDescription());
        if ($check) {
            $this->assertArrayHasKey('description', $array);
            $this->assertSame($check, $array['description']);
        } else {
            $this->assertArrayNotHasKey('description', $array);
        }
    }

    /**
     * @dataProvider provideStringOrNull
     *
     * @param string|null $summary
     */
    public function testSetSummary(?string $summary)
    {
        $check = trim((string)$summary);
        if (!$check) {
            $check = null;
        }
        $example = $this->getExample();
        $example->setSummary($summary);
        $array = $example->toArray();
        $this->assertSame($check, $example->getSummary());
        if ($check) {
            $this->assertArrayHasKey('summary', $array);
            $this->assertSame($check, $array['summary']);
        } else {
            $this->assertArrayNotHasKey('summary', $array);
        }
    }

    public function provideStringOrNull(): array
    {
        return [
            [' text '],
            ['text'],
            ['  '],
            [''],
            [null],
        ];
    }

    abstract protected function getExample(): AbstractExample;
}
