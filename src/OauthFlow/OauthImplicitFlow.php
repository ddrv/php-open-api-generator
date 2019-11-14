<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidAuthorizationUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;

/**
 * @method OauthImplicitFlow setRefreshUrl(string $refreshUrl)
 */
final class OauthImplicitFlow extends AbstractOauthFlow
{

    /**
     * @var string|null
     */
    protected $authorizationUrl;

    /**
     * @param string     $authorizationUrl
     * @param string     $refreshUrl
     * @param OauthScope $scope
     * @param OauthScope ...$scopes
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidRefreshUrlException
     */
    public function __construct(string $authorizationUrl, string $refreshUrl, OauthScope $scope, OauthScope ...$scopes)
    {
        parent::__construct($refreshUrl, $scope, ...$scopes);
        $this->setAuthorizationUrl($authorizationUrl);
    }

    /**
     * @param string $authorizationUrl
     *
     * @return OauthImplicitFlow
     *
     * @throws InvalidAuthorizationUrlException
     */
    public function setAuthorizationUrl(string $authorizationUrl): self
    {
        $authorizationUrl = trim($authorizationUrl);
        if (!$authorizationUrl) {
            throw new InvalidAuthorizationUrlException();
        }
        $this->authorizationUrl = $authorizationUrl;
        return $this;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->authorizationUrl;
    }

    public function getType(): string
    {
        return 'implicit';
    }

    public function toArray(): array
    {
        $result = [
            'authorizationUrl' => $this->getAuthorizationUrl(),
            'refreshUrl' => null,
            'scopes' => null,
        ];
        $result = array_replace($result, parent::toArray());
        return $result;
    }
}
