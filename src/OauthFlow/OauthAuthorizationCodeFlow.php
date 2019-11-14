<?php

declare(strict_types=1);

namespace Ddrv\OpenApiGenerator\OauthFlow;

use Ddrv\OpenApiGenerator\Exception\InvalidAuthorizationUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidRefreshUrlException;
use Ddrv\OpenApiGenerator\Exception\InvalidTokenUrlException;

/**
 * @method OauthAuthorizationCodeFlow setRefreshUrl(string $refreshUrl)
 */
final class OauthAuthorizationCodeFlow extends AbstractOauthFlow
{

    /**
     * @var string
     */
    protected $authorizationUrl;

    /**
     * @var string
     */
    protected $tokenUrl;

    /**
     * @param string     $authorizationUrl
     * @param string     $tokenUrl
     * @param string     $refreshUrl
     * @param OauthScope $scope
     * @param OauthScope ...$scopes
     *
     * @throws InvalidAuthorizationUrlException
     * @throws InvalidTokenUrlException
     * @throws InvalidRefreshUrlException
     */
    public function __construct(
        string $authorizationUrl,
        string $tokenUrl,
        string $refreshUrl,
        OauthScope $scope,
        OauthScope ...$scopes
    ) {
        parent::__construct($refreshUrl, $scope, ...$scopes);
        $this
            ->setAuthorizationUrl($authorizationUrl)
            ->setTokenUrl($tokenUrl)
        ;
    }

    /**
     * @param string $authorizationUrl
     *
     * @return OauthAuthorizationCodeFlow
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

    /**
     * @param string $tokenUrl
     *
     * @return OauthAuthorizationCodeFlow
     *
     * @throws InvalidTokenUrlException
     */
    public function setTokenUrl(string $tokenUrl): self
    {
        $tokenUrl = trim($tokenUrl);
        if (!$tokenUrl) {
            throw new InvalidTokenUrlException();
        }
        $this->tokenUrl = $tokenUrl;
        return $this;
    }

    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    public function getType(): string
    {
        return 'authorizationCode';
    }

    public function toArray(): array
    {
        $result = [
            'authorizationUrl' => $this->getAuthorizationUrl(),
            'tokenUrl' => $this->getTokenUrl(),
            'refreshUrl' => null,
            'scopes' => null,
        ];
        $result = array_replace($result, parent::toArray());
        return $result;
    }
}
