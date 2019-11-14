<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidDescriptionException;
use Ddrv\OpenApiGenerator\Exception\InvalidScopeException;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use PHPUnit\Framework\TestCase;

class OauthScopeTest extends TestCase
{

    /**
     * @dataProvider provideScopeData
     *
     * @param string      $code
     * @param string      $description
     * @param string|null $exception
     *
     * @throws InvalidDescriptionException
     * @throws InvalidScopeException
     */
    public function testConstructor(string $code, string $description, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $scope = new OauthScope($code, $description);
        $this->assertSame($code, $scope->getScope());
        $this->assertSame($description, $scope->getDescription());
    }

    /**
     * @dataProvider provideScopeData
     *
     * @param string      $code
     * @param string      $description
     * @param string|null $exception
     *
     * @throws InvalidDescriptionException
     * @throws InvalidScopeException
     */
    public function testSetters(string $code, string $description, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $scope = new OauthScope('code', 'description');
        $scope
            ->setScope($code)
            ->setDescription($description)
        ;
        $this->assertSame($code, $scope->getScope());
        $this->assertSame($description, $scope->getDescription());
    }

    public function provideScopeData(): array
    {
        return [
            ['app.test', 'Application testing', null],
            ['',         'Application testing', InvalidScopeException::class],
            ['  ',       'Application testing', InvalidScopeException::class],
            ['app.test', '',                    InvalidDescriptionException::class],
            ['app.test', '  ',                  InvalidDescriptionException::class],
        ];
    }
}
