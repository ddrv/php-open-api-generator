<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use PHPUnit\Framework\TestCase;

abstract class OauthFlowTestCase extends TestCase
{

    public function testType()
    {
        $flow = $this->getFlow();
        $this->assertSame($this->getType(), $flow->getType());
    }

    /**
     * @dataProvider provideSetRefreshUrl
     *
     * @param string      $url
     * @param string|null $exception
     *
     * @throws InvalidRefreshUrlException
     */
    public function testSetRefreshUrl(string $url, ?string $exception)
    {
        $flow = $this->getFlow();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $flow->setRefreshUrl($url);
        $this->assertSame($url, $flow->getRefreshUrl());
        $array = $flow->toArray();
        $this->assertArrayHasKey('refreshUrl', $array);
        $this->assertSame($url, $array['refreshUrl']);
    }

    /**
     * @dataProvider provideAddScope
     *
     * @param AbstractOauthFlow    $flow
     * @param OauthScope[] $scopes
     * @param int          $count
     */
    public function testAddScope(AbstractOauthFlow $flow, array $scopes, int $count)
    {
        foreach ($scopes as $scope) {
            $flow->addScope($scope);
        }
        $this->assertCount($count, $flow->getScopes());
        $array = $flow->toArray();
        $this->assertArrayHasKey('scopes', $array);
        $this->assertCount($count, $array['scopes']);
        foreach ($flow->getScopes() as $scope) {
            $key = $scope->getScope();
            $this->assertArrayHasKey($key, $array['scopes']);
            $this->assertSame($scope->getDescription(), $array['scopes'][$key]);
        }
    }

    /**
     * @dataProvider provideRemoveScope
     *
     * @param AbstractOauthFlow    $flow
     * @param OauthScope[] $scopes
     * @param int          $count
     * @param string|null  $exception
     *
     * @throws RemovingLastScopeException
     */
    public function testRemoveScope(AbstractOauthFlow $flow, array $scopes, int $count, ?string $exception)
    {
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        foreach ($scopes as $scope) {
            $flow->removeScope($scope);
        }
        $this->assertCount($count, $flow->getScopes());
        $array = $flow->toArray();
        $this->assertArrayHasKey('scopes', $array);
        $this->assertCount($count, $array['scopes']);
        foreach ($flow->getScopes() as $scope) {
            $key = $scope->getScope();
            $this->assertArrayHasKey($key, $array['scopes']);
            $this->assertSame($scope->getDescription(), $array['scopes'][$key]);
        }
    }

    abstract public function getFlow(): AbstractOauthFlow;

    abstract public function provideSetRefreshUrl(): array;

    abstract public function provideAddScope(): array;

    abstract public function provideRemoveScope(): array;

    abstract public function getType(): string;
}
