<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

/**
 * @method OauthClientCredentialsFlow setRefreshUrl(string $refreshUrl)
 * @method OauthClientCredentialsFlow setTokenUrl(string $tokenUrl)
 */
final class OauthClientCredentialsFlow extends AbstractOauthTokenUrlFlow
{

    public function getType(): string
    {
        return 'clientCredentials';
    }
}
