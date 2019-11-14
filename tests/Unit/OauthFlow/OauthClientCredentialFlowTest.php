<?php

declare(strict_types=1);

namespace Tests\Ddrv\OpenApiGenerator\Unit\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidDescriptionException;
use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidScopeException;
use Ddrv\OpenApiGenerator\Exception\InvalidTokenUrlException;
use Ddrv\OpenApiGenerator\OauthFlow\AbstractOauthFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthClientCredentialsFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthPasswordFlow;
use Ddrv\OpenApiGenerator\OauthFlow\OauthScope;
use Tests\Ddrv\OpenApiGenerator\TestCase\OauthTokenUrlTestCase;

class OauthClientCredentialsFlowTest extends OauthTokenUrlTestCase
{

    /**
     * @return OauthPasswordFlow
     *
     * @throws InvalidDescriptionException
     * @throws InvalidRefreshUrlException
     * @throws InvalidScopeException
     * @throws InvalidTokenUrlException
     */
    public function getFlow(): AbstractOauthFlow
    {
        $scope = new OauthScope('app.test', 'Testing application');
        return new OauthClientCredentialsFlow(
            'http://oauth.example.com/token',
            'http://oauth.example.com/refresh',
            $scope
        );
    }

    public function getType(): string
    {
        return 'clientCredentials';
    }
}
