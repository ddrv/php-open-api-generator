<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

/**
 * @method OauthPasswordFlow setRefreshUrl(string $refreshUrl)
 * @method OauthPasswordFlow setTokenUrl(string $tokenUrl)
 */
final class OauthPasswordFlow extends AbstractOauthTokenUrlFlow
{

    public function getType(): string
    {
        return 'password';
    }
}
