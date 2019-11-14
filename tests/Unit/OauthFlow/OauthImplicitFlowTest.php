<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidAuthorizationUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidDescriptionException;
use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidScopeException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthImplicitFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use Tests\Ddrv\OpenApiGenerator\TestCase\OauthFlowTestCase;

class OauthImplicitFlowTest extends OauthFlowTestCase
{

    /**
     * @dataProvider provideSetAuthorizationUrl
     *
     * @param string $url
     * @param string|null $exception
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     */
    public function testSetAuthorizationUrl(string $url, ?string $exception)
    {
        $flow = $this->getFlow();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $flow->setAuthorizationUrl($url);
        $this->assertSame($url, $flow->getAuthorizationUrl());
        $array = $flow->toArray();
        $this->assertArrayHasKey('authorizationUrl', $array);
        $this->assertSame($url, $array['authorizationUrl']);
    }

    /**
     * @return OauthImplicitFlow
     *
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidAuthorizationUrlException
     */
    public function getFlow(): AbstractOauthFlow
    {
        $scope = new OauthScope('app.test', 'Testing application');
        return new OauthImplicitFlow('http://oauth.example.com/auth', 'http://oauth.example.com/refresh', $scope);
    }

    /**
     * @return array[]
     */
    public function provideSetRefreshUrl(): array
    {
        return [
            ['',                                  InvalidRefreshUrlException::class],
            ['                                 ', InvalidRefreshUrlException::class],
            ['http://oauth.example.com/refresh2', null],
        ];
    }

    /**
     * @return array[]
     */
    public function provideSetAuthorizationUrl(): array
    {
        return [
            ['',                                  InvalidAuthorizationUrlException::class],
            ['                                 ', InvalidAuthorizationUrlException::class],
            ['http://oauth.example.com/auth2', null],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     */
    public function provideAddScope(): array
    {
        $scope0 = new OauthScope('app.test', 'Testing application');
        $scope1 = new OauthScope('user.read', 'Read user\'s data');
        $scope2 = new OauthScope('user.modify', 'User modification');
        $scope3 = new OauthScope('user.delete', 'User deletion');
        $flow = $this->getFlow();
        return [
            [clone $flow, [$scope1, $scope2, $scope3], 4],
            [clone $flow, [$scope0, $scope1],          2],
            [clone $flow, [$scope0, $scope0, $scope0], 1],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     */
    public function provideRemoveScope(): array
    {
        $scope0 = new OauthScope('app.test', 'Testing application');
        $scope1 = new OauthScope('user.read', 'Read user\'s data');
        $scope2 = new OauthScope('user.modify', 'User modification');
        $scope3 = new OauthScope('user.delete', 'User deletion');
        $flow1 = $this->getFlow();
        $flow2 = $this->getFlow()->addScope($scope1)->addScope($scope2);
        $flow3 = $this->getFlow();
        return [
            [$flow1, [],                          1, null],
            [$flow2, [$scope0, $scope1, $scope3], 1, null],
            [$flow3, [$scope0],                   0, RemovingLastScopeException::class],
        ];
    }

    public function getType(): string
    {
        return 'implicit';
    }
}
