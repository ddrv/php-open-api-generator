<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\SecurityScheme;

use Ddrv\OpenApiGenerator\Exception\InvalidAuthorizationUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidDescriptionException;
use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidScopeException;
use Ddrv\OpenApiGenerator\Exception\InvalidTokenUrlException;
use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthAuthorizationCodeFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthClientCredentialsFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthImplicitFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthPasswordFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use Ddrv\OpenApiGenerator\SecurityScheme\AbstractSecurityScheme;
use Ddrv\OpenApiGenerator\SecurityScheme\Oauth2SecurityScheme;
use Tests\Ddrv\OpenApiGenerator\TestCase\SecuritySchemeTestCase;

class Oauth2SecuritySchemeTest extends SecuritySchemeTestCase
{

    /**
     * @dataProvider provideConstruct
     *
     * @param AbstractOauthFlow[] $flows
     * @param int $check
     */
    public function testConstruct(array $flows, int $check)
    {
        $securityScheme = new Oauth2SecurityScheme(...$flows);
        $this->assertCount($check, $securityScheme->getFlows());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        foreach ($flows as $flow) {
            $this->assertArrayHasKey($flow->getType(), $array['flows']);
        }
    }

    /**
     * @dataProvider provideConstruct
     *
     * @param AbstractOauthFlow[] $flows
     * @param int $check
     */
    public function testSetFlow(array $flows, int $check)
    {
        $securityScheme = new Oauth2SecurityScheme();
        foreach ($flows as $flow) {
            $securityScheme->setFlow($flow);
        }
        $this->assertCount($check, $securityScheme->getFlows());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        foreach ($flows as $flow) {
            $this->assertArrayHasKey($flow->getType(), $array['flows']);
        }
    }

    /**
     * @dataProvider provideGetImplicitFlow
     *
     * @param AbstractOauthFlow[] $flows
     * @param bool                $exists
     */
    public function testGetImplicitFlow(array $flows, bool $exists)
    {
        $securityScheme = new Oauth2SecurityScheme(...$flows);
        if ($exists) {
            $this->assertInstanceOf(OauthImplicitFlow::class, $securityScheme->getImplicitFlow());
        } else {
            $this->assertNull($securityScheme->getImplicitFlow());
        }
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        if ($exists) {
            $this->assertArrayHasKey('implicit', $array['flows']);
        } elseif (!is_null($array['flows'])) {
            $this->assertArrayNotHasKey('implicit', $array['flows']);
        }
    }

    /**
     * @dataProvider provideGetPasswordFlow
     *
     * @param AbstractOauthFlow[] $flows
     * @param bool                $exists
     */
    public function testGetPasswordFlow(array $flows, bool $exists)
    {
        $securityScheme = new Oauth2SecurityScheme(...$flows);
        if ($exists) {
            $this->assertInstanceOf(OauthPasswordFlow::class, $securityScheme->getPasswordFlow());
        } else {
            $this->assertNull($securityScheme->getPasswordFlow());
        }
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        if ($exists) {
            $this->assertArrayHasKey('password', $array['flows']);
        } elseif (!is_null($array['flows'])) {
            $this->assertArrayNotHasKey('password', $array['flows']);
        }
    }

    /**
     * @dataProvider provideGetAuthorizationCodeFlow
     *
     * @param AbstractOauthFlow[] $flows
     * @param bool                $exists
     */
    public function testGetAuthorizationCodeFlow(array $flows, bool $exists)
    {
        $securityScheme = new Oauth2SecurityScheme(...$flows);
        if ($exists) {
            $this->assertInstanceOf(OauthAuthorizationCodeFlow::class, $securityScheme->getAuthorizationCodeFlow());
        } else {
            $this->assertNull($securityScheme->getAuthorizationCodeFlow());
        }
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        if ($exists) {
            $this->assertArrayHasKey('authorizationCode', $array['flows']);
        } elseif (!is_null($array['flows'])) {
            $this->assertArrayNotHasKey('authorizationCode', $array['flows']);
        }
    }

    /**
     * @dataProvider provideGetClientCredentialsCodeFlow
     *
     * @param AbstractOauthFlow[] $flows
     * @param bool                $exists
     */
    public function testGetClientCredentialsCodeFlow(array $flows, bool $exists)
    {
        $securityScheme = new Oauth2SecurityScheme(...$flows);
        if ($exists) {
            $this->assertInstanceOf(OauthClientCredentialsFlow::class, $securityScheme->getClientCredentialsFlow());
        } else {
            $this->assertNull($securityScheme->getClientCredentialsFlow());
        }
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        if ($exists) {
            $this->assertArrayHasKey('clientCredentials', $array['flows']);
        } elseif (!is_null($array['flows'])) {
            $this->assertArrayNotHasKey('clientCredentials', $array['flows']);
        }
    }

