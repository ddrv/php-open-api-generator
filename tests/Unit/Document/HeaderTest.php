<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\Document;

use Ddrv\OpenApiGenerator\Document\AbstractHeaderOrParameter;
use Ddrv\OpenApiGenerator\Document\Header;
use Tests\Ddrv\OpenApiGenerator\TestCase\HeaderOrParameterTestCase;

class HeaderTest extends HeaderOrParameterTestCase
{

    /**
     * @return Header
     */
    public function getElement(): AbstractHeaderOrParameter
    {
        return new Header();
    }
}
