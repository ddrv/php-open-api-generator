<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\TestCase;

use Ddrv\OpenApiGenerator\Exception\InvalidDescriptionException;
use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidScopeException;
use Ddrv\OpenApiGenerator\Exception\InvalidTokenUrlException;
use Ddrv\OpenApiGenerator\Exception\RemovingLastScopeException;
use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthPasswordFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use Tests\Ddrv\OpenApiGenerator\TestCase\OauthFlowTestCase;

abstract class OauthTokenUrlTestCase extends OauthFlowTestCase
{

    /**
     * @dataProvider provideSetTokenUrl
     *
     * @param string $url
     * @param string|null $exception
     *
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function testSetTokenUrl(string $url, ?string $exception)
    {
        $flow = $this->getFlow();
        if (!is_null($exception)) {
            $this->expectException($exception);
        }
        $flow->setTokenUrl($url);
        $this->assertSame($url, $flow->getTokenUrl());
        $array = $flow->toArray();
        $this->assertArrayHasKey('tokenUrl', $array);
        $this->assertSame($url, $array['tokenUrl']);
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
    public function provideSetTokenUrl(): array
    {
        return [
            ['',                                  InvalidTokenUrlException::class],
            ['                                 ', InvalidTokenUrlException::class],
            ['http://oauth.example.com/token2', null],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
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
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
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
}