    /**
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function testRemoveImplicitFlow()
    {
        $all = array_values($this->getAllFlows());
        $securityScheme = new Oauth2SecurityScheme(...$all);
        $securityScheme->removeImplicitFlow();
        $this->assertCount(3, $securityScheme->getFlows());
        $this->assertNull($securityScheme->getImplicitFlow());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        $this->assertCount(3, $array['flows']);
        $this->assertArrayNotHasKey('implicit', $array);
    }

    /**
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function testRemovePasswordFlow()
    {
        $all = array_values($this->getAllFlows());
        $securityScheme = new Oauth2SecurityScheme(...$all);
        $securityScheme->removePasswordFlow();
        $this->assertCount(3, $securityScheme->getFlows());
        $this->assertNull($securityScheme->getPasswordFlow());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        $this->assertCount(3, $array['flows']);
        $this->assertArrayNotHasKey('password', $array);
    }

    /**
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function testRemoveAuthorizationCodeFlow()
    {
        $all = array_values($this->getAllFlows());
        $securityScheme = new Oauth2SecurityScheme(...$all);
        $securityScheme->removeAuthorizationCodeFlow();
        $this->assertCount(3, $securityScheme->getFlows());
        $this->assertNull($securityScheme->getAuthorizationCodeFlow());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        $this->assertCount(3, $array['flows']);
        $this->assertArrayNotHasKey('authorizationCode', $array);
    }

    /**
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function testRemoveClientCredentialsFlow()
    {
        $all = array_values($this->getAllFlows());
        $securityScheme = new Oauth2SecurityScheme(...$all);
        $securityScheme->removeClientCredentialsFlow();
        $this->assertCount(3, $securityScheme->getFlows());
        $this->assertNull($securityScheme->getClientCredentialsFlow());
        $array = $securityScheme->toArray();
        $this->assertArrayHasKey('flows', $array);
        $this->assertCount(3, $array['flows']);
        $this->assertArrayNotHasKey('clientCredentials', $array);
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function provideConstruct(): array
    {
        $flows = $this->getAllFlows();
        $flow1 = $flows['implicit'];
        $flow2 = $flows['password'];
        $flow3 = $flows['clientCredentials'];
        $flow4 = $flows['authorizationCode'];
        return [
            [[],                               0],
            [[$flow1, $flow2, $flow3, $flow4], 4],
            [[$flow1, $flow1, $flow1, $flow1], 1],
            [[$flow1, $flow2],                 2],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function provideGetImplicitFlow(): array
    {
        $flows = $this->getAllFlows();
        $flow1 = $flows['implicit'];
        $flow2 = $flows['password'];
        return [
            [[],               false],
            [[$flow2],         false],
            [[$flow1],         true],
            [[$flow1, $flow1], true],
            [[$flow1, $flow2], true],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function provideGetPasswordFlow(): array
    {
        $flows = $this->getAllFlows();
        $flow1 = $flows['password'];
        $flow2 = $flows['implicit'];
        return [
            [[],               false],
            [[$flow2],         false],
            [[$flow1],         true],
            [[$flow1, $flow1], true],
            [[$flow1, $flow2], true],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function provideGetAuthorizationCodeFlow(): array
    {
        $flows = $this->getAllFlows();
        $flow1 = $flows['authorizationCode'];
        $flow2 = $flows['password'];
        return [
            [[],               false],
            [[$flow2],         false],
            [[$flow1],         true],
            [[$flow1, $flow1], true],
            [[$flow1, $flow2], true],
        ];
    }

    /**
     * @return array[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function provideGetClientCredentialsCodeFlow(): array
    {
        $flows = $this->getAllFlows();
        $flow1 = $flows['clientCredentials'];
        $flow2 = $flows['password'];
        return [
            [[],               false],
            [[$flow2],         false],
            [[$flow1],         true],
            [[$flow1, $flow1], true],
            [[$flow1, $flow2], true],
        ];
    }

    public function getType(): string
    {
        return 'oauth2';
    }

    /**
     * @return Oauth2SecurityScheme
     */
    public function getSecurityScheme(): AbstractSecurityScheme
    {
        return new Oauth2SecurityScheme();
    }

    /**
     * @return AbstractOauthFlow[]
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    private function getAllFlows()
    {
        $flows = [];
        $result = [];
        $scope = new OauthScope('app.test', 'Testing application');
        $flows[] = new OauthImplicitFlow('http://example.com/auth', 'http://example.com/refresh', $scope);
        $flows[] = new OauthPasswordFlow('http://example.com/token', 'http://example.com/refresh', $scope);
        $flows[] = new OauthClientCredentialsFlow('http://example.com/token', 'http://example.com/refresh', $scope);
        $flows[] = new OauthAuthorizationCodeFlow(
            'http://example.com/auth',
            'http://example.com/token',
            'http://example.com/refresh',
            $scope
        );
        foreach ($flows as $flow) {
            $result[$flow->getType()] = $flow;
        }
        return $result;
    }
}
